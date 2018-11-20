<?php

namespace EatWhat\Traits;

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

        if($dao === false || $this->mysqlDao->pdoException) {
            $this->request->outputResult($this->generateErrorResult("serverError", -404));
        }

        $result = $dao->fetch(\PDO::FETCH_ASSOC);
        return boolval($result);
    }

    /**
     * check mobile format
     * 
     */
    public function checkMobileFormat(string $mobile) : bool
    {
        if( !preg_match("/^(13|14|15|16|18|17|19)[0-9]{9}$/", $mobile) ) {
            return false;
        }
        return true;
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
}