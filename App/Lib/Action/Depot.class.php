<?php
//订单管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Depot {
    
    public $depot_open = FALSE;//
    
    public $depot_model;
    
    /**
     * 架构函数
     */
    public function __construct() {
        $this->depot_model = M('depot');
        
    }
    
    
    
    //=====================start  获取信息=======================================
    
    //获取订单记录
    public function get_depot($page_info=array(),$condition=array()){
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        
        $count = $this->depot_model->where($condition)->count();

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list = $this->depot_model->where($condition)->order('time desc')->page($page_con)->select();
            }
            else{
                $list = $this->depot_model->where($condition)->order('time desc')->select();
            }
        }
        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );
        
        return $return_result;
    }//end func get_order
    
    
    //=====================end  获取信息=======================================
    
    
    
    
    
    
}//end Class