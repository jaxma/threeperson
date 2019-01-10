<?php
//后台管理员的模块化代码
header("Content-Type: text/html; charset=utf-8");

//提货单
class Waybill {
    
    
    public $status_name = array(
        1   =>  '待审核',
        2   =>  '已发货',
        3   =>  '已收货',
        6   =>  '待发货',
    );
    
    private $is_top_supply = TRUE;//是否总部供货
    
    
    
    /**
     * 架构函数
     */
    public function __construct() {
        
    }
    
    
    /**
     * 写入订单
     * @param array $write_info
     * @return array
     */
    public function write_order($uid,$write_info){
        
        $order_num = $write_info['order_num'];
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        $s_name = $write_info['user_name'];
        $s_addre = $write_info['addre'];
        $s_phone = $write_info['phone'];
        $notes = $write_info['textarea'];
        
        
        
        $templet_obj = M('templet');
        $distributor_obj = M('distributor');
        $order_obj = M('waybill');
        
        
        //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
        
        //参数判断
        if( empty($order_num) || empty($p_ids) || empty($p_nums) || !is_array($p_ids) 
                || !is_array($p_nums) || empty($s_name) || empty($s_addre) || empty($s_phone) ){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '请确认您是否已选择商品，并填写完整的收货信息！',
//                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }
        
        //检查是否还有未审核的提货单
        $condition_order_not = array(
            'user_id'   =>  $uid,
            'status'    =>  1,
        );
        
        $order_not_info = $order_obj->where($condition_order_not)->field('order_num')->find();
        
        if( !empty($order_not_info) ){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '还有提货单未被审核，无法下单！',
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
            
            $add_order_info[] = array(
                'p_id'  =>  $p_id,//产品ID
                'name'  =>  $p_name,
                'num'   =>  $p_num,
                'price' =>  $p_price,
                'par_price' =>  $p_partent_price,//上级单价
                'par_profit'   =>  $p_price_profit,//上级利润
                'tem_info'  =>  $p_price_all_info,
            );
        }
        
        
        //判断库存点是否足够
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        $check_can_charge_result = $Stock->check_can_charge($uid,$add_order_info);
        
        if( $check_can_charge_result['code'] != 1 ){
            return $check_can_charge_result;
        }
        
        
        //判断金额
        if( $total_price <= 0 ){
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
//        
        //---------end 检查是否有足够的金额扣费----
        
        
        
        
        //----------生成订单------------
        $error_info = array();
        foreach( $add_order_info as $the_order_info ){
            $the_p_name = $the_order_info['name'];
            $the_p_id = $the_order_info['p_id'];
            $the_num = $the_order_info['num'];
            $the_price = $the_order_info['price'];
            
            $the_p_par_price = isset($the_order_info['par_price'])&&!empty($the_order_info['par_price'])?$the_order_info['par_price']:0;
            $the_p_par_profit = isset($the_order_info['par_profit'])&&!empty($the_order_info['par_profit'])?$the_order_info['par_profit']:0;
            
            
            $arr = array(
                'order_num' => $order_num,  //订单号
                'user_id' => $uid,          //下单用户
                'o_id' => $o_id,            //接单供货商
                'p_id' => $the_p_id,        //产品ID
                'p_name' => $the_p_name,    //产品名字------2016.11.9新增，ID应该废弃
                'status' => 1,              //订单状态，默认1为未审核
                's_name' => $s_name,        //收货人名字
                's_addre' => $s_addre,      //收货人地址
                's_phone' => $s_phone,      //收货人手机
                'notes' => $notes,          //订单备注
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
            );
            
            
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
        
        $order_obj = M('waybill');
        
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
        
        
    }//end func delordersa
    
    
    //后台审核订单
    public function radmin_audit($order_nums){
        
        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        $order_obj = M('Waybill');
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
        $is_can_not_charge  =  FALSE;
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
        $templet_info = array();
        $add_order_info   =   array();
        $all_stock_result = array();
        
        foreach ($order_nums as $order_num) {
            
            $uid = $order_info_key[$order_num]['user_id'];
            $total_price = $order_info_key[$order_num]['total_price'];
            $status = $order_info_key[$order_num]['status'];
//            $order_month = $order_info_key[$order_num]['month'];
            
            
            //判断
            if( $status != 1 ){
                $is_status_error = TRUE;
                break;
            }
            
            //判断库存点是否足够
            $check_can_charge_result = $Stock->check_can_charge($uid,$order_info_key2[$v_order_num]);
            if( $check_can_charge_result['code'] != 1 ){
                $is_can_not_charge = TRUE;
                break;
            }
            
            
            //----------start 库存点-----------
            $stock_result = $Stock->record_by_order_info($uid,$order_info_key2[$v_order_num],2);
            
            $all_stock_result[] = $stock_result;
            //----------end 库存点-----------
            

            $order_save_info = array(
                'status'    =>  2,
                'paytime'    =>  time(),
            );
            
            $order_obj->where(array('order_num' => $order_num))->save($order_save_info);//6为已审核未配送状态
            
            
        }//end froeach
        
//        return $all_stock_result;
        
        
        //库存不足以扣除
        if( $is_can_not_charge ){
            return $check_can_charge_result;
        }
        
        
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
            $order_num_str = implode(',', $order_num);
            
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
        
        $orderObj = M('Waybill');
        $distributorObj = M('distributor');
        $templet_obj = M('templet');
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        import('Lib.Action.Stock','App');
        $Stock = new Stock();
        
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
        $order_total_price = $order_info[0]['total_price'];
        
        
        //判断库存点是否足够
        $check_can_charge_result = $Stock->check_can_charge($order_user_id,$order_info);
        if( $check_can_charge_result['code'] != 1 ){
            return $check_can_charge_result;
        }
        
        
        //----------start 库存点-----------
        $Stock->record_by_order_info($order_user_id,$order_info,2);
        //----------end 库存点-----------
        
        
        
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
        
        $order_obj = M('Waybill');
        
        
        $condition = array(
            'order_num' =>  $order_num,
        );
        
        $order_info = $order_obj->where($condition)->find();
        $order_user_id = $order_info['user_id'];
        
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
            return $return_result;
        }
    }//end func confirm_order
    
    
    
    
    
}