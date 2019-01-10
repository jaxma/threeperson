/* 
 * 上传图片及编辑器上传图片js
 * add by zbs
 * create by 2017-10-23
 */

layui.use(['upload', 'form', 'layedit'], function() {
    var form = layui.form,
    $ = layui.jquery,
    layer = layui.layer,
    layedit = layui.layedit,
    upload = layui.upload;
    form.render();
    var uploadInst = upload.render({
    elem: '#upload-image',
    auto: true,
    url: 'upload/',
    method: 'post',
    size: 3072,
    accept: 'images',
    data: {upload_dir_name:upload_dir_name},

    before: function(obj) {
        //预读本地文件示例，不支持ie8
        obj.preview(function(index, file, result) {
          $('#show-image').attr('src', result).parent().fadeIn(); //图片链接（base64）
          $('#file-name').val(file.name);
        });
        layer.load();
    },
    done: function(res, index, upload){
        if(res.code == 0){
            $('input[name=image]').val(res.src);
        } else {

        }
        layer.closeAll('loading'); //关闭loading
        layer.msg(res.msg);
    }

//        error: function() {
//          //演示失败状态，并实现重传
//          var demoText = $('#demoText');
//          demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
//          demoText.find('.demo-reload').on('click', function() {
//            uploadInst.upload();
//          });
//        }
    });

    //创建一个编辑器
    var editIndex = layedit.build('LAY_demo_editor');

});
      
$(function(){
    $(document).on('click','#demoText',function(){
        if($(this).find('.delete').length>0){
            $(this).siblings('img').attr('src','').parent().hide();
        }
    });
});



