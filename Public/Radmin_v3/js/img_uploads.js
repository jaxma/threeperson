
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
 layui.use('upload', function() {
  var flag = false;
  var $ = layui.jquery,
    upload = layui.upload;
  upload.render({
    elem: '#uploads_btn',
    url: URL + '/upload/',
    multiple: true,
    method: 'post',
    size: 3072000,
    accept: 'images',
    xhr:xhrOnProgress,
    progress:function(value){//上传进度回调 value进度值
        element.progress('demo', value+'%');//设置页面进度条
    },
    auto:true,
    data: {
      upload_dir_name: upload_dir_name
    },
    before: function(obj) {
      debugger
      //预读本地文件示例，不支持ie8
      obj.preview(function(index, file, result) {
        $('.layui-upload-lists').append('<li class="img-item"><img src="' + result + '" alt="' + file.name + '" class="layui-upload-img"><i class="layui-icon delete">&#xe640;</i><input type="hidden" name="'+image_name+'" class="imgUrl"></li>')
        flag = true
      });
    },
    done: function(res) {
      debugger
      //上传完毕
      if(res.code > 0) {
        
        return layer.msg('上传失败');
      }
      //上传成功
      layer.closeAll('loading'); //关闭loading
      layer.msg(res.msg);
      var timer = setInterval(function(){if(flag){$('.img-item:last').children('.imgUrl').val(res.src);flag = false; clearInterval(timer)}},100);
    }
  });
})



$(function() {
if(!!imgList&&img_show==1){

      $.each(imgList2,function(key,value){
        $('.layui-upload-lists').append('<li ondrop="drop(event,this)" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event, this)"><img src="' + value + '" class="layui-upload-img"><i class="layui-icon delete">&#xe640;</i><input type="hidden" class="imgUrl" name="'+image_name+'" value="'+imgList[key]+'"></li>')
    });

}
//点击删除图片
$(document).on('click', '.delete', function() {
    $(this).parent().remove();
});

});

//拖拽代码
function allowDrop(ev) {
  ev.preventDefault();
}

var srcdiv = null;

function drag(ev, divdom) {
  srcdiv = divdom;
  ev.dataTransfer.setData("text/html", divdom.innerHTML);
}

function drop(ev, divdom) {
  ev.preventDefault();
  if (srcdiv != divdom) {
    srcdiv.innerHTML = divdom.innerHTML;
    divdom.innerHTML = ev.dataTransfer.getData("text/html");
  }
}