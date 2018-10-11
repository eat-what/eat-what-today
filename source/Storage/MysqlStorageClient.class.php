<?php

/**
 * mysql client
 *
 */

namespace EatWhat\Storage;

use EatWhat\EatWhatLog;
use EatWhat\Base\StorageBase;

class MysqlStorageClient extends StorageBase
{
    /**
     * get mysql client obj
     * 
     */
    public static function getClient()
    {
        static::getStorageConfig();
        $dsn = "mysql:dbname=".self::$config["dbname"].";host=".self::$config["host"];
        // get mysql obj
        try {
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8",
                \PDO::ATTR_TIMEOUT => self::$config["timeout"]
            ];

            $pdoClient = new \PDO($dsn, self::$config["dbuser"], self::$config["passwd"], $options);
            return $pdoClient;
        } catch (\PDOException  $exception) {
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