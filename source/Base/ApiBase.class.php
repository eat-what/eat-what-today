<?php

namespace EatWhat\Base;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatBase;
use EatWhat\Generator\Generator;

/**
 * Api Base
 * 
 */
class ApiBase extends EatWhatBase
{
    /**
     * pdo connection obj
     * 
     */
    protected $pdo;

    /**
     * redis connection obj
     * 
     */
    protected $redis;

    /**
     * mongodb connection obj
     * 
     */
    protected $mongodb;

    /**
     * request obj
     * 
     */
    protected $request;

    /**
     * Api Constructor!
     * 
     */
    public function __construct(EatWhatRequest $request)
    {
        $this->request = $request;
        //$this->pdo = Generator::storage("storageClient", "Mysql");
        //$this->redis = Generator::storage("storageClient", "Redis");
        //$this->mongodb = Generator::storage("storageClient", "Mongodb");
    }
}