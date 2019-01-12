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
function delAds(id) {
	$.dialog.confirm('你确定要删除这个广告图吗？', function(){
		window.location.href="<?php echo U('Ads/del');?>/ads_id/"+id;
	}, function(){
		//$.dialog.tips('执行取消操作');
	});
}
</script>
    <div class="column_Box mainAutoHeight">
        <div class="tab">
            <ul>
                <li class="current"><a href="javascript:">广告图列表</a></li>
            </ul>
        </div>
        <div class="wrapBox mainAutoHeight">
		
			
            <!--广告图列表-->
            <div class="body User">
                <div class="item">
                    <a href="javascript:void(0);" class="dot_Item"><span class="Icon_item icon_xingjian"></span><i><input type="button" value="新建广告图" class="submitNoBg" onclick="window.location.href='<?php echo U('Ads/add',array('cat_id'=>$cat_id));?>'"/></i></a>
                </div>
				
				<form method="POST" action="<?php echo U('Ads/batch');?>" name="listForm">
					<table border="0" cellpadding="0" cellspacing="0" class="center">
						<thead>
							<tr>
								<th style="width:70px;">编号</th>
								<!-- <th>广告图标题</th> -->
								<th>广告图描述</th>
								<th>预览</th>
								<?php if(($_GET['cat_id']) != "1"): ?><th>广告图链接</th><?php endif; ?>
								<th>排序</th>
								<!-- <th>是否显示</th> -->
								<th>图片尺寸</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($adsList)): foreach($adsList as $key=>$vo): ?><tr>
								<td><?php echo ($vo["ads_id"]); ?></td>
								<!-- <td><font color='red'><?php echo ($vo["title"]); ?></font></td> -->
								<td><?php echo (nl2br($vo["description"])); ?></td>
								<td><img src="__ROOT__/<?php echo ($vo["original_img"]); ?>" style='max-width:300px; max-height:200px;'/></td>
								<?php if(($_GET['cat_id']) != "1"): ?><td><?php echo ($vo["link"]); ?></td><?php endif; ?>
								<td><?php echo ($vo["sort_order"]); ?></td>
								<!-- <td><?php if($vo['is_open']==1): ?><img src="__PUBLIC__/Admin/Img/yes.gif"/><?php else: ?><img src="__PUBLIC__/Admin/Img/no.gif"/><?php endif; ?></td> -->
								<td><font color='red'><?php echo ($vo["img_size"]); ?></font></td>
								<td>
									<span>
										<a title="编辑" href="<?php echo U('Ads/edit',array('id'=>$vo['ads_id'],'cat_id'=>$vo['cat_id']));?>"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_edit.gif"></a>&nbsp;
										<a title="移除" onclick="delAds('<?php echo ($vo["ads_id"]); ?>')" href="javascript:;"><img width="16" height="16" border="0" src="__PUBLIC__/Admin/Img/icon_drop.gif">
										</a>
									</span>
								</td>
							</tr><?php endforeach; endif; ?>
						</tbody>
					</table>
					
					
					<div class="lineHeight" style="border-bottom:1px dashed #cccccc;"></div>
				</form>
            </div>
        </div>
    </div>
</body>
</html>