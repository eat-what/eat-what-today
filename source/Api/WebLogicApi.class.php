<?php

namespace EatWhat\Api;

use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;
use EatWhat\EatWhatStatic;
use EatWhat\AppConfig;

/**
 * Eat Api
 * 
 */
class WebLogicApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\WebLogicTrait,\EatWhat\Traits\CommonTrait;

    /**
     * github Webhook when push event triggered
     * 
     */
    public function githubWebHook() : void
    {
        $verifyResult = $this->verifyGithubWebHookSignature();
        if( $verifyResult ) {
            putenv("HOME=/home/daemon/");
            chdir("/web/www/eat-what/");
            $cmd = "git pull --rebase 2>&1";
            exec($cmd, $o);
            print_r($o);
        } else {
            EatWhatLog::logging("Illegality Github WebHook Request", [
                "ip" => getenv("REMOTE_ADDR"),
            ]);
            echo "Faild";
        }
    }

    /**
     * send verify code by sms
     * 
     */
    public function sendVerifyCode() : void
    {
        $this->checkPost();
        if(!$this->checkSmsIpRequestLimit()) {
            $this->generateStatusResult("sendSmsError", -1);
        }

        $mobile = $_GET["mobile"];
        if(!EatWhatStatic::checkMobileFormat($mobile)) {
            $this->generateStatusResult("wrongMobileFormatOrExists", -2);
        }        

        $type = $_GET["type"] ?? "login"; // login/join/modifyMobile
        $code = EatWhatStatic::getRandom(4);
        $smsConfig = AppConfig::get("sms", "global");

        $smsParameters = [];
        $smsParameters["mobile"] = $mobile;
        $smsParameters["mobile"] = $mobile;
        $smsParameters["accessKey"] = $smsConfig["accessKey"];
        $smsParameters["accessSecert"] = $smsConfig["accessSecert"];
        $smsParameters["signName"] = $smsConfig["verifyCode"]["signName"];
        $smsParameters["templateCode"] = $smsConfig["verifyCode"]["templateCode"];
        $smsParameters["params"] = [
            "code" => $code,
        ];
        $result = $this->sendSms($smsParameters);

        if( $result ) {
            $expire = 5 * 60;
            $this->redis->set($mobile . "_" . $type, $code, $expire);
            $this->redis->set(getenv("REMOTE_ADDR") . "_sms_request_time", time(), $expire);
            
            $countKey = getenv("REMOTE_ADDR") . "_sms_request_count";
            $count = $this->redis->get($countKey);
            if(!$count) {
                $this->redis->set($countKey, 1, 24 * 60 * 60);
            } else {
                $this->incr($countKey);
            }

            $this->generateStatusResult("sendSmsSuccess", 1);
            $this->outputResult();
        } else {
            $this->generateStatusResult("sendSmsError", -1);
        }
    }
}
