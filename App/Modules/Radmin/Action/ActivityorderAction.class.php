<?php

/**
 * 	topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class ActivityorderAction extends CommonAction {

    private $model;
    public $activity;
    private $type = 0;
    public function __construct() {
        parent::__construct();
        $this->model = M('activity_order');
        $this->activity = C('ACTIVITY');
    }

    public function index() {

        //搜索函数
        $get_name = $get_order_num = $get_status = '';
        if ($this->isGet()) {
            $get_name = trim(I('get.name'));
            $get_order_num = trim(I('get.order_num'));
            $get_status = trim(I('get.status'));
        }
        
        $start_time = I('start_time');
        $end_time = I('end_time');
        $templet = I('templet');

        import('ORG.Util.Page');
        
        //代理系统外活动中心(普通客户)
        if ($this->activity['WAY'] == 0) {
            //尚未开发
            
        } else {
            $distributor = M('distributor');
        }
        
        
        $condition = array(); //SQL搜索条件
        
        $shipper = AllShipperCode();

        
        if (!empty($get_order_num)) {
            $condition['order_num'] = $get_order_num;
        }
        if (!empty($get_status)) {
            $condition['status'] = $get_status;
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        $condition_search['type'] = $this->type;
        if( !empty($templet) ){
            $condition_search['product_id'] = $templet;
            
            $search_order = $this->model->field('order_num')->where($condition_search)->select();
            $all_order_nums = array();
            foreach( $search_order as $v_sear ){
                $v_sear_order_num = $v_sear['order_num'];
                $all_order_nums[] = $v_sear_order_num;
            }
            $condition['order_num'] = ['in',$all_order_nums];
        }
        
        if (!empty($get_name)) {
            //先搜出名字所属ID才能找到订单
            $getIDcondition['name'] = $get_name;
            $lista = $distributor->where($getIDcondition)->find();
            $condition['user_id'] = $lista['id'];
        }
        $condition['type'] = $this->type;
        $count = $this->model->where($condition)->count('distinct order_num');

        if ($count > 0) {
            $p = new Page($count, 50);
            $limit = $p->firstRow . "," . $p->listRows;
            //订单信息
            $applyList = $this->model->where($condition)->order('time desc')->group('order_num')->limit($limit)->select();
            
            $uids = array();
            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
                
                if( !isset($uids[$v_user_id]) ){
                    $uids[$v_user_id] = $v_user_id;
                }
            }
            
            array_values($uids);
            
            $dis_info = $distributor->where(array('id' => array('in',$uids)))->select();
            
            $dis_info_key['0'] = array(
                'name'  =>  '总部'
            );
            
            foreach( $dis_info as $v_dis ){
                $v_dis_id = $v_dis['id'];
                
                $dis_info_key[$v_dis_id]    =   $v_dis;
            }
            
            //订单信息
            $all_order_info = $this->model->field('order_num,product_id,product_name,num,price')->where($condition)->select();
            
            $all_order_key_info = array();
            foreach( $all_order_info as $v_ao ){
                $v_ao_order_num = $v_ao['order_num'];
                
                $all_order_key_info[$v_ao_order_num][] = $v_ao;
            }
            

            foreach ($applyList as $k => $v) {
                $v_user_id = $v['user_id'];
                $v_order_num = $v['order_num'];
                $v_shipper = $v['shipper'];
                
                $applyList[$k]['name'] = $dis_info_key[$v_user_id]['name'];
                $applyList[$k]['phone'] = $dis_info_key[$v_user_id]['phone'];
                $applyList[$k]['levname'] = $dis_info_key[$v_user_id]['levname'];
                $applyList[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'未选择快递公司';
                $the_order_info = isset($all_order_key_info[$v_order_num])?$all_order_key_info[$v_order_num]:array();
                
                $applyList[$k]['row'] = $the_order_info;
            }
            $page = $p->show();
            $this->page = $page;
            $this->assign('applyList', $applyList);
        }
        
        
        $this->temp_info = $this->get_temp_info();
        $this->shipper = $shipper;
        $this->status = $get_status;
        $this->display();
    }
    
    
    //获取产品
    private function get_temp_info(){
        $templet_obj = M('activity_product');
        
        $condition = array(
            'active'    =>  '1',
            'type' => $this->type
        );
        
        $templet_info = $templet_obj->where($condition)->field('id,name')->select();
        $temp_info = array();
        if( !empty($templet_info) ){
            foreach( $templet_info as $v_t ){
                $v_t_id = $v_t['id'];
                $v_t_name = $v_t['name'];
                
                $temp_info[$v_t_id] = $v_t_name;
            }
        }
        
        return $temp_info;
    }//end func get_temp_info
    
    //快递单号填写
    public function write_express() {
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
            'express_num' => $ordernumber
        );
        $save = $this->model->where($condition)->save($update_info);
        
        $this->ajaxReturn($save, 'JSON');
    }
    
    //未发货
    //品牌合伙人订单申请信息列表
    public function apply() {
        import('ORG.Util.Page');
        
        //代理系统外活动中心(普通客户)
        if ($this->activity['WAY'] == 0) {
            //尚未开发
            
        } else {
            $distributor = M('distributor');
        }
        
        $ordernumber = I('ordernumber');
        $get_name = trim(I('name'));
        $order_num = I('order_num');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $templet = I('templet');
        
        $shipper = AllShipperCode();
        
        
        if( $ordernumber != null ){
            if( $ordernumber == 1 ){
                $condition['express_num'] = array('exp','is not null');
            }
            else{
                $condition['express_num'] = array('exp','is null');
            }
        }
        
        if (!empty($order_num)) {
            $condition['order_num'] = $order_num;
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        if( !empty($templet) ){
            $condition_search['product_id'] = $templet;
            
            $search_order = $this->model->field('order_num')->where($condition_search)->select();
            $all_order_nums = array();
            foreach( $search_order as $v_sear ){
                $v_sear_order_num = $v_sear['order_num'];
                $all_order_nums[] = $v_sear_order_num;
            }
            $condition['order_num'] = ['in',$all_order_nums];
        }
        
        if (!empty($get_name)) {
            //先搜出名字所属ID才能找到订单
            $getIDcondition['name'] = $get_name;
            $lista = $distributor->where($getIDcondition)->find();
            $condition['user_id'] = $lista['id'];
        }
        
        $condition['status'] = 2;
        $condition['type'] = $this->type;
        
        //统计未发货订单的信息
        $total_info = $this->model->where($condition)->select();
        $total_info_count = array();
        if( !empty($total_info) ){
            foreach( $total_info as $v_total ){
                $v_total_p_id = $v_total['product_id'];
                $v_total_p_name = $v_total['product_name'];
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
        }
        
        $count = $this->model->where($condition)->count('distinct order_num');
        if ($count > 0) {
            //look 后台审核最高级别经销商的订单
            $p = new Page($count, 50);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $this->model->order('time desc')->where($condition)->group('order_num')->limit($limit)->select();
            
            $serach_uids = array();
            $serach_pids = array();
            foreach( $applyList as $k => $v ){
                $v_uid  =   $v['user_id'];
                
                $serach_uids[] = $v_uid;
            }
            
            array_unique($serach_uids);
            array_unique($serach_pids);
            
            $condition_all_dis = array(
                'id'    =>  array('in',$serach_uids),
            );
            
            $all_dis = $distributor->where($condition_all_dis)->select();
            
            $dis_info = array(
                '0' =>  array(
                    'name'  =>  '总部',
                    'phone' =>  '',
                    'levname'   =>  '总部',
                    'bossname'  =>  '',
                )
            );
            foreach( $all_dis as $v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_info[$v_dis_uid] = $v_dis;
            }
            
            
            
            foreach ($applyList as $k => $v) {
                $v_uid  =   $v['user_id'];
                $v_shipper = $v['shipper'];
                
                $applyList[$k]['name'] = $dis_info[$v_uid]['name'];
                $applyList[$k]['phone'] = $dis_info[$v_uid]['phone'];
                $applyList[$k]['levname'] = $dis_info[$v_uid]['levname'];
                $applyList[$k]['bossname'] = $dis_info[$v_uid]['bossname'];
                $applyList[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'';
                
                $applyList[$k]['o_name'] = $dis_info[$v_oid]['name'];
                
                $row = $this->model->field('product_id,product_name,num,price')->where(array('order_num' => $v['order_num']))->select();
                
                foreach ($row as $b => $d) {
                    
                    $row[$b]['pr_name'] = $d['product_name'];
                    $applyList[$k]['sum'] += $d['num'] * $d['price'];
                }
                
                
                $applyList[$k]['row'] = $row;
            }
            $page = $p->show();
            $this->shipper = $shipper;
            $this->page = $page;
            $this->row = $row;
            $this->applyList = $applyList;
        }
        
        $this->temp_info = $this->get_temp_info();
        $this->total_info_count = $total_info_count;
        $this->display();
    }
    
    //订单申请审核
    public function audit() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        $model = $this->model;
        foreach ($order_nums as $num) {
          $res = $model->where(['order_num' => $num])->save(['status' => 3]); 
        }
        $this->ajaxReturn($res, 'json');
    }
    
    //删除订单
    public function delete() {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        $res = $this->model->where(['order_num' => $_POST['order_num']])->delete();
        $this->ajaxReturn($res, 'json');
    }
}

?>