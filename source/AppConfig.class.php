<?php

/**
 * App Config 
 *
 */

namespace EatWhat;

use EatWhat\EatWhatStatic;

class AppConfig
{

    /**
     * already loaded config data
     *
     */
    static $loadedConfig = [];
    
    /**
     * return loaded config 
     *
     */
    public static function getLoadedConfig()
    {
        return self::$loadedConfig;
    }

    /**
     * set specific config
     *
     */
    public static function set($configType, $configValue)
    {
        self::$loadedConfig[$configType] = $configValue; 
    }

    /**
     * get specific config
     *
     */
    public static function get($configName, $configType = "global")
    {
        if( isset(self::$loadedConfig[$configType][$configName]) ) {
            $configValue = self::$loadedConfig[$configType][$configName];
            return $configValue;
        } else {
            $configFile = CONFIG_PATH."config_".$configType.".php";
            if( EatWhatStatic::checkFile($configFile) ) {
                $requireConfig = require_once $configFile;
                if( isset($requireConfig[$configName]) ) {
                    $configValue = $requireConfig[$configName];
                    self::set($configType, $requireConfig);
                    return $configValue;
                }
            }
        }

        return null;
    }
}