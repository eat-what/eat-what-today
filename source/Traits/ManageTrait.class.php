<?php

namespace EatWhat\Traits;


use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\EatWhatStatic;

/**
 * Management trait
 * 
 */
trait ManageTrait
{
    /**
     * get management user by name
     * 
     */
    public function getManageUserByName(string $username)
    {
        $user = $this->mysqlDao->table("manage_member")
                     ->select("*")
                     ->where(["username"])
                     ->prepare()
                     ->execute([$username], ["fetch", \PDO::FETCH_ASSOC]);

        return $user;
    }

    /**
     * get good attribute via name
     * 
     */
    public function getAttributeByName(string $attributeName) 
    {
        $attribute = $this->mysqlDao->table("attribute")
                          ->select("*")
                          ->where(["name"])
                          ->prepare()
                          ->execute([$attributeName], ["fetch", \PDO::FETCH_ASSOC]);
        
        return $attribute;
    }

    /**
     * add good attribute
     * 
     */
    public function _addAttribute(array $attribute) : int
    {
        $this->mysqlDao->table("attribute")
             ->insert(array_keys($attribute))
             ->prepare()
             ->execute(array_values($attribute));
        $this->redis->del("all_attributes");
        $this->redis->del("all_attributes_with_value");
        
        return (int)$this->mysqlDao->getLastInsertId();
    }

    /**
     * edit an attributeq
     * 
     */
    public function _editAttribute(int $attrId, string $attributeName) : bool
    {
        $this->mysqlDao->table("attribute")->update(["name"])->where("id")->prepare()->execute([$attributeName, $attrId]);

        $this->redis->del("all_attributes");
        $this->redis->del("all_attributes_with_value");
        return $this->mysqlDao->execResult;
    }

    /**
     * add attribute value
     * 
     */
    public function _addAttributeValue(int $attrId, array $attrValue) : bool
    {
        foreach($attrValue as $value) {
            $attr_value_id = $this->mysqlDao->table("attribute_value")
                                  ->insert(["attr_id", "name", "create_time"])
                                  ->prepare()
                                  ->execute([$attrId, $value, time()]);
        }
        
        $this->redis->del("all_attributes_value");
        $this->redis->del("all_attributes_with_value");
        return $this->mysqlDao->execResult;
    }

    /**
     * edit ad attribute value
     * 
     */
    public function _editAttributeValue(int $attrValueId, string $attrValue) : bool
    {
        $this->mysqlDao->table("attribute_value")
             ->update(["name"])
             ->where("id")->prepare()
             ->execute([$attrValue, $attrValueId]);

        $this->redis->del("all_attributes_value");
        $this->redis->del("all_attributes_with_value");
        return $this->mysqlDao->execResult;
    }

    /**
     * check good name, 2 - 10characters
     * 
     */
    public function checkGoodName(string $name, int $categoryId, string $model = "") : bool
    {
        return $this->checkGoodNameFormat($name) && !$this->checkGoodNameExists($name, $categoryId, $model);
    }

    /**
     * check good name, 2 - 10characters
     * 
     */
    public function checkGoodNameFormat(string $name) : bool
    {
        return boolval(preg_match("/^[\d\w\p{Han}\-\+\!\=\*\(\)\"\'\[\]\,\?\:]{1,40}$/iu", $name));
    }

    /**
     * check good name exists
     * return true when exists
     * 
     */
    public function checkGoodNameExists(string $name, int $categoryId, string $model = "") : bool
    {
        $result = $this->mysqlDao->table("good")
                                 ->select(["id"])
                                 ->where(["name", "category_id", "model"])
                                 ->prepare()
                                 ->execute([$name, $categoryId, $model], ["fetch", \PDO::FETCH_ASSOC]);
        return boolval($result);
    }

    /**
     * add a good base 
     * [unique: name-model-category_id]
     * 
     */
    public function addGoodBase(array $good) : int
    {
        $this->mysqlDao->table("good")
             ->insert(array_keys($good))
             ->prepare()
             ->execute(array_values($good));

        return $this->mysqlDao->getLastInsertId();
    }

    /**
     * add good segment
     * [unique: attr_value_ids]
     * 
     */
    public function addGoodSegment(array $segment) : int
    {
        $this->mysqlDao->table("good_segment")
             ->insert(array_keys($segment))
             ->prepare()
             ->execute(array_values($segment));

        return $this->mysqlDao->getLastInsertId();
    }

    /**
     * add good segment attr
     * 
     */
    public function addGoodSegmentAttr(array $segmentAttr) : int
    {
        $this->mysqlDao->table("good_segment_attribute")
             ->insert(array_keys($segmentAttr))
             ->prepare()
             ->execute(array_values($segmentAttr));

        return $this->mysqlDao->getLastInsertId();
    }

    /**
     * update segment attr value
     * 
     */
    public function updateSegmentAttr(int $segmentId, string $attrIds, string $attrValueIds) : bool
    {
        $this->mysqlDao->table("good_segment")
             ->update(["attr_ids", "attr_value_ids"])->where(["id"])
             ->prepare()->execute([$attrIds, $attrValueIds, $segmentId]);
        
        return $this->mysqlDao->execResult;
    }

