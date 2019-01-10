<?php
//订单管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Order {
    
    public $status_name = array(
        1   =>  '待审核',
        2   =>  '已发货',
        3   =>  '已收货',
//        4   =>  '申请退货',
        6   =>  '待发货',
//        7   =>  '拒绝退货',
//        8   =>  '待抢单',
        -1  => '已取消',
    );
    
    public $all_pay_type = array(
        0   =>  '默认',//不需要支付截图
//        1   =>  '虚拟币支付',
//        2   =>  '支付截图',
    );
    
    public $is_generate_order_count = TRUE;//是否生成订单统计表
    
    public $is_top_supply = FALSE;//是否总部供货
    
    public $is_top_supply_level = [
//        1   =>  1,
//        2   =>  1,
    ];//如果有值则根据级别判断是否根据，书写规则：级别 => 1 or 0（1代表总部供货，0代表上级供货）

    public $opent_order_limit = TRUE;//是否启用下单限制
    
    public $is_inventory = FALSE;//是否计入总部库存记录
    
    public $auto_order = TRUE;// 是否开启自动下单功能
    
    public $pay_photo_open = TRUE;//下单页面购买开启支付截图
    
    private $order_obj;
    private $order_count_obj;
    
    private $templet_obj;
    private $distributor_obj;
    private $cat_model;
    
    private $FUNCTION_MODULE_STOCK_ORDER;//是否开启了云仓下单的方法
    
    /**
     * 架构函数
     */
    public function __construct() {
        $this->order_obj = M('order');
        $this->distributor_obj = M('distributor');
        $this->order_count_obj = M('order_count');
        $this->templet_obj = M('templet');
        $this->cat_model = M('templet_category');
        
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $this->FUNCTION_MODULE_STOCK_ORDER = $FUNCTION_MODULE['STOCK_ORDER'];
        
        //云仓下单功能，提货时必须是总部供货
        if($this->FUNCTION_MODULE_STOCK_ORDER){
            $this->is_top_supply = TRUE;
        }
        
        $extra = C('extra');
        
        if( isset($extra['order']) ){
            $extra_info = $extra['order'];
            
            if( isset($extra_info['is_generate_order_count']) ){
                $this->is_generate_order_count = $extra_info['is_generate_order_count'];
            }
            if( isset($extra_info['is_top_supply']) ){
                $this->is_top_supply = $extra_info['is_top_supply'];
            }
            if( isset($extra_info['is_top_supply_level']) ){
                $this->is_top_supply_level = $extra_info['is_top_supply_level'];
            }
            if( isset($extra_info['opent_order_limit']) ){
                $this->opent_order_limit = $extra_info['opent_order_limit'];
            }
        }
        
    }
    
    
    
    //=====================start  获取信息=======================================
    
    //获取订单记录
    public function get_order($page_info=array(),$condition=array(),$other=array()){
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        
        $stock_type = !isset($other['stock'])||empty($other['stock'])?0:$other['stock'];
        $tb_order = $this->order_obj;
        
        $level_num = C('LEVEL_NAME');
        $status_name = $this->status_name;
        $all_pay_type = $this->all_pay_type;
        $shipper = AllShipperCode();
        
        $is_group = isset($other['is_group'])?$other['is_group']:0;
        $is_open_shop = isset($other['shop_open'])?$other['shop_open']:false;
        
        if($stock_type == 1){
            $tb_order = M('stock_order');
        }
        
        if($is_open_shop){
            $tb_order = M('shop_order');
        }
        
        $stock_type = !isset($other['stock'])||empty($other['stock'])?0:$other['stock'];
        
        $tb_order = $this->order_obj;
        
        if($stock_type == 1){
            $tb_order = M('stock_order');
        }
        
        $count = $tb_order->where($condition)->count('distinct order_num');

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
//              if($is_open_shop){
//                  $list = $this->order_obj->join('LEFT JOIN shop_order ON order.user_id = shop_order.o_id ')->where($condition)->order('time desc')->page($page_con)->select();
//              }else{
                    $list = $tb_order->where($condition)->order('time desc')->page($page_con)->select();
                    
//              }   

            }
            else{
//              if($is_open_shop){
//                  $list = $this->order_obj->join('LEFT JOIN shop_order ON order.user_id = shop_order.o_id ')->where($condition)->order('time desc')->select();
//              }else{
                    $list = $tb_order->where($condition)->order('time desc')->select();
//              }
            }
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = [];
            
            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }
                
            }
            
            array_unique($uids);
            array_values($uids);
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            
            $field = 'id,name,levname,headimgurl';
            
            $dis_info = $this->distributor_obj->field($field)->where($condition_dis)->select();

            
            $dis_key_info = [
                0   =>  [
                    'name'  =>  '总部',
                    'levname'   =>  '总部',
                ],
            ];
            
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;

            }

            
            array_values($pids);
            array_unique($pids);
            $condition_temp = [
                'id'    =>  ['in',$pids],
            ];

            $templet_info = $this->templet_obj->where($condition_temp)->select();

            $templet_key_info = [];
            foreach( $templet_info as $v_temp ){
                $v_temp_id = $v_temp['id'];
                $templet_key_info[$v_temp_id] = $v_temp;

            }
            
            $list_group = [];
            
            // 是店中店的话
            if($is_open_shop){
                $shop_templet = M('shop_templet');
                $bind_list = $shop_templet->select();
                $bind_list_key = [];
                foreach($bind_list as $k =>$v){
                    $bind_list_key[$v['id']] = $v['bind_pid'];
                }
                foreach( $list as $k => $v ){
                    $v_uid = $v['user_id'];
                    $v_oid = $v['o_id'];
                    $v_pid = $v['p_id'];
                    $v_order_num = $v['order_num'];
                    $v_u_level = $v['u_level'];
                    $v_p_level = $v['p_level'];
                    $v_updated = $v['updated'];
                    $v_status = $v['status'];
                    $v_pay_type = ['pay_type'];
                    $v_shipper = $v['shipper'];
    
    //                $list[$k]['u_info'] = $dis_key_info[$v_uid];
    //                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                    
                    //下单人信息
                    $list[$k]['name'] = $dis_key_info[$v_uid]['name'];
                    $list[$k]['phone'] = $dis_key_info[$v_uid]['phone'];
                    $list[$k]['levname'] = $dis_key_info[$v_uid]['levname'];
                    $list[$k]['bossname'] = $dis_key_info[$v_uid]['bossname'];
                    $list[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'';
                    $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
                    $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];
                    $list[$k]['headimgurl'] = $dis_key_info[$v_uid]['headimgurl'];
                    
                    //供货人信息
                    $list[$k]['o_name'] = $dis_key_info[$v_oid]['name'];
                    $list[$k]['o_levname'] = $dis_key_info[$v_oid]['levname'];
                    
                    
                    $list[$k]['templet'] = $templet_key_info[$v_pid];
                    $list[$k]['status_name'] = $status_name[$v_status];
                    $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);
                    $list[$k]['pay_type_name'] = $all_pay_type[$v_pay_type];
                    $list[$k]['bind_pid'] =  $bind_list_key[$v['p_id']];
                    
                    $list_group[$v_order_num][] = $list[$k];
            }
            //-----end 整理添加相应其它表的信息-----
            }else{
                foreach( $list as $k => $v ){
                    $v_uid = $v['user_id'];
                    $v_oid = $v['o_id'];
                    $v_pid = $v['p_id'];
                    $v_order_num = $v['order_num'];
                    $v_u_level = $v['u_level'];
                    $v_p_level = $v['p_level'];
                    $v_updated = $v['updated'];
                    $v_status = $v['status'];
                    $v_pay_type = ['pay_type'];
                    $v_shipper = $v['shipper'];
    
    //                $list[$k]['u_info'] = $dis_key_info[$v_uid];
    //                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                    
                    //下单人信息
                    $list[$k]['name'] = $dis_key_info[$v_uid]['name'];
                    $list[$k]['phone'] = $dis_key_info[$v_uid]['phone'];
                    $list[$k]['levname'] = $dis_key_info[$v_uid]['levname'];
                    $list[$k]['bossname'] = $dis_key_info[$v_uid]['bossname'];
                    $list[$k]['shipper_name'] = isset($shipper[$v_shipper])?$shipper[$v_shipper]:'';
                    $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
                    $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];
                    $list[$k]['headimgurl'] = $dis_key_info[$v_uid]['headimgurl'];
                    
                    //供货人信息
                    $list[$k]['o_name'] = $dis_key_info[$v_oid]['name'];
                    $list[$k]['o_levname'] = $dis_key_info[$v_oid]['levname'];
                    
                    
                    $list[$k]['templet'] = $templet_key_info[$v_pid];
                    $list[$k]['status_name'] = $status_name[$v_status];
                    $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);
                    $list[$k]['pay_type_name'] = $all_pay_type[$v_pay_type];
                    
                    $list_group[$v_order_num][] = $list[$k];
                }
                //-----end 整理添加相应其它表的信息-----
            }
            
            
            
            
            if( $is_group ){
                $list = $list_group;
            }
        }
        
        
        
        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        
        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );
        
        return $return_result;
    }//end func get_order
    
    
    //获取订单统计记录
    public function get_order_count($page_info=array(),$condition=array(),$order_by='created desc'){
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];

        
        $level_num = C('LEVEL_NAME');

        $count = $this->order_count_obj->where($condition)->count('id');

//        return $this->order_count_obj->getLastSql();

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $this->order_count_obj->where($condition)->order($order_by)->page($page_con)->limit(6)->select();
            }
            else{
                $list = $this->order_count_obj->where($condition)->order($order_by)->limit(6)->select();
            }
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = [];
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }
            }
            
            array_values($uids);
            array_unique($uids);
            
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );

            $field = 'id,name,levname';
            
            $dis_info = $this->distributor_obj->field($field)->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            $dis_key_info['0']['levname'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }

            array_values($pids);
            array_unique($pids);
            $condition_temp = [
                'id'    =>  ['in',$pids],
            ];
            $templet_info = $this->templet_obj->where($condition_temp)->select();
            
            $templet_key_info = [];
            foreach( $templet_info as $v_temp ){
                $v_temp_id = $v_temp['id'];
                $templet_key_info[$v_temp_id] = $v_temp;

            }

            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_updated = $v['updated'];
                
