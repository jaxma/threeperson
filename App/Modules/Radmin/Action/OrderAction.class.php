<?php

/**
 * 	topos代理管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class OrderAction extends CommonAction {
    private $order_model;
    public function _initialize() {
        parent::_initialize();
        $this->order_model = M('order');
    }

    public function index() {

        //搜索函数
        $get_name = trim(I('get.name'));
        $get_s_name = trim(I('get.s_name'));
        $get_order_num = trim(I('get.order_num'));
        $get_status = trim(I('get.status'));
//        $start_time = trim(I('start_time'));
//        $end_time = trim(I('end_time'));
        $s_time=trim(I('s_time'));
        $get_p_id = trim(I('get.p_id'));
        $search_shipper = trim(I('shipper'));
        $ishead = I('ishead');
        $pay_type = I('pay_type');
        
        $shipper = AllShipperCode();
        import('ORG.Util.Page');
        $order = M('Order');
        $distributor = M('distributor');
        $condition = array(); //SQL搜索条件
        
        import('Lib.Action.Order','App');
        $Order = new Order();

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


        if( $ishead != null ){
            if( $ishead ){
                $condition['o_id'] = 0;
            }
            else{
                $condition['o_id'] = ['neq',0];
            }     
        }
        
        if( !empty($pay_type) ){
            $condition['pay_type'] = $pay_type;
        }


        //开始时间-结束时间
        if(!empty($s_time)){
            $time=explode('-',$s_time);
            $start_time=$time[0];
            $end_time=$time[1];
        }

        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            if ($start_time == $end_time) {
                $end_time = strtotime($end_time) + 86399;
            } else {
                $end_time = strtotime($end_time);
            }
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
        if (!empty($get_s_name)) {
            $condition['_complex'] = array(
                's_name' => $get_s_name,
                '_logic' => 'or',
                's_phone' => $get_s_name,
            ); 
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
        
        if( !empty($search_shipper) ){
            $condition['shipper'] = $search_shipper;
        }
        
        $count = $order->where($condition)->count('distinct order_num');
        
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
//          var_dump($dis_info);die;
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
                $applyList[$k]['dis_address'] = $dis_info_key[$v_user_id]['address'];
                $applyList[$k]['name'] = $dis_info_key[$v_user_id]['name'];
                $applyList[$k]['dis_province'] = $dis_info_key[$v_user_id]['province'];
                $applyList[$k]['dis_city'] = $dis_info_key[$v_user_id]['city'];
                $applyList[$k]['dis_county'] = $dis_info_key[$v_user_id]['county'];
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
//            dump($applyList);die;
            
            $page = $p->show();
            $this->page = $page;


            
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);


        }
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        
//      var_dump($applyList);die;
        $this->con_url = base64_encode($con_url);
        $this->status = $get_status;
        $this->status_name = $Order->status_name;
        $this->shipper = $shipper;
        $this->count=$count;
        $this->p=I('p');
        $this->limit=$page_num;
//      var_dump($applyList);die;
        $this->display();
    }
    
    

    //品牌合伙人订单申请信息列表
    public function applyList() {
        import('ORG.Util.Page');
        $order = M('Order');
        $status = $this->_get('status');
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        $get_p_id = trim(I('get.p_id'));
        $name=trim(I('name'));
        $get_order_num=trim(I('order_num'));

        $ishead = I('ishead');
        $pay_type = I('pay_type');

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


        if( $ishead != null ){
            if( $ishead ){
                $condition['o_id'] = 0;
            }
            else{
                $condition['o_id'] = ['neq',0];
            }
        }
        if (!empty($get_order_num)) {
            $condition['order_num'] = $get_order_num;
        }

        if (!empty($status)) {
            $condition['status'] = $status;
        }

        if (!empty($get_p_id)) {
            $condition['p_id'] = $get_p_id;
        }

        if(!empty($name)){
            //先搜出名字所属ID才能找到订单
            $getIDcondition = array(
                'name' => ['like',"%$name%"],
                '_logic' => 'or',
                'wechatnum' => ['like',"%$name%"]
            );
            $lista = M('distributor')->where($getIDcondition)->select();

            $search_uids = [];

            foreach( $lista as $v_ser ){
                $v_ser_id = $v_ser['id'];
                $search_uids[] = $v_ser_id;
            }
            if (!empty($search_uids)) {
                $where['user_id']= ['in',$search_uids];
            } else {
                //没找到数据
                $where['user_id']= -1;
            }
            $condition['_complex'] = $where;
        }
//        $condition = array(
//            'o_id'  =>  0,
//            'status'    =>  $status,
//        );

        $shipper = AllShipperCode();
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        //统计未发货订单的信息
        $total_info = $order->where($condition)->select();
        $total_info_count = array();
        if( !empty($total_info) ){
            foreach( $total_info as $k_total => $v_total ){
                $v_total_p_id = $v_total['p_id'];
                $v_total_p_name = $v_total['p_name'];
                $v_total_price = $v_total['price'];
                $v_total_num = $v_total['num'];

                if( !isset($total_info_count[$v_total_p_id]) ){
                    $the_sum = bcmul($v_total_price,$v_total_num,2);
                    $total_info_count[$v_total_p_id] = array(
                        'p_name'    =>  $v_total_p_name,
                        'sum'       =>  $the_sum,
                        'num'       =>  $v_total_num,
                    );
                }
                else{
                    $the_sum = bcmul($v_total_price,$v_total_num,2);

                    $total_info_count[$v_total_p_id]['sum'] = bcadd($total_info_count[$v_total_p_id]['sum'],$the_sum,2);
                    $total_info_count[$v_total_p_id]['num'] = bcadd($total_info_count[$v_total_p_id]['num'],$v_total_num);
                }
            }
            $this->total_info_count = $total_info_count;
        }

        $count = $order->where($condition)->count('distinct order_num');

        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
        $page_num=20;
        if ($count > 0) {
            //look 后台审核最高级别代理的订单
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

                $row = $order->where(array('order_num' => $v['order_num']))->select();

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
        $this->status=$status;
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

    //订单申请审核
    public function audit() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        
        $order_obj = M('order');
        $templet_obj = M('templet');
        
        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $pass = trim(I('pass'));
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        //审核下单申请
        if($pass == 1){
            $order_audit_result = $Order->radmin_audit($order_nums);
            if( $order_audit_result['code'] == 1 ){
                $this->add_active_log('订单申请审核：'.$order_audit_result['msg']);
            }
        }
        //审核退货申请
        elseif($pass == 4){
            $order_audit_result = $Order->radmin_return_audit($order_nums);
            if( $order_audit_result['code'] == 1 ){
                $this->add_active_log('退货订单申请审核：'.$order_audit_result['msg']);
            }
        }
        elseif($pass == 8){
            $order_num=[];
            $order_info=M('order')->where(['id'=>['in',$order_nums]])->select();
            foreach ($order_info as $k=>$v){
                $order_num[]=$v['order_num'];
            }
            array_unique($order_num);
            $order_audit_result = $Order->grab_order('0',$order_num);
            if( $order_audit_result['code'] == 1 ){
                $this->add_active_log('待抢订单转回总部订单：'.$order_audit_result['msg']);
            }
        }
        //完成发货
        elseif ($pass == 6){
            $order_num=[];
            $order_info=M('order')->where(['id'=>['in',$order_nums]])->select();
            foreach ($order_info as $k=>$v){
                $order_num[]=$v['order_num'];
            }
            array_unique($order_num);
            $order_audit_result = $Order->send_goods($order_num);

        }
        $this->ajaxReturn($order_audit_result, 'json');
    }
    
    
    //订单申请审核为配送中
    public function audit_send() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        vendor("phpqrcode.phpqrcode");
        
        $orderobj = M('order');
        
        $mids = I('mids');
        $mids = substr($mids, 1);
        $managers = explode('_', $mids);

        foreach ($managers as $m) {
            //有电子面单或者配送单代码的情况下
            $order_info=$orderobj->where(['id'=>$m])->find();
            $orderobj->where(array('order_num' => $order_info['order_num']))->save(array('status' => 2));
        }
        $return_result=[
            'code'=>'1',
            'msg' => '处理完成',
        ];
        $this->ajaxReturn($return_result, 'json');
    }
    
    //订单转到抢单中心
    public function audit_grab() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        vendor("phpqrcode.phpqrcode");
        
        $mids = I('mids');
        $pass = trim(I('pass'));
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        $return_result = $Order->change_grab_order($order_nums);
        
        if( !empty($return_result['order_nums']) ){
            $this->add_active_log('订单转到抢单中心：'.$return_result['msg']);
        }
        
        $this->ajaxReturn($return_result, 'json');
    }
    
    
    //删除订单
    public function delorder(){
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        
        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        $order_audit_result = $Order->delorder($order_nums);
        
        if( $order_audit_result['code'] == 1 ){
            $this->add_active_log('订单审核不通过：'.$order_audit_result['msg']);
        }
        
        $this->ajaxReturn($order_audit_result, 'json');
    }//end func delorder
    
    

    //搜索
    public function search() {
        import('ORG.Util.Page');
        $keyword = $_GET['keyword'];
        $order = M('Order');
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
        $order = M('order');
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
        $order = M('order');
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
                //代理线最高代理下单的统计
                $money = 0;
                $xmoney = 0;
                $where['user_id'] = $a[$k]['id'];
                $list = $order->where($where)->group('order_num')->field('total_price')->select();

                foreach ($list as $ke => $va) {
                    $money = $money + $list[$ke]['total_price'];
                }
                $a[$k]['money'] = $money;
                //代理线其他代理下单的统计
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
        $order = M('Order');
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

    //配送中订单
    public function dispatching() {
        import('ORG.Util.Page');
        $order = M('Order');
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

    //已收货订单
    public function receipted() {
        import('ORG.Util.Page');
        $order = M('Order');
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
    
    
    //订单限制
    public function order_limit(){
        $order_limit_obj = M('order_limit');
        import('ORG.Util.Page');
        
        $level_name = C('LEVEL_NAME');
        $level_name['0'] = '所有级别';
        
        
        $condition_order_lim = array();
        
        $count = $order_limit_obj->where($condition_order_lim)->count('id');
        $page_num=20;
        if( $count > 0 ){
            $p = new Page($count, $page_num);
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
        
        
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
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
        
        $is_first = !empty($is_first)?'1':'0';
        
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
//                'is_first'  =>  $is_first,
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
            $this->success('编辑订单限制成功！');
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
            $this->success('删除成功！');
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
        $save = M('order')->where($condition)->save($update_info);
        
        if( $save ){
            $this->add_active_log('快递单号填写，订单号：'.$order_num);
        }
        
        $this->ajaxReturn($save, 'JSON');
    }
    
    
    
    //订单统计
    public function order_count(){
        
//       $month = I('month');
        $name = trim(I('name'));
        $pid = I('pid');
        $start_month=I('start_month');
        $end_month=I('end_month');
        $order_count_obj = M('order_count');
        $distributor_obj = M('distributor');
        $templet_obj = M('templet');
        import('ORG.Util.Page');
        $page = '';
        $type=trim(I('type'));
        if( empty($month) || !is_numeric($month) || strlen($month) != 6 ){
            $month = date('Ym');
        }

        $condition['uid']=array('neq',0);

        if($type == 'month'){
            $condition['day']=0;
        }
        elseif ($type == 'day'){
            $condition['day']= array('neq','0');

        }

        $condition_user = array();
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        $condtion_temp = array();
        
//        if( !empty($month) ){
//            $condition['month'] = $month;
//        }

        if( !empty($name) ){
            $condition_user['_complex']  =   array(
                'name'  =>  $name,
                'wechatnum' =>  $name,
                '_logic'    =>  'or',
            );
        }
        
//      $con_url2 = '';
//          if( !empty($condition_user) ){
//              $con_url2 = serialize($condition_user);
//          }
//          
//          $this->con_url2 = base64_encode($con_url2);
        
        if( !empty($pid) ){
            if($pid == 'a'){
                $condition['pid']=array('neq','0');
            }
            elseif ($pid == 'b'){
                    $condition['pid']=0;
                    $condition['month']=array('neq','0');
            }
            else{
                $condition['pid']   =   $pid;
                $condtion_temp['id']    =   $pid;
                $condition_top['pid']   =   $pid;
            }
        }
        if($type == 'month'){
            if(!empty($start_month)){
                $str_month=explode('-',$start_month);
                $s_month=$str_month[0];
                $en_month=$str_month[1];
            }else{
                $s_month=date('Ym');
                $en_month=date('Ym');
            }
        }
        if($type == 'day'){
            if(!empty($end_month)){
                $e_month=explode('-',$end_month);
                $s_month=$e_month[0];
                $en_month=$e_month[1];
            }else{
                $s_month=date('Ymd');
                $en_month=date('Ymd');
            }
        }


         if( !empty($s_month) && !empty($en_month) ) {
             if ($type == 'month') {
                 $condition['month'] = ['between', [$s_month, $en_month]];
             } elseif ($type == 'day') {
                 $condition['day'] = ['between', [$s_month, $en_month]];
             }
         }

        //------产品信息-----
        //$templet_info = $templet_obj->where($condtion_temp)->field('id,name')->select();
        $templet_info = $templet_obj->field('id,name')->select();
        
        $templet_key_info = array();
        if( !empty($templet_info) ){
            $templet_key_info = array(
                '0' =>  array(
                    'name'  =>  '所有产品统计',
                ),
            );

            foreach( $templet_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $templet_key_info[$v_tem_id] = $v_tem;
            }
        }
        //------end 产品信息-----
        
        
        
        //----总部订单统计信息----
//        $condition_top['uid'] = 0;
//        $top_list = $order_count_obj->where($condition_top)->order('pid asc')->select();
//        
//        
//        if( !empty($top_list) ){
//            foreach( $top_list as $k_top => $v_top ){
//                $v_top_pid = $v_top['pid'];
//                
//                $top_list[$k_top]['p_name'] = $templet_key_info[$v_top_pid]['name'];
//            }
//        }
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

        $page_num=20;
        if ($count > 0) {
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            //订单信息
            $list = $order_count_obj->where($condition)->limit($limit)->select();
            
            
            $all_uid = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                
                $all_uid[]  =   $v_uid;
            }
            
            array_unique($all_uid);
            
            
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
            
            $this->count=$count;
            $page = $p->show();
        }//end if
        
            $con_url = '';
            if( !empty($condition) ){
                $con_url = serialize($condition);
            }
            
            $this->con_url = base64_encode($con_url);
        
        $this->name = $name;
        $this->templet_info = $templet_info;
        $this->top_list = $top_list;
        $this->page =   $page;
        $this->month = $month;
        $this->list =   $list;

        $this->type=$type;
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
        
    }//end func order_count
    
    //总的订单统计
    public function order_count_all(){
        
        $order_count_obj = M('order_count');
        $templet_obj = M('templet');
        
        $pid = trim(I('pid'));
        $start_month=I('start_month');
        $condition_top['uid']   =   0;
        import('ORG.Util.Page');
        $page = '';
        if( !empty($pid) ){
            if($pid == 'b'){
                $condition_top['pid']   =  0;
            }else{
                $condition_top['pid']   =   $pid;
            }

        }else{
            $condition_top['pid']   =  0;
        }
        
        if(!empty($start_month)){
            $str_month=explode('-',$start_month);
            $s_month=$str_month[0];
            $en_month=$str_month[1];
            $condition_top['month'] =   ['between',[$s_month,$en_month]];
        }

        //------产品信息-----
        //$templet_info = $templet_obj->where($condtion_temp)->field('id,name')->select();
        $templet_info = $templet_obj->field('id,name')->select();
        
        $templet_key_info = array();
        if( !empty($templet_info) ){
            $templet_key_info = array(
                '0' =>  array(
                    'name'  =>  '所有产品统计',
                ),
            );
            
            foreach( $templet_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $templet_key_info[$v_tem_id] = $v_tem;
            }
        }
        //------end 产品信息-----

        //----总部订单统计信息----
        $count=$order_count_obj->where($condition_top)->count('id');
        $page_num=20;
        if($count>0){
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $top_list = $order_count_obj->where($condition_top)->order('day asc,month desc')->limit($limit)->select();
                foreach( $top_list as $k_top => $v_top ){
                    $v_top_pid = $v_top['pid'];

                    $top_list[$k_top]['p_name'] = $templet_key_info[$v_top_pid]['name'];
            }
        }
        
        $con_url = '';
        if( !empty($condition_top) ){
            $con_url = serialize($condition_top);
        }
            
        $this->con_url = base64_encode($con_url);

        $this->templet_info = $templet_info;
        $this->top_list = $top_list;
        $this->count=$count;
        $this->p=I('p');
        $this->limit=$page_num;
        $this->pid = $pid;
        $this->display();
    }//end func order_count_all
    
    
    
    //生成无订单数据的用户订单统计（用于展现）
    public function create_order_count(){
        
        $month = I('month');
        
        $condition_user = array();
        
        $order_count = M('order_count');
        
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



    //查看订单详情
    public function detail() {
        $status=I('get.status');
        $distributor = M('distributor');
        $accept_user = [];
        $order = M('order')->find(I('id'));
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

    //填写快递页面
    public function index_kd(){
        $order_num=trim(I('order_num'));
        $info=M('order')->where(array('order_num'=> $order_num))->field('id,order_num,shipper,ordernumber')->find();
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
        if( $shipper == ''){
            $this->error('快递信息填写不完整!');
        }
        $condition = array(
            'order_num' => $order_num,
        );
        $data=[
            'shipper'=>$shipper,
            'ordernumber'=>$ordernumber,
        ];
       $res=M('order')->where($condition)->save($data);
        if($res){
            $this->add_active_log('快递单号填写，订单号：'.$order_num);
            $this->success('快递单号填写成功');
        }else{
            $this->error('填写失败！原因没做出改变！');
        }
    }
    
    
    //总部为代理下单
    public function set_order(){
        
        $uid = I('get.uid');
        
        $distributor_obj = M('distributor');
        $templet_opj = M('templet');
        $receiving_obj = M('receiving');
        $money_funds_obj = M('money_funds');
        
        
        //用户信息
        $where_dis = array(
            'id'    =>  $uid,
        );
        
        $dis_info = $distributor_obj->where($where_dis)->find();
        
        if( empty($dis_info) ){
            $this->error('没有找到该用户！');return;
        }
        
        
        //产品信息
        $price = "price" . $dis_info['level'];
        $condition_templet['active'] = '1';
        $templet_info = $templet_opj->where($condition_templet)->field('id,name,image,disc,state,' . $price)->select();
        
        $a = 1;
        $templet_count  =   0;
        foreach ($templet_info as $k => $v) {
            $templet_info[$k]['price'] = $v[$price];
            $templet_info[$k]['id_num'] = $a;
            $a++;
            $templet_count++;
        }
        
//        print_r($templet_info);return;
        
        
        //收货信息
        $where_rec = array(
            'user_id'   =>  $uid,
        );
        $receiving_info = $receiving_obj->where($where_rec)->find();
        
        //查看该代理的资金表
        $money_funds = $money_funds_obj->where(array('uid'=>$uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        
        
        $this->recharge_money = $recharge_money;//资金金额
        $this->dis_info = $dis_info;
        $this->templet_info =   $templet_info;
        $this->templet_count    =   $templet_count;
        $this->receiving_info   =   $receiving_info;
        
        $this->display();
    }//end func set_order
    
    
    
    //提交订单
    public function set_order_submit(){
        $order_num = trim(I('post.order_num'));
        $p_ids = I('post.p_ids');
        $p_nums = I('post.p_nums');
        $s_name = trim(I('post.user_name'));
        $s_addre = trim(I('post.addre'));
        $s_phone = trim(I('post.phone'));
        $notes = trim(I('post.textarea'));
        
        $uid = trim(I('post.uid'));
        
//        print_r($this->_post());return;
        
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        $write_info = array(
            'order_num' =>  $order_num,
            'p_ids' =>  $p_ids,
            'p_nums'    =>  $p_nums,
            'user_name' =>  $s_name,
            'addre' =>  $s_addre,
            'phone' =>  $s_phone,
            'textarea'  =>  $notes,
            'sender_id' =>  '0',//总部替用户下单都是默认为该用户
        );
        
        $return_result = $Order->write_order($uid,$write_info);
        
        $this->ajaxReturn($return_result, 'json');
    }

    //总部订单汇总
    public function order_count_sum(){
        $order_count_obj = M('order_count');
        $templet_obj = M('templet');
        import('ORG.Util.Page');
        $templet_info = $templet_obj->field('id,name')->select();

        $templet_key_info = array();
        if( !empty($templet_info) ){


            foreach( $templet_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];

                $templet_key_info[$v_tem_id] = $v_tem;

            }
        }

        //总部订单汇总
        $boss_condition=[
            'uid'=>'0',
            'month'=>['gt',0],
            'day'=>'0',
            'pid'=>['gt',0],
        ];

        $count_info=$order_count_obj->where($boss_condition)->order('cost_money desc')->select();
        //取出pid
        $count=0;
        foreach ($count_info as $kk => $vv){
            $pid[]=$vv['pid'];
        }
        $pid=array_unique($pid);
        $count=count($pid);
        $page_num=20;

        if($count>0){
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $item=$order_count_obj->where($boss_condition)->order('cost_money desc')->select();
            $list=array();

            foreach ($item as $key => $value) {
                if (isset($list[$value['pid']])) {
                    $list[$value['pid']]['cost_money'] += $value['cost_money'];
                } else {
                    $list[$value['pid']] = $value;
                }
            }

            foreach( $list as $k => $v ){
                $v_pid = $v['pid'];
                $list[$k]['p_name'] = $templet_key_info[$v_pid]['name'];
            }

            $p=I('p');
            if($p == 1 || empty($p)){
                $start=0;
                $end=$page_num;
            }else{
                $start=($p-1)*$page_num;
                $end = $p*$page_num;
            }
            $list=array_merge($list);
            $row=array_slice($list,$start,$end);
        }

        $this->count=$count;
        $this->list=$row;

        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
    }

    //自动收货
    public function order_auto(){
        import('Lib.Action.Order','App');
        $Order = new Order();
        if(C('ORDER_IS_AUTO')){
            $res=$Order->auto();
        }
        $this->ajaxReturn($res, 'json');
    }

    //拒绝退货
    public function refuse_order(){
        $order_obj = M('order');
        $templet_obj = M('templet');

        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $pass = trim(I('pass'));
        $mids = substr($mids, 1);
        $order_num = explode('_', $mids);

        import('Lib.Action.Order','App');
        $Order = new Order();
        //审核退货申请
        $order_nums=['in',$order_num];
        if($pass == 4){
            $order_audit_result = $Order->refuse_order($order_nums);
            if( $order_audit_result['code'] == 1 ){
                $this->add_active_log('退货订单申请审核：'.$order_audit_result['msg']);
            }
        }
        $this->ajaxReturn($order_audit_result, 'json');
    }
    
    public function send_order(){
        $order_id = I('order_id');
        $order = M('order');
        if(empty($order_id)){
            return;
        }
        $order_id = explode(',',$order_id);
        
        if(is_array($order_id)){
            $list = $order->where(['id'=>array('in',$order_id)])->select();
            foreach($list as $k => $v){
                $temp[] = $list[$k]['order_num'];
            }
            $condition=[
                'order_num'=> array('in',$temp)
            ];
        }
        
        $order = M('Order');
        $shipper = AllShipperCode();
        
            //look 后台审核最高级别代理的订单
            $applyList = $order->order('time desc')->where($condition)->group('order_num')->select();
            
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

                $row = $order->where(array('order_num' => $v['order_num']))->select();

                foreach ($row as $b => $d) {
                    $rol = M('Templet')->field('name')->where(array('id' => $d['p_id']))->find();
                    $row[$b]['pr_name'] = $rol['name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }

                
                $applyList[$k]['row'] = $row;
            }
           
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);
        
        
        $this->shipper = $shipper;
        $this->display();
        
        
        
//      $this->list = $list;
//      $this->display();
    }
    
    //总部订单统计
    public function order_count_head(){
        
        $order_obj = M('order');
        $templet_obj = M('templet');
        
        $pid = trim(I('pid'));
        $status = trim(I('status'));
        $start_month=I('start_month');
        $condition_top['o_id']   =   0;
        import('ORG.Util.Page');
        if( !empty($pid) ){
            if ($pid == 'b') {
                $condition_top['p_id']   =  ['gt',0];
            } else {
                $condition_top['p_id']   =   $pid;
            }
        }else{
            $condition_top['p_id']   =  ['gt',0];
        }
        
        if($status !== ""){
            $condition_top['status']   =   $status;
        }else{
            $condition_top['status']   =  ['egt',0];
        }
        
        if(!empty($start_month)){
            $str_month=explode(' - ',$start_month);
            $b_m = substr($str_month[0],0,4);
            $b_d = substr($str_month[0],4);
            $en_m = substr($str_month[1],0,4);
            $en_d = substr($str_month[1],4);
            $s_month=$b_m.'-'.$b_d;
            
            $en_month=$en_m.'-'.$en_d;
            $s_time = strtotime($s_month);//月初时间戳
            
            $en_time = mktime(23, 59, 59, date('m', strtotime($en_month))+1, 00);//月尾时间戳
            $condition_top['time'] =   ['between',[$s_time,$en_time]];
        }
//        var_dump($condition_top);die;

        //------产品信息-----
        //$templet_info = $templet_obj->where($condtion_temp)->field('id,name')->select();
        $templet_info = $templet_obj->field('id,name')->select();
        
        $templet_key_info = array();
        if( !empty($templet_info) ){
            $templet_key_info = array(
                '0' =>  array(
                    'name'  =>  '所有产品统计',
                ),
            );
            
            foreach( $templet_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $templet_key_info[$v_tem_id] = $v_tem;
            }
        }
        $temp = [];
        $top_list = [];
        $list = $order_obj->where($condition_top)->select();
        foreach ($list as $k => $v) {
            $p_id = $v['p_id'];
            $temp[$p_id][] = $v;
        }
        foreach ($temp as $key => $value) {
            $total_num = 0;
            $total_price = 0;
            foreach ($value as $k=>$v) {
                $total_num += $v['total_num'];
                $total_price += $v['total_price'];
            }
            $top_list[$key]['total_num'] = $total_num;
            $top_list[$key]['total_price'] = $total_price;
            $top_list[$key]['p_name'] = $templet_key_info[$key]['name'];
        }
//        echo '<pre>';var_dump($top_list);die;
        
        //------end 产品信息-----

        //----总部订单统计信息----
            

        $this->templet_info = $templet_info;
        $this->top_list = $top_list;
        $this->pid = $pid;
        $this->status = $status;
        $this->month = $start_month;
        $this->display();
    }//end func order_count_all
}

?>