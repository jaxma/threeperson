<?php
//品牌商城的返利模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";
class Shoprebate extends Common {


    private $user_obj;
    private $month;
    private $mall_rebate_order_setting_model;
    private $mall_rebate_other_model;
    private $mall_money_log_model;
    private $mall_money_funds_model;
    public $order_rebate = 0; //订单返利
    public $open = 1; //返利开启
    public $close = 0; //返利关闭
    public $head_pay = 0; //总部支付
    public $sup_pay = 1; //上级支付
    public $percent_way = 0; //订单金额x百分比计算返利
    public $money_way = 1; //订单产品数量x金额计算返利
    public $yes_pay = 1; //已结算
    public $not_pay = 0; //未结算

    private $one_rebate = 1; //一级返利
    private $two_rebate = 2; //二级返利
    private $three_rebate = 3; //三级返利

    public $status_name = [];//返利状态
    public $rebate_name = [];//返利类型
    /**
     * 架构函数
     */
    public function __construct() {
        import('Lib.Action.User','App');
        $this->user_obj = new User();

        $this->month = get_month();
        $this->mall_order_count_model = M('order_count');
        $this->mall_rebate_order_setting_model = M('_rebate_order_setting');
        $this->mall_rebate_other_model = M('shop_rebate_other');
        $this->mall_money_log_model = M('shop_money_log');
        $this->mall_money_funds_model=M('shop_money_funds');
        $this->status_name[$this->not_pay] = '未结算';
        $this->status_name[$this->yes_pay] = '已结算';
        $this->rebate_name[$this->order_rebate]= '订单奖励';
    }

