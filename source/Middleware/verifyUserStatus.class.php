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
            if($userData["userStatus"] < 0) {
                if( !DEVELOPMODE ) {
                    EatWhatLog::logging("Illegality User Action.", [
                        "ip" => getenv("REMOTE_ADDR"),
                        "api" => $api,
                        "method" => $method,
                    ],
                    "file",
                    "user_action.log"
                    );
                    EatWhatStatic::illegalRequestReturn();
                } else {
                    throw new EatWhatException("Illegality User Action, Log In.");
                }
            } else {
                $next($request);
            }
        };
    }
}