<?php
//微信模板消息推送
header("Content-Type: text/html; charset=utf-8");


class Message {
    
    private $is_open = TRUE;//是否开启
    
    private $app_id;
    private $app_secret;
    private $domain;


//    const DELIVERY = 'delivery';//发货
    
    public $order_new = 'order_new';//新订单
    public $order_cancle = 'order_cancle';//取消订单
    public $order_audit = 'order_audit';//审核订单
    
    public $money = 'money';//虚拟币申请/审核

    public $development_apply = 'development_apply';//代理申请

    public $upgrade_apply = 'upgrade_apply';//升级申请
    public $upgrade_pass = 'upgrade_pass';//升级通过通知

    public $audit_manager = 'audit_manager';//审核代理通过
    public $not_audit_manager='not_audit_manager';//审核代理不通过
    
    /**
     * 架构函数
     */
    public function __construct() {
        if (!$this->is_open) {
            return;
        }
        $this->app_id = C('APP_ID');
        $this->app_secret = C('APP_SECRET');
        $this->domain = "http://" . C('YM_DOMAIN');
        
        $options = array(
            'token' => C('APP_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        import("Wechat.Wechat", APP_PATH);
        $this->wechat_obj = new Wechat($options);
        
    }
    
    public function push($openid, $content, $type) {
        
        import('ORG.Net.OrderPush');

//        $sendMsg = new OrderPush($this->app_id, $this->app_secret);
//        if (trim($type) == self::DELIVERY) {
//            $template_id = C('FH_MB');
//            $url = $this->domain . "/admin/order/ddxq/order_num/".$content['order_num'];
//            
//            $order_num = $content['order_num'];
//            $product_name = '产品';
//            $total_num = $content['total_num'];
//            $total_price = $content['total_price'];
//            $sendData = array(
//                'first' => array('value' => ("订单已发货"), 'color' => "#CC0000"),
//                'keyword1' => array('value' => ("$order_num"), 'color' => '#000'),
//                'keyword2' => array('value' => ("$product_name"), 'color' => '#000'),
//                'keyword3' => array('value' => ("$total_num"), 'color' => '#000'),
//                'keyword4' => array('value' => ("$total_price"), 'color' => '#000'),
//                'remark' => array('value' => ("查看订单详情"), 'color' => '#CC0000')
//            );
//        } 
        if (trim($type) == $this->money) {
            //虚拟币申请/审核
            $template_id = C('MONEY_MB');
            
            if ($content['status'] == 0) {
                $url = $this->domain . "/admin/funds/check_apply";
                $tip = '您好，您有一条充值申请';
                $status = '充值申请';
            } else if ($content['status'] == 1) {
                $url = $this->domain . "/admin/funds/check_apply";
                $tip = '您好，您的充值申请已经通过审核';
                $status = '充值成功';
            } else {
                $url = $this->domain . "/admin/funds/check_apply";
                $tip = '您好，您的充值申请未通过审核';
                $status = '充值失败';
            }
            $name = $content['name'];
            $money = $content['apply_money'];
            $sendData = array(
                'first' => array('value' => ("$tip"), 'color' => "#CC0000"),
                'accountType'=>array('value'=>("姓名"),'color'=>'#000'),
                'account'=>array('value'=>("$name"),'color'=>'#000'),
                'amount' => array('value' => ("$money"), 'color' => '#000'),
                'result' => array('value' => ("$status"), 'color' => '#000'),
                'remark' => array('value' => ("查看充值详情"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => $tip,
                'title' => $status,
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        } else if (trim($type) == $this->order_new) {
            //新订单
            $order_mb = C('ORDER_MB');
            $template_id = $order_mb['NEW'];
            $url = $this->domain . "/admin/order/examine";
            
            $date = date('Y-m-d H:i:s', time());
            $name = $content['customer_info']['name'];
            $phone = $content['customer_info']['phone'];
            $money = $content['total_price'];
            $sendData = array(
                'first'=>array('value'=>("您收到了一条新的订单"),'color'=>"#CC0000"),
                'tradeDateTime'=>array('value'=>("$date"),'color'=>'#000'),
                'orderType'=>array('value'=>("下级代理订单"),'color'=>'#000'),
                'customerInfo'=>array('value'=>("$name 手机号码:$phone"),'color'=>'#000'),
                'orderItemName'=>array('value'=>("总金额"),'color'=>'#000'),
                'orderItemData'=>array('value'=>("$money 元"),'color'=>'#000'),
                'remark'=>array('value'=>("点击查看订单详情"),'color'=>'#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => '下级代理'.$name.'已下单，下单金额为'.$money.'元，订单号为'.$content['order_num'],
                'title' => '下级订单消息',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        } else if (trim($type) == $this->order_cancle) {
            //取消订单
            $order_mb = C('ORDER_MB');
            $template_id = $order_mb['CANCLE'];
            $url = $this->domain . "/admin/order/all";
            
            $order_num = $content['order_num'];
            $total_price = $content['total_price'];
            $date = date('Y-m-d H:i:s', $content['time']);
            $name = $content['s_name'];
            $phone = $content['s_phone'];
            $sendData = array(
                'first'=>array('value'=>("尊敬的代理您好！"),'color'=>"#CC0000"),
                'keyword1' => array('value' => ("$order_num"), 'color' => '#000'),
                'keyword2' => array('value' => ("取消"), 'color' => '#000'),
                'keyword3' => array('value' => ("$total_price"), 'color' => '#000'),
                'keyword4' => array('value' => ("$date"), 'color' => '#000'),
                'keyword5' => array('value' => ("$name 手机号码:$phone"), 'color' => '#000'),
                'remark' => array('value' => ("如有疑问,请联系下单代理"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => '订单号：'.$order_num.'金额为：'.$total_price.'已取消',
                'title' => '取消订单',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        } else if (trim($type) == $this->order_audit) {
            //审核订单
            $order_mb = C('ORDER_MB');
            $template_id = $order_mb['AUDIT'];
            $url = $this->domain . "/admin/order/all?part=1";
            
            $order_num = $content['order_num'];
            $date = date('Y-m-d H:i:s', time());
            $sendData = array(
                'first'=>array('value'=>("您的订单已经通过审核！"),'color'=>"#CC0000"),
                'keyword1' => array('value' => ("$order_num"), 'color' => '#000'),
                'keyword2' => array('value' => ("$date"), 'color' => '#000'),
                'keyword3' => array('value' => ("通过"), 'color' => '#000'),
                'remark' => array('value' => ("点击查看订单详情"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => '您的订单已通过审核，订单号：'.$order_num,
                'title' => '订单已审核',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        else if(trim($type) == $this->development_apply){
            //代理申请
            $url = $this->domain . "/Admin/manage/index";
            $template_id = C('SQ_MB');
            $sendTime = date("Y-m-d H:i:s");
            $keyword1 = $content['name'];
            $phone = $content['phone'];

            $sendData = array(
                'first' => array('value' => ("经销商申请通知"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
                'keyword2' => array('value' => ("联系方式：" . $phone . "，申请时间：" . $sendTime), 'color' => '#000'),
                'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => $keyword1.'申请成为'.$LEVEL_NAME[$level].',联系方式：'.$phone,
                'title' => '审核申请',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        else if( trim($type) == $this->upgrade_apply ){
            //升级
            $url = $this->domain . "/admin/funds/check_apply";
            
            $template_id = C('UPGRADE_APPLY_MB');
            $name = $content['name'];
            $phone = $content['phone'];
            $time = $content['time'];
            $levname = $content['levname'];
            $apply_level = $content['apply_level'];
            $apply_time = $content['apply_time'];
            $LEVEL_NAME = C('LEVEL_NAME');
            $apply_levname = $LEVEL_NAME[$apply_level];
            
            $sendData = array(
                'first' => array('value' => ("经销商申请升级通知"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$name"), 'color' => '#000'),       //姓名
                'keyword2' => array('value' => ("$levname"), 'color' => '#000'),    //现在等级
                'keyword3' => array('value' => ("$apply_levname"), 'color' => '#000'),//申请等级
                'keyword4' => array('value' => ("$apply_time"), 'color' => '#000'),//申请时间
                'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
            );
            
            $msgData = array(
                'uid' => $content['id'],
                'content' => $levname.'代理'.$name.'申请升级为'.$apply_levname,
                'title' => '升级申请',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        } else if( trim($type) == $this->upgrade_pass ){
            //升级通过通知
            $url = $this->domain . "/admin/login/";
            
            $template_id = C('UPGRADE_PASS_MB');
            $name = $content['name'];
            $pass_time = date('Y-m-d H:i:s',time());
            $levname = $content['levname'];
            $apply_levname = $content['apply_levname'];
            $sendData = array(
                'first' => array('value' => ("你好，您已升级成功"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$name"), 'color' => '#000'),       //姓名
                'keyword2' => array('value' => ("$levname"), 'color' => '#000'),    //现在等级
                'keyword3' => array('value' => ("$apply_levname"), 'color' => '#000'),//申请等级
                'keyword4' => array('value' => ("$pass_time"), 'color' => '#000'),//通过时间
                'remark' => array('value' => ("点击进入代理后台"), 'color' => '#CC0000')
            );
            
            $msgData = array(
                'uid' => $content['id'],
                'content' => $levname.'代理'.$name.'申请升级为'.$apply_levname,
                'title' => '升级通过通知',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
            
        }
        else if(trim($type) == $this->audit_manager){
            //审核代理通过
            $url = $this->domain . "/index.php/Admin/";
            $template_id = C('SH_MB');
            $sendTime = date("Y-m-d H:i:s");
            $SYSTEM_NAME = C('SYSTEM_NAME');
            $uName=$content['name'];
            $keyword1=$content['name'];
            $phone=$content['phone'];
            $bname = $content['bossname'];

            $sendData = array(
                'first' => array('value' => ("$uName,您的" . $SYSTEM_NAME . "微商管理系统经销商审核成功！"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
                'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
                'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
                'remark' => array('value' => ("欢迎您加入" . $SYSTEM_NAME . "微商管理系统。您的直属上级:" . $bname . "。"), 'color' => '#CC0000')
            );
            $levname = C('LEVEL_NAME');
            $level = $content['level'];
            $msgData = array(
                'uid' => $content['id'],
                'content' => '恭喜'.$uName.'成为'.$SYSTEM_NAME.'品牌'.$levname[$level].'。您的直属上级：'.$bname,
                'title' => '审核通过',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        else if(trim($type) == $this->not_audit_manager){
            //审核代理不通过
            $sendTime = date("Y-m-d H:i:s");
            $template_id = C('SH_MB');
            $url = $this->domain . "/index.php/Admin/";
            $SYSTEM_NAME = C('SYSTEM_NAME');
            $uName=$content['name'];
            $keyword1=$content['name'];
            $phone=$content['phone'];
            $sendData = array(
                'first' => array('value' => ("$uName,您的".$SYSTEM_NAME."微商管理系统经销商审核不通过！"), 'color' => "#CC0000"),
                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
                'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
                'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
                'remark' => array('value' => ("具体原因请联系上级或总部了解情况"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => $uName.'申请为代理失败，具体情况请联系上级或总部了解情况',
                'title' => '审核不通过',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        else if (trim($type) == $this->stock_order_new) {
            //新订单
            $order_mb = C('ORDER_MB');
            $template_id = $order_mb['NEW'];
            $url = $this->domain . "/admin/order/examine_stock";

            $date = date('Y-m-d H:i:s', time());
            $name = $content['customer_info']['name'];
            $phone = $content['customer_info']['phone'];
            $money = $content['total_price'];
            $sendData = array(
                'first'=>array('value'=>("您收到了一条新的云仓库存订单"),'color'=>"#CC0000"),
                'tradeDateTime'=>array('value'=>("$date"),'color'=>'#000'),
                'orderType'=>array('value'=>("下级代理订单"),'color'=>'#000'),
                'customerInfo'=>array('value'=>("$name 手机号码:$phone"),'color'=>'#000'),
                'orderItemName'=>array('value'=>("总金额"),'color'=>'#000'),
                'orderItemData'=>array('value'=>("$money 元"),'color'=>'#000'),
                'remark'=>array('value'=>("点击查看云仓库存订单详情"),'color'=>'#CC0000')
            );
            
            $msgData = array(
                'uid' => $content['id'],
                'content' => '下级代理'.$name.'已下单，下单金额为'.$money.'元，订单号为'.$content['order_num'],
                'title' => '下级云仓订单消息',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        else if( trim($type) == $this->stock_audit ){
            //审核订单
            $order_mb = C('ORDER_MB');
            $template_id = $order_mb['AUDIT'];
            $url = $this->domain . "/admin/order/all?part=1&stock=1";

            $order_num = $content['order_num'];
            $date = date('Y-m-d H:i:s', time());
            $sendData = array(
                'first'=>array('value'=>("您的库存订单已经通过审核！"),'color'=>"#CC0000"),
                'keyword1' => array('value' => ("$order_num"), 'color' => '#000'),
                'keyword2' => array('value' => ("$date"), 'color' => '#000'),
                'keyword3' => array('value' => ("通过"), 'color' => '#000'),
                'remark' => array('value' => ("点击查看订单详情"), 'color' => '#CC0000')
            );
            $msgData = array(
                'uid' => $content['id'],
                'content' => '您的库存订单已经通过审核，订单号：'.$order_num,
                'title' => '订单已审核',
                'time' => time(),
                'openid' => $openid,
                'status' => '0'
            );
        }
        
        $template = array(
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => '#7B68EE',
            'data' => $sendData
        );
        
        //消息中心
        
        if(!empty($msgData)&&$openid&&C('MESSAGE_OPEN')){
            $info_dis = M('inform_dis');
            $add_msg = $info_dis->add($msgData);
        }
        
        //setlog(var_export($template,1),'message');
        $this->wechat_obj->sendTemplateMessage($template);
        
//        $sendMsg->doSend($openid, $template_id, $url, $sendData, $topcolor = '#7B68EE');
    }
}