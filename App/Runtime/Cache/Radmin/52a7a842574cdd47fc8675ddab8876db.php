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
            <span class="title">公司介绍</span>
        </blockquote>
    </header>
    <div class="layui-content">
        <form class="layui-form layui-box" action="__URL__/insert" data-auto="false" method="post">
            
            <div class="layui-form-item items">
                <label class="form-text">公司名称：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="name" lay-verify="name" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["name"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">公司名称（英文）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="name_en" lay-verify="name_en" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["name_en"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">中国所在城市：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="city_cn" lay-verify="city_cn" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["city_cn"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">中国所在城市（英文）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="city_cn_en" lay-verify="city_cn_en" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["city_cn_en"]); ?>">
                </div>
            </div>

            <div class="layui-form-item items">
                <label class="form-text">美国所在城市：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="city_usa" lay-verify="city_usa" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["city_usa"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">美国所在城市（英文）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="city_usa_en" lay-verify="city_usa_en" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["city_usa_en"]); ?>">
                </div>
            </div>

            <div class="layui-form-item items">
                <label class="form-text">中国公司地址：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="address_cn" lay-verify="address_cn" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["address_cn"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">中国公司地址（英文）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="address_cn_en" lay-verify="address_cn_en" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["address_cn_en"]); ?>">
                </div>
            </div>

            <div class="layui-form-item items">
                <label class="form-text">美国公司地址：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="address_usa" lay-verify="address_usa" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["address_usa"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">美国公司地址（英文）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="address_usa_en" lay-verify="address_usa_en" autocomplete="off" title="请输入名称" placeholder="请输入名称" value="<?php echo ($company["address_usa_en"]); ?>">
                </div>
            </div>



            
            <div class="layui-form-item items">
                <label class="form-text">中国联系电话：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="tel_en" lay-verify="tel_en" autocomplete="off" title="请输入电话" placeholder="请输入电话" value="<?php echo ($company["tel_en"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">美国联系电话：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="tel_usa" lay-verify="tel_usa" autocomplete="off" title="请输入电话" placeholder="请输入电话" value="<?php echo ($company["tel_usa"]); ?>">
                </div>
            </div>
           <div class="layui-form-item items">
                <label class="form-text">中国信息（info）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="info_en" lay-verify="info_en" autocomplete="off" title="请输入信息" placeholder="请输入信息" value="<?php echo ($company["info_en"]); ?>">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">美国信息（info）：</label>
                <div class="form-right">
                    <input class="input-inf2" required="" type="text" name="info_uas" lay-verify="info_uas" autocomplete="off" title="请输入信息" placeholder="请输入信息" value="<?php echo ($company["info_uas"]); ?>">
                </div>
            </div>
            <div class="layui-form-item  items">
                <label class="form-text">是否开启</label>
                <div class="form-right">
                    <input style="display: inline-block;margin-bottom:6px;" type="radio" name="status" value="1" title="开启" id="copen" checked><label for = "copen">开启</label>
                    <input style="display: inline-block;margin-bottom:6px;margin-left: 8px;" type="radio" name="status" value="0" title="关闭" id="cclose"  ><label for = "cclose">关闭</label>
                </div>
            </div>
            <div class="layui-form-item  items">
                <label class="form-text">公司详情：</label>
                <div class="form-right">
                    <textarea id="editor" class="ueditors" name="content"><?php echo ($company["content"]); ?></textarea>
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
                    <textarea id="editor_en" class="ueditors" name="content_en"><?php echo ($company["content_en"]); ?></textarea>
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