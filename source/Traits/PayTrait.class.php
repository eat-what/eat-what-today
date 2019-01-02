<?php

namespace EatWhat\Traits;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\EatWhatStatic;

/**
 * Car Traits For User Api
 * 
 */
trait PayTrait
{
    /**
     * verify webhooks signature
     * 
     */
    public function verifyWebHookSign(string $raw_data) : bool
    {
        $headers = EatWhatStatic::getallheaders();
        $signature = $headers["X-Pingplusplus-Signature"] ?? NULL;
        $pubkey = file_get_contents( (AppConfig::get("pingpp", "pay"))["webhook_public_key"] );

        return openssl_verify($raw_data, base64_decode($signature), $pubkey, "sha256");
    }

    /**
     * get ping++ channel
     * [["alipay", "wx", "wx_lite"]], // 支付宝app,微信app,微信小程序
     * 
     */
    public function getPingppChannel(int $payChannel, string $source) : string
    {
        if( $source == "app" ) {
            if($payChannel == 1) {
                return "alipay";
            } else if($payChannel == 2) {
                return "wx";
            }
        } else if($source == "wx"){
            return "wx_lite";
        }
    }

    /**
     * inform admin to process order after user paied
     * 
     */
    public function orderPaiedInform() : bool
    {
        $smsConfig = AppConfig::get("sms", "global");

        $result = $this->sendSms([
            "mobile" => $this->getSetting("adminMobile"),
            "accessKey" => $smsConfig["accessKey"],
            "accessSecert" => $smsConfig["accessSecert"],
            "signName" => $smsConfig["orderPaiedInform"]["signName"],
            "templateCode" => $smsConfig["orderPaiedInform"]["templateCode"],
        ]);

        return $result;
    }
}