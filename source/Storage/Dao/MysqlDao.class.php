<?php

namespace EatWhat\Storage\Dao;

use EatWhat\Exceptions\EatWhatException;
use EatWhat\AppConfig;
use EatWhat\Generator\Generator;
use EatWhat\EatWhatRequest;
use EatWhat\EatWhatLog;

/**
 * dao of mysql
 * 
 */
class MysqlDao
{
    /**
     * bind vlaue type
     * 
     */
    public $bindTypes = [
        "int" => \PDO::PARAM_INT,
        "string" => \PDO::PARAM_STR,
    ];

    /**
     * mysql obj
     * 
     */
    public $pdo;

    /**
     * the table name
     * 
     */
    public $table;

    /**
     * the last sql
     * 
     */
    public $lastSql;

    /**
     * the executable sql
     * 
     */
    public $executeSql = "";

    /**
     * statment obj
     * 
     */
    public $pdoStatment;

    /**
     * statment obj
     * 
     */
    public $pdoException;

    /**
     * constructor!
     * 
     */
    public function __construct()
    {
        $this->pdo = Generator::storage("storageClient", "Mysql");
    }

    /**
     * get execute sql
     * 
     */
    public function getExecuteSql() : string
    {
        return $this->executeSql;
    }

    /**
     * ensure table name
     * 
     */
    public function table(string $tableName) : self
    {
        $this->table = (AppConfig::get("MysqlStorageClient", "storage"))["prefix"] . $tableName;

        return $this;
    }

    /**
     * ensure select section
     * 
     */
    public function select($select) : self
    {
        if( is_array($select) ) {
            $select = implode(",", $select);
        }
        $this->executeSql .= "SELECT $select FROM " . $this->table;

        return $this;
    }

    /**
     * ensure where section
     * 
     */
    public function where($where) : self
    {
        if( !is_array($where) ) {
            $where = explode(",", $where);
        }

        $this->executeSql .= " WHERE";
        foreach($where as $value) {
            $this->executeSql .= " $value = ? AND";
        }
        $this->executeSql = substr($this->executeSql, 0, -3);

        return $this;
    }

    /**
     * Prepares a statement for execution  
     * 
     */
    public function prepare() : self
    {
        try {
            $this->pdoStatment = $this->pdo->prepare($this->getExecuteSql(), [
                \PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL,
            ]);
        } catch( \PDOException $exception ) {
            if( !DEVELOPMODE ) {
                $this->pdoException = true;
                EatWhatLog::logging("DB can not prepare sql: " . (string)$exception . ". ", [
                    "request_id" => EatWhatRequest::$staticRequestId,
                ], "file", "pdo.log");
            } else {
                throw new EatWhatException((string)$exception);
            }
        }

        return $this;
    }

    /**
     * execute plan
     * 
     */
    public function execute(array $parameters = [])
    {
        if( $this->pdoException ) 
            return false;

        if( isset($this->pdoStatment) ) {
            $placeholdersCount = preg_match_all("/\?/", $this->getExecuteSql()); 
            if($placeholdersCount != count($parameters)) {
                throw new EatWhatException("paratemers count can not matched. ");
            }
            
            try {
                foreach($parameters as $index => $parameter) {
                    $type = gettype($parameter);
                    $this->pdoStatment->bindValue($index + 1, $parameter, $this->bindTypes[$type]);
                }
                $this->pdoStatment->execute();
                return $this->pdoStatment;
            } catch (\PDOException $exception) {
                if( !DEVELOPMODE ) {
                    $this->pdoException = true;
                    EatWhatLog::logging((string)$exception, [
                        "request_id" => EatWhatRequest::$staticRequestId,
                    ], "file", "pdo.log");
                    return false;
                } else {
                    throw new EatWhatException((string)$exception);
                }
            }
        }
    }
}