//                $list[$k]['u_info'] = $dis_key_info[$v_uid];
//                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                
                $list[$k]['templet'] = $templet_key_info[$v_pid];
                $list[$k]['dis_info']= $dis_key_info[$v_uid];

                $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);
            }
            //-----end 整理添加相应其它表的信息-----
        }
        
        
        
        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        
        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
            'count' =>  $count,
        );
        
        return $return_result;
    }//end func get_order_count
    
    
    //获取团队的订单统计
    public function get_team_order_count($uid,$page_info=array(),$condition=array(),$order_by='created desc'){
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $user_team = $User->get_user_team($uid,[],TRUE);
        
        if( $user_team_result['code'] != 1 ){
            return $user_team_result;
        }
        $team_info = $user_team_result['team_info'];
        $team_num = $user_team_result['team_num'];
        $team_uids = $user_team_result['team_uids'];
        $team_dis_info = $user_team_result['team_dis_info'];
        $team_level_num = $user_team_result['team_level_num'];
        
        $condition['uid']   =   ['in',$team_uids];
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];

        
        $level_num = C('LEVEL_NAME');
        
        $count = $this->order_count_obj->where($condition)->count('id');

//        return $this->order_count_obj->getLastSql();

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $this->order_count_obj->where($condition)->order($order_by)->page($page_con)->limit(6)->select();
            }
            else{
                $list = $this->order_count_obj->where($condition)->order($order_by)->limit(6)->select();
            }
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = [];
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }
            }
            
//            array_values($uids);
//            array_unique($uids);
//            
//            $condition_dis = array(
//                'id'    =>  array('in',$uids),
//            );
//
//            $field = 'id,name,levname';
//            
//            $dis_info = $this->distributor_obj->field($field)->where($condition_dis)->select();
//
//            $dis_key_info[0]['name'] = '总部';
//            $dis_key_info['0']['levname'] = '总部';
//            foreach( $dis_info as $k_dis=>$v_dis ){
//                $v_dis_uid = $v_dis['id'];
//                
//                $dis_key_info[$v_dis_uid] = $v_dis;
//            }

            array_values($pids);
            array_unique($pids);
            $condition_temp = [
                'id'    =>  ['in',$pids],
            ];
            $templet_info = $this->templet_obj->where($condition_temp)->select();
            
            $templet_key_info = [];
            foreach( $templet_info as $v_temp ){
                $v_temp_id = $v_temp['id'];
                $templet_key_info[$v_temp_id] = $v_temp;

            }
            
            $new_list = [];
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_month = $v['month'];
                $v_day = $v['day'];
                $v_buy_money = $v['buy_money'];
                $v_buy_num = $v['buy_num'];
                $v_cost_monty = $v['cost_monty'];
                $v_cost_num = $v['cost_num'];
                
                
                $list[$k]['templet'] = $templet_key_info[$v_pid];
                
                $new_key = $v_pid.'-'.$v_month.'-'.$v_day;
                
                if( !isset($new_list[$new_key]) ){
                    $new_list[$new_key] = [
                        'pid'   =>  $v_pid,
                        'month' =>  $v_month,
                        'day'   =>  $v_day,
                        'buy_money' =>  $v_buy_money,
                        'buy_num'   =>  $v_buy_num,
                        'cost_monty'    =>  $v_cost_monty,
                        'cost_num'  =>  $v_cost_num,
                        'templet'   =>  $templet_key_info[$v_pid],
                    ];
                }
                else{
                    $new_list[$new_key]['buy_money'] = bcadd($new_list[$new_key]['buy_money'], $v_buy_money ,2);
                    $new_list[$new_key]['buy_num'] = bcadd($new_list[$new_key]['buy_num'], $v_buy_num);
                    $new_list[$new_key]['cost_monty'] = bcadd($new_list[$new_key]['cost_monty'], $v_cost_monty ,2);
                    $new_list[$new_key]['cost_num'] = bcadd($new_list[$new_key]['cost_num'], $v_cost_num);
                }
            }
            //-----end 整理添加相应其它表的信息-----
        }
        
        
        
        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        
        
        $return_result = array(
            'list'  =>  $new_list,
            'page'  =>  $page,
            'count' =>  $count,
        );
        
        return $return_result;
    }//end func get_team_order_count






    //获取产品信息
    public function get_templet($page_info=array(),$condition=array(),$sort_info='id desc',$price){

        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        
        
        
        $count = $this->templet_obj->where($condition)->count();

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;

                $list = $this->templet_obj->where($condition)->order($sort_info)->page($page_con)->select();

            }
            else{
                $list = $this->templet_obj->where($condition)->order($sort_info)->select();
            }
            foreach ($list as $key => $product) {
                $list[$key]['price'] = $product[$price];
            }
            
        }


        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        
        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );
        
        return $return_result;
    }//end func get_templet
    
    
    //获取产品分类
    public function get_templet_category($page_info=array(),$condition=array()){
        
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        
        
        
        $count = $this->cat_model->where($condition)->count();

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $this->cat_model->where($condition)->order('id desc')->page($page_con)->select();

            }
            else{
                $list = $this->cat_model->where($condition)->order('id desc')->select();
            }
            
        }
        
        
        
        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        
        
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );
        
        return $return_result;
    }//end func get_templet_category
    

    //扫码模块获取订单记录2
    public function get_sao_order($page_info=array(),$condition=array()){


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $level_num = C('LEVEL_NAME');
        $status_name = $this->status_name;
        $count = $this->order_obj->where($condition)->count('distinct order_num');

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;
                $fildes='order_num';
                $list = $this->order_obj->where($condition)->order('time desc')->group($fildes)->page($page_con)->select();

            }
            else{
                $fildes='order_num';
                $list = $this->order_obj->where($condition)->order('time desc')->group($fildes)->select();
            }
            $fiedd='order_num,p_id,p_name,num,price,p_image';
            $all_order_info = $this->order_obj->field($fiedd)->where($condition)->select();
            $all_order_key_info = array();
            foreach ($all_order_info as $k_ao => $v_ao) {
                $v_ao_order_num = $v_ao['order_num'];
                $all_order_key_info[$v_ao_order_num][] = $v_ao;

            }

            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = [];

            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }

            }

            array_values($uids);
            array_unique($uids);
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );

            $field = 'id,name,levname,wechatnum,headimgurl,authnum';

            $dis_info = $this->distributor_obj->field($field)->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            $dis_key_info['0']['levname'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;

            }

            array_values($pids);
            array_unique($pids);
            $condition_temp = [
                'id'    =>  ['in',$pids],
            ];

            $templet_info = $this->templet_obj->where($condition_temp)->select();

            $templet_key_info = [];
            foreach( $templet_info as $v_temp ){
                $v_temp_id = $v_temp['id'];
                $templet_key_info[$v_temp_id] = $v_temp;

            }


            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];

                $v_u_level = $v['u_level'];
                $v_p_level = $v['p_level'];
                $v_updated = $v['updated'];
                $v_status = $v['status'];

                $v_order_num = $v['order_num'];
                $v_ordernumber = $v['ordernumber'];
                $v_ordernumber_arr = !empty($v_ordernumber) ? explode(',', $v_ordernumber) : [];
                $list[$k]['ordernumber_arr'] = $v_ordernumber_arr;
                $the_order_info = isset($all_order_key_info[$v_order_num]) ? $all_order_key_info[$v_order_num] : array();
                $list[$k]['row'] = $the_order_info;

//                $list[$k]['u_info'] = $dis_key_info[$v_uid];
//                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['p_name'] = $dis_key_info[$v_pid]['name'];

                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];

                $list[$k]['status_name'] = $status_name[$v_status];
                $list[$k]['p_levname'] = $dis_key_info[$v_pid]['levname'];
                $list[$k]['templet'] = $templet_key_info[$v_pid];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);
            }
            //-----end 整理添加相应其它表的信息-----
        }



        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }


        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_order
    
    //=====================end  获取信息=======================================
    
    
    //---------------根据订单信息生成的统计-------------------
    
    
    
    
    /**
     * 生成订单统计         
     * 2017-4-17 经销商出货数据暂不进行统计
     * @param type $order_info  //多维数组
     * @param type $month
     * @return string|int
     */
    public function generate_order_count($order_info,$time=''){
        
        if( !$this->is_generate_order_count ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不生成统计！',
            );
            return $return_result;
        }
        
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
//        $order_obj = M('order');
//        $order_count_obj = M('order_count');
        
        
        $uid = $order_info[0]['user_id'];  //下单经销商ID
        $o_id = $order_info[0]['o_id'];    //接单经销商ID
        $p_id = $order_info[0]['p_id'];    //产品ID
        $status = $order_info[0]['status'];    //订单状态
        $total_price = $order_info[0]['total_price'];  //总价格
        $total_num = $order_info[0]['total_num'];      //总数量
        $error_info = array();//错误信息
        
        
        if( empty($time) ){
            $time = date('Ymd');
        }
        
        $time_len = strlen($time);
        
        if( !is_numeric($time) || $time == null || !in_array($time_len, [8]) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '时间不符合格式！',
            );
            return $return_result;
        }
        
        $month = substr($time,0,6);
        
        
        
        //------------针对产品的销量统计-----------
        
        
        
        foreach( $order_info as $k_o => $v_o ){
            $v_o_p_id = $v_o['p_id'];
            $v_o_price = $v_o['price'];
            $v_o_num = $v_o['num'];
            
            $v_o_p_total = bcmul($v_o_price,$v_o_num,2);
            
            $the_update_buy_info = array(
                'p_id'       =>  $v_o_p_id,
                'buy_money' =>  $v_o_p_total,
                'buy_num'   =>  $v_o_num,
            );
            
            $the_res_buy = $this->update_order_count($uid,$month,$the_update_buy_info);
            
            if( $the_res_buy['code'] != 1 ){
                $the_res_buy['codespe'] = '$the_res_buy';
                $error_info[] = $the_res_buy;
            }
            
            if( $time != $month ){
                $the_res_buy2 = $this->update_order_count($uid,$time,$the_update_buy_info);
            
                if( $the_res_buy2['code'] != 1 ){
                    $the_res_buy2['codespe'] = '$the_res_buy2';
                    $error_info[] = $the_res_buy2;
                }
            }
            
            
            
            //总部只有出货信息
            $the_all_update_buy_info = array(
                'p_id'       =>  $v_o_p_id,
                'cost_money' =>  $v_o_p_total,
                'cost_num'   =>  $v_o_num,
            );
           //原
           // $the_all_res_buy = $this->update_order_count(0,0,$the_all_update_buy_info);
            //修改后，用于总部订单统计
            $the_all_res_buy = $this->update_order_count(0,$month,$the_all_update_buy_info);
            
            if( $the_all_res_buy['code'] != 1 ){
                $the_all_res_buy['codespe'] = '$the_all_res_buy';
                $error_info[] = $the_all_res_buy;
            }
        }
        
        //------------end 针对产品的销量统计-----------
        
        
        //-------------更新出货商总的出货信息---------------
        
        //总部只有出货信息
        $all_update_buy_info = array(
            'p_id'       =>  0,
            'cost_money' =>  $total_price,
            'cost_num'   =>  $total_num,
        );
        //原
