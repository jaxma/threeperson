var count = 1;
var first = true;
$.each(set_config["rebatekind"], function (key, value) {
    if (key == "OPEN") {
        return;
    }
    if (value && first) {
        $('#rebate' + count).addClass("active in");
        $('a[href=#rebate' + count + ']').parent().addClass("active")
        first = !first;
    }
    count++;

});

layui.use('table', function () {
    var table = layui.table;
})
var holder = '百分比';
form.render();

//平级订单返利设置---------------------------------------开始
//开关
form.on('switch(open_rb)', function (data) {
    if (data.elem.checked) {
//          $('#rebate1').find('select').prop('disabled', false);
//          $('#rebate1').find('input').prop('disabled', false);
//          $('#rebate1').find('checkbox').prop('disabled', false);
        data.elem.value=1;
    } else {
//          $('#rebate1').find('select').prop('disabled', true);
//          $('#rebate1').find('input').prop('disabled', true);
//          $('#rebate1').find('checkbox').prop('disabled', true);
//          $(this).attr('disabled', false);
        data.elem.value=0;
    }
    form.render();
})
//代理等级
form.on('checkbox(rb_level)', function (data) {
    var val = data.elem.value;
    var parent = $(this).data('parent');
    if (this.checked) {
        $(parent+' .set-wrapper').find('.level_sel'+val).fadeIn();
    } else {
        $(parent+' .set-wrapper').find('.level_sel'+val).fadeOut();
    }
    form.render();
})

//推荐等级选择
form.on('select(rb_sel)', function (data) {
    var num = data.value;
    var level = $(data.elem).data('level');
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param['+ level +'][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});

//奖励方式
form.on('radio(rb_ap)', function (data) {
    holder = '百分比';
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/金额/g, '百分比');
        $(value).attr('placeholder', str);
    });
    form.render();
})

form.on('radio(rb_am)', function (data) {
    holder = '金额';
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/百分比/g, '金额');
        $(value).attr('placeholder', str);
    });
    form.render();
})

//初始化平级订单返利设置
//开关

//if (!$("#rebate1").find("input[lay-skin='switch']").is(':checked')) {
//    $('#rebate1').find('select').prop('disabled', true);
//    $('#rebate1').find('input').prop('disabled', true);
//    $('#rebate1').find('checkbox').prop('disabled', true);
//    $("#rebate1").find("input[lay-skin='switch']").attr('disabled', false);
//    form.render();
//}
$('.set-wrapper').children('.items').hide();
var level_wrapper = $('.level-wrapper');
$.each(level_wrapper, function(key,value) {
    var wp_cb = $(value).find("input[type='checkbox']");
    $.each(wp_cb,function(k,v){
        if($(v).is(":checked")){
            var val = $(v).val();
            var parent = $(v).data('parent');
            $(parent+' .set-wrapper').children(".level_sel" + val).show();
        }
    });
});

if ($('#rebate1 #awardPercent').is(":checked")) {
    holder = "百分比";
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/金额/g, '百分比');
        $(value).attr('placeholder', str);
    });
}
if ($('#rebate1 #awardMoney').is(":checked")) {
    holder = "金额";
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/百分比/g, '金额');
        $(value).attr('placeholder', str);
    });
}



