<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="x-ua-compatible" content="ie=7" />
<title>
cyx	网站管理登录 - cyx科技 http://cyx.com
</title>
<script type="text/javascript" src="__PUBLIC__/Admin/Js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/Admin/Js/jquery.form.js"></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/Admin/Css/css_hunuo.css" />
<script language="JavaScript">
$(function(){
	$('#form1').ajaxForm({
		beforeSubmit:  checkForm,  // pre-submit callback
		success:       complete,  // post-submit callback
		dataType: 'json'
	});
	function checkForm(){
		if( '' == $.trim($('#username').val())){
			$('#result').html('用户名不能为空！').show();
			return false;
		}else if( '' == $.trim($('#txtPassword').val())){
			$('#result').html('密码不能为空！').show();
			return false;
		}else if( '' == $.trim($('#verify').val())){
			$('#result').html('验证码不能为空！').show();
			return false;
		}
	}
	function complete(data){
		$('#result').html(data.info).show();
		if (data['status']==1){
			window.location.href="<?php echo U('Index/index');?>";
		}
	}
});
</script>

</head>


<body class="body_login">
<form name="theForm" method="post" action="<?php echo U('Public/checkLogin');?>" id="form1">
<div>
</div>
<div>

</div>
    <div class="login">
        <div class="loginlogo">
            <img src="__PUBLIC__/Admin/Img/logo.gif"/>
            
            </div>
        <div class="logintxt">
            <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                    <td height="180">
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        用户名：
                    </td>
                    <td>
                        <input name="username" type="text" id="username" class="text" />
                        <span id="rfvLoginName" style="color:Red;visibility:hidden;">(*不能为空)</span>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        密<span style="padding-left: 12px;">码</span>：
                    </td>
                    <td>
                        <input name="password" type="password" id="txtPassword" class="keyboardInput text" />
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="baseline">
                        验证码：
                    </td>
                    <td valign="top">
                        <input name="verify" type="text" id="verify" maxlength="8" style="width: 93px;" class="text"/>
						<img src="__ROOT__/Base-verify_code-w-50-h-25.html" title="看不清？单击此处刷新" onclick="this.src+='?rand='+Math.random();"  style="cursor: pointer; vertical-align: middle;margin-top:-5px;" align="absmiddle"/>
                        <span id="rfvCode" style="color:Red;visibility:hidden;">(*不能为空)</span>
						<input type="hidden" name="ajax" value="1">
                    </td>
                </tr>
				
				<tr>
                    <td align="right" valign="baseline">
                        &nbsp;
                    </td>
                    <td valign="top">
                        <div id="result"></div>
                    </td>
                </tr>
				
                <tr>
                    <td height="50" >
                    </td>
                    <td style="color:#4e4242"> 
                        <input type="submit" name="btnLogin" value="" class="but_login" />&nbsp;<input id="remember" type="checkbox" name="remember" /><label for="remember">记住用户名</label>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear">
        </div>
        <div id="divMessage" style="padding-left: 450px;">
        </div>
        <div class="copyright">
            cyx科技 http://www.cyx.com</div>
    </div>
    
	<script language="JavaScript">
	  document.forms['theForm'].elements['username'].focus();
	</script>

</form>
</body>
</html>