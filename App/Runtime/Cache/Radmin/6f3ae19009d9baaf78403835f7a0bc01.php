<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
<link href="//vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">
<script src="//vjs.zencdn.net/5.19/video.min.js"></script>
<style>
.video-js{
    display: inline-block;
}
</style>
</head>

<body>
<section class="layui-container">
    <div class="content">
        <header class="header">
            <h3>视频文件夹</h3>
            <input type="button"	class="layui-btn layui-btn-radius layui-btn-normal" data-open="__URL__/add" value="添加"/>
        </header>

        <div class="table-wrapper">
            <table class="layui-table" lay-skin="row" lay-size="sm">
                <thead>
                <tr>
                    <th>序号<button value='123' onclick="sc('me','index')"></button></th>
                    <th>标题</th>
                    <th>文件夹路径</th>
                    <th>视频</th>
                    <!-- <th>添加时间</th> -->
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($allpath)): ?><tr style="position: relative;">
                        <td style="position: absolute;width: 97%;border-top: solid 1px #e6e6e6;">对不起,没有找到数据!</td>
                    </tr>
                    <?php else: ?>
                    <!--<tr><td colspan="10"><div class="pull-right"><?php echo ($page); ?></div></td></tr>-->
                    <?php if(is_array($allpath)): $i = 0; $__LIST__ = $allpath;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><tr>
                            <?php if($m["des"] != ''): ?><td><?php echo ($m["des"]["id"]); ?></td>
                            <?php else: ?>
                                <td><span style="color:red;">无效视频</span></td><?php endif; ?>
                            <?php if($m["des"] != ''): ?><td><?php echo ($m["des"]["name"]); ?></td>
                            <?php else: ?>
                                <td><span style="color:red;">无效视频</span></td><?php endif; ?>
                            <td><?php echo ($m["path"]); ?></td>
                            <td>
                                <!-- <img src="__ROOT__<?php echo ($m["video"]); ?>" alt="" style="width:80px;"> -->
                            <video  controls="controls" height="150px" width="300px" class="video-js vjs-default-skin">
                                <source src="__ROOT__<?php echo ($m["real_path"]); ?>">
                            </video>
                            <!--<td><?php echo ($m["news"]); ?></td>-->
                            <!-- <td><?php echo (date("Y-m-d H:i",$m["time"])); ?></td> -->
                            <td>
                                <!-- <input type="button" class="layui-btn layui-btn-radius layui-btn-normal" value="编辑" data-open="__URL__/edit?id=<?php echo ($m["id"]); ?>"/> -->
                                <input type="button" class="layui-btn layui-btn-radius layui-btn-danger" value="删除"  data-load='__URL__/del_video_file?path=<?php echo ($m["real_path"]); ?>' data-confirm="您确定要删除'<?php echo ($m["real_path"]); ?>'吗？"/>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    
                    <!-- <div id="page"></div> -->
                    <!--<tr><td colspan="14"><div class="pull-right"><?php echo ($page); ?></div></td></tr>--><?php endif; ?>
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
</body>

</html>