//        $all_res_buy = $this->update_order_count(0,0,$all_update_buy_info);
        //修改后，用于总部订单统计
        $all_res_buy = $this->update_order_count(0,$month,$all_update_buy_info);
        
        if( $all_res_buy['code'] != 1 ){
            $all_res_buy['codespe'] = '$all_res_buy';
            $error_info[] = $all_res_buy;
        }
        
        
        if( $time != $month ){
            $all_res_buy2 = $this->update_order_count(0,$time,$all_update_buy_info);
            
            if( $all_res_buy2['code'] != 1 ){
                $all_res_buy2['codespe'] = '$all_res_buy2';
                $error_info[] = $all_res_buy2;
            }
        }
        
        
        
        
        //-------------end 更新出货商总的出货信息---------------
        
        //-------------更新进货商总的进货信息---------------
        
        
        $update_buy_info = array(
            'p_id'       =>  0,
            'buy_money' =>  $total_price,
            'buy_num'   =>  $total_num,
        );
        
        $res_buy = $this->update_order_count($uid,$month,$update_buy_info);
        
        if( $res_buy['code'] != 1 ){
            $res_buy['codespe'] = '$res_buy';
            $error_info[] = $res_buy;
        }
        
        if( $time != $month ){
            $res_buy2 = $this->update_order_count($uid,$time,$update_buy_info);
        
            if( $res_buy['code'] != 1 ){
                $res_buy2['codespe'] = '$res_buy2';
                $error_info[] = $res_buy2;
            }
        }
        
        
        //-------------end 更新进货商总的进货信息---------------
        
        
        if( !empty($error_info) ){
            setLog(var_export($error_info,1),'generate_order_count_error');
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '统计出现错误信息！',
                'info'  =>  $error_info,
            );
            return $return_result;
        }
        
        if (C('MONEY_COUNT_WAY')) {
            //代理任务升级
            import('Lib.Action.Upgrade', 'App');
            $user = M('distributor')->find($uid);
            (new Upgrade())->upgrade($user);
        }
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '统计成功！',
        );
        return $return_result;
        
        
    }//end func generate_order_count
    
    
    /**
     * 
     * @param int $uid              //0代表总部
     * @param int $time            //month=0代表所有该统计的所有时间
     * @param array $update_info    //多维数组
     * @param boolen $is_add
     * @return array
     */
    private function update_order_count($uid,$time,$update_info,$is_add=FALSE){
        
        if( $uid === NULL || empty($update_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }
        
        
        if( (!is_numeric($time) || $time == NULL) && $time != 0 ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '时间不符合格式！',
            );
            return $return_result;
        }
        
        
        $time_len   = strlen($time);
        $month      = substr($time, 0,6);
        $day        = substr($time, 0,8);
        
        if( $time_len == 6 ){
            $day = 0;
        }
        elseif( $time_len == 8 ){
            
        }
        elseif( $time == 0 ){
            $month = $day = 0;
        }
        else{
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '时间不符合格式！',
            );
            return $return_result;
        }
        
        
        if( !isset($update_info['p_id']) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '产品ID参数必传！',
            );
            return $return_result;
        }
        
        
        $order_count = $this->order_count_obj;
        
        
        //-------------更新进货商的信息---------------
        
        $new_buy_money = isset($update_info['buy_money'])?$update_info['buy_money']:0;
        $new_buy_num = isset($update_info['buy_num'])?$update_info['buy_num']:0;
        $new_cost_money = isset($update_info['cost_money'])?$update_info['cost_money']:0;
        $new_cost_num = isset($update_info['cost_num'])?$update_info['cost_num']:0;
        $p_id = isset($update_info['p_id'])?$update_info['p_id']:0;//产品ID
        
        
        $condition = array(
            'uid'   =>  $uid,
            'month' =>  $month,
            'day'   =>  $day,
            'pid'   =>  $p_id,
        );
        
        $count_info = $order_count->where($condition)->find();
        
        $new_count_info = array(
            'uid'    =>  $uid,
            'month' =>  $month,
            'day'   =>  $day,
        );
        
        
        //无则添加，有则修改
        if( !empty($count_info) ){
            
            $old_buy_money = $count_info['buy_money'];     //进货总金额
            $old_buy_num = $count_info['buy_num'];         //进货总数量
            $old_cost_money = $count_info['cost_money'];   //出货总金额
            $old_cost_num = $count_info['cost_num'];       //出货总数量
            
            //如果是直接增加
            if( !$is_add ){
                $new_buy_money = bcadd($new_buy_money,$old_buy_money,2);
                $new_buy_num = bcadd($new_buy_num,$old_buy_num,0);

                $new_cost_money = bcadd($new_cost_money,$old_cost_money,2);
                $new_cost_num = bcadd($new_cost_num,$old_cost_num,0);
            }
            
            $new_count_info['updated'] =   time();
        }
        else{
            $new_count_info['pid'] =   $p_id;
            $new_count_info['created'] =   time();
        }
        
        $new_count_info['buy_money']  =   $new_buy_money;
        $new_count_info['buy_num']  =   $new_buy_num;
        $new_count_info['cost_money']  =   $new_cost_money;
        $new_count_info['cost_num']  =   $new_cost_num;
        
        
        if( empty($count_info) ){
            $save_buy_result = $order_count->add($new_count_info);
        }
        else{
            $save_buy_result = $order_count->where($condition)->save($new_count_info);
        }
        
        
        if( !$save_buy_result ){
            setLog('订单统计出错：'.print_r($new_count_info,1).',LastSql:'.$order_count->getLastSql(),'order_count_error');
            
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '统计失败！',
            );
            return $return_result;
        }
        
        //-------------end 更新进货商的信息---------------
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '统计成功！',
        );
        return $return_result;
        
    }//end func update_order_count
    
    
    
    

    
    
    //直接根据某月的订单生成订单统计
    public function cal_order_count($month,$condition_user=array()){
        
        if( empty($month) ){
            $month = date('Ym');
        }
        
        if( !is_numeric($month) || strlen($month) != 6 ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '月份不符合格式！',
            );
            return $return_result;
        }
        
        
        $order_obj = $this->order_obj;
        $distributor_obj = $this->distributor_obj;
        $order_count = $this->order_count_obj;
        
        
        $field_dis = 'id,level';
        $dis_info = $distributor_obj->where($condition_user)->field($field_dis)->select();
        
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '查无经销商需要进行订单统计！',
            );
            return $return_result;
        }
        
//        return $dis_info;
        
        $this_month_first_day = $month.'01';
        $this_month_first_day = $this->get_day_time_tmp($this_month_first_day);
        $next_month_first_day = $this->get_next_month_first_day($month);
        $this_month_last_time = $next_month_first_day - 1;
        
        $condition_order['paytime'] = array(array('gt',$this_month_first_day),array('lt',$next_month_first_day),'and');
        $order_info = $order_obj->where($condition_order)->select();
        
