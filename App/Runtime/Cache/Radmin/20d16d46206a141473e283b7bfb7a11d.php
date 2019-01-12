<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="shortcut icon" href="__PUBLIC__/Radmin_v3/images/logo.png" />
    <title><?php echo (C("SYSTEM_NAME")); ?>&nbsp;·&nbsp;系统管理</title>
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/plugs/bootstrap/css/bootstrap.min.css?ver=170725" />
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/plugs/layui/css/layui.css?ver=170725" />
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/plugs/default/css/console.css?ver=170725">
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/plugs/default/css/animate.css?ver=170725">
    <!--自定义css-->
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/plugs/default/css/left_menu.css?ver=170725">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/Radmin_v3/plugs/default/css/top_bar.css"/>
    <!--列表展示css-->
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/list.css">
    <!--内容编辑/添加css-->
    <link rel="stylesheet" href="__PUBLIC__/Radmin_v3/css/content.css">

    <script src="__PUBLIC__/Radmin_v3/plugs/require/require.js?ver=170725"></script>
    <script src="__PUBLIC__/Radmin_v3/js/app.js?ver=170725"></script>
    <!--解决弹出层问题-->
    <style>
        .modal-backdrop{
            display: none;
        }
        #dialog{
            margin-top: 5%;

        }

    </style>
    <script>
        var URL = "__URL__";
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
<!--top开始-->
<style type="text/css">
  .layui-form-item span{
    line-height: 38px!important;
  }
  @media screen and (max-width: 768px) {
      .console-topbar {
          min-width: 0;
      }
  }
</style>
<div class="framework-topbar">
  <div class="console-topbar">
    <div class="topbar-wrap topbar-clearfix">
      <!--顶部左边开始-->
      <!--左边Logo开始-->
      <div class="topbar-head topbar-left">
        <a href="#" class="topbar-logo topbar-left">
          <span class="icon-logo"><?php echo (C("SYSTEM_NAME")); ?>管理系统</span>
        </a>
      </div>
      <div class="topbar-menu topbar-middle hidden-xs">
        <ul class="control-menu">
          <li class="dropdown pull-right tabdrop">
            <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="true">
              <i class="glyphicon glyphicon-align-justify"></i><b class="caret"></b>
            </a>
            <ul class="dropdown-menu downs">
            </ul>
          </li>
        </ul>
      </div>
      <!--左边logo结束-->
      <!--<a data-menu-target='m-61' class="topbar-home-link topbar-btn topbar-left">-->
      <!--<span> 微信管理</span>-->
      <!--</a>-->
      <!--<a data-menu-target='m-2' class="topbar-home-link topbar-btn topbar-left">-->
      <!--<span> 系统管理</span>-->
      <!--</a>-->
      <!--顶部左边结束-->
      <!--顶部右边开始-->
      <!--<div class="topbar-breadcrumb topbar-middle">
              <span class="layui-breadcrumb" style="visibility: visible;">
                <a href="/">首页<span class="layui-box">&gt;</span></a>
                <a href="/demo/">演示<span class="layui-box">&gt;</span></a>
                <a><cite>导航元素</cite></a>
              </span>
            </div>-->
      <div class="topbar-info topbar-right">
        <a data-reload data-tips-text='刷新' class=" topbar-btn topbar-left topbar-info-item text-center" style='width:50px;'>
          <span class='glyphicon glyphicon-refresh'></span>
        </a>
        <script>
          require(['jquery', 'topbar'], function() {
            $('[data-reload]').hover(function() {
              $(this).find('.glyphicon').addClass('fa-spin');
            }, function() {
              $(this).find('.glyphicon').removeClass('fa-spin');
            });
          });
        </script>
        <div class="topbar-left topbar-user">
          <div class="dropdown topbar-info-item">
            <a href="#" class="dropdown-toggle topbar-btn text-center" data-toggle="dropdown">
              <span class='glyphicon glyphicon-user'></span> <?php echo (session('aname')); ?> </span>
              <span class="glyphicon glyphicon-menu-up transition" style="font-size:12px"></span>
            </a>
            <ul class="dropdown-menu">
              <li class="topbar-info-btn">
                <a data-modal="__GROUP__/admin/editpsw" data-title="修改密码">
                  <span><i class='glyphicon glyphicon-lock'></i> 修改密码</span>
                </a>
              </li>

              <li class="topbar-info-btn">
                <a href="__GROUP__/index/logout" data-confirm='确定要退出登录吗？'>
                  <span><i class="glyphicon glyphicon-log-out"></i> 退出登录</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--顶部右边结束-->
    </div>
  </div>
