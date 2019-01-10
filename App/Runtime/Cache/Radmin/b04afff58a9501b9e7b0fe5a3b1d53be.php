<?php if (!defined('THINK_PATH')) exit();?><html>

<head>
    <style>
        @media screen and (max-width: 768px) {
            .edit-wrapper .layui-content .layui-form {
                padding: 15px 0;
            }
            .edit-wrapper .layui-content .layui-form .items {
                display: block;
            }
            .edit-wrapper .layui-content .layui-form .items .form-text {
                text-align: left;
                padding-left: 5%;
                float: none;
            }
            .edit-wrapper .layui-content .layui-form .items .form-right {
                float: none;
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
        var upload_dir_name = '';
    </script>
</head>

<body>
    <div class="container-fluid edit-wrapper layui-container">
        <header class="edit-title">
            <blockquote class="layui-elem-quote">
                <span class="title">编辑资料</span>
            </blockquote>
        </header>
        <div class="layui-content">
            <form class="layui-form layui-box" action="__URL__/update" data-auto="false" method="post">
                <div class="layui-form-item items layui-row">
                    <label class="form-text layui-col-xs12">用户名：</label>
                    <div class="form-right layui-col-xs12">
                        <input class="input-inf2 layui-input" value="<?php echo ($vo["username"]); ?>" required="" type="text" name="username" id="username" lay-verify="username"
                            autocomplete="off" title="请输入用户名" placeholder="请输入用户名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item  items layui-row">
                    <label class="form-text layui-col-xs12">手机号码：</label>
                    <div class="form-right layui-col-xs12">
                        <input class="input-inf2 layui-input" value="<?php echo ($vo["phone"]); ?>" type="number" name="phone" id="phone" lay-verify="phone" autocomplete="off"
                            placeholder="请输入手机号码">
                    </div>
                </div>

                <div class="layui-form-item  items layui-row">
                    <label class="form-text layui-col-xs12">邮箱：</label>
                    <div class="form-right layui-col-xs12">
                        <input class="input-inf2 layui-input" value="<?php echo ($vo["email"]); ?>" type="text" name="email" id="email" lay-verify="email" autocomplete="off"
                            placeholder="请输入邮箱地址">
                    </div>
                </div>
                <div class="layui-form-item  items layui-row">
                    <label class="form-text layui-col-xs12"></label>
                    <div class="form-right layui-col-xs12" style="margin-left: 10px;">
                        <input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
                        <input class="layui-btn" value="立即提交" type="submit">
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>