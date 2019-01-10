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

//平级订单返利设置---------------------------------------开始
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

//平级推荐充值返利----------------------开始
$('#rebate2 .set-wrapper').children('.items').hide()
//开关
form.on('switch(rb2_open)', function (data) {
    if (data.elem.checked) {
//          $('#rebate2').find('select').prop('disabled', false);
//          $('#rebate2').find('input').prop('disabled', false);
//          $('#rebate2').find('checkbox').prop('disabled', false);
        data.elem.val(1);
    } else {
//          $('#rebate2').find('select').prop('disabled', true);
//          $('#rebate2').find('input').prop('disabled', true);
//          $('#rebate2').find('checkbox').prop('disabled', true);
//          $(this).attr('disabled', false);
        data.elem.val(0);
    }
    form.render();
})
//代理等级
form.on('checkbox(rb2_level1)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel1').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel1').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level2)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel2').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel2').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level3)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel3').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel3').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level4)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel4').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel4').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level5)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel5').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel5').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level6)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel6').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel6').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level7)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel7').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel7').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb2_level8)', function (data) {
    if (this.checked) {
        $('#rebate2 .set-wrapper').find('.level_sel8').fadeIn();
    } else {
        $('#rebate2 .set-wrapper').find('.level_sel8').fadeOut();
    }
    form.render();
})
//推荐等级选择
form.on('select(rb2_sel1)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[1][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel2)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[2][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel3)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[3][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel4)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[4][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel5)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[5][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel6)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[6][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel7)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[7][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
form.on('select(rb2_sel8)', function (data) {
    var num = data.value;
    var temp = new Array();
    var chinese = ['一', '二', '三'];
    for (var i = 1; i <= num; i++) {
        var html = '<div class="layui-input-inline inp"><input type="text" name="param[8][]" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
        temp.push(html);
    }
    $(data.elem).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
    $(data.elem).parent('.layui-input-inline').after(temp);
});
//初始化平级推荐充值返利
//      if(!$("#rebate2").find("input[lay-skin='switch']").is(':checked')) {
//        $('#rebate2').find('select').prop('disabled', true);
//        $('#rebate2').find('input').prop('disabled', true);
//        $('#rebate2').find('checkbox').prop('disabled', true);
//        $("#rebate2").find("input[lay-skin='switch']").attr('disabled', false);
//        form.render();
//      }

var rb2_lv = $('#rebate2 .level-wrapper').find("input[type='checkbox']");
$.each(rb2_lv, function (key, value) {
    if ($(value).is(":checked")) {
        var val = $(value).val();
        $('#rebate2 .set-wrapper').children(".level_sel" + val).show()
    }
});
$.each($("#rebate2 .set-wrapper select"), function (key, value) {
    var level = $(this).data('level');
    if ($(value).val() != "") {
        var num = $(value).val();
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + money_info[level]["param" + i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利百分比" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});
//平级推荐充值返利---------------------结束

//低推高一次性返利设置---------------------------------------开始
$('#rebate3 .set-wrapper').children('.items').hide()
//开关
form.on('switch(open_rb3)', function (data) {
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
form.on('checkbox(rb3_level1)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel1').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel1').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level2)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel2').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel2').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level3)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel3').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel3').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level4)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel4').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel4').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level5)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel5').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel5').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level6)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel6').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel6').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level7)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel7').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel7').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb3_level8)', function (data) {
    if (this.checked) {
        $('#rebate3 .set-wrapper').find('.level_sel8').fadeIn();
    } else {
        $('#rebate3 .set-wrapper').find('.level_sel8').fadeOut();
    }
    form.render();
})
//推荐等级选择
form.on('select(rb3_sel1)', function (data) {
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
form.on('select(rb3_sel2)', function (data) {
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
form.on('select(rb3_sel3)', function (data) {
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
form.on('select(rb3_sel4)', function (data) {
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
form.on('select(rb3_sel5)', function (data) {
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
form.on('select(rb3_sel6)', function (data) {
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
form.on('select(rb3_sel7)', function (data) {
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
form.on('select(rb3_sel8)', function (data) {
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
//      form.on('radio(rb3_ap)', function(data) {
//          holder = '百分比';
//          $.each($('#rebate3 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      })
//
//      form.on('radio(rb3_am)', function(data) {
//          holder = '金额';
//          $.each($('#rebate3 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      })

//      if(!$("#rebate3").find("input[lay-skin='switch']").is(':checked')) {
//      }
var rb3_lv = $('#rebate3 .level-wrapper').find("input[type='checkbox']");
$.each(rb3_lv, function (key, value) {
    if ($(value).is(":checked")) {
        var val = $(value).val();
        $('#rebate3 .set-wrapper').children(".level_sel" + val).show()
    }
});
//      if($('#rebate3 #awardPercent').is(":checked")) {
//          holder = "百分比";
//          $.each($('#rebate3 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      }
//      if($('#rebate3 #awardMoney').is(":checked")) {
//          holder = "金额";
//          $.each($('#rebate3 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      }
$.each($("#rebate3 .set-wrapper select"), function (key, value) {
    holder = '金额';
    var level = $(this).data('level');
    if ($(value).val() != "") {
        var num = $(value).val();
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + once_info[level]["param" + i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});
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
        $('#rebate4').find('select').prop('disabled', false);
        $('#rebate4').find('input').prop('disabled', false);
        $('#rebate4').find('checkbox').prop('disabled', false);
        $("#add-grad").bind('click', addGrad);
    } else {
        $('#rebate4').find('select').prop('disabled', true);
        $('#rebate4').find('input').prop('disabled', true);
        $('#rebate4').find('checkbox').prop('disabled', true);
        $(this).attr('disabled', false);
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
$("#add-grad").bind('click', addGrad)
//初始化团队奖励
if (!$("#rebate4").find("input[lay-skin='switch']").is(':checked')) {
//    $('#rebate4').find('select').prop('disabled', true);
//    $('#rebate4').find('input').prop('disabled', true);
//    $('#rebate4').find('checkbox').prop('disabled', true);
    $("#rebate4").find("input[lay-skin='switch']").attr('disabled', false);
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
$('#rebate5 .set-wrapper').children('.items').hide()
//开关
form.on('switch(open_rb5)', function (data) {
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
form.on('checkbox(rb5_level1)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel1').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel1').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level2)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel2').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel2').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level3)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel3').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel3').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level4)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel4').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel4').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level5)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel5').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel5').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level6)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel6').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel6').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level7)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel7').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel7').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb5_level8)', function (data) {
    if (this.checked) {
        $('#rebate5 .set-wrapper').find('.level_sel8').fadeIn();
    } else {
        $('#rebate5 .set-wrapper').find('.level_sel8').fadeOut();
    }
    form.render();
})
//推荐等级选择
form.on('select(rb5_sel1)', function (data) {
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
form.on('select(rb5_sel2)', function (data) {
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
form.on('select(rb5_sel3)', function (data) {
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
form.on('select(rb5_sel4)', function (data) {
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
form.on('select(rb5_sel5)', function (data) {
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
form.on('select(rb5_sel6)', function (data) {
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
form.on('select(rb5_sel7)', function (data) {
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
form.on('select(rb5_sel8)', function (data) {
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
//      form.on('radio(rb5_ap)', function(data) {
//          holder = '百分比';
//          $.each($('#rebate5 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      })
//
//      form.on('radio(rb5_am)', function(data) {
//          holder = '金额';
//          $.each($('#rebate5 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      })

//初始化高发展低一次性返利设置
//开关


//      if(!$("#rebate5").find("input[lay-skin='switch']").is(':checked')) {
//      }
var rb5_lv = $('#rebate5 .level-wrapper').find("input[type='checkbox']");
$.each(rb5_lv, function (key, value) {
    if ($(value).is(":checked")) {
        var val = $(value).val();
        $('#rebate5 .set-wrapper').children(".level_sel" + val).show()
    }
});
//      if($('#rebate5 #awardPercent').is(":checked")) {
//          holder = "百分比";
//          $.each($('#rebate5 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      }
//      if($('#rebate5 #awardMoney').is(":checked")) {
//          holder = "金额";
//          $.each($('#rebate5 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      }
$.each($("#rebate5 .set-wrapper select"), function (key, value) {
    holder = '金额';
    var level = $(this).data('level');
    if ($(value).val() != "") {
        var num = $(value).val();
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + development_info[level]["param" + i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});
//高发展低一次性返利设置---------------------结束


//平级推荐一次性返利设置---------------------------------------开始
$('#rebate6 .set-wrapper').children('.items').hide()
//开关
form.on('switch(open_rb6)', function (data) {
    if (data.elem.checked) {
        data.elem.val(1);
    } else {
        data.elem.val(0);
    }
    form.render();
})
//代理等级
form.on('checkbox(rb6_level1)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel1').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel1').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level2)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel2').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel2').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level3)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel3').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel3').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level4)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel4').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel4').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level5)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel5').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel5').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level6)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel6').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel6').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level7)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel7').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel7').fadeOut();
    }
    form.render();
})
form.on('checkbox(rb6_level8)', function (data) {
    if (this.checked) {
        $('#rebate6 .set-wrapper').find('.level_sel8').fadeIn();
    } else {
        $('#rebate6 .set-wrapper').find('.level_sel8').fadeOut();
    }
    form.render();
})
//推荐等级选择
form.on('select(rb6_sel1)', function (data) {
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
form.on('select(rb6_sel2)', function (data) {
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
form.on('select(rb6_sel3)', function (data) {
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
form.on('select(rb6_sel4)', function (data) {
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
form.on('select(rb6_sel5)', function (data) {
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
form.on('select(rb6_sel6)', function (data) {
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
form.on('select(rb6_sel7)', function (data) {
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
form.on('select(rb6_sel8)', function (data) {
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
//      form.on('radio(rb6_ap)', function(data) {
//          holder = '百分比';
//          $.each($('#rebate6 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      })
//
//      form.on('radio(rb6_am)', function(data) {
//          holder = '金额';
//          $.each($('#rebate6 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      })

//初始化平级推荐一次性返利设置
//开关

//      if(!$("#rebate6").find("input[lay-skin='switch']").is(':checked')) {
//      }
var rb6_lv = $('#rebate6 .level-wrapper').find("input[type='checkbox']");
$.each(rb6_lv, function (key, value) {
    if ($(value).is(":checked")) {
        var val = $(value).val();
        $('#rebate6 .set-wrapper').children(".level_sel" + val).show()
    }
});
//      if($('#rebate6 #awardPercent').is(":checked")) {
//          holder = "百分比";
//          $.each($('#rebate6 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/金额/g, '百分比');
//              $(value).attr('placeholder', str);
//          });
//      }
//      if($('#rebate6 #awardMoney').is(":checked")) {
//          holder = "金额";
//          $.each($('#rebate6 .set-wrapper').find(".inp input"), function(key, value) {
//              var str = $(value).attr('placeholder');
//              str = str.replace(/百分比/g, '金额');
//              $(value).attr('placeholder', str);
//          });
//      }
$.each($("#rebate6 .set-wrapper select"), function (key, value) {
    holder = '金额';
    var level = $(this).data('level');
    if ($(value).val() != "") {
        var num = $(value).val();
        var temp = new Array();
        var chinese = ['一', '二', '三'];
        for (var i = 1; i <= num; i++) {
            var html = '<div class="layui-input-inline inp"><input type="text" name="param[' + level + '][]" value="' + same_development_info[level]["param" + i] + '" lay-verify="required" placeholder="' + chinese[i - 1] + '级返利' + holder + '" autocomplete="off" class="layui-input"></div>';
            temp.push(html);
        }
        $(value).parent('.layui-input-inline').siblings('.layui-input-inline').remove();
        $(value).parent('.layui-input-inline').after(temp);
    }
});
//平级发展返利设置---------------------结束