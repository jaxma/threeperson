<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <script>
        var websetUrl = '__GROUP__/Webset/get_webconfig';
        var levelExistUrl = '__GROUP__/Webset/get_level_exist';
        var updateWebsetUrl = '__GROUP__/Webset/update_webset';
        var upload_dir_name = 'logo';
        var logo_upload_url = '__GROUP__/Webset/upload';
        var index_logo_upload_url = '__GROUP__/Webset/index_logo_upload';
        var root = '__ROOT__';
        var mp_url = '__GROUP__/Webset/upload_mp';
        var mp_dir_name = 'mp';
        var map_url = '__PUBLIC__/Radmin_v3/plugs/echart/map/area.json';
        var province,province1,city,city1,area,area1,china;
        var re_province = '<?php echo (C("BOSS.PROVINCE")); ?>';
        var re_city = '<?php echo (C("BOSS.CITY")); ?>';
        var re_county = '<?php echo (C("BOSS.COUNTY")); ?>';
        form.render();
    </script>
    <style>
        h4 {
            margin-left: 50px;
            margin-bottom: 20px;
            font-size: 20px;
            padding: 5px 10px;
        }

        .edit-wrapper .layui-content .layui-form .items .form-text {
            flex: none;
        }

        .edit-wrapper .layui-content .layui-form .input-inf2 {
            min-width: 410px;
        }

        .edit-wrapper .layui-content .layui-form .items .form-right {
            padding-left: 60px;
        }

        .layui-form {
            padding: 15px 0;
        }

        .layui-input-block {
            margin-left: 60px;
        }

        .select-list .layui-form-select {
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .level-name li,
        .status-name li,
        .all-pay-type li,
        .is-top-supply-level li,
        .is-charge-money-level li,
        .integral-status li,
        .integral-rule-typ li {
            position: relative;
            height: 50px;
        }

        .level-name li .reduce {
            position: absolute;
            display: inline-block;
            left: 500px;
            top: 5px;
            width: 30px;
            height: 30px;
            line-height: 26px !important;
            font-size: 30px;
            background-color: #FF5722;
            color: #fff;
            border-radius: 3px;
            text-align: center;
            cursor: pointer;
        }

        .level-name .add {
            display: inline-block;
            margin-left: 60px;
            width: 30px;
            height: 30px;
            line-height: 26px !important;
            font-size: 30px;
            background-color: #009688;
            color: #fff;
            border-radius: 3px;
            text-align: center;
            cursor: pointer;
        }

        .tip {
            position: relative;
            font-size: 24px;
            text-align: center;
            padding: 30px 0;
        }

        .tip::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 50%;
            width: calc(50% - 200px);
            height: 1px;
            background-color: #c9c9c9;
        }

        .tip::after {
            content: '';
            position: absolute;
            right: 20px;
            top: 50%;
            width: calc(50% - 200px);
            height: 1px;
            background-color: #c9c9c9;
        }

        .submit-btn1,
        .submit-btn2,
        .submit-btn3,
        .submit-btn4,
        .submit-btn5 {
            text-align: right;
            margin-right: 50px;
            padding: 30px 0;
        }

        @media screen and (max-width: 768px) {
            .edit-wrapper .layui-content .layui-form .items .form-text {
                text-align: left;
            }
            .layui-input-block {
                margin-left: 0;
            }
            .edit-wrapper .layui-content .layui-form .items .form-right {
                padding-left: 0 !important;
            }
            .level-name li .reduce {
                left: 215px;
            }
            .level-name .add {
                margin-left: 10px;
            }

            .edit-wrapper .layui-content .layui-form .input-inf2 {
                min-width: 190px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid edit-wrapper layui-container">
        <header class="edit-title">
            <blockquote class="layui-elem-quote">
                <span class="title">网站配置列表</span>
            </blockquote>
        </header>
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <ul class="layui-tab-title">
                <li class="layui-this">基础配置</li>
                <li>公司信息.国内</li>
                <li>公司信息.国外</li>
                <?php if( $aid == 1 ): ?>
                <!-- <li>级别配置</li>
                <li>系统配置</li>
                <?php endif;?>
                <li>公众号配置</li>
                <li>微信支付配置</li>
                <li>消息模板配置</li>
                <?php if( $aid == 1 ): ?>
                <li>快递鸟配置</li>
                <li>返利配置</li>
                <li>功能模块配置</li>
                <li>品牌商城返利配置</li>
                <li>基本配置</li>
                <li>用户配置</li>
                <li>订单配置</li>
                <li>资金配置</li>
                <li>积分配置</li> -->
                <?php endif;?>
            </ul>

            <!-- ********************************* 基础配置 ********************************** -->

            <div class="layui-tab-content layui-content">
                <div class="layui-tab-item layui-show">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <!-- <h4>基础配置</h4> -->
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">域名：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="domain-name" type="text" value="" autocomplete="off" title="请输入域名" name="YM_DOMAIN"
                                        placeholder="请输入域名">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">系统名称：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="system-name" type="text" value="" autocomplete="off" title="请输入系统名称" name="SYSTEM_NAME"
                                        placeholder="请输入系统名称" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">前台logo链接</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="logo-url" type="text" value="" autocomplete="off" title="请输入前台logo链接" name="LOGO_URL"
                                        placeholder="请输入前台logo链接" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text layui-col-xs12">前台logo</label>
                            <div class="form-right">
                                
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger indexlogo-upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="1" data-url="__ROOT__<?php echo ($logo_img_path); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="logoimage" value="<?php echo ($logo_img_path); ?>" />
  </div>
</div>
                                <input type="hidden" name="logo_src" class="image-name" />
                                <small class="orange-text">（*请上传正方形的图片 图片大小在：150*150 图片类型为：png jpg gif jpeg）</small>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text layui-col-xs12">后台logo</label>
                            <div class="form-right">
                                
<!--
上传图片页面
-->
<div class="wrapper">
  <input class="input-inf2" type="text" name="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" class="layui-input">
  <button type="button" class="layui-btn orange layui-btn-danger upload-btn"><i class="layui-icon">&#xe67c;</i>上传图片</button>
  <div class="layui-upload layui-inline">
    <div class="layui-upload-list" data-show="1" data-url="__ROOT__<?php echo ($img_path); ?>">
      <img class="layui-upload-img">
      <p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p>
    </div>
    <input type="hidden" class="image-name" name="image" value="<?php echo ($img_path); ?>" />
  </div>
</div>
                                <input type="hidden" name="logo_src" class="image-name" />
                                <small class="orange-text">（*请上传正方形的图片 图片大小在：150*150 图片类型为：png）</small>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn first-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>

                    <!-- <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                      
                    </form> -->
                </div>
            <!-- ********************************* 公司信息 国内 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">位置：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_position-name" type="text" value="" autocomplete="off" title="请输入位置" name="T_POSITION"
                                        placeholder="请输入位置">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">地址：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_address-name" type="text" value="" autocomplete="off" title="请输入地址" name="T_ADDRESS"
                                        placeholder="请输入地址">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">电话：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_tel-name" type="text" value="" autocomplete="off" title="请输入电话" name="T_TEL"
                                        placeholder="请输入电话">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">邮箱：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_email-name" type="text" value="" autocomplete="off" title="请输入邮箱" name="T_EMAIL"
                                        placeholder="请输入邮箱">
                                </div>
                            </div>
                        </div>


                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn first-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>

                    <!-- <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                      
                    </form> -->
                </div>


            <!-- ********************************* 公司信息 国外 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">位置：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_en_position-name" type="text" value="" autocomplete="off" title="请输入位置" name="T_EN_POSITION"
                                        placeholder="请输入位置">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">地址：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_en_address-name" type="text" value="" autocomplete="off" title="请输入地址" name="T_EN_ADDRESS"
                                        placeholder="请输入地址">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">电话：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_en_tel-name" type="text" value="" autocomplete="off" title="请输入电话" name="T_EN_TEL"
                                        placeholder="请输入电话">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">邮箱：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input class="input-inf2 layui-input" id="t_en_email-name" type="text" value="" autocomplete="off" title="请输入邮箱" name="T_EN_EMAIL"
                                        placeholder="请输入邮箱">
                                </div>
                            </div>
                        </div>


                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn first-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>

                    <!-- <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                      
                    </form> -->
                </div>

                <!-- ********************************* 级别配置 ********************************** -->
                <?php if( $aid == 1 ): ?>
                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <!-- <h4>级别配置</h4> -->
                        <div style="font-size: 24px; padding: 30px 0;text-align: center;">注意代理数量太多时更改级别名可能会出现超时等问题</div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12" style="flex: none;">经销商级别数：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="level-num" value="" name='LEVEL_NUM' type="number" autocomplete="off" title="请输入经销商级别数"
                                    placeholder="请输入经销商级别数" disabled>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row level-name" id="level-name">
                            <label class="form-text layui-col-xs12">经销商级别名:</label>
                            <ul>
                                <!-- <li>
                                    <div class="form-right layui-col-xs12">
                                        <input class="input-inf2 layui-input"  value="<?php echo ($level_name); ?>" name='level_name[]' type="text" autocomplete="off" title="请输入经销商级别名" placeholder="请输入经销商级别名" class="layui-input">
                                    </div>
                                    <span>-</span>
                                </li>
                                <li>
                                    <div class="form-right layui-col-xs12">
                                        <input class="input-inf2 layui-input"  value="<?php echo ($level_name); ?>" name='level_name[]' type="text" autocomplete="off" title="请输入经销商级别名" placeholder="请输入经销商级别名" class="layui-input">
                                    </div>
                                </li> -->
                                <span class="add">+</span>
                            </ul>

                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- ********************************* 系统配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <!-- <h4>系统配置</h4> -->
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">发展模式：</label>
                            <div class="layui-input-block">
                                <select name="GROW_MODEL" id="grow_model" lay-filter="pattern">
                                    <option value=""></option>
                                    <option value="1">高发展低</option>
                                    <option value="2">高发展低及平级推</option>
                                    <option value="3">任意级别发展</option>
                                    <option value="4">特定级别规则</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row " id="patternList">
                            <label class="form-text layui-col-xs12">不同级别的发展模式：</label>
                            <div class="layui-input-block select-list">
                                <ul>
                                    
                                </ul>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">审核方式：</label>
                            <div class="layui-input-block">
                                <select name="AUDIT_WAY" id="audit_way" lay-filter="check" >
                                    <option value=""></option>
                                    <option value="1">上级审核</option>
                                    <option value="2">上级审核后总部审核</option>
                                    <option value="3">特定审核规则</option>
                                    <option value="4">直接总部审核</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row " id="is_audit">
                            <label class="form-text layui-col-xs12">总部可审上级审核：</label>
                            <div class="layui-input-block">
                                <select name="IS_AUDITED" id="is_audited" name="IS_AUDITED">
                                    <option value="">不可审核</option>
                                    <option value="1">可审核</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row " id="checkList">
                            <label class="form-text layui-col-xs12">不同级别的审核方式：</label>
                            <div class="layui-input-block select-list">
                                <ul>
                                     <!--<li>
                                        <select name="AUDIT_WAY_LEVEL" id="audit_way_level">
                                            <option value=""></option>
                                            <option value="1">上级审核</option>
                                            <option value="2">总部审核</option>
                                        </select>
                                    </li> -->
                                </ul>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否提交图片：</label>
                            <div class="layui-input-block">
                                <!-- <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入是否提交图片" placeholder="请输入是否提交图片" class="layui-input"> -->
                                <select id="selectImg" name="IS_SUBMIT_ID_CARD_IMG">
                                    <option value="">请选择提交图片类型</option>
                                    <option value="0">不提交图片</option>
                                    <option value="1">都要提交</option>
                                    <option value="2">只提交身份证截图</option>
                                    <option value="3">只提交保证金截图</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">业绩统计方式：</label>
                            <div class="layui-input-block">
                                <!-- <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入是否提交图片" placeholder="请输入是否提交图片" class="layui-input"> -->
                                <select id="selectWay" name="MONEY_COUNT_WAY">
                                    <option value="0">按虚拟币</option>
                                    <option value="1">按订单金额</option>
                                    <option value="2">按订单数量</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endif;?>
                <!-- ********************************* 公众号配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">微信功能：</label>
                            <div class="layui-input-block">
                                <select id="app_test" name="APP_TEST">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                            </div>
                        </div>
                        <!-- <h4>公众号配置</h4> -->
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">公众号应用ID：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="APP_ID" id="app-id" type="text" autocomplete="off" title="请输入公众号应用ID" placeholder="请输入公众号应用ID"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">公众号密钥：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="APP_SECRET" id="app-secret" type="text" autocomplete="off" title="请输入公众号密钥" placeholder="请输入公众号密钥"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text layui-col-xs12">mp文件上传：</label>
                            <div class="form-right">
                                <button type="button" class="layui-btn layui-btn-normal" id="btn_mp" name="mp_file">
                                    <i class="layui-icon">&#xe67c;</i>上传mp文件
                                </button>
                            </div>
                        </div>
                        
                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 微信支付配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">微信支付商户号：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="MCHID" id="mch-id" type="text" autocomplete="off" title="请输入微信支付商户号(MCHID)" placeholder="请输入微信支付商户号(MCHID)"
                                       class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">微信支付API密钥(32位)：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="KEY" id="key" type="text" autocomplete="off" title="请输入微信支付密钥" placeholder="请输入微信支付密钥"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">JSAPI支付授权目录(左边栏)：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" value="http://"  type="text" autocomplete="off" class="layui-input" disabled="disabled">
                            </div>
                        </div>
                        <?php if( C('FUNCTION_MODULE')['MONEY'] == true || $aid==1): ?>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">JSAPI支付授权目录(虚拟币充值)右边栏：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" value="<?php echo (C("YM_DOMAIN")); ?>/admin/fundspay"  type="text" autocomplete="off" class="layui-input" disabled="disabled">
                            </div>
                        </div>
                        <?php endif;?>
                        <?php if( C('FUNCTION_MODULE')['MALL_SHOP'] == true || $aid==1): ?>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">JSAPI支付授权目录(品牌商城充值)右边栏：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" value="<?php echo (C("YM_DOMAIN")); ?>/sale/mallwxpay"  type="text" autocomplete="off" class="layui-input" disabled="disabled">
                            </div>
                        </div>
                        <?php endif;?>
                        <?php if( C('FUNCTION_MODULE')['INTEGRAL_SHOP'] == true || $aid==1): ?>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">JSAPI支付授权目录(积分商城充值)右边栏：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" value="<?php echo (C("YM_DOMAIN")); ?>/sale/integralpay"  type="text" autocomplete="off" class="layui-input" disabled="disabled">
                            </div>
                        </div>
                        <?php endif;?>
                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 消息模板配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <!-- <h4>消息模板配置</h4> -->
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">系统消息：</label>
                            <div class="layui-input-block">
                                <select id="msg_m_system" name="MESSAGE_MODULE_SYSTEM">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">代理消息：</label>
                            <div class="layui-input-block">
                                <select id="msg_m_distributor" name="MESSAGE_MODULE_DISTRIBUTOR">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">审核模板：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="SH_MB" id="sh-mb" type="text" autocomplete="off" title="请输入审核模板" placeholder="请输入审核模板"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">申请模板：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="SQ_MB" id="sq-mb" type="text" autocomplete="off" title="请输入申请模板" placeholder="请输入申请模板"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">新订单：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="NEW" id="new" type="text" autocomplete="off" title="请输入新订单" placeholder="请输入新订单"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">取消订单：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="CANCLE" id="cancle" type="text" autocomplete="off" title="请输入取消订单" placeholder="请输入取消订单"
                                    class="layui-input">
                            </div>
                        </div>

                        <!-- <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">取消订单：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入取消订单" placeholder="请输入取消订单" class="layui-input">
                            </div>
                        </div> -->

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">审核订单：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="AUDIT" id="audit" type="text" autocomplete="off" title="请输入审核订单" placeholder="请输入审核订单"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">虚拟币申请/审核：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="MONEY_MB" id="money-mb" type="text" autocomplete="off" title="请输入虚拟币申请/审核" placeholder="请输入虚拟币申请/审核"
                                    class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">升级申请：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="UPGRADE_APPLY_MB" id="upgrade-mb" type="text" autocomplete="off" title="请输入代理升级申请模板" placeholder="请输入代理升级申请模板"
                                    class="layui-input">
                            </div>
                        </div>
                        
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">升级通过：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="UPGRADE_PASS_MB" id="upgrade-apply" type="text" autocomplete="off" title="请输入代理升级通过模板" placeholder="请输入代理升级通过模板"
                                    class="layui-input">
                            </div>
                        </div>


                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit>立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                
                <!-- ********************************* 快递鸟接口（电子面单） ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__GROUP__/Webset/update_webset">
                        <!-- <h4>消息模板配置</h4> -->
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">快递鸟EBusinessID：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="EBusinessID" id="sh-mb" type="text" autocomplete="off" title="快递鸟EBusinessID" placeholder="请输入快递鸟EBusinessID" value="<?php echo (C("kdnapi.EBusinessID")); ?>" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">快递鸟AppKey：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="AppKey" id="sq-mb" type="text" autocomplete="off" title="快递鸟AppKey" placeholder="请输入快递鸟AppKey" value="<?php echo (C("kdnapi.AppKey")); ?>" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">电子面单：</label>
                            <div class="layui-input-block">
                                <select id="kdnian_order" name="KDNIAO_ORDER">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">配送单：</label>
                            <div class="layui-input-block">
                                <select id="send_order" name="SEND_ORDER">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">电子面单产品价格：</label>
                            <div class="layui-input-block">
                                <!-- <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入是否提交图片" placeholder="请输入是否提交图片" class="layui-input"> -->
                                <select id="kdorder_price" name="KDORDER_PRICE" lay-filter="kdorder_price">
                                    <option value="">请选择</option>
                                    <option value="1">零售价</option>
                                    <option value="2">代理价</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">厂家发货人姓名：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="BOSS_NAME" id="bossname" type="text" autocomplete="off" title="厂家发货人姓名" placeholder="请输入厂家发货人姓名" value="<?php echo (C("BOSS.NAME")); ?>">
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">厂家发货地址：</label>
                            <div class="form-right flex special-flex">
                                <div class="layui-input-inline">
                                    <select class="select-hook" name="BOSS_PROVINCE" id="province" lay-filter="province" lay-search="" >
                                    </select>
                                    <!--<input type="text" autofocus name="address" value='<?php echo ($vo["idennum"]); ?>' pattern="^\S{1,}$" required=""  title="请输入地址" placeholder="请输入地址" class="layui-input">-->
                                </div>
                                <div class="layui-input-inline">
                                    <select class="select-hook" name="BOSS_CITY" id="city" lay-filter="city" lay-search="" >
                                    </select>
                                    <!--<input type="text" autofocus name="address" value='<?php echo ($vo["idennum"]); ?>' pattern="^\S{1,}$" required=""  title="请输入地址" placeholder="请输入地址" class="layui-input">-->
                                </div>
                                <div class="layui-input-inline">
                                    <select class="select-hook" name="BOSS_COUNTY" id="county" lay-filter="county" lay-search="" >
                                    </select>
                                    <!--<input type="text" autofocus name="address" value='<?php echo ($vo["idennum"]); ?>' pattern="^\S{1,}$" required=""  title="请输入地址" placeholder="请输入地址" class="layui-input">-->
                                </div>
                            </div>
                            
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">厂家发货详细地址：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="BOSS_DETAIL" id="boss_detail" type="text" autocomplete="off" title="厂家发货详细地址" placeholder="请输入厂家发货详细地址" value="<?php echo (C("BOSS.DETAIL")); ?>">
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">厂家发货电话：</label>
                            <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" name="BOSS_PHONE" id="boss_phone" type="number" autocomplete="off" title="厂家发货电话" placeholder="请输入厂家发货电话" value="<?php echo (C("BOSS.PHONE")); ?>">
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">邮费支付方式：</label>
                            <div class="layui-input-block">
                                <!-- <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入是否提交图片" placeholder="请输入是否提交图片" class="layui-input"> -->
                                <select id="shipper_paytype" name="SHIPPER_PAYTYPE" lay-filter="shipper_paytype">
                                    <option value="">请选择</option>
                                    <option value="1">现付</option>
                                    <option value="2">到付</option>
                                    <option value="3">月结</option>
                                    <option value="4">第三方支付</option>
                                </select>
                            </div>
                        </div>
                        <?php if(is_array($kdn_code)): foreach($kdn_code as $k=>$vo): ?><div class="layui-form-item items layui-row s_paytype">
                                <label class="form-text layui-col-xs12"><?php echo ($vo); ?></label>
                                <div class="form-right layui-col-xs12">
                                    <input class="input-inf2 layui-input" name="SHIPPER_CODE[<?php echo ($k); ?>]" id="<?php echo ($k); ?>" type="text" autocomplete="off" title="<?php echo ($vo); ?>月结码" placeholder="请输入<?php echo ($vo); ?>月结码" value="<?php echo (C("SHIPPER_CODE.$k")); ?>">
                                </div>
                            </div><?php endforeach; endif; ?>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit="" lay-filter="*">立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>
                

                <!-- ********************************* 返利配置 ********************************** -->
                <?php if( $aid == 1 ): ?>
                <div class="layui-tab-item">
                    <form class="layui-form layui-box layui-form layui-box" method="post" action="__URL__/set_rebate_config" data-auto="false">
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">返利总开关</label>
                            <div class="form-right">
                                <input type="checkbox" name="OPEN" value="1" id="rb_switch" lay-skin="switch" lay-filter="rb_switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">平级推荐订单返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="ORDER" value="1" id="order_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">平级推荐充值返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="MONEY" value="1" id="money_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">低推高一次性返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="ONCE" value="1" id="once_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">平级发展一次性返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="SAME_DEVELOPMENT" value="1" id="same_development_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">高发展低一次性返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="DEVELOPMENT" value="1" id="development_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">个人业绩返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="PERSONAL" value="1" id="personal_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">团队业绩返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="ORDINARY_TEAM" value="1" id="team_sw" lay-skin="switch" lay-filter="team_switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
<!--                        <div class="layui-form-item items" id="time">
                            <label class="form-text" style="flex: 1;">月季返利</label>
                            <div class="form-right">
                                <div class="layui-input-inline">
                                    <input type="radio" name="CLICK_TEAM_REBATE" checked="checked" id="time_team_sw"  lay-filter="rb1_ap" value="0" lay-skin="primary" title="实时生成" checked/>
                                </div>
                                <div class="layui-input-inline">
                                    <input type="radio" name="CLICK_TEAM_REBATE" id="click_team_sw" lay-filter="rb1_am" value="1" lay-skin="primary" title="定时生成" />
                                </div>
                            </div>
                        </div>-->

                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit="" lay-filter="*">立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 功能模块配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__URL__/set_function_module_config">
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">虚拟币模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[MONEY]" value="1" id="money-module" lay-skin="switch"  lay-filter="money_switch" checked lay-text="开启|关闭"/>
                            </div>
                        </div>

                        <div class="layui-form-item items" id="money_apply_pay_type">
                            <label class="form-text" style="flex: 1;">虚拟币充值的支付方式</label>
                            <div class="form-right">
                                <div class="layui-input-inline">
                                    <input type="radio" name="FUNCTION_MODULE[MONEY_APPLY_PAY_TYPE]"
                                           id="money_xia" lay-filter="rb1_ap" value="0" lay-skin="primary" title="线下支付" checked/>
                                </div>
                                <div class="layui-input-inline">
                                    <input type="radio" name="FUNCTION_MODULE[MONEY_APPLY_PAY_TYPE]" id="money_online" lay-
                                           filter="rb1_am" value="1" lay-skin="primary" title="在线支付" />
                                </div>
                                <div class="layui-input-inline">
                                    <input type="radio" name="FUNCTION_MODULE[MONEY_APPLY_PAY_TYPE]" id="money_all" lay-
                                           filter="rb1_am" value="2" lay-skin="primary" title="线下支付+在线支付" />
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">积分商城模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[INTEGRAL_SHOP]" value="1" id="integral-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">优惠商城模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[MALL_SHOP]" value="1" id="mall-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">出库模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[STOCK]" value="1" id="stock-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">营销模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[MARKET]" value="1" id="market-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">微官网模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[GW]" value="1" id="gw-module" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">团队模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[TEAM]" value="1" id="team-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">仓库模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[DEPOT]" value="1" id="DEPOT-module" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">总部下单模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[BOSS_ORDER]" value="1" id="boss-order-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        
                        
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">库存下单模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[STOCK_ORDER]" value="1" id="STOCK_ORDER-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">产品规格模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[ORDER_FORMAT]" value="1" id="ORDER_FORMAT-module" lay-skin="switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">运费模板模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="ORDER_SHIPPING" value="1" id="ORDER_SHIPPING-module" lay-skin="switch" lay-filter="shipping_switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">店中店模块</label>
                            <div class="form-right">
                                <input type="checkbox" name="FUNCTION_MODULE[SHOP_IN_SHOP]" value="1" id="shopInShop-module" lay-skin="switch" checked lay-text="开启|关闭"/>
                            </div>
                        </div>
                        
                        <div class="layui-form-item items" id="shipping">
                            <label class="form-text" style="flex: 1;">运费满减</label>
                            <div class="form-right">
                                <div class="layui-input-inline">
                                    <input type="radio" name="SHIPPING_REDUCE_WAY"
                                           id="shipping_reduce_way_all" lay-filter="rb1_ap" value="0" lay-skin="primary" title="全场" checked/>
                                </div>
                                <div class="layui-input-inline">
                                    <input type="radio" name="SHIPPING_REDUCE_WAY" id="shipping_reduce_way_one" lay-
                                           filter="rb1_am" value="1" lay-skin="primary" title="指定商品" />
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit="">立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 品牌商城返利配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box layui-form layui-box" method="post" action="__URL__/set_mall_rebate_config" data-auto="false">
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">返利开关</label>
                            <div class="form-right">
                                <input type="checkbox" name="OPEN" value="1" id="mall_rb_switch" lay-skin="switch" lay-filter="mall_rb_switch" checked lay-text="开启|关闭"
                                />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">订单返利</label>
                            <div class="form-right">
                                <input type="checkbox" name="ORDER" value="1" id="mall_order_sw" lay-skin="switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;">提现开关</label>
                            <div class="form-right">
                                <input type="checkbox" name="IS_OPEN" value="1" id="mall_refund_sw" lay-skin="switch" lay-filter="mall_refund_switch" checked lay-text="开启|关闭" />
                            </div>
                        </div>
                        <div class="layui-form-item items" id="refund">
                            <label class="form-text" style="flex: 1;">提现方式</label>
                            <div class="form-right">
                                <div class="layui-input-inline">
                                    <input type="radio" name="MALL_REFUND_PAY_TYPE" checked="checked" id="mall_re_bank" lay-filter="rb1_ap" value="0" lay-skin="primary" title="转账到指定账号" checked/>
                                </div>
                                <div class="layui-input-inline">
                                    <input type="radio" name="MALL_REFUND_PAY_TYPE" id="mall_re_wx" lay-filter="rb1_am" value="1" lay-skin="primary" title="直接提现到微信" />
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items">
                            <label class="form-text" style="flex: 1;"></label>
                            <div class="form-right" style="text-align:right; max-width: 93%;">
                                <button class="layui-btn" lay-submit="" lay-filter="*">立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 基本配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post" action="__URL__/update_webset">
                        <!-- <h4>基本配置</h4> -->
                        <div class="tip">以下内容仅供参考，不能修改</div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否开启测试模式：</label>
                            <div class="form-right layui-col-xs12">
                                <input type="checkbox" name="IS_TEST" value="1" id="is-test" lay-skin="switch" checked lay-text="开启|关闭"
                             disabled />
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否开启调试模式：</label>
                            <div class="form-right layui-col-xs12">
                                <input disabled class="input-inf2 layui-input" id="app-debug" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false"
                                       class="layui-input">
                            </div>
                        </div>
<!--                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">统计业绩方式：</label>
                            <div class="form-right layui-col-xs12">
                                <input disabled class="input-inf2 layui-input" id="money-count-way" type="text" autocomplete="off" title="" placeholder="虚拟币/订单金额/订单数量"
                                    class="layui-input">
                            </div>
                        </div>-->

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">如何定义团队关系：</label>
                            <div class="form-right layui-col-xs12">
                                <input disabled class="input-inf2 layui-input" id="default-team" type="text" autocomplete="off" title="团队是根据上下级关系还是推荐人关系定义"
                                    placeholder="团队是根据上下级关系还是推荐人关系定义" class="layui-input">
                            </div>
                        </div>
                        <!--<div class="layui-form-item items">-->
                            <!--<label class="form-text"></label>-->
                            <!--<div class="form-right" style="text-align:right; max-width: 93%;">-->
                                <!--<button class="layui-btn" lay-submit="">立即提交</button>-->
                            <!--</div>-->
                        <!--</div>-->
                    </form>
                </div>

                <!-- ********************************* 用户配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post">
                        <!-- <h4>用户配置</h4> -->
                        <div class="tip">以下内容仅供参考，不能修改</div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否多层级：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-multilayer" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false"
                                        class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否生成用户关系：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="has-user-bind" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false"
                                        class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否获得多层代理关系：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-cycle-multilayer" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 订单配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post">
                        <!-- <h4>订单配置</h4> -->
                        <div class="tip">以下内容仅供参考，不能修改</div>
                        <div class="layui-form-item items layui-row status-name" id="status-name">
                            <label class="form-text layui-col-xs12">订单状态:</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="status-name" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false" class="layui-input">
                            </div> -->
                            <ul>
                            </ul>
                        </div>

                        <div class="layui-form-item items layui-row all-pay-type" id="all-pay-type">
                            <label class="form-text layui-col-xs12">支付方式：</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false" class="layui-input">
                            </div> -->
                            <ul></ul>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否生成订单统计表：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-generate-order-count" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否总部供货：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-top-supply" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false"
                                        class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row is-top-supply-level" id="is-top-supply-level">
                            <label class="form-text layui-col-xs12">根据级别判断供货方式：</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="is-top-supply-level" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false" class="layui-input">
                            </div> -->
                            <ul></ul>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否启用下单限制：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="opent-order-limit" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 资金配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post">
                        <!-- <h4>资金配置</h4> -->
                        <div class="tip">以下内容仅供参考，不能修改</div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否进行虚拟币系统的逻辑:</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-charge-money" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row is-charge-money-level" id="is-charge-money-level">
                            <label class="form-text layui-col-xs12">根据值判断是否进行虚拟币功能逻辑：</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="is-charge-money-level" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false" class="layui-input">
                            </div> -->
                            <ul></ul>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否充值金额都可以提现：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-all-can-refund" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否使用获取最低申请金额：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-get-min-apply-money" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否使用获取最低提现金额：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-get-min-refund-money" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">订单扣费资金动向：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-parent-order" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否由直属上级审核虚拟币：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-parent-audit" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否启用订单返还：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-order-return" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">订单返还循环的次数：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="order-return-rank" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">返利是否充入账户：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="is-rebate-recharge" type="text" autocomplete="off" title="请输入true或false"
                                        placeholder="请输入true或false" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ********************************* 积分配置 ********************************** -->

                <div class="layui-tab-item">
                    <form class="layui-form layui-box" data-auto="false" method="post">
                        <!-- <h4>积分配置</h4> -->
                        <div class="tip">以下内容仅供参考，不能修改</div>
                        <div class="layui-form-item items layui-row">
                            <label class="form-text layui-col-xs12">是否开启积分功能：</label>
                            <div class="form-right layui-col-xs12">
                                <div class="layui-input-inline">
                                    <input disabled class="input-inf2 layui-input" id="integral-open" type="text" autocomplete="off" title="请输入true或false" placeholder="请输入true或false"
                                        class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item items layui-row integral-status" id="integral-status">
                            <label class="form-text layui-col-xs12">日志类型：</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="integral-status" type="text" autocomplete="off" title="请输入虚拟币或订单金额" placeholder="请输入虚拟币或订单金额" class="layui-input">
                            </div> -->
                            <ul></ul>
                        </div>

                        <div class="layui-form-item items layui-row integral-rule-typ" id="integral-rule-typ">
                            <label class="form-text layui-col-xs12">积分规则类型：</label>
                            <!-- <div class="form-right layui-col-xs12">
                                <input class="input-inf2 layui-input" id="integral-rule-typ" type="text" autocomplete="off" title="团队是根据上下级关系还是推荐人关系定义" placeholder="团队是根据上下级关系还是推荐人关系定义" class="layui-input">
                            </div> -->
                            <ul></ul>
                        </div>
                    </form>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <script src="__PUBLIC__/Radmin_v3/js/webset.js"></script>
</body>

</html>