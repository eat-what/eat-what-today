<?php

/**
 * some static operation in app
 *
 */

namespace EatWhat;

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
}