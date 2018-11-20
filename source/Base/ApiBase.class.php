<?php

namespace EatWhat\Base;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatBase;
use EatWhat\AppConfig;
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
        $this->mysqlDao = new MysqlDao;
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
    public function generateErrorResult(string $langName, int $code) : array
    {
        $result = [
            "note" => AppConfig::get($langName, "lang"),
            "code" => $code,
        ];
        return $result;
    }
}