<?php

ob_start();
ini_set('date.timezone', 'Asia/Shanghai');
define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', 'Admin');
define('APP_PATH', './Admin/');
define("WEB_ROOT", str_replace("\\",'/',dirname(__FILE__)) . "/");
define('WEB_CACHE_PATH', WEB_ROOT."Cache/");//网站当前路径
define("RUNTIME_PATH", WEB_ROOT . "Cache/Runtime/Admin/");
define("DatabaseBackDir", WEB_ROOT . "Databases/"); //系统备份数据库文件存放目录
define('ADMIN_CSS', './Admin/Tpl/Public/');  //样式与图片路径
define('APP_DEBUG', true);

require(THINK_PATH . "ThinkPHP.php");
?>