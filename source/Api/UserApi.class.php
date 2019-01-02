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

/**
 * User Api
 * 
 */
class UserApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\UserTrait,\EatWhat\Traits\CommonTrait,\EatWhat\Traits\GoodTrait;

    /**
     * a new user
     * @param void
     * 
     */
    public function join() : void
    {
        $this->checkPost();
        $this->checkParameters(["mobile" => null, "verifyCode" => ["int", "nonzero"], "username" => null]);        
        $this->beginTransaction();

        $mobile = $_GET["mobile"];
        if( !$this->checkMobile($mobile) ) {
            $this->generateStatusResult("wrongMobileFormatOrExists", -2);
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($mobile, $verifyCode, "join") ) {
            // $this->generateStatusResult("wrongVerifyCode", -3);
        }

        $username = $_GET["username"];
        if( !$this->checkUsername($username) ) {
            $this->generateStatusResult("wrongUsernameFormatOrExists", -4);
        }
        
        $newUser = [];
        $newUser["mobile"] = $mobile;
        $newUser["username"] = $username;
        $newUser["create_time"] = time();
        $newUser["last_login_time"] = time();
        isset($_GET["lastUid"]) && (int)$_GET["lastUid"] && ($newUser["lastUid"] = (int)$_GET["lastUid"]);
        $newUserId = $this->createNewUser($newUser);

        $this->setDefaultAvatar($newUserId, hash("sha256", $username));

        $userData = [
            "uid" => $newUserId,
            "username" => $username,
            "avatar" => $this->getUserAvatar($newUserId),
            "tokenType" => "user",
        ];

        $this->initMemberCount($newUserId);
        $this->commit();

        $this->setUserLogin($userData);
        $this->generateStatusResult("registerActionSuccess", 1);
        $this->outputResult();
    }

    /**
     * login
     * @param void
     * 
     */
    public function login() : void
    {
        $this->checkPost(); 

        $loginType = $_GET["loginType"] ?? "code";
        
        if($loginType == "code") {
            $this->loginByVerifyCode();    
        } else if($loginType == "password") {
            $this->loginByPassword();
        }
    }

    /**
     * login by verify code
     * @param void
     * 
     */
    public function loginByVerifyCode() : void
    {
        $this->checkParameters(["mobile" => null, "verifyCode" => ["int", "nonzero"]]); 

        $mobile = $_GET["mobile"];
        $user = $this->getUserBaseInfoByMobile($mobile);

        if(!$user || $user["status"] < 0) {
            $this->generateStatusResult("userStatusAbnormal", -1); 
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($mobile, $verifyCode, "login") ) {
            $this->generateStatusResult("wrongVerifyCode", -3);
        }
        
        $this->setUserLogin([
            "uid" => $user["id"],
            "username" => $user["username"],
            "avatar" =>  $this->getUserAvatar($user["id"]),
            "tokenType" => "user",
        ]);

        $this->updateUserLastLoginTime($user["id"]);
        $this->generateStatusResult("loginActionSuccess", 1);
        $this->outputResult();
    }

    /**
     * get user info, include base info and property
     * @param void
     * 
     */
    public function userInfo() : void
    {
        $userBaseInfo = $this->getUserBaseInfoById($this->uid);

        $userCount = $this->getUserCount($this->uid);
        unset($userCount["id"], $userCount["uid"]);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "base" => $userBaseInfo,
            "property" => $userCount,
        ]);
    }

    /**
     * modify user mobile
     * @param void
     * 
     */
    public function modifyMobile() : void
    {
        $this->checkPost();
        $this->checkParameters(["newmobile" => null]); 

        $newMobile = $_GET["newmobile"];
        if( !$this->checkMobile($newMobile) ) {
            $this->generateStatusResult("wrongMobileFormatOrExists", -2);
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($newMobile, $verifyCode, "modifyMobile") ) {
            $this->generateStatusResult("wrongVerifyCode", -3);
        }

        $this->updateUserMobile($newMobile);

        $this->request->getUserController()->setUserField("mobile", $newMobile);
        $this->generateStatusResult("updateSuccess", 1);
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
     * modify user info
     * @param void
     * 
     */
    public function modifyUserAvatar() : void
    {
        $this->checkPost();
        $this->checkParameters(["avatar" => null]);

        $userAvatar = $this->getUserAvatar($this->uid, false);
        $factory = new FileUploadFactory(
            new PathResolver(dirname($userAvatar)),
            new FileSystem(), [
                new ValidatorSimple("2M", ["image/png", "image/jpg", "image/jpeg"]),
            ]
        );
        $fileUpload = $factory->create($_FILES["avatar"], $_SERVER);
        
        $customGenerator = new FileNameCustom("avatar.png");
        $fileUpload->setFileNameGenerator($customGenerator);

        list($files, $headers) = $fileUpload->processAll();
        if( $files[0]->completed ) {
            $this->generateStatusResult("modifyAvatarSuccess", 1);
            $this->outputResult();
        } else {
            $this->generateStatusResult($files[0]->error, -1, false);
        }
    }

    /**
     * modify user base info
     * sex age location
     * @param void
     * 
     */
    public function modifyUserBase() : void
    {
        $this->checkPost();

        $baseInfo = [];
        foreach(["sex", "age", "location"] as $option) {
            isset($_GET[$option]) && ($baseInfo[$option] = $_GET[$option]);
        }

        if(!empty($baseInfo)) {
            $this->updateUserBaseInfo($baseInfo);
        }

        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * generate a invitation qrcode
     * @param void
     * 
     */
    public function inviteJoinQrcode() : void
    {
        $inviteJoinUrl = AppConfig::get("protocol", "global") . AppConfig::get("server_name", "global") . "/" . "join.html?lastUid=" . $this->uid;
        EatWhatStatic::getUrlQrcode($inviteJoinUrl);
    }

    /**
     * get all distributors of user
     * @param void
     * 
     */
    public function getAllDistributors() : void
    {
        $page = $_GET["page"] ?? 1;
        $num = $_GET["num"] ?? 10;

        $distributors = $this->_getAllDistributors($this->uid, $page, $num);
        
        $this->generateStatusResult("200 OK", 1, false);
        $result["distributors"] = $distributors;
        $result["count"] = $this->getDistributorsCount();

        $this->outputResult($result);
    }

    /**
     * add a shipping address
     * @param void
     * 
     */
    public function addAddress() : void
    {
        $this->checkPost();
        $this->checkParameters(["province" => null, "province_id" => ["int", "nonzero"], "city" => null, "district" => null, "detail" => null, "contact_name" => null, "contact_number" => null]);

        $count = $this->getAddressCount($this->uid);
        if($count == $this->getSetting("addressCountLimit")) {
            $this->generateStatusResult("addessCountOutOfLimit", -1);
        }

        if(!$this->checkUsernameFormat($_GET["contact_name"])) {
            $this->generateStatusResult("wrongContactNameFormat", -1);
        }

        if(!$this->checkMobileFormat($_GET["contact_number"])) {
            $this->generateStatusResult("wrongContactNumberFormat", -2);
        }

        $address = [];
        foreach(["province", "city", "district", "detail", "contact_number", "contact_name"] as $option) {
            $address[$option] = $_GET[$option];
        }

        if(isset($_GET["isdefault"]) && $_GET["isdefault"] == 1) {
            $address["isdefault"] = 1;
            $this->setDefaultAddressToNot($this->uid);
        } else {
            $address["isdefault"] = 0;
        }

        $address["uid"] = $this->uid;
        $address["create_time"] = time();
        
        $addressId = $this->_addAddress($address);
        
        $this->generateStatusResult("addAddressSuccess", 1);
        $this->outputResult();
    }

    /**
     * delete user shipping address
     * @param void
     * 
     */
    public function deleteAddress() : void
    {
        $this->checkPost();
        $this->checkParameters(["address_ids" => ["array_int", "array_nonzero"]]);
        
        foreach( $_GET["address_ids"] as $addressId ) {
            $address = $this->getAddressInfo($addressId);
            if($this->uid != $address["uid"]) {
                $this->request->generateStatusResult("serverError", -404);
            }
        }

        empty($addressIds) && $this->generateStatusResult("parameterError", -1);

        $this->_deleteAddress($addressIds);
        
        $this->generateStatusResult("deleteSuccess", 1);
        $this->outputResult();
    }

    /**
     * Set to the default shipping address
     * @param void
     * 
     */
    public function setToDefaultAddress() : void
    {                                                                                                                                                                                       
        $this->checkPost();
        $this->checkParameters(["address_id" => ["int", "nonzero"]]);

        $addressId = (int)$_GET["address_id"];
        $address = $this->getAddressInfo($addressId);
        if($this->uid != $address["uid"]) {
            $this->request->generateStatusResult("serverError", -404);
        }

        $this->setDefaultAddressToNot($this->uid);
        $this->_setToDefaultAddress($addressId);

        $this->generateStatusResult("setSuccess", 1);
        $this->outputResult();
    }

    /**
     * get user all shipping address
     * @param void
     * 
     */
    public function getAddress() : void
    {
        $addresses = $this->getUserAddress($this->uid, (int)$_GET["default"] == 1);

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult(["data" => $addresses, "count" => count($addresses)]);
    }

    /**
     * edit user shipping address
     * @param void
     * 
     */
    public function editAddress() : void
    {
        $this->checkPost();
        $this->checkParameters(["address_id" => ["int", "nonzero"], "province" => null, "province_id" => ["int", "nonzero"], "city" => null, "district" => null, "detail" => null, "contact_name" => null, "contact_number" => null]);

        if(!$this->checkUsernameFormat($_GET["contact_name"])) {
            $this->generateStatusResult("wrongContactNameFormat", -1);
        }

        if(!$this->checkMobileFormat($_GET["contact_number"])) {
            $this->generateStatusResult("wrongContactNumberFormat", -2);
        }

        $addressId = (int)$_GET["address_id"];
        $addressInfo = $this->getAddressInfo($addressId);
        if($this->uid != $addressInfo["uid"]) {
            $this->request->generateStatusResult("serverError", -404);
        }

        $address = [];
        foreach(["province", "city", "district", "detail", "contact_name", "contact_number"] as $option) {
            $address[$option] = $_GET[$option];
        }

        if(isset($_GET["isdefault"]) && $_GET["isdefault"] == 1) {
            $address["isdefault"] = 1;
            $this->setDefaultAddressToNot($this->uid);
        }

        if( !empty($address) ) {
            $this->editUserAddress($addressId, $address);
        }

        $this->generateStatusResult("updateSuccess", 1);
        $this->outputResult();
    }

    /**
     * return - user money-return log
     * @param void
     * 
     */
    public function moneyReturnLog() : void
    {
        $page = $_GET["page"] ?? 1;
        $size = $_GET["size"] ?? 10;

        $returnLogs = $this->getMoneyReturnLog($this->uid, $page, $size);
        $pagemore = count($returnLogs) == $size ? 1 : 0;
        
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "logs" => $returnLogs,
            "page" => $page,
            "pagemore" => $pagemore,
        ]);
    }

    /**
     * property financing
     * check expire: time() >= property_financing_expire
     * check not expire: time() <= property_financing_start 
     *
     */
    public function propertyFinancing() : void
    {
        $this->checkPost();
        $this->checkParameters(["period" => ["int", "nonzero", [30, 90, 180, 360]]]);

        $userCount = $this->getUserCount($this->uid);
        if($userCount["property_financing"]) {
            $this->generateStatusResult("propertyAlreadyFinancing", -1);
        }

        if($userCount["property"] < $this->getSetting("minimumFinancingProperty")) {
            $this->generateStatusResult("lessThanMinimumFinancingProperty", -1);
        }

        $this->userBeginPropertyFinancing($this->uid, $_GET["period"]);

        $this->generateStatusResult("financingSuccess", 1);
        $this->outputResult();
    }

    /**
     * user financing info
     * @param void
     * 
     */
    public function myFinancing() : void
    {
        if(!($financingInfo = $this->getUserFinancingInfo($this->uid))) {
            $this->generateStatusResult("userNoFinancingInfo", -1);
        }

        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "financing" => $financingInfo,
        ]);
    }

    /**
     * add a user undeposit account
     * @param void
     * 
     */
    public function addAccount() : void
    {
        $this->checkPost();
        $this->checkParameters(["account" => null]);

        if( ($accountList = $this->getUserAccountList($this->uid)) && count($accountList) >= 1) {
            $this->generateStatusResult("accountLimit", -1);
        }

        $accountId = $this->insertOneObject([
            "uid" => $this->uid,
            "type" => "bank",
            "account" => $_GET["account"],
            "bind_time" => time(),
        ], "member_account");

        $this->generateStatusResult("bindSuccess", 1);
        $this->outputResult([
            "account_id" => $accountId,
        ]);
    }

    /**
     * get user account list
     * @param void
     * 
     */
    public function accountList() : void
    {
        $accountList = $this->getUserAccountList($this->uid);

        $this->generateStatusResult("200 OK", 1);
        $this->outputResult([
            "accounts" => $accountList,
        ]);
    }

    /**
     * delete user account
     * @param void
     * 
     */
    public function deleteAccount() : void 
    {
        $this->checkPost();
        $this->checkParameters(["account_id" => ["int", "nonzero"]]);

        $accountId = (int)$_GET["account_id"];
        $account = $this->getAccountInfo($accountId);
        if($account["uid"] != $this->uid) {
            $this->generateStatusResult("serverError", -404);
        }

        $this->deleteUserAccount($accountId);
        
        $this->generateStatusResult("deleteSuccess", 1);
        $this->outputResult();
    }

    /**
     * initiate undeposit
     * @param void
     * 
     */
    public function initiateUndeposit() : void
    {
        $this->checkPost();
        $this->checkParameters(["amount" => ["float", "nonzero"], "account_id" => ["int", "nonzero"]]);
        bcscale($this->getSetting("decimalPlaces"));

        $undepositAmount = $_GET["amount"];
        if($undepositAmount < 1.0) {
            $this->generateStatusResult("undepositMinimumError", -1);
        }

        $userCount = $this->getUserCount($this->uid);
        if($userCount["property_financing"] == 1 && time() < $userCount["property_financing_expire"]) {
            $this->generateStatusResult("propertyFinancingIsNotExpired", -2);
        }
        if(bcsub($userCount["property"], $undepositAmount) < 0.1) {
            $this->generateStatusResult("propertyMoneyLack", -3);
        }

        $accountId = (int)$_GET["account_id"];
        $account = $this->getAccountInfo($accountId);
        if($account["uid"] != $this->uid) {
            $this->generateStatusResult("serverError", -404);
        }

        $logId = $this->insertOneObject([
            "uid" => $this->uid,
            "amount" => $undepositAmount,
            "log_time" => time(),
            "account_id" => $accountId,
        ], "member_log_undeposit");
        $this->updateUserCount($this->uid, "property", -$undepositAmount);

        $this->generateStatusResult("initiateUndepositSuccess", 1);
        $this->outputResult(["logId" => $logId]);        
    }

    /**
     * User Undeposit Logs
     * @param void
     * 
     */
    public function undepositLog() : void 
    {
        $page = $_GET["page"] ?? 1;
        $size = $_GET["size"] ?? 10;

        $undepositLogs = $this->getUndepositLog([
            "uid" => $this->uid
        ], $page, $size);
        $pagemore = count($undepositLogs) == $size ? 1 : 0;
        
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "logs" => $undepositLogs,
            "page" => $page,
            "pagemore" => $pagemore,
        ]);
    }

    /**
     * user property log
     * @param void
     * 
     */
    public function propertyLog() : void
    {
        $page = $_GET["page"] ?? 1;
        $size = $_GET["size"] ?? 10;

        $propertyLogs = $this->getPropertyLog($this->uid, $page, $size);
        $pagemore = count($propertyLogs) == $size ? 1 : 0;
        
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "logs" => $propertyLogs,
            "page" => $page,
            "pagemore" => $pagemore,
        ]);
    }

    /**
     * user messages
     * @param void
     * 
     */
    public function userMessage() : void
    {
        $page = $_GET["page"] ?? 1;
        $size = $_GET["size"] ?? 10;

        $userMessages = $this->getUserMessages($this->uid, $page, $size);
        $pagemore = count($userMessages) == $size ? 1 : 0;
        
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "messages" => $userMessages,
            "page" => $page,
            "pagemore" => $pagemore,
        ]);
    }

    /**
     * mark message read done
     * @param void
     * 
     */
    public function messageReadDone() : void
    {
        $this->checkPost();
        $this->checkParameters(["message_id" => ["int", "nonzero"]]);

        $messageId = (int)$_GET["message_id"];
        $this->markMessageReadDone($messageId);

        $this->generateStatusResult("markReadDoneSuccess", 1);
        $this->outputResult();
    }
}
