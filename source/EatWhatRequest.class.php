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
    public $args = [];

    /**
     * route 
     * 
     */
    public function __construct()
    {
        $params = $_GET;
        $this->setClass($_GET["cls"] ?? "EatWhat");
        $this->setMethod($_GET["mtd"] ?? "EatWhat");
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
        $instanceName = "EatWhat\\Api\\" . ucfirst($this->class) . "Api";
        if(class_exists($instanceName) && method_exists($instanceName, $this->method) && is_callable([$instanceName, $this->method])) {
            $methodObj = new \ReflectionMethod($instanceName, $this->method);
            if($methodObj->getParameters()) {
                $this->getArgs();
            }
            $api = new $instanceName();
            call_user_func_array([$api, $this->method], $this->args);
        }
    }

    /**
     * set class
     * 
     */
    public function setClass($class)
    {   
        $this->class = $class;
    }

    /**
     * set method
     * 
     */
    public function setMethod($method)
    {   
        $this->method = $method;
    }

    /**
     * set method
     * 
     */
    public function setArgs($args)
    {   
        $this->args = $args;
    }

    /**
     * get api args
     * 
     */
    public function getArgs()
    {
        $args = $_GET;
        unset($args["cls"], $args["mtd"]);
        $this->setArgs($args);
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