</div>
<!--top结束-->
<!--左边导航栏开始-->
<div class="framework-body framework-sidebar-full">

    <div class="framework-sidebar">
      <div class="hide-scroll">
        <div class="sidebar-content">
            <div class="wrapper">

</div>
<div class="sidebar-inner">
    <div class="sidebar-fold">
        <span class="glyphicon glyphicon-option-vertical transition"></span>
    </div>
    <div data-menu-box="m-1" class="toleft">
        <!--用户信息-->
        <div class="user-wrapper">
            <div class="user-img">
                <img class="avatar" src="__ROOT__/upload/system_logo/system_logo.png" onerror="this.src='__PUBLIC__/Radmin_v3/images/logo/system_logo.png'" alt="" />
            </div>
            <div class="user-info">
                <p class="name"><?php echo (session('aname')); ?></p>
                <span>
                    <i class="status"></i>在线</span>
            </div>
        </div>
        <!--搜索框-->
        <!--<div class="search-wrapper">
      <form action="" method="get" class="sidebar-form" onsubmit="return false;">
        <div class="input-group">
          <input type="text" name="q" class="form-control" id="search-text" placeholder="搜索菜单">
          <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
          <div class="menuresult list-group sidebar-form hide" style="width: 210px;">
          </div>
        </div>
      </form>
    </div>-->

        <!--新版菜单列表-->
        <div class="menu-wrapper" style="padding-bottom: 100px;">

            <ul class="sidebar-menu">
                <li>
                    <a data-menu-node='m-0-0' data-open="__GROUP__/analysis/index" class="only">
                        <i class="fa fa-home icons"></i>
                        <span class="title2">首页</span>
                    </a>
                </li>
                
                
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-cogs icons"></i>
                        <span class="title">账号管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-1-2' data-open="__GROUP__/admin/edit">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">编辑资料</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                        <li>
                            <a class="tm-items" data-menu-node='m-1-3' data-open="__GROUP__/admin/active_log">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">操作日志</span>
                            </a>
                        </li>
                        <?php if( $aid==1 || in_array(1,$admin_auth) || in_array($aid,$superids) ): ?>
                        <li>
                            <a class="tm-items" data-menu-node='m-1-4' data-open="__GROUP__/admin/manage">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">管理员管理</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if( in_array($aid,$superids) || in_array(98,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-asterisk icons"></i>
                        <span class="title">公司管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
