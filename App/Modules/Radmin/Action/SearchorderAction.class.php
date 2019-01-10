<?php

/**
 * 	topos代理管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class SearchOrderAction extends Action {

    public function index() {
        //搜索函数

        $get_name = trim(I('get.name'));
//      var_dump($get_name);
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
        $s_phone = trim(I('get.s_phone'));
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
            $end_time = strtotime($end_time) + 86399;
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        if(!empty($get_name)){
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


        if(!empty($get_order_num)){
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

        $condition['s_phone'] = $s_phone;

        $count = $order->where($condition)->count('distinct order_num');
        
        $level_arr = C('LEVEL_NAME');
        $level_arr_flip = array_flip($level_arr);
        
        //测试topos版本的特殊规则，不影响其它项目
        if( $_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2 ){
            $count = 0;
        }
//        $page_num = 20;
        if ($count > 0) {
//            $p = new Page($count, $page_num);
//            $limit = $p->firstRow . "," . $p->listRows;
            //订单信息
            $applyList = $order->where($condition)->order('time desc')->group('order_num')->select();
            
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
            $this->status = $get_status;
//            $page = $p->show();
//            $this->page = $page;


            
            $this->assign('row', $row);
            $this->assign('applyList', $applyList);


        }
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        
//      var_dump($applyList);die;
        $this->con_url = base64_encode($con_url);

        $this->shipper = $shipper;
//        $this->count=$count;
        $this->p=I('p');
//        $this->limit=$page_num;
//      var_dump($applyList);die;
        $this->display();
    }

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


}

?>