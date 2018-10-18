<?php

namespace EatWhat\Base;

use EatWhat\EatWhatBase;
use EatWhat\Generator\Generator;

/**
 * Api Base
 * 
 */
class ApiBase extends EatWhatBase
{
    /**
     * Api Constructor!
     * 
     */
    public function __construct()
    {
        //$this->pdo = Generator::storage("storageClient", "Mysql");
        //$this->redis = Generator::storage("storageClient", "Redis");
        //$this->mongodb = Generator::storage("storageClient", "Mongodb");
    }
}