<?php
/**
*	topos代理管理系统
*/
header("Content-Type: text/html; charset=utf-8");
class NewRebateAction extends CommonAction
{
    private $level;
    private $new_rebate_obj;
    private $rebate_other_setting_model;
    private $rebate_team_setting_model;
    private $distributor_model;
    private $rebate_other_model;
    private $rebate_team_model;
    
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.NewRebate','App');
        $this->new_rebate_obj = new NewRebate();
        $this->level = C('LEVEL_NAME');
        $this->rebate_other_setting_model = M('rebate_other_setting');
        $this->rebate_team_setting_model = M('rebate_team_setting');
        $this->rebate_other_model = M('rebate_other');
        $this->rebate_team_model = M('rebate_team');
        $this->distributor_model = M('distributor');
    }

    //返利设置页面
    public function setting() {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        $setting = $this->rebate_other_setting_model->select();
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
                //平级推荐充值返利
                case $this->new_rebate_obj->money_rebate:
                    $money_info[$v['level']] = $v;
                    $money_level[] = $v['level'];
                    $money_pay_way = $v['pay_way'];
                    $money_status = $v['status'];
                    break;
                case $this->new_rebate_obj->once_rebate:
                    //低推高
                    $once_info[$v['level']] = $v;
                    $once_level[] = $v['level'];
                    $once_pay_way = $v['pay_way'];
                    $once_status = $v['status'];
                    break;
                case $this->new_rebate_obj->development_rebate:
                    //高发展低
                    $development_info[$v['level']] = $v;
                    $development_level[] = $v['level'];
                    $development_pay_way = $v['pay_way'];
                    $development_status = $v['status'];
                    break;
                case $this->new_rebate_obj->same_development_rebate:
                    //平级发展低
                    $same_development_info[$v['level']] = $v;
                    $same_development_level[] = $v['level'];
                    $same_development_pay_way = $v['pay_way'];
                    $same_development_status = $v['status'];
                    break;
                case $this->new_rebate_obj->same_development_rebate:
                    //平级发展低
                    $same_development_info[$v['level']] = $v;
                    $same_development_level[] = $v['level'];
                    $same_development_pay_way = $v['pay_way'];
                    $same_development_status = $v['status'];
                    break;
                default:
                    break;
            }
        }
        //团队返利
        $team_setting = $this->rebate_team_setting_model->where(['type'=>3])->select();
        foreach ($team_setting as $v) {
            $team_level = json_decode($v['level']);
            $team_count_way = $v['count_way'];
            $team_achievement_way = $v['achievement_way'];

            $team_status = $v['status'];
        }
        //个人返利
        $person_setting = $this->rebate_team_setting_model->where(['type'=>5])->select();
        foreach ($person_setting as $v) {
            $person_level = json_decode($v['level']);
            $person_count_way = $v['count_way'];
            $person_achievement_way = $v['achievement_way'];

            $person_status = $v['status'];
        }
        //团队返利参数输出
        $this->team_info = $team_setting;
        $this->team_level = $team_level;
        $this->team_count_way = $team_count_way;
        $this->team_achievement_way = $team_achievement_way;
        $this->team_status = $team_status;
        $this->money_count_way=C('MONEY_COUNT_WAY');

        //个人返利参数输出
        $this->person_info = $person_setting;
        $this->person_level = $person_level;
        $this->person_count_way = $person_count_way;
        $this->person_achievement_way = $person_achievement_way;
        $this->person_status = $person_status;
        
        //平级推荐订单返利参数输出
        $this->order_info = $order_info;
        $this->order_level = $order_level;
        $this->order_count_way = $order_count_way;
        $this->order_pay_way = $order_pay_way;
        $this->order_status = $order_status;
        
        //平级推荐充值返利参数输出
        $this->money_info = $money_info;
        $this->money_level = $money_level;
        $this->money_pay_way = $money_pay_way;
        $this->money_status = $money_status;
        
        //低推高返利参数输出
        $this->once_info = $once_info;
        $this->once_level = $once_level;
        $this->once_pay_way = $once_pay_way;
        $this->once_status = $once_status;
        
        //高发展低返利参数输出
        $this->development_info = $development_info;
        $this->development_level = $development_level;
        $this->development_pay_way = $development_pay_way;
        $this->development_status = $development_status;

        //平级发展返利参数输出
        $this->same_development_info = $same_development_info;
        $this->same_development_level = $same_development_level;
        $this->same_development_pay_way = $same_development_pay_way;
        $this->same_development_status = $same_development_status;
        
        //公共参数输出
        $this->level_name = $this->level;
        $this->level_num = C('LEVEL_NUM');
        $this->rebate = C('REBATE');
        $this->count_way = $count_way;
        $this->display();
    }
    
    //返利设置提交
    public function other_setting_submit() {
        $this->check_other_setting_submit($_POST);
        $status = I('status');
        $level = I('level');
        $type = I('type');
        $depth = I('depth');
        $param = $_POST['param'];
        $count_way = I('count_way');
        $pay_way = I('pay_way');
        //删除重新写入
        $this->rebate_other_setting_model->where(['type' => $type])->delete();
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
            $res = $this->rebate_other_setting_model->add($data);
        }
        if ($res) {
            $this->add_active_log('返利设置成功');
            $this->success('返利设置成功');
        } else {
            $this->error('返利设置失败');
        }
    }
    
    //团队返利设置提交
    public function team_setting_submit() {
//        var_dump($_POST);die;
//      $this->check_team_setting_submit($_POST);
        $status = I('status');
        $level = json_encode(I('level'));
        $count_way = I('count_way');
        $achievement_way = C('MONEY_COUNT_WAY');
        $achievement = I('achievement');
        $parameter = I('parameter');
        $type = I('type');
        $where=[
            'id'=> ['gt', 0],
            'type' => $type,
        ];
        $setting = $this->rebate_team_setting_model->where($where)->select();
        $this->add_active_log('修改前团队返利比例设置：'. json_encode($setting));
        
        //删除返利设置重新写入
        $this->rebate_team_setting_model->where($where)->delete();
        foreach ($achievement as $k => $v) {
            $data = [
                'level' => $level,
                'status' => $status,
                'count_way' => $count_way,
                'achievement_way' => $achievement_way,
                'achievement' => $v,
                'parameter' => $parameter[$k],
                'time' => time(),
                'type' => $type,
            ];
            $res = $this->rebate_team_setting_model->add($data);
        }
        if ($res) {
            $this->add_active_log('团队返利设置成功');
            $this->success('返利设置成功');
        } else {
            $this->error('返利设置失败');
        }
    }
    
    public function check_other_setting_submit($data) {

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
    
    //检查团队返利设置参数
    public function check_team_setting_submit($data) {

        if (empty($data['level'])) {
            $this->error('请选择产生返利的代理等级');
            exit();
        }
        if (empty($data['achievement'])) {
            $this->error('请填写团队返利业绩');
            exit();
        }
        if (empty($data['achievement'])) {
            $this->error('请填写团队返利比例');
            exit();
        }
        foreach ($data['achievement'] as $v) {
            if ($v <=0) {
               $this->error('团队业绩不能小于0');
                exit(); 
            }
        }
        foreach ($data['parameter'] as $v) {
            if ($v >=1) {
               $this->error('请输入0至1之间的返利比例');
                exit(); 
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
        $get_types=trim(I('get.types'));
        $condition = array();
        if($get_type == 2 && empty($get_types)){
            $condition['type'] = array('in',array(2,3,4));
        }elseif ($get_type == 2 || !empty($get_types)){
            $condition['type'] = $get_types;
        }elseif($get_type){
            $condition['type'] = $get_type;
        }else{
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
    
    //审核返利
    public function audit_other_rebate() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }

        $ids = I('mids');
        $ids = substr($ids, 1);
        $rerebate = explode('_', $ids);
        $return_result = $this->new_rebate_obj->audit_other_rebate($rerebate, $this->new_rebate_obj->yes_pay);
        $this->ajaxReturn($return_result);
    }

    //团队返利
    public function team() {
        
        $condition = array();
        
        $month = I('month');
        $name = I('name');
        $name = trim($name);
        $status = I('status');
        $type = I('type');
        $uid = I('uid');
        $payer_id = I('payer_id');
        
        if( !empty($name) ){
            $where = [
                'name'=>['like','%'.$name.'%'],
                '_logic'=>'or',
                'wechatnum'=>['like','%'.$name.'%'],
            ];
            $sear_dis_info = $this->distributor_model->where($where)->select();
            
            $sear_uids = array();
            foreach( $sear_dis_info as $k_dis => $v_dis ){
                $sear_uids[] = $v_dis['id'];
            }
            
            $condition['uid']  =   array('in',$sear_uids);
        }
        if ($uid) {
            $condition['uid']  = $uid;
        }
        
        if( !empty($month) ){
            $condition['month'] =   $month;
        }
        
        if( $status != null ){
            $condition['status'] =   $status;
        }
         if( $payer_id != null ){
             if ($payer_id == 0) {
                $condition['payer_id'] =   $payer_id;
             } else {
                 $condition['payer_id'] =   ['gt',0];
             }
        }
         if( $type != null ){
             $condition['type'] =   $type;
             if ($type == 'all') {
                $condition['type'] = ['in',[3,5]
                ];
            }
         }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        $result = $rebate->get_team_rebate($page_info,$condition);
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }

        $this->con_url = base64_encode($con_url);
        
        $this->p = I('p');
        $this->count = $result['count'];
        $this->limit = $result['limit'];
        
        $this->list = $result['list'];
        $this->status_name = $result['status_name'];
        $this->status = $status;
        $this->payer_id = $payer_id;
        $this->month    =   $month;
        $this->name = $name;
//        echo '<pre>';var_dump($result['list']);die;
        $this->display();
    }
    
    //团队返利结算
    public function audit_team_rebate() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        $ids = I('mids');
        $ids = substr($ids, 1);
        $ids_arr = explode('_', $ids);
        $return_result = $this->new_rebate_obj->audit_team_rebate($ids_arr, $this->new_rebate_obj->yes_pay);
        $this->ajaxReturn($return_result);
    }
}
?>