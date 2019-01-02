<?php

namespace EatWhat\Api;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;
use EatWhat\EatWhatStatic;
use FileUpload\Validator\Simple as ValidatorSimple;
use FileUpload\PathResolver\Simple as PathResolver;
use FileUpload\FileSystem\Simple as FileSystem;
use FileUpload\FileNameGenerator\Custom as FileNameCustom;
use FileUpload\FileUploadFactory;
use Overtrue\Pinyin\Pinyin;

/**
 * Manage Api
 * 
 */
class ManageApi extends ApiBase
{
    use \EatWhat\Traits\ManageTrait,\EatWhat\Traits\CommonTrait,\EatWhat\Traits\GoodTrait;
    use \EatWhat\Traits\OrderTrait;
    use \EatWhat\Traits\UserTrait;

    /**
     * manage login
     * @param void
     * 
     */
    public function login() : void
    {
        $this->checkPost();
        $this->checkParameters(["username" => null, "password" => null]);

        $username = $_GET["username"];
        $user = $this->getManageUserByName($username);

        if(!$user || $user["status"] < 0) {
            $this->generateStatusResult("userStatusAbnormal", -1); 
        }

        if(!password_verify($_GET["password"], $user["password"])) {
            $this->generateStatusResult("userVerifyError", -2);
        }

        $this->setUserLogin([
            "uid" => $user["id"],
            "username" => $user["username"],
            "tokenType" => "manage",
        ]);

        $this->generateStatusResult("loginActionSuccess", 1);
        $this->outputResult();
    }

    /**
     * log out
     * @param void
     * 
     */
    public function logout() : void
    {
        $this->_logout();
    }

    /**
     * set global parameter
     * json
     * @param void
     * 
     */
    public function setGlobal() : void 
    {
        $this->checkPost();
        $this->checkParameter(["setting" => ["json"]]);

        $setting = $_GET["setting"];

        $this->setSetting($setting["key"], $setting["value"]);

        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }

    /**
     * add a attribute for good
     * @param void
     * 
     */
    public function addAttribute() : void
    {
        $this->checkPost();
        $this->checkParameters(["attrName" => null]);

        $attributeName = $_GET["attrName"];
        if( $this->getAttributeByName($attributeName) ) {
            $this->generateStatusResult("attributeExists", -1);
        }

        $attrId = $this->_addAttribute([
            "name" => $attributeName,
            "create_time" => time(),
        ]);

        $this->generateStatusResult("addSuccess", 1);
        $this->outputResult([
            "attrId" => $attrId,
        ]);
    }

