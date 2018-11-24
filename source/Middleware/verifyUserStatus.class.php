<?php

namespace EatWhat\Middleware;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\EatWhatStatic;
use EatWhat\EatWhatRequest;
use EatWhat\Generator\Generator;
use EatWhat\Base\MiddlewareBase;
use EatWhat\Exceptions\EatWhatException;

/**
 * check request api and mtd legality
 * 
 */
class verifyUserStatus extends MiddlewareBase
{
    /**
     * return a callable function
     * 
     */
    public static function generate()
    {
        return function(EatWhatRequest $request, callable $next) 
        {
            $userData = $request->getUserController()->getUserData();
            if(empty($userData)) {
                $request->outputResult($request->generateStatusResult("actionWithLogIn", -400));
            } else if($userData["tokenStatus"] == -401) {
                $request->outputResult($request->generateStatusResult("loginStatusHasExpired", -401));
            } else {
                $next($request);
            }
        };
    }
}