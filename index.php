<?php
ini_set('date.timezone', 'Asia/Shanghai');
define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', 'Home');
define('APP_PATH', './Home/');
define("WEB_ROOT", str_replace("\\",'/',dirname(__FILE__)) . "/");
define('WEB_CACHE_PATH', WEB_ROOT."Cache/");//网站当前路径
define("RUNTIME_PATH", WEB_ROOT . "Cache/Runtime/Home/");
define('APP_DEBUG', true);
if (!file_exists(WEB_ROOT.'Common/systemConfig.php')) {
    exit;
}

//默认排序方式
define('SO','is_recommend desc,sort_order asc,add_time desc');
define('CSO','is_recommend desc,sort_order asc,cat_id desc');
define('FSO','is_best desc,is_top desc,is_recommend desc,is_hot desc,id desc');
define('COLS','article_id,title,original_img,short,cat_id');

require(THINK_PATH . "ThinkPHP.php");
?>