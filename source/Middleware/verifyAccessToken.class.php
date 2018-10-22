<?php

namespace EatWhat\Middleware;

use EatWhat\EatWhatRequest;
use EatWhat\Base\MiddlewareBase;
use EatWhat\Exceptions\EatWhatException;

/**
 * verify user access token
 * 
 */
class verifyAccessToken extends MiddlewareBase
{
    /**
     * return a callable function
     * 
     */
    public static function generate()
    {
        return function(EatWhatRequest $request, callable $next)
        {
            $verifyResult = $request->getAccessTokenAnalyzer()->verify();
            if(!$verifyResult) {
                if( !DEVELOPMODE ) {
                    EatWhatLog::logging("Illegality Access Token.", [
                        "ip" => getenv("REMOTE_ADDR"),
                    ]);
                    EatWhatStatic::illegalRequestReturn();
                } else {
                    throw new EatWhatException("Illegality Access Token, Check it.");
                }
            } else {
                $request->setUserData($verifyResult);
                $next($request);
            }
        };
    }
}