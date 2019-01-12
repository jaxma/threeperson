<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (strip_tags($site_title)); ?></title>
<meta name='keywords' content="<?php echo (strip_tags($site_keywords)); ?>"/>
<meta name='description' content="<?php echo (strip_tags($site_description)); ?>"/>
<meta name='author' content="cyx科技:http://www.cyx.com"/>
<meta name="viewport" content="width=1920,initial-scale=1">

<link type="text/css" rel="stylesheet" href="__CSS__/style.css"/>

<script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__JS__/JQ_common.js"></script>
<script type="text/javascript" src="__JS__/jquery.SuperSlide.2.1.js"></script>
<script type="text/javascript" src="__JS__/myScript.js"></script>
</head>

<body>
<div id="header">
    <div class="header">
        <dl class="h_top">
        <dt><a href="/"><img class="logo" src="__ROOT__/<?php echo ($logo["original_img"]); ?>" alt='logo'></a></dt>
        <dd>
            <div class="h_box">
                <?php if(empty($userInfo["user_id"])): ?><a href="<?php echo U('User/login');?>">Login</a>
                <?php else: ?>
                <?php echo ($userInfo["user_name"]); ?>,<a href="<?php echo U('User/logout');?>" style='color:red;'>Login Out</a><?php endif; ?>
                |<a href="/">CHINESE</a>|<a href="/E">ENGLISH</a>
            </div>
        </dd>
        </dl>
        <ul class="nav">
        <li <?php if(($nid) == "1"): ?>class='hover'<?php endif; ?>><a href="/">Home</a></li>
        <li <?php if(($nid) == "2"): ?>class='hover'<?php endif; ?>><a href="<?php echo U('Info/pService',array('cat_id'=>6));?>">Product Serv</a>
            <div class="sub">
            <?php if(is_array($all_cats["1"]["sub_cat"])): foreach($all_cats["1"]["sub_cat"] as $key=>$item): ?><a href="<?php echo U('Info/pService',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><?php echo (mysubstr($item["cat_name"],7)); ?></a><?php endforeach; endif; ?>
            </div>
        </li>
        <li <?php if(($nid) == "3"): ?>class='hover'<?php endif; ?>><a href="<?php echo U('Info/news',array('cat_id'=>11));?>">News</a>
            <div class="sub">
            <?php if(is_array($all_cats["2"]["sub_cat"])): foreach($all_cats["2"]["sub_cat"] as $key=>$item): ?><a href="<?php echo U('Info/news',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><?php echo (mysubstr($item["cat_name"],7)); ?></a><?php endforeach; endif; ?>
            </div>
        </li>
        <li <?php if(($nid) == "4"): ?>class='hover'<?php endif; ?>><a href="<?php echo U('Info/cService',array('cat_id'=>19));?>">Customer Serv</a>
            <div class="sub">
            <?php if(is_array($all_cats["3"]["sub_cat"])): foreach($all_cats["3"]["sub_cat"] as $key=>$item): ?><a href="<?php echo U('Info/cService',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><?php echo (mysubstr($item["cat_name"],7)); ?></a><?php endforeach; endif; ?>
            </div>
        </li>
        <li <?php if(($nid) == "5"): ?>class='hover'<?php endif; ?>><a href="<?php echo U('Info/fboIndex');?>">FBO</a>
            <div class="sub">
            <?php if(is_array($all_cats["4"]["sub_cat"])): foreach($all_cats["4"]["sub_cat"] as $key=>$item): ?><a href="<?php echo U('Info/fbo',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><?php echo (mysubstr($item["cat_name"],7)); ?></a><?php endforeach; endif; ?>
            </div>
        </li>
        <li <?php if(($nid) == "6"): ?>class='hover'<?php endif; ?>><a href="<?php echo U('Info/about',array('cat_id'=>14));?>">About</a>
            <div class="sub">
            <?php if(is_array($all_cats["5"]["sub_cat"])): foreach($all_cats["5"]["sub_cat"] as $key=>$item): ?><a href="<?php echo U('Info/about',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><?php echo (mysubstr($item["cat_name"],7)); ?></a><?php endforeach; endif; ?>
            </div>
        </li>
        <li>
            <form action="<?php echo U('Common/search');?>" class="search">
                <input class="submit" type="submit" value="">
                <input class="text" name='keyword' type="text" placeholder="Keyword Search">              
            </form>
        </li>
        </ul>
    </div>
