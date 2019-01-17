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
    </style>
</head>

<body>

<div class="container-fluid edit-wrapper">
    <header class="edit-title">
        <blockquote class="layui-elem-quote">
            <span class="title">首页介绍</span>
        </blockquote>
    </header>
    <div class="layui-content">
        <form class="layui-form layui-box" action="__URL__/update" data-auto="false" method="post">
            
            <div class="layui-form-item items">
                <label class="form-text">公司名称：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="name" lay-verify="name" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($row["name"]); ?>">
                </div>
            </div>
            <div class="layui-form-item  items">
                <label class="form-text">是否开启</label>
                <div class="form-right">
                    <input style="display: inline-block;margin-bottom:6px;" type="radio" name="status" value="1" title="开启" id="copen" <?php echo $row['status']==1?'checked':'';?> ><label for = "copen">开启</label>
                    <input style="display: inline-block;margin-bottom:6px;margin-left: 8px;" type="radio" name="status" value="0" title="关闭" id="cclose" <?php echo $row['status']==0?'checked':'';?>  ><label for = "cclose">关闭</label>
                </div>
            </div>
            <div class="layui-form-item  items">
                <label class="form-text">公司详情：</label>
                <div class="form-right">
                    <textarea id="editor" class="ueditors" name="content"><?php echo ($row["content"]); ?></textarea>
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
                <label class="form-text">公司详情（英文）：</label>
                <div class="form-right">
                    <textarea id="editor_en" class="ueditors" name="content_en"><?php echo ($row["content_en"]); ?></textarea>
                </div>
            </div>
            <div class="layui-form-item  items">
                <label class="form-text"></label>
                <div class="form-right">
                    <input type="hidden" name="id" value="<?php echo ($id); ?>">
                    <button class="layui-btn" type='submit'>确定</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>

</html>