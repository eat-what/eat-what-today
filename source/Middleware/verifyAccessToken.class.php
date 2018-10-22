<?php

namespace EatWhat\Middleware;

use EatWhat\EatWhatRequest;
use EatWhat\Base\MiddlewareBase;

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
            // $accessToken = $_COOKIE["access_token"];
            $next($request);
        };
    }
}