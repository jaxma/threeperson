<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        .layui-form {
            width: 720px;
        }
        .layui-form-label {
            width: 130px;
            font-size: 20px;
        }
        .layui-form-item {
            margin-top: 50px;
        }
        .layui-form-item span {
            line-height: 28px !important;
        }
        .layui-input-block {
            margin-left: 130px;
        }
        .layui-input-block span {
            display: inline-block;
            padding-right: 20px;
            vertical-align: bottom;
        }
        
    </style>
    <title>清除缓存</title>
</head>

<body>
    <div class="container-fluid edit-wrapper layui-container">
        <header class="edit-title">
            <blockquote class="layui-elem-quote">
                <span class="title">清除缓存</span>
            </blockquote>
        </header>
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">缓存选项：</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="Buffer[Cache]" title="Cache(缓存)" data-name="cache">
                    <span class="text1"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text2"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text3"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                </div>
                <div class="layui-input-block">
                    <input type="checkbox" name="Buffer[Data]" title="Data(数据)" data-name="data" checked>
                    <span class="text4"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text5"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text6"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                </div>
                <div class="layui-input-block">
                    <input type="checkbox" name="Buffer[Logs]" title="Logs(日志)" data-name="logs" checked>
                    <span class="text7"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text8"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text9"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                </div>
                <div class="layui-input-block">
                    <input type="checkbox" name="Buffer[Temp]" title="Temp(模版)" data-name="temp" checked>
                    <span class="text10"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text11"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                    <span class="text12"><i class="layui-icon layui-anim layui-anim-loop layui-anim-rotate" style="font-size: 30px; color: #5FB878;">&#xe63d;</i></span>
                </div>  
            </div>

            <div class="layui-form-item layui-form-text">
                <div class="layui-input-block">
                    <textarea name="desc" placeholder="输出返回" class="layui-textarea" id="show-msg" disabled></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" id="clear">开始清除</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function(){
            var getRuntimeInfoUrl = '__GROUP__/Webset/get_runtime_info';
            var clearCacheAjaxUrl = '__GROUP__/Webset/clear_cache_ajax';

            var oClearCache = { cache: 0, data: 1, logs: 1, temp: 1 }; // 缓存选项

            form.render(); // 初始化表单

            layer.load(); // 加入加载状态
            getCacheStatus(); // 获取缓存状态
            layer.close(layer.load()); // 删除加载状态

            form.on('checkbox', function(data){
                // console.log($(data.elem).data('index')); //得到checkbox原始DOM对象
                // console.log(data.elem.checked); //是否被选中，true或者false
                // console.log(data.value); //复选框value值，也可以通过data.elem.value得到
                // console.log(data.othis); //得到美化后的DOM对象
                switch($(data.elem).data('name')) {
                    case 'cache':
                        oClearCache.cache = data.elem.checked ? 1 : 0;
                        break;
                    case 'data':
                        oClearCache.data = data.elem.checked ? 1 : 0;
                        break;
                    case 'logs':
                        oClearCache.logs = data.elem.checked ? 1 : 0;
                        break;
                    case 'temp':
                        oClearCache.temp = data.elem.checked ? 1 : 0; 
                        break;
                }
                
            }); 
            
            // 开始清除缓存
            $('#clear').click(function(){
                layer.load();// 加入加载状态
                $.ajax({
                    url: clearCacheAjaxUrl,
                    type: 'post',
                    dataType: 'json',
                    data: oClearCache,
                    success: function(data){
                        if(data.code == 1) {
                            getCacheStatus();// 获取缓存状态
                            $('#show-msg').append(data.msg + '\n') // 展示清除缓存信息
                            layer.close(layer.load());// 删除加载状态
                        }
                    }
                })
                return false;
            })

            // 获取缓存状态
            function getCacheStatus() {
                $.ajax({
                    url: getRuntimeInfoUrl,
                    type: 'post',
                    dataType: 'json',
                    success: function(data){
                        if(data.code == 1) {
                            // console.log(data);
                            for(var k in data.info) {
                                switch(k) {
                                    case 'cache':
                                        $('.text1').text(data.info[k].file_exists ? '存在' : '不存在');
                                        $('.text2').text(data.info[k].is_writable ? '可读' : '不可读');
                                        $('.text3').text(data.info[k].size);
                                        break;
                                    case 'data':
                                        $('.text4').text(data.info[k].file_exists ? '存在' : '不存在');
                                        $('.text5').text(data.info[k].is_writable ? '可读' : '不可读');
                                        $('.text6').text(data.info[k].size);
                                        break;
                                    case 'logs':
                                        $('.text7').text(data.info[k].file_exists ? '存在' : '不存在');
                                        $('.text8').text(data.info[k].is_writable ? '可读' : '不可读');
                                        $('.text9').text(data.info[k].size);
                                        break;
                                    case 'temp':
                                        $('.text10').text(data.info[k].file_exists ? '存在' : '不存在');
                                        $('.text11').text(data.info[k].is_writable ? '可读' : '不可读');
                                        $('.text12').text(data.info[k].size);
                                        break;
                                }
                            }
                        }
                    }
                })
            }
        }) 
    </script>
</body>

</html>