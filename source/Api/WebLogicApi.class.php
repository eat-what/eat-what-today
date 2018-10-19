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
}
