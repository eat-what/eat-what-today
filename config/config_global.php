<?php

return [
    // "aes_key" => hash_hmac("sha256", "EatWhatAesKey", "ewak");
    "pub_key_pem_file" => "file://".SOURCE_PATH."Public".DS."secret".DS."api_pub_key.pem",
    "pri_key_pem_file" => "file://".SOURCE_PATH."Public".DS."secret".DS."api_private_key.pem",
];