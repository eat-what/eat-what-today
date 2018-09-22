<?php

namespace EatWhat\Exceptions;

/**
 * eat what exception
 * 
 */

class EatWhatException extends \Exception
{
    /**
     * Constructor!
     * 
     */
    public function __construct($message = "", $code = 0)
    {
        $message = $message . " EatWhat " . PHP_EOL;
        parent::__construct($message, $code);
    }
}