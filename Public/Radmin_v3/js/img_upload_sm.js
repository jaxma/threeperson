/*
 * 上传图片及编辑器上传图片js
 * add by zbs
 * create by 2017-10-23
 */
layui.use(['upload'], function () {
    var upload = layui.upload;
    //执行实例
    var uploadInst = upload.render({
        elem: '.imgbtn_upload',
        url: URL + '/upload/',
        method: 'post',
        size: 3072,
        accept: 'images',
        data: {
            upload_dir_name: upload_dir_name
        },
        before: function (obj) {
            //预读本地文件示例，不支持ie8
            var item = this.item;
            obj.preview(function (index, file, result) {
                $(item).attr('src', result); //图片链接（base64)
            });
        },
        done: function (res, index, upload) {
            //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
            //如果上传失败
            if (res.code > 0) {
                return layer.msg('上传失败');
            }
            //上传成功
            layer.closeAll('loading'); //关闭loading
            layer.msg(res.msg);
            var item = this.item;
            // $(item).siblings('.image-name').val(res.src)
            var key = $(item).parent().parent().parent().attr('key');
            if (!product.propertyPrices[key]) {
                product.propertyPrices[key] = {};
            }
            product.propertyPrices[key]['image'] = res.src;
        }
    });
});

//点击删除图片
//$(function () {
//  $('.layui-upload-list').each(function (key, value) {
//      if ($(this).data('show') == 1) {
//          $(this).fadeIn().find('.layui-upload-img').attr('src', $(this).data('url'))
//      }
//  });
//  $(document).on('click', '.demoText', function () {
//      if ($(this).find('.delete').length > 0) {
//          $(this).siblings('img').attr('src', '').parent().hide().siblings('.image-name').val('');
//      }
//  });
//
//});