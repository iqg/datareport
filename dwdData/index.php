<?php

define('APPLICATION_PATH', dirname(__FILE__));

Yaf_Loader::import(APPLICATION_PATH.'/application/init.php');
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
