<?php
/*
 * 通用配置文件
 * Date:2019-01-09
 */
$config1 = array(
    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'SHOW_PAGE_TRACE' => FALSE,
    'TOKEN_ON' => false, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => FALSE, //令牌验证出错后是否重置令牌 默认为true
    'LANG_AUTO_DETECT' => FALSE, //关闭语言的自动检测，如果你是多语言可以开启
    'LANG_SWITCH_ON' => TRUE, //开启语言包功能，这个必须开启
    'DEFAULT_LANG' => 'zh-cn', //zh-cn文件夹名字 /lang/zh-cn/common.php
    'LOG_RECORD'=>false,
    'DB_SQL_LOG'=>false,
    'LOG_EXCEPTION_RECORD'=>false,
    'TMPL_STRIP_SPACE' => false,
    'DB_HOST' => 'localhost', 
    'DB_NAME' => (WEB_URL=='yangsi.tk' || WEB_URL=='tp.yangsi.tk')?'threeperson': ((WEB_URL=='tp.com' || WEB_URL=='www.tp.com')?'mytp':''),
    'DB_USER' => (WEB_URL=='yangsi.tk' || WEB_URL=='tp.yangsi.tk')?'threeperson': ((WEB_URL=='tp.com' || WEB_URL=='www.tp.com')?'root':''),
    'DB_PWD' => (WEB_URL=='yangsi.tk' || WEB_URL=='tp.yangsi.tk')?'sSbw5mkRXbzn4wr5': ((WEB_URL=='tp.com' || WEB_URL=='www.tp.com')?'root':''),
    'DB_PORT' => '3306', 
    'DB_PREFIX' => 'tp_',
);
$config2 = WEB_ROOT . "Common/systemConfig.php";
$config2 = file_exists($config2) ? include "$config2" : array();
return array_merge($config1, $config2);
?>