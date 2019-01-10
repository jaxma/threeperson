var cart_ids = nums = sku_ids = tids = "",
    default_input = 0;
$(function() {
    $('#order_num').val(new Date().getTime());
    form.render();
    getCartsDetail("");
    // 监听等级
    form.on('select(level)', function(data) {
        var html = '<option value="">请选择代理</option>';
        //      var otemp = [];
        //      $.each($('#agent_list option'), function(key, value) {
        //          otemp.push($(value).val());
        //      });
        //      console.log(otemp)
        $('#daili2').val("");
        $("#agent_list").empty().append(html);
        if(data.value != "") {
            $.post(get_agentid, {
                level: data.value
            }, function(data) {
                if(data != 'none') {
                    var text = '';

                    $.each(data, function(index, array) {
                        //                      if(array.id && otemp.indexOf(array.id) == -1) {
                        if(array.id) {
                            text += '<option value="' +
                                array["id"] + '">' + array["name"] + '</option>';
                        }
                    })
                    $("#agent_list").append(text);
                    cleanAddress();
                    disableAddress();
                }
                form.render()
            });
        }
    });

    // 监听代理
    form.on('select(agent_list)', function(data) {
        var html = '<option value="">请选择地址</option><option value="add">添加新地址</option>';
        $('#sel_address').empty().append(html);
        cleanAddress();
        if(data.value != "") {
            //          用户信息
            getAgentInfo(data.value);
            getCartsDetail(data.value);
        }
    });

    //  监听地址
    form.on('select(sel_address)', function(data) {
        cleanAddress();
        if(data.value == "add") {
            $.getJSON(root + "/Radmin_v3/plugs/echart/map/area.json", function(data) {
                province = new Array();
                $.each(data, function(key, value) {
                    province.push('<option value="' + value.name + '">' + value.name + '</option>');
                })
                $('#level_one').append(province);
                $('#level_one').removeAttr('disabled');
                $('#level_two').removeAttr('disabled');
                $('#level_three').removeAttr('disabled');
                $('#address_detail').removeAttr('disabled');
                $('#agent_name').val("").removeAttr('disabled');
                $('#phone').val("").removeAttr('disabled');
                form.render();
            });
        } else if(data.value != "") {
            disableAddress();
            var seleted = $(data.elem).children('option:selected');
            var _province = seleted.data('province');
            var _city = seleted.data('city');
            var _area = seleted.data('area');
            var _address = seleted.data('address');
            var _name = seleted.data('name');
            var _phone = seleted.data('phone');
            $('#level_one').append('<option value="' + _province + '" selected>' + _province + '</option>');
            $('#level_two').append('<option value="' + _city + '" selected>' + _city + '</option>');
            $('#level_three').append('<option value="' + _area + '" selected>' + _area + '</option>');
            $('#address_detail').val(_address);
            $('#agent_name').val(_name);
            $('#phone').val(_phone);

        } else {
            disableAddress();
        }
        form.render();
    });

    //  三级联动
    var province, province1, city, city1, area, area1;

    form.on('select(level_one)', function(data) {
        province1 = data.value;
        $.getJSON(root + "/Radmin_v3/plugs/echart/map/area.json", function(data) {
            city = new Array();
            $.each(data, function(key, value) {
                if(province1 === value.name) {
                    $.each(value.sub, function(k, v) {
                        city.push('<option value="' + v.name + '">' + v.name + '</option>');
                    });
                }
            });
            $('#level_two').empty().append(city);
            form.render('select');
        });
    });

    form.on('select(level_two)', function(data) {
        city1 = data.value;
        $.getJSON(root + "/Radmin_v3/plugs/echart/map/area.json", function(data) {
            area = new Array();
            $.each(data, function(key, value) {
                if(province1 == value.name) {
                    $.each(value.sub, function(k, v) {
                        if(city1 == v.name) {
                            if(!(v.sub == undefined || v.sub == "" || v.sub == null)) {
                                $.each(v.sub, function(i, j) {
                                    area.push('<option value="' + j.name + '">' + j.name + '</option>');
                                });
                            }
                        }
                    });
                }
            });
            $('#level_three').empty().append(area);
            form.render('select');
        });
    });

    //  购买数量
    //  $(document).on('focus','.buy_num',function(){
    //      default_input = $(this).val();
    //  })

    $(document).on('change', '.buy_num', function() {
        if($(this).val().trim() == "" || $(this).val().trim() < 0) {
            $(this).val(0);
        } else if($(this).val().trim() != "") {
            var c_id = $(this).parent().parent().data('id').toString();
            var _cart_ids = cart_ids.split('|');
            var index = _cart_ids.indexOf(c_id);
            var _num = nums.split('|');
            _num[index] = $(this).val();
            nums = _num.join('|');
            $('#nums').val(nums);
        }
        sumTotal();
    });

    //  删除
    $(document).on('click', '.delete-grad', function() {
        var _this = this;
        var c_id = $(_this).parent().parent().data('id').toString();
        // console.log(c_id);
        layer.confirm("你确定要取消购买该产品？", function(index) {
            layer.close(index);
            $(_this).parent().parent().remove();
            var _cart_ids = cart_ids.split('|');
            var _num = nums.split('|');
            var _tids = tids.split('|');
            var _sku_ids = sku_ids.split('|');
            var index = _cart_ids.indexOf(c_id);
            _cart_ids.splice(index, 1);
            _tids.splice(index, 1);
            _sku_ids.splice(index, 1);
            _num.splice(index, 1);
            nums = _num.join('|');
            cart_ids = _cart_ids.join('|');
            tids = _tids.join('|');
            sku_ids = _sku_ids.join('|');
            $('#nums').val(nums);
            $('#cart_ids').val(cart_ids);
            $('#tids').val(tids);
            $('#sku_ids').val(sku_ids);
            sumTotal();
        });
    });

    //  $(document).on('keyup', 'select[lay-search] ~ .layui-form-select .layui-select-title input', function() {
    //      var name = $(this).val();
    //      $.post(get_distributor, {
    //          name: name
    //      }, function(data) {
    //          console.log(data)
    //          if(data.status == "succ") {
    //              var name_temp = [];
    //              var count = 0;
    //              var old_temp = [];
    //              var o_aim = '';
    //              $.each($('#agent_list option'), function(key, value) {
    //                  old_temp.push($(value).val());
    //              });
    //              $.each(data.data, function(key, value) {
    //                  count++;
    //                  if(old_temp.indexOf(value.id.toString()) == -1) {
    //                      var str = '<option value="' + value.id + '"' + (count == 1 ? 'selected' : '') + '>' + value.name + '</option>';
    //                      name_temp.push(str);
    //                  } else {
    //                      $('#agent_list option[value=' + value.id + ']').attr('selected', true);
    //                  }
    //                  if(count == 1) {
    //                      o_aim = $('#level ~ .layui-form-select .layui-anim').children('dd[lay-value=' + value.level + ']');
    //                      
    //                  }
    //              });
    //              $('#agent_list').append(name_temp);
    //              o_aim.click();
    //              form.render();
    //          }
    //      });
    //  });

});