</div>

<div id="main" class="m_bg">
    <div style="background:url(__ROOT__/<?php echo ($banner["original_img"]); ?>) top center no-repeat; height:341px;"></div>
    <div class="main" style="margin:-30px auto 0; background:#FFF;">
    	<div class="w955">
        	<div class="blank1"></div>
        	<div class="wz"><?php echo ($ur_here); ?></div>
            <div class="m_wrap2">
            	<div class="con"><p class="p1"><?php echo ($article_cat["25"]["cat_name"]); ?></p>
                <p class="p2"><?php echo (mysubstr(strip_tags($article_cat["25"]["content"]),150)); ?></p>
                <a href="<?php echo U('Info/fbo',array('cat_id'=>25));?>">Detail</a></div>
                <div class="img">
                <a href="<?php echo U('Info/fbo',array('cat_id'=>25));?>">
                <img src="__ROOT__/<?php echo ($article_cat["25"]["cat_img"]); ?>" width='334' height='218'>
                </a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="blank1"></div>
            <div class="blank2"></div>
            <div class="m_list">
            	<div class="tit"><?php echo ($article_cat["26"]["cat_name"]); ?></div>
            	<div class="img">
                <a href="<?php echo U('Info/fbo',array('cat_id'=>26));?>">
                <img src="__ROOT__/<?php echo ($article_cat["26"]["cat_img"]); ?>" width="271" height='133'>
                </a>
                </div>
                <div class="con">
                	<?php echo (mysubstr(strip_tags($article_cat["26"]["content"]),62)); ?>
                </div>
                <a href="<?php echo U('Info/fbo',array('cat_id'=>26));?>">Detail</a>
            </div>
            <div class="m_list">
            	<div class="tit"><!-- <?php echo ($article_cat["27"]["cat_name"]); ?> --></div>
            	<div class="img">
                   <!--  <a href="<?php echo U('Info/fbo',array('cat_id'=>27));?>">
                   <img src="__ROOT__/<?php echo ($article_cat["27"]["cat_img"]); ?>" width="271" height='133'>
                   </a> -->
                </div>
                <div class="con">
                   <!--  <?php echo (mysubstr(strip_tags($article_cat["27"]["content"]),62)); ?> -->
                </div>
               <!--  <a href="<?php echo U('Info/fbo',array('cat_id'=>27));?>">Detail</a> -->
            </div>
            <div class="m_list" style="padding-right:0;">
            	<div class="tit"><?php echo ($article_cat["28"]["cat_name"]); ?></div>
            	<div class="img">
                    <a href="<?php echo U('Info/fbo',array('cat_id'=>28));?>">
                    <img src="__ROOT__/<?php echo ($article_cat["28"]["cat_img"]); ?>" width="271" height='133'>
                    </a>
                </div>
                <div class="con">
                    <?php echo (mysubstr(strip_tags($article_cat["28"]["content"]),62)); ?>
                </div>
                <a href="<?php echo U('Info/fbo',array('cat_id'=>28));?>">Detail</a>
            </div>
            <div class="clear"></div>
            <div style="height:155px;"></div>
        </div>   	
    </div>
</div>

<div id="footer">
	<div class="footer">
    	<dl class="f_foot">
        <dt><a href="<?php echo U('Info/detail',array('id'=>54));?>">Legal Disclaimer</a><em>|</em><a href="<?php echo U('Info/detail',array('id'=>55));?>">Links</a><em>|</em><a href="<?php echo U('Info/detail',array('id'=>58));?>">Site Navigation</a><em>|</em><a href="<?php echo U('Info/about',array('cat_id'=>18));?>">Contact Us</a><em>|</em><a href="<?php echo U('Info/cService',array('cat_id'=>20));?>">Complaint</a><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254481202'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1254481202%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
        </dt>
        <dd>CopyRight © <?php echo ($Year); ?> <?php echo ($site_info["name"]); ?></dd>
        </dl>
    </div>
</div>
</body>
</html>