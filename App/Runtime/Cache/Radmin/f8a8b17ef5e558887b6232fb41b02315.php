<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
</head>

<body>
    <section class="layui-container">
        <div class="content">
            <header class="header">
                <h3>操作日志</h3>
            </header>
            <form class="layui-form form layui-row form-search" action="__SELF__" method="get" onsubmit="return false">
                <div class="layui-form-item form-item">
                    <div class="select-wrapper">
                        <label class="layui-form-label" for="aid">管理员</label>
                        <select name="aid" id="aid" lay-verify="required" lay-search="">
                            <option value="">管理员</option>
                            <?php if(is_array($admin_info)): foreach($admin_info as $key_a=>$vo_a): ?><option value="<?php echo ($vo_a["id"]); ?>"><?php echo ($vo_a["username"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item form-item">
                    <label class="layui-form-label" for="log">日志记录</label>
                    <div class="input-wrapper">
                        <input type="text" autocomplete="on" class="layui-input" id="log" name="log" placeholder="日志记录">
                    </div>
                </div>
                <div class="layui-form-item form-item">
                    <label class="layui-form-label" for="start_time">开始时间</label>
                    <div class="time-wrapper site-demo-laydate">
                        <input type="text" class="layui-input blue" name="start_time" id="start_time" readonly="readonly" placeholder="开始时间" autocomplete="on">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </div>
                <div class="layui-form-item form-item">
                    <label class="layui-form-label" for="end_time">结束时间</label>
                    <div class="time-wrapper site-demo-laydate">
                        <input type="text" class="layui-input blue" name="end_time" id="end_time" readonly="readonly" placeholder="结束时间" autocomplete="on">
                        <i class="fa fa-angle-down"></i>
                    </div>
                </div>
                <div class="search-btn">
                    <button type="submit" class="layui-btn layui-btn-radius">搜索</button>
                </div>
            </form>
            <div class="table-wrapper">
                <table class="layui-table" lay-skin="row" lay-size="sm">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>管理员</th>
                            <th>日志记录</th>
                            <!--<th>操作链接</th>-->
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($list)): ?><tr style="position: relative;">
                                <td style="position: absolute;width: 97%;border-top: solid 1px #e6e6e6;">对不起,暂无更多数据!</td>
                            </tr>
                            <?php else: ?>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($m["id"]); ?></td>
                                    <td><?php echo ($m["username"]); ?></td>
                                    <td><?php echo ($m["log"]); ?></td>
                                    <!--<td><?php echo ($m["active_url"]); ?></td>-->
                                    <td><?php echo ($m["created_format"]); ?></td>
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
        <script>
            layui.use('laydate', function () {
                var laydate = layui.laydate;
                laydate.render({
                    elem: '#start_time'
                });
                laydate.render({
                    elem: '#end_time'
                });
            })
        </script>
    </section>
</body>

</html>