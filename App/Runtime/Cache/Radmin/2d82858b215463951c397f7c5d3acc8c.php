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
			<span class="title">编辑列表</span>
		</blockquote>
	</header>
	<div class="layui-content">
		<form class="layui-form layui-box" action="__URL__/update" data-auto="false" method="post">
			<div class="layui-form-item items">
				<label class="form-text">标题名称：</label>
				<div class="form-right">
					<input class="input-inf2" required="" type="text" name="title" lay-verify="title" autocomplete="off" title="请输入名称" placeholder="请输入标题名称" value="<?php echo ($row["title"]); ?>">
					<input type="hidden" name="id" value="<?php echo ($id); ?>">
				</div>
			</div>
      <div class="layui-form-item items">
        <label class="form-text">标题（英文）：</label>
        <div class="form-right">
          <input class="input-inf2" required="" type="text" name="title_en" lay-verify="title_en" autocomplete="off" title="请输入名称" placeholder="请输入标题名称" value="<?php echo ($row["title_en"]); ?>">
        </div>
      </div>
			<div class="layui-form-item  items">
				<label class="form-text label-required">分类：</label>
				<div class="form-right">
					<div class="layui-input-inline three-select">
						<select name="category_id1" id="level_one" lay-verify="required" lay-filter="level_one" autocomplete="off">
							<option value="a">请选择</option>
						</select>
					</div>
					<div class="layui-input-inline three-select">
						<select name="category_id2" id="level_two" lay-verify="required" lay-filter="level_two" autocomplete="off">
							<option value="a">请选择</option>
						</select>
					</div>
				</div>
			</div>
      <div class="layui-form-item  items">
        <label class="form-text">是否开启</label>
        <div class="form-right">
                <input type="radio" name="isopen" value="1" title="开启" <?php echo $row['isopen']==1?'checked':'';?> >
                <input type="radio" name="isopen" value="0" title="关闭" <?php echo $row['isopen']==0?'checked':'';?> >
        </div>
      </div>
      <div class="layui-form-item  items">
        <label class="form-text">经典项目</label>
        <div class="form-right">
                <input type="radio" name="classical" value="1" title="是" <?php echo $row['classical']==1?'checked':'';?>>
                <input type="radio" name="classical" value="0" title="否" <?php echo $row['classical']==0?'checked':'';?>>
        </div>
      </div>
			<!--引入图片页面-->
			<div class="layui-form-item items">
				<label class="form-text label-required">首页封面图片：</label>
				<div class="form-right">
					<script src="__PUBLIC__/Radmin_v3/js/img_upload.js"></script>
					<?php if($row["image"] != ''): ?><!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" multiple="multiple" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="1" data-url="__ROOT__<?php echo ($row["image"]); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image" value="<?php echo ($row["image"]); ?>" />
  </div>
  <p style="color: red;">务必检查图片文件名称，错误列子：1..png，图片上传尽量清晰小。</p>
</div>
						<?php else: ?>
						
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" multiple="multiple" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="0" data-url="__ROOT__<?php echo ($row["image"]); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image" value="<?php echo ($row["image"]); ?>" />
  </div>
  <p style="color: red;">务必检查图片文件名称，错误列子：1..png，图片上传尽量清晰小。</p>
</div><?php endif; ?>
					<!-- <small class="orange-text desc">(请上传正方型的图片 图片大小为：80*80-150*150 最合适80*80)</small> -->
				</div>
			</div>

      <!--引入图片页面-->
      <div class="layui-form-item items">
        <label class="form-text">分享图片：</label>
        <div class="form-right">
          <?php if($row["image_icon"] != ''): ?><!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" multiple="multiple" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="1" data-url="__ROOT__<?php echo ($row["image_icon"]); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image_icon" value="<?php echo ($row["image_icon"]); ?>" />
    图片链接：<span><?php echo ($ym_domain); ?></span><span class="image-name2"><?php echo ($row["image_icon"]); ?></span>
  </div>
</div>
            <?php else: ?>
            
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" multiple="multiple" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="0" data-url="__ROOT__<?php echo ($row["image_icon"]); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image_icon" value="<?php echo ($row["image_icon"]); ?>" />
    图片链接：<span><?php echo ($ym_domain); ?></span><span class="image-name2"><?php echo ($row["image_icon"]); ?></span>
  </div>
