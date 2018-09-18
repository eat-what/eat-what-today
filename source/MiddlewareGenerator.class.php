<?php

namespace EatWhat;

/**
 * Middleware generator 
 * 
 */
class MiddlewareGenerator
{
    /**
     * generate a middleware handle
     * 
     */
    public static function generate($handle)
    {
        $middleClassName = "EatWhat\\Middleware\\".$handle;
        $middleClass = new $middleClassName;
        return $middleClass->generate();
    }
}