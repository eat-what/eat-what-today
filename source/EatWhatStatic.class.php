<?php

/**
 * some static operation in app
 *
 */

namespace EatWhat;

use EatWhat\AppConfig;

class EatWhatStatic
{
    /**
     * check file exists and can be read
     *
     */
    public static function checkFile($file)
    {
        return file_exists($file) && is_readable($file);
    }

    /**
     * check a variable is empty except the val in the array exclude
     *
     */
    public static function checkEmpty($value, $exclude = [])
    {
        $exclude && !is_array($exclude) && ($exclude = [$exclude]);

        if( $exclude && in_array($value, $exclude, true) )
            return false;

        if( empty($value) ) {
            return true;
        }

        return false;
    }

    /**
     * check http method is post
     * 
     */
    public static function checkPostMethod()
    {
        return getenv("REQUEST_METHOD") == "POST";
    }

    /**
     * get passed params sign
     * 
     */
    public static function getParamsSign()
    {
        $data = json_encode($_GET);
        $pri_key_pem_file = AppConfig::get("pri_key_pem_file", "global");
        $pri_key = openssl_pkey_get_private($pri_key_pem_file);

        openssl_sign($data, $signature, $pri_key, "sha256");
        return $signature;
    }

    /**
     * get GP value
     * 
     */
    public static function getGPValue($key)
    {
        if(isset($_GET[$key])) {
            return $_GET[$key];
        }
        return "";
    }

    /**
     * illegal return
     * 
     */
    public static function illegalRequestReturn()
    {
        $output = <<<EATWHAT
------------------------------------------------------------
|        _                _           _                     |
|        ___  __ _| |_    __      _| |__   __ _| |_         |
|       / _ \/ _` | __|___\ \ /\ / / '_ \ / _` | __|        |
|      |  __/ (_| | ||_____\ V  V /| | | | (_| | |_         |
|       \___|\__,_|\__|     \_/\_/ |_| |_|\__,_|\__|        |
|                                                           | 
------------------------------------------------------------
EATWHAT;
        http_response_code(500);
        exit($output);
    }

    /**
     * convert a number to a specify base, $int is a decimal number
     * 
     */
    public static function convertBase(int $int, $tobase) : string
    {
        if($tobase <= 32 && $tobase >= 2) {
            return base_convert($int, 10, $tobase);
        } else if($tobase > 32 && $tobase <= 62) {
            $mod = self::numberToAscii($int % $tobase);
            $div = (int)($int / $tobase);
            if($div >= $tobase) {
                return self::convertBase($div, $tobase) . $mod;
            } else {
                return self::numberToAscii($div) . $mod;
            }
        }
    }

    /**
     * multiple band and ascii convert by bit
     * 
     */
    public static function numberToAscii(int $num) : string
    {
        if($num >= 10 && $num <= 35) {
            $num = chr($num + 55);
        } else if($num > 35 && $num <= 61) {
            $num = chr($num + 61);
        }
        return $num;
    }

    /**
     * trim value recursive
     * 
     */
    public static function trimValue(array &$value) : void
    {
        foreach($value as &$v) {
            if( is_array($v) ) {
                self::trimValue($v);
            } else {
                $v = trim($v);
            }
        }
    }
}