//        return $order_obj->getLastSql();
        
        $order_key_info = array();
        
        if( !empty($order_info) ){
            foreach( $order_info as $k_o => $v_o ){
            
                $v_o_uid = $v_o['user_id'];

                $order_key_info[$v_o_uid][] = $v_o;
            }
        }
        
        $error_info = array();
        
        foreach ( $dis_info as $k => $v ){
            $v_uid = $v['id'];
            $v_level = $v['level'];
            
            $the_order_info = isset($order_key_info[$v_uid])?$order_key_info[$v_uid]:array();
            
            if( empty($the_order_info) ){
                
                $new_count_info = array(
                    'uid'    =>  $v_uid,
                    'level' =>  $v_level,
                    'month' =>  $month,
                );
                
                $the_order_count_info = $order_count->where($new_count_info)->field('uid')->find();
                
                //如果已经有了该统计信息，不进行下一步操作
                if( !empty($the_order_count_info) ){
                    continue;
                }
                
                $new_count_info['created'] = time();
                $save_buy_result = $order_count->add($new_count_info);
                
                if( !$save_buy_result ){
                    $error_info['add_error']    =   $new_count_info;
                }
            }
            else{
                $gen_result = $this->generate_order_count($the_order_info,$month);
                
                if( $gen_result['code'] != 1 ){
                    $error_info['gen_error'] = $gen_result;
                }
            }
        }
        
        
        if( !empty($error_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '生成统计失败！',
                'error_info'    =>  $error_info,
            );
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '生成统计成功！',
                'error_info'    =>  $error_info,
            );
            return $return_result;
        }
        
        
        
    }//end func cal_order_count

    //---------------end 根据订单信息生成的统计-------------------
    
    
    
    //---------------订单的业务逻辑-------------------
    
    
    /**
     * 写入订单
     * @param array $write_info
     * @return array
     */
    public function write_order($uid,$write_info){
        $cart_ids = "";
        $order_num = $write_info['order_num'];
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        if ($write_info['cart_ids']) {
            $cart_ids = explode('|', $write_info['cart_ids']);
        }
        $pay_type = $write_info['pay_type'];
        $pay_photo = $write_info['pay_photo'];
        $templet_obj = M('templet');
        $distributor_obj = M('distributor');
        $order_obj = M('Order');
        //运费id
        $shipping_way_ids=$write_info['shipping_way_id'];
        $shipping_ways=$write_info['shipping_way'];
        //购物车总运费
        $total_money_fee = $write_info['total_money_fee'];
        //属性
        $sku_ids = $write_info['sku_ids'];
        //判断是否是自动下单的
        $auto_order=$write_info['auto_order'];
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        
        

        foreach ($sku_ids as $key => $id) {

            $sku_info = $sku->get_templet_sku($id);
            if (!$sku->check_templet_quantity($sku_info, $id, $p_ids[$key], $p_nums[$key])) {
                $return_result = array(
                    'code'  =>  -1,
                    'msg'   =>  '库存不足，请重新下单!',
                );
                return $return_result;
            }
        }

        
        if(isset($write_info['share_phone']) && isset($write_info['share_name']) && isset($write_info['share_province']) && isset($write_info['share_city']) && isset($write_info['share_county']) && isset($write_info['share_addre'])){
            $address['phone'] = $write_info['share_phone'];
            $address['name'] = $write_info['share_name'];
            $address['province'] = $write_info['share_province'];
            $address['city'] = $write_info['share_city'];
            $address['area'] = $write_info['share_county'];
            $address['address'] = $write_info['share_addre'];
//          setLog('share info');
        }elseif( isset($write_info['phone']) && isset($write_info['name']) && isset($write_info['province']) && isset($write_info['city']) && isset($write_info['area']) && isset($write_info['address']) ){
            $address['phone'] = $write_info['phone'];
            $address['name'] = $write_info['name'];
            $address['province'] = $write_info['province'];
            $address['city'] = $write_info['city'];
            $address['area'] = $write_info['area'];
            $address['address'] = $write_info['address'];
        }else{
            $address = M('address')->where(['user_id' => $uid, 'default' => 1])->find();
            //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
        }
//      var_dump($write_info);die;
        //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
         if( empty($address) ){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '请填写完整的收货信息！',
//                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }

        //参数判断
        if( empty($order_num) || empty($p_ids) || empty($p_nums) || !is_array($p_ids) 
                || !is_array($p_nums) || empty($address)){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '请确认您是否已选择商品，并填写完整的收货信息！',
//                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }

        if(empty($address['phone']) || empty($address['name']) || empty($address['province']) || empty($address['city']) ||empty($address['area'])||empty($address['address'])){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '收货信息不完整！',
            );
            return $return_result;
        }
        if(strlen($address['phone']) != '11'){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '手机号码长度有误！',
            );
            return $return_result;
        }

        if( $pay_type == 2 && empty($pay_photo) ){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '请提交您的支付截图！',
            );
            
            return $return_result;
        }
        
        if( !isset($this->all_pay_type[$pay_type]) ){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '非可用的支付类型！',
                'pay_type'  =>  $pay_type,
            );
            
            return $return_result;
        }
        
        
        /**
         * TODO:优化$order_num生成，在后端生成，并确保为唯一值
         */
//        $order_num_len = strlen($order_num);
//        $order_num_sub_len = $order_num_len-2;
//        $order_num = substr($order_num,2,$order_num_sub_len);
        $order_num = rand(1,99).$order_num;
        
        //经销商信息
        $where['id'] = $uid;
        $manager = $distributor_obj->where($where)->find();
        
        
        if( empty($uid) || empty($manager) ){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '没有找到用户信息！'
            );
            
            return $return_result;
        }
        
        
        $o_id = $manager['pid'];
        $tallestID = $manager['tallestID'];
        $manager_level = $manager['level'];
        
        //是否总部供货
        $is_top_supply_level = $this->is_top_supply_level;
        //如果该级别有特殊的限定则根据规则进行
        if( isset($is_top_supply_level[$manager_level]) && $is_top_supply_level[$manager_level] == 1 ){
            $o_id = 0;
        }
        elseif( $this->is_top_supply && !isset($is_top_supply_level[$manager_level]) ){
            $o_id = 0;
        }
        
        //如果属于最高级别经销商，供货商都是总部
        if ($manager['level'] == 1) {
            $o_id = 0;
        }
        
        //得到其上级信息，用于计算利润
        if( !empty($o_id) && $o_id != 0 ){
            $where_partent = array(
                'id'    =>  $o_id,
            );
            $partent_info = $distributor_obj->where($where_partent)->find();
        }
        else{
            $partent_info = array();
        }
        
        $partent_level = !empty($partent_info)?$partent_info['level']:0;
        
        //如果用户被禁用
        if( !empty($partent_info) && $partent_info['disable'] == 1 ){
            $return_result = array(
                'code'  =>  -6,
                'msg'   =>  '供货商'.$partent_info['name'].'已被系统禁用，请联系您的供货商或总部进行申诉！',
            );
            
            return $return_result;
        }
        
        
        
        //产品信息
        $price_key_name = "price" . $manager['level'];//该用户相应等级的产品单价key名
        
        $partent_price_key_name = "price" . $partent_level;//该用户上级相应等级的产品单价key名
        
        $templet_info = $templet_obj->select();
        
        if( empty($templet_info) ){
            $return_result = array(
                'code'  =>  -2,
                'msg'   =>  '没有产品信息！',
            );
            
            return $return_result;
        }
        
        //--------计算订单相关信息------
        $total_price = 0;//该次下单总金额（
        $total_num = 0;//该次下单的总数量
        $total_partent_profit = 0;//该次下单上级的总利润
        $add_order_info = array();//写入订单的部分信息
        $total_shipping_fee=0;//该次下单的总运费
        $sum_price=0;//订单的总价钱(运费+货物钱）
        $add_order_info_key = [];//写入订单的部分信息（以产品id为key）

        $templet_key_info = array();//以产品ID为键值的数组
        foreach( $templet_info as $temp_info ){
            $temp_info_id = $temp_info['id'];
            
            $templet_key_info[$temp_info_id] = $temp_info;
        }
        
        //属性
        $sku_info = $sku->get_templet_sku_ids($sku_ids);
        $sku_key_info = [];
        if( !empty($sku_info) ){
            foreach( $sku_info as $k_sku => $v_sku ){
                $sku_key_info[$v_sku['id']] = $v_sku;
            }
        }
        
        //计算该次下单的总金额
        foreach( $p_ids as $p_key => $p_id ){
            if( empty($p_id) ){
                continue;
            }
            
            //如果在产品信息没找到该产品单价，不进行计算
            if( !isset($templet_key_info[$p_id][$price_key_name]) ){
                continue;
            }
            
            
            $p_num = $p_nums[$p_key];//产品数量

            //属性
            $p_sku_id = $sku_ids[$p_key];//产品属性ID
            $p_sku_info = isset($sku_key_info[$p_sku_id])?$sku_key_info[$p_sku_id]:[];//产品属性信息
            if (empty($p_sku_info)) {
                $p_price = $templet_key_info[$p_id][$price_key_name];//产品单价
            } else {
                $p_price = $p_sku_info["price".$manager['level']];
            }

            $p_name = $templet_key_info[$p_id]['name'];//产品名称
            $p_image = $templet_key_info[$p_id]['image'];//产品名称
            $p_quantity = $templet_key_info[$p_id]['quantity'];//产品库存
            if($p_quantity <= 0){
                $return_result = array(
                    'code'  =>  '-15',
                    'msg'   => '产品'.$p_name.'的库存不足',
                );
                return $return_result;
            }
           // 运费相关
            $shipping_way=$shipping_ways[$p_key];
            $shipping_way_id=$shipping_way_ids[$p_key];

            //如果产品数量选择小于或等于0，不进行计算
            if( $p_num <= 0 ){
                continue;
            }
            
            //-----通过其上级的拿货价格，计算上级利润-----
            
            $partent_profit = 0;
            
            //如果在产品信息没找到该产品单价，或者该用户上级为总部，不进行计算
            if( !isset($templet_key_info[$p_id][$partent_price_key_name]) || $partent_level == 0 ){
                $p_partent_price =  0;
            }
            else{
                $p_partent_price    =   $templet_key_info[$p_id][$partent_price_key_name];
                //上下级单件差价 = 下级单价 - 上级单价
                $p_price_profit = bcsub($p_price,$p_partent_price,2);
                
                if( $p_price_profit >= 0 ){
                    //利润
                    $partent_profit = bcmul($p_price_profit,$p_num,2);
                }
                
            }
            
            //-----end 通过其上级的拿货价格，计算上级利润-----
            
            
            $p_price_all_info = $templet_key_info[$p_id];//该产品的所有信息
            
            $the_total_price = bcmul($p_price,$p_num,2);
            $total_price = bcadd($total_price,$the_total_price,2);
            $total_partent_profit = bcadd($total_partent_profit,$partent_profit,2);
            
            $total_num+=$p_num;


            //运费相关
            if(C('ORDER_SHIPPING')){
                if (empty($cart_ids) && empty($auto_order)) {
                    $condition=[
                        'id'=>$shipping_way_id,
                    ];
                    //产品参数
                    $product_parameter=$p_price_all_info['product_parameter'];
                    //运费模板的id
                    $template_id=$p_price_all_info['template_id'];
                    $shipping_fee=$this->get_shipping_fee($p_num,$the_total_price,$condition,$product_parameter,$template_id);

                    $total_shipping_fee+=$shipping_fee['toatl_money_fee'];
                    $sum_price=bcadd($total_price,$total_shipping_fee,2);
                } else {
                    //购物车运费
                    //如果满减是全场，则直接计算
                    if(!C('SHIPPING_REDUCE_WAY')){
                        foreach ($p_price_all_info as $v=>$k){
                            $shipping = M('shipping_goods_shipping_template')->where(['id'=>$k['template_id']])->find();
                            $redcuce_info=M('shipping_reduce')->where(['id'=>$shipping['reduce_id'],'shipping_reduce_way'=>0])->find();
                            if($redcuce_info){
//                                if ($total_num>=$redcuce_info['need_num'] && $total_price>=$redcuce_info['need_money']){
//                                    $total_money_fee = 0;
//                                }
                                //修改为高精度判断
                                if ( bccomp($total_num,$redcuce_info['need_num'],2) != -1 && bccomp($total_price,$redcuce_info['need_money'],2) != -1 ){
                                    $total_money_fee = 0;
                                }
                            }
                        }
                    }
                    
                    $total_shipping_fee = $total_money_fee;
                    $sum_price=bcadd($total_price,$total_shipping_fee,2);

                }
            }
            else{
                $sum_price = $total_price;
            }
            
            
            $add_order_info[] = $add_order_info_key[$p_id] = array(
                'p_id'  =>  $p_id,//产品ID
                //属性
                'sku_id'=>  $p_sku_id,
                'name'  =>  $p_name,
                'image' => $p_image,
                'num'   =>  $p_num,
                'price' =>  $p_price,
                'par_price' =>  $p_partent_price,//上级单价
                'par_profit'   =>  $p_price_profit,//上级利润
                'tem_info'  =>  $p_price_all_info,
                'shipping_way_id'=>$shipping_way_id,
                'shipping_fee'=>$shipping_fee['toatl_money_fee'],
                'shipping_way'=>$shipping_way
            );
        }
        
        
        //得到功能模块信息
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $STOCK_ORDER = $FUNCTION_MODULE['STOCK_ORDER'];
        
        //假如是开启了云仓模块，那么就判断云仓的订单（原代理商城不判断）
        if( !$STOCK_ORDER ){
            $this_order_info = array(
                'total_num' =>  $total_num,
                'total_money'   =>  $total_price,
                'total_shipping_fee'=>$total_shipping_fee,
                'sum_price'=>$sum_price,
                'order_detail' => $add_order_info_key,
            );

            $order_limit_result = $this->order_limit($uid,$manager,$this_order_info);

            if( $order_limit_result['code'] != 1 ){
                $return_result = array(
                    'code'  =>  '-6',
                    'msg'   =>  !empty($order_limit_result['msg'])?$order_limit_result['msg']:'订单限制未通过',
                );
                return $return_result;
            }
        }
        
        
        
        //判断金额
        if( $sum_price <= 0 ){
            $return_result = array(
                'code'  =>  '-3',
                'msg'   =>  '订单总金额不能小于或等于0！',
            );
            return $return_result;
        }
        
        
        //判断数量
        if( $total_num <= 0 ){
            $return_result = array(
                'code'  =>  '-4',
                'msg'   =>  '订单总产品数量不能小于或等于0！'
            );
            return $return_result;
        }
        //判断上级利润
        if( $total_partent_profit <= 0 ){
            $total_partent_profit = 0;
        }
        
        
        //--------end 计算订单相关信息------
        
        
        //---------检查是否有足够的金额扣费------
        
        if( $pay_type == 1 || $pay_type == 0 ){
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $check_money = $Funds->check_recharge_money($uid,$sum_price,TRUE);

            if( !$check_money ){
                $return_result = array(
                    'code'  =>  '-5',
                    'msg'   =>  '请检查您的余额以及未审核的订单！'
                );
                return $return_result;
            }
        }
        
        //---------end 检查是否有足够的金额扣费----
        
        
        //这里已经废弃，因为在审核订单时进行扣费
        
        //----------扣费逻辑------------
