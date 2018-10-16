<?php

namespace EatWhat\Traits;

/**
 * Eat Traits For Eat Api
 * 
 */
trait EatTrait
{
    /**
     * verify the request of github webhook by signature
     * 
     */
    public function verifyGithubWebHookSignature()
    {
        $headers = getallheaders();
        $json = json_decode(file_get_contents("php://input"), true);

        $signature = $headers["X-Hub-Signature"];
        $secretToken = getenv("SECRET_TOKEN"); 
    }
}