<?php
// +----------------------------------------------------------------------
// | Author:Stark
// +----------------------------------------------------------------------
// | Date:	2023/2/14
// +----------------------------------------------------------------------

/* 定义这个常量是为了在application.ini中引用*/
define('APPLICATION_PATH', dirname(__FILE__));
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

# 自定义加载类
require_once APPLICATION_PATH . '/app/Loader.php';

$application->bootstrap()->run();

