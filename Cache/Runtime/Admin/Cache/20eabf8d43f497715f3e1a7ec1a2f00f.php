<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>cyx科技网站后台管理系統</title>
    <link href="__PUBLIC__/Admin/Css/Style.css" rel="stylesheet" />
    <script src="__PUBLIC__/Admin/Js/jquery-1.7.2.min.js"></script>
    <script src="__PUBLIC__/Admin/Js/jQueryPlugin.js"></script>
    <script src="__PUBLIC__/Admin/Js/JavaScript.js"></script>
    <!--[if lte IE 6]>    <script src="__PUBLIC__/Admin/Js/DD_belatedPNG_0.0.8a.js" type="text/javascript"></script><script type="text/javascript">DD_belatedPNG.fix('*');</script><![endif]-->
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery(".current_click").each(function () {
                var list = jQuery(this).find("ul.c li");
                var liList = jQuery(".Container_Left ul");
                list.each(function () {
                    var o = jQuery(this);
                    o.click(function () {
                        list.removeClass('current');
                        o.addClass('current');
                    });
                    //显示相对应的左侧栏目
                    o.children("a").click(function () {
                        var a = jQuery(this);
                        var className = a.attr("class");
                        if (className == undefined) return;
                        var newlist = liList.find("." + className);
                        liList.children("li").hide();
                        newlist.show().eq(0).click();
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div class="Header">
	<a class="logo" href="javascript:" onclick="document.location.reload();"></a>
	<div class="MenuDiv current_click">
		<ul class="c">
			<?php if(admin_priv2('Columns')): ?><li><a href="<?php echo U('Articlecat/index');?>" target="main" class="show_a">栏目分类</a></li><?php endif; ?>

			<?php if(admin_priv2('Message')): ?><li><a href="<?php echo U('Articlecat/index2');?>" target="main" class="show_d">文章管理</a></li><?php endif; ?>

			<li><a href="<?php echo U('Goods/index',array('cat_id'=>1));?>" target="main" class="show_c">案例管理</a></li>

			<?php if(admin_priv2('Images')): ?><li><a href="<?php echo U('Ads/index',array('cat_id'=>2));?>" target="main" class="show_e">图片管理</a></li><?php endif; ?>


			<li><a href="<?php echo U('Feedback/index');?>" target="main" class="show_h">留言管理</a></li>

			<li><a href="<?php echo U('Survey/index');?>" target="main" class="show_i">在线调查</a></li>

			<li><a href="<?php echo U('User/index');?>" target="main" class="show_j">会员管理</a></li>

			<?php if(admin_priv2('Systems')): ?><li><a href="<?php echo U('Webinfo/index');?>" target="main" class="show_y">系统设置</a></li><?php endif; ?>

			<li><a href="<?php echo U('Privilege/index');?>" target="main" class="show_z">管理员设置</a></li>
		</ul>
	</div>
	<div class="itemBar">
		<ul>
			<li><a href="<?php echo U('Privilege/edit',array('id'=>$_SESSION['admin_id']));?>" class="name" target="main"><?php echo (session('admin_name')); ?> </a>，您好！</li>
			<li><a href="/" target="_blank" class="about">网站首页</a></li>
			<li><a href="http://www.cyx.com/" target="_blank" class="about">关于cyx科技</a></li>
			<li><a href="<?php echo U('Index/clearCache');?>">清除缓存</a></li>
			<li><a href="<?php echo U('Public/loginOut');?>" class="out">退出</a></li>
		</ul>
	</div>
</div>
    <div class="minlineheight"></div>
    <div class="Container">
        <div class="Container_Left autoHeight current_click">
	<ul class="c">
		<li class="show_a"><a href="<?php echo U('Articlecat/index');?>" target="main">栏目分类</a></li>

		<li class="show_d" style="display:none;"><a href="<?php echo U('Articlecat/index2');?>" target="main">分类文章列表</a></li>
		<li class="show_d" style="display:none;"><a href="<?php echo U('Article/index');?>" target="main">全部文章</a></li>

		<li class="show_c" style="display:none;"><a href="<?php echo U('Goods/index',array('cat_id'=>1));?>" target="main">案例列表</a></li>
		<li class="show_c" style="display:none;"><a href="<?php echo U('Goodscat/index');?>" target="main">案例分类</a></li>

		<li class="show_e" style="display:none;"><a href="<?php echo U('Ads/index',array('cat_id'=>2));?>" target="main">首页Banner图</a></li>
		<li class="show_e" style="display:none;"><a href="<?php echo U('Ads/index',array('cat_id'=>6));?>" target="main">首页广告图</a></li>
		<li class="show_e" style="display:none;"><a href="<?php echo U('Ads/index',array('cat_id'=>3));?>" target="main">内页Banner图</a></li>
		<li class="show_e" style="display:none;"><a href="<?php echo U('Ads/index',array('cat_id'=>5));?>" target="main">右边轮播图</a></li>
		<li class="show_e" style="display:none;"><a href="<?php echo U('Ads/index',array('cat_id'=>4));?>" target="main">其他图片</a></li>

		<li class="show_h" style="display:none;"><a href="<?php echo U('Feedback/index');?>" target="main">留言列表</a></li>

		<li class="show_i" style="display:none;"><a href="<?php echo U('Survey/index');?>" target="main">文件列表</a></li>

		<li class="show_j" style="display:none;"><a href="<?php echo U('User/index');?>" target="main">会员列表</a></li>

		<li class="show_y" style="display:none;"><a href="<?php echo U('Webinfo/index');?>" target="main">站点配置</a></li>
		<li class="show_y" style="display:none;"><a href="<?php echo U('Webinfo/setEmailConfig');?>" target="main">邮件服务器</a></li>

		<li class="show_z"><a href="<?php echo U('Privilege/index');?>" target="main">管理员列表</a></li>
		<!-- <li class="show_z"><a href="<?php echo U('Role/index');?>" target="main">角色管理</a></li> -->
	</ul>
</div>
        <div class="ContainerMain autoHeight">
            <iframe src="<?php echo U('Index/main');?>" width="100%" height="100%" id="main" frameborder="0" scrolling="yes" name="main"></iframe>
        </div>
    </div>
    <div class="Footer">
	Copyright  &copy; <a href="http://www.cyx.com" target="_blank">cyx.com</a>
</div>
</body>
</html>