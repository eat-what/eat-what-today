<?php

/**
 * mysql client
 *
 */

namespace EatWhat\Storage;

use EatWhat\EatWhatLog;
use EatWhat\Base\StorageBase;

class MongodbStorageClient extends StorageBase
{
    /**
     * get mysql client obj
     * 
     */
    public static function getClient()
    {
        static::getStorageConfig();
        try {
            $mongoClient = new \MongoDB\Client(self::$config["uri"]);
            return $mongoClient->{self::$config["dbname"]};
        } catch(\MongoDB\Driver\Exception\ConnectionException $exception) {
            if( !DEVELOPMODE ) {
                EatWhatLog::logging($exception, array(
                    "line" => $exception->getLine(),
                    "file" => $exception->getFile(),
                ));
            } else {
                throw $exception;
            }
        }
        
    }
}