<?php

/**
 * 	雨丝燕经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class MallorderAction extends CommonAction {
    private $model;
    private $cat_model;
    public function _initialize() {
        parent::_initialize();
        $this->model = M('mall_templet');
        $this->cat_model = M('mall_templet_category');
    }


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
        $order = M('mall_order');
        $distributor = M('distributor');
        $condition = array(); //SQL搜索条件


////        读取templet表
//
        $templet = M('mall_templet');
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
        $level_arr = C('LEVEL_NAME');
        $level_arr_flip = array_flip($level_arr);
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        $page_num=20;
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
            $all_order_info = $order->field('order_num,p_id,p_name,num,price')->where($condition)->select();
            
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
            $this->count=$count;

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
        $this->display();
    }
    
    

    //品牌合伙人订单申请信息列表
    public function applyList() {
        import('ORG.Util.Page');
        $order = M('mall_order');
        $status = $this->_get('status');
        
        $condition = array(
            'o_id'  =>  '0',
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
                    $rol = M('mall_templet')->field('name')->where(array('id' => $d['p_id']))->find();
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
        
        $order_obj = M('mall_order');
        $templet_obj = M('mall_templet');
        
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
        
        $orderobj = M('mall_order');
        
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
        $order = M('mall_order');
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
        $order = M('mall_order');
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
        $order = M('mall_order');
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
        $order = M('mall_order');
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
        $order = M('mall_order');
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
        $order = M('mall_order');
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
        $order_limit_obj = M('mall_order_limit');
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
            $order_limit_obj = M('mall_order_limit');
        
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
        $order_limit_obj = M('mall_order_limit');
        
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
        
        $order_limit_obj = M('mall_order_limit');
        
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
        $save = M('mall_order')->where($condition)->save($update_info);
        
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
        
        $order_count_obj = M('mall_order_count');
        $distributor_obj = M('distributor');
        $templet_obj = M('mall_templet');
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
        
        $order_count = M('mall_order_count');
        
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


    //--------*********商品分类*****------------

    //商品分类列表
    public function category_index() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $count =  $this->cat_model->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres =  $this->cat_model->limit($limit)->select();
            //排序
            $list=$Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }

    //添加分类
    public function category_add() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $reduction_category = M('mall_templet_category');
        $cate = $reduction_category->select();
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);
        $this->display();
    }

    public function category_insert()
    {
        $image = $this->upload();
        $data = array(
            'name' => trim(I('post.name')),
            'image' =>$image,
            'statu' => trim(I('post.statu')),
            'pid' => trim(I('post.pid')),
            'time' => time(),
            'active' =>trim(I('post.active')),
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $this->success('添加成功', category_index);
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function category_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $id = $_GET['id'];
        $row = $this->cat_model->find($id);
        $listres =  $this->cat_model->select();
        $list=$Team->sortt($listres);


        $this->list = $list;
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function category_update()
    {
        $id = I('post.id');
        if ($_FILES['image']['size'] == 0) {
            $image = I('post.old_image');
        } else {
            $model_info =$this->cat_model->where(array('id'=>$id))->select();
            $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
            @unlink($url);
            $image = $this->upload();
        }

        $data = array(
            'name' => trim(I('post.name')),
            'image' => $image,
            'statu' => trim(I('post.statu')),
            'pid' =>trim(I('post.pid')),
            'time' => time(),
            'active' =>trim(I('post.active')),
        );

        $res =  $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功", category_index);
        }
    }


    //删除
    public function category_delete()
    {

        $id = I('id');
        $model_info =$this->cat_model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
        @unlink($url);
        $res = $this->cat_model->delete($id);

        if ($res) {
            $this->success('删除成功', category_index);
        } else {
            $this->error('删除失败');
        }
    }



    public function upload()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/mallorder/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = $info[0]['savepath'] . $info[0]['savename'];
        return $image;

    }

    //--------*********商品模板信息*****------------

    //显示模板列表
    public function product_index(){
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $count =  $this->model->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres =  $this->model->limit($limit)->select();
            //排序
            $list=$Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示

            //联表查询
            $category_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['category_id']])) {
                    $ids[$v['category_id']] = $v['category_id'];
                }
            }

            //将取出来的id在另外的表根据id查询
            $cats = M('mall_templet_category')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $category_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['category_name'] = $category_info[$v['category_id']]['name'];
            }


            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();

    }
    //添加商品信息
    public function product_add() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $reduction_category = M('mall_templet_category');
        $cate = $reduction_category->select();
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);

        $this->display();
    }

    public function  product_insert()
    {
        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);

        $image = $this->upload();
        $data = array(
            'image' =>$image,
            'name' => I('post.name'),
            'category_id' => I('post.category_id'),
            'active' => I('post.active'),
            'statu' => I('post.statu'),
            'price' => I('post.price'),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time(),
            'ratio1' => I('ratio1'),
            'ratio2' => I('ratio2'),
            'ratio3' => I('ratio3'),
            'mail_fee' => I('post.mail_fee'),
        );

        $res = $this->model->add($data);
        if ($res) {
            $this->success('添加成功', product_index);
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function product_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $id = $_GET['id'];
        $row = $this->model->find($id);
        $listres =  $this->model->select();
        $list=$Team->sortt($listres);

        $category_info = M('mall_templet_category');
        $dis_category = $category_info->select();
        $this->assign('dis_category', $dis_category);
        $this->list = $list;
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function product_update()
    {
        $id = I('post.id');

        if ($_FILES['image']['size'] == 0) {
            $image = I('post.old_image');
        } else {
            $model_info =$this->cat_model->where(array('id'=>$id))->select();
            $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
            @unlink($url);
            $image = $this->upload();
        }

        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);

        $data = array(
            'image' =>$image,
            'name' => I('post.name'),
            'category_id' => I('post.category_id'),
            'active' => I('post.active'),
            'statu' => I('post.statu'),
            'price' => I('post.price'),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time(),
            'ratio1' => I('ratio1'),
            'ratio2' => I('ratio2'),
            'ratio3' => I('ratio3'),
            'mail_fee' => I('post.mail_fee'),
        );

        $res = $this->model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功", product_index);
        }
    }

    //删除
    public function product_delete()
    {
        $id = I('id');

        $model_info =$this->model->where('id='.$id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
        @unlink($url);
        $res = $this->model->delete($id);

        if ($res) {
            $this->success('删除成功', product_index);
        } else {
            $this->error('删除失败');
        }
    }

    //iframe查看订单详情
    public function index_iframe() {
        $distributor = M('distributor');
        $accept_user = [];
        $order = M('mall_order')->find(I('id'));
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
        $info=M('mall_order')->where(array('order_num'=> $order_num))->field('id,order_num,shipper,ordernumber')->find();
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
        $res=M('mall_order')->where($condition)->save($data);
        if($res){
            $this->success('快递单号填写成功');
        }else{
            $this->error('填写失败！原因没做出改变！');
        }
    }


}

?>