//        $charge_money_result = $this->charge_money($uid,$total_price,'order',$order_num);
//        
//        if( $charge_money_result['code'] != 1 ){
//            return $return_result;
//        }
        //----------end 扣费逻辑------------
//        
//        
//        //----------订单返还-----------
//        $monery_order_return_result = $this->monery_order_return($uid,$order_num,$add_order_info);
//        //----------end 订单返还-----------
        
        
        
        //----------生成订单------------
        $error_info = array();
        foreach( $add_order_info as $the_order_info ){
            $the_p_name = $the_order_info['name'];
            $the_p_image = $the_order_info['image'];
            $the_p_id = $the_order_info['p_id'];
            $the_num = $the_order_info['num'];
            $the_price = $the_order_info['price'];

            //属性
            $the_sku_id = $the_order_info['sku_id'];
            
            $the_p_par_price = isset($the_order_info['par_price'])&&!empty($the_order_info['par_price'])?$the_order_info['par_price']:0;
            $the_p_par_profit = isset($the_order_info['par_profit'])&&!empty($the_order_info['par_profit'])?$the_order_info['par_profit']:0;
            
            //属性
            //记录下单商品的属性值
            $properties = "";
            $style = "";
            if ($the_sku_id) {
                $properties = $sku->get_templet_property_com($the_sku_id);
                $style = $sku->get_value($properties);
            }

            //运费相关
            $the_shipping_way_id = $the_order_info['shipping_way_id'];
            $shipping_fee=$the_order_info['shipping_fee'];
            $shipping_way=$the_order_info['shipping_way'];

            $arr = array(
                'order_num' => $order_num,  //订单号
                'user_id' => $uid,          //下单用户
                'o_id' => $o_id,            //接单供货商
                'p_id' => $the_p_id,        //产品ID
                'p_name' => $the_p_name,    //产品名字------2016.11.9新增，ID应该废弃
                'p_image' => $the_p_image,
                'status' => 1,              //订单状态，默认1为未审核
                's_name' => $address['name'],        //收货人名字
                's_addre' => $address['province'].$address['city'].$address['area'].$address['address'],      //收货人地址
                's_phone' => $address['phone'],      //收货人手机
                'notes' => $write_info['note'],          //订单备注
                'num' => $the_num,          //产品数量
                'price' => $the_price,      //产品单价
                'par_price' => $the_p_par_price,    //上级的产品单价------2016.11.9新增，用于上级利润记录
                'par_profit' => $the_p_par_profit,  //上级的产品利润------2016.11.9新增，用于上级利润记录
                'total_par_profit' => $total_partent_profit,    //上级的该订单号总利润-----2016.11.9新增，用于上级利润记录
                'time' => time(),           //订单生成日期
//                'month' => date('Ym'),      //订单生成月份
                'total_num' => $total_num,  //总数量----下单时多个产品记录为同一订单号的多条记录
                'total_price' => $total_price,  //总金额-----理由同上
                'tallestID' => $tallestID,      //最高负责人
                'paytime'   =>  0,          //支付时间-------2016.11.9近期新增，一般在审核时更新，如没虚拟币模块，可为审核时间
                'pay_type'  =>  $pay_type,
                'pay_photo' =>  $pay_photo,
                'province'=>$address['province'],
                'city'=>$address['city'],
                'county'=>$address['area'],
                'address_detail' => $address['address'],
                //属性
                'sku_id' => $the_sku_id,    //商品属性ID
                'properties' => $properties,
                'style' => $style,
                //运费相关
                'shipping_fee'=>$total_shipping_fee,
                'shipping_way_id'=>json_encode($shipping_way_ids),
                'sum_price'=>$sum_price,
                'shipping_way'=>$shipping_way

            );
            
            
//            old code
//            $arr = array(
//                'order_num' => $order_num,
//                'user_id' => $uid,
//                'o_id' => $o_id,
//                'p_id' => $the_p_id,//产品ID
//                'status' => 1,
//                's_name' => $s_name,
//                's_addre' => $s_addre,
//                's_phone' => $s_phone,
//                'notes' => $notes,
//                'num' => $the_num,
//                'price' => $the_price,
//                'time' => time(),
//                'month' => date('Ym'),
//                'total_num' => $total_num,
//                'total_price' => $total_price,
//                'tallestID' => $tallestID,
//            );
            $addorder = $order_obj->add($arr);
            
//          var_dump($arr);die;
//            $arr['last_sql']    =   $order_obj->getLastSql();
//            $error_info[]   =   $arr;
            
            if( !$addorder ){
                break;
            }elseif($write_info['type']=='shop'){    //店中店
                $condition_shop = [
                    'order_num' => $write_info['order_shop_num'],
                    'o_id' => $uid
                ];
                $shop_order = M('shop_order');
                $data['auto_order'] = 2;
                $auto_info = $shop_order->where($condition_shop)->save($data);
                if($auto_info){
                    $error_info['shop_order'] = '自动下单成功，但更改店中店订单失败！';
                }
            }else{
                if(isset($write_info['auto_order_num'])){
                    $condition_shop = [
                        'order_num' => $write_info['auto_order_num']
                    ];
                    $shop_order = M('order');
                    $data['auto_order'] = 2;
                    $auto_info = $shop_order->where($condition_shop)->save($data);
                    if($auto_info){
                        $error_info['shop_order'] = '自动下单成功，但更改订单状态失败！';
                    }
                }
            }

            //属性
            //如果是向上级发货并且代理有库存功能，则加减库存需要判断

            $sku_info = $sku->change_quantity_and_sales($the_sku_id, $the_p_id, $the_num);
        }
        //----------end 生成订单------------
        
        
        if( !$addorder ){
            $return_result = array(
                'code'  =>  '-1',
                'msg'   =>  '创建订单失败！',
                'error_info'=>  $error_info,
            );
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '下单成功！',
                'order_num' =>  $order_num,
            );

            //新订单模板消息
            if ($o_id) {
                import('Lib.Action.Message','App');
                $message = new Message();
                $openid = $distributor_obj->where(['id' => $o_id])->getField('openid');
                $content = [
                    'customer_info' => $manager,
                    'total_price' => $sum_price,
                ];
                $message->push(trim($openid), $content , $message->order_new);
            }
            
            //如果是购物车就删除id
            if ($cart_ids) {
                M('order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            }
            return $return_result;
        }
        
        
    }//end func write_order
    
    
    
    
    /**
     * 删除订单
     * @param type $order_num   //数组形式则取多个
     * @param type $cur_id      //操作者的ID    0为总部
     * @return string|int
     */
    public function delorder($order_num,$cur_id='') {
        if( empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }
        
        $order_obj = M('order');
        
        $where = array(
            'status' => 1,
//            'order_num' =>  $order_num,
        );
        
        $order_num_format = $order_num;
        if( is_array($order_num) ){
            $where['order_num'] = ['in',$order_num];
            $order_num_format = implode(',', $order_num);
        }
        else{
            $where['order_num'] = $order_num;
        }
        
        if( $cur_id != '' && $cur_id != NULL ){
            $where['oid']   =   $cur_id;
        }
        
        $order_info = $order_obj->where($where)->group('order_num')->select();
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到该订单！',
            );
            return $return_result;
        }
        
        $can_del = TRUE;
        foreach( $order_info as $v ){
            $v_status = $v['status'];
            
            if( $v_status != 1 ){
                $can_del = FALSE;
            }
        }
        
        //只有未审核的订单才能删除
        if( !$can_del ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '删除的订单有审核的订单，不能删除！',
            );
            return $return_result;
        }
        
        $all_order_info = $order_obj->where($where)->select();
        
