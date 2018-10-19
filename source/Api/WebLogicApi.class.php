<?php

namespace EatWhat\Api;

use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;

/**
 * Eat Api
 * 
 */
class WebLogicApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\WebLogicTrait;

    /**
     * github Webhook when push event triggered
     * 
     */
    public function githubWebHook()
    {
        $verifyResult = $this->verifyGithubWebHookSignature();
        if( $verifyResult ) {
            putenv("HOME=/home/daemon/");
            $cmd = "cd /web/www/eat-what/ && git pull --rebase";
            $resource = popen($cmd, "r");
            if($resource)
                echo "Success";
            else 
                echo "popen faild";
        } else {
            EatWhatLog::logging("Illegality Github WebHook Request", [
                "ip" => getenv("REMOTE_ADDR"),
            ]);
            echo "Faild";
        }
    }
}
