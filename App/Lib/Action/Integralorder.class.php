<?php
//订单管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Integralorder {
    
    public $status_name = array(
        1   =>  '待付积分',
        6   =>  '待发货',
        2   =>  '已发货',
        3   =>  '已收货',
    );
    
    private $is_generate_order_count = FALSE;//是否生成订单统计表
    
    private $is_top_supply = TRUE;//是否总部供货

    private $opent_order_limit = FALSE;//是否启用下单限制
    
    private $is_inventory = FALSE;//是否计入总部库存记录
    
    
    /**
     * 架构函数
     */
    public function __construct() {
        
    }
    
    
    
    
    
    //---------------根据订单信息生成的统计-------------------
    
    
    /**
     * 生成订单统计         
     * 2017-4-17 经销商出货数据暂不进行统计
     * @param type $order_info  //多维数组
     * @param type $month
     * @return string|int
     */
    public function generate_order_count($order_info,$month=''){
        
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
        
        
//        $order_obj = M('integralorder');
//        $order_count_obj = M('order_count');
        
        
        $uid = $order_info[0]['user_id'];  //下单经销商ID
        $o_id = $order_info[0]['o_id'];    //接单经销商ID
        $p_id = $order_info[0]['p_id'];    //产品ID
        $status = $order_info[0]['status'];    //订单状态
        $total_price = $order_info[0]['total_price'];  //总价格
        $total_num = $order_info[0]['total_num'];      //总数量
        $error_info = array();//错误信息
        
        if( empty($month) ){
            $month = date('Ym');
        }
        
        if( !is_numeric($month) || strlen($month) != 6 ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '月份不符合格式！',
            );
            return $return_result;
        }
        
        
        
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
                $error_info[] = $the_res_buy;
            }
            
            
            //总部只有出货信息
            $the_all_update_buy_info = array(
                'p_id'       =>  $v_o_p_id,
                'cost_money' =>  $v_o_p_total,
                'cost_num'   =>  $v_o_num,
            );
            
            $the_all_res_buy = $this->update_order_count(0,0,$the_all_update_buy_info);
            
            if( $the_all_res_buy['code'] != 1 ){
                $error_info[] = $the_all_res_buy;
            }
        }
        
        //------------end 针对产品的销量统计-----------
        
        
        //-------------更新出货商总的出货信息---------------
        