    /**
     * 生成订单返利
     * $type为0订单返利
     */
    public function order_rebate($uid,$data, $type = 0) {
        $user = $this->user_obj->get_user_by_id($uid);
        $level = $user['level'];
        $where = [
            'level' => $level,
            'type' => $type,
            'status' => $this->open,
        ];
        $setting = $this->mall_rebate_order_setting_model->where($where)->find();
        if (!$setting || !$user['recommendID']) {
            return;
        }
        $rec_user = $this->user_obj->get_user_by_id($user['recommendID']);

        //支付者
        if ($setting['pay_way'] == $this->head_pay) {
            $payer_id = 0;
        }
        //返利计算方式
        if ($setting['count_way'] == $this->percent_way) {
            if ($type == $this->order_rebate) {
                //订单返利
                $total = $data['total_price'];
                $other_info = $data['order_num'];
            }
            $ratio_info = ' 百分比';
        } else if ($setting['count_way'] == $this->money_way) {
            $total = $data['total_num'];
            $ratio_info = '元/件';
            $other_info = $data['order_num'];
        }
        //一层返利
        if (!isset($payer_id)) {
            //上级支付返利
            $payer_id = 0;//直接写死，无论如何都是总部返佣金
        }
        //如果第一层比例为空，则直接return
        if(empty($setting['param1'])){
            return;
        }
        //写入返利表
        $data = [
            'uid' => $rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param1'],
            'ratio_info' => $setting['param1'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];
        $res_one=$this->mall_rebate_other_model->add($data);
        if(!$res_one){
            setLog('一层返利写进品牌商城返利明细表失败:'.json_encode($data), 'detail');
            return;
        }
        //写入佣金表
        $this->add_rebate_money($rec_user['id'],$uid,$other_info,$total,$setting['param1']);


        if ($setting['depth'] < $this->two_rebate) {
            return;
        }
        //二层返利
        if (!$rec_user['recommendID']) {
            return;
        }
        //如果第二层比例为空，则直接return
        if(empty($setting['param2'])){
            return;
        }
        $rec_rec_user = $this->user_obj->get_user_by_id($rec_user['recommendID']);
        $data = [
            'uid' => $rec_rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param2'],
            'ratio_info' => $setting['param2'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];
        $res_two=$this->mall_rebate_other_model->add($data);
        if(!$res_two){
            setLog('第二层返利写进品牌商城返利明细表失败:'.json_encode($data), 'detail');
            return;
        }
        //写入佣金表
        $this->add_rebate_money($rec_rec_user['id'],$uid,$other_info,$total,$setting['param2']);


        if ($setting['depth'] < $this->three_rebate) {
            return;
        }
        //三层返利
        if (!$rec_rec_user['recommendID']) {
            return;
        }
        if(empty($setting['param3'])){
            return;
        }
        $rec_rec_rec_user = $this->user_obj->get_user_by_id($rec_rec_user['recommendID']);
        $data = [
            'uid' => $rec_rec_rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param3'],
            'ratio_info' => $setting['param3'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];

        $res_three=$this->mall_rebate_other_model->add($data);
        if(!$res_three){
            setLog('第三层返利写进品牌商城返利明细表失败:'.json_encode($data), 'detail');
            return;
        }
        //写入佣金表
        $this->add_rebate_money($rec_rec_rec_user['id'],$uid,$other_info,$total,$setting['param3']);
    }


    //将佣金流动记录和佣金写进对应的表
    /**
     * @param $rec_id  获利人id
     * @param $uid     下单人id
     * @param $other_info   订单号
     * @param $total    订单总金额/总数量
     * @param $ratio    返利的比例
     */
    public function add_rebate_money($rec_id,$uid,$other_info,$total,$ratio){
        //写入金额记录表
        $rebate_money=$total * $ratio;
        $data_log=[
            'uid'=>$rec_id,
            'x_id'=>$uid,
            'order_num'=>$other_info,
            'order_money'=>$total,
            'ratio'=>$ratio,
            'money' => $rebate_money,
            'time'=>time(),
        ];
        //直接写入金额流动记录表
        $success=$this->mall_money_log_model->add($data_log);
        //上条插入成功则写入佣金表
        if($success){
            $funds = $this->mall_money_funds_model->where(['uid' => $rec_id])->find();
            if ($funds) {
                $money = [
                    'total_money' => $funds['total_money'] + $rebate_money,
                    'no_refund_money' => $funds['no_refund_money'] + $rebate_money,
                ];
                $this->mall_money_funds_model->where(['uid' => $rec_id])->save($money);
            } else {
                $money = [
                    'uid' => $rec_id,
                    'total_money' => $funds['total_money'] + $rebate_money,
                    'no_refund_money' => $funds['no_refund_money'] + $rebate_money,
                    'time' => time()
                ];
                $this->mall_money_funds_model->add($money);
            }
        }
    }


    /**
     * 获取其它返利记录
     */
    public function get_other_rebate($page_info=array(),$condition=array()) {
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $count = $this->mall_rebate_other_model->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->mall_rebate_other_model->where($condition)->page($page_con)->select();
            }
            else{
                $list = $this->mall_rebate_other_model->where($condition)->select();
            }
            //获取获利人、被推荐人、支付人信息
            $list = $this->get_related_data($list, 'distributor', ['uid','rec_id','payer_id']);
            foreach ($list as $k =>$v) {
                if (!$v['payer_id_info']) {
                    $list[$k]['payer_id_info']['name'] = '总部';
                }
                $list[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
                $list[$k]['status_name'] = $this->status_name[$v['status']];
                $list[$k]['rebate_name'] = $this->rebate_name[$v['type']];
            }
        }
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page_list_num,
            'count' => $count,
            'status_name' => $this->status_name,
        );

        return $return_result;
    }//end func get_rerebate

    //审核其它返利
    public function audit_other_rebate($ids, $status) {
        foreach ($ids as $id) {
            $rebate_info = $this->mall_rebate_other_model->find($id);

            if( empty($rebate_info) ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '查无此返利数据！',
                ];
                return $return_result;
            }

            import('Lib.Action.Funds','App');
            $funds = new Funds();
            $user_id = $rebate_info['uid'];
            $money = $rebate_info['money'];
            $payer_id = $rebate_info['payer_id'];

            $return_result = $funds->rebate_aduit_recharge($user_id,$money,$payer_id);

            if( $return_result['code'] == 1 ){
                $data = array(
                    'status' => $status,
                    'time' => time()
                );
                $row = $this->mall_rebate_other_model->where(['id' => $id, 'status' => $this->not_pay])->save($data);

                if( !$row ){
                    $return_result = [
                        'code'  =>  3,
                        'msg'   =>  '返利审核失败，请重试！',
                    ];
                    return $return_result;
                }else{
                    $return_result = [
                        'code'  =>  1,
                        'msg'   =>  '返利审核成功！',
                    ];
                }
            }
        }
        return $return_result;
    }
}
?>