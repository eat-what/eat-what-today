<?php

namespace EatWhat\Middleware;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\EatWhatStatic;
use EatWhat\EatWhatRequest;
use EatWhat\Base\MiddlewareBase;
use EatWhat\Exceptions\EatWhatException;

/**
 * check request api and mtd legality
 * 
 */
class verifyApiAndMethod extends MiddlewareBase
{
    /**
     * return a callable function
     * 
     */
    public static function generate()
    {
        return function(EatWhatRequest $request, callable $next) 
        {
            $api = $request->getApi();
            $method = $request->getMethod();
            $legalApiAndMethod = AppConfig::get("legalApiAndMethod", "global");

            if(!isset($legalApiAndMethod[$api]) || !in_array($method, $legalApiAndMethod[$api])) {
                if( !DEVELOPMODE ) {
                    EatWhatLog::logging("Illegality Request With Wrong Api or Method.", [
                        "ip" => getenv("REMOTE_ADDR"),
                        "api" => $api,
                        "method" => $method,
                    ]);
                    EatWhatStatic::illegalRequestReturn();
                } else {
                    throw new EatWhatException("Wrong Api or Method, Check it.");
                }
            } else {
                $next($request);
            }
        };
    }
}