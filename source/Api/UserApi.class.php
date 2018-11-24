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
    use \EatWhat\Traits\UserTrait;

    /**
     * register new user
     * 
     */
    public function register() : void
    {
        $this->checkPost();        

        $mobile = $_GET["mobile"];
        if( !$this->checkMobile($mobile) ) {
            $this->outputResult($this->generateStatusResult("wrongMobileFormatOrExists", -2));
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($mobile, $verifyCode, "register") ) {
            $this->outputResult($this->generateStatusResult("wrongVerifyCode", -3));
        }

        $username = $_GET["username"];
        if( !$this->checkUsername($username) ) {
            $this->outputResult($this->generateStatusResult("wrongUsernameFormatOrExists", -4));
        }
        
        $newUser = [];
        $newUser["mobile"] = $mobile;
        $newUser["username"] = $username;
        $newUser["create_time"] = time();
        $newUser["last_login_time"] = time();
        $_GET["lastUid"] && ($newUser["last_login_time"] = $_GET["lastUid"]);
        $newUserId = $this->createNewUser($newUser);

        $this->setDefaultAvatar($newUserId, hash("sha256", $username));

        $userData = [
            "uid" => $newUserId,
            "username" => $username,
            "level" => 0,
            "mobile" => $mobile,
            "avatar" => $this->getUserAvatar($newUserId),
        ];
        $this->setUserLogin($userData);
        $this->outputResult($this->generateStatusResult("registerActionSuccess", 1));
    }
    
    /**
     * set user info
     * 
     */
    public function setUserLogin(array $userData) : void
    {
        $accessToken = $this->request->getAccessTokenAnalyzer()->generate($userData);
        $requestUserController =  $this->request->getUserController();
        $requestUserController->setUserData($userData);
        $requestUserController->setAccessToken($accessToken);
    }

    /**
     * login
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
     * 
     */
    public function loginByVerifyCode()
    {
        $mobile = $_GET["mobile"];
        $user = $this->getUserBaseInfo($mobile);

        if(!$user || $user["status"] < 0) {
            $this->outputResult($this->generateStatusResult("userStatusAbnormal", -1)); 
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($mobile, $verifyCode, "login") ) {
            $this->outputResult($this->generateStatusResult("wrongVerifyCode", -3));
        }
        
        $this->setUserLogin([
            "uid" => $user["id"],
            "username" => $user["username"],
            "level" => $user["level"],
            "mobile" => $user["mobile"],
            "avatar" =>  $this->getUserAvatar($user["id"]),
        ]);
        $this->outputResult($this->generateStatusResult("loginActionSuccess", 1));
    }

    /**
     * modify user mobile
     * 
     */
    public function modifyMobile()
    {
        $this->checkPost();

        $newMobile = $_GET["newmobile"];
        if( !$this->checkMobile($newMobile) ) {
            $this->outputResult($this->generateStatusResult("wrongMobileFormatOrExists", -2));
        }

        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($newMobile, $verifyCode, "modifyMobile") ) {
            $this->outputResult($this->generateStatusResult("wrongVerifyCode", -3));
        }

        $this->updateUserMobile($newMobile);
        $this->outputResult($this->generateStatusResult("updateSuccess", 1));
    }

    /**
     * log out
     * 
     */
    public function logout() : void
    {
        $this->request->getUserController()->logout();
        $this->outputResult($this->generateStatusResult("logoutSuccess", 1));
    }

    /**
     * modify user info
     * 
     */
    public function modifyUserAvatar() : void
    {
        $this->checkPost();

        $userAvatar = $this->getUserAvatar($this->userData["uid"], false);
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
            $this->outputResult($this->generateStatusResult("modifyAvatarSuccess", 1));
        } else {
            $this->outputResult($this->generateStatusResult($files[0]->error, -1, false));
        }
    }

    /**
     * modify user base info
     * sex age location
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
        $this->outputResult($this->generateStatusResult("updateSuccess", 1));
    }
}