$.each($(".set-wrapper select"), function (key, value) {
//  console.log(key)
    var level = $(value).data('level');

    var info = $(value).data('info');
//  console.log(info)
    if ($(value).val() != ""&&set_config[info]!=null) {
        var num = Number($(value).children('option:selected').val());
//      console.log(num)
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
//          console.log(info)
//          console.log(set_config[info][level]["param"+i]);
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + set_config[info][level]["param"+i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
//      console.log(temp);
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});

//平级订单返利设置---------------------结束

//平级推荐充值返利----------------------开始



//平级推荐充值返利---------------------结束

//低推高一次性返利设置---------------------------------------开始


//奖励方式


//低推高一次性返利设置---------------------结束

//团队奖励----------------------------------开始
var rb4_holder1 = "金额",
    rb4_holder2 = "比例";

function addGrad() {
    var html = '<tr><td><input type="text" name="achievement[]" class="tb_input" placeholder="请输入' + rb4_holder1 + '"/></td><td><input type="text" name="parameter[]" class="tb_input" placeholder="请输入' + rb4_holder2 + '"/></td></tr>';
    $("#table-grad tbody").append(html);
}

form.on('switch(rb4_open)', function (data) {
    if (data.elem.checked) {
//      $('#rebate4').find('select').prop('disabled', false);
//      $('#rebate4').find('input').prop('disabled', false);
//      $('#rebate4').find('checkbox').prop('disabled', false);
        $("#add-grad").bind('click', addGrad);
    } else {
//      $('#rebate4').find('select').prop('disabled', true);
//      $('#rebate4').find('input').prop('disabled', true);
//      $('#rebate4').find('checkbox').prop('disabled', true);
//      $(this).attr('disabled', false);
        $("#add-grad").off('click');
    }
    form.render();
})
form.on('radio(rb4_co)', function (data) {
    if (data.elem.checked) {
        $('#count-num').attr('disabled', false);
        form.render();
    }
})
form.on('radio(rb4_cc)', function (data) {
    if (data.elem.checked) {
        $('#count-num').attr('disabled', true);
        $('#count-money').click();
        rb4_holder1 = "金额";
        rb4_holder2 = "比例";
        $("#table-grad thead tr th:eq(0)").text(rb4_holder1);
        $("#table-grad thead tr th:eq(1)").text(rb4_holder2 + "%");
        $.each($("#table-grad tbody tr").find('td:eq(0)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入金额");
        })
        $.each($("#table-grad tbody tr").find('td:eq(1)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入比例");
        })
        form.render();
    }
})
form.on('radio(rb4_cm)', function (data) {
    if (data.elem.checked) {
        rb4_holder1 = "金额";
        rb4_holder2 = "比例";
        $("#table-grad thead tr th:eq(0)").text(rb4_holder1);
        $("#table-grad thead tr th:eq(1)").text(rb4_holder2 + "%");
        $.each($("#table-grad tbody tr").find('td:eq(0)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入金额");
        })
        $.each($("#table-grad tbody tr").find('td:eq(1)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入比例");
        })
    }
})
form.on('radio(rb4_cn)', function (data) {
    if (data.elem.checked) {
        rb4_holder1 = "数量";
        rb4_holder2 = "金额";
        $("#table-grad thead tr th:eq(0)").text(rb4_holder1);
        $("#table-grad thead tr th:eq(1)").text(rb4_holder2);
        $.each($("#table-grad tbody tr").find('td:eq(0)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入数量");
        })
        $.each($("#table-grad tbody tr").find('td:eq(1)'), function (key, value) {
            $(value).children('input').attr("placeholder", "请输入金额");
        })
    }
})
//添加梯度
$("#add-grad").bind('click', addGrad)
//删除梯度
$(document).on('click','.delete-grad',function(){
    $(this).parent().parent().remove();
});
//初始化团队奖励
if (!$("#rebate4").find("input[lay-skin='switch']").is(':checked')) {
//    $('#rebate4').find('select').prop('disabled', true);
//    $('#rebate4').find('input').prop('disabled', true);
//    $('#rebate4').find('checkbox').prop('disabled', true);
//  $("#rebate4").find("input[lay-skin='switch']").attr('disabled', false);
    form.render();
}
if ($("#count-order").is(":checked")) {
    $('#count-num').attr('disabled', false);
}
if ($('#count-coin').is(":checked")) {
    $('#count-num').attr('disabled', true);
}
if ($('#count-num').is(":checked")) {
    rb4_holder1 = "数量";
    rb4_holder2 = "金额";
    $("#table-grad thead tr th:eq(0)").text(rb4_holder1);
    $("#table-grad thead tr th:eq(1)").text(rb4_holder2);
    $.each($("#table-grad tbody tr").find('td:eq(0)'), function (key, value) {
        $(value).children('input').attr("placeholder", "请输入数量");
    })
    $.each($("#table-grad tbody tr").find('td:eq(1)'), function (key, value) {
        $(value).children('input').attr("placeholder", "请输入金额");
    })
}



//高推低一次性返利设置---------------------------------------开始

//初始化高发展低一次性返利设置

//高发展低一次性返利设置---------------------结束

//平级推荐一次性返利设置---------------------------------------开始

//平级发展返利设置---------------------结束
$(function(){
    // 初始化页面
    $('.nav-tabs li:first-child').addClass('active');
    var now_tab = $('.nav-tabs li:first-child a').attr('href');
    $(now_tab).addClass('active in');
})