<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
</head>

<body>
    <section class="layui-container">
        <div class="content">
            <header class="header">
                <h3>管理员管理</h3>
                <div class="pull-right">
                <input type="button" class="layui-btn" data-modal="__GROUP__/admin/add_admin" value="添加" data-title="添加管理员"/>
              </div>
            </header>
<!--            <form class="layui-form form layui-row form-search" action="__SELF__" method="get" onsubmit="return false">
                <div class="layui-form-item form-item">
                    <div class="select-wrapper">
                        <label class="layui-form-label" for="aid"></label>
                        <select name="aid" id="aid" lay-verify="required" lay-search="">
                            <option value="">管理员</option>
                            <?php if(is_array($admin_info)): foreach($admin_info as $key_a=>$vo_a): ?><option value="<?php echo ($vo_a["id"]); ?>"><?php echo ($vo_a["username"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="search-btn">
                    <button type="submit" class="layui-btn layui-btn-radius">搜索</button>
                </div>
            </form>-->
            
            <div class="table-wrapper">
                <table class="layui-table" lay-skin="row" lay-size="sm">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>管理员</th>
                            <th>电话号码</th>
                            <th>邮箱</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($admin_info)): ?><tr style="position: relative;">
                                <td style="position: absolute;width: 97%;border-top: solid 1px #e6e6e6;">对不起,暂无更多数据!</td>
                            </tr>
                            <?php else: ?>
                            <?php if(is_array($admin_info)): $i = 0; $__LIST__ = $admin_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($m["id"]); ?></td>
                                    <td><?php echo ($m["username"]); ?></td>
                                    <td><?php echo ($m["phone"]); ?></td>
                                    <td><?php echo ($m["email"]); ?></td>
                                    <td class="operate">
                                        <span data-tips-text="权限分配" data-modal="__GROUP__/admin/auth?id=<?PHP echo $m['id'] ?>" data-title='编辑管理员权限'></span>
                                        <span data-tips-text="修改密码" data-modal="__GROUP__/admin/edit_admin?id=<?PHP echo $m['id'] ?>" data-title='修改密码'></span>
                                        <span data-tips-text="修改资料" data-modal="__GROUP__/admin/edit_message?id=<?PHP echo $m['id'] ?>" data-title='修改资料'></span>
                                        <span data-tips-text="删除管理员" data-title='删除管理员' onclick="return adminDelete(<?PHP echo $m['id'] ?>)"></span>
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

            function adminDelete(id) {
                layer.confirm('确定删除该管理员？', function(){
                    $.ajax({
                        url: '__GROUP__/admin/delete_admin',
                        data: {
                            id: id
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function(data){
                            console.log(data);
                            if (data.code == 1) {
                                layer.msg(data.info);
                            } else {
                                layer.msg(data.info);
                            }

                            setTimeout(function () {
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.location.reload();
                            }, 2000)
                        }
                    })
                })
            }
        </script>
    </section>
</body>

</html>