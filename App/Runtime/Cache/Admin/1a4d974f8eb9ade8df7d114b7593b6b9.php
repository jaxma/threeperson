<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <title></title>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/fonts/mui.ttf">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/light7.min.css">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/light7-swiper.min.css">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/fonts/icon.ttf">
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/public/public.css">
    <style>
        .icon-outxx{
            float:right;
            width: 2.5rem;
            height:2.5rem;
            display:inline-block;
            background: url(__PUBLIC__/Admin_v3/images/out.png) no-repeat;
            background-size:1.5rem 1.5rem;
            background-position:.5rem .5rem;
            margin:.3rem .5rem 0 1.2rem; 
            text-align: center;
        }
    </style>
    <script type="text/javascript">
       var photoCAt = '__APP__/Api/photo/cat';
       var GROUP = '__GROUP__';
       var app = '__APP__';
       var Public = '__PUBLIC__';
    </script>
    <link rel="stylesheet" href="__PUBLIC__/Admin_v3/css/index/index.css">
  </head>
    <style>
      .home-contentbgbg515{
        width: 100%;
        height: 27%;
        background-image:url(__PUBLIC__/Admin_v3/images/logo515.png);
        background-repeat:no-repeat; 
        background-size:100% 100%;
        -moz-background-size:100% 100%;
        z-index: 1000;
      }
    </style>
  <body >
    <div class="page">
      <div class="content">
 <!--                     <div class="home-content">
 <div class="home-contentbgbg515top"></div>
            <div class="home-contentbgbg515"></div>
            <div class="home-contentbgbg515bottom"></div>
            <div class="ig"></div> -->
            <ul class="home-content-UL">
              <li><a href="__GROUP__/AboutUs/index?type=1" class="external">ABOUT</a></li>
              <li><a href="__GROUP__/Image/index" class="external">IMAGE</a></li>
              <li><a href="__GROUP__/VideoList/index" class="external">VIDEO</a></li>
              <li><a href="__GROUP__/Rental/title_rental" class="external">RENTAL</a></li>
              <li><a href="__GROUP__/Backstage/backstage_cat" class="external">BACKSTAGE</a></li>
              <li><a href="__GROUP__/Links/index" class="external">LINKS</a></li>
            </ul>
          </div>
        </div>
      </div>
  
    <script type='text/javascript' src='__PUBLIC__/Admin_v3/js/jquery-2.1.1.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='__PUBLIC__/Admin_v3/js/light7.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='__PUBLIC__/Admin_v3/js/light7-swiper.min.js' charset='utf-8'></script>
     <script type='text/javascript'>
           $(function(){
                $('.home-content').height($(window).height());
                $('.home-content').width($(window).width());
            });
     </script>

  </body>
</html>