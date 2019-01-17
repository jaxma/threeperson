<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>首页</title>
    <!--<link rel="stylesheet" href="__PUBLIC__/Radmin_v2/css/reset.css" />-->
    <!-- <link rel="stylesheet" type="text/css" href="__PUBLIC__/Radmin_v3/css/analysis.css" /> -->
    <style>
/*    .table_Box {border:1px solid #BDC0D1;}
    .table_Box{margin-bottom:18px;padding:20px;}
    .table_Box table th{text-align: left;background: url('__PUBLIC__/Radmin_v3/images/icon_site.gif') no-repeat 20px center;padding: 0 0 0 40px;}*/
    /*.container-fluid{background: url('__PUBLIC__/Radmin_v3/images/welcome.png');background-repeat:no-repeat; background-size: 100% 100%;-moz-background-size: 100% 100%; -webkit-background-size: 100% 100%;opacity: 0.92;}*/
    </style>
    <script type="text/javascript">
      var distributor = '__GROUP__/Analysis/count_distributor';
      var order_money = '__GROUP__/Analysis/count_order_money';
    </script>
  </head>

  <body>
    <div class="container-fluid edit-wrapper layui-container">
      <header class="edit-title">
        <blockquote class="layui-elem-quote">
          <span class="title">欢迎进入<?php echo (C("SYSTEM_NAME")); ?>管理系统</span>
        </blockquote>
      </header>
    <!--   <div class="layui-content clearfloat">
        <dl class="analysis-wrapper clearfloat">
          <dt class="analysis-top clearfloat">
        	  <h1 class="title">经销商模块</h1>
        	  <ul class="agent-count layui-row">
        	    <li class="layui-col-xs12" style="margin-bottom: 15px;">
        	        <img src="__PUBLIC__/Radmin_v3/images/icons-1_01.png" alt="" />
          	      <p class="num"><?php echo ($total_users); ?></p>
          	      <p class="title">经销商总数量</p>
        	    </li>
        	    <li class="layui-col-xs12" style="margin-bottom: 15px;">
        	      <img src="__PUBLIC__/Radmin_v3/images/icons-1_02.png" alt="" />
                <p class="num"><?php echo ($no_head_audited_users); ?></p>
                <p class="title">待总部审核数量</p>
        	    </li>
                <li class="layui-col-xs12" style="margin-bottom: 15px;">
        	      <img src="__PUBLIC__/Radmin_v3/images/icons-1_10.png" alt="" />
                <p class="num"><?php echo ($no_agent_audited_users); ?></p>
                <p class="title">待上级审核数量</p>
        	    </li>
        	    <li class="layui-col-xs12" style="margin-bottom: 15px;">
        	      <img src="__PUBLIC__/Radmin_v3/images/icons-1_03.png" alt="" />
                <p class="num"><?php echo ($yes_audited_users); ?></p>
                <p class="title">已审核数量</p>
        	    </li>
        	    <li class="layui-col-xs12" style="margin-bottom: 15px;">
        	      <img src="__PUBLIC__/Radmin_v3/images/icons-1_04.png" alt="" />
                <p class="num"><?php echo ($day_users); ?></p>
                <p class="title">今日增长数量</p>
        	    </li>
        	  </ul>
              
              <ul class="agent-count layui-row">
                  <?php if(is_array($level_name)): $i = 0; $__LIST__ = $level_name;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($i) <= "5"): ?><li class="layui-col-xs12">
                          <img src="__PUBLIC__/Radmin_v3/images/level<?php echo ($i); ?>.png" alt="" />
                          <p class="num" id="total_orders"><?php echo (($agent[$i])?($agent[$i]):'0'); ?></p>
                          <p class="title"><?php echo ($vo); ?>数量</p>
                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
               </ul>
              <?php if($level_count > 5): ?><ul class="agent-count layui-row">
                  <?php if(is_array($level_name)): $i = 0; $__LIST__ = $level_name;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($i) > "5"): ?><li class="layui-col-xs12">
                          <img src="__PUBLIC__/Radmin_v3/images/other.png" alt="" />
                          <p class="num" id="total_orders"><?php echo (($agent[$i])?($agent[$i]):'0'); ?></p>
                          <p class="title"><?php echo ($vo); ?>数量</p>
                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </ul><?php endif; ?>
        	</dt>
          <dd class="analysis-bottom clearfloat">
              <div class="layui-input-inline timer">
                <input type="text" class="layui-input green" id="agent" readonly="readonly">
                <i class="fa fa-angle-down"></i>
              </div>
            <div id="charts1"></div>
          </dd>
        </dl>
        
        <dl class="analysis-wrapper clearfloat">
          <dt class="analysis-top clearfloat">
            <h1 class="title">订单模块</h1>
            <ul class="agent-count layui-row">
              <li class="layui-col-xs12">
                <img src="__PUBLIC__/Radmin_v3/images/icons-1_05.png" alt="" />
                <p class="num" id="total_orders"><?php echo ($total_order_money); ?></p>
                <p class="title">订单总金额</p>
              </li>
              <li class="layui-col-xs12">
                <img src="__PUBLIC__/Radmin_v3/images/icons-1_06.png" alt="" />
                <p class="num"><?php echo ($no_audited_orders); ?></p>
                <p class="title">待审核订单数量</p>
              </li>
              <li class="layui-col-xs12">
                <img src="__PUBLIC__/Radmin_v3/images/icons-1_09.png" alt="" />
                <p class="num"><?php echo ($yes_audited_orders); ?></p>
                <p class="title">已发货订单数量</p>
              </li>
              <li class="layui-col-xs12">
                <img src="__PUBLIC__/Radmin_v3/images/icons-1_07.png" alt="" />
                <p class="num"><?php echo ($finished_orders); ?></p>
                <p class="title">已收货订单数量</p>
              </li>
              <li class="layui-col-xs12">
                <img src="__PUBLIC__/Radmin_v3/images/icons-1_08.png" alt="" />
                <p class="num"><?php echo ($day_orders); ?></p>
                <p class="title">今日订单数量</p>
              </li>
            </ul>
          </dt>
          <dd class="analysis-bottom clearfloat">
              <div class="layui-input-inline timer">
                <input type="text" class="layui-input blue" id="order" readonly="readonly">
                <i class="fa fa-angle-down"></i>
              </div>
            <div id="charts2"></div>
          </dd>
        </dl>
      </div> -->
 <!--      <div class="table_Box">
            <table cellspacing="0" border="0" cellpadding="0" width="100%">
                <tr><th colspan="4">版本信息</th></tr>
                <tr>
                    <td>目前版本：V3.1.0</td>
                    <td>程序开发：<a href="http://www.toposla.com/index" target="_blank">广州topos科技</a></td>
                    <td>联系方式：http://www.toposla.com/index</td>
                    <td></td>
                </tr>
                <tr>
                    <td>版权声明：</td>
                    <td colspan="3">1、本软件为商业软件；<br/>
            2、您可以对本系统进行修改和美化，但必须保留完整的版权信息，不得将修改后的版本用于任何商业目的；<br/>
            3、本软件受中华人民共和国《著作权法》《计算机软件保护条例》等相关法律、法规保护，作者保留一切权利。<br/>
            4、如有可能，请在您的网站上添加本站链接,</td>
                </tr>
            </table>
        </div>
        <div class="table_Box">
            
            <table cellspacing="0" border="0" cellpadding="0" width="100%">
                <tr><th colspan="4">服务器参数</th></tr>
              <tr>
                <td>服务器域名/IP地址</td>
                <td colspan="3"><?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php if('/'==DIRECTORY_SEPARATOR){echo $_SERVER['SERVER_ADDR'];}else{echo @gethostbyname($_SERVER['SERVER_NAME']);} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?></td>
              </tr>
              <tr>
                <td>服务器标识</td>
                <td colspan="3"><?php if($sysInfo['win_n'] != ''){echo $sysInfo['win_n'];}else{echo @php_uname();};?></td>
              </tr>
              <tr>
                <td width="13%">服务器操作系统</td>
                <td width="37%"><?php $os = explode(" ", php_uname()); echo $os[0];?> &nbsp;内核版本：<?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?></td>
                <td width="13%">服务器解译引擎</td>
                <td width="37%"><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
              </tr>
              <tr>
                <td>服务器语言</td>
                <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE");?></td>
                <td>服务器端口</td>
                <td><?php echo $_SERVER['SERVER_PORT'];?></td>
              </tr>
              <tr>
                  <td>服务器主机名</td>
                  <td><?php if('/'==DIRECTORY_SEPARATOR ){echo $os[1];}else{echo $os[2];} ?></td>
                  <td>绝对路径</td>
                  <td><?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
                </tr>
              <tr>
                  <td>管理员邮箱</td>
                  <td><?php echo $_SERVER['SERVER_ADMIN'];?></td>
                    <td>探针路径</td>
                    <td><?php echo str_replace('\\','/',__FILE__)?str_replace('\\','/',__FILE__):$_SERVER['SCRIPT_FILENAME'];?></td>
                </tr>   
              <tr>
                <td>服务器当前时间</td>
                <td><?php echo date('Y-m-d H:i:s');?></td>
                <td>Session支持：</td>
                <td><?php echo ($session_start); ?></td>
                </tr>   
            </table>
        </div>
        <div class="table_Box">
            <table cellspacing="0" border="0" cellpadding="0" width="100%">
                  <tr><th colspan="4">PHP相关参数</th></tr>
                  <tr>
                    <td width="32%">PHP信息（phpinfo）：</td>
                    <td width="18%">
                        <?php
 $phpSelf = $_SERVER[PHP_SELF] ? $_SERVER[PHP_SELF] : $_SERVER[SCRIPT_NAME]; $disFuns=get_cfg_var("disable_functions"); ?>
                    <?php echo (false!==eregi("phpinfo",$disFuns))? '<font color="red">×</font>' :"<a href='$phpSelf?act=phpinfo' target='_blank'>PHPINFO</a>";?>
                    </td>
                    <td width="32%">PHP版本（php_version）：</td>
                    <td width="18%"><?php echo PHP_VERSION;?></td>
                  </tr>
                  <tr>
                    <td>PHP运行方式：</td>
                    <td><?php echo strtoupper(php_sapi_name());?></td>
                    <td>脚本占用最大内存（memory_limit）：</td>
                    <td><?php echo show("memory_limit");?></td>
                  </tr>
                  <tr>
                    <td>PHP安全模式（safe_mode）：</td>
                    <td><?php echo show("safe_mode");?></td>
                    <td>POST方法提交最大限制（post_max_size）：</td>
                    <td><?php echo show("post_max_size");?></td>
                  </tr>
                  <tr>
                    <td>上传文件最大限制（upload_max_filesize）：</td>
                    <td><?php echo show("upload_max_filesize");?></td>
                    <td>浮点型数据显示的有效位数（precision）：</td>
                    <td><?php echo show("precision");?></td>
                  </tr>
                  <tr>
                    <td>脚本超时时间（max_execution_time）：</td>
                    <td><?php echo show("max_execution_time");?>秒</td>
                    <td>socket超时时间（default_socket_timeout）：</td>
                    <td><?php echo show("default_socket_timeout");?>秒</td>
                  </tr>
                  <tr>
                    <td>PHP页面根目录（doc_root）：</td>
                    <td><?php echo show("doc_root");?></td>
                    <td>用户根目录（user_dir）：</td>
                    <td><?php echo show("user_dir");?></td>
                  </tr>
                  
                   <tr>
                    <td>SMTP支持：</td>
                    <td><?php echo get_cfg_var("SMTP")?'<font color="green">√</font>' : '<font color="red">×</font>';?></td>
                    <td>SMTP地址：</td>
                    <td><?php echo get_cfg_var("SMTP")?get_cfg_var("SMTP"):'<font color="red">×</font>';?></td>
                  </tr> 
                </table>
        </div>
        <div class="table_Box">
            <table cellspacing="0" border="0" cellpadding="0" width="100%">
              <tr><th colspan="4">数据库支持</th></tr>
              <tr>
                <td width="32%">MySQL 数据库：</td>
                <td width="18%"><?php echo isfun("mysql_close");?>
                <?php
 if(function_exists("mysql_get_server_info")) { $s = @mysql_get_server_info(); $s = $s ? '&nbsp; mysql_server 版本：'.$s : ''; $c = '&nbsp; mysql_client 版本：'.@mysql_get_client_info(); echo $s; } ?>
                </td>
                <td width="32%">ODBC 数据库：</td>
                <td width="18%"><?php echo isfun("odbc_close");?></td>
              </tr>
              <tr>
                <td>Oracle 数据库：</td>
                <td><?php echo isfun("ora_close");?></td>
                <td>SQL Server 数据库：</td>
                <td><?php echo isfun("mssql_close");?></td>
              </tr>
            </table>
        </div> -->
    </div>
    </div>
    <!-- <script src="__PUBLIC__/Radmin_v3/js/analysis.js" type="text/javascript" charset="utf-8"></script> -->
  </body>


</html>