function cleanAddress() {
    $('#level_one').empty().append('<option value="">请选择</option>');
    $('#level_two').empty().append('<option value="">请选择</option>');
    $('#level_three').empty().append('<option value="">请选择</option>');
    $('#address_detail').val("");
    form.render();
}

function disableAddress() {
    $('#level_one').attr('disabled', true);
    $('#level_two').attr('disabled', true);
    $('#level_three').attr('disabled', true);
    $('#address_detail').attr('disabled', true);
    $('#agent_name').attr('disabled', true);
    $('#phone').attr('disabled', true);
    form.render();
}

function getCartsDetail(uid) {
    //          商品信息
    $.post(get_cartinfo, {
        uid: uid
    }, function(data) {
        if(data.code == 1) {
            // console.log(data);
            var product_html = '';
            var thead = '<table class="layui-table" lay-skin="row" lay-size="sm" id="table-grad"><thead><tr>' +
                '<th>产品名称</th><th>产品规格</th><th>价格</th><th>库存</th><th>购买数量</th><th>操作</th></tr>' +
                '</thead><tbody>';
            if(data.carts != null) {
                product_html += thead;
                cart_ids = nums = sku_ids = tids = "";
                $.each(data.carts, function(key, value) {
                    cart_ids += value.id + '|';
                    nums += value.num + '|';
                    tids += value.tid + '|';
                    sku_ids += value.sku_id + '|';
                    var str =
                        '<tr data-id="' + value.id + '"><td><input type="text" class="tb_input" value="' + value.products_name + '" disabled/></td>' +
                        '<td><input type="text" class="tb_input" value="' + value.properties + '" disabled/></td>' +
                        '<td><input type="number" class="tb_input buy_price" value="' + value.price + '" disabled/></td>' +
                        '<td><input type="number" class="tb_input" value="' + value.quantity + '" disabled/></td>' +
                        '<td><input type="number" class="tb_input buy_num" placeholder="请输购买数量" value="' + value.num + '" /></td>' +
                        '<td><input type="button" class="layui-btn layui-btn-danger delete-grad" value="删除" /></td></tr>';
                    product_html += str;
                });
                var thead_end = '<input type="hidden" name="nums" id="nums" value="' + nums + '" />' +
                    '<input type="hidden" name="sku_ids" id="sku_ids" value="' + sku_ids + '" />' +
                    '<input type="hidden" name="tids" id="tids" value="' + tids + '" />' +
                    '<input type="hidden" name="cart_ids" id="cart_ids" value="' + cart_ids + '" />' +
                    '</tbody></table>';
                product_html += thead_end;
                $('#total_money').val(data.total_money);
            }
            $('.table-wrapper').empty().append(product_html);
            sumTotal();
        } else {
            layer.msg(data.msg);
        }
    });
}

