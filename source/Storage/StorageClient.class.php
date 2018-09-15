<?php

/**
 * database baseclass
 *
 */

namespace EatWhat\Storage;

use EatWhat\AppConfig;

abstract class StorageClient 
{

    /**
     * storage config 
     *
     */
    public $config = [];

    /**
     * get config
     * 
     */
    public function __construct()
    {
        $this->config = AppConfig::get(static::className(), "storage");
    }

    /**
     * static classname
     * 
     */
    public static function className()
    {
        $classname = get_called_class();
        return $classname;
    }

    /**
     * get storege client
     *
     */
    public function getClient();
}