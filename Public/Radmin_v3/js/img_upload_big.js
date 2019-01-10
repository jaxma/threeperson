/*
 * 上传图片及编辑器上传图片js
 * add by zbs
 * create by 2017-10-23
 */
 //创建监听函数
 var xhrOnProgress=function(fun) {
    xhrOnProgress.onprogress = fun; //绑定监听
     //使用闭包实现监听绑
    return function() {
        //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
        var xhr = $.ajaxSettings.xhr();
         //判断监听函数是否为函数
          if (typeof xhrOnProgress.onprogress !== 'function')
               return xhr;
           //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
            if (xhrOnProgress.onprogress && xhr.upload) {
                  xhr.upload.onprogress = xhrOnProgress.onprogress;
            }
            return xhr;
     }
 }
layui.use(['upload'], function () {
    var upload = layui.upload;
    var urls = "";
    var elems = '.upload-btn-big';
    try{
    	urls = logo_upload;
    	elems+= rand;
    	alert(elems);
    }catch(e){
      console.log(e)
    	//TODO handle the exception
    }
//  console.log(urls)
    if(urls == ""||urls == undefined|| urls == null){
      urls = URL + '/upload/';
    }
    //执行实例
    var uploadInst = upload.render({
        elem: elems,
        url: urls,
        method: 'post',
        size: 3072,
        accept: 'images',
        xhr:xhrOnProgress,
        progress:function(value){//上传进度回调 value进度值
            element.progress('demo', value+'%');//设置页面进度条
        },
        data: {
            upload_dir_name: upload_dir_name
        },
        before: function (obj) {
            //预读本地文件示例，不支持ie8
            var item = this.item;
            obj.preview(function (index, file, result) {
                $(item).siblings('.layui-upload').find('.layui-upload-list').fadeIn().find('.layui-upload-img').attr('src', result); //图片链接（base64)
                $(item).siblings('.input-inf2').val(file.name)
            });
        },
        done: function (res, index, upload) {
            //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
            //如果上传失败
            console.info(res);
            if (res.code > 0) {
                return layer.msg('上传失败');
            }
            //上传成功
            layer.closeAll('loading'); //关闭loading
            layer.msg(res.msg);
            var item = this.item;
            $(item).siblings('.layui-upload').find('.image-name').val(res.src)
            //网站配置的额外配置方法
            try{
            	if(upload_done&&typeof(upload_done)=="function"){
            	  console.log(res.src)
            	  $(item).siblings('.layui-upload').find('.image-name').val(res.src)
            	  upload_done(res.src);
            	}else{
            	  console.log(1)
            	}
            }catch(e){
              console.log(e);
            	//TODO handle the exception
            }
        },
        error:function(index,upload){
            layer.msg("上传失败");
            layer.closeAll('loading');
        }
    });
});

//点击删除图片
$(function () {
    
    $('.layui-upload-list').each(function (key, value) {
        if ($(this).data('show') == 1) {
            $(this).fadeIn().find('.layui-upload-img').attr('src', $(this).data('url'))
        }
    });
    $(document).on('click', '.demoText', function () {
        if ($(this).find('.delete').length > 0) {
            $(this).siblings('img').attr('src', '').parent().hide().siblings('.image-name').val('');
        }
    });

});