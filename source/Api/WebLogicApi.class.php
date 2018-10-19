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
            chdir("/web/www/eat-what/");
            putenv("HOME=/home/daemon/");
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
