<?php
//header("Content-Type: text/html; charset=utf-8");
//return  print_r('系统维护中...');
	define('APP_NAME','App');
	define('APP_PATH','./App/');
	// define('APP_DEBUG',false);
        define('BUILD_DIR_SECURE',true);
        //define('DIR_SECURE_FILENAME', 'default.html');
        define('DIR_SECURE_CONTENT', 'deney Access!');
        define('WEB_URL',  $_SERVER['SERVER_NAME']);
	include './ThinkPHP/ThinkPHP.php';
?>