    /**
     * edit an attribute
     * @param void
     * 
     */
    public function editAttribute() : void
    {
        $this->checkPost();
        $this->checkParameters(["attrId" => "int", "attrName" => null]);

        $attrId = $_GET["attrId"];
        $attributeName = $_GET["attrName"];

        $this->_editAttribute($attrId, $attributeName);

        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * get all attributes
     * @param void
     * 
     */
    public function getAllAttributes() : void 
    {
        $attributes = $this->_getAllAttributes();

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "attributes" => $attributes,
        ]);
    }

    /**
     * get all attributes and value
     * @param void
     * 
     */
    public function allAttributesWithValue() : void
    {
        $attributes = $this->getAllAttributesWithValue();

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "attributes" => $attributes,
        ]);
    }

    /**
     * add attribute value
     * @param void
     * 
     */
    public function addAttributeValue() : void
    {
        $this->checkPost();
        $this->checkParameters(["attrId" => "int", "attrValue" => null]);

        $attrId = $_GET["attrId"];
        $attrValue = $_GET["attrValue"];
        if( !is_array($attrValue) ) {
            $attrValue = explode(",", $attrValue);
        }

        $this->_addAttributeValue($attrId, $attrValue);

        $this->generateStatusResult("addSuccess", 1);
        $this->outputResult();
    }

    /**
     * edit attribute value
     * @param void
     * 
     */
    public function editAttributeValue() : void
    {
        $this->checkPost();
        $this->checkParameters(["attrValueId" => "int", "attrValue" => null]);

        $attrValueId = $_GET["attrValueId"];
        $attrValue = $_GET["attrValue"];
        $this->_editAttributeValue($attrValueId, $attrValue);

        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * get attribute values
     * @param void
     * 
     */
    public function getAttributeValue() : void
    {
        $this->checkParameters(["attrId" => "int"]);

        $attrId = (int)$_GET["attrId"];
        $attrValues = $this->_getAttributeValue($attrId);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "attrValues" => $attrValues,
        ]);
    }

    /**
     * get all category
     * @param void
     * 
     */
    public function getAllCategory() : void
    {
        $category = $this->_getAllCategory();

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "category" => $category,
        ]);
    }

    /**
     * add a good
     * [{"attr":["1_1","2_3","7_4"],"price":99.99,"stock":10},{"attr":["1_2","2_3","7_6"],"price":119.00,"stock":10}]
     * @param void
     * 
     */
    public function addGood() : void
    {
        $this->checkPost();
        $this->checkParameters([
            "name" => null, 
            "category_id" => "int", 
            "price" => "float", 
            "stock" => "int", 
            "attributes" => ["json"], 
            "tag" => "int", 
            "detail_images" => null, 
            "banner_images" => null
        ]);
        $this->beginTransaction();
        $pinyin = new Pinyin();

        $goodAttributes = $_GET["attributes"];

        $goodBase = [];
        foreach(["price", "stock", "tag"] as $option) {
            $goodBase[$option] = $_GET[$option];
        }

        $goodName = $_GET["name"];
        $categoryId = (int)$_GET["category_id"];
        if( !$this->checkGoodName($goodName, $categoryId, $_GET["model"] ?? "") ) {
            $this->generateStatusResult("goodNameError", -2);
        }

        $goodBase["name"] = $goodName;
        $goodBase["category_id"] = $categoryId;
        $goodBase["name_pinyin"] = $pinyin->permalink($goodName, "");

        if(isset($_GET["description"]) && !empty($_GET["description"])) {
            $goodBase["description"] = $_GET["description"];
            $goodBase["description_pinyin"] = $pinyin->permalink($_GET["description"], "");
        }

        if(isset($_GET["model"]) && !empty($_GET["model"])) {
            $goodBase["model"] = $_GET["model"];
        }

        if(isset($_GET["props"]) && !empty($_GET["props"])) {
            $goodBase["props"] = $_GET["props"];
        }

        if(isset($_GET["nodiscount"])) {
            $goodBase["nodiscount"] = (int)$_GET["nodiscount"];
        }

        $goodBase["create_time"] = $goodBase["update_time"] = time();
        $goodId = $this->addGoodBase($goodBase);

        foreach($goodAttributes as $attribute) {
            $segment = [];
            $segment["good_id"] = $goodId;
            $segment["price"] = (string)$attribute["price"];
            $segment["stock"] = $attribute["stock"];
            $segment["create_time"] = $segment["update_time"] = time();
            $segment["cost_price"] = $attribute["cost_price"] ?? "0.0";
            $segmentId = $this->addGoodSegment($segment); 

            $attrValueIds = $attrIds = [];
            foreach(array_unique($attribute["attr"]) as $attr) {
                list($attrId, $attrValueId) = explode("_", $attr);
                $attrIds[] = $attrId;
                $attrValueIds[] = $attrValueId;

                $segmentAttribute = [];
                $segmentAttribute["good_id"] = $goodId;
                $segmentAttribute["segment_id"] = $segmentId;
                $segmentAttribute["attr_id"] = $attrId;
                $segmentAttribute["attr_value_id"] = $attrValueId;
                $segmentAttrId = $this->addGoodSegmentAttr($segmentAttribute);
            }

            $this->updateSegmentAttr($segmentId, implode("_", $attrIds),  implode("_", $attrValueIds));         
        }

        $uploadResult = $this->uploadGoodImage($goodId);
        if(!$uploadResult) {
            $this->generateStatusResult("uploadError", -3);
        }

        $this->commit();
        $this->generateStatusResult("addSuccess", 1);
        $this->outputResult();
    }

    /**
     * upload good image 
     * @param void
     * 
     */
    public function uploadGoodImage(int $goodId, bool $isEdit = false) : bool
    {
        $goodImagePath = $this->getGoodImagePath($goodId);
        $fileGenerator = function($source_name, $type, $tmp_name, $index, $content_range, \FileUpload\FileUpload $upload) {
            return hash("sha256", uniqid() . $index) . ".png";
        };

        $uploadResult = true;
        if(!empty($_FILES["detail_images"])) {
            $uploadResult = $this->uploadFile([
                "path" => $goodImagePath . "detail_images",
                "size" => 20,
                "uploadname" => "detail_images",
                "filename" => $fileGenerator,
            ], $isEdit);
        }

        $bannerUploadResult = true;
        if(!empty($_FILES["banner_images"])) {
            $bannerUploadResult = $this->uploadFile([
                "path" => $goodImagePath . "banner_images",
                "size" => 10,
                "uploadname" => "banner_images",
                "filename" => $fileGenerator,
            ], $isEdit);
        }

        if($bannerUploadResult) {
            $sourceBanner = (array_slice(scandir($goodImagePath . "banner_images"), 2))[0];
            $this->resizeImage([
                "source" => $goodImagePath . "banner_images" . DS . $sourceBanner,
                "target" => $goodImagePath . "thumb.png",
            ]);
        }

        return $uploadResult && $bannerUploadResult;
    }

    /**
     * get edit good info
     * @param void
     * 
     */
    public function getGoodDetail() : void
    {   
        $this->checkParameters(["good_id" => ["int", "nonzero"]]);
        $good = $this->_getGoodDetail((int)$_GET["good_id"], true);

        $this->generateStatusResult("200 OK", 1);
        $this->outputResult([
            "good" => $good,
        ]);
    }

    /**
     * edit good
     * [{"segment_id":48,"attr":["1_1","2_3"],"price":10.99,"stock":10},{"segment_id":49,"attr":["1_2","2_3"],"price":19.99,"stock":10, "status":-1},{"segment_id":0,"attr":["1_1"],"price":10,"stock":10}]
     * @param void
     * 
     */
    public function editGood() : void
    {
        $this->checkPost();
        $this->checkParameters([
            "good_id" => "int", 
            "name" => null, 
            "category_id" => "int", 
            "price" => "float", 
            "stock" => "int", 
            "attributes" => ["json"], 
            "tag" => "int"
        ]);
        $this->beginTransaction();
        $pinyin = new Pinyin();

        $goodAttributes = $_GET["attributes"];

        $goodBase = [];
        $goodId = (int)$_GET["good_id"];
        foreach(["price", "stock", "tag"] as $option) {
            $goodBase[$option] = $_GET[$option];
        }

        $goodName = $_GET["name"];
        $categoryId = (int)$_GET["category_id"];
        if( !$this->checkGoodName($goodName, $categoryId, $_GET["model"] ?? "") ) {
            $this->generateStatusResult("goodNameError", -1);
        }

        $goodBase["name"] = $goodName;
        $goodBase["category_id"] = $categoryId;
        $goodBase["name_pinyin"] = $pinyin->permalink($goodName, "");

        if(isset($_GET["description"]) && !empty($_GET["description"])) {
            $goodBase["description"] = $_GET["description"];
            $goodBase["description_pinyin"] = $pinyin->permalink($_GET["description"], "");
        }

        if(isset($_GET["model"])) {
            $goodBase["model"] = $_GET["model"];
        }

        if(isset($_GET["props"])) {
            $goodBase["props"] = $_GET["props"];
        }

        if(isset($_GET["nodiscount"])) {
            $goodBase["nodiscount"] = (int)$_GET["nodiscount"];
        }

        $goodBase["update_time"] = time();
        $this->editGoodBase($goodId, $goodBase);

        foreach($goodAttributes as $attribute) {
            $segment = [];
            $segment["good_id"] = $goodId;
            $segment["price"] = (string)$attribute["price"];
            $segment["stock"] = $attribute["stock"];
            $segment["update_time"] = time();
            $segment["status"] = $attribute["status"] ?? 0; // delete segment, set to -1
            $segment["cost_price"] = $attribute["cost_price"] ?? "0.0";

            $segmentId = $attribute["segment_id"];
            if( $segmentId ) {
                $this->deleteSegmentAttr($segmentId);
                $this->editGoodSegment($segmentId, $segment);
            } else {
                $segment["create_time"] = time();
                $segmentId = $this->addGoodSegment($segment);
            }  

            $attrValueIds = $attrIds = [];
            foreach(array_unique($attribute["attr"]) as $attr) {
                list($attrId, $attrValueId) = explode("_", $attr);
                $attrIds[] = $attrId;
                $attrValueIds[] = $attrValueId;

                $segmentAttribute = [];
                $segmentAttribute["good_id"] = $goodId;
                $segmentAttribute["segment_id"] = $segmentId;
                $segmentAttribute["attr_id"] = $attrId;
                $segmentAttribute["attr_value_id"] = $attrValueId;
                $segmentAttrId = $this->addGoodSegmentAttr($segmentAttribute);
            }

            $this->updateSegmentAttr($segmentId, implode("_", $attrIds),  implode("_", $attrValueIds));         
        }

        $this->updateOrderGoodInfoAfterGoodEdit($goodId, $goodBase);
        $this->updateGoodThumbnail($goodId, (int)($_GET["thumb_index"] ?? 0));

        $this->commit();
        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * add good image in editing case
     * @param void
     * 
     */
    public function addGoodImage() : void
    {
        $this->checkPost();
        $this->checkParameters(["good_id" => ["int", "nonzeros"], "good_image" => null, "type" => [["detail", "banner"]]]);

        $goodId = (int)$_GET["good_id"];
        $goodImagePath = $this->getGoodImagePath($goodId) . $_GET["type"] . "_images";

        $newIndex = count(scandir($goodImagePath)) - 2;
        $fileName = hash("sha256", uniqid() . $newIndex) . ".png";

        $uploadResult = $this->uploadFile([
            "path" => $goodImagePath,
            "size" => 10,
            "uploadname" => "good_image",
            "filename" => $fileName,
        ]);

        if(!$uploadResult) {
            $this->generateStatusResult("uploadError", -1);
        }
        
        $this->generateStatusResult("addSuccess", 1);
        $this->outputResult([
            "pic" => $this->getGoodImagePath($goodId, true) . $_GET["type"] . "_images" . "/" . $fileName,
            "pic_index" => $newIndex,
        ]);
    }

    /**
     * delete good image in editing case
     * pic_index starting from 0
     * @param void
     * 
     */
    public function deleteGoodImage() : void
    {
        $this->checkPost();
        $this->checkParameters(["good_id" => ["int", "nonzeros"], "pic_index" => ["int"], "type" => [["detail", "banner"]]]);

        $goodId = (int)$_GET["good_id"];
        $picIndex = (int)$_GET["pic_index"];
        $goodImagePath = $this->getGoodImagePath($goodId) . $_GET["type"] . "_images";

        $files = array_slice(scandir($goodImagePath), 2);
        if(count($files) <= $picIndex) {
            $this->generateStatusResult("deleteNotExistsImageIndex", -1);
        } 

        if(!unlink($goodImagePath . DS . $files[$picIndex])) {
            $this->generateStatusResult("deleteFaild", -2);
        }

        $this->generateStatusResult("deleteSuccess", 1);
        $this->outputResult();
    }

    /**
     * set good status,up/down
     * @param void
     * 
     */
    public function upShelf() : void
    {
        $this->upDownShelf(0);
    }

    /**
     * set good status,up/down
     * @param void
     * 
     */
    public function downShelf() : void
    {
        $this->upDownShelf(-1);
    }

    /**
     * good up/down shelf
     * @param void
     * 
     */
    public function upDownShelf(int $status) : void
    {
        $this->checkPost();
        $this->checkParameters(["good_ids" => ["array_int", "array_nonzero"]]);

        $goodIds = $_GET["good_ids"];
        if( !is_array($goodIds) ) {
            $goodIds = [$goodIds];
        }
        $this->setGoodStatus($goodIds, $status);
        
        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }

    /**
     * get good list
     * @param void
     * 
     */
    public function listGood() : void
    {
        $filters = [];
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $_GET["size"] ?? 10;

        foreach(["keyword", "tag", "period"] as $option) {
            if(isset($_GET[$option])) {
                $filters[$option] = $_GET[$option];
            }
        } 
        $filters["listAll"] = true;

        extract($this->getGoodList($filters, true));

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("goods", "count", "page"));
    }

    /**
     * delete good comment
     * @param void
     * 
     */
    public function deleteGoodComment() : void
    {
        $this->checkPost();
        $this->checkParameters(["comment_id" => ["int", "nonzero"]]);

        $this->_deleteComment($_GET["comment_id"]);

        $this->generateStatusResult("deleteSuccess", 1);
        $this->outputResult();
    }

    /**
     * add banner
     * @param void
     * 
     */
    public function addBanner() : void
    {
        $this->checkPost();
        $this->checkParameters(["link_type" => null, "link_value" => null, "banner_image" => null]);
    
        if($_GET["link_type"] == "url" && !$this->checkUrlFormat($_GET["link_value"])) {
            $this->generateStatusResult("urlFormatError", -1);
        }

        $banner = [];
        $banner["link_type"] = $_GET["link_type"];
        $banner["link_value"] = $_GET["link_value"];
        $banner["create_time"] = time();
        $bannerId = $this->_addBanner($banner);

        $uploadParameters = [];
        $uploadParameters["path"] = ATTACH_PATH . "banner_image" . DS . chunk_split(sprintf("%06s", $bannerId), 2, DS);
        $uploadParameters["size"] = 20;
        $uploadParameters["uploadname"] = "banner_image";
        $uploadParameters["filename"] = "banner.png";
        $uploadResult = $this->uploadFile($uploadParameters);

        if($uploadResult) {
            $this->resizeImage([
                "source" => $uploadParameters["path"] . "banner.png",
                "target" => $uploadParameters["path"] . "thumb.png",
            ]);
        }

        $this->generateStatusResult("addSuccess", 1);
        $this->outputResult();
    }

    /**
     * edit banner
     * @param void
     * 
     */
    public function editBanner() : void
    {
        $this->checkPost();
        $this->checkParameters(["link_type" => null, "link_value" => null, "banner_id" => "int"]);
    
        if($_GET["link_type"] == "url" && !$this->checkUrlFormat($_GET["link_value"])) {
            $this->generateStatusResult("urlFormatError", -1);
        }

        $bannerId = $_GET["banner_id"];
        $banner = [];
        $banner["link_type"] = $_GET["link_type"];
        $banner["link_value"] = $_GET["link_value"];
        $banner["update_time"] = time();
        $this->_editBanner($bannerId, $banner);

        if(!empty($_FILES["banner_image"])) {
            $uploadParameters = [];
            $uploadParameters["path"] = ATTACH_PATH . "banner_image" . DS . chunk_split(sprintf("%06s", $bannerId), 2, DS);
            $uploadParameters["size"] = 20;
            $uploadParameters["uploadname"] = "banner_image";
            $uploadParameters["filename"] = "banner.png";
            $uploadResult = $this->uploadFile($uploadParameters, true);

            if($uploadResult) {
                $this->resizeImage([
                    "source" => $uploadParameters["path"] . "banner.png",
                    "target" => $uploadParameters["path"] . "thumb.png",
                ]);
            }
        }

        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * delete banner
     * @param void
     * 
     */
    public function deleteBanner() : void
    {
        $this->checkPost();
        $this->checkParameters(["banner_ids" => ["array_int", "array_nonzero"]]);

        $bannerIds = $_GET["banner_ids"];
        $this->_deleteBanner($bannerIds);

        $this->generateStatusResult("deleteSuccess", 1);
        $this->outputResult();
    }

    /**
     * list banners
     * @param void
     * 
     */
    public function listBanner() : void
    {
        $filters = [];
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $_GET["size"] ?? 10;

        extract($this->getBannerList($filters, true));

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("banners", "count", "page"));
        // $this->outputResult(compact("banners"));
    }

    /**
     * set banner position
     * @param banner_setting [{"bannerid":1,"position":1},{"bannerid":3,"position":2}]
     * @param void
     * 
     */
    public function setBanner() : void
    {
        $this->checkPost();
        $this->checkParameters(["banner_setting" => ["json"]]);

        $bannerSetting = $_GET["banner_setting"];
        if(count($bannerSetting) > $this->getSetting("bannerCountLimit")) {
            $this->generateStatusResult("bannerShowCountError", -1);
        }

        foreach($bannerSetting as $banner) {
            $this->setBannerPosition($banner["bannerid"], $banner["position"]);
        }

        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }

    /**
     * statistics information
     * @param void
     * 
     */
    public function statisticsInfo() : void 
    {
        $statisticsInfo = $this->getStatisticsInfo();

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "statistics" => $statisticsInfo,
        ]);
    }

    /**
     * agree undeposit request
     * @param void
     * 
     */
    public function agreeUndeposit() : void
    {
        $this->checkPost();
        $this->checkParameters(["log_id" => ["int", "nonzero"]]);
        $this->beginTransaction();

        $logId = (int)$_GET["log_id"];
        $logInfo = $this->getLogBaseInfo($logId, "member_log_undeposit");
        if($logInfo["status"] != 0) {
            $this->generateStatusResult("serverError", -404);
        }

        $this->updateUndepositLogStatus($logId, 1);
        $propertyLogId = $this->insertOneObject([
            "uid" => $logInfo["uid"],
            "amount" => -$logInfo["amount"],
            "log_time" => time(),
            "description" => AppConfig::get("undepositDescription", "lang"),
        ], "member_log_property");

        $messageId = $this->insertOneObject([
            "uid" => $logInfo["uid"],
            "message" => AppConfig::get("message_tpl", "global", "agreeUndeposit"),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("agreeUndepositRequest", 1);
        $this->outputResult();
    }

    /**
     * reject undeposit request
     * @param void
     * 
     */
    public function rejectUndeposit() : void
    {
        $this->checkPost();
        $this->checkParameters(["log_id" => ["int", "nonzero"], "reason" => null]);
        $this->beginTransaction();

        $logId = (int)$_GET["log_id"];
        $logInfo = $this->getLogBaseInfo($logId, "member_log_undeposit");
        if($logInfo["status"] != 0) {
            $this->generateStatusResult("serverError", -404);
        }

        $this->updateUndepositLogStatus($logId, -1);
        $this->updateUserCount($logInfo["uid"], "property", $logInfo["amount"]);

        $messageId = $this->insertOneObject([
            "uid" => $logInfo["uid"],
            "message" => sprintf(AppConfig::get("message_tpl", "global", "rejectUndeposit"), $_GET["reason"]),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("rejectUndepositRequest", 1);
        $this->outputResult();
    }

    /**
     * list orders by filter
     *
     */
    public function listOrder() : void
    {
        $filters = [];
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;

        if(isset($_GET["order_status"])) {
            !$this->checkOrderStatusAvaliable($_GET["order_status"]) && $this->generateStatusResult("parameterError", -1);
            $filters["order_status"] = (int)$_GET["order_status"];
        }

        if(isset($_GET["period"]) && (int)$_GET["period"]) {
            $filters["period"] = (int)$_GET["period"];
        }

        if(isset($_GET["order_no"])) {
            $filters["order_no"] = $_GET["order_no"];
        }

        $filters["manage"] = true;
        extract($this->getOrderList($filters, true));

        //down load csv data
        $this->redis->set("downloadcsvdata_order", $orders, 24 * 3600);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("orders", "count", "page"));
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

    /**
     * set order tracking number 
     * @param void
     * 
     */
    public function setOrderTrackNumber() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"], "track_no" => null]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);
        if($orderInfo["order_status"] != 1) {
            $this->generateStatusResult("orderCantSetTrackNo", -1);
        }

        $this->updateOrderInfo($_GET["order_id"], [
            "order_status" => 2,
            "track_no" => $_GET["track_no"],
        ]);

        $messageId = $this->insertOneObject([
            "uid" => $orderInfo["uid"],
            "message" => sprintf(AppConfig::get("message_tpl", "global", "orderDeliverd"), $_GET["track_no"]),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }

    /**
     * agree good-return request
     * * 同意退货请求（先不做）
     * @param void
     * 
     */
    public function agreeGoodReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        $this->updateOrderStatus($orderId, -2);
        $messageId = $this->insertOneObject([
            "uid" => $orderInfo["uid"],
            "message" => AppConfig::get("message_tpl", "global", "goodReturnSteps"),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("agreeGoodReturn", 1);
        $this->outputResult();
    }

    /**
     * reject good-return request
     * 拒绝退货请求（先不做）
     * @param void
     * 
     */
    public function rejectGoodReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"], "reason" => null]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        $this->updateOrderStatus($orderId, 3);
        $messageId = $this->insertOneObject([
            "uid" => $orderInfo["uid"],
            "message" => sprintf(AppConfig::get("message_tpl", "global", "goodReturnReject"), $_GET["reason"]),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("rejectGoodReturn", 1);
        $this->outputResult();
    }

    /**
     * agree money-return request
     * * 同意退款请求
     * @param void
     * 
     */
    public function agreeMoneyReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        $this->updateOrderStatus($orderId, -6);
        $messageId = $this->insertOneObject([
            "uid" => $orderInfo["uid"],
            "message" => AppConfig::get("message_tpl", "global", "moneyReturnAgree"),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("agreeMoneyReturn", 1);
        $this->outputResult();
    }

    /**
     * complete money-return order
     * 完成退款请求
     * @param void
     * 
     */
    public function completeMoneyReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"]]);
        $this->beginTransaction();
    }

    /**
     * reject money-return request
     * 拒绝退款请求
     * @param void
     * 
     */
    public function rejectMoneyReturn() : void
    {
        $this->checkPost();
        $this->checkParameters(["order_id" => ["int", "nonzero"], "reason" => null]);
        $this->beginTransaction();

        $orderId = (int)$_GET["order_id"];
        $orderInfo = $this->getOrderBaseInfo($orderId);

        $this->updateOrderStatus($orderId, 1);
        $messageId = $this->insertOneObject([
            "uid" => $orderInfo["uid"],
            "message" => sprintf(AppConfig::get("message_tpl", "global", "moneyReturnReject"), $_GET["reason"]),
            "message_time" => time(),
        ], "member_message");

        $this->commit();
        $this->generateStatusResult("rejectMoneyReturn", 1);
        $this->outputResult();
    }

    /**
     * get member list
     * 获取用户列表
     * @param void
     * 
     */
    public function listMember() : void
    {
        $filters = [];
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;

        foreach(["status", "id", "period", "level", "namekey", "mobile"] as $option) {
            if(isset($_GET[$option])) {
                $filters[$option] = $_GET[$option];
            }
        }

        extract($this->getMemberList($filters, true));

        //down load csv data
        $this->redis->set("downloadcsvdata_member", $members, 24 * 3600);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("members", "count", "page"));
    }

    /**
     * get member order list
     * 获取用户订单列表
     * @param void
     * 
     */
    public function listMemberOrder() : void 
    {
        $this->checkParameters(["uid" => ["int", "nonzero"]]);

        $filters = [];
        $filters["page"] = $page = $_GET["page"] ?? 1;
        $filters["size"] = $size = $_GET["size"] ?? 10;

        $uid = (int)$_GET["uid"];
        $filters["uid"] = $uid;

        extract($this->getOrderList($filters, true));

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(compact("orders", "count", "page"));
    }

    /**
     * set member level
     * 设置用户等级
     * @param void
     * 
     */
    public function setMemberLevel() : void
    {
        $this->checkPost();
        $this->checkParameters(["uid" => ["int", "nonzero"], "level" => ["int", [1, 2, 3, 4]]]);

        $this->updateMemberLevel($_GET["uid"], $_GET["level"]);
        
        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }
}