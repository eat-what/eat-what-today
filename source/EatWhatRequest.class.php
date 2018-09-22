<?php

/**
 * bulid request, route
 *
 */

namespace EatWhat;

class EatWhatRequest
{
    /**
     * middlewares 
     * 
     */
    public $middwares = [];

    /**
     * class
     * 
     */
    public $class;

    /**
     * method 
     * 
     */
    public $method;

    /**
     * method args
     * 
     */
    public $args;

    /**
     * route 
     * 
     */
    public function __construct()
    {
        $params = $_GET;
        $this->class = $_GET["cls"];
        $this->method = $_GET["mtd"];
    }

    /**
     * invoke
     * 
     */
    public function __invoke() 
    {
        array_reduce();
    }

    /**
     * set class
     * 
     */
    public function setClass($class)
    {   
        $this->$class = $class;
    }

    /**
     * set method
     * 
     */
    public function setMethod($method)
    {   
        $this->$method = $method;
    }

    /**
     * set method
     * 
     */
    public function setArgs($args)
    {   
        $this->$args = $args;
    }

    /**
     * get GP value
     * 
     */
    public function getGPValue($key)
    {
        if(isset($_GET[$key])) {
            return $_GET[$key];
        }
        return "";
    }

    /**
     * add a request filter
     * 
     */
    public function addMiddleWare(callable $middleware)
    {
        $this->middwares[] = $middleware;
    }
}