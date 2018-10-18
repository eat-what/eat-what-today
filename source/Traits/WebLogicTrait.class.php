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
    public function verifyGithubWebHookSignature()
    {
        $headers = getallheaders();
        $payloadBody = file_get_contents("php://input");

        $signature = $headers["X-Hub-Signature"];
        $secretToken = getenv("SECRET_TOKEN");

        $verifyHashHex = "sha1=" . hash_hmac("sha1", $payloadBody, $secretToken);
        
        return hash_equals($signature, $verifyHashHex);
    }
}