<?php

define("DS", DIRECTORY_SEPARATOR);
define("SOURCE_PATH", __DIR__.DS."source".DS);
define("CONFIG_PATH", __DIR__.DS."config".DS);
define("VENDOR_PATH", __DIR__.DS."vendor".DS);
define("LOG_PATH", __DIR__.DS."log".DS);

$initConfig = require_once CONFIG_PATH."config_init.php";
define("DEVELOPMODE", $initConfig["developement"]);

require_once VENDOR_PATH."autoload.php";
require_once SOURCE_PATH."AppInit.class.php";
require_once SOURCE_PATH."EatWhatStatic.class.php";

$appInit = new EatWhat\AppInit();
$appInit->_Init($initConfig);