//        $del_res = $order_obj->where($where)->delete();
        //将原来的删除订单改为将订单的状态改为-1 edit by qjq 2018-6-07
        $del_res = $order_obj->where($where)->save(['status'=> -1]);

        if( !$del_res ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '删除失败，请重试！',
            );
            return $return_result;
        }
        else{
             //属性
            import('Lib.Action.Sku','App');
            $sku = new sku();
            
            foreach( $all_order_info as $k => $v ){
                $sku_id = $v['sku_id'];
                $templet_id = $v['p_id'];
                $num = $v['num'];

//                if( empty($sku_id) || $num <= 0 ){
//                    continue;
//                }
                if($num <= 0 ){
                    continue;
                }
                $sku->change_quantity_and_sales($sku_id, $templet_id, $num, 'inc');
            }
            //取消订单模板消息
            import('Lib.Action.Message', 'App');
            $message = new Message();
            
            foreach( $order_info as $info ){
                $openid = M('distributor')->where(['id' => $info['o_id']])->getField('openid');
                $message->push(trim($openid), $info, $message->order_cancle);
            }
            
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '订单'.$order_num_format.'删除成功！',
            );
            return $return_result;
        }
        
        
    }//end func delorder
    
    //变为可抢订单
    public function change_grab_order($order_nums){
        
        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $condition = [
            'order_num' =>  ['in',$order_nums],
        ];
        
        $order_info = $this->order_obj->where($condition)->select();
        
        $order_info_key = [];
        $true_order_nums = [];
        
        
        $status_error = '';
        $oid_error = '';
        
        //------------start 判断订单状态---------------
        
        foreach( $order_info as $v ){
            $v_order_num = $v['order_num'];
            $v_status = $v['status'];
            $v_oid = $v['o_id'];
            
            if( $v_status != 1 ){
                $status_error = '订单号'.$v_order_num.'并非未审核状态！';
                break;
            }
            if( $v_oid != 0 ){
                $oid_error = '订单号'.$v_order_num.'并非总部订单，无法分发到抢单平台！';
                break;
            }
            
            $true_order_nums[$v_order_num] = $v_order_num;
//            $order_info_key[$v_order_num][] = $v;
        }
        
        $true_order_nums_str = '';
        if( !empty($true_order_nums) ){
            $true_order_nums_str = implode(',', $true_order_nums);
        }
        
        //错误
        if( !empty($status_error) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  $status_error,
            );
            return $return_result;
        }
        if( !empty($oid_error) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  $oid_error,
            );
            return $return_result;
        }
        //------------end 判断订单状态---------------
        
        //------------start 更新订单----------------
        $save_info = [
            'status'    =>  8,
            'grab_time'   =>  1,//转为抢单中心的时间
        ];
        
        $result = $this->order_obj->where($condition)->save($save_info);
        
        if( !$result ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '更改为可抢订单失败，请重试！',
                'getLastSql' =>  $this->order_obj->getLastSql(),
                'getDbError'  =>  $this->order_obj->getDbError(),
            );
            return $return_result;
        }
        //------------end 更新订单----------------
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '订单号'.$true_order_nums_str.'已转到抢单中心！',
            'order_nums'    => $true_order_nums_str,
        );
        return $return_result;
        
    }
    
    /**
     * 抢订单
     * @param type $uid 抢单用户ID
     * @param type $order_num   订单号
     * @return string|int
     */
    public function grab_order($uid,$order_num){
        
        if( $uid == NULL || !is_numeric($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '对象参数错误！',
            );
            return $return_result;
        }
        
        if( empty($order_num) || !is_array($order_num) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '订单号参数错误！',
            );
            return $return_result;
        }
        
        $condition = [
            'order_num' =>  ['in',$order_num],
        ];
        
        $order_info = $this->order_obj->where($condition)->find();
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '查无订单！',
            );
            return $return_result;
        }
        
        if( $order_info['o_id'] != 0 ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '该订单已被抢！',
            );
            return $return_result;
        }
        if( $order_info['status'] != 8 ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '非抢单平台的订单无法进行抢单！',
                'info'  =>  $order_info,
                'order_num' =>  $order_num,
            );
            return $return_result;
        }
        
        
        $save_info = [
            'o_id'   =>  $uid,
            'status'    =>  1,
        ];
        
        $result = $this->order_obj->where($condition)->save($save_info);
        
        if( !$result ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '抢单失败，请重试！',
            );
            return $return_result;
        }
        
        $order_num_str = implode('、', $order_num);
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '订单号'.$order_num_str.'抢单成功！',
        );
        return $return_result;
    }
    
    
    //后台审核订单
    public function radmin_audit($order_nums){
        
        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        $order_obj = M('order');
        $templet_obj = M('templet');
        
        //--------订单信息-----------------
        $condition_order['order_num']   =   array('in',$order_nums);
        
        //这里获取的会出现例如两个order_num一样的订单，注意分清
        $order_info = $order_obj->where($condition_order)->select();
        
        if( empty($order_info) ){
            $return_result = array(
                'status'    =>  0,
                'msg'   =>  '',
                'error_info'    =>  $order_info,
            );

            return $return_result;
        }
        
        $order_info_key = array();//以订单号为key
        $order_info_key2 = array();//以订单号为key的多维数组
        foreach( $order_info as $k => $v ){
            $v_order_num = $v['order_num'];
            
            $order_info_key[$v_order_num]   =   $v;
            $order_info_key2[$v_order_num][]   =   $v;
        }
        //--------end 订单信息-----------------
        
        $is_status_error = FALSE;
        $is_charge_money_break = FALSE;
        $charge_money_result = array();
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        import('Lib.Action.NewRebate','App');
        $new_rebate_obj = new NewRebate();
        
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $STOCK_ORDER = $FUNCTION_MODULE['STOCK_ORDER'];
        
        $templet_info = array();
        $add_order_info   =   array();
        
        foreach ($order_nums as $order_num) {
            
            $uid = $order_info_key[$order_num]['user_id'];
            $o_id = $order_info_key[$order_num]['o_id'];
            $total_price = $order_info_key[$order_num]['total_price'];
            $status = $order_info_key[$order_num]['status'];
            $pay_type = $order_info_key[$order_num]['pay_type'];
            $sum_price = $order_info_key[$order_num]['sum_price'];
//            $order_month = $order_info_key[$order_num]['month'];
            
            
            //判断
            if( $status != 1 ){
                $is_status_error = TRUE;
                break;
            }
            
            
            //----------扣费逻辑------------
            if( ($pay_type == 1 || $pay_type == 0) && !$this->FUNCTION_MODULE_STOCK_ORDER ){
                $charge_money_result = $Funds->charge_money($uid,$sum_price,'order',$order_num);
            
                if( $charge_money_result['code'] != 1 ){
                    $charge_money_result['msg'] = '订单号：'.$order_num.'，'.$charge_money_result['msg'];
                    $charge_money_result['status']  =   0;
                    $is_charge_money_break = TRUE;
                    break;
                }
            }
            
            //----------end 扣费逻辑------------
            
            //----------订单返还-----------
            
            if( $Funds->is_order_return && (!$this->FUNCTION_MODULE_STOCK_ORDER || $Stock->can_refund_sotck) ){
//                foreach( $order_info_key2[$order_num] as $o_k => $o_v ){
//                    $p_id = $o_v['p_id'];
//                    $p_num = $o_v['num'];
//                    $p_price = $o_v['price'];
//
//                    if( !isset($templet_info[$p_id]) ){
//                        $templet_info[$p_id] = $templet_obj->where(array('id'=>$p_id))->find();
//                    }
//
//                    $add_order_info[$order_num][] = array(
//                        'p_id'  =>  $p_id,//产品ID
//                        'num'   =>  $p_num,
//                        'price' =>  $p_price,
//                        'tem_info'  =>  $templet_info[$p_id],
//                    );
//                }
//
//                $monery_order_return_result = $Funds->monery_order_return($uid,$order_num,$add_order_info[$order_num]);
                
                $monery_order_return_result = $Funds->monery_order_return($uid,$order_num,$order_info_key2[$order_num]);

                //$this->ajaxReturn(array('status' => 0,'test'=>$monery_order_return_result), 'json');

                if( $monery_order_return_result['code'] != 1 ){
                    setLog('订单号：'.$order_num.'----返回信息为：'.var_dump($monery_order_return_result,1), 'order_return_money');
                }
            }
            //----------end 订单返还-----------
            
            
            //----------start 库存点-----------
            $Stock->record_by_order_info($uid,$order_info_key2[$order_num]);
            //----------end 库存点-----------
            
            

            $order_save_info = array(
                'status'    =>  2,
                'paytime'    =>  time(),
            );
            
            $order_obj->where(array('order_num' => $order_num))->save($order_save_info);//6为已审核未配送状态
//            foreach ($order_info_key2[$order_num] as $order) {
//                $templet_obj->where(['id' => $order['p_id']])->setInc('sales', $order['num']);
//            }
            //订单审核模板消息
            import('Lib.Action.Message','App');
            $message = new Message();
            $openid = M('distributor')->where(['id' => $order_info_key[$order_num]['user_id']])->getField('openid');
            $message->push(trim($openid), $order_info_key[$order_num] , $message->order_audit);
            
            //如果订单由总部审核发货，则进行订单月统计
            if ($this->is_top_supply) {
                $this->month_count($order_info_key2[$order_num][0]);
            } else {
                //由上级审核发货的模式还需要沟通，待以后开发
                
            }
            
            //----------生成订单统计--------------
            if( !$this->FUNCTION_MODULE_STOCK_ORDER || $Stock->can_refund_sotck ){
                $this->generate_order_count($order_info_key2[$order_num]);
            }
            //----------end 生成订单统计--------------
            
            //----------生成返利--------------
            //开启云仓模块时，触发订单返利的环节在云仓下单时产生
            if( !$STOCK_ORDER ){
                $Rebate->radmin_order_audit_rebate($uid,$order_info_key2[$order_num]);
            }
            //----------end 生成返利--------------
            
            //----------更改总部库存记录--------------
            if( $o_id == 0 ){
                $this->update_inventory($o_id,$order_info_key2[$order_num]);
            }
            //----------end 更改总部库存记录--------------
            
            //----------积分触发----------------------
            $Integral->aduit_order($uid,$order_info_key2[$order_num]);
            //----------end 积分触发----------------------

            //------------触发团队返利----------------
            //如果开启团队业绩并且是实时统计的，则触发
            if(C('REBATE')['ORDINARY_TEAM'] && (!C('REBATE')['CLICK_TEAM_REBATE'])){
                $new_rebate_obj->create_team_rebate($uid);
            }
            //------------end触发团队返利结束---------

        }//end froeach
        
        if( $is_status_error ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '只有未审核订单才能审核！',
                'status'=>  0,
            );
            return $return_result;
        }
        elseif( $is_charge_money_break ){
            
            return $charge_money_result;
        }
        else{
            $order_num_str = implode(',', $order_nums);
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '订单号：'.$order_num_str.'的订单审核通过！',
                'status'=>  1,
            );
            return $return_result;
        }
        
    }//end func audit
    
    //经销商审核订单
    public function admin_audit($order_num){
        
        if( empty($order_num) || is_array($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $orderObj = M('order');
        $distributorObj = M('distributor');
        $templet_obj = M('templet');
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $STOCK_ORDER = $FUNCTION_MODULE['STOCK_ORDER'];
        
//        import('Lib.Action.Stock','App');
//        $Stock = new Stock();
        
        $condition_order = array(
            'order_num' =>  $order_num,
        );
        
        $order_info = $orderObj->where($condition_order)->select();

        if( empty($order_info) ){
            $return_result = array(
                'status'    =>  3,
                'msg'   =>  '找不到订单',
                'error_info'    =>  $order_info,
            );

            return $return_result;
        }
        
        $templet_info = array();
        $add_order_info   =   array();
        
        $order_user_id = $order_info[0]['user_id'];
        $order_o_id = $order_info[0]['o_id'];
        $order_total_price = $order_info[0]['total_price'];
        $status = $order_info[0]['status'];
        $pay_type = $order_info[0]['pay_type'];
        $order_sum_price = $order_info[0]['sum_price'];

        //判断
        if( $status != 1 ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '只有未审核订单才能审核！',
                'status'=>  0,
            );
            return $return_result;
        }
        
        $check_can_charge = $Stock->check_can_charge($order_o_id,$order_info);
        
        if( $check_can_charge['code'] != 1 ){
            return $check_can_charge;
        }
        
        
        //----------扣费逻辑------------
        if( ($pay_type == 1 || $pay_type == 0) && !$this->FUNCTION_MODULE_STOCK_ORDER ){
            $charge_money_result = $Funds->charge_money($order_user_id,$order_sum_price,'order',$order_num);

            if( $charge_money_result['code'] != 1 ){
                return $charge_money_result;
            }
        }
        //----------end 扣费逻辑------------
        
        //----------订单返还-----------
        if( $Funds->is_order_return && (!$this->FUNCTION_MODULE_STOCK_ORDER || $Stock->can_refund_sotck) ){
            
//            foreach( $order_info as $o_k => $o_v ){
//                $p_id = $o_v['p_id'];
//                $p_num = $o_v['num'];
//                $p_price = $o_v['price'];
//
//                if( !isset($templet_info[$p_id]) ){
//                    $templet_info[$p_id] = $templet_obj->where(array('id'=>$p_id))->find();
//                }
//
//                $add_order_info[] = array(
//                    'p_id'  =>  $p_id,//产品ID
//                    'num'   =>  $p_num,
//                    'price' =>  $p_price,
//                    'tem_info'  =>  $templet_info[$p_id],
//                );
//            }
//
//            $monery_order_return_result = $Funds->monery_order_return($order_user_id,$order_num,$add_order_info);
            
            $monery_order_return_result = $Funds->monery_order_return($order_user_id,$order_num,$order_info);
            
            //$this->ajaxReturn(array('status' => 0,'test'=>$monery_order_return_result), 'json');

            if( $monery_order_return_result['code'] != 1 ){
                setLog('订单号：'.$order_num.'----返回信息为：'.var_dump($monery_order_return_result,1), 'order_return_money');
            }
        }
        //----------end 订单返还-----------
        
        
        //----------start 库存点-----------
        $Stock->record_by_order_info($order_user_id,$order_info);
        //----------end 库存点-----------
        
        
        //----------生成订单统计--------------
        if( !$this->FUNCTION_MODULE_STOCK_ORDER || ($this->FUNCTION_MODULE_STOCK_ORDER && $Stock->can_refund_sotck) ){
            $this->generate_order_count($order_info);
        }
        //----------end 生成订单统计--------------
        
        //----------生成返利--------------
        //开启云仓模块时，触发订单返利的环节在云仓下单时产生
        if( !$STOCK_ORDER ){
            $Rebate->admin_order_audit_rebate($order_user_id,$order_info);
        }
        //----------end 生成返利--------------
        
        //----------积分触发----------------------
        $Integral->aduit_order($order_user_id,$order_info);
        //----------end 积分触发----------------------
        
        
        $where['order_num'] = $order_num;
        $order_save_info = array(
            'status'    =>  2,
            'paytime'    =>  time(),
        );
        $rew = $orderObj->where($where)->save($order_save_info);

        if( !$rew ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '审核未通过，请重试！',
                'status'=>  0,
            );
            return $return_result;
        }
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '审核通过！',
            'status'=>  1,
        );
        
