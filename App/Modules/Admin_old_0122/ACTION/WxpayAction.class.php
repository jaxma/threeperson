<?php 

class WxpayAction extends ActivityAction {
    
    public function __construct() {
        parent::__construct();
    }
    
    //支付页面
    public function pay() {
        $order_num = I('get.order_num');
        $where = [
            'user_id' => $this->uid,
            'order_num' => trim($order_num),
            'status' => 1
        ];
        $order = M('activity_order')->where($where)->find();
        
        //判断订单是否符合支付要求
        $this->checkOrder($order);
        
        //调用统一支付接口获取支付参数
        $return = $this->unifiedOrder($order);
        
        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($return);
        
        $this->jsApiParameters = $jsApiParameters;
        $this->order_num = $order_num;
        $this->order = $order;
        $this->display();
    }
    
    //判断订单是否符合支付要求
    private function checkOrder($order) {
        if (!$order) {
            error_tip('未找到相关订单');
        }
        if ($order['status'] > 1) {
            error_tip('此订单已支付');
        }
        
    if ($order['total_price'] <=0 || !is_numeric($order['total_price'])) {
            error_tip('订单金额必须大于0');
        }
    }


    //统一下单接口
    private function unifiedOrder($order) {
        import('Lib.Action.wxPay.WxPayJsApiPay','App');
        $input = new WxPayUnifiedOrder();
        $input->SetBody($order['order_num']);
        $input->SetAttach($order['order_num']);
        $input->SetOut_trade_no($order['order_num']);
        $input->SetTotal_fee($order['total_price'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($this->openid);
        $input->SetSpbill_create_ip('192.168.1.1');
        $result = WxPayApi::unifiedOrder($input);
        if ($result['return_code'] !== 'SUCCESS') {
            //跳转错误页面提示
            setLog('统一下单支付错误返回:'.json_encode($result), 'wxpay');
            error_tip($result['return_msg']);
        }
        return $result;
    }
}

 ?>