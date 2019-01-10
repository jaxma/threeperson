<?php
//header("Content-Type: text/html; charset=utf-8");
//return  print_r('系统维护中...');
	define('APP_NAME','App');
	define('APP_PATH','./App/');
	define('APP_DEBUG',true);
        define('BUILD_DIR_SECURE',true);
        //define('DIR_SECURE_FILENAME', 'default.html');
        define('DIR_SECURE_CONTENT', 'deney Access!');
	include './ThinkPHP/ThinkPHP.php';
?>