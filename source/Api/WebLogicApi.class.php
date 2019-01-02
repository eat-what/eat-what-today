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
    use \EatWhat\Traits\UserTrait,\EatWhat\Traits\OrderTrait;
    use \EatWhat\Traits\GoodTrait;

    const DOWNLOAD_TYPES = ["order", "member"];

    /**
     * github Webhook when push event triggered
     * @param void
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
     * @param void
     * 
     */
    public function sendVerifyCode() : void
    {
        $this->checkPost();
        $this->checkParameters(["mobile" => null]);
        if(!$this->checkSmsIpRequestLimit()) {
            $this->generateStatusResult("sendSmsError", -1);
        }

        $mobile = $_GET["mobile"];
        if( !EatWhatStatic::checkMobileFormat($mobile) ) {
            $this->generateStatusResult("wrongMobileFormatOrExists", -2);
        }        

        $type = $_GET["type"] ?? "login"; // login/join/modifyMobile
        $code = EatWhatStatic::getRandom(4);
        $smsConfig = AppConfig::get("sms", "global");

        $smsParameters = [];
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
            $expire = 1 * 60;
            $this->redis->set($mobile . "_" . $type, $code, $expire);
            $this->redis->set(getenv("REMOTE_ADDR") . "_sms_request_time", time(), $expire);
            
            $countKey = getenv("REMOTE_ADDR") . "_sms_request_count";
            $count = $this->redis->get($countKey);
            if(!$count) {
                $this->redis->set($countKey, 1, 24 * 60 * 60);
            } else {
                $this->redis->incr($countKey);
            }

            $this->generateStatusResult("sendSmsSuccess", 1);
            $this->outputResult();
        } else {
            $this->generateStatusResult("sendSmsError", -1);
        }
    }

    /**
     * get province all allowable  
     * @param void
     * 
     */
    public function getProvinceAllowable() : void 
    {
        $this->generateStatusResult("200 OK", 1, false);
        $this->outputResult([
            "province" => AppConfig::get("province_id", "global"),
        ]);
    }

    /**
     * download orders/members etc, csv format
     * @param void
     *
     */
    public function triggerDownload() : void
    {
        $this->checkPost();
        $this->checkParameters(["type" => [self::DOWNLOAD_TYPES], "filters" => ["json"]]);

        $downloadType = $_GET["type"];
        $csvHeaders = AppConfig::get("csv_headers", "global");

        $downloadData = $this->redis->get("downloadcsvdata_" . $downloadType);
        if( !$downloadData ) {
            switch( $downloadType ) {
                case "order":
                $_GET["filters"]["manage"] = true;
                extract($this->getOrderList($_GET["filters"], false));
                $downloadData = $orders;
                break;

                case "member":
                extract($this->getMemberList($_GET["filters"], false));
                $downloadData = $members;
                break;
            }
        } 

        if(empty($downloadType)) return;

        /* export data with csv format*/
        $tmpFile = new \SplTempFileObject(10);
        $tmpFile->fwrite(AppConfig::get("csvFileCanBeOpendByExcel", "lang") . PHP_EOL . PHP_EOL);
        $tmpFile->fputcsv($csvHeaders[$downloadType], ",", " ");
        foreach($downloadData as $csvFields) {
            $tmpFile->fputcsv($csvFields, ",", "\"", "\\");
        }

        $contentLength = $tmpFile->ftell();
        header("Content-Disposition: attachment; filename=" . hash("sha256", time()) . ".csv");
        header("Content-Type: application/octet-stream;charset=utf-8");
        header("Content-Length: " . $contentLength);

        $tmpFile->rewind();
        $tmpFile->fpassthru();

        $log = "Download Type: order  Download Length: " . $contentLength;
        EatWhatLog::logging($log, [
            "request_id" => $this->request->getRequestId(),
        ], "file", date("Y-m") . "_download.log");
    }

    /**
     * check order expired, for crontab per 15 mins
     * @param void
     * 
     */
    public function checkOrderExpiredRegularTask() : void
    {
        $result = $this->checkOrderExpired();

        if($result) {
            EatWhatLog::logging($result, [
                "request_id" => $this->request->getRequestId(),
            ], "file", "check_order_expired.log");
        }
    }

    /**
     * crontab task to process user financing income at 0 o'clock everyday
     *
     */
    public function processUserFinancingIncomeTask() : void
    {
        $result = $this->processUserFinancingIncome();

        if($result) {
            file_put_contents(LOG_PATH . "process_user_financing_income_" . date("Y-m") . ".log", $result, FILE_APPEND);
        }
    }
}
