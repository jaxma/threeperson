<?php

//微信支付回调地址
class NoticeAction extends Action {

    //轮盘抽奖支付标识
    private $lunpan_type = 'lunpan';
    //积分标识
    private $Integral_type = 'Integral';

    //优惠商城支付标识
    private $mall_type =  'mall';

    public function __construct() {
        parent::__construct();
    }

    //异步通知
    public function return_url() {
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        import('Lib.Action.wxPay.WxPayJsApiPay', 'App');
        //如果返回成功则验证签名
        try {
            $result = WxPayResults::Init($xml);
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }
        $notice = new WxPayNotifyReply();
        if ($result['return_code'] !== 'SUCCESS') {
            $notice->SetReturn_code("FAIL");
        } else {
            if ($result['attach'] == $this->lunpan_type) {
                //轮盘支付回调
                $order_model = M('sale_order');
                $where = [
                    'order_num' => $result['out_trade_no'],
                    'status' => 0
                ];
                $order = $order_model->where($where)->find();
                if ($order) {
                    $data = [
                        'status' => 1,
                        'trade_num' => $result['transaction_id']
                    ];
                    $order_model->where($where)->save($data);
                }
            } elseif ($result['attach'] == $this->Integral_type) {
                import('Lib.Action.Integralorder', 'App');
                $Integralorder = new Integralorder();

                $order_audit_result = $Integralorder->admin_audit($result['out_trade_no']);

                if ($order_audit_result['result'] != 1) {
                    setLog('错误信息：' . var_export($order_audit_result, 1), 'NoticeAction_error');
                }
            }
            elseif ($result['attach'] == $this->mall_type) {
                //优惠商城订单处理
                import('Lib.Action.Mallorder', 'App');
                $mallorder = new Mallorder();
                $mallorder->pay_callback_handle($result);
            }
            else {
                //活动订单处理
                $order_model = M('activity_order');
                $where = [
                    'order_num' => $result['out_trade_no'],
                    'status' => 1
                ];
                $order = $order_model->where($where)->find();
                if ($order) {
                    $data = [
                        'status' => 2,
                        'trade_num' => $result['transaction_id']
                    ];
                    $order_model->where($where)->save($data);
                }
            }
            $notice->SetReturn_code("OK");
        }
        $notice->SetReturn_msg('OK');
        WxpayApi::replyNotify($notice->ToXml());
    }

}

?>