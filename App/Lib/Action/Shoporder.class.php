<?php
//商城订单管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Shoporder {

    public $status_name = array(
        1   =>  '待审核',
        2   =>  '已发货',
        3   =>  '已收货',
        6   =>  '待发货',
    );

    private $is_generate_order_count = TRUE;//是否生成订单统计表

    private $is_top_supply = TRUE;//是否总部供货

    private $opent_order_limit = FALSE;//是否启用下单限制

    private $is_inventory = FALSE;//是否计入总部库存记录
    private $order_obj;
    private $templet_obj;
    private $distributor_obj;

    /**
     * 架构函数
     */
    public function __construct() {
        $this->order_obj = M('shop_order');
        $this->distributor_obj = M('distributor');
        $this->templet_obj = M('shop_templet');
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

        
        
//        $order_obj = M('mall_order');
//        $order_count_obj = M('mall_order_count');
        

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


        $order_obj = M('shop_order');
        $distributor_obj = M('distributor');
        $order_count = M('shop_order_count');

        

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

        
        
        $order_count = M('shop_order_count');
        
        

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
        //属性
        $sku_ids[] = $write_info['sku_ids'];

        $templet_obj = M('shop_templet');
        $distributor_obj = M('distributor');
        $order_obj = M('shop_order');
        $write_info['type'] =isset($write_info['type'])?$write_info['type']:0;
        
        import('Lib.Action.Shopsku','App');
        $sku = new Shopsku();
//      foreach ($sku_ids as $key => $id) {
//
//          $sku_info = $sku->get_templet_sku($id);
//          if (!$sku->check_templet_quantity($sku_info, $id, $p_ids[$key], $p_nums[$key])) {
//              $return_result = array(
//                  'code'  =>  -1,
//                  'msg'   =>  '库存不足，请重新下单!',
//              );
//              return $return_result;
//          }
//      }
        
        //属性
        $sku_info = $sku->get_templet_sku_ids($sku_ids);
        $sku_key_info = [];
        if( !empty($sku_info) ){
            foreach( $sku_info as $k_sku => $v_sku ){
                $sku_key_info[$v_sku['id']] = $v_sku;
            }
        }
        
        // 店中店对应的表
        if($write_info['type'] != 'shop'){
            $address = M('shop_address')->where(['user_id' => $uid, 'default' => 1])->find();
        }else{
            $order_obj = M('shop_order');
            $address = [
                'name' => $write_info['s_name'],
                'address_detail' => $write_info['s_address'].$write_info['s_address_detail'],
                'phone' => $write_info['s_phone']
            ];
        }
        
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

        if($write_info['type'] != 'shop'){
            $o_id = $manager['pid'];
        }else{
            $o_id = $uid;
        }
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
//        $price_key_name = "price" . $manager['level'];//该用户相应等级的产品单价key名
        $price_key_name = "price";//该用户相应等级的产品单价key名

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

//      $shipping_num = C('SHOP_IN_SHOP_NUM');
//      $shipping_fee = C('SHOP_IN_SHOP_SHIPPING');
        
        
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
            //属性
            $p_sku_id = $sku_ids[$p_key];//产品属性ID
            $p_sku_info = isset($sku_key_info[$p_sku_id])?$sku_key_info[$p_sku_id]:[];//产品属性信息

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
                //属性
                'sku_id'=>  $p_sku_id,
                'name'  =>  $p_name,
                'image' => $p_image,
                'num'   =>  $p_num,
                'price' =>  $p_price,
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

        import('Lib.Action.Funds','App');
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


        //这里已经废弃，因为在审核订单时进行扣费

        //----------扣费逻辑------------

//        $charge_money_result = $this->charge_money($uid,$total_price,'mall_order',$order_num);
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
       
        if($write_info['type'] != 'shop'){
            $ad_name = $address['name'];
            $ad_addre = $address['province'].$address['city'].$address['area'].$address['address'];
            $ad_phone = $address['phone'];
        }else{
            $ad_name = $address['name'];
            $ad_addre = $address['address_detail'];
            $ad_phone = $address['phone'];
            $o_id = $uid;
            $uid = 0;
        }
        
        foreach( $add_order_info as $the_order_info ){
            $the_p_name = $the_order_info['name'];
            $the_p_image = $the_order_info['image'];
            $the_p_id = $the_order_info['p_id'];
            $the_num = $the_order_info['num'];
            $the_price = $the_order_info['price'];
            
            //属性
            $the_sku_id = $the_order_info['sku_id'];
            
            //属性
            //记录下单商品的属性值
            $properties = "";
            $style = "";
            if ($the_sku_id) {
                $properties = $sku->get_templet_property_com($the_sku_id);
                $style = $sku->get_value($properties);
            }

            $the_p_par_price = isset($the_order_info['par_price'])&&!empty($the_order_info['par_price'])?$the_order_info['par_price']:0;
            $the_p_par_profit = isset($the_order_info['par_profit'])&&!empty($the_order_info['par_profit'])?$the_order_info['par_profit']:0;
//          if($total_num<$shipping_num){
//              $total_price += $shipping_fee;
//          }
            
            $arr = array(
                'order_num' => $order_num,  //订单号
                'user_id' => $uid,          //下单用户
                'o_id' => $o_id,            //接单供货商
                'p_id' => $the_p_id,        //产品ID 
                'p_name' => $the_p_name,    //产品名字------2016.11.9新增，ID应该废弃
                'p_image' => $the_p_image,
                'status' => 1,              //订单状态，默认1为未审核
                's_name' => $ad_name,        //收货人名字
                's_addre' => $ad_addre,      //收货人地址
                's_phone' => $ad_phone,      //收货人手机
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
                'tallestID' => $tallestID,      //最高负责人
                //属性
                'sku_id' => $the_sku_id,    //商品属性ID
                'properties' => $properties,
                'style' => $style,
                'paytime'   =>  0,          //支付时间-------2016.11.9近期新增，一般在审核时更新，如没虚拟币模块，可为审核时间
                'month' => date('Ym')
            );
            
//          var_dump($arr);die;
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
                M('shop_order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            }
            return $return_result;
        }


    }//end func write_order




    //删除订单
    public function delorder($order_num,$type="") {
        if( empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }

        if($type == "shop"){
          $order_obj = M('shop_order');
        }else{
          $order_obj = M('shop_order');
        }
        

        $where = array(
            'status' => 1,
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
            //取消订单模板消息
            import('Lib.Action.Message', 'App');
            $message = new Message();
            $openid = M('distributor')->where(['id' => $order_info['o_id']])->getField('openid');
            $message->push(trim($openid), $order_info, $message->order_cancle);


            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '订单'.$order_num.'删除成功！',
            );
            return $return_result;
        }


    }//end func delorder




    //后台审核订单
    public function radmin_audit($order_nums){

        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        
        
        $order_obj = M('shop_order');
        $templet_obj = M('shop_templet');
        

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

        $templet_info = array();
        $add_order_info   =   array();

        foreach ($order_nums as $order_num) {

            $uid = $order_info_key[$order_num]['user_id'];
            $o_id = $order_info_key[$order_num]['o_id'];
            $total_price = $order_info_key[$order_num]['total_price'];
            $status = $order_info_key[$order_num]['status'];
//            $order_month = $order_info_key[$order_num]['month'];


            //判断
            if( $status != 1 ){
                $is_status_error = TRUE;
                break;
            }



            //----------扣费逻辑------------
            $charge_money_result = $Funds->charge_money($uid,$total_price,'Shoporder',$order_num);

            if( $charge_money_result['code'] != 1 ){
                $charge_money_result['status']  =   0;
                $is_charge_money_break = TRUE;
                break;
            }
            //----------end 扣费逻辑------------

            //----------订单返还-----------

            if( $Funds->is_order_return ){
                foreach( $order_info_key2[$order_num] as $o_k => $o_v ){
                    $p_id = $o_v['p_id'];
                    $p_num = $o_v['num'];
                    $p_price = $o_v['price'];

                    if( !isset($templet_info[$p_id]) ){
                        $templet_info[$p_id] = $templet_obj->where(array('id'=>$p_id))->find();
                    }

                    $add_order_info[$order_num][] = array(
                        'p_id'  =>  $p_id,//产品ID
                        'num'   =>  $p_num,
                        'price' =>  $p_price,
                        'tem_info'  =>  $templet_info[$p_id],
                    );
                }

                $monery_order_return_result = $Funds->monery_order_return($uid,$order_num,$add_order_info[$order_num]);

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

            
            
            
            //订单审核模板消息
//            import('Lib.Action.Message','App');
//            $message = new Message();
//            $openid = M('distributor')->where(['id' => $order_info_key[$order_num]['user_id']])->getField('openid');
//            $message->push(trim($openid), $order_info_key[$order_num] , $message->order_audit);
            
            

            //----------生成返利--------------
//            $Rebate->radmin_order_audit_rebate($uid,$order_info_key2[$order_num]);
            //----------end 生成返利--------------


            //----------生成订单统计--------------
            $this->generate_order_count($order_info_key2[$order_num]);
            //----------end 生成订单统计--------------


            //----------更改总部库存记录--------------
            if( $o_id == 0 ){
//                $this->update_inventory($o_id,$order_info_key2[$order_num]);
            }
            //----------end 更改总部库存记录--------------

            //----------积分触发----------------------
//            $Integral->aduit_order($uid,$order_info_key2[$order_num]);
            //----------end 积分触发----------------------

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

        
        $orderObj = M('shop_order');
        $distributorObj = M('distributor');
        $templet_obj = M('shop_templet');
        

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

        
        

        //----------生成订单统计--------------
        $this->generate_order_count($order_info);
        //----------end 生成订单统计--------------

        //----------生成返利--------------
//        $Rebate->admin_order_audit_rebate($order_user_id,$order_info);
        //----------end 生成返利--------------

        //----------积分触发----------------------
//        $Integral->aduit_order($order_user_id,$order_info);
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

        
        $order_obj = M('shop_order');
        
        

        $condition = array(
            'order_num' =>  $order_num,
        );

        $order_info = $order_obj->where($condition)->select();
        $order_user_id = $order_info[0]['user_id'];

        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();

        //-----在经销商确认收货时生成记录-----
//        $Rebate->confirm_order_audit_rebate($order_user_id,$order_info);
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

        
        
        $order_obj = M('shop_order');
        $order_limit_obj = M('shop_order_limit');
        

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
        $model = M('shop_order_month_count');
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
            setLog('订单月统计失败:'.json_encode($data), 'shop_order_count');
            return false;
        }
        return true;
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
        
        
        $level_num = C('LEVEL_NAME');
        $status_name = $this->status_name;
        
        $is_group = isset($other['is_group'])?$other['is_group']:0;
        $order_type = isset($other['type'])?$other['type']:0;
        
        if($order_type == 'shop'){
            $this->order_obj = M('shop_order');
        }
        
        $count = $this->order_obj->where($condition)->count('distinct order_num');

        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                $list = $this->order_obj->where($condition)->order('time desc')->page($page_con)->select();

            }
            else{
                $list = $this->order_obj->where($condition)->order('time desc')->select();
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
            
            $list_group = [];
            
            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];
                $v_order_num = $v['order_num'];
                $v_u_level = $v['u_level'];
                $v_p_level = $v['p_level'];
                $v_updated = $v['updated'];
                $v_status = $v['status'];


//                $list[$k]['u_info'] = $dis_key_info[$v_uid];
//                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
//              $list[$k]['p_name'] = $dis_key_info[$v_pid]['name'];
                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];

                $list[$k]['status_name'] = $status_name[$v_status];
                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['p_levname'] = $dis_key_info[$v_pid]['levname'];
                $list[$k]['templet'] = $templet_key_info[$v_pid];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);
                if(isset($templet_key_info[$v_pid]['bind_pid'])){
                  $list[$k]['bind_pid'] = $templet_key_info[$v_pid]['bind_pid'];
                }
                
                $list_group[$v_order_num][] = $list[$k];
            }
            //-----end 整理添加相应其它表的信息-----
            
            
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
    
    //支付回调
    public function pay_callback_handle($result) {
        $where = [
            'order_num' => $result['out_trade_no'],
            'status' => 1
        ];
        $order = $this->order_obj->where($where)->select();
        if ($order) {
            $data = [
                'status' => 2,
                'trade_num' => $result['transaction_id']
            ];
            $this->order_obj->where($where)->save($data);

            //------------触发返利开始
            import('Lib.Action.Rebate', 'App');
            $Rebate = new Rebate();
            $Rebate->pay_order_success_rebate($order[0]
            ['user_id'],$order[0]);
            //-----------触发返利结束
        }
    }

    //获取下单返现记录
    public function get_refund($page_info=array(),$condition=array()){
        $mall_money_log = M('shop_money_log');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');

        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];

        $count = $mall_money_log->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $mall_money_log->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $mall_money_log->where($condition)->order('id desc')->select();
            }

            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_x_id = $v['x_id'];
                if( !isset($uids[$v_x_id]) ){
                    $uids[$v_x_id] = $v_x_id;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );

            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_x_id = $v_dis['id'];
                $dis_key_info[$v_dis_x_id] = $v_dis;
            }

            $dis_key_info['0']['name'] = '总部';
            foreach( $list as $k => $v ){
                $v_x_id = $v['x_id'];
                $list[$k]['x_id_info'] = $dis_key_info[$v_x_id];
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
    }
    //end get_refund

}//end Class