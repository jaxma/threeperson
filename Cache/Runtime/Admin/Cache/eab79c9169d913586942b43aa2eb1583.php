<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>内容管理=>课程管理</title>
    <link href="__PUBLIC__/Admin/Css/Style.css" rel="stylesheet" />
    <link href="__PUBLIC__/Admin/lhgdialog/skins/default.css" rel="stylesheet" />
    <script src="__PUBLIC__/Admin/Js/jquery-1.7.2.min.js"></script>
    <script src="__PUBLIC__/Admin/Js/jquery.treeview.js"></script>
    <script src="__PUBLIC__/Admin/lhgdialog/lhgdialog.min.js"></script>
    <script src="__PUBLIC__/Admin/Js/jQueryPlugin.js"></script>
    <script src="__PUBLIC__/Admin/Js/JavaScript.js"></script>
	<script src="__PUBLIC__/Admin/kindeditor/kindeditor.js"></script>
    <!--[if lte IE 6]>    <script src="__PUBLIC__/Admin/Js/DD_belatedPNG_0.0.8a.js" type="text/javascript"></script><script type="text/javascript">DD_belatedPNG.fix('*');</script><![endif]-->

	<style type="text/css">textarea{font-size: 12px; line-height: 20px;}</style>
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
</script>



<div class="column_Box mainAutoHeight">
	<div class="tab">
		<ul>
			<li class="current"><a href="javascript:">课程属性</a></li>
		</ul>
	</div>
	<div class="column_Box mainAutoHeight wrapBox">
        <div class="body">
			<form method="post" action="<?php echo U('Goods/insert');?>" id="submitForm" name="submitForm" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align:right;">课程名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="title" value=""  size="50"/><em>*</em></td>
						</tr>
						<tr>
							<td style="text-align:right;">课程分类：</td>
							<td style="text-align:left;">
								<select name="cat_id" id="cat_id">
									<option value="0">请选择...</option>
									<?php echo ($cat_select); ?>
								</select><em>*</em>
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">排序：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="" name="sort_order" /></td>
						</tr>
						<tr>
							<td style="text-align:right;">是否显示：</td>
							<td style="text-align:left;">
						      <input type="radio" name="is_open" value="1" checked/>是&nbsp;&nbsp;
						      <input type="radio" name="is_open" value="0" />否
							</td>
						</tr>
						<!-- <tr>
							<td style="text-align:right;">首页推荐：</td>
							<td style="text-align:left;">
						      <input type="radio" name="is_recommend" value="1" <?php if(($info["is_recommend"]) == "1"): ?>checked<?php endif; ?> />是&nbsp;&nbsp;
						      <input type="radio" name="is_recommend" value="0" <?php if(($info["is_recommend"]) == "0"): ?>checked<?php endif; ?>/>否
							</td>
						</tr> -->
						<tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;开课时间</td>
							<td style="text-align:left;"><input type="text" class="txt" name="short" value=""/></td>
						</tr>
						<tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;开课说明</td>
							<td style="text-align:left;"><input type="text" class="txt" name="content" value=""/></td>
						</tr>


						<tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;课程班型</td>
							<td style="text-align:left;">
								<input type="hidden" value="13" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="课程班型">
								<input type="hidden" name="ex_sort_order[]" value="1">
								<input name="ex_content[]" value="">
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;上课地点</td>
							<td style="text-align:left;">
								<input type="hidden" value="14" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="上课地点">
								<input type="hidden" name="ex_sort_order[]" value="2">
								<input name="ex_content[]" value="">
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;课程周期</td>
							<td style="text-align:left;">
								<input type="hidden" value="15" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="课程周期">
								<input type="hidden" name="ex_sort_order[]" value="3">
								<input name="ex_content[]" value="">
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;课程费用</td>
							<td style="text-align:left;">
								<input type="hidden" value="16" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="课程费用">
								<input type="hidden" name="ex_sort_order[]" value="4">
								<input name="ex_content[]" value=""> 元／人 (含住宿)
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;报名程度</td>
							<td style="text-align:left;">
								<input type="hidden" value="17" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="报名程度">
								<input type="hidden" name="ex_sort_order[]" value="5">
								还剩 <input name="ex_content[]" value=""> 个名额
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;距报名结束还有</td>
							<td style="text-align:left;">
								<input type="hidden" value="18" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="距报名结束还有">
								<input type="hidden" name="ex_sort_order[]" value="6">
								<input name="ex_content[]" value=""> 名
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;课程内容</td>
							<td style="text-align:left;">
								<input type="hidden" value="19" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="课程内容">
								<input type="hidden" name="ex_sort_order[]" value="7">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;特色教学</td>
							<td style="text-align:left;">
								<input type="hidden" value="20" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="特色教学">
								<input type="hidden" name="ex_sort_order[]" value="8">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;全程名师指导</td>
							<td style="text-align:left;">
								<input type="hidden" value="21" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="全程名师指导">
								<input type="hidden" name="ex_sort_order[]" value="9">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;课程量身定制</td>
							<td style="text-align:left;">
								<input type="hidden" value="22" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="课程量身定制">
								<input type="hidden" name="ex_sort_order[]" value="10">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;专业活动丰富</td>
							<td style="text-align:left;">
								<input type="hidden" value="23" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="专业活动丰富">
								<input type="hidden" name="ex_sort_order[]" value="11">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;一对一专业辅导</td>
							<td style="text-align:left;">
								<input type="hidden" value="24" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="一对一专业辅导">
								<input type="hidden" name="ex_sort_order[]" value="12">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr>
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;师兄师姐分享会</td>
							<td style="text-align:left;">
								<input type="hidden" value="25" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="师兄师姐分享会">
								<input type="hidden" name="ex_sort_order[]" value="13">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr><tr class="tdColor">
							<td style="text-align:right;"><em>*</em>&nbsp;&nbsp;全程监护式教学</td>
							<td style="text-align:left;">
								<input type="hidden" value="26" name="ex_id[]">
								<input type="hidden" name="ex_title[]" value="全程监护式教学">
								<input type="hidden" name="ex_sort_order[]" value="14">
								<textarea class="s_content" name="ex_content[]" style="width:700px;height:300px;"></textarea>
							</td>
						</tr>
						
						<tr>
							<td style="text-align:right;">关键字：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="" name="keywords"  /> 	关键字为选填项，其目的在于方便外部搜索引擎搜索</td>
						</tr>
						<tr>
							<td style="text-align:right;">描述：</td>
							<td style="text-align:left;"><textarea style="width:400px;height:100px;" name="description" ></textarea></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td align='left'>
								<input type='hidden' name='goods_id' value=''/>
								<input type="submit" value="提交"/>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
        </div>
    </div>
</div>

<script src="__PUBLIC__/Admin/kindeditor/kindeditor.js"></script>
<script type="text/javascript">var editor;
KindEditor.ready(function(K) {
	editor = K.create('.s_content', {
		resizeType : 1,
		allowPreviewEmoticons : false,
		allowImageUpload : true,
		items : [
			'source','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
			'insertunorderedlist', '|', 'emoticons', 'image', 'link']
	});
});
</script>
</body>
</html>