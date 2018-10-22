<?php

namespace EatWhat;

use EatWhat\AppConfig;

/**
 * simple jwt generate/verify
 * 
 */
class EatWhatJwt
{
    /**
     * hash algo
     * 
     */
    private $algo;

    /**
     * cipher key
     * 
     */
    private $cipherKey;

    /**
     * Constructor!
     * 
     */
    public function __construct($algo = null, $cipherKey = null)
    {
        $this->setAlgo($algo);
        $this->setCipherKey($cipherKey);
    }

    /**
     * set algo
     * 
     */
    private function setAlgo($algo)
    {
        $this->algo = $algo ?? "sha256";
    }

    /**
     * set cipher key
     * 
     */
    private function setCipherKey($cipherKey)
    {
        $this->cipherKey = $cipherKey ?? AppConfig::get("cipher_key", "global");
    }

    /**
     * generate token
     * @param $data  array  ["foo" => bar]
     * 
     */
    public function generate($data)
    {
        $header = base64_encode(json_encode([
            "typ" => "jwt",
            "alg" => $this->algo,
        ]));

        $payload = base64_encode(json_encode([
            "aud" => "eat-what.cn",
            "iat" => $_SERVER["REQUEST_TIME"],
            "exp" => $_SERVER["REQUEST_TIME"] + AppConfig::get("access_token_expire"),
            "data" => $data,
        ]));

        $data = $header . '.' . $payload;
        $signature = hash_hmac($this->algo, $data, $this->cipherKey);

        $jwt = $header . '.' . $payload . "." . $signature;

        $pri_key_pem_file = AppConfig::get("pri_key_pem_file", "global");
        $pri_key = openssl_pkey_get_private($pri_key_pem_file);
        openssl_private_encrypt($jwt, $token, $pri_key);

        return base64_encode($token);
    }

    /**
     * verify token
     * 
     */
    public function verify()
    {
        $headers = getallheaders();
        if(!isset($headers["Authorization"]))
            return true;

        list($token) = sscanf($headers["Authorization"], "Bearer %s");
        if(!$token) {
            return false;
        }

        $pub_key_pem_file = AppConfig::get("pub_key_pem_file", "global");
        $pub_key = openssl_pkey_get_public($pub_key_pem_file);
        openssl_public_decrypt(base64_decode($token), $jwt, $pub_key);

        list($jwtHeader64, $jwtPayload64, $jwtSignature) = explode(".", $jwt);
        $jwtHeader = json_decode(base64_decode($jwtHeader64), 1);
        $jwtPayload = json_decode(base64_decode($jwtPayload64), 1);

        if($jwtHeader["typ"] != "jwt" || $jwtHeader["alg"] != $this->algo || $jwtPayload["aud"] != "eat-what.cn") {
            return false;
        }

        if(!hash_equals(hash_hmac($this->algo, $jwtHeader64.'.'.$jwtPayload64, $this->cipherKey), $jwtSignature)) {
            return false;
        }

        // expire
        if(jwtPayload["exp"] > time()) {
            return false;
        }

        return jwtPayload["data"];
    }
}