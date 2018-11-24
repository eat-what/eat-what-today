<?php

namespace EatWhat\Traits;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use EatWhat\EatWhatStatic;

use function GuzzleHttp\Promise\unwrap;

/**
 * User Traits For User Api
 * 
 */
trait UserTrait
{

    /**
     * check mobile format and is that exists
     * 
     */
    public function checkMobile(string $mobile) : bool
    {
        return $this->checkMobileFormat($mobile) && !$this->checkMobileExists($mobile);
    }

    /**
     * check username format and is that exists
     * 
     */
    public function checkUsername(string $username) : bool
    {
        return $this->checkUsernameFormat($username) && !$this->checkUsernameExists($username);
    }

    /**
     * check mobile format
     * 
     */
    public function checkMobileFormat(string $mobile) : bool
    {
        return EatWhatStatic::checkMobileFormat($mobile);
    }

    /**
     * check mobile is exists
     * return true when exists
     * 
     */
    public function checkMobileExists(string $mobile) : bool
    {
        $dao = $this->mysqlDao->table("member")
                    ->select(["id"])
                    ->where(["mobile"])
                    ->prepare()
                    ->execute([$mobile]);

        $result = $dao->fetch(\PDO::FETCH_ASSOC);
        return boolval($result);
    }

    /**
     * check mobile code
     * 
     */
    public function checkMobileCode(string $mobile, string $code, string $action = "login") : bool
    {
        $codeKey = $mobile . "_" . $action;
        $verifyCode = $this->redis->get($codeKey);

        if(!$verifyCode || $verifyCode != $code) {
            return false;
        }

        return true;
    }

    /**
     * check username format
     * 
     */
    public function checkUsernameFormat(string $username) : bool
    {
        return boolval(preg_match("/^[\d\w\p{Han}]{2,12}$/iu", $username));
    }

    /**
     * check username format
     * 
     */
    public function checkUsernameExists(string $username) : bool
    {
        $dao = $this->mysqlDao->table("member")
                    ->select(["id"])
                    ->where(["username"])
                    ->prepare()
                    ->execute([$username]);

        $result = $dao->fetch(\PDO::FETCH_ASSOC);
        return boolval($result);
    }

    /**
     * create a new user
     * 
     */
    public function createNewUser(array $newUser) : int
    {
        $this->mysqlDao->table("member")
             ->insert(array_keys($newUser))
             ->prepare()
             ->execute(array_values($newUser));

        return (int)$this->mysqlDao->getLastInsertId();
    }

    /**
     * get user status
     * 
     */
    public function getUserBaseInfo(string $mobile, ?string $field = null)
    {
        $dao = $this->mysqlDao->table("member")
                    ->select(["*"])
                    ->where(["mobile"])
                    ->prepare()
                    ->execute([$mobile]);

        $user = $dao->fetch(\PDO::FETCH_ASSOC);
        return is_null($field) ? $user : $user[$field];
    }

    /**
     * set a default gravatar for user
     * 
     */
    public function setDefaultAvatar(int $uid, ?string $hashString = null) : void
    {
        if( is_null($hashString) ) {
            $hashString = hash("sha256", $uid);
        }

        $avatarPath = ATTACH_PATH . "avatar" . DS . chunk_split(sprintf("%08s", $uid), 2, DS);
        if( !file_exists($avatarPath) ) {
            mkdir($avatarPath, 0777, true);
        }

        try {
            $retry = true;
            $Client = new GuzzleClient([
                "base_uri" => "http://www.gravatar.com/avatar/" . $hashString,
                "time_out" => 6.0,
            ]);

            GRAVATAR_RETRY:
            $promises = [
                "big" => $Client->getAsync("?s=200&d=identicon"),
            ];
            $results = unwrap($promises); 
            $response = $results["big"];

            if($response->getStatusCode() == 200 && $response->getHeader('Content-Length') > 1024) {
                $avatarContent = $response->getBody()->getContents();
                $avatarFile = $avatarPath . "avatar.png";
                file_put_contents($avatarFile, $avatarContent);
            } else if( $retry ) {
                $retry = false;
                goto GRAVATAR_RETRY;
            }   
        } catch( RequestException $exception ) {
            EatWhatLog::logging((string)$exception, [
                "request_id" => $this->request->getRequestId(),
            ], "file", "gravatar.log");
        }
    }

    /**
     * get user avatar
     * 
     */
    public function getUserAvatar(int $uid, bool $withHost = true) : string
    {
        $path = "attachment/avatar/" . chunk_split(sprintf("%08s", $uid), 2, "/") . "avatar.png";
        return ($withHost ? AppConfig::get("server_name", "global") . "/" : "") . $path;
    }

    /**
     * modify user mobile
     * 
     */
    public function updateUserMobile(string $newMobile) : bool
    {
        $values = [$newMobile, $this->userData["mobile"]];

        $this->mysqlDao->table("member")
             ->update(["mobile"])
             ->where(["mobile"])
             ->prepare()
             ->execute($values);

        return $this->mysqlDao->execResult;
    }

    /**
     * update user base info
     * 
     */
    public function updateUserBaseInfo(array $baseInfo) : bool 
    {
        $statment = $this->mysqlDao->table("member")
                         ->update(array_keys($baseInfo))
                         ->where(["id"])
                         ->prepare();

        array_push($baseInfo, $this->userData["uid"]);
        $statment->execute($baseInfo);

        return $this->mysqlDao->execResult;
    }
}