<?php

/**
 * 	老先生经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class RebateAction extends CommonAction {

    //上下级下单返利列表
    public function index() {
        $distributor = M('distributor');
        import('ORG.Util.Page');
        $count = M('Rebate')->count('id');
        if ($count > 0) {
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = M('Rebate')->order('time desc')->limit($limit)->select();
            $field = 'name,phone,levname';
            foreach ($list as $k => $v) {
                if ($v['o_id'] != 0) {
                    $row = $distributor->where(array('id' => $v['o_id']))->field($field)->find();
                    $list[$k]['o_name'] = $row['name'];
                    $list[$k]['o_phone'] = $row['phone'];
                    $list[$k]['o_levname'] = $row['levname'];
                    if ($v['state'] == 0) {
                        $list[$k]['pd'] = 1;
                    } else {
                        $list[$k]['pd'] = 0;
                    }
                } else {
                    $list[$k]['o_name'] = "总部";
                    $list[$k]['o_phone'] = "总部电话";
                    $list[$k]['o_levname'] = "总部";
                    $list[$k]['pd'] = 1;
                }
                $rol = $distributor->where(array('id' => $v['user_id']))->field($field)->find();
                $list[$k]['name'] = $rol['name'];
                $list[$k]['phone'] = $rol['phone'];
                $list[$k]['levname'] = $rol['levname'];
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('list', $list);
        }
        $this->display();
    }

    //总部审核最高级返利单
    public function confirm() {
        $distributor = M('distributor');
        $list = M('Rebate')->where(array('o_id' => 0, 'state' => 0))->order('time desc')->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $rol = $distributor->where(array('id' => $v['user_id']))->find();
                $list[$k]['name'] = $rol['name'];
                $list[$k]['phone'] = $rol['phone'];
                $list[$k]['levname'] = $rol['levname'];
            }
        }
        $this->assign('list', $list);
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //下单返利列表
    public function rerebate() {
        $distributor = M('distributor');
        $order = M('Order');
        $rerebate = M('Rerebate');
        import('ORG.Util.Page');

        $get_state = trim(I('get.st'));
        $x_name = trim(I('get.x_name'));
        $u_name = trim(I('get.u_name'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
//        print_r($get_state);

        $page = '';
        $condition = array();
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        if( is_numeric($get_state) ){
            if ($get_state == 0) {
                $condition['state'] = array('eq', 0);
            } else {
                $condition['state'] = array('gt', 0);
            }
        }


        if( !empty($x_name) ){

            $sear_dis_info = $distributor->where(array('name'=>$x_name))->find();

            $condition['x_id']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';
        }

        if( !empty($u_name) ){
            $where = [
                'name' => $u_name,
                '_logic' => 'or',
                'wechatnum' => $u_name,
                'phone'=>$u_name
            ];
            $sear_dis_info = $distributor->where($where)->find();

            $condition['user_id']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';
        }


        $list_count = $rerebate->where($condition)->count();
        $page_num=20;
        if ( $list_count > 0 ) {
            $p = new Page($list_count, $page_num);
            $limit= $p->firstRow . "," . $p->listRows;
            $page = $p->show();

            $list = $rerebate->where($condition)->order('time desc')->limit($limit)->select();


            $user_id_arr = array(
                '0' =>  array(
                    'name'  =>  '总部',
                ),
            );//代理用户信息
            $user_id_str = '';
            $order_num_arr = array();//订单下单信息
            $order_num_str = '';

            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_x_id = $v['x_id'];
                $list_order_num = $v['order_num'];
                $list_month = $v['month'];
                $list_pay_id = $v['pay_id'];

                //出现的代理用户ID数组
                if( !isset($user_id_arr[$list_user_id]) ){
                    $user_id_arr[$list_user_id] = $list_user_id;
                }
                if( !isset($user_id_arr[$list_x_id]) ){
                    $user_id_arr[$list_x_id] = $list_x_id;
                }
                if( !isset($user_id_arr[$list_pay_id]) ){
                    $user_id_arr[$list_pay_id] = $list_pay_id;
                }

                //订单下单数组
                if( !isset($order_num_arr[$list_order_num]) ){
                    $order_num_arr[$list_order_num] = $list_order_num;
                }
            }

            //代理信息
            $user_id_str = implode(',', $user_id_arr);
            $condition_dis['id'] = array('in',$user_id_str);
            $distributor_info = $distributor->where($condition_dis)->select();
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部'
                ),
            );//以pid为key的代理信息数组

            foreach( $distributor_info as $val1 ){
                $dis_info[$val1['id']]   =   $val1;
            }

            //订单下单返利信息调整
            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_month = $v['month'];
                $list_x_id = $v['x_id'];
                $list_pay_id = $v['pay_id'];


                $list[$k]['user_info'] = isset($dis_info[$list_user_id])?$dis_info[$list_user_id]:array();
                $list[$k]['x_info'] = isset($dis_info[$list_x_id])?$dis_info[$list_x_id]:array();
                $list[$k]['pay_info'] = isset($dis_info[$list_pay_id])?$dis_info[$list_pay_id]:array();

            }

            //print_r($list);return;
        }


        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }

        $this->con_url = base64_encode($con_url);

//        $this->page = $page;
        $this->cur_month = date('Ym');
        $this->level_name = C('LEVEL_NAME');
        $this->assign('list', $list);
        $this->status = $get_state;
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$list_count;
        $this->display();
    }//end func rerebate
    
    
    //更改返利金额
    public function change_rerebate_money(){
        if( !IS_AJAX ){
            return false;
        }
        
        $return_res = array(
            'status'    =>  0,
            'info'      =>  '',
        );
        
        $rerebate_id = $this->_post('id');
        $rerebate_money = $this->_post('money');
        
        if( empty($rerebate_id) ){
            $return_res['status']   =   1;
            $return_res['info']   =   '参数错误！';
        }
        elseif( !is_numeric($rerebate_money) ){
            $return_res['status']   =   2;
            $return_res['info']   =   '金额必须为数字！';
        }
        else{
            $rerebate = M('Rerebate');
            
            $data  = array(
                'money' =>  $rerebate_money,
                'updated' =>  time(),
            );
            
            $condition['id'] = $rerebate_id;
            $save_res = $rerebate->where($condition)->save($data);
            
            if( $save_res ){
                $this->add_active_log('更改返利金额');
                $return_res['status']   =   'succ';
                $return_res['info']   =   $data;
            }
            else{
                $return_res['status']   =   3;
                $return_res['info']   =   '写入错误！';
            }
        }
        
        
        $this->ajaxReturn($return_res, 'JSON');
    }
    
    
    
    

    //推荐返利设置
    public function recommend_setting() {
        $recommend_setting = M('recommend_setting');
        $set_info = $recommend_setting->where(array('id'=>'1'))->find();
        
        $level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');

        
        $this->level_num = $level_num;
        $this->level_name = $level_name;
        $this->set_info = $set_info;
        $this->display();
    }
    
    //提交推荐返利设置
    public function recommend_setting_submit(){
        if (!$this->isPost()) {
            $this->error('修改失败');
            return;
        }
        
        $save = array(
            'level1' => I('post.level1'),
            'level2' => I('post.level2'),
            'level3' => I('post.level3'),
            'level4' => I('post.level4'),
            'level5' => I('post.level5'),
            'level6' => I('post.level6'),
            'updated'   =>  time(),
        );
        $a = M('recommend_setting')->where('id=1')->save($save);

        
        if ($a) {
            $this->add_active_log('修改推荐返利设置');
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
    

    //订单返利设置
    public function order_rebate_setting() {

        $order_rebate_setting = M('rebate_order_setting');
        $set_info = $order_rebate_setting->where(array('id'=>'1'))->find();
        
        $level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');

        
        $this->level_num = $level_num;
        $this->level_name = $level_name;
        $this->set_info = $set_info;
        $this->display();
    }
    
    
    //提交订单返利设置
    public function order_rebate_setting_submit(){
        if (!$this->isPost()) {
            $this->error('修改失败');
            return;
        }
        
        $save = array(
            'level1' => I('post.level1'),
            'level2' => I('post.level2'),
            'level3' => I('post.level3'),
            'level4' => I('post.level4'),
            'level5' => I('post.level5'),
            'level6' => I('post.level6'),
            'updated'   =>  time(),
        );
        $a = M('rebate_order_setting')->where('id=1')->save($save);

        
        if ($a) {
            $this->add_active_log('修改订单返利设置');
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
    

    //返利比例设置
    public function edit() {
        $rebate = M('Returnrate');
//        $rebate_info = $rebate->select();
        $rebate_info = $rebate->where('id=3')->find();

        $level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');

        $this->level_num = $level_num;
        $this->level_name = $level_name;
        $this->row = $rebate_info;
        $this->display();
    }

    //修改返利比例
    public function editrebate() {

        if (!$this->isPost()) {
            $this->error('修改失败', "edit");
            return;
        }

        $save = array(
            'ratio1' => I('post.ratio1'),
            'ratio2' => I('post.ratio2'),
            'ratio3' => I('post.ratio3'),
            'ratio4' => I('post.ratio4'),
            'ratio5' => I('post.ratio5'),
            'ratio6' => I('post.ratio6'),
            'min1' => I('post.min1'),
            'min2' => I('post.min2'),
            'min3' => I('post.min3'),
            'min4' => I('post.min4'),
            'min5' => I('post.min5'),
            'min6' => I('post.min6')
        );
        $a = M('Returnrate')->where('id=1')->save($save);


        if ($a) {
            $this->add_active_log('修改返利比例');
            $this->success('修改成功', "edit");
        } else {
            $this->error('修改失败', "edit");
        }
    }

    //审核最高级返利单
    public function queren() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }

        $ids = I('mids');
        $ids = substr($ids, 1);
        $rerebate = explode('_', $ids);
        foreach ( $rerebate as $id ){
            if (I('post.pid') == 1) {
                $arr['state'] = 2;
                $save = M('Rebate')->where(array('id' => $id))->save($arr);
            } elseif (I('post.pid') == 2) {
                $arr['status'] = 1;
                $save = M('Recommend_rebate')->where(array('id' => $id,'payer_id'=>0))->save($arr);
            } elseif (I('post.pid') == 3) {
                $arr['state'] = 1;
                $save = M('Rerebate')->where(array('id' => $id,'pay_id'=>0))->save($arr);
            }elseif ((I('post.pid') == 4)){
                $arr['state'] = 2;
                $save = M('rebate_apply')->where(array('id' => $id))->save($arr);
            }
        }

        if ($save) {
            $return_result = [
                'code' => 1,
                'msg' => '审核成功',
            ];
            $this->ajaxReturn($return_result);
//            $this->ajaxReturn('1', 'JSON');
        } else {
            $return_result = [
                'code' => 2,
                'msg' => '审核失败',
            ];
            $this->ajaxReturn($return_result);;
        }

    }

    //经销商推荐返利列表
    public function rrebate() {

        $distributor = M('distributor');
        $Rerebate = M('Recommend_rebate');

        import('ORG.Util.Page');
        
        $get_state = trim(I('get.st'));
        $start_time = trim(I('get.start_time'));
        $end_time = trim(I('get.end_time'));
        $u_name = trim(I('get.u_name'));
        
//        print_r($get_state);

        $condition = array();

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
            $sear_dis_info = $distributor->where($where)->find();

            $condition['user_id']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';
        }

        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        
        
        $page_num=20;
        $count = $Rerebate->where($condition)->count();
        if ($count > 0) {
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $Rerebate->where($condition)->order('time desc')->limit($limit)->select();
            
            
            $uids = array();
            foreach ($list as $k => $v) {
                $v_x_id = $v['x_id'];
                $v_user_id = $v['user_id'];
                $v_pay_id = $v['payer_id'];
                
                $uids[] = $v_x_id;
                $uids[] = $v_user_id;
                $uids[] = $v_pay_id;
            }
            
            array_unique($uids);
            array_values($uids);
            
            $condition_user = array(
                'id'    =>  array('in',$uids),
            );
            
            $all_dis_info = $distributor->where($condition_user)->select();
            
            $all_dis_key_info = array(
                '0' =>  array(
                  'name'    =>  '总部'  
                ),
            );
            
            foreach( $all_dis_info as $k_d => $v_d ){
                $v_d_id = $v_d['id'];
                
                $all_dis_key_info[$v_d_id] = $v_d;
            }
            
            
            foreach ($list as $k => $v) {
                $v_x_id = $v['x_id'];
                $v_user_id = $v['user_id'];
                $v_pay_id = $v['payer_id'];
                
                
                $list[$k]['x_info'] = $all_dis_key_info[$v_x_id];
                $list[$k]['u_info'] = $all_dis_key_info[$v_user_id];
                $list[$k]['pay_info'] = $all_dis_key_info[$v_pay_id];
            }
            $page = $p->show();
            $this->page = $page;
        }


        $this->status = $get_state;
        $this->assign('list', $list);
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();
    }//end func rrebate
    
    
    
//    public function recommend_setting(){
//        $rebate = M('Returnrate');
////        $rebate_info = $rebate->select();
//        $rebate_info = $rebate->where('id=3')->find();
//
//        $level_name = C('LEVEL_NAME');
//        $level_num = C('LEVEL_NUM');
//
//        $this->level_num = $level_num;
//        $this->level_name = $level_name;
//        $this->row = $rebate_info;
//        $this->display();
//    }







    //审核时的删除
    public function delete() {
        $ids=I('post.mids');
        $ids = substr($ids, 1);
        $rerebate = explode('_', $ids);
        foreach ( $rerebate as $id ) {
            if (I('post.pid') == 1) {
                $row = M('Rebate')->where(array('id' =>$id))->delete();
            } elseif (I('post.pid') == 2) {
                $row = M('Recommend_rebate')->where(array('id' => $id))->delete();
            } elseif (I('post.pid') == 3) {
                $row = M('Rerebate')->where(array('id' =>$id))->delete();
            }elseif (I('post.pid') == 4){
                $row = M('rebate_apply')->where(array('id' =>$id))->delete();
            }
        }
        if ($row) {
            $return_result = [
                'code' => 1,
                'msg' => '删除成功',
                $this->add_active_log('删除返利'),
            ];
            $this->ajaxReturn($return_result);
//            $this->ajaxReturn('1', 'JSON');
        }else {
            $return_result = [
                'code' => 2,
                'msg' => '删除失败',
            ];
            $this->ajaxReturn($return_result);;
        }
    }

    //搜索
    public function search() {
        $keyword = $_GET['keyword'];
        $distributor = M('distributor');
        import('ORG.Util.Page');
        $a = $distributor->where(array('name' => $keyword))->find();
        $where = array(
            'user_id' => $a['id'],
            '_logic' => 'or',
            'o_id' => $a['id'],
        );
        $count = M('Rebate')->where($where)->count();
        if ($count > 0) {
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = M('Rebate')->where($where)->order('time desc')->limit($limit)->select();
            $field = 'name,phone,levname';
            foreach ($list as $k => $v) {
                if ($v['o_id'] != 0) {
                    $row = $distributor->where(array('id' => $v['o_id']))->field($field)->find();
                    $list[$k]['o_name'] = $row['name'];
                    $list[$k]['o_phone'] = $row['phone'];
                    $list[$k]['o_levname'] = $row['levname'];
                    if ($v['state'] == 0) {
                        $list[$k]['pd'] = 1;
                    } else {
                        $list[$k]['pd'] = 0;
                    }
                } else {
                    $list[$k]['o_name'] = "总部";
                    $list[$k]['o_phone'] = "总部电话";
                    $list[$k]['o_levname'] = "总部";
                    $list[$k]['pd'] = 1;
                }
                $rol = $distributor->where(array('id' => $v['user_id']))->field($field)->find();
                $list[$k]['name'] = $rol['name'];
                $list[$k]['phone'] = $rol['phone'];
                $list[$k]['levname'] = $rol['levname'];
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('list', $list);
        }
        $this->display();
    }
    
    
    /**
     * 获取上个月
     * @return string $lastmonth (ps:201607)
     */
    private function getlastMonth(){
        
        $firstday=date('Y-m-01');
        $lastmonth_time = strtotime($firstday)-60*60*24;
        $lastmonth = date('Ym',$lastmonth_time);//ps:201607
        
        return $lastmonth;
    }
    
    
    /**
     * 获得某月的下个月第一天
     * @param int $month
     * @return int
     */
    private function get_next_month_first_day($month){
        if( empty($month) ){
            return FALSE;
        }
        
        //切割出年份  
        $tmp_year=substr($month,0,4);  
        //切割出月份  
        $tmp_mon =substr($month,4,2);  
        
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);  
        
