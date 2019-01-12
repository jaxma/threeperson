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
    	<!--左边区域-->
        <div class="l w203">
	<div class="m_wrap3">
    	<div class="tit"><p class="p1"><?php echo ($parent_cat["cat_name"]); ?></p><p class="p2"><?php echo ($parent_cat["cat_en_name"]); ?></p></div>
    	<ul class="list">
        <?php if(is_array($article_cat)): foreach($article_cat as $key=>$item): ?><li><a href="<?php echo U('Info/'.$action,array('cat_id'=>$item['cat_id']));?>" title='<?php echo ($item["cat_name"]); ?>' <?php if(($cat_id == $item['cat_id']) or ($cat_info['parent_id'] == $item['cat_id'])): ?>class='hover'<?php endif; ?>><?php echo ($item["cat_name"]); ?></a></li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>
        <div class="l w570">
        	<div class="blank1"></div>
        	<div class="wz"><?php echo ($ur_here); ?> <a href="javascript:history.back();" style='float:right;'>Back</a></div>
            <div class="m_con1">
                <?php echo ($article["content"]); ?>
                
                <?php if(($id) == "48"): ?><br><br>
                    <!--投诉和建议-->
                    <style type="text/css">#cmForm input,#cmForm textarea{border: solid 1px #E7E7E7; line-height: 24px; text-indent: 4px;}</style>
<form id='cmForm'>
    <table cellpadding="4" cellspacing="10">
        <tr>
            <td>Names<font color='red'>*</font></td>
            <td><input type="text" name='name' value="" size="30" /></td>
        </tr>
        <tr>
            <td>Email<font color='red'>*</font></td>
            <td><input type="text" name='email' value="" size="30" /></td>
        </tr>
        <tr>
            <td>Mobile<font color='red'>*</font></td>
            <td><input type="text" name='phone' value="" size="30" /></td>
        </tr>
        <tr>
            <td>Message<font color='red'>*</font></td>
            <td><textarea name='content' style="width: 450px; height: 187px;"></textarea></a>
        </tr>
        <tr>
        <tr>
            <td>&nbsp;</td>
            <td align='left'>  
                <input type="button" value="Send" onclick="mySubmit('cmForm','<?php echo U('Info/addMessage');?>','cmresult')" style='padding:2px 10px; margin-right:5px;'/>
                <input type='reset' value='Reset' style='padding:2px 10px;'/>
                &nbsp;&nbsp;&nbsp;&nbsp;<font color='red' size='-1' id='cmresult'></font>
            </td>
        </tr>
    </table>
</form><?php endif; ?>
            </div>

            <?php if(($action) == "news"): ?><div style='position:absolute; bottom:30px;'>
                
                PREV：<?php if(!empty($prev_article)): ?><a href="<?php echo U('Info/newsDetail', array('id'=>$prev_article['article_id']));?>"><?php echo ($prev_article["title"]); ?></a><?php else: ?>NO DATA....<?php endif; ?>
                <div class='blank'></div>
                NEXT：<?php if(!empty($next_article)): ?><a href="<?php echo U('Info/newsDetail', array('id'=>$next_article['article_id']));?>"><?php echo ($next_article["title"]); ?></a><?php else: ?>NO DATA....<?php endif; ?>
            </div><?php endif; ?>
        </div>

        <!--右边区域-->
        <div class="r w193">
	<div class="m_con"><a href="<?php echo U('Info/news',array('cat_id'=>11));?>"><span>+</span>MORE</a>
        <p class="p1"><?php echo ($right_notice["title"]); ?></p>
        <p class="p2"><?php echo (mysubstr(strip_tags($right_notice["short"]),32)); ?>
        <span onclick="location.href = '<?php echo U('Info/detail',array('id'=>$right_notice['article_id']));?>';" style='cursor:pointer;'>[Detail]</span>
        </p>
        <div class="arrows"></div>
    </div>
    <div id="slideBox" class="slideBox2" style='height:169px;'>
        <div class="hd">
            <ul><?php if(is_array($right_lunbao)): foreach($right_lunbao as $key=>$item): ?><li>0</li><?php endforeach; endif; ?></ul>
        </div>
        <div class="bd">
            <ul>
                <?php if(is_array($right_lunbao)): foreach($right_lunbao as $key=>$item): ?><li><a href="<?php echo ($item["link"]); ?>" target='_blank'><img src="__ROOT__/<?php echo ($item["original_img"]); ?>" width='193' height='169'/></a></li><?php endforeach; endif; ?>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
    $(".slideBox2").slide({mainCell:".bd ul",autoPlay:true,effect:"fold"});
    </script>
    <div class="blank4"></div>
	<div><a href="<?php echo U('Info/about',array('cat_id'=>18));?>"><img src="__IMG__/gdyt_pic46.jpg"></a></div>
    <div><a href="<?php echo U('Info/cService',array('cat_id'=>19));?>"><img src="__IMG__/gdyt_pic47.jpg"></a></div>
</div>
        <div class="clear"></div>
        <div style="height:95px;"></div>	   	
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