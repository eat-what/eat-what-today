<?php

namespace EatWhat\Generator;

/**
 * Generator a middleware, storage object
 * 
 */
class Generator
{

	/**
     * generate a handle
     * 
     */
	public static function __callStatic($name, $args)
	{
		$handleClassName = "EatWhat\\".ucfirst($name)."\\".$args[0];
        $handleClass = new $handleClassName;
        return $handleClass->generate();
	}
}