    /**
     * delete segment attributes
     * 
     */
    public function deleteSegmentAttr(int $segmentId) : bool
    {
        $this->mysqlDao->table("good_segment_attribute")
                    ->delete()->where(["segment_id"])
                    ->prepare()->execute([$segmentId]);
        
        return $this->mysqlDao->execResult;
    }

    /**
     * edit good base
     * 
     */
    public function editGoodBase(int $goodId, array $goodBase) : bool
    {
        $dao = $this->mysqlDao->table("good")->update(array_keys($goodBase))->where(["id"])->prepare();
        array_push($goodBase, $goodId);
        $dao->execute(array_values($goodBase));

        return $this->mysqlDao->execResult;
    }

    /**
     * edit good segment
     * 
     */
    public function editGoodSegment(int $segmentId, array $segment) : bool
    {
        $dao = $this->mysqlDao->table("good_segment")->update(array_keys($segment))->where(["id"])->prepare();
        array_push($segment, $segmentId);
        $dao->execute(array_values($segment));

        return $this->mysqlDao->execResult;
    }

    /**
     * set good status
     * 
     */
    public function setGoodStatus(array $goodIds, int $status) : bool
    {
        $dao = $this->mysqlDao->table("good")->update(["status"])->in("id", count($goodIds))->prepare();
        array_unshift($goodIds, $status);
        $dao->execute($goodIds);

        return $this->mysqlDao->execResult;
    }

    /**
     * add banner
     * 
     */
    public function _addBanner(array $banner) : int
    {
        $this->mysqlDao->table("banner")->insert(array_keys($banner))->prepare()->execute(array_values($banner));

        return (int)$this->mysqlDao->getLastInsertId();
    }

    /**
     * edit banner
     * 
     */
    public function _editBanner(int $bannerId, array $banner) : bool
    {
        $dao = $this->mysqlDao->table("banner")->update(array_keys($banner))->where(["id"])->prepare();
        array_push($banner, $bannerId);
        $dao->execute(array_values($banner));

        return $this->mysqlDao->execResult;
    }

    /**
     * delete banner
     * 
     */
    public function _deleteBanner(array $bannerIds) : bool
    {
        $dao = $this->mysqlDao->table("banner")->update(["status"])->in("id", count($bannerIds))->prepare();
        array_unshift($bannerIds, -1);
        $dao->execute($bannerIds);

        return $this->mysqlDao->execResult;
    }

    /**
     * set banners show position
     * 
     */
    public function setBannerPosition(int $bannerId, int $position) : bool
    {
        $this->mysqlDao->table("banner")
             ->update(["status", "position"])->where(["id"])
             ->prepare()->execute([1, $position, $bannerId]);
             
        return $this->mysqlDao->execResult;
    }

    /**
     * get today/all statistics information. include new members count, sales and orders count
     * 
     */
    public function getStatisticsInfo(bool $statToday = false) : array
    {
        $statisticsInfo = [];
        $todayTimeStamp = (new \DateTime(date("Y-m-d"), new \DateTimeZone('Asia/Shanghai')))->getTimeStamp();

        $orderExecuteSql = "select count(*) as orders, if(isnull(sum(order_total_money)), 0.0 , sum(order_total_money)) as sales from shop_order where order_status >= ?
                       union all
                       (select count(*) as orders, if(isnull(sum(order_total_money)), 0.0 , sum(order_total_money)) as sales from shop_order where order_status >= ? and create_time >= ?)";
        $orderStatistics = $this->mysqlDao->setExecuteSql($orderExecuteSql)->prepare()->execute([1, 1, $todayTimeStamp], ["fetchAll", \PDO::FETCH_ASSOC]);
        
        $memberExecuteSql = "select count(*) as members from shop_member where status >= ?
                       union all
                       (select count(*) as members from shop_member where status >= ? and create_time >= ?)";
        $memberStatistics = $this->mysqlDao->setExecuteSql($memberExecuteSql)->prepare()->execute([0, 0, $todayTimeStamp], ["fetchAll", \PDO::FETCH_ASSOC]);

        foreach(["all", "today"] as $index => $option) {
            $statisticsInfo[$option] = [
                "orders" => $orderStatistics[$index]["orders"],
                "sales" => $orderStatistics[$index]["sales"],
                "members" => $memberStatistics[$index]["members"],
            ];
        }

        return $statisticsInfo;
    }

    /**
     * delete a good comment
     * 
     */
    public function _deleteComment(int $commentId) : bool 
    {
        $this->mysqlDao->table("good_comment")
            ->delete()->where(["id"])
            ->prepare()->execute([$commentId], ["fetchAll", \PDO::FETCH_ASSOC]);
        
        return $this->mysqlDao->execResult;
    }

    /**
     * update undeposit log status
     *
     */
    public function updateUndepositLogStatus(int $logId, int $status) : bool
    {
        $this->mysqlDao->table("member_log_undeposit")
            ->update(["status"])->where(["id"])
            ->prepare()->execute([$status, $logId]);

        return $this->mysqlDao->execResult;
    }
}
