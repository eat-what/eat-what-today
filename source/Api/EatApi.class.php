<?php

namespace EatWhat\Api;

use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;

/**
 * Eat Api
 * 
 */
class EatApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\EatTrait;

    /**
     * method what!
     * 
     */
    public function What()
    {
        echo "EatWhat!";
    }

    /**
     * github Webhook when push event triggered
     * 
     */
    public function githubWebHook()
    {
        $verifyResult = $this->verifyGithubWebHookSignature();
        if( $verifyResult ) {
            $cmd = "cd /web/www/eatwhat/ && git pull --rebase";
            pclose(popen($cmd, "r"));
        } else {
            EatWhatLog::logging("Illegality Github WebHook Request", [
                "ip" => getenv("REMOTE_ADDR"),
            ]);
        }
    }
}