<?php

/**
 * mysql client
 *
 */

namespace EatWhat\Storage;

use EatWhat\AppConfig;
use EatWhat\EatWhatStatic;
use EatWhat\Exception\EatWhatPdoException;

class MysqlStorageClient
{
    /**
     * get mysql client obj
     * 
     */
    public static function getClient()
    {
        $config = AppConfig::get("MysqlStorageClient", "storage");
        $dsn = "mysql:dbname=".$config["dbname"].";host=".$config["host"];
        try {
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8",
            ];

            $pdoClient = new \PDO($dsn, $config["dbuser"], $config["passwd"], $options);
            return $this->pdoClient;
        } catch (EatWhatPdoException $exception) {
            if( !DEVEMODE ) {
                EatWhatStatic::log($exception->getMessage());
            } else {
                throw $exception;
            }
        }
    }
}