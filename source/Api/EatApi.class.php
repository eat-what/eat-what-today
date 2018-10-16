<?php

namespace EatWhat\Api;

use EatWhat\EatWhatLog;

/**
 * Eat Api
 * 
 */
class EatApi
{
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
        $json = json_decode(file_get_contents("php://input"), true);
        EatWhatLog::logging($json);
    }
}