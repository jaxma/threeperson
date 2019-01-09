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
		url:"<?php echo U('Goodscat/add');?>",
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
function goodscatInfo(id){
	$.ajax({
		url:"<?php echo U('Goodscat/edit');?>",
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
function delGoodscat(id) {
	$.dialog.confirm('你确定要删除这个记录吗？', function(){
		window.location.href="<?php echo U('Goodscat/del');?>/cat_id/"+id;
	}, function(){
		//$.dialog.tips('执行取消操作');
	});
}
$(function(){
	<?php if($action == 'add'): ?>manageColumn(<?php echo ($parent_id); ?>);<?php endif; ?>
	<?php if($action == 'edit'): ?>goodscatInfo(<?php echo ($cat_id); ?>);<?php endif; ?>
})
</script>
	<div id="loading"></div>
    <div class="filetree treeview mainAutoHeight border_radius">
        <ul id="browser" class="autoHeight_scroll browser">
            <li><span class="folder">当前站点</span>
                <ul>
					<?php echo ($html); ?>
					<!-- <?php if(is_array($articlecat)): $i = 0; $__LIST__ = $articlecat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="closed"><a href="javascript:void(0);" onclick="articlecatInfo(<?php echo ($vo["id"]); ?>)"><span class="folder"><?php echo ($vo["name"]); ?></span></a>
							<ul>
								<?php if(is_array($vo['cat_id'])): $i = 0; $__LIST__ = $vo['cat_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0);" onclick="articlecatInfo(<?php echo ($vo2["id"]); ?>)"><span class="file"><?php echo ($vo2["name"]); ?></span></a>
										<ul>
											<?php if(is_array($vo2['cat_id'])): $i = 0; $__LIST__ = $vo2['cat_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo3): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0);" onclick="articlecatInfo(<?php echo ($vo3["id"]); ?>)"><span class="file"><?php echo ($vo3["name"]); ?></span></a>
													<ul>
														<volist name="vo3['cat_id']" id="vo4">
															<li><a href="javascript:void(0);" onclick="articlecatInfo(<?php echo ($vo4["id"]); ?>)"><span class="file"><?php echo ($vo4["name"]); ?></span></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
													</ul>
												</li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ul>
									</li><?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</li>
					</volist> -->
                </ul>
            </li>
        </ul>
    </div>
    <div class="ContainerMain_Box mainAutoHeight border_radius">
        <div class="column_Box mainAutoHeight">
			<div class="tab">
				<ul>
					<li class="current"><a href="javascript:">分类属性</a></li>
				</ul>
			</div>
			<div class="wrapBox mainAutoHeight">
				<!--栏目属性-->
				<div class="body">
					<div class="item">
						<a href="javascript:void(0);" class="dot_Item" onclick="manageColumn(0)"><span class="Icon_item icon_xingjian2"></span><i>新建分类</i></a>
					</div>
				</div>
			</div>
		</div>
    </div>

</body>
</html>