<?php

if (!defined('THINK_PATH'))
    exit();
// 系统别名定义文件
return array(
    'PHPMailer' => WEB_ROOT . 'Common/Extend/PHPMailer/phpmailer.class.php',
    'CheckCode' => WEB_ROOT . 'Common/Extend/CheckCode/Checkcode.class.php',
    'QRCode' => WEB_ROOT . '/Common/Extend/QRCode.class.php',
    'Category' => WEB_ROOT . '/Common/Extend/Category.class.php',
	'File' => WEB_ROOT . '/Common/Extend/File/File.class.php',
	'SplitWord' => WEB_ROOT . '/Common/Extend/Word/SplitWord.class.php',
	'Pingyin' => WEB_ROOT . '/Common/Extend/Pingyin/Pingyin.class.php',
	'String' => WEB_ROOT . '/Common/Extend/String/String.class.php',
);