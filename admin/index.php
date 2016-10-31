<?php
// 定义ThinkPHP框架路径
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
define('THINK_PATH','../ThinkPHP/');

//定义项目名称和路径
define('APP_NAME', 'application');
define('APP_PATH', './application/');
define('AUTH_KEY','9e13yK8RN2M0lKP8CLRLhGs468d1WMaSlbDeCcI_1tsdk@you@sdk@2015');

//设置目录安全文件 
define('BUILD_DIR_SECURE',true);
define('DIR_SECURE_FILENAME', 'default.html');
define('DIR_SECURE_CONTENT', 'deney Access!');
define('APP_DEBUG',TRUE); 
// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");
//实例化一个网站应用实伿
//App::run();
?>