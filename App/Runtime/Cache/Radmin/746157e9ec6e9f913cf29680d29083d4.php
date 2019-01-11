<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-cn">


  <head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="shortcut icon" href="__PUBLIC__/Radmin_v3/images/logo.png" />
    <title>用户登录&nbsp;·&nbsp;<?php echo (C("SYSTEM_NAME")); ?></title>
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/layui.css" />
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/console.css">
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/animate.css">
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/login.css">

    <script src="__PUBLIC__/Radmin_v3/plugs/require/require.js?ver=<?php echo date('ymd');?>"></script>
    <script src="__PUBLIC__/Radmin_v3/js/app.js?ver=<?php echo date('ymd');?>"></script>

    <script type="text/javascript">
      var URL = '__GROUP__/Login/verify/';
    </script>
    
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?6c445663e719abf0b505d6cf82bbb15d";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
    </script>
  </head>

  <body>

    <div class="login-container" style="height:100%">

      <!-- 动态云层动画 开始 -->
      <div class="clouds-wrapper">
        <div class="clouds clouds-footer"></div>
        <div class="clouds"></div>
        <div class="clouds clouds-fast"></div>
      </div>
      <!-- 动态云层动画 结束 -->

      <!-- 顶部导航条 开始 -->
      <!-- 顶部导航条 开始 -->
<div class="header">
    <span>欢迎登录<?php echo (C("SYSTEM_NAME")); ?>后台管理系统</span>
    <ul>
        <!--<li><a href="#">回首页</a></li>-->
<!--        <li>
            <a href="javascript:void(0)" target="_blank">
                <b style="color:#fff">推荐谷歌浏览器</b>
            </a>
        </li>-->
        <li><a target="_blank" href="http://weisika.net">官网</a></li>
<!--        <li>
            <a href="http://www.toposla.com/about.html" target="_blank">关于我们</a>
        </li>-->
    </ul>
</div>
<!-- 顶部导航条 结束 -->
      <!-- 顶部导航条 结束 -->

      <!-- 页面表单主体 开始 -->
      <div class="layui-container">
        <div class="layui-row">

        <form onsubmit="return false;" data-time="0.001" data-auto="true"  class="content layui-form layui-col-md8 layui-col-md-offset2 layui-col-sm10 layui-col-sm-offset1">
          <div class="people">
            <div class="tou"></div>
            <div id="left-hander" class="initial_left_hand transition"></div>
            <div id="right-hander" class="initial_right_hand transition"></div>
          </div>
          <ul>
            <li>
              <input style='display:none' />
              <input required="required" pattern="^\S{4,}$" title="请输入4位及以上的字符" type="text" name="username" value="" autocomplete="off" autofocus="autofocus" id="account" class="login-input username" placeholder="请输入用户名" />
            </li>
            <li>
              <input style='display:none' />
              <input required="required" pattern="^\S{4,}$" title="请输入4位及以上的字符" type="password" name="password" id="passWord" autocomplete="off" class="login-input password" placeholder="请输入密码" />
            </li>
            <li class="flex-wrapper">
                    <input  style='display:none'/>
                    <input required="required" pattern="^\S{4,}$" type="text" name="code" value="" autocomplete="off" class="login-input code flex2" placeholder="请输入验证码" style="display:inline-block" />
                           <!--验证码-->
            <a href="javascript:void(change_code(this));" class="login-change-code flex1" id="changeimg_link">
                    <img src="__GROUP__/Login/verify" id="code"/>
                </a>
                </li>
            
            <li class="text-center">
              <button  class="layui-btn layui-disabled" data-form-loaded="立 即 登 入" id="verifys">立 即 登 入</button>
              <!--<button type="button" class="layui-btn layui-disabled" id="verifys" data-form-loaded="立 即 登 入">立 即 登 入</button>-->
              <!--<input type="hidden" id="scroll-verify" data-modal="__GROUP__/Public/verifys" data-title="滑动验证码"></input>-->
              <!--<a style="position:absolute;display:block;right:0" href="javascript:void(0)">忘记密码？</a>-->
            </li>
          </ul>
        </form>
      </div>
      </div>
      <!-- 页面表单主体 结束 -->

      <!-- 底部版权信息 开始 -->
      <!-- 底部版权信息 开始 -->
<div class="footer">技术支持：广州市topos网络科技有限公司 © 2017-2019</div>
<!-- 底部版本信息 结束 -->
      <!-- 底部版本信息 结束 -->

    </div>

    <script>
      var passWord = '';
      var account = '';
      var loginurl = '__GROUP__/Login/login_code';
      var code = '';
      if(window.location.href.indexOf('#') > -1) {
        window.location.href = window.location.href.split('#')[0];
      }
      require(['jquery'], function($) {
        $('[name="password"]').on('focus', function() {
          $('.tou').css('backgroundImage', 'url(__PUBLIC__/Radmin_v3/images/touActive.png)');
//           $('#left-hander').removeClass('initial_left_hand').addClass('left_hand');
//           $('#right-hander').removeClass('initial_right_hand').addClass('right_hand')
        }).on('blur', function() {
          $('.tou').css('backgroundImage', 'url(__PUBLIC__/Radmin_v3/images/tou.png)');
//           $('#left-hander').addClass('initial_left_hand').removeClass('left_hand');
//           $('#right-hander').addClass('initial_right_hand').removeClass('right_hand')
        });

        $('#verifys').click(function() {
          passWord = $('#passWord').val();
          account = $('#account').val();
          code = $('.code').val();
          if(passWord == "" || account == "") {
            layer.msg("帐号或密码不能为空！");
            return false;
          }else if(code == ''){
                layer.msg("验证码不能为空！");
                return false;
            }
            odata={
                password:passWord,
                username:account,
                code:code,
            }
            $.post("__APP__/Radmin/login/login_code", odata, function(data) {
               if(data.code == '1'){
                   layer.msg(data.msg);
                   setTimeout(function() {
                       window.location.href =  '__APP__/radmin/index/index/#' +'__APP__/radmin/analysis/index?spm=m-0-0';
                   }, 500);
               }else if(data.code == '-1'){
                   layer.msg(data.msg);
                   change_code()
                }else{
                   layer.msg(data.msg);
               }
            });
        })
      });

      //验证码图像转换
      function change_code(obj) {
        $("#code").attr("src", URL + Math.random());
        return false;
      }

      //  $('#verifys').bind('click',function(){
      //    $("#verifys-wrapper").fadeIn();
      //  });
    </script>

  </body>


</html>