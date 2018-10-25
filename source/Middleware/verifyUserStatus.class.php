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
            $userData = $request->getUserData();
            if(empty($userData)) {
                $request->outputResult([
                    "login" => 1,
                ]);
            } else if($userData["userStatus"] < 0) {
                $request->outputResult([
                    "relogin" => 1,
                ]);
            } else {
                $next($request);
            }
        };
    }
}