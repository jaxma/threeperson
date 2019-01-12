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

    <div class="column_Box mainAutoHeight">
        <div class="tab">
            <ul>
                <li class="current"><a href="javascript:">会员列表</a></li>
            </ul>
        </div>
        <div class="wrapBox mainAutoHeight">
		
			
            <!--文章列表-->
            <div class="body User">
				<form method="POST" action="<?php echo U('User/batch');?>" name="listForm">
					<table border="0" cellpadding="0" cellspacing="0" class="center">
						<thead>
							<tr>
								<th style="width:70px;"><input type="checkbox" name="checkBox_All" class="checkBox_All" />编号</th>
								<th>公司名称</th>
								<th>用户名</th>
								<th>联系电话</th>
								<th>E-mail</th>
								<th>上次登录时间</th>
								<th>注册日期</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
								<td><?php echo ($vo["user_id"]); ?></td>
								<td><?php echo ($vo["company"]); ?></td>
								<td><?php echo ($vo["user_name"]); ?></td>
								<td><?php echo ($vo["phone"]); ?></td>
								<td><?php echo ($vo["email"]); ?></td>
								<td><?php echo ($vo["last_login_time"]); ?></td>
								<td><?php echo ($vo["reg_time"]); ?></td>
								<td>
									<span>
										<!-- <a title="编辑" href="<?php echo U('User/edit',array('user_id'=>$vo['user_id']));?>"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_edit.gif"></a>&nbsp; -->
										<a title="移除" href="<?php echo U('User/drop',array('user_id'=>$vo['user_id'],'p'=>$_GET['p']));?>" onclick="return confirm('删除不可恢复，你确定删除吗？');"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_drop.gif"></a>
									</span>
								</td>
							</tr><?php endforeach; endif; ?>
						</tbody>
					</table>
					
					
					<div class="lineHeight" style="border-bottom:1px dashed #cccccc;"></div>
					<div class="batchChange">
						<div class="f_r">
							<div class="pagination"><?php echo ($page); ?></div>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</body>
</html>