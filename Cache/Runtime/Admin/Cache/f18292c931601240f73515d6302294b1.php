<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>管理员管理=>权限管理</title>
    <link href="__PUBLIC__/Admin/Css/Style.css" rel="stylesheet" />
    <link href="__PUBLIC__/Admin/lhgdialog/skins/default.css" rel="stylesheet" />
    <script src="__PUBLIC__/Admin/Js/jquery-1.7.2.min.js"></script>
    <script src="__PUBLIC__/Admin/Js/jquery.treeview.js"></script>
    <script src="__PUBLIC__/Admin/lhgdialog/lhgdialog.min.js"></script>
    <script src="__PUBLIC__/Admin/Js/jQueryPlugin.js"></script>
    <script src="__PUBLIC__/Admin/Js/JavaScript.js"></script>
	<script src="__PUBLIC__/Admin/kindeditor/kindeditor.js"></script>
    <!--[if lte IE 6]>    <script src="__PUBLIC__/Admin/Js/DD_belatedPNG_0.0.8a.js" type="text/javascript"></script><script type="text/javascript">DD_belatedPNG.fix('*');</script><![endif]-->
</head>
<body>
<script type="text/javascript">
function manageColumn(id){
	$.ajax({
		url:"<?php echo U('Articlecat/add');?>",
		type:'get',
		data:'cat_id='+id,
		async:false,
		timeout:15000,
		beforeSend:function(XMLHttpRequest){
			$("#loading").html("<img src='__PUBLIC__/Admin/Img/loading.gif' />");
		}, 
		complete:function(XMLHttpRequest,textStatus){
			$("#loading").empty();
		}, 
		success:function(msg){
			$('.ContainerMain_Box').html(msg);
		}
   });
}	
function articlecatInfo(id){
	$.ajax({
		url:"<?php echo U('Article/index');?>",
		type:'get',
		data:'cat_id='+id,
		async:false,
		timeout:15000,
		beforeSend:function(XMLHttpRequest){
			$("#loading").html("<img src='__PUBLIC__/Admin/Img/loading.gif' />");
		}, 
		complete:function(XMLHttpRequest,textStatus){
			$("#loading").empty();
		}, 
		success:function(msg){
			$('.ContainerMain_Box').html(msg);
		}
   });
}
</script>
	<div id="loading"></div>
    <div class="filetree treeview mainAutoHeight border_radius">
        <ul id="browser" class="autoHeight_scroll browser">
            <li><span class="folder">当前站点</span>
                <ul>
					<?php echo ($html); ?>
                </ul>
            </li>
        </ul>
    </div>
    <div class="ContainerMain_Box mainAutoHeight border_radius">
    	<iframe src="" frameborder="0"  width="100%" height="100%" frameborder="0" scrolling="yes" name="container"></iframe>
    </div>
</body>
</html>