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
        "integer" => \PDO::PARAM_INT,
        "string" => \PDO::PARAM_STR,
    ];

    /**
     * mysql obj
     * 
     */
    public $pdo;

    /**
     * request obj
     * 
     */
    public $request;

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
     * has transaction
     * 
     */
    public $hasTransaction;

    /**
     * last exec result
     * 
     */
    public $execResult;

    /**
     * constructor!
     * 
     */
    public function __construct(EatWhatRequest $request)
    {
        $this->request = $request;
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
     * set execute sql
     * 
     */
    public function setExecuteSql(string $sql = "") : self
    {
        $this->executeSql = $sql;
        return $this;
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
     * ensure insert section
     * 
     */
    public function insert(array $insert) : self
    {
        $this->executeSql .= "INSERT INTO " . $this->table . "(" . implode(",", $insert) . ")" . " VALUES(" . substr(str_repeat("?,", count($insert)), 0, -1) . ")";

        return $this;
    }

    /**
     * ensure update section
     * 
     */
    public function update(array $update) : self
    {
        $this->executeSql .= "UPDATE " . $this->table . " SET";
        foreach($update as $field) {
            $this->executeSql .= " $field = ? ,";
        }
        $this->executeSql = substr($this->executeSql, 0, -1);

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
                EatWhatLog::logging("DB can not prepare sql: " . (string)$exception . ". ", [
                    "request_id" => $this->request->getRequestId(),
                    "sql" => $this->getExecuteSql(),
                ], "file", "pdo.log");

                $this->pdoException = true;
                $this->request->outputResult($this->request->generateStatusResult("serverError", -404));
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
        if( isset($this->pdoStatment) ) {
            $placeholdersCount = preg_match_all("/\?/", $this->getExecuteSql()); 
            if($placeholdersCount != count($parameters)) {
                throw new EatWhatException("paratemers count can not matched. ");
            }
            
            try {
                foreach(array_values($parameters) as $index => $parameter) {
                    $parameterType = gettype($parameter);
                    $this->pdoStatment->bindValue($index + 1, $parameter, $this->bindTypes[$parameterType]);
                }
                $execResult = $this->pdoStatment->execute();
                $this->execResult = $execResult;
                $this->setExecuteSql();
                return $this->pdoStatment;
            } catch (\PDOException $exception) {
                if($this->hasTransaction) {
                    $this->pdo->rollBack();
                }

                if( !DEVELOPMODE ) {
                    EatWhatLog::logging((string)$exception, [
                        "request_id" => $this->request->getRequestId(),
                        "sql" => $this->getExecuteSql(),
                    ], "file", "pdo.log");

                    $this->pdoException = true;
                    $this->request->outputResult($this->request->generateStatusResult("serverError", -404));
                } else {
                    throw new EatWhatException((string)$exception);
                }
            }
        }
    }

    /**
     * get last insert id
     * 
     */
    public function getLastInsertId() : string
    {
        return $this->pdo->lastInsertId();
    }
}