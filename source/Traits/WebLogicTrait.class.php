<?php

namespace EatWhat\Traits;

/**
 * Eat Traits For Eat Api
 * 
 */
trait WebLogicTrait
{
    /**
     * verify the request of github webhook by signature
     * 
     */
    public function verifyGithubWebHookSignature() : bool
    {
        $headers = getallheaders();
        $payloadBody = file_get_contents("php://input");

        $signature = $headers["X-Hub-Signature"];
        $secretToken = getenv("SECRET_TOKEN");

        $verifyHashHex = "sha1=" . hash_hmac("sha1", $payloadBody, $secretToken);
        
        return hash_equals($signature, $verifyHashHex);
    }

        /**
     * send sms
     * 
     */
    public function sendSms(array $parameters) : bool
    {
        require_once SDK_PATH . 'aliyun-dysms-php-sdk/api_sdk/vendor/autoload.php'; 
        \Aliyun\Core\Config::load();

        //产品名称:云通信短信服务API产品,开发者无需替换
        $product = "Dysmsapi";
        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";
        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = $parameters["accessKey"]; // AccessKeyId
        $accessKeySecret = $parameters["accessSecert"]; // AccessKeySecret
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        // 服务结点
        $endPointName = "cn-hangzhou";

        //初始化acsClient,暂不支持region化
        $profile = \Aliyun\Core\Profile\DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // 增加服务结点
        \Aliyun\Core\Profile\DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        // 初始化AcsClient用于发起请求
        $client = new \Aliyun\Core\DefaultAcsClient($profile);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new \Aliyun\Api\Sms\Request\V20170525\SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($parameters["mobile"]);
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($parameters["signName"]);
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($parameters["templateCode"]);
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode($parameters["params"], JSON_UNESCAPED_UNICODE));

        // 发起访问请求
        $acsResponse = $client->getAcsResponse($request);
        // print_r($acsResponse);die;
        return true;
    }

    /**
     * check ip request limit
     * 
     */
    public function checkSmsIpRequestLimit(int $count = 5) : bool
    {
        $ip = getenv("REMOTE_ADDR");
        $requestCount = $this->redis->get($ip . "_sms_request_count");
        $requestTime = $this->redis->get($ip . "_sms_request_time");

        if($requestTime || $requestCount > $count) {
            return false;
        }

        return true;
    }
}