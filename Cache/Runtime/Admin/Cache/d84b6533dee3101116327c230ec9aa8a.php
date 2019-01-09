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
function delArticlecat(id) {
	$.dialog.confirm('你确定要删除这个栏目吗？', function(){
		window.location.href="<?php echo U('Articlecat/del');?>/cat_id/"+id;
	}, function(){
		//$.dialog.tips('执行取消操作');
	});
}
$(function(){
	<?php if($action == 'add'): ?>manageColumn(<?php echo ($parent_id); ?>);<?php endif; ?>
	<?php if($action == 'edit'): ?>articlecatInfo(<?php echo ($cat_id); ?>);<?php endif; ?>
})
</script>

<script type="text/javascript">
$(function(){
	autoHeight(jQuery('.autoHeight'));
	jQuery(".column_Box").each(function () {
		var t = jQuery(this);
		if (t.length < 1) return;
		Tab_click(t.find('.tab ul li'), t.find(".wrapBox .body"));
	});
});

</script>
<script type="text/javascript">
$(function() {
	var editor = KindEditor.create('textarea[name="content"]',{urlType : 'absolute'}); 
}); 
</script>
<div class="column_Box mainAutoHeight">
	<div class="tab">
		<ul>
			<li class="current"><a href="javascript:">栏目属性</a></li>
		</ul>
	</div>
	<div class="column_Box mainAutoHeight wrapBox">
        <div class="body">
			<div class="item">
				<a href="<?php echo U('Articlecat/add',array('cat_id'=>$cat_id));?>" class="dot_Item"><span class="Icon_item icon_xingjian2"></span><i>新建子栏目</i></a>
				<a href="javascript:void(0);" class="dot_Item" onclick="delArticlecat('<?php echo ($cat_id); ?>')"><span class="Icon_item icon_shanchu"></span><i>删除</i></a>
				<!-- <a href="<?php echo U('Article/index',array('cat_id'=>$cat['cat_id']));?>" class="dot_Item"><span class="Icon_item icon_yulan"></span><i>栏目文章列表</i></a>
				<a href="<?php echo U('Article/add',array('cat_id'=>$cat['cat_id']));?>" class="dot_Item"><span class="Icon_item icon_xingjian"></span><i>新建栏目文章</i></a> -->
				<!-- <a href="<?php echo U('Articlecat/batchimg',array('cat_id'=>$cat['cat_id']));?>" class="dot_Item"><span class="Icon_item icon_xingjian"></span><i>批量上传图片</i></a> -->
			</div>
			<form method="post" action="<?php echo U('Articlecat/update');?>" id="submitForm" name="submitForm" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align:right;">文章分类名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="cat_name" value="<?php echo ($cat["cat_name"]); ?>"  /><em>*</em></td>
						</tr>
						<tr>
							<td style="text-align:right;">分类英文名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="cat_en_name" value="<?php echo ($cat["cat_en_name"]); ?>"  /><em>*</em></td>
						</tr>
						<tr>
							<td style="text-align:right;">上级分类：</td>
							<td style="text-align:left;">
								<select name="parent_id">
									<option value="0">顶级分类</option>
									<?php echo ($cat_select); ?>
								</select>
							<em>*</em></td>
						</tr>
						<tr>
							<td style="text-align:right;">排序：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="<?php echo ($cat["sort_order"]); ?>" name="sort_order"  /><em>*</em> </td>
						</tr>
						<tr>
							<td style="text-align:right;">分类图片：</td>
							<td style="text-align:left;"><input type="file" name="cat_img"  />&nbsp;&nbsp;&nbsp;<?php if(!empty($cat["cat_img"])): ?><font color='green'>✔</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="__ROOT__/<?php echo ($cat["cat_img"]); ?>" width='100'/><?php endif; ?>
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">分类介绍：</td>
							<td style="text-align:left;"><textarea style="width:680px;height:200px;" name="content" id="content" ><?php echo ($cat["content"]); ?></textarea></td>
						</tr>
						<tr>
							<td style="text-align:right;">关键字：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="<?php echo ($cat["keywords"]); ?>" name="keywords"  /> 	关键字为选填项，其目的在于方便外部搜索引擎搜索</td>
						</tr>
						<tr>
							<td style="text-align:right;">描述：</td>
							<td style="text-align:left;"><textarea style="width:400px;height:100px;" name="cat_desc" ><?php echo ($cat["cat_desc"]); ?></textarea></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="text-align:left;">
								<input type="hidden" name="cat_id" value="<?php echo ($cat["cat_id"]); ?>"/>
								<input type="submit" value="提交"/>
							</td>
						</tr>
						<tr><tr>
					</tbody>
				</table>
			</form>
        </div>
    </div>
</div>

</body>
</html>