//        //销量+
//        foreach ($order_info as $order) {
//            $templet_obj->where(['id' => $order['p_id']])->setInc('sales', $order['num']);
//        }

        //订单审核模板消息
//        if (!$this->is_top_supply) {
            import('Lib.Action.Message','App');
            $message = new Message();
            $openid = $distributorObj->where(['id' => $order_info[0]['user_id']])->getField('openid');
            $message->push(trim($openid), $order_info[0] , $message->order_audit);
//        }
        return $return_result;
        
    }//end func admin_audit
    
    
    
    /**
     * 订单确认收货
     * @param type $order_num
     * @param type $cur_audit_id 执行者ID
     * @return string|int
     */
    public function confirm_order($order_num,$cur_audit_id=0){
        
        if( empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有订单号',
            );
            return $return_result;
        }
        
        $order_obj = M('Order');
        
        
        $condition = array(
            'order_num' =>  $order_num,
        );
        
        //如非本人
        if( $cur_audit_id != 0 ){
            $condition['user_id'] = $cur_audit_id;
        }
        
        
        $order_info = $order_obj->where($condition)->select();
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有此订单或非订单本人操作！',
            );
            return $return_result;
        }
        
        $order_user_id = $order_info[0]['user_id'];
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        //-----在经销商确认收货时生成记录-----
        $res = $Rebate->confirm_order_audit_rebate($order_user_id,$order_info);
        //-----end 在经销商确认收货时生成记录
        
        //确认生成返利记录再生成订单
        $data['status'] = 3;
        $result = $order_obj->where('order_num=' . $order_num)->data($data)->save(); // 根据条件保存修改的数据
        
        if( !$result ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '确认收货失败，请重试！',
            );
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '确认收货',
                'rebate' => $res,
            );
            return $return_result;
        }
    }//end func confirm_order
    
    
    //订单限制
    public function order_limit($uid,$dis_info,$this_order_info){
        
        if( !$this->opent_order_limit ){
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不启用订单限制',
            );
            
            return $return_result;
        }
        
        if( empty($uid) || empty($this_order_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            
            return $return_result;
        }
        
        
        if( empty($dis_info) ){
            $distributor_obj = M('distributor');
            
            $condition_dis = array(
                'id'    =>  $uid,
            );
            
            $dis_info = $distributor_obj->where($condition_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到该经销商!',
            );
            
            return $return_result;
        }
        
        //下单用户信息
        $dis_level = $dis_info['level'];
        
        //得到功能模块信息
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $STOCK_ORDER = $FUNCTION_MODULE['STOCK_ORDER'];
        
        //假如是开启了云仓模块，那么就判断云仓的订单（原代理商城不判断）
        if( $STOCK_ORDER ){
            $order_obj = M('stock_order');
        }
        else{
            $order_obj = M('order');
        }
        
        $order_limit_obj = M('order_limit');
        
        
        $level_search = array($dis_level,0);
        $condition_order_lim = array(
            'level' =>  array('in',$level_search),
        );
        
        $order_limit_info = $order_limit_obj->where($condition_order_lim)->order('level desc')->select();
        
        if( empty($order_limit_info) ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '没有针对该次订单的限制！',
            );
            
            return $return_result;
        }
        
        
        $return_limit = [];//订单限制判断结果
        //该次订单的信息
        $order_total_num = isset($this_order_info['total_num'])?$this_order_info['total_num']:0;
        $order_total_money = isset($this_order_info['total_money'])?$this_order_info['total_money']:0;
        $order_detail = isset($this_order_info['order_detail'])?$this_order_info['order_detail']:[];
        
        $check_all_level = TRUE;
        
        //列出限制时，按级别从大到小排列，即级别0（全局级别判断）到最后才判断
        //如已判断针对自己级别的限制，不需要再判断全局级别
        foreach( $order_limit_info as $v ){
            //各个限制条件
            //TODO:可增加的限制条件：
            //针对产品的数量及金额（需要考虑多种规则的冲突）
            $is_first = $v['is_first'];//首次下单限制
            $tid = $v['tid'];
            $total_num_min = $v['total_num_min'];//订单产品总数量最小限制
            $total_money_min = $v['total_money_min'];//订单总金额最小限制
            $level = $v['level'];//限制的级别
            
            if( isset($order_detail[$tid]) ){
                $tid_detail = isset($order_detail[$tid])?$order_detail[$tid]:[];
            }
            
            if( $level == $dis_level ){
                $check_all_level = FALSE;
            }
            
            if( $level == 0 && !$check_all_level ){
                break;
            }

            //--------------限制规则---------------
            //如果是首次下单限制
            if( $is_first == 1 ){
                $condition_order = array(
                    'user_id'   =>  $uid,
                );
                
                $old_order_info = $order_obj->where($condition_order)->field('order_num,status')->order('time asc')->group('order_num')->find();

                $old_order_info_status = $old_order_info['status'];

                if( $old_order_info_status == 1 ){
                    $return_limit = array(
                        'code'  =>  4,
                        'msg'   =>  '首次下单的订单未审核禁止再进行下单！',
                    );
                    break;
                }
                else if( !empty($old_order_info) ){
                    //已有订单，不再进行首次下单规则限制！
                    continue;;
                }
            }
            
            //如果有产品的话，
            if( !empty($tid_detail) ){
                $price = $tid_detail['price'];
                $num = $tid_detail['num'];
                $name = $tid_detail['name'];
                $tid_total_money = bcmul($price,$num,2);
                
                if( bccomp($num,$total_num_min,0) == -1 && $total_num_min != 0 ){
                    $return_limit = array(
                        'code'  =>  5,
                        'msg'   =>  '产品《'.$name.'》数量最小限制（'.$total_num_min.'）未通过！',
                    );
                    break;
                }
                elseif( bccomp($tid_total_money,$total_money_min,2) == -1 && $total_money_min != 0 ){
                    $return_limit = array(
                        'code'  =>  6,
                        'msg'   =>  '产品《'.$name.'》金额最小限制（'.$total_money_min.'）未通过！',
                    );
                    
                    break;
                }
            }
            else{
                //订单产品总数量限制最小限制
                if( bccomp($order_total_num,$total_num_min,0) == -1 && $total_num_min != 0 ){
                    $return_limit = array(
                        'code'  =>  7,
                        'msg'   =>  '订单产品总数量最小限制（'.$total_num_min.'）未通过！',
                    );
                    
                    break;
                }
                //订单总金额最小限制
                elseif( bccomp($order_total_money,$total_money_min,2) == -1 && $total_money_min != 0 ){
                    $return_limit = array(
                        'code'  =>  8,
                        'msg'   =>  '订单总金额最小限制（'.$total_money_min.'）未通过！',
                    );

                    break;
                }
            }
            
            
            
            
            
            //通过所有限制，继续循环判断
        }
        
        //--------------end 限制规则---------------
        
        if( !empty($return_limit) ){
            return $return_limit;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '通过所有限制，正常下单！',
            );

            return $return_result;
        }
    }//end func order_limit
    
    
    
    //库存记录
    public function update_inventory($uid,$update_info){
        
        if( !$this->is_inventory ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '未开启库存记录！',
            );
            return $return_result;
        }
        
        
        if( $uid == NULL || empty($update_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        $pids = array();
        foreach( $update_info as $k_if => $v_if ){
            $v_if_p_id = $v_if['p_id'];
            $v_if_num = $v_if['num'];
            
            $pids[] = $v_if_p_id;
        }
        
        
        
        $inventory_obj = M('inventory');
        
        
        $condition_inv = array(
            'pid'   =>  array('in',$pids),
        );
        
        $list = $inventory_obj->where($condition_inv)->select();
        
        
        
        $save_list = array();
        foreach( $list as $k => $v ){
            
            $v_pid = $v['pid'];
            $v_out_num = $v['out_num'];
            
            $save_list[$v_pid] = $v;
        }
        
        
        
        foreach( $update_info as $k_if => $v_if ){
            $v_if_p_id = $v_if['p_id'];
            $v_if_num = $v_if['num'];
            
            $new_out_num = 0;
            
            
            if( isset($save_list[$v_if_p_id]['out_num']) ){
                $old_out_num = $save_list[$v_if_p_id]['out_num'];
                
                $new_out_num = $v_if_num + $old_out_num;
                
                $condition_inventory_save = array(
                    'uid'   =>  $uid,
                    'pid'   =>  $v_if_p_id,
                );

                $save_info = array(
                    'out_num'   =>  $new_out_num,
                );

                $inventory_obj->where($condition_inventory_save)->save($save_info);
            }
            else{
                $new_out_num = $v_if_num;
                
                $add_info = array(
                    'uid'   =>  $uid,
                    'pid'   =>  $v_if_p_id,
                    'total_num' =>  0,
                    'out_num'   =>  $new_out_num,
                    'created'   =>  time(),
                );
                $inventory_obj->add($add_info);
            }
            
        }
        
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '库存记录成功！',
        );
        
        
        return $return_result;
    }//end func update_inventory
    
    
    
    
    //---------------end 订单的业务逻辑-------------------
    
    
    
    //获取某日的时间戳
    private function get_day_time_tmp($day){
        
        if( empty($day) ){
            return FALSE;
        }
        
        //切割出年份  
        $tmp_year=substr($day,0,4);  
        //切割出月份  
        $tmp_mon =substr($day,4,2);  
        //切割出日期  
        $tmp_day =substr($day,6,2);  
        
        
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon,$tmp_day,$tmp_year);  
        
