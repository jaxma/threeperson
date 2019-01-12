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
			<li class="current"><a href="javascript:">管理员信息</a></li>
		</ul>
	</div>
	<div class="column_Box mainAutoHeight wrapBox">
        <div class="body">
			<form method="post" action="<?php echo U('Privilege/update');?>" id="submitForm" name="submitForm">
				<table width="100%">
				  <tbody><tr>
					<td class="label">用户名</td>
					<td>
					  <input type="text" size="34" value="<?php echo ($user["user_name"]); ?>" maxlength="20" name="user_name"><span class="require-field">*</span></td>
				  </tr>
				  <tr>
					<td class="label">Email地址</td>
					<td>
					  <input type="text" size="34" value="<?php echo ($user["email"]); ?>" name="email"><span class="require-field">*</span></td>
				  </tr>
				  <tr>
					<td class="label">旧密码</td>
					<td>
					  <input type="password" size="34" maxlength="32" name="old_password"><span class="require-field">*</span></td>
				  </tr>
				  <tr>
					<td class="label">新密码</td>
					<td>
					  <input type="password" size="34" maxlength="32" name="new_password"><span class="require-field">*</span></td>
				  </tr>
				  <tr>
					<td class="label">确认新密码</td>
					<td>
					  <input type="password" size="34" value="" name="pwd_confirm"><span class="require-field">*</span></td>
				  </tr>
				   <!-- <tr>
				      				   <td class="label">角色选择</td>
				      					<td>
				      					  <select name="select_role">
				      						<option value="">请选择...</option>
				      							<?php if(is_array($select_role)): $i = 0; $__LIST__ = $select_role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["role_id"]); ?>"<?php if($vo.role_id==$user.role_id): ?>selected<?php endif; ?>><?php echo ($vo["role_name"]); ?> </option><?php endforeach; endif; else: echo "" ;endif; ?>
				      					    </select>
				      					</td>
				      				  </tr> -->
				  <tr>
					<td align="center" colspan="2">
					  <input type="submit" class="button" value=" 确定 ">&nbsp;&nbsp;&nbsp;
					  <input type="reset" class="button" value=" 重置 ">
					  <input type="hidden" value="update" name="act">
					  <input type="hidden" value="<?php echo ($user["user_id"]); ?>" name="id"></td>
				  </tr>
				</tbody></table>
			</form>
        </div>
    </div>
</div>
</body>
</html>