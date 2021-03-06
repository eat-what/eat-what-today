<?php

return [
    "cipher_key" => hash_hmac("sha256", "EatWhatCipherKey", "ewck"),
    "pub_key_pem_file" => "file://".SOURCE_PATH."Public".DS."secret".DS."api_pub_key.pem",
    "pri_key_pem_file" => "file://".SOURCE_PATH."Public".DS."secret".DS."api_private_key.pem",
    
    "access_token_expire" => 30 * 24 * 3600,

    "protocol" => "http://",
    "server_name" => "www.eat-what.cn", 

    "global_status" => [
        "serverError" => -404,
        "notLogin" => -400,
        "reLogin" => -401,
    ],
];