<!--                             <a class="tm-items" data-menu-node='m-2-1' data-open="__GROUP__/company/company">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">公司介绍</span>
                            </a> -->
                            <a class="tm-items" data-menu-node='m-2-1' data-open="__GROUP__/company/company">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">公司信息</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                        <!-- <li>
                            <a class="tm-items" data-menu-node='m-2-1' data-open="__GROUP__/photo/company_con2">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">个人介绍</span>
                                
                            </a>
                        </li> -->
                    </ul>
                </li>
                <?php endif;?>
                <?php if( in_array($aid,$superids) || in_array(99,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-sitemap icons"></i>
                        <span class="title">网站分类管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-3-1' data-open="__GROUP__/photo/cat">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">网站分类</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>

                <?php if( in_array($aid,$superids) || in_array(97,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-file-text icons"></i>
                        <span class="title">项目</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-4-1' data-open="__GROUP__/item/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">项目列表</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>

                <?php if( in_array($aid,$superids) || in_array(99,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-bank icons"></i>
                        <span class="title">事务所管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-6-1' data-open="__GROUP__/aboutus/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">管理列表</span>
                            </a>
                        </li>
                        <!-- <li>
                            <a class="tm-items" data-menu-node='m-5-2' data-open="__GROUP__/aboutus/designer">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">设计师</span>
                            </a>
                        </li>
                        <li>
                            <a class="tm-items" data-menu-node='m-5-3' data-open="__GROUP__/aboutus/books">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">出版书</span>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <?php endif;?>
<!--                 <li>
                    <a data-menu-node='m-200-1' data-open="__GROUP__/cats/index" class="only">
                        <i class="fa fa-home icons"></i>
                        <span class="title2">分类</span>
                    </a>
                </li> -->
                
                <?php if( in_array($aid,$superids) || in_array(100,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-money icons"></i>
                        <span class="title">摄影封面管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-9-1' data-open="__GROUP__/photo/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">摄影封面上传</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>
                
                <?php if( in_array($aid,$superids) || in_array(101,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-gift icons"></i>
                        <span class="title">视频分享管理</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-12-1' data-open="__GROUP__/video/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">视频上传</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                        <li>
                            <a class="tm-items" data-menu-node='m-12-2' data-open="__GROUP__/video/scan_video">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">视频文件夹</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>

                <?php if( in_array($aid,$superids) || in_array(102,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-file-text icons"></i>
                        <span class="title">后台花絮</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                        <!--<span class="layui-badge-dot spot"></span>-->
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-15-1' data-open="__GROUP__/backstage/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">后台花絮</span>
                                <!--<span class="layui-badge">99</span>-->
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>
                
                <?php if( in_array($aid,$superids) || in_array(21,$admin_auth)):?>
                <li class="treeview">
                    <a href="javascript:;" class="list">
                        <i class="fa fa-line-chart icons"></i>
                        <span class="title">系统配置</span>
                        <span class="fa fa-angle-right arrow-icon"></span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="tm-items" data-menu-node='m-22-1' data-open="__GROUP__/webset/index">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">系统配置</span>
                            </a>
                        </li>
                        <?php if( $aid==1 ): ?>
                        <li>
                            <a class="tm-items" data-menu-node='m-22-2' data-open="__GROUP__/webset/clear_cache">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">清除缓存</span>
                            </a>
                        </li>
                        <li>
                            <a class="tm-items" data-menu-node='m-22-3' data-open="__GROUP__/webset/replace">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">系统更新</span>
                            </a>
                        </li>
<!--                         <li>
                            <a class="tm-items" data-menu-node='m-22-4' data-open="__GROUP__/webset/system_style">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">后台样式</span>
                            </a>
                        </li> -->
                        <li>
                            <a class="tm-items" data-menu-node='m-22-5' data-open="__GROUP__/webset/log_view">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">日志管理</span>
                            </a>
                        </li>
                        <?php endif;?>
<!--                         <li>
                            <a class="tm-items" data-menu-node='m-22-6' data-open="__GROUP__/webset/certificate_set">
                                <i class="fa fa-plus-circle icons2"></i>
                                <span class="title2">自定义授权书</span>
                            </a>
                        </li> -->
                        
                    </ul>
                </li>
                <?php endif;?>
                

            </ul>
        </div>
    </div>
</div>
        </div>
      </div>
    </div>
    <!--左边导航栏到此结束-->
    <!--中间内容部分-->
    <div class="framework-container layer-main-container framework-sidebar-full">
    </div>
    <!--中间内容部分结束-->
</div>



<!-- 百度统计 开始 -->
<!-- 百度统计 结束 -->
</body>

</html>