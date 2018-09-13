<?php

define("DS", DIRECTORY_SEPARATOR);

define("SOURCE_PATH", __DIR__.DS."source".DS);

define("CONFIG_PATH", __DIR__.DS."config".DS);

$initConfig = require_once CONFIG_PATH."config_init.php";

require_once SOURCE_PATH."AppInit.class.php";

$appInit = new EatWhat\AppInit();
$appInit->_Init($initConfig);

