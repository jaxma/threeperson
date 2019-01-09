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
function delArticle(id) {
	$.dialog.confirm('你确定要删除这个文章吗？', function(){
		window.location.href="<?php echo U('Article/del');?>/article_id/"+id;
	}, function(){
		//$.dialog.tips('执行取消操作');
	});
}
</script>
    <div class="column_Box mainAutoHeight">
        <div class="tab">
            <ul>
                <li class="current"><a href="javascript:">文章列表</a></li>
            </ul>
        </div>
        <div class="wrapBox mainAutoHeight">
		
			
            <!--文章列表-->
            <div class="body User">
                <div class="item">
                    <a href="javascript:void(0);" class="dot_Item"><span class="Icon_item icon_xingjian"></span><i><input type="button" value="新建文章" class="submitNoBg" onclick="window.location.href='<?php echo U('Article/add',array('cat_id'=>$_REQUEST['cat_id']));?>'"/></i></a>
                    <div class="searchBar">
						<form action="<?php echo U('Article/index');?>">
							所属分类：
							<select name="cat_id" class="dot_Item">
								<option value="0">全部分类</option>
								<?php echo ($cat_select); ?>
							</select>
							排序方式：
							<select name="sort_by" class="dot_Item">
								<option value="">请选择排序方式</option>
								<option value="a.article_id" <?php if($_GET['sort_by'] == 'a.article_id'): ?>selected=""<?php endif; ?>>文章编号</option>
								<option value="a.title" <?php if($filter["sort_by"] == 'a.title'): ?>selected=""<?php endif; ?>>文章标题</option>
								<option value="ac.cat_name" <?php if($filter["sort_by"] == 'ac.cat_name'): ?>selected=""<?php endif; ?>>文章分类</option>
								<option value="a.sort_order" <?php if(($filter["sort_by"] == 'a.sort_order') or empty($_GET['sort_by'])): ?>selected=""<?php endif; ?>>排序号</option>
								<option value="a.is_open" <?php if($filter["sort_by"] == 'a.is_open'): ?>selected=""<?php endif; ?>>是否显示</option>
							</select>
							排序：
							<select name="sort_order" class="dot_Item">
								<option value="">请选择排序</option>
								<option value="DESC" <?php if($_GET['sort_order'] == 'DESC'): ?>selected=""<?php endif; ?>>倒序</option>
								<option value="ASC" <?php if(($filter["sort_order"] == 'ASC') or empty($_GET['sort_order'])): ?>selected=""<?php endif; ?>>顺序</option>
							</select>
							文章标题 ：
							<input type="text" class="txt" name="keyword" value="<?php echo ($filter["keyword"]); ?>"/>&nbsp;&nbsp;<input type="submit" class="submit btn_search" value="搜索" />
						</form>
                    </div>
                </div>
				
				<form method="POST" action="<?php echo U('Article/batch');?>" name="listForm">
					<table border="0" cellpadding="0" cellspacing="0" class="center">
						<thead>
							<tr>
								<th style="width:70px;"><input type="checkbox" name="checkBox_All" class="checkBox_All" />编号</th>
								<th>文章标题</th>
								<th>文章分类</th>
								<th>排序</th>
								<!-- <th>是否显示</th> -->
								<th>首页推荐</th>
								<th>添加日期</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
								<td><input type="checkbox" name="checkboxes[]" class="checkBox_list" value="<?php echo ($vo["article_id"]); ?>" /><?php echo ($vo["article_id"]); ?></td>
								<td><?php echo ($vo["title"]); ?></td>
								<td><?php echo ($vo["cat_name"]); ?></td>
								<td><?php echo ($vo["sort_order"]); ?></td>
								<!-- <td><?php if($vo['is_open']==1): ?><img src="__PUBLIC__/Admin/Img/yes.gif"/><?php else: ?><img src="__PUBLIC__/Admin/Img/no.gif"/><?php endif; ?></td> -->
								<td><?php if($vo['is_recommend']==1): ?><img src="__PUBLIC__/Admin/Img/yes.gif"/><?php else: ?><img src="__PUBLIC__/Admin/Img/no.gif"/><?php endif; ?></td>
								<td><?php echo (date('Y-m-d H:i:s',$vo["add_time"])); ?></td>
								<td>
									<span>
										<a title="编辑" href="<?php echo U('Article/edit',array('id'=>$vo['article_id']));?>"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_edit.gif"></a>&nbsp;
										<a title="移除" onclick="delArticle('<?php echo ($vo["article_id"]); ?>')" href="javascript:;"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_drop.gif"></a>
									</span>
								</td>
							</tr><?php endforeach; endif; ?>
						</tbody>
					</table>
					
					
					<div class="lineHeight" style="border-bottom:1px dashed #cccccc;"></div>
					<div class="batchChange">
						<div class="f_l">
							<select onchange="changeAction()" id="selAction" name="type">
							<option value="">请选择...</option>
							<option value="button_remove">批量删除</option>
							<!-- <option value="button_hide">批量隐藏</option>
							<option value="button_show">批量显示</option> -->
							<option value="button_recommend_yes">批量推荐</option>
							<option value="button_recommend_no">取消推荐</option>
							<option value="move_to">转移到分类</option>
							</select>
							<select name="target_cat" style="display:none">
								<option value="0">请选择...</option>
								<?php echo ($cat_select); ?>
							</select>
							<input type="submit" class="button" name="btnSubmit" id="btnSubmit" value=" 确定 "/>
						</div>
						<div class="f_r">
							<div class="pagination"><?php echo ($page); ?></div>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
	<script type="text/javascript">
	/**
	   * @param: bool ext 其他条件：用于转移分类
	   */
	  function confirmSubmit(frm, ext){
		  if (frm.elements['type'].value == 'button_remove'){
			  return confirm('您确定要删除文章吗');
		  }else if (frm.elements['type'].value == 'not_on_sale'){
			  return confirm('您确定要隐藏文章吗');
		  }else if (frm.elements['type'].value == 'move_to'){
			  ext = (ext == undefined) ? true : ext;
			  return ext && frm.elements['target_cat'].value != 0;
		  }else if (frm.elements['type'].value == ''){
			  return false;
		  }else{
			  return true;
		  }
	  }
	  function changeAction(){
		  var frm = document.forms['listForm'];
		  // 切换分类列表的显示
		  frm.elements['target_cat'].style.display = frm.elements['type'].value == 'move_to' ? '' : 'none';
		  if (!document.getElementById('btnSubmit').disabled &&
			  confirmSubmit(frm, false)){
			  frm.submit();
		  }
	  }
	</script>
</body>
</html>