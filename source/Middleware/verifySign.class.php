<?php

namespace EatWhat\MiddleWare;

use EatWhat\Middleware\MiddlewareBase;
use EatWhat\Exceptions\EatWhatException;
use EatWhat\AppConfig;
use EatWhat\EatWhatStatic;
use EatWhat\EatWhatRequest;

/**
 * check request sign middleware
 * 
 */
class verifySign implements MiddlewareBase
{
    /**
     * return a callable handler
     * 
     */
    public static function generate()
    {
        return function(EatWhatRequest $request, callable $next) {
            $signature = EatWhatStatic::getGPValue("signature");
            $verifyResult = static::verify($signature);
            if( !$verifyResult ) {
                throw new EatWhatException("Sign is incorrect, Check it.");
            } else {
                $next($request);
            }
        };
    }

    /**
     * verify sign
     * 
     */
    public static function verify($signature)
    {
        $pub_key_pem_file = AppConfig::get("pub_key_pem_file", "global");
        $pub_key = openssl_pkey_get_public($pub_key_pem_file);
        $data = EatWhatStatic::getGPValue("paramsSign");

        return openssl_verify($data, $signature, $pub_key, "sha256");
    }
}