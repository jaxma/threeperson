<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
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
			<li class="current"><a href="javascript:">栏目属性</a></li>
		</ul>
	</div>
	<div class="column_Box mainAutoHeight wrapBox">
        <div class="body">
			<form method="post" action="<?php echo U('Goodscat/insert');?>" id="submitForm" name="submitForm" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align:right;">分类名称：</td>
							<td style="text-align:left;"><input type="text" class="txt" name="cat_name" value=""  /><em>*</em></td>
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
						<?php if($tagscat): ?><tr>
							<td style="text-align:right;">关联属性分类：</td>
							<td style="text-align:left;">
							<?php if(is_array($tagscat)): $i = 0; $__LIST__ = $tagscat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="checkbox" name="tag_cat[]" value="<?php echo ($vo["cat_id"]); ?>" id="chk_<?php echo ($vo["cat_id"]); ?>"/>
								<label for="chk_<?php echo ($vo["cat_id"]); ?>"><?php echo ($vo["cat_name"]); ?></label>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
							</td>
						</tr><?php endif; ?>
						<tr>
							<td style="text-align:right;">排序：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="50" name="sort_order"  /><em>*</em> </td>
						</tr>
						<!-- <tr>
							<td style="text-align:right;">分类属性：</td>
							<td style="text-align:left;"><input type="text" class="txt" value="" name="cat_attr" size="100" /><em>*</em> <br/><font color="#cb0000">请填写分类属性以提供添加产品时使用，以|隔开;例：深黑色真皮|银色配饰|意大利制造</font> </td>
						</tr> -->
						<tr>
							<td style="text-align:right;">分类图片：</td>
							<td style="text-align:left;"><input type="file" name="goodscat_img" size="35" /><!-- <br/><font color="#ff0000"> 最佳大小为1153px*271px</font> --></td>
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