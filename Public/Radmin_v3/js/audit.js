/* 
 * 审核相关代码
 */

// 选择所有选项
$(function () {
    $('#checkAll').on('click', function () {
        if($(this).is(':checked')) {
            $('.checkItem input[name=mid]').prop('checked', true)
        } else {
            $('.checkItem input[name=mid]').prop('checked', false)
        }
    });
    
    //审核操作
    $("#audit").on('click', function() {
        var managers = document.getElementsByName("mid");
        var mids = "";
        var temp = [];
        for (var i = 0; i < managers.length; i++) {
            if (managers[i].checked) {
//                mids = mids + '_' + managers[i].value;
                temp[i] = managers[i].value;
            }
        }
        temp.sort();
        mids = temp.join('_');
        
        if (!mids) {
            layer.msg('请至少选择一条记录审核！');
            return;
        }
        mids = '_'+mids;
//        console.log(mids);
        var pass = $(this).data('status');
        var pid =$(this).data('id');
        var title = '"确定审核通过？"';
        if( pass == 8 ){
            title = '确定转回总部订单？';
        }
        if ($(this).attr('title') != undefined) {
            title = $(this).attr('title');
        }
        layer.confirm(title, function (index) {
            layer.close(index);
            layer.load();
            $.post(auditUrl, {
                mids: mids,
                pass: pass,
                pid:pid
            }, function (res) {
                layer.close(layer.load());
                if (res.code == 1) {
                    layer.msg(res.msg);
                    //假设这是iframe页
                } else {
                    layer.msg(res.msg);
                }
                setTimeout(function () {
                     var index = parent.layer.getFrameIndex(window.name);
                     parent.layer.close(index);
                   parent.location.reload();
                }, 2000)
            }, 'json');
        });
    });
    
    //审核操作
    $("#grab").on('click', function() {
        var managers = document.getElementsByName("mid");
        var mids = "";
        var temp = [];
        for (var i = 0; i < managers.length; i++) {
            if (managers[i].checked) {
//                mids = mids + '_' + managers[i].value;
                temp[i] = managers[i].value;
            }
        }
        temp.sort();
        mids = temp.join('_');
        
        if (!mids) {
            layer.msg('请至少选择一条记录审核！');
            return;
        }
        mids = '_'+mids;
//        console.log(mids);
        var pass = $(this).data('status');
        var pid =$(this).data('id');
        var title = '"确定转到抢单中心？"';
        if ($(this).attr('title') != undefined) {
            title = $(this).attr('title');
        }
        layer.confirm(title, function (index) {
            layer.close(index);
            layer.load();
            $.post(grabUrl, {
                mids: mids,
                pass: pass,
                pid:pid
            }, function (res) {
                layer.close(layer.load());
                if (res.code == 1) {
                    layer.msg(res.msg);
                    //假设这是iframe页
                } else {
                    layer.msg(res.msg);
                }
                setTimeout(function () {
                     var index = parent.layer.getFrameIndex(window.name);
                     parent.layer.close(index);
                   parent.location.reload();
                }, 2000)
            }, 'json');
        });
    });
    
    //发货操作
    $("#auditsend").on('click', function() {
        var managers = document.getElementsByName("mid");
        var mids = "";
        var temp = [];
        for (var i = 0; i < managers.length; i++) {
            if (managers[i].checked) {
//                mids = mids + '_' + managers[i].value;
                temp[i] = managers[i].value;
            }
        }
        temp.sort();
        mids = temp.join('_');
        
        if (!mids) {
            layer.msg('请至少选择一条记录审核！');
            return;
        }
        mids = '_'+mids;
//        console.log(mids);
        var pass = $(this).data('status');
        var pid =$(this).data('id');
        var title = '"确定审核通过？"';
        if ($(this).attr('title') != undefined) {
            title = $(this).attr('title');
        }
        layer.confirm(title, function (index) {
            layer.close(index);
            layer.load();
            $.post(auditsendUrl, {
                mids: mids,
                pass: pass,
                pid:pid
            }, function (res) {
                layer.close(layer.load());
                if (res.code == 1) {
                    layer.msg(res.msg);
                    //假设这是iframe页
                } else {
                    layer.msg(res.msg);
                }
                setTimeout(function () {
                     var index = parent.layer.getFrameIndex(window.name);
                     parent.layer.close(index);
                   parent.location.reload();
                }, 2000)
            }, 'json');
        });
    });
    
    //审核不通过
    $("#delete").on('click', function() {
        var managers = document.getElementsByName("mid");
        var mids = "";

        for (var i = 0; i < managers.length; i++) {
            if (managers[i].checked) {
                mids = mids + '_' + managers[i].value;
            }
        }
        if (mids == '') {
            layer.msg('请至少选择一条记录审核！');
            return;
        }
        var pass = $(this).data('status');

        layer.confirm('<span style="color:red;font-size=20px;font-weight:bold;"><i class="fa fa-warning"></i>确定要不通过审核吗？</span>', function (index) {
            layer.close(index);
            layer.load();
            $.post(delUrl, {
                mids: mids,
                pass: pass,
            }, function (res) {
                layer.close(layer.load());
                if (res.code == 1) {
                    layer.msg(res.msg);
                } else {
                    layer.msg(res.msg);
                }
                setTimeout(function () {
                     location.reload();
                }, 2000)
            }, 'json');
        });
    });
    
    //删除申请代理
    $("#delete-agent").on('click', function() {
        var managers = document.getElementsByName("mid");
        var mids = "";

        for (var i = 0; i < managers.length; i++) {
            if (managers[i].checked) {
                mids = mids + '_' + managers[i].value;
            }
        }
        if (mids == '') {
            layer.msg('请至少选择一条记录删除！');
            return;
        }
        var pid =$(this).data('id');
        layer.confirm("删除代理后将清空该代理的所有数据，如果该代理有下属，则默认归上级/推荐人所直接管理，确定要删除吗？", function (index) {
            layer.close(index);
            layer.load();
            $.post(delUrl, {
                mids: mids,
                pid:pid
            }, function (res) {
                layer.close(layer.load());
                if (res.code == 1) {
                    layer.msg(res.msg);
                } else {
                    layer.msg(res.msg);
                }
                setTimeout(function () {
                    location.reload();
                }, 2000)
            }, 'json');
        });
    });
});


