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
                <li class="current"><a href="javascript:">在线调查列表</a></li>
            </ul>
        </div>
        <div class="wrapBox mainAutoHeight">
		
			
            <!--招聘列表-->
            <div class="body User">

				
				<form method="POST" action="<?php echo U('Feedback/batch');?>" name="listForm">
					<table border="0" cellpadding="0" cellspacing="0" class="center">
						<thead>
							<tr>
								<th style="width:70px;">编号</th>
								<th>上传文件名称</th>
								<th>姓名</th>
								<th>电子邮箱</th>
								<th>上传时间</th>
								<th>查看详情</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
								<td><?php echo ($vo["id"]); ?></td>
								<td><?php echo ($vo["file_name"]); ?></td>
								<td><?php echo ($vo["user_name"]); ?></td>
								<th><?php echo ($vo["email"]); ?></th>
								<th><?php echo (date('Y-m-d H:i',$vo["add_time"])); ?></th>
								<td>
									<span>
										<a title="查看详情" href="__ROOT__/<?php echo ($vo["file_url"]); ?>" target='_blank'><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_edit.gif"></a>&nbsp;
										<a title="移除" onclick="return confirm('您确定要删除此记录吗？');" href="<?php echo U('Survey/del',array('id'=>$vo['id']));?>"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_drop.gif"></a>
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