//        return $fm_next_month = date("Ymd",$tmp_nextmonth);
        
        return $tmp_nextmonth;
    }


    //订单返利设置
    public function apply_rebate_setting() {

        $order_rebate_setting = M('rebate_apply_setting');
        $set_info = $order_rebate_setting->where(array('id'=>'1'))->find();

        $level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');


        $this->level_num = $level_num;
        $this->level_name = $level_name;
        $this->set_info = $set_info;


        $this->display();
    }


    //提交订单返利设置
    public function apply_rebate_setting_submit(){
        if (!$this->isPost()) {
            $this->error('修改失败');
            return;
        }
        $rebate1_level2 = I('post.rebate1_level2');
        $rebate2_level2 = I('post.rebate2_level2');
        $rebate1_level3 = I('post.rebate1_level3');
        $rebate1_level4 = I('post.rebate1_level4');
        $rebate1_level5 = I('post.rebate1_level5');
        if($rebate1_level2 >=0 && $rebate2_level2 >= 0 && $rebate1_level3 >= 0 && $rebate1_level4 >= 0&& $rebate1_level5 >=0){
            $save = array(
                'rebate1_level2' =>$rebate1_level2,
                'rebate2_level2' =>$rebate2_level2,
                'rebate1_level3' =>$rebate1_level3,
                'rebate1_level4' =>$rebate1_level4,
                'rebate1_level5' =>$rebate1_level5,
                'updated'   =>  time(),
            );
            $a = M('rebate_apply_setting')->where('id=1')->save($save);
            $this->add_active_log('修改充值返利设置');
            $this->success('修改成功');
        }else{
            $this->error('修改失败,比例不能小于0');
        }

    }

    //充值返利列表
    public function rebate_apply_money() {
        $distributor = M('distributor');
        $order = M('Order');
        $rerebate = M('rebate_apply');
        import('ORG.Util.Page');

        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        $get_state = I('get.st');
        $x_name = trim(I('get.x_name'));
        $u_name = trim(I('get.u_name'));
        $pay_id = trim(I('get.pay_id'));
//        print_r($get_state);

        $page = '';
        $condition = array();
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }

        if( is_numeric($get_state) ){
            if ($get_state == 0) {
                $condition['state'] = array('eq', 0);
            } else {
                $condition['state'] = array('gt', 0);
            }
        }


        if( !empty($x_name) ){

            $sear_dis_info = $distributor->where(array('name'=>$x_name))->find();

            $condition['x_id']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';
        }

        if( !empty($u_name) ){
            $where = [
                'name' => $u_name,
                '_logic' => 'or',
                'wechatnum' => $u_name,
                'phone'=>$u_name
            ];
            $sear_dis_info = $distributor->where($where)->find();

            $condition['user_id']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'999999';

        }

        $condition['pay_id'] = 0;
        $list_count = $rerebate->where($condition)->count();

        $page_num=20;
        if ( $list_count > 0 ) {
            $p = new Page($list_count,$page_num);
            $limit= $p->firstRow . "," . $p->listRows;
            $page = $p->show();

            $list = $rerebate->where($condition)->order('time desc')->limit($limit)->select();


            $user_id_arr = array(
                '0' =>  array(
                    'name'  =>  '总部',
                ),
            );//代理用户信息
            $user_id_str = '';
            $order_num_arr = array();//订单下单信息
            $order_num_str = '';

            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_x_id = $v['x_id'];
                $list_order_num = $v['order_num'];

                $list_month = $v['month'];
                $list_pay_id = $v['pay_id'];

                //出现的代理用户ID数组
                if( !isset($user_id_arr[$list_user_id]) ){
                    $user_id_arr[$list_user_id] = $list_user_id;
                }
                if( !isset($user_id_arr[$list_x_id]) ){
                    $user_id_arr[$list_x_id] = $list_x_id;
                }
                if( !isset($user_id_arr[$list_pay_id]) ){
                    $user_id_arr[$list_pay_id] = $list_pay_id;
                }

                //订单下单数组
                if( !isset($order_num_arr[$list_order_num]) ){
                    $order_num_arr[$list_order_num] = $list_order_num;
                }
            }

            //代理信息
            $user_id_str = implode(',', $user_id_arr);
            $condition_dis['id'] = array('in',$user_id_str);
            $distributor_info = $distributor->where($condition_dis)->select();
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部'
                ),
            );//以pid为key的代理信息数组

            foreach( $distributor_info as $val1 ){
                $dis_info[$val1['id']]   =   $val1;
            }

            //订单下单返利信息调整
            foreach ($list as $k => $v) {
                $list_user_id = $v['user_id'];
                $list_month = $v['month'];
                $list_x_id = $v['x_id'];
                $list_pay_id = $v['pay_id'];


                $list[$k]['user_info'] = isset($dis_info[$list_user_id])?$dis_info[$list_user_id]:array();
                $list[$k]['x_info'] = isset($dis_info[$list_x_id])?$dis_info[$list_x_id]:array();
                $list[$k]['pay_info'] = isset($dis_info[$list_pay_id])?$dis_info[$list_pay_id]:array();

            }

            //print_r($list);return;
        }


        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }

        $this->con_url = base64_encode($con_url);

        $this->page = $page;
        $this->cur_month = date('Ym');
        $this->level_name = C('LEVEL_NAME');

        $this->assign('list', $list);
        $this->status = $get_state;

        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$list_count;
        $this->display();
    }//end func rerebate

    //审核最高级返利单
    public function queren_apply() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }

        $ids = I('ids');
        $ids = substr($ids, 1);
        $rerebate = explode('_', $ids);
        $pid =I('post.pid');

        foreach ( $rerebate as $id ){
            if (I('post.pid') == 1) {
                $arr['state'] = 2;
                $save = M('Rebate')->where(array('id' => $id))->save($arr);
            } elseif (I('post.pid') == 2) {
                $arr['status'] = 1;
                $save = M('Recommend_rebate')->where(array('id' => $id,'payer_id'=>0))->save($arr);
            } else{
                $arr['state'] = 1;
                $save = M('rebate_apply')->where(array('id' => $id))->save($arr);
            }
        }

        if ($save) {
            $this->ajaxReturn('1', 'JSON');
        } else {
            $this->ajaxReturn('2', 'JSON');
        }

    }
    
    
    //其它返利
    public function other() {
        
        $condition = array();
        
        $month = I('month');
        $name = I('name');
        $name = trim($name);
        $status = I('status');
        $type = I('type');
        
        if( !empty($name) ){
            $distributor_obj = M('distributor');
            $where = array('name'=>array('like','%'.$name.'%'),'_logic'=>'or','wechatnum'=>array('like','%'.$name.'%'));
            $sear_dis_info = $distributor_obj->where($where)->select();
            
            $sear_uids = array();
            foreach( $sear_dis_info as $k_dis => $v_dis ){
                $sear_uids[] = $v_dis['id'];
            }
            
            $condition['uid']  =   array('in',$sear_uids);
        }
        
        if( !empty($month) ){
            $condition['month'] =   $month;
        }
        
        if( $status != null ){
            $condition['status'] =   $status;
        }
        if( $type != null ){
            $condition['type'] =   $type;
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        $result = $Rebate->get_other_rebate_info($page_info,$condition);
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }

        $this->con_url = base64_encode($con_url);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->status_name  =   $result['status_name'];
        $this->status = $status;
        $this->month    =   $month;
        $this->name = $name;
//        echo '<pre>';var_dump($result['list']);die;
        $this->display();
    }
    
    //其它返利结算
    public function pay() {
        $ids = I('ids');
        $ids = substr($ids, 1);
        $ids_arr = explode('_', $ids);
        if (!$ids_arr) {
            $this->ajaxReturn(2, 'JSON');
        }
        $count_model = M('rebate_other');
        $curr_month = date('Ym');
        foreach ($ids_arr as $id) {
            $rebate_month = $count_model->where(['id' => $id])->getField('month');
            if ($rebate_month == $curr_month) {
                $this->ajaxReturn(1, 'JSON'); 
            }
        }
        if(!$count_model->where(['id' => ['in', $ids_arr]])->save(['status' => 1])) {
           $this->ajaxReturn(2, 'JSON');
        }
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        $info = $count_model->where(['id' => ['in', $ids_arr]])->select();
        
        foreach( $info as $v ){
            $uid = $v['uid'];
            $rebate_money = $v['rebate_money'];
            $type = $v['type'];
            $type_name = $type==2?'团队业绩返利':'分红业绩分红';
            
            $Funds->rebate_aduit_recharge($uid,$rebate_money,0,$type_name);
        }
        
        $this->ajaxReturn(0, 'JSON');
    }
    
    
    
}

?>