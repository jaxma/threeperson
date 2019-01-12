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

<div id="main">
	<div id="slideBox" class="slideBox">
        <div class="bd">
            <ul>
                <?php if(is_array($banner_list)): foreach($banner_list as $key=>$item): ?><li><a href="<?php echo ($item["link"]); ?>" target='_blank' style="background:url(__ROOT__/<?php echo ($item["original_img"]); ?>) top center no-repeat; height:524px;"></a></li><?php endforeach; endif; ?>
            </ul>
        </div>
        <a class="prev" href="javascript:void(0)"></a>
        <a class="next" href="javascript:void(0)"></a>
    </div>
    <script type="text/javascript">
    $(".slideBox").slide({mainCell:".bd ul",autoPlay:true});
    </script>
    <div class="main">
    	<ul class="m_wrap">
        <?php if(is_array($fbo_projects)): foreach($fbo_projects as $key=>$item): ?><li <?php if(($key) == "2"): ?>style="margin-right:0;"<?php endif; ?>>
        	<div class="box">
            	<div class="img">
                    <a href="<?php echo ($item["link"]); ?>">
                    <img src="__ROOT__/<?php echo ($item["original_img"]); ?>" width='113' height='103' alt='<?php echo ($item["description"]); ?>'>
                    </a>
                </div>
                <div class="con">
                	<p title='<?php echo ($item["description"]); ?>'><?php echo ($item["description"]); ?></p>
                    <a href="<?php echo ($item["link"]); ?>">MORE</a>
                </div>
                <div class="clear"></div>
            </div>
        </li><?php endforeach; endif; ?>
        </ul>
        <div class="blank2"></div>
        <div class="blank2"></div>
        <div class="l w285">
        	<dl class="m_wrap1">
            <dt><span class="cn">News Center</span><span class="en">NEWS</span></dt>
            <dd>
            	<div class="list">
                    <?php if(is_array($rec_articles)): foreach($rec_articles as $key=>$item): ?><a href="<?php echo U('Info/newsDetail',array('id'=>$item['article_id']));?>" title='<?php echo ($item["title"]); ?>'><?php echo (mysubstr($item["title"],16)); ?></a><?php endforeach; endif; ?>
                </div>
            </dd>
            </dl>
        </div>
        <div class="l w303">
        	<dl class="m_wrap1">
            <dt><span class="cn">Product Service</span><span class="en">SERVICES</span></dt>
            <dd>
            	<div id="slideBox" class="slideBox1">
                    <div class="hd">
                        <ul><?php if(is_array($rec_services)): foreach($rec_services as $key=>$item): ?><li>0</li><?php endforeach; endif; ?></ul>
                    </div>
                    <div class="bd">
                        <ul>
                            <?php if(is_array($rec_services)): foreach($rec_services as $key=>$item): ?><li><a href="<?php echo U('Info/pService',array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>'><img src="__ROOT__/<?php echo ($item["cat_img"]); ?>" width='276' height='112' alt='<?php echo ($item["cat_name"]); ?>'/></a></li><?php endforeach; endif; ?>
                        </ul>
                    </div>
                    <a class="prev" href="javascript:void(0)"></a>
                    <a class="next" href="javascript:void(0)"></a>
                </div>
                <script type="text/javascript">
                $(".slideBox1").slide({mainCell:".bd ul",autoPlay:true,effect:"leftLoop"});
                </script>
            </dd>
            </dl>
        </div>
        <div class="r w350">
        	<dl class="m_wrap1">
            <dt><span class="cn" style="text-indent:0;">Customer Service</span><span class="en">NEWS</span></dt>
            <dd>
            	<ul class="box">
                <li class="hover"><a href="<?php echo U('User/login');?>"><span class="ico1"></span><p>UserCenter</p></a></li>
                <li><a href="<?php echo U('Info/cService',array('cat_id'=>19));?>"><span class="ico2"></span><p>Online Survey</p></a></li>
                <li style="padding-right:0;"><a href="<?php echo U('Info/cService',array('cat_id'=>20));?>"><span class="ico3"></span><p>Complaint</p></a></li>
                </ul>
            </dd>
            </dl>
        </div>
        <div class="clear"></div>
        <div class="blank2"></div>
        <div class="blank2"></div>
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