<?php

namespace EatWhat\Api;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;
use EatWhat\EatWhatStatic;

/**
 * Car Api
 * 
 */
class OrderApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\OrderTrait,\EatWhat\Traits\CommonTrait,\EatWhat\Traits\GoodTrait,\EatWhat\Traits\UserTrait;

    /**
     * generate order
     * goods : [{"good_id":1,"segment_id":1,"good_count":1},{}]
     * @param void
     * 
     */
    public function generateOrder() : void
    {
        $this->checkPost();
        $this->checkParameters([
            "pay_channel" => ["nonzero", "int", ["1", "2"]], 
            "source" => [["wx", "app", "wap"]],
            "address_id" => ["int", "nonzero"],
            "postage" => ["float"],
            "goods" => ["json"],
        ]);
        $this->beginTransaction();
        bcscale($this->getSetting("decimalPlaces"));
        
        $totalGoodMoney = 0.0;
        $additionOrderMoney = $_GET["postage"];

        $addressId = (int)$_GET["address_id"];
        $addressInfo = $this->getAddressInfo($addressId);
        if($this->uid != $addressInfo["uid"]) {
            $this->generateStatusResult("serverError", -404);
        }

        $order = [];
        $order["uid"] = $this->uid;
        $order["address_id"] = $addressId;
        $order["order_no"] = $this->generateOrderNo();
        $order["create_time"] = time();
        $order["update_time"] = 0;
        $order["order_status"] = 0;

        foreach(["source", "postage", "pay_channel"] as $option) {
            $order[$option] = $_GET[$option];
        }

        if(isset($_GET["remark"]) && $_GET["remark"] != "") {
            if(mb_strlen($_GET["remark"]) > 50) {
                $this->generateStatusResult("remarkTooLang", -1);
            }
            $order["remark"] = $_GET["remark"];
        }

        if(isset($_GET["discount"])) {
            if( !$this->checkFloat($_GET["discount"]) ) {
                $this->generateStatusResult("parameterError", -1);
            }
            $order["discount"] = $_GET["discount"];
            $additionOrderMoney = bcsub($additionOrderMoney, $order["discount"]);
        }

        $orderId = $this->insertOneObject($order, "order");
        
        $goods = $_GET["goods"];
        foreach( $goods as $good ) {
            $orderGood = [];
            $goodInfo = $this->getGoodBase($good["good_id"]);
            $segmentInfo = $this->getSegmentBase($good["segment_id"], true); // lock line

            if($segmentInfo["good_id"] != $good["good_id"]) {
                $this->generateStatusResult("serverError", -404);
            }

            if($segmentInfo["stock"] < $good["good_count"]) {
                $this->generateStatusResult("goodUnderStock", -3);
            }

            $orderGood["uid"] = $this->uid;
            $orderGood["category_id"] = $goodInfo["category_id"];
            $orderGood["category_name"] = $this->getCategoryNameById($goodInfo["category_id"]);
            $orderGood["good_id"] = $good["good_id"];
            $orderGood["order_id"] = $orderId;
            $orderGood["segment_id"] = $good["segment_id"];
            $orderGood["good_name"] = $goodInfo["name"];
            $orderGood["good_count"] = $good["good_count"];
            $orderGood["good_price"] = $segmentInfo["price"];
            $orderGood["good_model"] = $goodInfo["model"];
            $orderGood["good_description"] = $goodInfo["description"];
            $orderGood["attr_value_ids"] = $segmentInfo["attr_value_ids"];
            
            $goodMoney = bcmul($segmentInfo["price"], $good["good_count"]);
            $totalGoodMoney = bcadd($totalGoodMoney, $goodMoney);
            $orderGood["good_money"] = $goodMoney;

            $orderGoodId = $this->insertOneObject($orderGood, "order_good");
            $this->updateSegmentStock($good["segment_id"], -$good["good_count"]);
        }

        $orderTotalMoney = bcadd($totalGoodMoney, $additionOrderMoney);
        
        $this->updateOrderInfo($orderId, [
            "order_good_total_money" => $totalGoodMoney, 
            "order_total_money" => $orderTotalMoney
        ]);

        $this->commit();

        $order["id"] = $orderId;
        $order["order_total_money"] = $orderTotalMoney;
        $order["order_good_total_money"] = $totalGoodMoney;
        $this->generateStatusResult("generateOrderSuccess", 1);
        $this->outputResult([
            "order" => $order,
        ]);
    }

    /**
     * get order discount
     * @param void
     * 
     */
    public function orderDiscount() : void
    {
        $this->checkParameters(["goods" => ["json"]]);
        bcscale($this->getSetting("decimalPlaces"));

        $discount = "0.0";
        $discountRatio = $this->getUserLevelDiscountRatio($this->uid);

        $goods = $_GET["goods"];
        foreach( $goods as $good ) {
            $orderGood = [];
            $goodInfo = $this->getGoodBase($good["good_id"]);

            if( !$goodInfo["nodiscount"] ) {
                $segmentInfo = $this->getSegmentBase($good["segment_id"], true);
                $goodMoney = bcmul($segmentInfo["price"], $good["good_count"]);
                $discount = bcadd($discount, bcmul($goodMoney, bcsub("1.0", $discountRatio)));
            }
        }

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "discount" => $discount,
        ]);

    }

    /**
     * get order postage
     * @param void
     * 
     */
    public function getOrderPostage() : void
    {
        $this->checkParameters(["address_id" => ["nonzero", "int"]]);

        $addressId = (int)$_GET["address_id"];
        $addressInfo = $this->getAddressInfo($addressId);

        if($this->uid != $addressInfo["uid"]) {
            $this->request->generateStatusResult("serverError", -404);
        }

        $postage = $this->getPostageByProvince($addressInfo["province_id"]);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "postage" => $postage,
        ]);
    }

    /**
     * the order discount of different user level
     * @param void
     * 
     */
    public function levelDiscountRatio() : void
    {
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "discount" => $this->getUserLevelDiscountRatio($this->uid),
        ]);
    }

    /**
     * cancel a order 
     * @param void
     * 
     */
    public function cancelOrder() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["nonzero", "int"]]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId, ["uid", "order_status"]);

        if($orderInfo["uid"] != $this->uid) {
            $this->generateStatusResult("serverError", -404);
        }

        if($orderInfo["order_status"] != 0) {
            $this->generateStatusResult("orderStatusWrong", -1);
        }

        $this->updateOrderStatus($orderId, -4);
        $orderGoods = $this->getOrderGoods($orderId);
        foreach($orderGoods as $orderGood) {
            $this->updateSegmentStock($orderGood["segment_id"], $orderGood["good_count"]);
        }

        $this->commit();
        $this->generateStatusResult("cancelOrderSuccess", -1);
        $this->outputResult();
    }

    /**
     * initiate goods-return request
     * @param void
     * 
     */
    public function initiateGoodReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        /* user can not initiate good-return request that order status was not-recieve */
        if($orderInfo["order_status"] != 3 || $orderInfo["uid"] != $this->uid) {
            $this->generateStatusResult("orderStatusWrong", -1);
        }

        $this->updateOrderStatus($orderId, -1);
        $this->generateStatusResult("initiateGoodReturnSuccess", 1);
        $this->outputResult();
    }

    /**  
     * initiate money-return request
     * @param void
     * 
     */
    public function initiateMoneyReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        /* user can not initiate money-return request that order status was paied */
        if($orderInfo["order_status"] != 1 || $orderInfo["uid"] != $this->uid) {
            $this->generateStatusResult("orderStatusWrong", -1);
        }

        $this->updateOrderStatus($orderId, -5);
        $this->generateStatusResult("initiateGoodReturnSuccess", 1);
        $this->outputResult();
    }

    /**
     * list all user order group by order status
     * @param void
     * 
     */
    public function listOrder() : void
    {
        $filters = [];
        $filters["uid"] = $this->uid;
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;

        if(isset($_GET["order_status"])) {
            !$this->checkOrderStatusAvaliable($_GET["order_status"]) && $this->generateStatusResult("parameterError", -1);
            $filters["order_status"] = (int)$_GET["order_status"];
        }

        extract($this->getOrderList($filters));

        if(!empty($orders)) {
            $pagemore = ($page - 1) * $size  + count($orders) == $count ? 0 : 1;
        }

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("orders", "pagemore", "page"));
    }

    /**
     * get single order detail
     * @param void
     * 
     */
    public function orderDetail() : void
    {
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult($this->getOrderDetail($_GET["order_id"]));
    }
}