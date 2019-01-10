<?php
header("Content-Type: text/html; charset=utf-8");
class ShoprebateAction extends  CommonAction {
    private $level;
    private $new_rebate_obj;
    private $mall_rebate_order_setting_model;
    private $distributor_model;

    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.Shoprebate','App');
        $this->new_rebate_obj = new Shoprebate();
        $this->level = C('LEVEL_NAME');
        $this->mall_rebate_order_setting_model = M('shop_rebate_order_setting');
        $this->distributor_model = M('distributor');
    }
    //返利设置页面
    public function setting() {
        $setting = $this->mall_rebate_order_setting_model->select();

        foreach ($setting as $v) {
            switch ($v['type']) {
                //平级推荐订单返利
                case $this->new_rebate_obj->order_rebate:
                    $order_info[$v['level']] = $v;
                    $order_level[] = $v['level'];
                    $order_count_way = $v['count_way'];
                    $order_pay_way = $v['pay_way'];
                    $order_status = $v['status'];
                    break;
                default:
                    break;
            }
        }



        //平级推荐订单返利参数输出
        $this->order_info = $order_info;
        $this->order_level = $order_level;
        $this->order_count_way = $order_count_way;
        $this->order_pay_way = $order_pay_way;
        $this->order_status = $order_status;


        //公共参数输出
        $this->level_name = $this->level;
        $this->level_num = C('LEVEL_NUM');
        $this->rebate = C('SHOP_REBATE');
        $this->display();
    }

    //返利设置提交
    public function mall_order_setting_submit() {
        $this->check_setting_submit($_POST);
        $status = I('status');
        $level = I('level');
        $type = I('type');
        $depth = I('depth');
        $param = $_POST['param'];
        $count_way = I('count_way');
        $pay_way = I('pay_way');
        //删除重新写入
        $this->mall_rebate_order_setting_model->where(['type' => $type])->delete();
        foreach ($level as $k => $v) {
            $data = [
                'level' => $v,
                'type' => $type,
                'depth' => empty($depth[$v-1]) ? 1 : $depth[$v-1],
                'status' => $status,
                'count_way' => $count_way,
                'pay_way' => $pay_way,
                'param1' => $param[$v][0],
                'param2' => $param[$v][1],
                'param3' => $param[$v][2],
                'time' => time(),
            ];
            $res = $this->mall_rebate_order_setting_model->add($data);
        }
        if ($res) {
            $this->success('返利设置成功');
            $this->add_active_log('返利设置成功');
        } else {
            $this->error('返利设置失败');
        }
    }

    public function check_setting_submit($data) {

        if (empty($data['level'])) {
            $this->error('请选择产生返利的代理等级');
            exit();
        }
        foreach ($data['level'] as $v) {
            if (empty($data['depth'][$v-1])) {
                $this->error('请选择几层返利');
                exit();
            }
        }
        foreach ($data['param'] as $v) {
            if ((isset($v[0]) && $v[0] <=0) || (isset($v[1]) && $v[1] <=0) || (isset($v[2]) && $v[2] <=0)) {
                $this->error('返利比例/金额不能小于0');
                exit();
            }
        }
        if ($data['count_way'] == $this->new_rebate_obj->percent_way) {
            foreach ($data['param'] as $v) {
                if ((isset($v[0]) && $v[0] >=1) || (isset($v[1]) && $v[1] >=1) || (isset($v[2]) && $v[2] >=1)) {
                    $this->error('请输入0至1之间的返利比例');
                    exit();
                }
            }
        }
    }
    //返利显示
    public function other() {
        $get_type = trim(I('get.type'));
        $get_payer_id = trim(I('get.payer_id'));
        $get_state = trim(I('get.status'));
        $start_time = trim(I('get.start_time'));
        $end_time = trim(I('get.end_time'));
        $u_name = trim(I('get.u_name'));

        $condition = array();
        if(empty($get_type)){
            $condition['type'] = '0';
        }



        if( is_numeric($get_payer_id) ){
            if ( $get_payer_id == 0) {
                $condition['payer_id'] = array('eq', 0);
            } else {
                $condition['payer_id'] = array('gt', 0);
            }
        }
        if( is_numeric($get_state) ){
            if ( $get_state == 0) {
                $condition['status'] = array('eq', 0);
            } else {
                $condition['status'] = array('gt', 0);
            }
        }
        if( !empty($u_name) ){
            $where = [
                'name' => $u_name,
                '_logic' => 'or',
                'phone'=> $u_name,
                '_logic' => 'or',
                'wechatnum'=>$u_name
            ];
            $sear_dis_info = $this->distributor_model->where($where)->find();

            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';
        }

        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }

        $page_info=[
            'page_num' => I('p'),
            'page_list_num' => '',
        ];
        $list = $this->new_rebate_obj->get_other_rebate($page_info, $condition);
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        $this->con_url = base64_encode($con_url);

        $this->type = $get_type;
        $this->payer_id = $get_payer_id;
        $this->status = $get_state;
        $this->status_name = $list['status_name'];
        $this->list = $list['list'];
        $this->p = I('p');
        $this->limit = $list['page'];
        $this->count = $list['count'];
        $this->display();
    }//end func rrebate
}

?>