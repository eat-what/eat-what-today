<?php

/**
 * bulid request, route
 *
 */

namespace EatWhat;

class EatWhatRequest
{
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

    }

    /**
     * invoke
     * 
     */
    public function __invoke() 
    {

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
}