<?php if (!defined('THINK_PATH')) exit();?><html>

  <head>
    <script>
      /**
       * 使用图片上传接口需满足以下条件
       * 1.容器选择器id=upload
       * 2.设置name=image的input标签隐藏域
       * 3.指定上传目录名称
       */
      //上传目录
      var upload_dir_name = 'photo';
    </script>
    <style>
        @media screen and (max-width: 768px) {
            .edit-wrapper .layui-content .layui-form .items .desc {
                position: static;
            }
        } 
        .edit-wrapper .layui-content .layui-form .layui-upload-lists{
          min-width: 610px;
          max-width: 350px;
          max-height: 226px;
        } 
        .edit-wrapper .layui-content .layui-form .items .form-right {
          min-width: 610px;
          max-width: 350px;
        }
/*        .layui-form-item .form-right input{
            margin-left: 15px;
            display: inline-block;
        }
        .layui-form-item .form-right label{
            margin-bottom: -8px;
            margin-left: 6px;
        }*/
    </style>
  </head>

  <body>

    <div class="container-fluid edit-wrapper">
      <header class="edit-title">
        <blockquote class="layui-elem-quote">
          <span class="title">添加新闻</span>
        </blockquote>
      </header>
      <div class="layui-content">
        <form class="layui-form layui-box" action="__URL__/insert" data-auto="false" method="post">
          
          <div class="layui-form-item items">
            <label class="form-text">主页标题：</label>
            <div class="form-right">
              <input class="input-inf2" required="" type="text" name="title" lay-verify="name" autocomplete="off" title="请输入标题" placeholder="请输入标题名称">
            </div>
          </div>
          <div class="layui-form-item items">
            <label class="form-text">主页标题（英文）：</label>
            <div class="form-right">
              <input class="input-inf2" required="" type="text" name="title_en" lay-verify="name" autocomplete="off" title="请输入标题" placeholder="请输入标题名称">
            </div>
          </div>

          <!--引入图片页面-->
          <div class="layui-form-item items">
            <label class="form-text label-required">主页图片：</label>
            <div class="form-right">
              <script src="__PUBLIC__/Radmin_v3/js/img_upload.js"></script>
              
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="0" data-url="__ROOT__">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image" value="" />
  </div>
</div>
              <!-- <br/> -->
              <!-- <small class="orange-text desc">(请上传正方型的图片 图片大小为：80*80-150*150 最合适80*80)</small> -->
            </div>
          </div>

          <div class="layui-form-item items">
            <label class="form-text">详情页标题：</label>
            <div class="form-right">
              <input class="input-inf2" required="" type="text" name="title" lay-verify="name" autocomplete="off" title="请输入标题" placeholder="请输入标题名称">
            </div>
          </div>
          <div class="layui-form-item items">
            <label class="form-text">详情页标题（英文）：</label>
            <div class="form-right">
              <input class="input-inf2" required="" type="text" name="title_en" lay-verify="name" autocomplete="off" title="请输入标题" placeholder="请输入标题名称">
            </div>
          </div>

          <!--引入图片页面-->
          <div class="layui-form-item items">
            <label class="form-text label-required">详情页主图：</label>
            <div class="form-right">
              <script src="__PUBLIC__/Radmin_v3/js/img_upload.js"></script>
              
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="0" data-url="__ROOT__">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image" value="" />
  </div>
</div>
              <!-- <br/> -->
              <!-- <small class="orange-text desc">(请上传正方型的图片 图片大小为：80*80-150*150 最合适80*80)</small> -->
            </div>
          </div>

          <div class="layui-form-item  items">
            <label class="form-text">内容描述</label>
            <div class="form-right">
              <textarea id="editor" class="ueditors" name="content"></textarea>
              <!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>文本编辑器</title>
    <script type="text/javascript" charset="UTF-8">
      window.UEDITOR_HOME_URL = "__PUBLIC__/Radmin_v3/plugs/ueditor/"; //编辑器项目路径
    </script>
  </head>

  <body>
    <script type="text/javascript">
      require(['ZeroClipboard','ueditor.config', 'ueditor.all', 'zh-cn'], function(ZeroClipboard) {
        window['ZeroClipboard'] = ZeroClipboard;
        $('.ueditors').each(function(key,value){
          UE.delEditor($(value).attr("id"));
          var ue = UE.getEditor($(value).attr("id"),{initialFrameWidth:'100%',initialFrameHeight:350});
        })
      })
    </script>
  </body>


</html>
            </div>
          </div>
          <div class="layui-form-item  items">
            <label class="form-text">内容描述（英文）</label>
            <div class="form-right">
              <textarea id="editor2" class="ueditors" name="content_en"></textarea>
              <!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>文本编辑器</title>
    <script type="text/javascript" charset="UTF-8">
      window.UEDITOR_HOME_URL = "__PUBLIC__/Radmin_v3/plugs/ueditor/"; //编辑器项目路径
    </script>
  </head>

  <body>
    <script type="text/javascript">
      require(['ZeroClipboard','ueditor.config', 'ueditor.all', 'zh-cn'], function(ZeroClipboard) {
        window['ZeroClipboard'] = ZeroClipboard;
        $('.ueditors').each(function(key,value){
          UE.delEditor($(value).attr("id"));
          var ue = UE.getEditor($(value).attr("id"),{initialFrameWidth:'100%',initialFrameHeight:350});
        })
      })
    </script>
  </body>


</html>
            </div>
          </div>
        <div class="layui-form-item  items">
          <label class="form-text">是否开启</label>
          <div class="form-right">
                  <input type="radio" name="isopen" value="1" title="开启" checked>
                  <input type="radio" name="isopen" value="0" title="关闭" >
          </div>
        </div>
        <div class="layui-form-item  items">
            <label class="form-text">优先级</label>
            <div class="form-right">
                <input class="input-inf2" style="max-width: 190px;" type="number" name="sequence" lay-verify="sequence" autocomplete="off" placeholder="请输入优先级" class="layui-input" required="" title="请输入优先级" value="">
                <i class="fa fa-question-circle-o question" data-tips-text="默认为0，数字越大，优先级越高"></i>
            </div>
        </div>
            <div class="layui-form-item items">
                <label class="form-text">详情：</label>
                <div class="form-right">
                    <!--引入图片页面-->
                    <!--多图上传-->
<script type="text/javascript">

	var imgList = '[row_image]';
	imgList = imgList.split(',');
	var imgList2 = '[row_arr]';
    imgList2 = imgList2.split(',');
	var img_show = '[is_show]';
	var image_name = 'many_image[]';
</script>
<div class="layui-upload">
  <button type="button" class="layui-btn" id="uploads_btn">多图片上传</button>
  <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
    预览图：
    <ul class="layui-upload-lists"></ul>
  </blockquote>
</div>
<script src="__PUBLIC__/Radmin_v3/js/img_uploads.js" type="text/javascript" charset="utf-8"></script>
                </div>
            </div>
          <div class="layui-form-item  items">
            <label class="form-text"></label>
            <div class="form-right">
              <button class="layui-btn" type='submit'>立即提交</button>
            </div>
          </div>
        </form>
      </div>
    </div>
<script>
      
  </script>
  </body>
</html>