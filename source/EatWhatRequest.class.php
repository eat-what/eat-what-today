<?php

/**
 * bulid request, route
 *
 */

namespace EatWhat;

use EatWhat\EatWhatStatic;
use EatWhat\Exceptions\EatWhatException;

class EatWhatRequest
{
    /**
     * middlewares 
     * 
     */
    public $middlewares = [];

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
    public function invoke() 
    {
        if(!empty($this->middlewares)) {
            $handle = array_reduce(array_reverse($this->middlewares), function($next, $middleware){
                return function($request) use($next, $middleware) {
                    $middleware($request, $next);
                };
            }, [$this, "call"]);

            $handle($this);
        } else {
            $this->call();
        }
    }

    /**
     * call after middleware filter
     * 
     */
    public function call()
    {
        echo "welcome to here!";
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
     * add a request filter
     * 
     */
    public function addMiddleWare(callable $middleware)
    {
        $this->middlewares[] = $middleware;
    }
}