</div><?php endif; ?>
          <!-- <small class="orange-text desc">(请上传正方型的图片 图片大小为：80*80-150*150 最合适80*80)</small> -->
        </div>
      </div>

        <?php if(is_array($icons)): foreach($icons as $k=>$i): ?><div class="layui-form-item  items">
                <?php if($k == 0): ?><label class="form-text">分享平台</label>
                <?php else: ?>
                <label class="form-text"></label><?php endif; ?>
                <!-- <div class="form-right">
                  <span class="layui-btn" data-modal="__GROUP__/item/edit_icon">编辑分享平台</span>
                </div>
     -->
                  <div class="form-right">
                  <!--   <label class="form-text"><?php echo ($i["title_en"]); ?>&nbsp;&nbsp;</label>
                    <textarea  name="item_icon[]" placeholder="平台链接"><?php echo ($i["url"]); ?></textarea>
                    <input type="hidden" class="layui-input input-inf2"  name="item_icon_id[]" lay-verify="" autocomplete="off" placeholder="平台链接" value="<?php echo ($i["id"]); ?>">
                    <div class="layui-form-item  items"> -->
                    <p><?php echo ($i["title_en"]); ?>/说明：<?php echo ($i["content"]); ?></p>
                    <textarea  name="item_icon[]" placeholder="平台链接"><?php echo ($i["url"]); ?></textarea>
                    <input type="hidden" class="layui-input input-inf2"  name="item_icon_id[]" lay-verify="" autocomplete="off" placeholder="分享关键词设置" value="<?php echo ($i["id"]); ?>">
                    <div class="layui-form-item items" style="display: none;">
                      <div class="form-right">
                        <label class="form-text">说明：&nbsp;&nbsp;</label>
                        <textarea><?php echo ($i["content"]); ?></textarea>
                      </div>
                    </div>
                  </div>
                  <br/>
          </div><?php endforeach; endif; ?>
      <div class="layui-form-item  items">
            <label class="form-text">发布时间</label>
            <div class="form-right">
              <input required="" type="text" class="layui-input input-inf2" id="publish_time" name="publish_time" lay-verify="" autocomplete="off" placeholder="发布时间" value="<?php echo (date("Y-m-d H:i",$row["publish_time"])); ?>">
            </div>
          </div>

      <div class="layui-form-item  items">
          <label class="form-text">优先级</label>
          <div class="form-right">
              <input class="input-inf2" style="max-width: 190px;" type="number" name="sequence" lay-verify="sequence" autocomplete="off" placeholder="请输入优先级" class="layui-input" required="" title="请输入优先级" value="<?php echo ($row["sequence"]); ?>">
              <i class="fa fa-question-circle-o question" data-tips-text="默认为0，数字越大，优先级越高"></i>
          </div>
      </div>

      <div class="layui-form-item items">
        <label class="form-text">设计内容：</label>
        <div class="form-right">
          <input class="input-inf2" required="" type="text" name="detial_title" lay-verify="detial_title" autocomplete="off" title="请输入名称" placeholder="请输入标题名称" value="<?php echo ($row["title_news"]); ?>">
        </div>
      </div>
      <div class="layui-form-item items">
        <label class="form-text">设计内容（英文）：</label>
        <div class="form-right">
          <input class="input-inf2" required="" type="text" name="detial_title_en" lay-verify="detial_title_en" autocomplete="off" title="请输入名称" placeholder="请输入标题名称" value="<?php echo ($row["title_news_en"]); ?>">
        </div>
      </div>
      <div class="layui-form-item items">
          <label class="form-text">地点;用地面积;类型等</label>
          <div class="form-right">
            <input class="input-inf2" type="text" name="detail" lay-verify="detail" autocomplete="off" title="请输入内容" placeholder="" value="<?php echo ($row["detail"]); ?>">
              <i class="fa fa-question-circle-o question" data-tips-text="请用;分割"></i>
          </div>
        </div>
      <div class="layui-form-item items">
        <label class="form-text">地点;用地面积;类型等（英文）：</label>
        <div class="form-right">
          <input class="input-inf2" required="" type="text" name="detail_en" lay-verify="detail_en" autocomplete="off" title="请输入内容" placeholder="" value="<?php echo ($row["detail_en"]); ?>">
              <i class="fa fa-question-circle-o question" data-tips-text="请用;分割"></i>
        </div>
      </div>

      <div class="layui-form-item items">
          <label class="form-text  label-required">详情页图片：</label>
          <div class="form-right">
              <!--引入图片页面-->
              <div class="imgs-wrapper">
                  <?php if($row["many_image"] != ''): ?><!--多图上传-->
<script type="text/javascript">

	var imgList = '<?php echo ($row["many_image"]); ?>';
	imgList = imgList.split(',');
	var imgList2 = '<?php echo ($arr); ?>';
    imgList2 = imgList2.split(',');
	var img_show = '1';
	var image_name = 'many_image[]';