//        $update_cost_info = array(
//            'p_id'       =>  0,
//            'cost_money' =>  $total_price,
//            'cost_num'   =>  $total_num,
//        );
//        
//        $res_cost = $this->update_order_count($o_id,$month,$update_cost_info);
        
        
        //总部只有出货信息
        $all_update_buy_info = array(
            'p_id'       =>  0,
            'cost_money' =>  $total_price,
            'cost_num'   =>  $total_num,
        );
        
        $all_res_buy = $this->update_order_count(0,0,$all_update_buy_info);
        
        if( $all_res_buy['code'] != 1 ){
            $error_info[] = $all_res_buy;
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
            $error_info[] = $res_buy;
        }
        
        //-------------end 更新进货商总的进货信息---------------
        
        
        if( !empty($error_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '统计出现错误信息！',
                'info'  =>  $error_info,
            );
            return $return_result;
        }
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '统计成功！',
        );
        return $return_result;
        
        
    }//end func generate_order_count
    
    
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
        
        
        $order_obj = M('integralorder');
        $distributor_obj = M('distributor');
        $order_count = M('integralorder_count');
        
        
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
    
    
    
    
    /**
     * 
     * @param int $uid              //0代表总部
     * @param int $month            //month=0代表所有该统计的所有时间
     * @param array $update_info    //多维数组
     * @param boolen $is_add
     * @return array
     */
    private function update_order_count($uid,$month,$update_info,$is_add=FALSE){
        
        if( $uid === NULL || empty($update_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }
        
        if( empty($month) && $month != 0 ){
            $month = date('Ym');
        }
        
        if( (!is_numeric($month) || strlen($month) != 6) && $month != 0 ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '月份不符合格式！',
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
        
        
        $order_count = M('integralorder_count');
        
        
        //-------------更新进货商的信息---------------
        
        $new_buy_money = isset($update_info['buy_money'])?$update_info['buy_money']:0;
        $new_buy_num = isset($update_info['buy_num'])?$update_info['buy_num']:0;
        $new_cost_money = isset($update_info['cost_money'])?$update_info['cost_money']:0;
        $new_cost_num = isset($update_info['cost_num'])?$update_info['cost_num']:0;
        $p_id = isset($update_info['p_id'])?$update_info['p_id']:0;//产品ID
        
        
        $condition = array(
            'uid'   =>  $uid,
            'month' =>  $month,
            'pid'   =>  $p_id,
        );
        
        $count_info = $order_count->where($condition)->find();
        
        $new_count_info = array(
            'uid'    =>  $uid,
            'month' =>  $month,
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

    
    


    //---------------end 根据订单信息生成的统计-------------------
    
    
    
    //---------------订单的业务逻辑-------------------
    
    
    /**
     * 写入订单
     * @param array $write_info
     * @return array
     */
    public function write_order($uid,$write_info){
        
        $order_num = $write_info['order_num'];
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        $cart_ids = explode('|', $write_info['cart_ids']);
        
        $templet_obj = M('integraltemplet');
        $distributor_obj = M('distributor');
        $order_obj = M('integralorder');
        
        $address = M('address')->where(['user_id' => $uid, 'default' => 1])->find();
        //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
        
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
        
        /**
         * TODO:优化$order_num生成，在后端生成，并确保为唯一值
         */
//        $order_num_len = strlen($order_num);
//        $order_num_sub_len = $order_num_len-2;
//        $order_num = substr($order_num,2,$order_num_sub_len);
        $order_num = rand(0,99).$order_num;
        
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
        
        //是否总部供货
        if( $this->is_top_supply ){
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
                'msg'   =>  '供货商'.$partent_level['name'].'已被系统禁用，请联系您的供货商或总部进行申诉！',
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
        $total_price = 0;//该次下单总金额
        $total_num = 0;//该次下单的总数量
        $total_partent_profit = 0;//该次下单上级的总利润
        $add_order_info = array();//写入订单的部分信息
        $total_integral = 0;//该次下单所需总积分
        
        $templet_key_info = array();//以产品ID为键值的数组
        foreach( $templet_info as $temp_info ){
            $temp_info_id = $temp_info['id'];
            
            $templet_key_info[$temp_info_id] = $temp_info;
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
            $p_price = $templet_key_info[$p_id][$price_key_name];//产品单价
            $p_name = $templet_key_info[$p_id]['name'];//产品名称
            $p_image = $templet_key_info[$p_id]['image'];//产品名称
            $p_integral = $templet_key_info[$p_id]['integral'];//产品积分
            
            
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
            
            $the_total_integral = bcmul($p_integral,$p_num,2);//
            $total_integral = bcadd($total_integral,$the_total_integral,2);//总积分
            $total_num+=$p_num;
            
            $add_order_info[] = array(
                'p_id'  =>  $p_id,//产品ID
                'name'  =>  $p_name,
                'image' => $p_image,
                'num'   =>  $p_num,
                'price' =>  $p_price,
                'integral'  =>  $p_integral,
                'par_price' =>  $p_partent_price,//上级单价
                'par_profit'   =>  $p_price_profit,//上级利润
                'tem_info'  =>  $p_price_all_info,
            );
        }
        
        
        $this_order_info = array(
            'total_num' =>  $total_num,
            'total_money'   =>  $total_price,
        );
        
        $order_limit_result = $this->order_limit($uid,$manager,$this_order_info);
        
        if( $order_limit_result['code'] != 1 ){
            $return_result = array(
                'code'  =>  '-6',
                'msg'   =>  !empty($order_limit_result['msg'])?$order_limit_result['msg']:'订单限制未通过',
            );
            return $return_result;
        }
        
        
        //判断金额
//        if( $total_price <= 0 ){
//            $return_result = array(
//                'code'  =>  '-3',
//                'msg'   =>  '订单总金额不能小于或等于0！',
//                'info'  =>  $this_order_info,
//            );
//            return $return_result;
//        }
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
        //判断积分
        if( $total_integral < 0 ){
            $return_result = array(
                'code'  =>  '-6',
                'msg'   =>  '积分不能少于0！'
            );
            return $return_result;
        }
        
        //--------end 计算订单相关信息------
        
        
        //---------检查是否有足够的金额扣费------
        
//        import('Lib.Action.Funds','App');
//        $Funds = new Funds();
//        $check_money = $Funds->check_recharge_money($uid,$total_price,TRUE);
//        
//        if( !$check_money ){
//            $return_result = array(
//                'code'  =>  '-5',
//                'msg'   =>  '请检查您的余额以及未审核的订单！'
//            );
//            return $return_result;
//        }
        
        //---------end 检查是否有足够的金额扣费----
        
        if( $total_integral > 0 ){
            import('Lib.Action.Integral','App');
            $Integral = new Integral();
            $check_money = $Integral->check_enough_integral($uid,$total_integral,TRUE);

            if( !$check_money ){
                $return_result = array(
                    'code'  =>  '-5',
                    'msg'   =>  '请检查您的积分余额以及未付款的订单！'
                );
                return $return_result;
            }
        }
        //---------检查是否有足够的积分扣除------
        
        
        //---------end 检查是否有足够的积分扣除------
        
        
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
            $the_integral = $the_order_info['integral'];

            $the_p_par_price = isset($the_order_info['par_price'])&&!empty($the_order_info['par_price'])?$the_order_info['par_price']:0;
            $the_p_par_profit = isset($the_order_info['par_profit'])&&!empty($the_order_info['par_profit'])?$the_order_info['par_profit']:0;
            
            
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
                'notes' => '',          //订单备注
                'num' => $the_num,          //产品数量
                'price' => $the_price,      //产品单价
                'par_price' => $the_p_par_price,    //上级的产品单价------2016.11.9新增，用于上级利润记录
                'par_profit' => $the_p_par_profit,  //上级的产品利润------2016.11.9新增，用于上级利润记录
                'total_par_profit' => $total_partent_profit,    //上级的该订单号总利润-----2016.11.9新增，用于上级利润记录
                'time' => time(),           //订单生成日期
//                'month' => date('Ym'),      //订单生成月份
                'total_num' => $total_num,  //总数量----下单时多个产品记录为同一订单号的多条记录
                'total_price' => $total_price,  //总金额-----理由同上
                'integral' =>$the_integral, //单件积分
                'total_integral'    =>  $total_integral,    //积分
                'tallestID' => $tallestID,      //最高负责人
                'paytime'   =>  0,          //支付时间-------2016.11.9近期新增，一般在审核时更新，如没虚拟币模块，可为审核时间
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
            
//            $arr['last_sql']    =   $order_obj->getLastSql();
//            $error_info[]   =   $arr;
            
            if( !$addorder ){
                break;
            }
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
                    'total_price' => $total_price
                ];
                $message->push(trim($openid), $content , $message->order_new);
            }
            
            //如果是购物车就删除id
            if ($cart_ids) {
                M('integralorder_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            }
            return $return_result;
        }
        
        
    }//end func write_order
    
    
    
    
    //删除订单
    public function delorder($order_num) {
        if( empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }
        
        $order_obj = M('integralorder');
        
        $where = array(
            'order_num' =>  $order_num,
        );
        
        $order_info = $order_obj->where($where)->find();
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到该订单！',
            );
            return $return_result;
        }
        
        //只有未审核的订单才能删除
        if( $order_info['status'] != 1 ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '只有未审核的订单才能删除！',
            );
            return $return_result;
        }
        
        
        
        $del_res = $order_obj->where($where)->delete();
        
        if( !$del_res ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '删除失败，请重试！',
            );
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '订单'.$order_num.'删除成功！',
            );
            return $return_result;
        }
        
        
    }//end func delorder
    
    
    
    //经销商审核订单
    public function admin_audit($order_num){
        
        if( empty($order_num) || is_array($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $orderObj = M('integralorder');
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
        $order_total_integral = $order_info[0]['total_integral'];
        $status = $order_info[0]['status'];
        
        //判断
        if( $status != 1 ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '只有未审核订单才能审核！',
                'status'=>  0,
            );
            return $return_result;
        }
        
//        $check_can_charge = $Stock->check_can_charge($order_o_id,$order_info);
//
//        if( $check_can_charge['code'] != 1 ){
//            return $check_can_charge;
//        }
        
        //----------start 扣积分------------
        if( $order_total_integral > 0 ){
            $charge_integral_result = $Integral->charge($order_user_id,$order_total_integral,'8',['note'=>$order_num]);
            
            if( $charge_integral_result['code'] != 1 ){
                return $charge_integral_result;
            }
        }
        //----------end 扣积分------------
        
        
        //----------start 库存点-----------
        $Stock->record_by_order_info($order_user_id,$order_info);
        //----------end 库存点-----------
        
        
        //----------生成订单统计--------------
        $this->generate_order_count($order_info);
        //----------end 生成订单统计--------------
        
        //----------生成返利--------------
        $Rebate->admin_order_audit_rebate($order_user_id,$order_info);
        //----------end 生成返利--------------
        
        
        $order_save_info = array(
            'status'    =>  6,
            'paytime'    =>  time(),
        );
        $rew = $orderObj->where($condition_order)->save($order_save_info);
        
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

        //订单审核模板消息
        if (!$this->is_top_supply) {
            import('Lib.Action.Message','App');
            $message = new Message();
            $openid = $distributorObj->where(['id' => $order_info[0]['user_id']])->getField('openid');
            $message->push(trim($openid), $order_info[0] , $message->order_audit);
        }
        return $return_result;
        
    }//end func admin_audit
    
    
    
    
    //订单确认收货
    public function confirm_order($order_num){
        
        if( empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有订单号',
            );
            return $return_result;
        }
        
        $order_obj = M('integralorder');
        
        
        $condition = array(
            'order_num' =>  $order_num,
        );
        
        $order_info = $order_obj->where($condition)->select();
        $order_user_id = $order_info[0]['user_id'];
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        //-----在经销商确认收货时生成记录-----
        $Rebate->confirm_order_audit_rebate($order_user_id,$order_info);
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
            );
            
            //如果订单由总部审核发货，则进行订单月统计
            if ($this->is_top_supply) {
                $this->month_count($order_info[0]);
            } else {
                //由上级审核发货的模式还需要沟通，待以后开发

            }
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
        
        
        $order_obj = M('integralorder');
        $order_limit_obj = M('order_limit');
        
        
        $level_search = array($dis_level,0);
        $condition_order_lim = array(
            'level' =>  array('in',$level_search),
        );
        
        $order_limit_info = $order_limit_obj->where($condition_order_lim)->find();
        
        if( empty($order_limit_info) ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '没有针对该次订单的限制！',
            );
            
            return $return_result;
        }
        
        //各个限制条件
        //TODO:可增加的限制条件：
        //针对产品的数量及金额（需要考虑多种规则的冲突）
        $is_first = $order_limit_info['is_first'];//首次下单限制
        $total_num_min = $order_limit_info['total_num_min'];//订单产品总数量最小限制
        $total_money_min = $order_limit_info['total_money_min'];//订单总金额最小限制
        
        //该次订单的信息
        $order_total_num = isset($this_order_info['total_num'])?$this_order_info['total_num']:0;
        $order_total_money = isset($this_order_info['total_money'])?$this_order_info['total_money']:0;
        
        
        //--------------限制规则---------------
        
        //如果是首次下单限制
        if( $is_first == 1 ){
            $condition_order = array(
                'user_id'   =>  $uid,
            );
            
            $old_order_info = $order_obj->where($condition_order)->field('order_num,status')->order('time asc')->group('order_num')->find();
            
            $old_order_info_status = $old_order_info['status'];
            
            if( $old_order_info_status == 1 ){
                $return_result = array(
                    'code'  =>  4,
                    'msg'   =>  '首次下单的订单未审核禁止再进行下单！',
                );

                return $return_result;
            }
            else if( !empty($old_order_info) ){
                $return_result = array(
                    'code'  =>  1,
                    'msg'   =>  '已有订单，首次下单规则，不需要再进行订单限制！',
                );

                return $return_result;
            }
        }
        
        
        //订单产品总数量限制最小限制
        if( bccomp($order_total_num,$total_num_min,0) == -1 && $total_num_min != 0 ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '订单产品总数量最小限制（'.$total_num_min.'）未通过！',
            );
            
            return $return_result;
        }
        //订单总金额最小限制
        elseif( bccomp($order_total_money,$total_money_min,2) == -1 && $total_money_min != 0 ){
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '订单总金额最小限制（'.$total_money_min.'）未通过！',
            );
            
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '通过所有限制，正常下单！',
            );

            return $return_result;
        }
        
        //--------------end 限制规则---------------
        
        
        
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
        $model = M('integralorder_month_count');
        $month = get_month();
        
        $where = [
            'uid' => $data['user_id'],
            'month' => $month
        ];
        $month_count = $model->where($where)->find();
        if ($month_count) {
            $res = $model->where($where)->setInc('money', $data['total_price']);
        } else {
            $data = [
                'uid' => $data['user_id'],
                'money' => $data['total_price'],
                'month' => $month,
                'day' => date('Ymd')
            ];
            $res = $model->add($data);
        }
        
        if (!$res) {
            setLog('订单月统计失败:'.json_encode($data), 'order_count');
            return false;
        }
        return true;
    }

}//end Class