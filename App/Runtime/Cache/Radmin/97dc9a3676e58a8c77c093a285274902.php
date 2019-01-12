<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <style>
        .layui-input, 
        .layui-textarea{
            width: 500px;
            display: inline-block;
        }
        .layui-form-item {
            margin-top: 50px;
        }
        .layui-form-label {
            width: 110px;
            font-size: 18px;
        }
        .update_wrapper{
          background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="container-fluid edit-wrapper layui-container">
        <header class="edit-title">
            <blockquote class="layui-elem-quote">
                <span class="title">系统更新</span>
            </blockquote>
        </header>
        <div class="update_wrapper layui-container">
        <form class="layui-form" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
            <div class="layui-form-item">
                <label class="layui-form-label">发布命令</label>
                <div class="layui-input-block">
                    <input type="text" class="layui-input" id="input-msg" name="SYSTEM_UPDATE">
                    <button class="layui-btn" lay-submit style="margin-left: 50px;" id="submit-msg">提交</button>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <div class="layui-input-block">
                    <textarea name="desc" placeholder="输出返回" class="layui-textarea" id="show-msg" disabled></textarea>
                </div>
            </div>
        </form>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" id="updata">开始更新</button>
            </div>
        </div></div>
    </div>
    <script>
        $(document).ready(function(){
            var websetUrl = '__GROUP__/Webset/get_webconfig';
            var updateWebsetUrl = '__GROUP__/Webset/update_webset';
            var runReplaceUrl = '__GROUP__/Webset/run_replace_ajax';
            form.render(); // 初始化表单

            //  提交命令
            // $('#submit-msg').click(function(){
            //     var sInputMsg = $('#input-msg').val();
            //     if(sInputMsg == '') {
            //         layer.alert('提交内容不能为空！');
            //         return false;
            //     } else {
            //         $.ajax({
            //             url: updateWebsetUrl,
            //             type: 'post',
            //             dataType: 'json',
            //             data: {
            //                 SYSTEM_UPDATE: sInputMsg
            //             },
            //             success: function(data){
            //                 layer.alert(data.msg);
            //             }
            //         })
            //     }
            //     return false;
            // })

            $.ajax({
                url: websetUrl,
                type: 'post',
                dataType: 'json',
                success: function(data){
                    console.log(data);
                    if(data.code == 1) {
                        $('#input-msg').val(data.config.SYSTEM_UPDATE);
                    }
                    
                }
            })

            $('#updata').click(function(){
                layer.load();// 加入加载状态
                $.ajax({
                    url: runReplaceUrl,
                    type: 'post',
                    dataType: 'json',
                    success: function(data){
                        if(data.code == 1) {
                            $('#show-msg').append(data.msg + '\n') // 展示清除缓存信息
                            layer.close(layer.load());// 删除加载状态
                        }
                    }
                })
                return false;
            })
        })
    </script>
</body>

</html>