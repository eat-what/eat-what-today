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
     * userid
     * 
     */
    private $userData = [];

    /**
     * middlewares 
     * 
     */
    private $middlewares = [];

    /**
     * api
     * 
     */
    private $api;

    /**
     * method 
     * 
     */
    private $method;

    /**
     * method args
     * 
     */
    private $args = [];

    /**
     * access token analyzer
     * 
     */
    private $accessTokenAnalyzer;

    /**
     * user data manager
     * 
     */
    private $userController;

    /**
     * route 
     * 
     */
    public function __construct()
    {
        $params = $_GET;
        $this->setApi($_GET["api"] ?? "EatWhat");
        $this->setMethod($_GET["mtd"] ?? "EatWhat");
    }

    /**
     * set api
     * 
     */
    private function setApi($api)
    {   
        $this->api = ucfirst($api);
    }

    /**
     * set method
     * 
     */
    private function setMethod($method)
    {   
        $this->method = $method;
    }

    /**
     * set method
     * 
     */
    private function setArgs($args)
    {   
        $this->args = $args;
    }

    /**
     * set access token analyzer
     * 
     */
    public function setAccessTokenAnalyzer($analyzer)
    {
        $this->accessTokenAnalyzer = $analyzer;
    }

    /**
     * set user controller
     * 
     */
    public function setUserController($controller)
    {
        $this->userController = $controller;
    }

    /**
     * get api args
     * 
     */
    public function getArgs()
    {
        $args = $_GET;
        unset($args["api"], $args["mtd"]);
        $this->setArgs($args);
    }

    /**
     * get api
     * 
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * get method
     * 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * get access token analyzer obj
     * 
     */
    public function getAccessTokenAnalyzer()
    {
        return $this->accessTokenAnalyzer;
    }

    /**
     * get user controller
     * 
     */
    public function getUserController()
    {
        return $this->userController;
    }

    /**
     * add a request filter
     * 
     */
    public function addMiddleWare(callable $middleware)
    {
        $this->middlewares[] = $middleware;
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
        $instanceName = "EatWhat\\Api\\" . ucfirst($this->api) . "Api";
        if(class_exists($instanceName) && method_exists($instanceName, $this->method) && is_callable([$instanceName, $this->method])) {
            $methodObj = new \ReflectionMethod($instanceName, $this->method);
            if($methodObj->getParameters()) {
                $this->getArgs();
            }
            $api = new $instanceName($this);
            call_user_func_array([$api, $this->method], $this->args);
        }
    }

    /**
     * out put result with json format
     * 
     */
    public function outputRequestResult($result = [])
    {
        print_r($result);
    }
}