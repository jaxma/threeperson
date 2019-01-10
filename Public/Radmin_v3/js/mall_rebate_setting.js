var count = 1;
var first = true;
$.each(rebatekind, function (key, value) {
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

//订单返利设置---------------------------------------开始
$('#rebate1 .set-wrapper').children('.items').hide()
//开关
form.on('switch(open_rb1)', function (data) {
    if (data.elem.checked) {
//          $('#rebate1').find('select').prop('disabled', false);
//          $('#rebate1').find('input').prop('disabled', false);
//          $('#rebate1').find('checkbox').prop('disabled', false);
        data.elem.val(1);
    } else {
//          $('#rebate1').find('select').prop('disabled', true);
//          $('#rebate1').find('input').prop('disabled', true);
//          $('#rebate1').find('checkbox').prop('disabled', true);
//          $(this).attr('disabled', false);
        data.elem.val(0);
    }
    form.render();
})
//代理等级
form.on('checkbox(rb1_level1)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel1').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel1').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level2)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel2').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel2').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level3)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel3').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel3').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level4)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel4').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel4').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level5)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel5').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel5').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level6)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel6').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel6').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level7)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel7').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel7').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb1_level8)', function (data) {
    if (this.checked) {
        $('#rebate1 .set-wrapper').find('.level_sel8').fadeIn();
    } else {
        $('#rebate1 .set-wrapper').find('.level_sel8').fadeOut();
    }
    form.render();
})
//推荐等级选择
form.on('select(rb1_sel1)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[1][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel2)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[2][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel3)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[3][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel4)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[4][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel5)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[5][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel6)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[6][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel7)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[7][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb1_sel8)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[8][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
//奖励方式
form.on('radio(rb1_ap)', function (data) {
    holder = '百分比';
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/金额/g, '百分比');
        $(value).attr('placeholder', str);
    });
})

form.on('radio(rb1_am)', function (data) {
    holder = '金额';
    $.each($('#rebate1 .set-wrapper').find(".inp input"), function (key, value) {
        var str = $(value).attr('placeholder');
        str = str.replace(/百分比/g, '金额');
        $(value).attr('placeholder', str);
    });
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
var rb1_lv = $('#rebate1 .level-wrapper').find("input[type='checkbox']");
$.each(rb1_lv, function (key, value) {
    if ($(value).is(":checked")) {
        var val = $(value).val();
        $('#rebate1 .set-wrapper').children(".level_sel" + val).show()
    }
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
$.each($("#rebate1 .set-wrapper select"), function (key, value) {
    var level = $(this).data('level');
    if ($(value).val() != "") {
        var num = $(value).val();
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + order_info[level]["param" + i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});
//平级订单返利设置---------------------结束
