<?php

/**
 * mysql client
 *
 */

namespace EatWhat\Storage;

use EatWhat\AppConfig;
use EatWhat\Exception\EatWhatException;

class MysqlStorageClient
{
    /**
     * get mysql client obj
     * 
     */
    public static function getClient()
    {
        $config = AppConfig::get("MysqlStorageClient", "storage");
        print_r($config);die;
    }
}