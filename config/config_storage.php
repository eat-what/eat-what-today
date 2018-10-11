<?php

return [

    // mysql config
    "MysqlStorageClient" => [
        "host" => "localhost",
        "dbuser" => "eatwhat",
        "passwd" => "",
        "dbname" => "eatwhat",
        "port" => 3306,
        "timeout" => 15,
    ],

    // redis config
    "RedisStorageClient" => [
        "host" => "localhost",
        "port" => 6379,
        "serialize" => 0,
        "auth" => "",
        "prefix" => "",
        "timeout" => 15,
    ],

    // mongoDB config
    "MongodbStorageClient" => [
        "uri" => "mongodb://eatwhat:eatwhat@localhost:27017",
        "dbname" => "eatwhat",
    ],
];