//        return $fm_next_month = date("Ymd",$tmp_nextmonth);
        
        return $tmp_nextmonth;
    }//end func get_time_tmp
    
    
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
    
    //订单月统计
    public function month_count($data) {
        return true;//先废弃，使用 order_count进行统计
        
        $model = M('order_month_count');
        $month = get_month();
        $day = date('Ymd');
        
        $where = [
            'uid' => $data['user_id'],
            'month' => $month,
            'day' => $day
        ];
        $month_count = $model->where($where)->find();
        if ($month_count) {
            $res = $model->where($where)->setInc('money', $data['total_price']);
        } else {
            $data = [
                'uid' => $data['user_id'],
                'money' => $data['total_price'],
                'month' => $month,
                'day' => $day
            ];
            $res = $model->add($data);
        }
        
        if (!$res) {
            setLog('订单月统计失败:'.json_encode($data), 'order_count');
            return false;
        }
        return true;
    }


    //运费模版---获取快递运费信息
    public function get_shipping($condition=array(),$other=array()){
        $shipping_way=M('shipping_way');
        $list = $shipping_way->where($condition)->order('shipping_way asc')->select();
        $is_group = isset($other['is_group'])?$other['is_group']:0;

        foreach( $list as $k => $v ){
            $list_group = [];
            foreach( $list as $k => $v ){
                $v_uid = $v['shipping_way'];
                $list_group[$v_uid][] = $list[$k];
            }
            //-----end 整理添加相应其它表的信息-----
            if( $is_group ){
                $list = $list_group;
            }
            $return_result = array(
                'list'  =>  $list,
            );

            return $return_result;
        }
    }

    //运费模板，运费相关计算
    public function get_shipping_fee($num,$total_money_order,$condition=array(),$product_parameter,$template_id){
        $shipping_reduce_way=C('SHIPPING_REDUCE_WAY');//0总金额是 1是指定产品
        if(empty($condition)){
            $return_result = [
                'code' => 2,
                'msg' => '参数错误',
            ];
            return $return_result;
        }
        
        //判断减免运费的类型
        //判断运费模板是否绑定满减免运费
        $flag=true;
        $template=M('shipping_goods_shipping_template')->find($template_id);
        if($shipping_reduce_way){
            $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>$shipping_reduce_way])->find();
        }else{
            $reduce_info=M('shipping_reduce')->where(['id'=>$template['reduce_id'],'shipping_reduce_way'=>0])->find();
        }

        if($reduce_info['type'] == 1){
            $flag=$reduce_info['need_num']>$num;
        }elseif ($reduce_info['type'] == 2){
            $flag=$reduce_info['need_money']>$total_money_order;
        }elseif ($reduce_info['type'] == 3){
            $flag= ($reduce_info['need_num']>$num)&&($reduce_info['need_money']>$total_money_order);
        }
        if(empty($template['reduce_id']) || $flag){
            $template_info=M('shipping_way')->where($condition)->find();

            $template_info_first_num=$template_info['first_num'];
            $template_info_first_fee=$template_info['first_fee'];
            $template_info_continue_num=$template_info['continue_num'];
            $template_info_continue_fee=$template_info['continue_fee'];
            //总数=数量*产品参数
            $total_num=$num*$product_parameter;
            //运费计算
            if($total_num<=$template_info_first_num){
                $toatl_money_fee=bcadd($template_info_first_fee,0,2);;
                $toatl_money=bcadd($total_money_order,$template_info_first_fee,2);
            }

            if($total_num>$template_info_first_num){
                $num_two=$total_num-$template_info_first_num;
                $continue_money=bcmul(ceil($num_two/$template_info_continue_num),$template_info_continue_fee,2);
                $toatl_money_fee=bcadd($template_info_first_fee,$continue_money,2);
                $toatl_money=bcadd($total_money_order,$toatl_money_fee,2);
            }
        }
       else{
            $toatl_money_fee=0;
            $toatl_money=$toatl_money=bcadd($total_money_order,$toatl_money_fee,2);
        }


        if($toatl_money > 0){
            $return_result = [
                'code' => 1,
                'toatl_money_fee'=>$toatl_money_fee,
                'toatl_money' => $toatl_money,
                'msg' => '获取成功',
            ];
            return $return_result;
        }

    }

}//end Class