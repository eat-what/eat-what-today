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
        if( !EatWhatStatic::checkPostMethod() ) {
            $this->request->outputResult($this->generateErrorResult("illegalRequest", -1));
        }

        // check mobile
        $mobile = $_GET["mobile"];
        if( !$this->checkMobile($mobile) ) {
            $this->request->outputResult($this->generateErrorResult("wrongMobileFormatOrExists", -2));
        }

        // check mobile code
        if( !($verifyCode = $_GET["verifyCode"]) || !$this->checkMobileCode($mobile, $verifyCode, "register") ) {
            $this->request->outputResult($this->generateErrorResult("wrongVerifyCode", -3));
        }

        // check username
        if( !$this->checUserName($_GET["username"]) ) {
            $this->request->outputResult($this->generateErrorResult("wrongUsernameFormatOrExists", -4));
        }
    }
}