</script>
<div class="layui-upload">
  <button type="button" class="layui-btn" id="uploads_btn">多图片上传</button>
  <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
    预览图：
    <ul class="layui-upload-lists"></ul>
  </blockquote>
  <p style="color: red;">务必检查图片文件名称，错误列子：1..png，图片上传尽量清晰小。</p>
</div>
<script src="__PUBLIC__/Radmin_v3/js/img_uploads.js" type="text/javascript" charset="utf-8"></script>
                      <?php else: ?>
                      <!--多图上传-->
<script type="text/javascript">

	var imgList = '<?php echo ($row["many_image"]); ?>';
	imgList = imgList.split(',');
	var imgList2 = '<?php echo ($arr); ?>';
    imgList2 = imgList2.split(',');
	var img_show = '0';
	var image_name = 'many_image[]';
</script>
<div class="layui-upload">
  <button type="button" class="layui-btn" id="uploads_btn">多图片上传</button>
  <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
    预览图：
    <ul class="layui-upload-lists"></ul>
  </blockquote>
  <p style="color: red;">务必检查图片文件名称，错误列子：1..png，图片上传尽量清晰小。</p>
</div>
<script src="__PUBLIC__/Radmin_v3/js/img_uploads.js" type="text/javascript" charset="utf-8"></script><?php endif; ?>
              </div>
          </div>
      </div>
      <div class="layui-form-item  items">
            <label class="form-text label-required">文章正文内容：</label>
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
            <label class="form-text label-required">文章正文内容（英文）：</label>
            <div class="form-right">
              <textarea id="editor_en" class="ueditors" name="content_en"><?php echo ($row["content_en"]); ?></textarea>
            </div>
          </div>

			<div class="layui-form-item  items">
				<label class="form-text"></label>
				<div class="form-right">
					<button class="layui-btn" type='submit'>确定</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
      var get_level = '__GROUP__/photo/templet_category'
      var get_son = '__GROUP__/photo/get_son_templet_category';
      $(function() {
        var c_id = getUrlParms('id');
        $.ajax({
          url: get_level,
          async: false,
          type: 'GET',
          success: function(data) {
            if(data.code == 1) {
              $.each(data.info, function(key, value) {
                var temp = new Array();
                if(key != 'one'||value==null) {
                  return
                }
                var aim = $('#level_one');
                $.each(value, function(k, val) {
                  var html = '';
                  console.log(val.id);
                  console.log('and');
                  console.log(<?php echo ($row["cat1"]); ?>);
                  if(val.id == <?php echo ($row["cat1"]); ?>){
                  	html = '<option selected="selected" value="' + val.id + '">' + val.name + '</option>';
                  }else{
                  	html = '<option value="' + val.id + '">' + val.name + '</option>';
                  }
                  temp.push(html)
                });
                if(aim != '') {
                  aim.append(temp)
                }
                if(<?php echo ($row["cat1"]); ?> && <?php echo ($row["cat1"]); ?> != 0){
            		getTwo(<?php echo ($row["cat1"]); ?>);
                }
                form.render();
              });
            	}else {
              layer.msg(data.msg);
            }
            form.render('select');
          }
        });
        form.on('select(level_one)', function(data) {
          var p_id = data.value;
          if(p_id != 'a') {
            getTwo(p_id)
          } else {
            $('#level_two').empty().append('<option value="a">请选择</option>')
          }
        });
      });

      function getTwo(p_id) {
        $.ajax({
          url: get_son,
          data: {
            pid: p_id
          },
          async: false,
          success: function(data) {
            if(data.code == 1) {
              var aim = $('#level_two')
              var temp = []
              aim.empty().append('<option value="a">请选择</option>')
              $('#level_three').empty().append('<option value="a">请选择</option>')
              $.each(data.info, function(key, value) {
                if(key != 'two'||value == null) {
                  return
                }
                $.each(value, function(k, val) {
                  var html = '';
                  if(val.id == <?php echo ($row["cat2"]); ?>){
                  	html = '<option selected="selected" value="' + val.id + '">' + val.name + '</option>';
                  }else{
                  	html = '<option value="' + val.id + '">' + val.name + '</option>';
                  }
                  temp.push(html)
                });
                if(aim != '') {
                  aim.append(temp)
                }
              });
            } else {
              layer.msg(data.msg)
            }
            form.render('select')
          }
        });
      }

      function getUrlParms(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var href = window.location.href;
        var r = window.location.href.substring(href.indexOf('?'), href.length).substr(1).match(reg);
        if(r != null)
          return unescape(r[2]);
        return null;
      }
  </script>
</body>

</html>