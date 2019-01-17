<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
</head>

<body>
<section class="layui-container">
    <div class="content">
        <header class="header">
            <h3>项目列表</h3>
            <input type="button"    class="layui-btn layui-btn-radius layui-btn-normal" data-open="__URL__/add" value="添加"/>
        </header>
            <form class="layui-form form layui-row form-search" action="__SELF__" method="get" onsubmit="return false">
                <div class="layui-form-item form-item">
                    <div class="form-right">
                        <div class="layui-input-inline three-select">
                            <select name="category_id1" id="level_one" lay-verify="required" lay-filter="level_one" autocomplete="off">
                                <option value="">请选择</option>
                            </select>
                        </div>
                        <div class="layui-input-inline three-select">
                            <select name="category_id2" id="level_two" lay-verify="required" lay-filter="level_two" autocomplete="off">
                                <option value="">请选择</option>
                            </select>
                        </div>
                        <div class="search-btn">
                            <button type="submit" class="layui-btn layui-btn-radius">搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        <div class="table-wrapper">
            <table class="layui-table" lay-skin="row" lay-size="sm">
                <thead>
                <tr>
                    <th>序号<button value='123' onclick="sc('me','index')"></button></th>
                    <th>标题</th>
                    <th>分类</th>
                    <th>封面图片</th>
                    <th>详情页标题</th>
                    <th>详情页图片</th>
                    <th>是否开启</th>
                    <th>是否经典</th>
                    <th>优先级</th>
                    <th>发布时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($list)): ?><tr style="position: relative;">
                        <td style="position: absolute;width: 97%;border-top: solid 1px #e6e6e6;">对不起,没有找到数据!</td>
                    </tr>
                    <?php else: ?>
                    <!--<tr><td colspan="10"><div class="pull-right"><?php echo ($page); ?></div></td></tr>-->
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($m["id"]); ?></td>
                            <td><?php echo ($m["title"]); ?><br><?php echo ($m["title_en"]); ?></td>
                            <td><?php echo ($m["cat1"]); ?><br><?php echo ($m["cat2"]); ?></td>
                            <td>
                                <?php if($m["image"] != ''): ?><img src="__ROOT__<?php echo ($m["image"]); ?>" alt="" style="width:80px;"><?php endif; ?>
                            </td>
                            <td><?php echo ($m["title_news"]); ?><br><?php echo ($m["title_news_en"]); ?></td>
                            <td>
                            <?php if($m["image"] != ''): ?><img src="__ROOT__<?php echo ($m["image2"]); ?>" alt="" style="width:80px;"><?php endif; ?>
                            </td>
                            <td>
                            <?php if($m["isopen"] == 1): ?>开启<?php else: ?>关闭<?php endif; ?>
                            </td>
                            <td>
                            <?php if($m["classical"] == 1): ?>经典<?php else: ?>非经典<?php endif; ?>
                            </td>
                            <td><?php echo ($m["sequence"]); ?></td>
                            <!--<td><?php echo ($m["news"]); ?></td>-->
                            <td><?php echo (date("Y-m-d H:i",$m["publish_time"])); ?></td>
                            <td>
                                <input type="button" class="layui-btn layui-btn-radius layui-btn-normal" value="编辑" data-open="__URL__/edit?id=<?php echo ($m["id"]); ?>"/>
                                <input type="button" class="layui-btn layui-btn-radius layui-btn-danger" value="删除"  data-load='__URL__/delete?id=<?php echo ($m["id"]); ?>' data-confirm="您确定要删除'<?php echo ($m["name"]); ?>'吗？"/>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </tbody>
            </table>
        </div>
      <script>
    /**
     * 1.要使用搜索功能，则form必须要设置class="form-search",详情请参考listen.js
     * 2.要使用分页功能，只需要传三个参数即可
     */
    //总数
    var count = "<?php echo ($count); ?>";
    //当前页
    var p = "<?php echo ($p); ?>";
    //每页显示数量
    var limit = "<?php echo ($limit); ?>";
//      require(['page','url'])

    $(document).ready(function () {
        form.render();
    })
</script>

<div id="page"></div>

<script src="__PUBLIC__/Radmin_v3/js/url.js"></script>
<script src="__PUBLIC__/Radmin_v3/js/page.js"></script>

    </div>

</section>
<script>
      var get_level = '__GROUP__/photo/templet_category'
      var get_son = '__GROUP__/photo/get_son_templet_category';
      $(function() {
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
                  html = '<option value="' + val.id + '">' + val.name + '</option>';
                  temp.push(html)
                });
                if(aim != '') {
                  aim.append(temp)
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
            $('#level_two').empty().append('<option value="">请选择</option>')
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
              aim.empty().append('<option value="">请选择</option>')
              $('#level_three').empty().append('<option value="">请选择</option>')
              $.each(data.info, function(key, value) {
                if(key != 'two'||value == null) {
                  return
                }
                $.each(value, function(k, val) {
                  var html = '';
                  html = '<option value="' + val.id + '">' + val.name + '</option>';
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
  </script>
</body>

</html>