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
$(function(){
	autoHeight(jQuery('.autoHeight'));
	jQuery(".column_Box").each(function () {
		var t = jQuery(this);
		if (t.length < 1) return;
		Tab_click(t.find('.tab ul li'), t.find(".wrapBox .body"));
	});
});
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
			<form method="post" action="<?php echo U('Articlecat/insert');?>" id="submitForm" name="submitForm" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align:right;">文章分类名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="cat_name" value=""  /><em>*</em></td>
						</tr>
						<tr>
							<td style="text-align:right;">分类英文名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="cat_en_name" value=""  /><em>*</em></td>
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
							<td style="text-align:left;"><input type="text" class="txt" value="50" name="sort_order"  /><em>*</em> </td>
						</tr>
						<tr>
							<td style="text-align:right;">分类图片：</td>
							<td style="text-align:left;">
								<input type="file" name="cat_img"  />
								<!-- <br/><font color='red'>
									加盟叶子分类图片:299px * 174px;<br/>
									关于叶子分类图片（首页）:240px * 287px;<br/>
									关于叶子分类图片（分类页）:258px * 140px;<br/>
								</font> -->
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">分类介绍：</td>
							<td style="text-align:left;"><textarea style="width:880px;height:400px;" name="content" id="content" ></textarea></td>
						</tr>
						<tr>
							<td style="text-align:right;">关键字：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="" name="keywords"  /> 	关键字为选填项，其目的在于方便外部搜索引擎搜索</td>
						</tr>
						<tr>
							<td style="text-align:right;">描述：</td>
							<td style="text-align:left;"><textarea style="width:400px;height:100px;" name="cat_desc" ></textarea></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="text-align:left;">
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