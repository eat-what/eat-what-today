<?php

namespace EatWhat\Base;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatBase;
use EatWhat\AppConfig;
use EatWhat\EatWhatStatic;
use EatWhat\Generator\Generator;
use EatWhat\Storage\Dao\MysqlDao;

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
        $this->userData = $request->getUserData();
        $this->mysqlDao = new MysqlDao($request);
        $this->redis = Generator::storage("storageClient", "Redis");
        $this->mongodb = Generator::storage("storageClient", "Mongodb");
    }

    /**
     * output result
     * 
     */
    public function outputResult($result) {
        $this->request->outputResult($result);
    }

    /**
     * generate an array that includ a note and acode
     * 
     */
    public function generateStatusResult(string $langName, int $code, bool $isLang = true) : array
    {
        return $this->request->generateStatusResult($langName, $code, $isLang);
    }

    /**
     * check post request
     * 
     */
    public function checkPost() : void
    {
        if( !EatWhatStatic::checkPost() ) {
            $this->outputResult($this->generateStatusResult("illegalRequest", -1));
        }
    }
}