<?php

/**
 * 	雨丝燕经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class IntegralorderAction extends CommonAction {

    public function index() {

        //搜索函数
        $get_name = trim(I('get.name'));
        $get_order_num = trim(I('get.order_num'));
        $get_status = trim(I('get.status'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        $get_p_id = trim(I('get.p_id'));
        $shipper = AllShipperCode();
        
        import('ORG.Util.Page');
        $order = M('integralorder');
        $distributor = M('distributor');
        $condition = array(); //SQL搜索条件


////        读取templet表
//
        $templet = M('integraltemplet');
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
                $getIDcondition['name'] = $get_name;
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
                    
                }else{
                    $condition['user_id']='0';
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
        $level_arr = C('LEVEL_NAME');
        $level_arr_flip = array_flip($level_arr);
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        $page_num=20;
        if ($count > 0) {
            $p = new Page($count, 20);
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
            $all_order_info = $order->field('order_num,p_id,p_name,num,price,integral')->where($condition)->select();
            
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
        $this->status = $get_status;
        $this->shipper = $shipper;

        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();
    }
    
    

    //品牌合伙人订单申请信息列表
    public function applyList() {
        import('ORG.Util.Page');
        $order = M('integralorder');
        $status = $this->_get('status');
        
        $condition = array(
            'o_id'  =>  0,
            'status'    =>  $status,
        );
        
        $shipper = AllShipperCode();
        
        $count = $order->where($condition)->count('distinct order_num');
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        
        if ($count > 0) {
            //look 后台审核最高级别经销商的订单
            $p = new Page($count, 20);
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
                
                $row = $order->field('p_id,num,price')->where(array('order_num' => $v['order_num']))->select();
                
                foreach ($row as $b => $d) {
                    $rol = M('integraltemplet')->field('name')->where(array('id' => $d['p_id']))->find();
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
        
        $this->shipper = $shipper;
        
//        if( $status == 1 ){
            $this->display();
//        }
//        else{
//            $this->display('applyList_send');
//        }
    }

    //订单申请审核
    public function audit() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        
        $order_obj = M('integralorder');
        $templet_obj = M('integraltemplet');
        
        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        $order_audit_result = $Order->radmin_audit($order_nums);
        
        if( $order_audit_result['code'] == 1 ){
            $this->add_active_log('订单申请审核：'.$order_audit_result['msg']);
        }
        
        $this->ajaxReturn($order_audit_result, 'json');
    }
    
    
    //订单申请审核为配送中
    public function audit_send() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        vendor("phpqrcode.phpqrcode");
        
        $orderobj = M('integralorder');
        
        $mids = I('mids');
        $mids = substr($mids, 1);
        $managers = explode('_', $mids);
        
        foreach ($managers as $m) {
            $orderobj->where(array('order_num' => $m))->save(array('status' => 2));
        }
        
        $this->ajaxReturn(array('status' => 1), 'json');
    }
    

    //搜索
    public function search() {
        import('ORG.Util.Page');
        $keyword = $_GET['keyword'];
        $order = M('integralorder');
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
            $p = new Page($count, 20);
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
                    $rol = M('integraltemplet')->field('name')->where(array('id' => $d['p_id']))->find();
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
        $order = M('integralorder');
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
            $p = new Page($count, 20);
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
        $order = M('integralorder');
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
            $p = new Page($count, 20);
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
    //删除订单
//    public function delOrder() {
//        $order_num = $_GET['id'];
//        $result = D('Order')->where(array('order_num' => $order_num))->delete();
//        if ($result) {
//            $this->success('删除成功');
//        } else {
//            $this->error('删除失败');
//        }
//    }

    //add by z
    //审核不通过订单
    public function nopass() {
        import('ORG.Util.Page');
        $order = M('integralorder');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 20);
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
                        $rol = M('integraltemplet')->field('name')->where(array('id' => $d['p_id']))->find();
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

    //配送中订单
    public function dispatching() {
        import('ORG.Util.Page');
        $order = M('integralorder');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 20);
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
                    $rol = M('integraltemplet')->field('name')->where(array('id' => $d['p_id']))->find();
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

    //已收货订单
    public function receipted() {
        import('ORG.Util.Page');
        $order = M('integralorder');
        $distributor = M('distributor');
        $count = $order->count('distinct order_num');
        if ($count > 0) {
            $p = new Page($count, 20);
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
                        $rol = M('integraltemplet')->field('name')->where(array('id' => $d['p_id']))->find();
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
    
    
    //订单限制
    public function order_limit(){
        $order_limit_obj = M('order_limit');
        import('ORG.Util.Page');
        
        $level_name = C('LEVEL_NAME');
        $level_name['0'] = '所有级别';
        
        
        $condition_order_lim = array();
        
        $count = $order_limit_obj->where($condition_order_lim)->count('id');
        
        if( $count > 0 ){
            $p = new Page($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;

            $list = $order_limit_obj->where($condition_order_lim)->limit($limit)->select();
            
            
            foreach( $list as $k => $v ){
                $v_level = $v['level'];
                
                $list[$k]['levname'] = $level_name[$v_level];
            }
            
            

            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->list = $list;
        }
        
        
        
        $this->display();
    }//end func order_limit
    
    
    
    
    //订单限制限制编辑
    public function order_limit_edit(){
        $id = I('id');
        
        $list = array(
            'total_num_min' =>  0,
            'total_money_min'   =>  0,
        );
        
        if( !empty($id) ){
            $order_limit_obj = M('order_limit');
        
            $condition_order_lim = array(
                'id'    =>  $id,
            );

            $list = $order_limit_obj->where($condition_order_lim)->find();
            
            
        }
        
        $level_name = C('LEVEL_NAME');
        $level_name[0] = '所有级别';
        
        
        $this->level_name = $level_name;
        $this->list = $list;
        $this->display();
    }//end func order_limit_edit
    
    
    //订单限制提交
    public function order_limit_post(){
        $order_limit_obj = M('order_limit');
        
        $id = I('id');
        $level = I('level');
        $is_first = I('is_first');
        $total_num_min = I('total_num_min');
        $total_money_min = I('total_money_min');
        
//        echo __URL__.'/order_limit';return;
//        print_r(I());return;
        
        if( empty($total_money_min) && empty($total_num_min) ){
            $this->error('《订单产品总数量最小限制》及《订单总金额最小限制》不能都为0');
            return;
        }
        
        if( $total_money_min < 0 || $total_num_min < 0 ){
            $this->error('参数错误！');
            return;
        }
        
        $is_first = 1;
        
        $save_info = array(
            'level' =>  $level,
            'is_first'  =>  $is_first,
            'total_num_min' =>  $total_num_min,
            'total_money_min'   =>  $total_money_min,
            'updated'   =>  time(),
        );
        
        
        if( empty($id) ){
            $condition_old = array(
                'level' =>  $level,
            );
            $old_order_limit = $order_limit_obj->where($condition_old)->find();
            
            if( !empty($old_order_limit) ){
                $this->error('该等级已经设置了限制，请找到该级别的限制进行编辑！');
            }
            
            
            $save_result = $order_limit_obj->add($save_info);
            $id = $order_limit_obj->getLastInsID();
        }
        else{
            $condition = array(
                'id'    =>  $id,
            );
            $save_result = $order_limit_obj->where($condition)->save($save_info);
        }
        
        
        if( $save_result ){
            $this->add_active_log('订单限制编辑，序号：'.$id);
            $this->success('编辑订单限制成功！',__URL__.'/order_limit');
        }
        else{
            $this->error('编辑订单限制失败！');
        }
        
        
    }//end func order_limit_post
    
    
    //订单限制删除
    public function order_limit_delete(){
        $id = I('id');
        
        if( empty($id) ){
            $this->error('参数错误！');
        }
        
        $order_limit_obj = M('order_limit');
        
        $condition = array(
            'id'    =>  $id,
        );
        $result = $order_limit_obj->where($condition)->delete();
        
        
        if( $result ){
            $this->add_active_log('订单限制规则删除，序号：'.$id);
            $this->success('删除成功！',__URL__.'/order_limit');
        }
        else{
            $this->error('删除失败，请重试！');
        }
        
    }//end func order_limit_delete
    
    
    
    
    //快递单号填写
    public function ordernb() {
        if (!$this->isPost()) {
            return false;
        }

        $order_num = I('post.order_num');
        $ordernumber = I('post.ordernumber');
        $shipper = I('shipper');

        $condition = array(
            'order_num' => $order_num,
        );

        $update_info = array(
            'shipper'   =>  $shipper,
            'ordernumber' => $ordernumber
        );
        $save = M('integralorder')->where($condition)->save($update_info);
        
        if( $save ){
            $this->add_active_log('快递单号填写，订单号：'.$order_num);
        }
        
        $this->ajaxReturn($save, 'JSON');
    }
    
    
    
    //订单统计
    public function order_count(){
        
        $month = I('month');
        $name = I('name');
        $pid = I('pid');
        
        $order_count_obj = M('integralorder_count');
        $distributor_obj = M('distributor');
        $templet_obj = M('integraltemplet');
        import('ORG.Util.Page');
        $page = '';
        
        if( empty($month) || !is_numeric($month) || strlen($month) != 6 ){
            $month = date('Ym');
        }
        
        $condition = array(
            'uid'   =>  array('neq','0'),
        );
        $condition_user = array();
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        $condtion_temp = array();
        
        if( !empty($month) ){
            $condition['month'] = $month;
        }
        
        
        if( !empty($name) ){
            $condition_user['_complex']  =   array(
                'name'  =>  $name,
                'wechatnum' =>  $name,
                '_logic'    =>  'or',
            );
        }
        
        if( !empty($pid) ){
            $condition['pid']   =   $pid;
            $condtion_temp['id']    =   $pid;
            $condition_top['pid']   =   $pid;
        }
        
        
        
        //------产品信息-----
        //$templet_info = $templet_obj->where($condtion_temp)->field('id,name')->select();
        $templet_info = $templet_obj->field('id,name')->select();
        
        $templet_key_info = array();
        if( !empty($templet_info) ){
            $templet_key_info = array(
                '0' =>  array(
                    'name'  =>  '所有产品',
                ),
            );
            
            foreach( $templet_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $templet_key_info[$v_tem_id] = $v_tem;
            }
        }
        //------end 产品信息-----
        
        
        
        //----总部订单统计信息----
        $condition_top['uid'] = 0;
        $top_list = $order_count_obj->where($condition_top)->order('pid asc')->select();
        
        
        if( !empty($top_list) ){
            foreach( $top_list as $k_top => $v_top ){
                $v_top_pid = $v_top['pid'];
                
                $top_list[$k_top]['p_name'] = $templet_key_info[$v_top_pid]['name'];
            }
        }
        //----总部订单统计信息----
        
        
        
        
         if( !empty($condition_user) ){
            $search_dis = $distributor_obj->where($condition_user)->select();
            
            $sear_uids = array();
            if( !empty($search_dis) ){
                foreach( $search_dis as $sear_k=>$sear_v ){
                    $sear_uids[] = $sear_v['id'];
                }
                
                array_unique($sear_uids);
                
                $condition['uid'] = ['in',$sear_uids];
            }
            else{
                $condition['uid'] = '-1';
            }
        }
        
        $count = $order_count_obj->where($condition)->count(); 
        
        
        
        
        if ($count > 0) {
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            //订单信息
            $list = $order_count_obj->where($condition)->limit($limit)->select();
            
            
            $all_uid = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                
                $all_uid[]  =   $v_uid;
            }
            
            array_unique($uids);
            
            
            //用户信息
            $condition_user = array(
                'id'    =>  array('in',$all_uid),
            );
            $search_dis = $distributor_obj->where($condition_user)->select();
            
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部',
                    'wechatnum'  =>  '总部',
                    'levname'   =>  '总部',
                )
            );
            foreach( $search_dis as $k_dis => $v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_info[$v_dis_uid] = $v_dis;
            }
            
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_month = $v['month'];
                $v_buy_money = $v['buy_money'];
                
                $list[$k]['name'] =   $dis_info[$v_uid]['name'];
                $list[$k]['wechatnum'] =   $dis_info[$v_uid]['wechatnum'];
                $list[$k]['levname'] =   $dis_info[$v_uid]['levname'];
                
                $list[$k]['p_name'] = $templet_key_info[$v_pid]['name'];
            }
            
            
            $page = $p->show();
        }//end if
        
        
        
        $this->templet_info = $templet_info;
        $this->top_list = $top_list;
        $this->page =   $page;
        $this->month = $month;
        $this->list =   $list;
        
        $this->display();
        
    }//end func order_count
    
    
    //生成无订单数据的用户订单统计（用于展现）
    public function create_order_count(){
        
        $month = I('month');
        
        $condition_user = array();
        
        $order_count = M('integralorder_count');
        
        $condition = array(
            'month' =>  $month
        );
        
        $field = 'uid';
        $order_count_info = $order_count->where($condition)->field($field)->select();
        
        if( !empty($order_count_info) ){
            $uids = array();
            foreach( $order_count_info as $k => $v ){
                $v_uid = $v['uid'];
                
                $uids[] = $v_uid;
            }
            
            $condition_user['id']  =   array('not in',$uids);
        }
        
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        $return_result = $Order->cal_order_count($month,$condition_user);
        
//        var_dump($return_result);
//        return;
        if( $return_result['code'] != 1 ){
            $this->error($return_result['msg']);
        }
        else{
            $this->success($return_result['msg']);
        }
        
    }//end func create_order_count

    //iframe查看订单详情
    public function index_iframe() {
        $distributor = M('distributor');
        $accept_user = [];
        $order = M('integralorder')->find(I('id'));
        $order_user = $distributor->find($order['user_id']);

        if ($order['o_id']) {
            $accept_user = $distributor->find($order['o_id']);
        }
        $this->order = $order;
        $this->order_user = $order_user;
        $this->accept_user = $accept_user;
        $this->display();
    }


    //填写快递页面
    public function index_kd(){
        $order_num=trim(I('order_num'));
        $info=M('integralorder')->where(array('order_num'=> $order_num))->field('id,order_num,shipper,ordernumber')->find();
        $shipper = AllShipperCode();
        $this->shipper=$shipper;
        $this->info=$info;
        $this->display();
    }
    //快递的提交方法
    public function set_kd(){

        $order_num=trim(I('order_num'));
        $ordernumber=trim(I('ordernumber'));
        $shipper=trim(I('shipper'));
        if($ordernumber == '' || $shipper == ''){
            $this->error('快递信息填写不完整!');
        }
        $condition = array(
            'order_num' => $order_num,
        );
        $data=[
            'shipper'=>$shipper,
            'ordernumber'=>$ordernumber,
        ];
        $res=M('integralorder')->where($condition)->save($data);
        if($res){
            $this->success('快递单号填写成功');
        }else{
            $this->error('填写失败！原因没做出改变！');
        }
    }
    //积分商城的完成发货
    public function finish_order_audit(){
        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        $integralorder=M('integralorder');
        $order_save_info = array(
            'status'    =>  2,
//            'paytime'    =>  time(),
        );

        foreach ($order_nums as $key => $value){
//            $order_info=$integralorder->where(['order_num'=>$value])->find();
            $integralorder->where(array('order_num' => $value))->save($order_save_info);

//
//            //订单审核模板消息
//            import('Lib.Action.Message','App');
//            $message = new Message();
//            $openid = M('distributor')->where(['id' => $order_info['user_id']])->getField('openid');
//            $message->push(trim($openid), $order_info, $message->order_audit_stock);
        }

        $audit_result=[
            'code' => 1,
            'msg' => '处理完成',
        ];
        $this->ajaxReturn($audit_result, 'json');
    }
}

?>