function sumTotal() {
    var total = 0;
    if($('.buy_num').length > 0) {
        $.each($('.buy_num'), function(key, value) {
            total = total + Number($(value).val()) * Number($(this).parent().parent().find('.buy_price').val());
        });
    }
    $('#total_money').val(total);
}

function getAgentInfo(uid) {
    $.post(get_agentinfo, {
        uid: uid
    }, function(data) {
        if(data.code == 1) {
            // console.log(data);
            $('#wechatnum').val(data.dis_info.wechatnum);
            $('#recharge_money').val(data.recharge_money);
            var address_temp = ['<option value="">请选择地址</option>'];
            if(data.address_info != null) {
                $.each(data.address_info, function(key, value) {
                    var str = '<option data-name="' + value.name + '" data-phone="' + value.phone + '" data-province="' + value.province + '" data-city="' + value.city + '" data-address="' + value.address + '" data-area="' + value.area + '" value="' + value.id + '" ' + (value.default == "1" ? "selected" : "") + '>' + value.province + '/' + value.city + '/' + value.area + '/' + value.address + '</option>'
                    address_temp.push(str);
                    if(value.default == "1") {
                        $('#agent_name').val(value.name).attr('disabled', true);
                        $('#phone').val(value.phone).attr('disabled', true);
                        $('#level_one').append('<option value="' + value.province + '" selected>' + value.province + '</option>');
                        $('#level_two').append('<option value="' + value.city + '" selected>' + value.city + '</option>');
                        $('#level_three').append('<option value="' + value.area + '" selected>' + value.area + '</option>');
                        $('#address_detail').val(value.address);
                    }
                });
            } else {
                $('#agent_name').val("").removeAttr('disabled');
                $('#phone').val("").removeAttr('disabled');
            }
            address_temp.push('<option value="add">添加新地址</option>');
            $('#sel_address').empty().append(address_temp);
            form.render();
        } else {
            layer.msg(data.msg);
        }
    });
}

//选择代理
function secect_daili(js_id) {
    var name = $("#" + js_id).val();

    $.post(get_distributor, {
        name: name
    }, function(res) {
        if(res.status == 'succ') {
            $('#level option').removeAttr('selected');
            var count = 0;
            var name_temp = [];
            $.each(res.data, function(key, value) {
                count++;
                var str = '<option value="' + value.id + '" ' + (count == 1 ? 'selected' : '') + '>' + value.name + '</option>';
                name_temp.push(str);
                if(count==1){
                    getAgentInfo(value.id);
                    getCartsDetail(value.id);
                    $('#level option[value='+value.level+']').prop('selected',true);
                    form.render();
                }
                form.render();
            });
            // console.log(name_temp);
            $('#agent_list').empty().append(name_temp);
            form.render();
        } else {
            layer.msg(res.msg);
        }
        form.render();
    });
    form.render();
}


