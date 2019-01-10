<?php

/**
 * 	雨林控股经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class StockorderAction extends CommonAction {

    public function index() {

        //搜索函数
        $get_name = $get_order_num = $get_status = '';
        if ($this->isGet()) {
            $get_name = trim(I('get.name'));
            $get_order_num = trim(I('get.order_num'));
            $get_status = trim(I('get.status'));
        }
        

        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');

        $condition = array(); //SQL搜索条件

        if (!empty($get_name)) {
            if( $get_name=='总部' ){
                $condition = array(
                    'o_id' => 0, //上级
//                    's_id' => 0, //供货商
                );
            }
            else{
                //先搜出名字所属ID才能找到提货下单
                $getIDcondition['name'] = $get_name;
                $lista = $distributor->where($getIDcondition)->find();

                if (!empty($lista)) {
                    $condition = array(
                        '_logic' => 'or',
                        'user_id' => $lista['id'],
                        'o_id' => $lista['id'], //上级
//                        's_id' => $lista['id'], //供货商
                    );
                }
            }
        }
        if (!empty($get_order_num)) {
            $condition['order_num'] = $get_order_num;
        }
        if (!empty($get_status)) {
            $condition['status'] = $get_status;
        }

        $count = $order->where($condition)->count('distinct order_num');
        $level_arr = C('LEVEL_NAME');
        $level_arr_flip = array_flip($level_arr);

        if ($count > 0) {
            $p = new Page($count, 8);
            $limit = $p->firstRow . "," . $p->listRows;
            //提货下单信息
            $applyList = $order->where($condition)->order('time desc')->group('order_num')->limit($limit)->select();
            
            $uids = array();
            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
                $v_s_id = $v['s_id'];
                $v_o_id = $v['o_id'];
                
                if( !isset($uids[$v_user_id]) ){
                    $uids[$v_user_id] = $v_user_id;
                }
//                if( !isset($uids[$v_s_id]) ){
//                    $uids[$v_s_id] = $v_s_id;
//                }
                if( !isset($uids[$v_o_id]) ){
                    $uids[$v_o_id] = $v_o_id;
                }
            }
            
            array_values($uids);
            
            $dis_info = $distributor->where(array('id' => array('in',$uids)))->select();
            
            $dis_info_key['0'] = array(
                'name'  =>  '总部'
            );
            
            foreach( $dis_info as $k_dis => $v_dis ){
                $v_dis_id = $v_dis['id'];
                
                $dis_info_key[$v_dis_id]    =   $v_dis;
            }
            

            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
//                $v_s_id =   $v['s_id'];
                $v_o_id = $v['o_id'];
                
                
                $applyList[$k]['name'] = $dis_info_key[$v_user_id]['name'];
                $applyList[$k]['phone'] = $dis_info_key[$v_user_id]['phone'];
                $applyList[$k]['levname'] = $dis_info_key[$v_user_id]['levname'];
                $applyList[$k]['o_name'] = $dis_info_key[$v_o_id]['name'];
//                $applyList[$k]['bossphone'] = $ros['phone'];

//                //如果供货商与上级不同则输出供货商信息
//                $applyList[$k]['s_name'] = '';
//                if ($v['o_id'] != $v['s_id']) {
//                    $s_info = $distributor->where(array('id' => $v['s_id']))->field('name')->find();
//                    $applyList[$k]['s_name'] = $s_info['name'];
//                }

                $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }
                $applyList[$k]['row'] = $row;
            }
            //dump($applyList);die;
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        $this->display();
    }
    
    

    //品牌合伙人提货下单申请信息列表
    public function applyList() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $status = $this->_get('status');
        
        $condition = array(
            'o_id'  =>  0,
            'status'    =>  $status,
        );
        
        
        $count = $order->where($condition)->count('distinct order_num');
        
        if ($count > 0) {
            //look 后台审核最高级别经销商的提货下单
            $p = new Page($count, 8);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->order('time desc')->where($condition)->group('order_num')->limit($limit)->select();
            
            $serach_uids = array();
            foreach( $applyList as $k => $v ){
                $v_uid  =   $v['user_id'];
                $v_oid =    $v['o_id'];
                
                $serach_uids[] = $v_uid;
                $serach_uids[] = $v_oid;
            }
            
            array_unique($serach_uids);
            
            $condition_all_dis = array(
                'id'    =>  array('in',$serach_uids),
            );
            
            $all_dis = M('distributor')->where($condition_all_dis)->select();
            
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部',
                    'phone' =>  '',
                    'levname'   =>  '总部',
                    'bossname'  =>  '',
                )
            );
            foreach( $all_dis as $k_dis => $v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_info[$v_dis_uid] = $v_dis;
            }
            
            foreach ($applyList as $k => $v) {
                $v_uid  =   $v['user_id'];
                $v_oid =    $v['o_id'];
                
                $applyList[$k]['name'] = $dis_info[$v_uid]['name'];
                $applyList[$k]['phone'] = $dis_info[$v_uid]['phone'];
                $applyList[$k]['levname'] = $dis_info[$v_uid]['levname'];
                $applyList[$k]['bossname'] = $dis_info[$v_uid]['bossname'];
                
                $applyList[$k]['o_name'] = $dis_info[$v_oid]['name'];
                
                $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                
                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }
                
                
                $applyList[$k]['row'] = $row;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        
//        if( $status == 1 ){
            $this->display();
//        }
//        else{
//            $this->display('applyList_send');
//        }
    }

    //提货下单申请审核
//    public function audit() {
//        if (!IS_AJAX) {
//            halt('页面不存在！');
//        }
//        
//        $order_obj = M('Waybill');
//        $templet_obj = M('templet');
//        
//        vendor("phpqrcode.phpqrcode");
//        $mids = I('mids');
//        $mids = substr($mids, 1);
//        $order_nums = explode('_', $mids);
//        
//        import('Lib.Action.Waybill','App');
//        $Waybill = new Waybill();
//        
//        $order_audit_result = $Waybill->radmin_audit($order_nums);
//        
//        if( $order_audit_result['code'] == 1 ){
//            $this->add_active_log('提货下单申请审核：'.$order_audit_result['msg']);
//        }
//        
//        $this->ajaxReturn($order_audit_result, 'json');
//    }
    
    
    //提货下单申请审核为已审核
//    public function audit_send() {
//        if (!IS_AJAX) {
//            halt('页面不存在！');
//        }
//        vendor("phpqrcode.phpqrcode");
//        
//        $orderobj = M('Waybill');
//        
//        $mids = I('mids');
//        $mids = substr($mids, 1);
//        $managers = explode('_', $mids);
//        
//        foreach ($managers as $m) {
//            $orderobj->where(array('order_num' => $m))->save(array('status' => 2));
//        }
//        
//        $this->ajaxReturn(array('status' => 1), 'json');
//    }
    

    //搜索
    public function search() {
        import('ORG.Util.Page');
        $keyword = $_GET['keyword'];
        $order = M('Waybill');
        $distributor = M('distributor');
        if ($_GET['id'] == 1) {
            $map = array(
                'order_num' => $keyword,
                '_logic' => 'or',
                's_name' => $keyword,
            );
        } elseif ($_GET['id'] == 2) {
            $where = array(
                'name' => $keyword,
            );
            $lista = $distributor->where($where)->find();
            $map = array(
                'user_id' => $lista['id'],
                '_logic' => 'or',
                'o_id' => $lista['id'],
            );
        } else {
            $map = array(
                'status' => $keyword,
            );
        }
        $count = $order->where($map)->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->where($map)->order('time desc')->group('order_num')->limit($limit)->select();
            foreach ($applyList as $k => $v) {
                $list = $distributor->where(array('id' => $v['user_id']))->find();
                $ros = $distributor->where(array('name' => $list['bossname']))->find();
                $applyList[$k]['name'] = $list['name'];
                $applyList[$k]['phone'] = $list['phone'];
                $applyList[$k]['levname'] = $list['levname'];
                $applyList[$k]['bossname'] = $list['bossname'];
                $applyList[$k]['bossphone'] = $ros['phone'];
                $row = $order->field('p_id,num')->where(array('order_num' => $v['order_num']))->select();
                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                }
                $applyList[$k]['row'] = $row;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        $this->display();
    }

    public function cont() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');
        
        $start_time = I('start_time');
        $end_time = I('end_time');
        
        if( !empty($start_time) && !empty($end_time) ){
            $start_time = $this->get_timestamp($start_time);
            $end_time = $this->get_timestamp($end_time);
            $end_time = $end_time+60*60 - 1;
            
            $condition_list['time'] = array(array('egt', $start_time), array('elt', $end_time));
            $condition_lista['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        
        $count = $distributor->where(array('pid' => 0, 'level' => 1))->count();
        if ($count > 0) {
            $p = new Page($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;
            $a = $distributor->where(array('pid' => 0, 'level' => 1))->field('id,name')->limit($limit)->select();
            foreach ($a as $k => $v) {
                //代理线最高代理下单的统计
                $money = 0;
                $xmoney = 0;
                
                $condition_list['user_id'] = $a[$k]['id'];
                $list = $order->where($condition_list)->group('order_num')->field('total_price')->select();
                if (!empty($list)) {
                    foreach ($list as $ke => $va) {
                        $money = $money + $list[$ke]['total_price'];
                    }
                }
                $a[$k]['money'] = $money;
                
                $condition_lista['tallestID'] = $a[$k]['id'];
                //代理线其他代理下单的统计
                $lista = $order->where($condition_lista)->group('order_num')->field('total_price')->select();
                if (!empty($lista)) {
                    foreach ($lista as $u => $i) {
                        $xmoney = $xmoney + $lista[$u]['total_price'];
                    }
                }
                $a[$k]['xmoney'] = $xmoney;
                $a[$k]['zmoney'] = $xmoney + $money;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('a', $a);
        }
        $this->display();
    }

    public function orsearch() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        $keyword = I('get.keyword');
        if ($start_time && $end_time) {
            if (!is_numeric($start_time) && !is_numeric($end_time)) {
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time) + 86399;
                $wherea['time'] = array(array('egt', $start_time), array('elt', $end_time));
                $where['time'] = array(array('egt', $start_time), array('elt', $end_time));
            }
        }
        if ($keyword) {
            $map['id'] = $keyword;
        }
        $map['pid'] = 0;
        $map['level'] = 1;
        $count = $distributor->where($map)->count();
        if ($count > 0) {
            $p = new Page($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;
            $a = $distributor->where($map)->field('id,name')->limit($limit)->select();
            foreach ($a as $k => $v) {
                //经销商线最高经销商下单的统计
                $money = 0;
                $xmoney = 0;
                $where['user_id'] = $a[$k]['id'];
                $list = $order->where($where)->group('order_num')->field('total_price')->select();

                foreach ($list as $ke => $va) {
                    $money = $money + $list[$ke]['total_price'];
                }
                $a[$k]['money'] = $money;
                //经销商线其他经销商下单的统计
                $wherea['tallestID'] = $a[$k]['id'];
                $lista = $order->where($wherea)->group('order_num')->field('total_price')->select();

                foreach ($lista as $u => $i) {
                    $xmoney = $xmoney + $lista[$u]['total_price'];
                }
                $a[$k]['xmoney'] = $xmoney;
                $a[$k]['zmoney'] = $xmoney + $money;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('a', $a);
        }
        $this->display();
    }

    //add by z
    //删除提货下单
//    public function delOrder() {
//        $order_num = $_GET['id'];
//        $result = D('Waybill')->where(array('order_num' => $order_num))->delete();
//        if ($result) {
//            $this->success('删除成功');
//        } else {
//            $this->error('删除失败');
//        }
//    }

    //add by z
    //审核不通过提货下单
    public function nopass() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 8);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->order('time desc')->where(array('status' => 4))->group('order_num')->limit($limit)->select();
            if (!empty($applyList)) {
                foreach ($applyList as $k => $v) {
                    $list = $distributor->where(array('id' => $v['user_id']))->find();
                    $ros = $distributor->where(array('name' => $list['bossname']))->find();
                    $applyList[$k]['name'] = $list['name'];
                    $applyList[$k]['phone'] = $list['phone'];
                    $applyList[$k]['levname'] = $list['levname'];
                    $applyList[$k]['bossname'] = $list['bossname'];
                    $applyList[$k]['bossphone'] = $ros['phone'];
                    $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                    foreach ($row as $b => $d) {
                        $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                        $row[$b]['pr_name'] = $rol['name'];
                        $applyList[$k]['sum'] += $d['num'] * $d['price'];
                    }
                    $applyList[$k]['row'] = $row;
                }
            } else {
                $row = "";
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        $this->display();
    }

    //已审核提货下单
    public function dispatching() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 8);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->order('time desc')->where(array('status' => 2))->group('order_num')->limit($limit)->select();
            foreach ($applyList as $k => $v) {
                $list = $distributor->where(array('id' => $v['user_id']))->find();
                $ros = $distributor->where(array('name' => $list['bossname']))->find();
                $applyList[$k]['name'] = $list['name'];
                $applyList[$k]['phone'] = $list['phone'];
                $applyList[$k]['levname'] = $list['levname'];
                $applyList[$k]['bossname'] = $list['bossname'];
                $applyList[$k]['bossphone'] = $ros['phone'];
                $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }
                $applyList[$k]['row'] = $row;
            }

            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        $this->display();
    }

    //已收货提货下单
    public function receipted() {
        import('ORG.Util.Page');
        $order = M('Waybill');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 8);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->order('time desc')->where(array('status' => 3))->group('order_num')->limit($limit)->select();
            if (!empty($applyList)) {
                foreach ($applyList as $k => $v) {
                    $list = $distributor->where(array('id' => $v['user_id']))->find();
                    $ros = $distributor->where(array('name' => $list['bossname']))->find();
                    $applyList[$k]['name'] = $list['name'];
                    $applyList[$k]['phone'] = $list['phone'];
                    $applyList[$k]['levname'] = $list['levname'];
                    $applyList[$k]['bossname'] = $list['bossname'];
                    $applyList[$k]['bossphone'] = $ros['phone'];
                    $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                    foreach ($row as $b => $d) {
                        $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                        $row[$b]['pr_name'] = $rol['name'];
                        $applyList[$k]['sum'] += $d['num'] * $d['price'];
                    }
                    $applyList[$k]['row'] = $row;
                }
            } else {
                $row = "";
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        $this->display();
    }
    
    
    //快递单号填写
    public function ordernb() {
        if (!$this->isPost()) {
            return false;
        }

        $order_num = I('post.order_num');
        $ordernumber = I('post.ordernumber');

        $condition = array(
            'order_num' => $order_num,
        );

        $update_info = array(
            'ordernumber' => $ordernumber
        );
        $save = M('Waybill')->where($condition)->save($update_info);
        
        if( $save ){
            $this->add_active_log('快递单号填写，提货下单号：'.$order_num);
        }
        
        $this->ajaxReturn($save, 'JSON');
    }
    
    
    
    //最低消费统计
    public function cont_user(){
        
        $month = I('month');
        $name = I('name');
        $level = I('level');
        
        import('Lib.Action.User','App');
        $User = new User();
        
        
        if( empty($month) || !is_numeric($month) || strlen($month) != 6 ){
            $month = date('Ym');
        }
        
        $condition = array();
        $condition_user = array();
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        
        if( !empty($month) ){
            $condition['month'] = $month;
        }
        
        if( !empty($level) ){
            $condition_user['level']    =   $level;
        }
        
        if( !empty($name) ){
            $condition_user['_complex']  =   array(
                'name'  =>  $name,
                'wechatnum' =>  $name,
                '_logic'    =>  'or',
            );
        }
        
        $condition['is_get_team_info'] = TRUE;
        
        $user_order_count = $User->get_users_order_count($condition,$condition_user,$page_info);
        
//        print_r($user_order_count);return;
        
        $page = '';
        $reba_data_info = array();
        $all_user_order_money = array();
        $all_user_rebate_percent = array();
        
        if( $user_order_count['code'] != 1 ){
            $all_dis_info = array();
        }
        else{
            $count_result = $user_order_count['result'];
            
            $dis_info = $count_result['dis_info'];
            $page =   $count_result['page'];
            $month = $count_result['month'];
        }
        
        $this->page =   $page;
        $this->month = $month;
        $this->list =   $dis_info;
        
        $this->display();
        
    }//end func cont_user
    
    
    //用户云仓库存记录
    public function stock(){
        
        $distributor_obj = M('distributor');
        $templet_obj = M('templet');
        $stock_point_obj = M('stock_point');
        
        $condition = array();
        
        $name = I('get.name');
        $pid = I('get.pid');
        
        if( is_numeric($pid) ){
            $condition['pid'] = $pid;
        }
        
        if( !empty($name) ){
            $sear_dis_info = $distributor_obj->where(array('name'=>$name))->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        //产品记录
        $temp_info = $templet_obj->field('id,name')->select();
        
        //获取云仓库存记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $result = $Stock->get_stock($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        
        
        $this->temp_info = $temp_info;
        $this->display();
    }//end func stock
    
    
    /**
     * 云仓库存日志
     */
    public function stock_log(){
//        $money_recharge_log = M('money_recharge_log');
        $distributor_obj = M('distributor');
//        import('ORG.Util.Page');
        $condition = array();
        
        $type = I("get.type");
        $name = I('get.name');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        if( is_numeric($type) ){
            $condition['type'] = $type;
        }
        
        if( !empty($name) ){
            $sear_dis_info = $distributor_obj->where(array('name'=>$name))->find();
            
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
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $result = $Stock->get_stock_log($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        
        $this->display();
        
        
    }//end func stock_log
    
    
    //充入云仓库存
    public function change_stock(){
        
        $templet_obj = M('templet');
        $distributor_obj  = M('distributor');
        
        $uid = I('get.uid');
        
        $condition = array(
            'uid'   =>  $uid,
        );
        
        $page_info = [];
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $condition_special = array(
            'key_for_temp_id'   =>  TRUE,
        );
        
        $result = $Stock->get_stock($page_info,$condition,$condition_special);
        $all_templet = $templet_obj->select();
        
        $list = array();
        $all_pid_str = '';
        
        $cal_id = 1;
        foreach( $all_templet as $k => $v ){
            $v_id = $v['id'];
            $v_name = $v['name'];
            
            $all_pid_str = $v_id.','.$all_pid_str;
            
            $default_array = array(
                'temp_info' =>  array(
                    'id'    =>  $v_id,
                    'name'  =>  $v_name,
                ),
                'num'   =>  0,
            );
            
            $list[$k] = isset($result['list'][$v_id])?$result['list'][$v_id]:$default_array;
            $list[$k]['cal_id'] =   $cal_id;
            $cal_id++;
        }
        
        $condition_dis = array(
            'id'    =>  $uid,
        );
        $dis_info = $distributor_obj->where($condition_dis)->find();
        
//        print_r($list);
//        print_r($result['list']);
//        return;
        
        $this->dis_info = $dis_info;
        $this->list = $list;
        $this->all_pid_str = $all_pid_str;
        $this->display();
    }//end func change_stock
    
    
    
    //充入云仓库存提交
    public function change_stock_post(){
        
//        print_r(I());return;
        
        $all_pid_str = I('all_pid');
        $uid = I('uid');
        $note = I('note');
        $type = I('type');
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        
        $all_pid = explode(',', $all_pid_str);
        
        $change_pids = array();
        $stock_info = array();
        foreach( $all_pid as $v_pid ){
            
            if( !is_numeric($v_pid) ){
                continue;
            }
            
            $p_num = I('tid_'.$v_pid);
            
            if( empty($p_num) || $p_num <= 0 ){
                continue;
            }
            
            $change_pids[$v_pid] = $p_num;
            
            $stock_info[] = array(
                'p_id'  =>  $v_pid,
                'num'   =>  $p_num,
            );
            
        }
        
//        print_r(I());return;
        
        
        if( empty($type) ){
            $this->error('请必须选择是充入或是扣除！');
            return;
        }
        
        
        //echo $type.'<br />';
        
//        print_r($stock_info);return;
        
        $res = $Stock->stock_point($uid,$stock_info,$type,$note);
        
        
        if( $res['code'] == 1 ){
            $this->add_active_log($res['msg']);
            $this->success($res['msg']);
        }
        else{
            $this->error($res['msg']);
        }
        
        
    }//end func change_stock_post
    
        public function order_all(){
        //搜索函数
        $get_name = trim(I('get.name'));
        $get_order_num = trim(I('get.order_num'));
        $get_status = trim(I('get.status'));
//        $start_time = trim(I('start_time'));
//        $end_time = trim(I('end_time'));
        $s_time=trim(I('s_time'));
        $get_p_id = trim(I('get.p_id'));
        $shipper = AllShipperCode();
        $ishead = I('ishead');
        $pay_type = I('pay_type');
        
        import('ORG.Util.Page');
        $order = M('stock_order');
        $distributor = M('distributor');
        $condition = array(); //SQL搜索条件
        
        ////        读取templet表
//
        $templet = M('Templet');
        $dis_templet_List = $templet->field('id,name')->select();
        $condition_temp = array();
        if(!empty($dis_templet_List)){
            foreach( $dis_templet_List as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                $condition_temp[$v_tem_id] = $v_tem;
            }
        }
        $this->assign('dis_templet_List', $dis_templet_List);
        
        //开始时间-结束时间
        if(!empty($s_time)){
            $time=explode('-',$s_time);
            $start_time=$time[0];
            $end_time=$time[1];
        }

        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        if (!empty($get_name)) {
            if( $get_name=='总部' ){
                $condition = array(
                    'o_id' => 0, //上级
//                    's_id' => 0, //供货商
                );
            }
            else{
                //先搜出名字所属ID才能找到订单
                $getIDcondition = array(
                    'name' => $get_name,
                    '_logic' => 'or',
                    'phone' => $get_name,
                    '_logic' => 'or',
                    'wechatnum' => $get_name
                );
                $lista = $distributor->where($getIDcondition)->select();

                $search_uids = [];
                
                foreach( $lista as $v_ser ){
                    $v_ser_id = $v_ser['id'];
                    $search_uids[] = $v_ser_id;
                }
                
                if (!empty($search_uids)) {
                    if( empty($condition) ){
                        $condition = array(
                            '_logic' => 'or',
                            'user_id' => ['in',$search_uids],
                            'o_id' => ['in',$search_uids], //上级
    //                        's_id' => $lista['id'], //供货商
                        );
                    }
                    else{
                        $condition['_complex'] = array(
                            '_logic' => 'or',
                            'user_id' => ['in',$search_uids],
                            'o_id' => ['in',$search_uids], //上级
    //                        's_id' => $lista['id'], //供货商
                        );
                    }
                    
                } else {
                    //没找到数据
                    $condition['user_id'] = 0;
                }
            }
        }
        if (!empty($get_order_num)) {
            $condition['order_num'] = $get_order_num;
        }

        if (!empty($get_status)) {
            $condition['status'] = $get_status;
        }

        if (!empty($get_p_id)) {
            $condition['p_id'] = $get_p_id;
        }
        $count = $order->where($condition)->count('distinct order_num');
        setLog($count);
        $level_arr = C('LEVEL_NAME');
        $level_arr_flip = array_flip($level_arr);
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        $page_num = 20;
        if ($count > 0) {
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            //订单信息
            $applyList = $order->where($condition)->order('time desc')->group('order_num')->limit($limit)->select();
            
            $uids = array();
            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
                $v_s_id = $v['s_id'];
                $v_o_id = $v['o_id'];
                
                if( !isset($uids[$v_user_id]) ){
                    $uids[$v_user_id] = $v_user_id;
                }
//                if( !isset($uids[$v_s_id]) ){
//                    $uids[$v_s_id] = $v_s_id;
//                }
                if( !isset($uids[$v_o_id]) ){
                    $uids[$v_o_id] = $v_o_id;
                }
            }
            
            array_values($uids);
            
            $dis_info = $distributor->where(array('id' => array('in',$uids)))->select();
            
            $dis_info_key['0'] = array(
                'name'  =>  '总部'
            );
            
            foreach( $dis_info as $k_dis => $v_dis ){
                $v_dis_id = $v_dis['id'];
                
                $dis_info_key[$v_dis_id]    =   $v_dis;
            }
            
            //订单信息
            $all_order_info = $order->field('order_num,p_id,p_name,num,price,style')->where($condition)->select();
            
            $all_order_key_info = array();
            foreach( $all_order_info as $k_ao => $v_ao ){
                $v_ao_order_num = $v_ao['order_num'];
                
                $all_order_key_info[$v_ao_order_num][] = $v_ao;
            }
            
            
            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
//                $v_s_id =   $v['s_id'];
                $v_o_id = $v['o_id'];
                $v_order_num = $v['order_num'];
                $v_ordernumber = $v['ordernumber'];
                $v_shipper = $v['shipper'];
                $v_ordernumber_arr = !empty($v_ordernumber)?explode(',', $v_ordernumber):[];
                
                $applyList[$k]['name'] = $dis_info_key[$v_user_id]['name'];
                $applyList[$k]['phone'] = $dis_info_key[$v_user_id]['phone'];
                $applyList[$k]['levname'] = $dis_info_key[$v_user_id]['levname'];
                $applyList[$k]['o_name'] = $dis_info_key[$v_o_id]['name'];
//                $applyList[$k]['bossphone'] = $ros['phone'];
                $applyList[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'未选择快递公司';
                $applyList[$k]['ordernumber_arr'] = $v_ordernumber_arr;
                $applyList[$k]['ordernumber_count'] = count($v_ordernumber_arr);

//                //如果供货商与上级不同则输出供货商信息
//                $applyList[$k]['s_name'] = '';
//                if ($v['o_id'] != $v['s_id']) {
//                    $s_info = $distributor->where(array('id' => $v['s_id']))->field('name')->find();
//                    $applyList[$k]['s_name'] = $s_info['name'];
//                }
                
                $the_order_info = isset($all_order_key_info[$v_order_num])?$all_order_key_info[$v_order_num]:array();
                
                $applyList[$k]['row'] = $the_order_info;
            }
            //dump($applyList);die;
            $this->status = $get_status;
            $page = $p->show();
            $this->page = $page;


            $this->assign('row', $row);
            $this->assign('applyList', $applyList);


        }
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        
        $this->con_url = base64_encode($con_url);

        $this->shipper = $shipper;
        $this->count=$count;
        $this->p=I('p');
        $this->limit=$page_num;

        $this->display();
        
    }
    
    
        //品牌合伙人订单申请信息列表
    public function stock_examine() {
        import('ORG.Util.Page');
        $order = M('stock_order');
        $status = $this->_get('status');
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        $condition = array(
            'o_id'  =>  0,
            'status'    =>  $status,
        );
        
        $shipper = AllShipperCode();
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        $count = $order->where($condition)->count('distinct order_num');
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        $page_num=20;
        if ($count > 0) {
            //look 后台审核最高级别经销商的订单
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $order->order('time desc')->where($condition)->group('order_num')->limit($limit)->select();
            
            $serach_uids = array();
            foreach( $applyList as $k => $v ){
                $v_uid  =   $v['user_id'];
                $v_oid =    $v['o_id'];
                
                $serach_uids[] = $v_uid;
                $serach_uids[] = $v_oid;
            }
            
            array_unique($serach_uids);
            
            $condition_all_dis = array(
                'id'    =>  array('in',$serach_uids),
            );
            
            $all_dis = M('distributor')->where($condition_all_dis)->select();
            
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部',
                    'phone' =>  '',
                    'levname'   =>  '总部',
                    'bossname'  =>  '',
                )
            );
            foreach( $all_dis as $k_dis => $v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_info[$v_dis_uid] = $v_dis;
            }
            
            foreach ($applyList as $k => $v) {
                $v_uid  =   $v['user_id'];
                $v_oid =    $v['o_id'];
                $v_shipper = $v['shipper'];
                
                $applyList[$k]['name'] = $dis_info[$v_uid]['name'];
                $applyList[$k]['phone'] = $dis_info[$v_uid]['phone'];
                $applyList[$k]['levname'] = $dis_info[$v_uid]['levname'];
                $applyList[$k]['bossname'] = $dis_info[$v_uid]['bossname'];
                $applyList[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'';
                
                $applyList[$k]['o_name'] = $dis_info[$v_oid]['name'];
                
                $row = $order->field('p_id,num,price,style')->where(array('order_num' => $v['order_num']))->select();
                
                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }
                
                
                $applyList[$k]['row'] = $row;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        }
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        
        $this->con_url = base64_encode($con_url);
        $this->shipper = $shipper;
        
//        if( $status == 1 ){
            $this->p=I('p');
            $this->count=$count;
            $this->limit=$page_num;
            $this->display();
//        }
//        else{
//            $this->display('applyList_send');
//        }
    }
    
    public function audit_order(){
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
//      var_dump($order_nums);die;
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $type = 'radmin';
        
        $order_audit_result = $Stock->audit_order($order_nums,$type);
        
        if( $order_audit_result['code'] == 1 ){
            $this->add_active_log('云仓库存订单申请审核：'.$order_audit_result['msg']);
        }
        
        $this->ajaxReturn($order_audit_result, 'json');
        
    }
    
    public function stock_set(){
        
        $level = C('LEVEL_NAME');
        $st_point = M('stock_set');
        $data = [];
        
        $res = $st_point->select();
        if(!empty($res)){
            
            foreach( $level as $k => $v ){
                $res_info['level'] = $k;
                $res_info['lv_name'] = $v;
                $res_num = $st_point->where(['level'=>$k])->find();
                if($res_num){
                    $res_num = $res_num['point'];
                }else{
                    $res_num = 0;
                }
                $res_info['num'] = $res_num;
                array_push($data,$res_info);
            }
        }else{
            foreach( $level as $k => $v ){
                $res_info['level'] = $k;
                $res_info['lv_name'] = $v;
                $res_info['num'] = 0;
                array_push($data,$res_info);
            }
        }
        
        $this->res_info = $data;
        $this->display();
    }
    
    public function save_stock(){
        
        $stock_set = M('stock_set');
        
        $levelname = C('LEVEL_NAME');
        $newinfo = [];
        $flag = false;
        
        $list = $stock_set->select();
        $oldinfo = [];
        foreach( $list as $v ){
            $oldinfo[$v['level']] = $v;
        }
        
        foreach($levelname as $level => $name ){
            $list = $stock_set->where(['level' => $level ])->select();
            
            $newinfo = [
//              'level'=>   $level,
                'point'=> I('point'.$level),
                'updated'=>  time(),
            ];
            
            if( empty($list) ){
                $newinfo['level'] = $level;
                $res = $stock_set->add($newinfo);
                if($res){
                    $flag = true;
                }
            }
            else{
                $res = $stock_set->where(['level'=>$level])->save($newinfo);
                if($res){
                    $flag = true;
                }
            }
        }
        
        if($flag){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败！");
        }
//      var_dump($stock);die;
    }

            //查看订单详情
    public function detail() {
        $status=I('get.status');
        $distributor = M('distributor');
        $accept_user = [];
        $order = M('stock_order')->find(I('id'));
        $order_user = $distributor->find($order['user_id']);
        if ($order['o_id']) {
            $accept_user = $distributor->find($order['o_id']);
        }
        $this->number = explode(',', $order['ordernumber']);
        $this->status=$status;
        $this->order = $order;
        $this->order_user = $order_user;
        $this->accept_user = $accept_user;
        $this->display();
    }
    
    public function stock_return_examine(){
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        $condition = [
            'status' => 0
        ];
        $result = $Stock->get_stock_refund_apply($page_info,$condition);
        $this->p = I('p');
        $this->count = $result['count'];
        $this->limit = $result['limit'];
        
        $this->list = $result['list'];
        $this->display();
    }
    
    public function stock_return(){
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        $condition = [];
        $result = $Stock->get_stock_refund_apply($page_info,$condition);
        $this->p = I('p');
        $this->count = $result['count'];
        $this->limit = $result['limit'];
        
        $this->list = $result['list'];
        $this->display();
    }
    
    public function audit_stock_return(){
        
        $mids = I('mids');
        $mids = substr($mids, 1);
        $apply_id = explode('_', $mids);
//      var_dump($order_nums);die;
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $pass = I('pass');
        
        $result_info = [];
        $flag = true;
        foreach($apply_id as $k => $v){
            $result = $Stock->pass_stock_refund_apply($v,$pass);
            if($result['code']!=1){
                $flag = flase;
            }
        }
        
        if($flag){
            $result_info = [
                'code' => 1,
                'msg' => '审核成功！'
            ];
        }else{
            $result_info = [
                'code' => 1,
                'msg' => '审核失败！'
            ];
        }
        
        $this->ajaxReturn($result_info);
    }

}

?>