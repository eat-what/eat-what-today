<?php

namespace EatWhat\Api;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;
use EatWhat\EatWhatStatic;

/**
 * User Api
 * 
 */
class GoodApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\GoodTrait,\EatWhat\Traits\CommonTrait,\EatWhat\Traits\UserTrait;
    use \EatWhat\Traits\OrderTrait;

    /**
     * get index banner
     * @param void
     * 
     */
    public function getBanner() : void
    {
        $filters = [];
        $filters["status"] = 1;
        $filters["size"] = $this->getSetting("bannerCountLimit");
        $filters["sort"] = "position_asc";

        extract($this->getBannerList($filters, false));

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "banners" => $banners,
        ]);
    }

    /**
     * get goods by filters
     * @param void
     * 
     */
    public function listGood() : void
    {
        $filters = [];
        
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;

        foreach(["keyword", "tag", "period"] as $option) {
            if(isset($_GET[$option])) {
                $filters[$option] = $_GET[$option];
            }
        }

        extract($this->getGoodList($filters, true));

        if(!empty($goods)) {
            $pagemore = ($page - 1) * $size  + count($goods) == $count ? 0 : 1;
        } else if($page == 1) {
            $recommendGoods = ($this->getGoodList([
                "sort" => "salesnum_desc",
                "size" => 20,
            ], false))["goods"];
            $goods = $recommendGoods;
        }

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("goods", "pagemore", "page"));
    }

    /**
     * get goods by attribute
     * @param void
     * 
     */
    public function listGoodByAttribute() : void
    {
        $this->checkParameters(["attr_value_id" => ["int", "nonzero"]]);

        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;
        $filters["attr_value_id"] = (int)$_GET["attr_value_id"];

        extract($this->getGoodListByAttribute($filters, true));

        if(!empty($goods)) {
            $pagemore = ($page - 1) * $size  + count($goods) == $count ? 0 : 1;
        }

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("goods", "pagemore", "page"));
    }

    /**
     * get good detail
     * @param void
     * 
     */
    public function getGoodDetail() : void
    {   
        $this->checkParameters(["good_id" => ["int", "nonzero"]]);

        $goodId = (int)$_GET["good_id"];

        $this->incrGoodView($goodId);
        $good = $this->_getGoodDetail($goodId);
        $attributes = $this->_getAllAttributes();

        $this->generateStatusResult("200 OK", 1);
        $this->outputResult([
            "good" => $good,
            "attributes" => $attributes,
        ]);
    }

    /**
     * add good comment
     * @param void
     * 
     */
    public function addGoodComment() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"],"good_id" => ["int", "nonzero"], "segment_id" => ["int", "nonzero"],"status" => ["int", [1, 2, 3]],"comment" => null]);

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);
        if(!$orderInfo || $orderInfo["uid"] != $this->uid || $orderInfo["order_status"] != 3) {
            $this->generateStatusResult("serverError", -404);
        }

        $comment = $_GET["comment"];
        if( ($length = mb_strlen($comment)) > 255 && $length < 2 ) {
            $this->generateStatusResult("commentLengthWrong", -1);
        }

       $commentId =  $this->insertOneObject([
            "uid" => $this->uid,
            "add_time" => time(),
            "comment" => $comment,
            "order_id" => $orderId,
            "good_id" => (int)$_GET["good_id"],
            "segment_id" => (int)$_GET["segment_id"],
            "status" => (int)$_GET["status"],
        ], "good_comment");

        $this->generateStatusResult("addCommentSuccess", 1);
        $this->outputResult([
            "comment_id" => $commentId,
        ]);
    }

    /**
     * get comments of good
     * @param void
     * 
     */
    public function goodComments() : void
    {
        $this->checkParameters(["good_id" => ["int", "nonzero"]]);

        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;
        if(isset($_GET["status"]) && $_GET["status"]) {
            $filters["status"] = (int)$_GET["status"];
        }

        $goodId = $_GET["good_id"];
        extract($this->getGoodComments($goodId, $filters, true));
        $pagemore = ($page - 1) * $size  + count($comments) == $count ? 0 : 1;

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "comments" => $comments,
            "page" => $page,
            "pagemore" => $pagemore,
        ]);
    }
}
