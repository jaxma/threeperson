<?php

/**
 * 	topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class FundsAction extends CommonAction {

    
    //经销商资金记录
    public function index(){
        
        $money_funds_obj = M('money_funds');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');
        $condition = array();
        
        
        
        
        $name = trim(I('get.name'));

        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum' => $name,
                'phone'=>$name
            ];
            $sear_dis_info = $distributor_obj->where($where)->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        
        $count = $money_funds_obj->where($condition)->count();

        $page_num = 20;
        $list = array();
        if( $count > 0 ){
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            
            
            
            
            $list = $money_funds_obj->where($condition)->order('id desc')->limit($limit)->select();
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
            }
            
            array_values($uids);
            
            $condition_dis = array();
            $dis_info = $distributor_obj->where($condition_dis)->select();
            
            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){
                
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }
            
            
            //可提现数设置
            $money_min_refund = M('money_min_refund');
            $min_refund_set_info = $money_min_refund->where(array('id'=>'1'))->find();
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_recharge_money = $v['recharge_money'];
                $the_dis_level = $dis_key_info[$v_uid]['level'];
                
                $the_refund_money_key = 'level'.$the_dis_level;
                
                $the_refund_money = bcsub($v_recharge_money,$min_refund_set_info[$the_refund_money_key],2);
        
                if( $the_refund_money < 0 ){
                    $the_refund_money = 0;
                }
                
                
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['refund_money'] = $the_refund_money;
                
            }
            //-----end 整理添加相应其它表的信息-----
            
            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->list = $list;
        }
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }

        $this->con_url = base64_encode($con_url);

        $this->count = $count;
        $this->p = I('p');
        $this->limit = $page_num;
        $this->display();
    }
    
    //经销商充值申请
    public function money_apply(){
        
        $money_apply_obj = M('money_apply');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');
        
        $status = I('get.status');
        $search_status = I('get.search_status');
        $audit_id = I('get.audit_id');
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        $id = trim(I('get.id'));

        $condition = array();
        $condition_search = array();
        $status_name = array(
            '0' =>  '未审核',
            '1' =>  '已审核',
            '2' =>  '不通过',
        );


        if( $status != null ){
            $condition['status'] = $status;
            if ($status == '1') {
                $condition['status'] = ['in',['1','2']];
            }else {
                $condition['status'] = '0';
            }
            $condition_search=$condition;
        }
        if($search_status != null){
            $condition['status'] = $search_status;

            $condition_search=$condition;
        }
        if( is_numeric($audit_id) ){
            $condition['audit_id'] = $audit_id;
            $condition_search=$condition;
        }
        if( is_numeric($id) ){
            $condition['id'] = $id;
            $condition_search=$condition;
        }

        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum'=>$name,
                '_logic' => 'or',
                'phone'=>$name,
            ];

            $sear_dis_info = $distributor_obj->where($where)->find();
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
            $condition_search=$condition;
        }

        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
            $condition_search=$condition;
        }


        $count = $money_apply_obj->where($condition_search)->count();

        $list = array();
        $page_num = 20;
        if( $count > 0 ){
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            
            $list = $money_apply_obj->where($condition_search)->order('id desc')->limit($limit)->select();

            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_audit_id]) ){
                    $uids[$v_audit_id] = $v_audit_id;
                }
            }
            
            array_values($uids);
            
            $condition_dis = array();
            $dis_info = $distributor_obj->where($condition_dis)->select();
            
            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){
                
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }
            
            $dis_key_info['0']['name'] = '总部';
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                $v_status = $v['status'];
                
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['audit_info'] = $dis_key_info[$v_audit_id];
            }
            //-----end 整理添加相应其它表的信息-----
            
            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->list = $list;
            $this->count = $count;
        }


        $this->p = I('p');
        $this->limit = $page_num;
        $this->status = $status;
        $this->display();
    }
    
    
    //审核充值申请
    public function apply_pass(){
        $pass = I('pass');
        $id = I('mids');
        $ids = substr($id, 1);
        $e_ids = explode('_', $ids);
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        foreach ( $e_ids as $ids ){
            $return_result = $Funds->apply_pass($ids,$pass);
            if( $return_result['code'] == 1 ){
                $this->add_active_log('审核充值申请：'.$return_result['msg']);
            }

        }
        $this->ajaxReturn($return_result, 'JSON');

    }
    
    
    
    //充值记录
    public function money_recharge_log(){
        
//        $money_recharge_log = M('money_recharge_log');
        $distributor_obj = M('distributor');
//        import('ORG.Util.Page');
        $condition = array();
        
        $type = I("get.type");
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        if( is_numeric($type) ){
            $condition['type'] = $type;
        }
        
        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum' => $name,
                'phone'=>$name
            ];
            $sear_dis_info = $distributor_obj->where($where)->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $result = $Funds->get_money_recharge_log($page_info,$condition);
        $this->count = $result['count'];
//        $this->p = $result['page'];
        $this->p = I('p');
        $this->limit = $result['limit'];
        $this->list = $result['list'];
        

//        $count = $money_recharge_log->where($condition)->count();
//        
//        
//        $list = array();
//        if( $count > 0 ){
//            $p = new Page($count, 20);
//            $limit = $p->firstRow . "," . $p->listRows;
//            
//            $list = $money_recharge_log->where($condition)->order('id desc')->limit($limit)->select();
//            
//            //-----整理添加相应其它表的信息-----
//            $uids = array();
//            $applys = array();
//            $order_notes = array();
//            
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                $v_source_id = $v['source_id'];
//                
//                if( !isset($uids[$v_uid]) ){
//                    $uids[$v_uid] = $v_uid;
//                }
//                if( !isset($uids[$v_source_id]) ){
//                    $uids[$v_source_id] = $v_source_id;
//                }
//            }
//            
//            array_values($uids);
//            
//            $condition_dis = array();
//            $dis_info = $distributor_obj->where($condition_dis)->select();
//            
//            $dis_key_info[0]['name'] = '总部';
//            foreach( $dis_info as $k_dis=>$v_dis ){
//                
//                $v_dis_uid = $v_dis['id'];
//                
//                $dis_key_info[$v_dis_uid] = $v_dis;
//            }
//            
//            
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                $v_source_id = $v['source_id'];
//                
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
//                $list[$k]['source_info'] = $dis_key_info[$v_source_id];
//            }
//            //-----end 整理添加相应其它表的信息-----
//            
//            //*分页显示*
//            $page = $p->show();
//            $this->page = $page;
//            $this->list = $list;
//        }





        $this->display();
    }
    
    //扣费记录
    public function money_charge_log(){
        
//        $money_charge_log_obj = M('money_charge_log');
        $distributor_obj = M('distributor');
//        import('ORG.Util.Page');
        $condition = array();
        
        $type = I("get.type");
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        if( is_numeric($type) ){
            $condition['type'] = $type;
        }
        
        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum' => $name,
                'phone'=>$name,
            ];
            $sear_dis_info = $distributor_obj->where($where)->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $result = $Funds->get_money_charge_log($page_info,$condition);
        $this->list = $result['list'];
        $this->count = $result['count'];
//        $this->page = $result['page'];
        $this->p = I('p');
        $this->limit=$result['limit'];

        
//        $count = $money_charge_log_obj->where($condition)->count();
//        
//        $list = array();
//        if( $count > 0 ){
//            $p = new Page($count, 20);
//            $limit = $p->firstRow . "," . $p->listRows;
//            
//            $list = $money_charge_log_obj->where($condition)->order('id desc')->limit($limit)->select();
//            
//            //-----整理添加相应其它表的信息-----
//            $uids = array();
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                
//                if( !isset($uids[$v_uid]) ){
//                    $uids[$v_uid] = $v_uid;
//                }
//            }
//            
//            array_values($uids);
//            
//            $condition_dis = array();
//            $dis_info = $distributor_obj->where($condition_dis)->select();
//            
//            $dis_key_info = array();
//            foreach( $dis_info as $k_dis=>$v_dis ){
//                
//                $v_dis_uid = $v_dis['id'];
//                
//                $dis_key_info[$v_dis_uid] = $v_dis;
//            }
//            
//            
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
//            }
//            //-----end 整理添加相应其它表的信息-----
//            
//            //*分页显示*
//            $page = $p->show();
//            $this->page = $page;
//            $this->list = $list;
//        }
        
        $this->display();
    }
    
    
    //AJAX获取申请信息
    public function get_apply_info(){
        
        $apply_id = I('post.id');
        
        $money_apply_obj = M('money_apply');
        $distributor_obj = M('distributor');
        
        
        $return_result = array(
            'code'  =>  0,
            'msg'   =>  '',
            'info'  =>  array(),
        );
        
        
        $condition = array(
            'id'    =>  $apply_id,
        );
        
        $list = $money_apply_obj->where($condition)->find();
        
        
        if( empty($list) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有查询到申请充值信息',
                'info'  =>  array(),
            );
            $this->ajaxReturn($return_result, 'json');return;
        }
        
        $head_info = array(
            'name'  =>  '总部'
        );
        
        $v_uid = $list['uid'];
        $v_audit_id = $list['audit_id'];

        if( $v_uid == 0 ){
            $dis_info = $head_info;
        }
        else{
            $dis_info = $distributor_obj->where(array('id'=>$list['uid']))->find();
        }

        if( $v_audit_id == 0 ){
            $audit_info = $head_info;
        }
        else{
            $audit_info = $distributor_obj->where(array('id'=>$list['audit_id']))->find();
        }

        $list['dis_info'] = $dis_info;
        $list['audit_info'] = $audit_info;
        $list['created_format'] = date('Y-m-d H:i:s',$list['created']);
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '',
            'info'  =>  $list,
        );
        
        $this->ajaxReturn($return_result, 'json');return;
    }
    
    
    //最低可提现数设置
    public function min_refund_set(){
        $money_min_refund = M('money_min_refund');
        $set_info = $money_min_refund->where(array('id'=>'1'))->find();
        
        if( empty($set_info) ){
            $arr = [
                'id'    =>  1,
                'updated'   =>  0,
            ];
            $money_min_refund->add($arr);
        }
        
        
        $level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        
        $this->level_num = $level_num;
        $this->level_name = $level_name;
        $this->set_info = $set_info;
        $this->display();
    }
    
    
    public function min_refund_set_submit(){
        if (!$this->isPost()) {
            $this->error('修改失败');
            return;
        }
        
        $money_min_refund = M('money_min_refund');
        
        $save = array(
            'level1' => I('post.level1'),
            'level2' => I('post.level2'),
            'level3' => I('post.level3'),
            'level4' => I('post.level4'),
            'level5' => I('post.level5'),
            'level6' => I('post.level6'),
            'updated'   =>  time(),
        );
        $a = $money_min_refund->where('id=1')->save($save);

        
        if ($a) {
            $this->add_active_log('最低可提现数更改');
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
    
    
    
    //提现列表
    public function money_refund(){
        
        $money_refund_obj = M('money_refund');
        $distributor_obj = M('distributor');
        
        
        $condition = array();
        
        
        $name = trim(I('get.name'));
        $start_time=strtotime(trim(I('start_time')));
        $end_time=strtotime(trim(I('end_time')));

        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum' => $name,
                'phone'=>$name
            ];
            $sear_dis_info = $distributor_obj->where($where)->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        if( !empty($start_time) && !empty($end_time) ){
            $condition['created'] =   ['between',[$start_time,$end_time]];
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $result = $Funds->get_money_refund($page_info,$condition);
        
//        print_r($result);return;
        
//        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->p=I('p');
        $this->count=$result['count'];
        $this->limit=$result['limit'];
        
        $this->display();
    }
    
    
    //提现申请列表
    public function money_refund_apply(){
        
        $distributor_obj = M('distributor');
        
        
        $condition = array();
        
        
        $status = I('get.status');
        $search_status = I('get.search_status');
        $audit_id = I('get.audit_id');
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');

        $condition = array();
        $condition_search = array();
        $status_name = array(
            '0' =>  '未审核',
            '1' =>  '已审核',
            '2' =>  '不通过',
        );


        if( $status != null ){
            $condition['status'] = $status;
            if ($status == '1') {
                $condition['status'] = ['in',['1','2']];
            }else {
                $condition['status'] = '0';
            }
            $condition_search=$condition;
        }
        if($search_status != null){
            $condition['status'] = $search_status;

            $condition_search=$condition;
        }

        if( is_numeric($audit_id) ){
            $condition['audit_id'] = $audit_id;
            $condition_search=$condition;
        }
        
        if( !empty($name) ){
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum'=>$name,
                '_logic' => 'or',
                'phone'=>$name,
            ];
            $sear_dis_info = $distributor_obj->where($where)->find();

            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
            $condition_search=$condition;
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
            $condition_search=$condition;
        }
        
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $result = $Funds->get_money_refund_apply($page_info,$condition_search);
        
//        print_r($result);return;
        
//        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->p=I('p');
        $this->count=$result['count'];
        $this->limit=$result['limit'];
        $this->status=$status;
        $this->search_status=$search_status;
        $this->display();
    }
    
    
    //审核提现申请
    public function apply_refund_pass(){
        $audit_remark = I('post.audit_remark');
        $pass = I('pass');
        $ids = I('mids');

        $ids = substr($ids, 1);
        $eids = explode('_', $ids);
        import('Lib.Action.Funds','App');
        $Funds = new Funds();

        foreach ( $eids as $ids ){
            $return_result = $Funds->apply_refund_pass($ids,$pass);
            if( $return_result['code'] == 1 ){
                $this->add_active_log('审核提现申请'.$return_result['msg']);
            }
        }
        $this->ajaxReturn($return_result, 'JSON');
    }
    
    
    
    //总部提现提交
    public function refund_submit(){
        
        $uid = I('post.uid');
        $refund_money = I('post.refund_money');
        
//        echo $uid.'<br />';
//        echo $refund_money;
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        $refund_res = $Funds->refund($uid,$refund_money,1);
        
//        print_r($refund_res);
        
        if( $refund_res['code'] == 1 ){
            $this->add_active_log('总部提现提交'.$refund_res['msg']);
        }
        
        if( IS_AJAX ){
            $this->ajaxReturn($refund_res,'json');
        }
        else{
            if( $refund_res['code'] != 1 ){
                $this->error($refund_res['msg']);
                return;
            }
            $this->success('提现成功！');
        }
    }
    
    //扣费
    public function charge_money(){
        
        $uid = I('uid');
        
        $money_funds_obj = M('money_funds');
        $distributor_obj = M('distributor');
        
        $this->uid = $uid;
        $this->money_funds = $money_funds_obj->where(['uid'=>$uid])->find();
        $this->dis_info = $distributor_obj->where(['id'=>$uid])->find();
        $this->display();
    }
    
    
    //总部扣费提交
    public function charge_submit(){
        
        $uid = I('post.uid');
        $charge_money = I('post.charge_money');
        
//        echo $uid.'<br />';
//        echo $refund_money;
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        $charge_type = 'radmin';
        $refund_res = $Funds->charge_money($uid,$charge_money,$charge_type);
        
//        print_r($refund_res);return;
        
        
        if( $refund_res['code'] == 1 ){
            //扣除业绩
            import('Lib.Action.Team','App');
            (new Team)->charge_person_money($uid, $charge_money);
            
            $this->add_active_log('总部扣费提交'.$refund_res['msg']);
        }
        
        
//        if( IS_AJAX ){
//            $this->ajaxReturn($refund_res,'json');
//        }
//        else{
            if( $refund_res['code'] != 1 ){
                $this->error($refund_res['msg']);
                return;
            }
            
            $this->success('扣费成功！');
//        }
    }
    
    //更改审核备注
    public function change_refund_audit_remark(){
        
        $id = I('post.id');
        $remark = I('post.remark');
        
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        $change_result = $Funds->change_refund_audit_remark($id,$remark,0);
        
        $this->add_active_log('更改提现审核备注'.$change_result['msg']);
        $this->ajaxReturn($change_result, 'JSON');
    }
    

    
    //统计
    public function count(){
        
        $money_funds_obj = M('money_funds');
        $money_min_refund_obj = M('money_min_refund');
        $money_recharge_log_obj = M('money_recharge_log');
        $distributor_obj = M('distributor');
        
        
        // 1.总充值数：所经销商截止当天的总充值（不包括订单充值的）
        // 2.流通数：所有经销商当前充值金额总数（就是经销商后台的虚拟币汇总） 
        // 3.可提取数：所有经销商目前可提取虚拟币总数 
        // 4.支出数：历史总的提取数+历史总扣费金额-历史总订单充值金额  //即为：历史总提现数 + 历史总扣费 - 历史总订单返现
        
        
        //最低提取金额设置
//        $set_info = $money_min_refund_obj->where(array('id'=>'1'))->find();
//        
//        
//        //申请充值记录，统计总充值数
//        $condition_recharge_log = array(
//            'type'  =>  array('in','1,3'),//1申请充值,3订单返现
//        );
//        $money_recharge_log = $money_recharge_log_obj->where($condition_recharge_log)->select();
//        
//        $total_real_recharge_money = 0;//总充值数
//        $total_order_recharge_money = 0;//历史总订单返现
//        
//        foreach( $money_recharge_log as $key => $val ){
//            $val_money = $val['money'];
//            $val_type = $val['type'];
//            
//            
//            if( $val_type == 1 ){
//                //总充值数
//                $total_real_recharge_money = bcadd($total_real_recharge_money,$val_money,2);
//            }
//            elseif( $val_type == 3 ){
//                //历史总订单充值金额
//                $total_order_recharge_money = bcadd($total_order_recharge_money, $val_money,2);
//            }
//            
//        }
        
        
        //资金信息表的计算
        $total_recharge_money = 0;//流通数
        $total_can_refund_money = 0;//可提取数
        $total_pay_money = 0;//总支出数
        $total_his_pay_money = 0;//资金表的历史支出数（未计算订单返现）
        $total_real_recharge_money = 0;//总充值数
        
        
        $money_funds = $money_funds_obj->select();
        
        
        //得到用户某些信息
        $uids = array();
        foreach( $money_funds as $key => $val ){
            $uids[] = $val['uid'];
            $val_recharge_money = $val['recharge_money'];
            $val_apply_money = $val['apply_money'];
            $val_can_refund_money = $val['can_refund_money'];
            $val_his_recharge_money = $val['his_recharge_money'];
            $val_his_charge_money = $val['his_charge_money'];
            
            
            $total_real_recharge_money = bcadd($total_real_recharge_money, $val_his_recharge_money,2);
            $total_recharge_money = bcadd($total_recharge_money, $val_recharge_money,2);
            $total_can_refund_money = bcadd($total_can_refund_money, $val_can_refund_money,2);
            $total_pay_money = bcadd($total_pay_money, $val_his_charge_money,2);
        }
        
//        $condition_dis['id']    =   array('in',$uids);
//        $field_dis = 'id,level';
//        $dis_info = $distributor_obj->where($condition_dis)->field($field_dis)->select();
//        $dis_key_info   =   array();//以UID为数组
//        
//        foreach( $dis_info as $key => $val ){
//            $val_id = $val['id'];
//            $val_level = $val['level'];
//            
//            $dis_key_info[$val_id]  =   $val_level;
//        }
        
        
        
        
        
//        foreach( $money_funds as $key => $val ){
//            $val_uid = $val['uid'];
//            $val_recharge_money = $val['recharge_money'];
//            $val_apply_money = $val['apply_money'];
//            $val_refund_money = $val['refund_money'];
//            $val_his_recharge_money = $val['his_recharge_money'];
//            $val_his_charge_money = $val['his_charge_money'];
//            $val_his_refund_money = $val['his_refund_money'];
//            
//            //流通数
//            $total_recharge_money = bcadd($total_recharge_money, $val_recharge_money,2);
//            
//            //可提取数
////            $the_level = $dis_key_info[$val_uid];
////            $the_min_refund_money = $this->get_min_refund_money($the_level,$val_recharge_money,$set_info);
////            $total_can_refund_money  = bcadd($total_can_refund_money, $the_min_refund_money,2);
//            
//            //支出数
//            $the_his_pay_money = bcadd($val_his_charge_money,$val_his_refund_money,2);
//            $total_his_pay_money = bcadd($total_his_pay_money,$the_his_pay_money,2);
//        }
        
        //总支出数
//        $total_pay_money = bcsub($total_his_pay_money,$total_order_recharge_money,2);
        
        
        $count_info = array(
            'total_real_recharge_money' =>  $total_real_recharge_money,//总充值数
            'total_recharge_money'  =>  $total_recharge_money,//流通数
            'total_can_refund_money'    =>  $total_can_refund_money,//可提取数
            'total_pay_money'   =>  $total_pay_money,//总支出数
        );
        
        
        $this->count_info = $count_info;
        
        $this->display();
    }
    
    
    
    /**
     * 获取退款金额
     * 
     * @param int $level
     * @param decimal $money
     * @return decimal
     */
    private function get_min_refund_money($level,$recharge_money,$set_info){
        
        if( $recharge_money == 0 || $level == 0 || is_null($level) || empty($set_info) ){
            return 0;
        }
        
//        $money_min_refund = M('money_min_refund');
//        $set_info = $money_min_refund->where(array('id'=>'1'))->find();
        
        $min_refund_key = 'level'.$level;
        $min_refund = isset($set_info[$min_refund_key])?$set_info[$min_refund_key]:0;
        
        
        $refund_money = bcsub($recharge_money,$min_refund,2);
        
        if( $refund_money < 0 ){
            $refund_money = 0;
        }
        
        
        return $refund_money;
    }
    
    
    
    //总部充值
    public function recharge(){
        $uid = I('get.uid');
        
        if( empty($uid) ){
            $this->error('请选择用户！');
        }
        
        $distributor_obj = M('distributor');
        $money_funds_obj = M('money_funds');
        
        $where_dis = array(
            'id'   =>  $uid,
        );
        $dis_info = $distributor_obj->where($where_dis)->find();
        
        //查看该经销商的资金表
        $money_funds = $money_funds_obj->where(array('uid'=>$uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        
        
        $this->recharge_money = $recharge_money;//虚拟币金额
        $this->dis_info =   $dis_info;
        $this->display();
    }
    
    //总部充值提交
    public function recharge_submit(){
        
        $uid = I('post.uid');
        $recharge_money = I('post.recharge_money');
        $note = I('post.note');
        
        // print_r($this->_post());return;
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        $recharge_info['note']  =   $note;
        
        $recharge_result = $Funds->recharge($uid,$recharge_money,'radmin_recharge',$recharge_info);
        
        if( $recharge_result['code'] != 1 ){
            $this->error($recharge_result['msg']);
        }
        else{
            $this->add_active_log('总部充值，用户ID:'.$uid.'，金额：'.$recharge_money);
            
            $msg = !empty($recharge_result['msg'])?$recharge_result['msg']:'充值成功！';
            $this->success($msg);
        }
        
        
    }
    
    //------------------*****iframe弹出事件****-------------
    public function money_apply_iframe(){
        $id=I('get.id');
        $info=M('money_apply')->find($id);
        $this->apply_img=$info['apply_img'];
        $this->display();
    }


}

?>