<?php

/**
 *  经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class RunAction extends Action {
    
    private $Order;

    public function _initialize() {
        import('Lib.Action.Order','App');
        $this->Order = new Order();
    }

    
    //订单迁移
    public function order_cancel_move(){
        
        $order_obj = M('order');
        $order_cancel_obj = M('order_cancel');
        
        
        $condition = [
            'status'    =>  '-1',
        ];
        $page = '1,100';
        $order_info = $order_obj->where($condition)->order('id asc')->select();
        
        if( !$order_info ){
            echo 'empty order';return;
        }
        
        $add_res = $order_cancel_obj->addAll($order_info);
        
        if( !$add_res ){
            echo 'add error<hr />';
            echo $order_cancel_obj->getDbError().'<hr />';
            return;
        }
        
        $del_res = $order_obj->where($condition)->order('id asc')->delete();
        
        if( !$del_res ){
            echo 'del error';return;
        }
        
        echo 'succ';
    }
    
    
    public function order_confirm_move(){
        
        $order_obj = M('order');
        $order_confirm_obj = M('order_confirm');
        
        
        $condition = [
            'status'    =>  '3',
        ];
        $page = '1,100';
        $order_info = $order_obj->where($condition)->order('id asc')->page($page)->select();
        
        if( !$order_info ){
            echo 'empty order';return;
        }
        
        $add_res = $order_confirm_obj->addAll($order_info);
        
        if( !$add_res ){
            echo 'add error<hr />';
            echo $order_confirm_obj->getDbError().'<hr />';
            return;
        }
        
        $del_res = $order_obj->where($condition)->order('id asc')->page($page)->delete();
        
        if( !$del_res ){
            echo 'del error';return;
        }
        
        echo 'succ';
        
    }
    
    
    
    
    
    //自动确认订单
    public function order_confirm(){
        $confirm_time = 30;
        
        $timeout = time() - $confirm_time*24*60*60;
        
        //echo date('Ymd H:i',$timeout).'<hr />'.$timeout.'<hr />';return;
        
        $excu_hour = [0,1,2,3,4,5,6,7,8,9,14,15,16,17,18,19,20,21,22,23];
        
        if( !in_array(date('H'), $excu_hour) ){
            echo 'error excu time';
            exit;
        }
        
        $condition = [
            'status'    =>  '2',
            'paytime'   =>  ['elt',$timeout],
        ];
        $page = '1,500';
        $order_info = M('order')->where($condition)->field('order_num,paytime')->order('id asc')->page($page)->select();
        
        //echo M('order')->getLastSql();return;
        
//        foreach ( $order_info as $v ){
//            echo $v['order_num'].'-----'.date('Ymd H:i:s',$v['paytime']).'<hr />';
//        }
        
        foreach ( $order_info as $v ){
            $order_num = $v['order_num'];
            $this->Order->confirm_order($order_num);
        }
        
        echo 'succ';
    }
    
    
    
//    public function cancel_confirm_order(){
//        $timeout = 1532484158;
//        
//        $condition = [
//            'status'    =>  '2',
//            'paytime'   =>  ['egt',$timeout],
//        ];
//        $page = '1,500';
//        $order_info = M('order')->where($condition)->field('order_num,paytime')->order('id asc')->page($page)->select();
//        
//        
//        
//    }
    
    
    
    
    
    
    
}

?>