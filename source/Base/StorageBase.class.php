<?php

namespace EatWhat\Base;

use EatWhat\AppConfig;

/**
 * Middleware Base
 * 
 */
abstract class StorageBase
{
    /**
     * config
     * 
     */
    public static $config;

    /**
     * static classname
     * 
     */
    public static function className($withoutNamespace = false)
    {
        $classname = get_called_class();
        $withoutNamespace && ($classname = substr($classname, (strrpos($classname, "\\") + 1)));
        return $classname;
    }

    /**
     * get storage obj config
     * 
     */
    public static function getStorageConfig()
    {
        $classname = static::className(true);
        self::$config = AppConfig::get($classname, "storage");
    } 

    /**
     * get client
     * 
     */
    abstract static public function getClient();
}