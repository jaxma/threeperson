$.ajax({
    url: map_url,
    async: false,
    success: function(data) {
        province = new Array();
        china = data;
        $.each(china, function(key, value) {
            if(value.name == re_province) {
                province.push('<option value="' + value.name + '" selected>' + value.name + '</option>');
            } else {
                province.push('<option value="' + value.name + '">' + value.name + '</option>');
            }
        })
        $('#province').append(province);
        form.render('select');
    }
})

$(document).ready(function() {
    layui.use('element', function() {
        var element = layui.element;
    });
    $('#patternList').hide();
    $('#checkList').hide();
    $('.s_paytype').hide();
    // 初始化地址
    if(re_province != "") {
        getCity(re_province);
        if(re_city != "") {
            $('#city option[value="' + re_city.substring(0, re_city.length - 1) + '"]').attr("selected", true);
            $('#city option[value="' + re_city + '"]').attr("selected", true);
            getCounty(re_city);
            getCounty(re_city.substring(0, re_city.length - 1));
            if(re_county != "") {
                $('#county option[value="' + re_county.substring(0, re_county.length - 1) + '"]').attr("selected", true);
                $('#county option[value="' + re_county + '"]').attr("selected", true);
            }
        }
    }
    // 初始化地址结束
    
    // 系统配置
    form.on('select(pattern)', function(data) {
        if(data.value == 4) {
            $('#patternList').show();
        } else {
            $('#patternList').hide();
        }
        form.render();
    })
    form.on('select(check)', function(data) {
        if(data.value == 3) {
            $('#checkList').show();
            $('#is_audit').hide();
        } else if(data.value == 2){
            $('#is_audit').show();
            $('#checkList').hide();
        } else {
            $('#checkList').hide();
            $('#is_audit').hide();
        }
        form.render();
    })
    
    // 邮费方式
    form.on('select(shipper_paytype)',function(data){
       if(data.value == 3){
           $('.s_paytype').fadeIn();
       }else{
           $('.s_paytype').hide();
       }
    });
    
    // 地址
    form.on('select(province)', function(data) {
        $('#county').empty();
        $('#city').empty();
        province1 = data.value;
        getCity(province1);
    })

    form.on('select(city)', function(data) {
        $('#county').empty();
        city1 = data.value;
        getCounty(city1);
    })
    
    //返利配置
    form.on('switch(rb_switch)', function(data) {
        if(data.elem.checked) {
            $('#order_sw').attr('disabled', false);
            $('#money_sw').attr('disabled', false);
            $('#once_sw').attr('disabled', false);
            $('#same_development_sw').attr('disabled', false);
            $('#development_sw').attr('disabled', false);
            $('#personal_sw').attr('disabled', false);
            $('#team_sw').attr('disabled', false);
        } else {
            layer.tips('关闭后所有返利设置将不可用,且默认全部关闭！', data.othis);
            $('#order_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#money_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#once_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#same_development_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#development_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#personal_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#team_sw').attr({
                'disabled': true,
                'checked': false
            });
            $('#time').hide();
        }
        form.render()
    });
    //返利配置
    form.on('switch(team_switch)', function(data) {
        if(data.elem.checked) {
            $('#time').show();
        } else {
            $('#time').hide();
        }
        form.render()
    });

    //品牌商城返利配置
    form.on('switch(mall_rb_switch)', function(data) {
        if(data.elem.checked) {
            $('#mall_order_sw').attr('disabled', false);
        } else {
            layer.tips('关闭后品牌商城的所有返利设置将不可用,且默认全部关闭！', data.othis);
            $('#mall_order_sw').attr({
                'disabled': true,
                'checked': false
            });
        }
        form.render()
    });
    form.on('switch(mall_refund_switch)', function(data) {
        if(data.elem.checked) {
            $('#refund').show();
        } else {
            layer.tips('关闭后代理将无法提现', data.othis);
            $('#refund').hide();
        }
        form.render()
    })

    //运费模板
    form.on('switch(shipping_switch)', function(data) {
        if(data.elem.checked) {
            $('#shipping').show();
        } else {
            $('#shipping').hide();
        }
        form.render()
    })

    //虚拟币充值的支付方式
    form.on('switch(money_switch)', function(data) {
        if(data.elem.checked) {
            $('#money_apply_pay_type').show();
        } else {
            $('#money_apply_pay_type').hide();
        }
        form.render()
    })

    $('#level-name').on('click', '.reduce', function() {
        var nId = $(this).siblings('div').find('input').data('id');
        var _this = this
        $.ajax({
            url: levelExistUrl,
            dataType: 'json',
            type: 'post',
            data: {
                level: nId
            },
            success: function(data) {
                // console.log(data)
                if(data.code == 1) {
                    $(_this).parent().remove();
                    $('#level-name ul li:last .reduce').show();
                } else {
                    layer.alert(data.msg);
                }
            }
        })
    })
    $('#level-name').on('click', '.add', function() {
        var oId = $('#level-name ul li:last input').data('id') + 1;
        var sLevelName = `<li>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" data-id="${oId}" value="" name='LEVEL_NAME[]' type="text" autocomplete="off" title="请输入经销商级别名" placeholder="请输入经销商级别名" class="layui-input">
                            </div>
                            <span class="reduce">-</span>
                            </li>`
        if($('#level-name li').length < 10) {
            $(this).before(sLevelName);
        } else {
            layer.alert('最多不能超过10个经销商等级！')
        }
        $('#level-name .reduce').hide();
        $('#level-name ul li:last .reduce').show()
    })

    $.ajax({
        url: websetUrl,
        type: 'post',
        dataType: 'json',
        success: function(data) {
            // console.log(data)
            if(data.code == 1) {
                var sLevelName = '';

                /************************基础配置**********************/

                //域名
                $('#domain-name').val(data.config.YM_DOMAIN);

                //系统名称
                $('#system-name').val(data.config.SYSTEM_NAME);
                //前台logo链接
                $('#logo-url').val(data.config.LOGO_URL);

                // 过滤域名
                $('.first-btn').click(function() {
                    var sValue = $('#domain-name').val();
                    if(sValue.indexOf('www.toposla.com') > 0) {
                        $('#domain-name').val('www.toposla.com');
                    }
                })

                /************************公司信息**********************/

                //域名
                $('#t_position-name').val(data.config.T_POSITION);
                $('#t_address-name').val(data.config.T_ADDRESS);
                $('#t_tel-name').val(data.config.T_TEL);
                $('#t_email-name').val(data.config.T_EMAIL);
                $('#t_en_position-name').val(data.config.T_EN_POSITION);
                $('#t_en_address-name').val(data.config.T_EN_ADDRESS);
                $('#t_en_tel-name').val(data.config.T_EN_TEL);
                $('#t_en_email-name').val(data.config.T_EN_EMAIL);

                /************************级别配置**********************/

                //经销商级别数
                $('#level-num').val(data.config.LEVEL_NUM);
                for(k in data.config.LEVEL_NAME) {
                    var count = 0;
                    for(i in data.config.LEVEL_NAME) {
                        count = i
                    }
                    if(k == i) {
                        sLevelName += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input class="input-inf2 layui-input" data-id="${k}" value="${data.config.LEVEL_NAME[k]}" name='LEVEL_NAME[]' type="text" autocomplete="off" title="请输入经销商级别名" placeholder="请输入经销商级别名" class="layui-input">
                                        </div>
                                        <span class="reduce">-</span>
                                    </li>`
                    } else {
                        sLevelName += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input class="input-inf2 layui-input" data-id="${k}" value="${data.config.LEVEL_NAME[k]}" name='LEVEL_NAME[]' type="text" autocomplete="off" title="请输入经销商级别名" placeholder="请输入经销商级别名" class="layui-input">
                                        </div>
                                        <span class="reduce" style="display:none;">-</span>
                                    </li>`
                    }
                }

                $('#level-name ul').prepend(sLevelName);

                //经销商级别名
                $('#level-name').val(data.config.LEVEL_NAME);

                /************************系统配置**********************/

                //发展模式
                for(var i = 0; i < $('#grow_model option').length; i++) {
                    if($('#grow_model option').eq(i).val() == data.config.GROW_MODEL) {
                        $('#grow_model option').eq(i).attr('selected', true);
                    }
                }
                
                if(data.config.LEVEL_NAME){
                    var lv_html = '<li id="level0"><select  class="level-model" name="GROW_MODEL_LEVEL_KEY[]"><option value="0">默认级别发展模式</option></select>';
                    var lv_str = '<select class="level-dev" name="GROW_MODEL_LEVEL_VAL[]"><option value=""></option><option value="1">高发展低</option><option value="2">高发展低及平级推</option>'+
                                  '<option value="3">任意级别发展</option></select></li>';
                    var lv_temp = [];
                    lv_temp.push(lv_html+lv_str);
                    $.each(data.config.LEVEL_NAME,function(key,val){
                        lv_html ='<li id="level'+key+'"><select class="level-model" name="GROW_MODEL_LEVEL_KEY[]"><option value="'+key+'">'+val+'</option></select>';
                        lv_temp.push(lv_html+lv_str);
                    });
                    $('#patternList ul').append(lv_temp);
                }
                //不同级别的发展模式
                if(data.config.GROW_MODEL == 4) {
                    $('#patternList').show();
//                  var html1 = '',
//                      _sLevelName = '',
//                      _sGrowModel = '';

                    for(k in data.config.GROW_MODEL_LEVEL) {
                        // 默认级别发展模式
//                      if(k == 0) {
                            $('#patternList ul').find('#level'+k+' .level-dev option[value="'+ data.config.GROW_MODEL_LEVEL[k] +'"]').attr('selected',true);
//                          _sLevelName += `<option value="0" selected>默认级别发展模式</option>`;
//                          if(data.config.GROW_MODEL_LEVEL[k] == 1) {
//                              _sGrowModel += `<option value="1" selected>高发展低</option>
//                                              <option value="2">高发展低及平级推</option>
//                                              <option value="3">任意级别发展</option>`;
//                          } else if(data.config.GROW_MODEL_LEVEL[k] == 2) {
//                              _sGrowModel += `<option value="1">高发展低</option>
//                                              <option value="2" selected>高发展低及平级推</option>
//                                              <option value="3">任意级别发展</option>`;
//                          } else if(data.config.GROW_MODEL_LEVEL[k] == 3) {
//                              _sGrowModel += `<option value="1">高发展低</option>
//                                              <option value="2">高发展低及平级推</option>
//                                              <option value="3" selected>任意级别发展</option>`;
//                          }
                            // 特定级别发展模式
//                      } else {
                            
//                          _sLevelName = '';
//                          _sGrowModel = '';
//                          for(i in data.config.LEVEL_NAME) {
//                              if(k == i) {
//                                  _sLevelName += `<option value="${data.config.GROW_MODEL_LEVEL[k]}" selected>${data.config.LEVEL_NAME[i]}</option>`;
//                              } else {
//                                  _sLevelName += `<option value="${data.config.GROW_MODEL_LEVEL[k]}">${data.config.LEVEL_NAME[i]}</option>`;
//                              }
//                          }
//                          if(data.config.GROW_MODEL_LEVEL[k] == 1) {
//                              _sGrowModel += `<option value="1" selected>高发展低</option>
//                                              <option value="2">高发展低及平级推</option>
//                                              <option value="3">任意级别发展</option>`;
//                          } else if(data.config.GROW_MODEL_LEVEL[k] == 2) {
//                              _sGrowModel += `<option value="1">高发展低</option>
//                                              <option value="2" selected>高发展低及平级推</option>
//                                              <option value="3">任意级别发展</option>`;
//                          } else if(data.config.GROW_MODEL_LEVEL[k] == 3) {
//                              _sGrowModel += `<option value="1">高发展低</option>
//                                              <option value="2">高发展低及平级推</option>
//                                              <option value="3" selected>任意级别发展</option>`;
//                          }
//                      }

//                      html1 += `<li>
//                                  <select class="level-name" disabled>
//                                      <option value=""></option>
//                                      ${_sLevelName}
//                                  </select>
//                                  <select class="grow-model" disabled>
//                                      <option value=""></option>
//                                      ${_sGrowModel}
//                                  </select>
//                              </li>`;
                    }

//                  $('#patternList ul').empty().append(html1);
//                  $('#patternList').show();
                }

                for(var i = 0; i < $('#audit_way option').length; i++) { //审核方式
                    if($('#audit_way option').eq(i).val() == data.config.AUDIT_WAY) {
                        $('#audit_way option').eq(i).attr('selected', true);
                    }
                }
                for(var i = 0; i < $('#is_audited option').length; i++) { //审核方式为2时触发
                    if($('#is_audited option').eq(i).val() == data.config.IS_AUDITED) {
                        $('#is_audited option').eq(i).attr('selected', true);
                    }
                }
                
                if(data.config.LEVEL_NAME){
                    var lv_html = '<li id="waylevel0"><select  class="level-model" name="AUDIT_WAY_LEVEL_KEY[]"><option value="0">默认级别审核方式</option></select>';
                    var lv_str = '<select class="level-dev" name="AUDIT_WAY_LEVEL_VAL[]"><option value=""></option><option value="1">上级审核</option><option value="2">总部审核</option>'+
                                  '</select></li>';
                    var lv_temp = [];
                    lv_temp.push(lv_html+lv_str);
                    $.each(data.config.LEVEL_NAME,function(key,val){
                        lv_html ='<li id="waylevel'+key+'"><select class="level-model" name="AUDIT_WAY_LEVEL_KEY[]"><option value="'+key+'">'+val+'</option></select>';
                        lv_temp.push(lv_html+lv_str);
                    });
                    $('#checkList ul').append(lv_temp);
                }
                
                if(data.config.AUDIT_WAY == 2) {
                    $("#is_audit").show();
                } else {
                    $("#is_audit").hide();
                }
                if(data.config.AUDIT_WAY == 3) {
//                  var html2 = '',
//                      sAuditLevel = '',
//                      sAuditWay = '';
                    data.config.AUDIT_WAY_LEVEL.forEach(function(value, index) {
                        $('#checkList ul').find('#waylevel'+index+' .level-dev option[value="'+ data.config.AUDIT_WAY_LEVEL[index] +'"]').attr('selected',true);
//                      // 默认级别审核方式
//                      if(index == 0) {
//                          sAuditLevel += `<option value="0" selected>默认级别审核方式</option>`;
//                          if(value == 1) {
//                              sAuditWay += `<option value="1" selected>上级审核</option>
//                                              <option value="2">总部审核</option>`;
//                          } else if(value == 2) {
//                              sAuditWay += `<option value="1">上级审核</option>
//                                              <option value="2"  selected>总部审核</option>`;
//                          }
//                          // 特定级别审核方式
//                      } else {
//                          sAuditLevel = '',
//                              sAuditWay = '';
//                          for(i in data.config.LEVEL_NAME) {
//                              if(index == i) {
//                                  sAuditLevel += `<option value="${data.config.GROW_MODEL_LEVEL[index]}" selected>${data.config.LEVEL_NAME[index]}</option>`;
//                              } else {
//                                  sAuditLevel += `<option value="${data.config.GROW_MODEL_LEVEL[index]}">${data.config.LEVEL_NAME[index]}</option>`;
//                              }
//                          }
//                          if(value == 1) {
//                              sAuditWay += `<option value="1" selected>上级审核</option>
//                                              <option value="2">总部审核</option>`;
//                          } else if(value == 2) {
//                              sAuditWay += `<option value="1">上级审核</option>
//                                              <option value="2"  selected>总部审核</option>`;
//                          }
//                      }
//                      html2 += `<li>
//                                  <select disabled>
//                                      <option value=""></option>
//                                      ${sAuditLevel}
//                                  </select>
//                                  <select disabled>
//                                      <option value=""></option>
//                                      ${sAuditWay}
//                                  </select>
//                              </li>`;
                    })
//                  $('#checkList ul').empty().append(html2);
                    $('#checkList').show();
                }

                //请选择提交图片类型
                for(var i = 0; i < $('#selectImg option').length; i++) {
                    if($('#selectImg option').eq(i).val() == data.config.IS_SUBMIT_ID_CARD_IMG) {
                        $('#selectImg option').eq(i).attr('selected', true);
                    }
                }
                
                for(var i = 0; i < $('#selectWay option').length; i++) {
                    if($('#selectWay option').eq(i).val() == data.config.MONEY_COUNT_WAY) {
                        $('#selectWay option').eq(i).attr('selected', true);
                    }
                }
                
                if(data.config.SHIPPER_PAYTYPE && data.config.SHIPPER_PAYTYPE == 3){
                    $('.s_paytype').fadeIn();
                }
                
                //请选择提交图片类型
                for(var i = 0; i < $('#kdorder_price option').length; i++) {
                    if($('#kdorder_price option').eq(i).val() == data.config.KDORDER_PRICE) {
                        $('#kdorder_price option').eq(i).attr('selected', true);
                    }
                }
                
                //请选择提交图片类型
                for(var i = 0; i < $('#shipper_paytype option').length; i++) {
                    if($('#shipper_paytype option').eq(i).val() == data.config.SHIPPER_PAYTYPE) {
                        $('#shipper_paytype option').eq(i).attr('selected', true);
                    }
                }

                /************************公众号配置**********************/

                //公众号AppID
                $('#app-id').val(data.config.APP_ID);

                // 公众号密钥
                $('#app-secret').val(data.config.APP_SECRET);

                /************************消息配置**********************/
                $('#sh-mb').val(data.config.SH_MB); //审核模板
                $('#sq-mb').val(data.config.SQ_MB); //申请模板
                $('#new').val(data.config.NEW); //新订单
                $('#cancle').val(data.config.CANCLE); //取消订单
                $('#audit').val(data.config.AUDIT); //审核订单
                $('#money-mb').val(data.config.MONEY_MB); //虚拟币申请/审核
                $('#upgrade-mb').val(data.config.UPGRADE_APPLY_MB); //代理升级
                $('#upgrade-apply').val(data.config.UPGRADE_PASS_MB); //代理升级

                /************************返利配置**********************/
                if(data.config.REBATE.OPEN) {
                    $('#rb_switch').attr('checked', true);
                } else {
                    $('#rb_switch').removeAttr('checked');
                }
                if(data.config.REBATE.ORDER) {
                    $('#order_sw').attr('checked', true);
                } else {
                    $('#order_sw').removeAttr('checked');
                }
                if(data.config.REBATE.MONEY) {
                    $('#money_sw').attr('checked', true);
                } else {
                    $('#money_sw').removeAttr('checked');
                }
                if(data.config.REBATE.ONCE) {
                    $('#once_sw').attr('checked', true);
                } else {
                    $('#once_sw').removeAttr('checked');
                }
                if(data.config.REBATE.SAME_DEVELOPMENT) {
                    $('#same_development_sw').attr('checked', true);
                } else {
                    $('#same_development_sw').removeAttr('checked');
                }
                if(data.config.REBATE.DEVELOPMENT) {
                    $('#development_sw').attr('checked', true);
                } else {
                    $('#development_sw').removeAttr('checked');
                }
                if(data.config.REBATE.PERSONAL) {
                    $('#personal_sw').attr('checked', true);
                } else {
                    $('#personal_sw').removeAttr('checked');
                }
                if(data.config.REBATE.ORDINARY_TEAM) {
                    $('#team_sw').attr('checked', true);
                } else {
                    $('#team_sw').removeAttr('checked');
                }
                if(!data.config.REBATE.ORDINARY_TEAM) {
                    $('#time').hide();
                }
                for(var i = 0; i < $('#kdnian_order option').length; i++) {
                    if($('#kdnian_order option').eq(i).val() == data.config.KDNIAO_ORDER) {
                        $('#kdnian_order option').eq(i).attr('selected', true);
                    }
                }
                for(var i = 0; i < $('#send_order option').length; i++) {
                    if($('#send_order option').eq(i).val() == data.config.SEND_ORDER) {
                        $('#send_order option').eq(i).attr('selected', true);
                    }
                }
                if(data.config.REBATE.CLICK_TEAM_REBATE) {
                    $('#click_team_sw').attr('checked', true);
                } else {
                    $('#click_team_sw').removeAttr('checked');
                }
                /************************消息模块配置**********************/
                for(var i = 0; i < $('#msg_m_system option').length; i++) {
                    if($('#msg_m_system option').eq(i).val() == data.config.MESSAGE_MODULE.SYSTEM) {
                        $('#msg_m_system option').eq(i).attr('selected', true);
                    }
                }
                for(var i = 0; i < $('#msg_m_distributor option').length; i++) {
                    if($('#msg_m_distributor option').eq(i).val() == data.config.MESSAGE_MODULE.DISTRIBUTOR) {
                        $('#msg_m_distributor option').eq(i).attr('selected', true);
                    }
                }
                /************************功能模块配置**********************/

                if(data.config.FUNCTION_MODULE.MONEY) {
                    $('#money-module').attr('checked', true);
                } else {
                    $('#money-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.MONEY) {
                    $('#money_apply_pay_type').show();
                } else {
                    $('#money_apply_pay_type').hide();
                }
                if(data.config.FUNCTION_MODULE.MONEY_APPLY_PAY_TYPE == 1) {
                    $('#money_online').attr('checked', true);
                }
                else if(data.config.FUNCTION_MODULE.MONEY_APPLY_PAY_TYPE == 2){
                    $('#money_all').attr('checked', true);
                }
                else {
                    $('#money_xia').attr('checked', true);
                }
                if(data.config.FUNCTION_MODULE.INTEGRAL_SHOP) {
                    $('#integral-module').attr('checked', true);
                } else {
                    $('#integral-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.MALL_SHOP) {
                    $('#mall-module').attr('checked', true);
                } else {
                    $('#mall-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.STOCK) {
                    $('#stock-module').attr('checked', true);
                } else {
                    $('#stock-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.MARKET) {
                    $('#market-module').attr('checked', true);
                } else {
                    $('#market-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.GW) {
                    $('#gw-module').attr('checked', true);
                } else {
                    $('#gw-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.TEAM) {
                    $('#team-module').attr('checked', true);
                } else {
                    $('#team-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.BOSS_ORDER) {
                    $('#boss-order-module').attr('checked', true);
                } else {
                    $('#boss-order-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.STOCK_ORDER) {
                    $('#STOCK_ORDER-module').attr('checked', true);
                } else {
                    $('#STOCK_ORDER-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.ORDER_FORMAT) {
                    $('#ORDER_FORMAT-module').attr('checked', true);
                } else {
                    $('#ORDER_FORMAT-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.SHOP_IN_SHOP) {
                    $('#shopInShop-module').attr('checked', true);
                } else {
                    $('#shopInShop-module').removeAttr('checked');
                }
                if(data.config.FUNCTION_MODULE.DEPOT) {
                    $('#DEPOT-module').attr('checked', true);
                } else {
                    $('#DEPOT-module').removeAttr('checked');
                }
                /************************品牌商城返利配置**********************/
                if(data.config.MALL_REBATE.OPEN) {
                    $('#mall_rb_switch').attr('checked', true);
                } else {
                    $('#mall_rb_switch').removeAttr('checked');
                }
                if(data.config.MALL_REBATE.ORDER) {
                    $('#mall_order_sw').attr('checked', true);
                } else {
                    $('#mall_order_sw').removeAttr('checked');
                }
                if(data.config.MALL_REFUND.IS_OPEN) {
                    $('#mall_refund_sw').attr('checked', true);
                } else {
                    $('#mall_refund_sw').removeAttr('checked');
                }
                if(data.config.MALL_REFUND.IS_OPEN) {
                    $('#refund').show();
                } else {
                    $('#refund').hide();
                }
                if(data.config.MALL_REFUND.MALL_REFUND_PAY_TYPE) {
                    $('#mall_re_wx').attr('checked', true);
                } else {
                    $('#mall_re_bank').attr('checked', true);
                }

                if(data.config.ORDER_SHIPPING) {
                    $('#order-shipping-module').attr('checked', true);
                } else {
                    $('#order-shipping-module').removeAttr('checked');
                }
                if(data.config.ORDER_SHIPPING) {
                    $('#shipping').show();
                } else {
                    $('#shipping').hide();
                }
                if(data.config.SHIPPING_REDUCE_WAY) {
                    $('#shipping_reduce_way_one').attr('checked', true);
                } else {
                    $('#shipping_reduce_way_all').attr('checked', true);
                }
                /************************基本配置**********************/
                //是否测试模式
                if(data.config.IS_TEST) {
                    $('#is-test').attr('checked', true);
                } else {
                    $('#is-test').removeAttr('checked');
                }
                //是否调试模式
                $('#app-debug').val(data.config.APP_DEBUG);
                //是否测试模式
                for(var i = 0; i < $('#app_test option').length; i++) {
                    if($('#app_test option').eq(i).val() == data.config.APP_TEST) {
                        $('#app_test option').eq(i).attr('selected', true);
                    }
                }

                //   $('#is-test').val(data.config.IS_TEST); //是否测试模式
                //统计业绩方式
                if(data.config.MONEY_COUNT_WAY == 0) {
                    $('#money-count-way').val('虚拟币');
                } else if(data.config.MONEY_COUNT_WAY == 1) {
                    $('#money-count-way').val('订单金额');
                } else if(data.config.MONEY_COUNT_WAY == 2) {
                    $('#money-count-way').val('订单数量');
                }
                //团队是根据上下级关系(path)还是推荐人关系(rec_path)定义
                $('#default-team').val(data.config.DEFAULT_TEAM);

                /************************用户配置**********************/
                $('#is-multilayer').val(data.config.user.is_multilayer); //是否多层级，选择TRUE，该系统必须有“代理关系表”
                $('#has-user-bind').val(data.config.user.has_user_bind); //是否生成用户关系
                $('#is-cycle-multilayer').val(data.config.user.is_cycle_multilayer); //是否使用有限制次数的循环获得多层代理关系

                /************************订单配置**********************/
                //订单状态
                var sStatusName = '';
                for(k in data.config.order.status_name) {
                    sStatusName += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${data.config.order.status_name[k]}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                }
                $('#status-name ul').append(sStatusName);

                //支付方式
                var sPayType = '';
                data.config.order.all_pay_type.forEach(function(value) {
                    sPayType += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${value}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                })
                $('#all-pay-type ul').append(sPayType);

                //是否生成订单统计表
                $('#is-generate-order-count').val(data.config.order.is_generate_order_count);

                //是否总部供货
                $('#is-top-supply').val(data.config.order.is_top_supply);

                //根据级别判断供货方式
                var sSupplyLevel = '';
                data.config.order.is_top_supply_level.forEach(function(value) {
                    sSupplyLevel += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${value}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                })
                $('#is-top-supply-level ul').append(sSupplyLevel);

                //是否启用下单限制
                $('#opent-order-limit').val(data.config.order.opent_order_limit);

                /************************资金配置**********************/

                //是否进行虚拟币系统的逻辑
                $('#is-charge-money').val(data.config.funds.is_charge_money);

                //如果有值则根据该值进行级别判断是否进行虚拟币功能逻辑
                var sMoneyLevel = '';
                data.config.funds.is_charge_money_level.forEach(function(value) {
                    sMoneyLevel += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${value}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                })
                $('#is_charge_money_level ul').append(sMoneyLevel);

                //是否充值金额都可以提现，即可提现金额等于充值金额
                $('#is-all-can-refund').val(data.config.funds.is_all_can_refund);

                //是否使用获取最低申请金额
                $('#is-get-min-apply-money').val(data.config.funds.is_get_min_apply_money);

                //是否使用获取最低提现金额
                $('#is-get-min-refund-money').val(data.config.funds.is_get_min_refund_money);

                //订单扣费时，扣费金额充回订单供货商时为TRUE，直接扣费不做充值操作为FALSE
                $('#is-parent-order').val(data.config.funds.is_parent_order);

                //是否由直属上级审核虚拟币，是则由上级审核下级充值申请，并上级相应余额转到下级。否则总部审核充值
                $('#is-parent-audit').val(data.config.funds.is_parent_audit);

                //是否启用订单返还（需要开启扣虚拟币系统is_charge_money）,注意，开启后经销商及总部审核时都会触发
                $('#is-order-return').val(data.config.funds.is_order_return);

                //订单返还循环的次数
                $('#order-return-rank').val(data.config.funds.order_return_rank);

                //返利是否充入账户
                $('#is-rebate-recharge').val(data.config.funds.is_rebate_recharge);

                /************************积分配置**********************/

                //是否开启积分功能
                $('#integral-open').val(data.config.integral.integral_open);

                //日志类型
                var sIntegralStatus = '';
                for(k in data.config.integral.integral_status) {
                    sIntegralStatus += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${data.config.integral.integral_status[k]}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                }
                $('#integral-status ul').append(sIntegralStatus);

                //积分规则类型，排序应与日志类型的行为是一致的（描述可以略有不同，但是进行的系统业务是一致）
                var sIntegralRule = '';
                for(k in data.config.integral.integral_rule_typ) {
                    sIntegralRule += `<li>
                                        <div class="form-right layui-col-xs12">
                                            <input disabled class="input-inf2 layui-input" value="${data.config.integral.integral_rule_typ[k]}" type="text" autocomplete="off" class="layui-input">
                                        </div>
                                    </li>`
                }
                $('#integral-rule-typ ul').append(sIntegralRule);

                form.render();

                /************************微信支付配置**********************/
                //微信支付商户号
                $('#mch-id').val(data.config.WX_PAY_CONFIG.MCHID);
                // 微信支付密钥
                $('#key').val(data.config.WX_PAY_CONFIG.KEY);

            }
        }

    })

})

layui.use(['upload'], function() {
    var upload = layui.upload;
    //执行实例
    var uploadInst = upload.render({
        elem: '#btn_mp',
        url: mp_url,
        method: 'post',
        size: 3072,
        accept: 'file',
        exts: 'txt',
        data: {
            upload_dir_name: mp_dir_name
        },
        done: function(res, index, upload) {
            //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
            //如果上传失败
            if(res.code > 0) {
                return layer.msg('上传失败');
            }
            //上传成功
            layer.closeAll('loading'); //关闭loading
            layer.msg(res.msg);
            var item = this.item;

        },
        error: function(index, upload) {
            layer.closeAll('loading');
        }
    });
});

/*
 * 上传图片及编辑器上传图片js
 * add by zbs
 * create by 2017-10-23
 */
layui.use(['upload'], function() {
    var upload = layui.upload;
    var urls = "";
    var elems = '.upload-btn';
    try {
        urls = logo_upload_url;
        elems += rand;
        alert(elems);
    } catch(e) {
        console.log(e)
        //TODO handle the exception
    }
    //  console.log(urls)
    if(urls == "" || urls == undefined || urls == null) {
        urls = URL + '/upload/';
    }
    //执行实例
    var uploadInst = upload.render({
        elem: elems,
        url: urls,
        method: 'post',
        size: 3072,
        accept: 'images',
        data: {
            upload_dir_name: upload_dir_name
        },
        before: function(obj) {
            //预读本地文件示例，不支持ie8
            var item = this.item;
            obj.preview(function(index, file, result) {
                $(item).siblings('.layui-upload').find('.layui-upload-list').fadeIn().find('.layui-upload-img').attr('src', result); //图片链接（base64)
                $(item).siblings('.input-inf2').val(file.name)
            });
        },
        done: function(res, index, upload) {
            //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
            //如果上传失败
            if(res.code > 0) {
                return layer.msg('上传失败');
            }
            //上传成功
            layer.closeAll('loading'); //关闭loading
            layer.msg(res.msg);
            var item = this.item;
            $(item).siblings('.layui-upload').find('.image-name').val(res.src)
            //网站配置的额外配置方法
            $('.user-img .avatar').attr('src', root + res.src + "?" + Math.random());
        },
        error: function(index, upload) {
            layer.msg("上传失败");
            layer.closeAll('loading');
        }
    });
});

/*
 * 上传前台图片及编辑器上传图片js
 * add by zbs
 * create by 2017-10-23
 */
layui.use(['upload'], function() {
    var upload = layui.upload;
    var urls = "";
    var elems = '.indexlogo-upload-btn';
    try {
        urls = index_logo_upload_url;
        elems += rand;
        alert(elems);
    } catch(e) {
        console.log(e)
        //TODO handle the exception
    }
    //  console.log(urls)
    if(urls == "" || urls == undefined || urls == null) {
        urls = URL + '/upload/';
    }
    //执行实例
    var uploadInst = upload.render({
        elem: elems,
        url: urls,
        method: 'post',
        size: 3072,
        accept: 'images',
        data: {
            upload_dir_name: upload_dir_name
        },
        before: function(obj) {
            //预读本地文件示例，不支持ie8
            var item = this.item;
            obj.preview(function(index, file, result) {
                $(item).siblings('.layui-upload').find('.layui-upload-list').fadeIn().find('.layui-upload-img').attr('src', result); //图片链接（base64)
                $(item).siblings('.input-inf2').val(file.name)
            });
        },
        done: function(res, index, upload) {
            //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
            //如果上传失败
            if(res.code > 0) {
                return layer.msg('上传失败');
            }
            //上传成功
            layer.closeAll('loading'); //关闭loading
            layer.msg(res.msg);
            var item = this.item;
            $(item).siblings('.layui-upload').find('.image-name').val(res.src)
            //网站配置的额外配置方法
            // $('.user-img .avatar').attr('src', root + res.src + "?" + Math.random());
        },
        error: function(index, upload) {
            layer.msg("上传失败");
            layer.closeAll('loading');
        }
    });
});

//点击删除图片
$(function() {

    $('.layui-upload-list').each(function(key, value) {
        if($(this).data('show') == 1) {
            $(this).fadeIn().find('.layui-upload-img').attr('src', $(this).data('url'))
        }
    });
    $(document).on('click', '.demoText', function() {
        if($(this).find('.delete').length > 0) {
            $(this).siblings('img').attr('src', '').parent().hide().siblings('.image-name').val('');
        }
    });

});

function getCity(str) {

    province1 = str;
    city = new Array();
    $.each(china, function(key, value) {
        if(province1 === value.name) {
            $.each(value.sub, function(k, v) {
                if(v.name == re_city) {
                    city.push('<option value="' + v.name + '" selected>' + v.name + '</option>');
                } else {
                    city.push('<option value="' + v.name + '">' + v.name + '</option>');
                }
            });
        }
    });
    $('#city').empty().append(city);
    $('#county').empty();
    form.render('select');
}

function getCounty(str) {
    area = [];
    city1 = str;
    county = new Array();
    $.each(china, function(key, value) {
        if(province1 == value.name) {
            $.each(value.sub, function(k, v) {
//              console.log(city1 + ' : ' + v.name)
                if(city1 == v.name || (city1 == v.name.substring(0, v.name.length - 1))) {
                    if(!(v.sub == undefined || v.sub == "" || v.sub == null)) {
                        $.each(v.sub, function(i, j) {
                            if(j.name == re_county) {
                                area.push('<option value="' + j.name + '" selected>' + j.name + '</option>');
                            } else {
                                area.push('<option value="' + j.name + '">' + j.name + '</option>');
                            }
                        });
                    }
                }
            });
        }
    });
    $('#county').empty().append(area);
    form.render('select');
}