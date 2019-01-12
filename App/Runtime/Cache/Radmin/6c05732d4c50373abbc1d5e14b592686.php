<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    .layui-form-item span {
        line-height: 38px !important;
    }
    /*小屏幕样式*/
    @media screen and (max-width: 768px) {
        .edit-wrapper .layui-content .layui-form .items .form-text {
            margin-left: 10px;
        }
    }
</style>
<script>
    /**
     * 使用图片上传接口需满足以下条件
     * 1.容器选择器id=upload
     * 2.设置name=image的input标签隐藏域
     * 3.指定上传目录名称
     */
        //上传目录
    var upload_dir_name = 'templet';
</script>

<div class="container-fluid edit-wrapper layui-container">

    <div class="layui-content">
        <form class="layui-form layui-box " style='padding:25px 30px 20px 0' action="__URL__/category_insert"
              data-auto="false" method="post">
            <div class="layui-form-item items">
                <label class="form-text">上级名称</label>
                <div class="form-right">
                    <div class="layui-input-inline three-select">
                        <select class="select-hook" name="pid1" id="lv_one" lay-filter="lv_one" lay-verify="required"
                                lay-search="">
                            <option value="a">请选择</option>
                        </select>
                    </div>
                    <div class="layui-input-inline three-select">
                        <select class="select-hook" name="pid2" id="lv_two" lay-verify="required" lay-search="">
                            <option value="a">请选择</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="layui-form-item items">
                <label class="form-text">分类名称（中文）</label>
                <div class="form-right">
                    <input type="text" name="name" required="" title="请输入名称" placeholder="请输入名称" class="input-inf2">
                </div>
            </div>
            <div class="layui-form-item items">
                <label class="form-text">分类名称（英文）</label>
                <div class="form-right">
                    <input type="text" name="name_en" required="" title="请输入英文名称" placeholder="请输入英文名称" class="input-inf2">
                </div>
            </div>

            <div class="layui-form-item items">
                <label class="form-text">优先级</label>
                <div class="form-right">
                    <input type="text" name="sequence" required="" title="请输入数字" placeholder="请输入数字" class="input-inf2"
                           value="0">
                    <i class="fa fa-question-circle-o question" data-tips-text="默认值为0，数字越大，优先级越高，最大值为9999"></i>
                </div>
            </div>
<!--             <div class="layui-form-item items">
                <label class="form-text">图片：</label>
                <div class="form-right">
                    <script src="__PUBLIC__/Radmin_v3/js/img_upload.js"></script>
                    
                    <small class="orange-text">（*请上传正方形的图片 图片大小为：188*188）</small>
                </div>
            </div>
 -->
            <div class="layui-form-item text-center">
                <button class="layui-btn" lay-submit lay-filter="sub">提交</button>
                <button class="layui-btn layui-btn-danger" type='button' data-confirm="确定要取消吗？" data-close>取消</button>
            </div>
            <input type="hidden" name="c_id" id="c_id" value="<?php echo ($c_id); ?>"/>
            <input type="hidden" name="p_id" id="p_id" value="<?php echo ($p_id); ?>"/>
        </form>
    </div>
</div>
<script type="text/javascript">
    var check_level = '__GROUP__/photo/get_category_ajax'
    var get_level = '__GROUP__/photo/templet_category'
    var get_son = '__GROUP__/photo/get_son_templet_category';

    var c_id = $('#c_id').val();
    var p_id = $('#p_id').val();

    $(function () {
        $.get(get_level, function (data) {
            if (data.code == 1) {
                $.each(data.info, function (key, value) {
                    var temp = new Array();
                    if (key != 'one'||value==null) {
                        return
                    }
                    var aim = $('#lv_one');
                    $.each(value, function (k, val) {
                        var html = '';
                        html = '<option value="' + val.id + '">' + val.name + '</option>';
                        temp.push(html)
                        if (p_id == val.id) {
                            aim.attr('disabled', 'disabled')
                            $('#lv_two').attr('disabled', 'disabled')
                            return;
                        } else if (p_id == 0 && p_id != "") {
                            aim.attr('disabled', 'disabled')
                            $('#lv_two').attr('disabled', 'disabled')
                            html = ''
                            return;
                        }
                    });
                    if (aim != '') {
                        aim.append(temp)
                        aim.val(p_id)
                    }
                });
                $.post(check_level, {
                    id: c_id
                }, function (data) {
                    if (data.info_one == null && data.info_two == null) {
                        return
                    }
                    if (data.info_one != null) {
                        getTwo(data.info_one.id);
                    } else {
                        $('#lv_one').empty().append('<option value="a">请选择</option>').val('a')
                    }
                    if (data.info_two != null) {
                        $('#lv_two').val(data.info_two.id);
                    } else if (c_id != "" && c_id != 0) {
                        $('#lv_two').val(c_id)
                    } else {
                        $('#lv_two').val('a')
                    }
                    form.render('select')
                });
            } else {
                layer.msg(data.msg);
            }
            form.render('select')
        });
        form.on('select(lv_one)', function (data) {
            var p_id = data.value;
            if (p_id != 'a') {
                getTwo(p_id)
            } else {
                $('#lv_two').empty().append('<option value="a">请选择</option>')
            }
        });
        form.on('submit(sub)', function (data) {
            $('#lv_one').removeAttr('disabled')
            $('#lv_two').removeAttr('disabled')
            form.render();
        })
    })

    function getTwo(p_id) {
        $.ajax({
            url: get_son,
            data: {
                pid: p_id
            },
            async: false,
            success: function (data) {
                if (data.code == 1) {
                    var aim = $('#lv_two')
                    var temp = []
                    aim.empty().append('<option value="a">请选择</option>')
                    $.each(data.info, function (key, value) {
                        if (key != 'two') {
                            return
                        }
                        if (value == null) {
                            return
                        }
                        $.each(value, function (k, val) {
                            var html = '';
                            html = '<option value="' + val.id + '">' + val.name + '</option>';
                            temp.push(html)
                            if (c_id == val.id && c_id != "") {
                                return
                            }
                        });
                        if (aim != '') {
                            aim.append(temp)
                            aim.val(c_id)
                            form.render('select')
                        }
                    });
                } else {
                    layer.msg(data.msg)
                }
            }
        });

    }
</script>