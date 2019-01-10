<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of S
 *
 * @author Administrator
 */
class Shipping {

    private $shipping_reduce_way;
    private $shipping_goods_shipping_template_model;
    private $shipping_reduce;
    /**
     * 架构函数
     */
    public function __construct() {

        $this->shipping_reduce_way = C('SHIPPING_REDUCE_WAY');//0是总金额 1是指定产品满减运费
        $this->shipping_goods_shipping_template_model=M('shipping_goods_shipping_template');
        $this->shipping_reduce=M('shipping_reduce');
    }

    public function count_shipping($carts,$province) {
        //排除没有绑定运费模板的产品
        foreach ($carts as $cart) {
            if ($cart['product']['template_id']) {
                $shipping_ids[] = $cart['product']['template_id'];
            }
        }
        if (empty($shipping_ids)) {
            return [
                'shipping_fee' => 0,
                'shipping_ids' => [],
            ];
        }

        $shipping_ids=array_unique($shipping_ids);

        //如果是按单一商品来算运费减免
        if($this->shipping_reduce_way){
            $all_reduce_ids[]='';
            //如果有满减运费的商品
            foreach ($shipping_ids as $v=>$k){
                $shipping_goods_shipping_template_info=$this->shipping_goods_shipping_template_model->where(['id'=>$k])->find();
                //得出有运费减免的运费模板的id
                if(!empty($shipping_goods_shipping_template_info['reduce_id'])){
                    $shipping_reduce_info=$this->shipping_reduce->where(['id'=>$shipping_goods_shipping_template_info['reduce_id'],'shipping_reduce_way'=>$this->shipping_reduce_way])->find();
                    //找出符合条件的减免信息
                    if($shipping_reduce_info){
                        $reduce_ids[$k]=$shipping_reduce_info;
                        $all_reduce_ids[]=$shipping_goods_shipping_template_info['id'];
                    }
                }
            }

//            //先将同一产品的不同属性的商品临时归到同一产品里面
//            $item=array();
//            foreach($carts as $k=>$v){
//                if(!isset($item[$v['product']['template_id']])){
//                    $item[$v['product']['template_id']]=$v;
//                }else{
//                    $item[$v['product']['template_id']]['num']+=$v['num'];
//                }
//            }

//            //将相同运费模板的数量和金额（数量*参数）相加
            $same_product_parameter=0;//相同运费模板的产品数量*产品参数的总值
            $same_product_parameter_money=0;//相同运费模板的产品数量*产品金额的总金额
            foreach ($carts as $k=>$cart) {
                if (in_array($cart['product']['template_id'], $all_reduce_ids)) {
                    //统计参数
                    //如果满减是精确按重量、体积、件数计算，则将注释掉的参数计算开启即可
//                    $product_parametered =$cart['num']*$cart['product']['product_parameter'];
                    $product_parametered =$cart['num'];
                    $product_parametered_money =$cart['num']*$cart['price'];
                    $same_product_parameter += $product_parametered;
                    $same_product_parameter_money +=$product_parametered_money;
                }
                //用一个字段，用于临时储存相同运费模板的不同产品的数量
                $carts[$k]['product_parameter_num']=$same_product_parameter;
                $carts[$k]['product_parameter_money']=$same_product_parameter_money;
            }

            //去除符合条件的id
            foreach ($carts as $cart){
                foreach ($reduce_ids as $k=>$v){
                    if($cart['product']['template_id'] == $k){
                        if($cart['product_parameter_num']>=$v['need_num']){
                            if(($cart['product_parameter_money'])>=$v['need_money']){
                                $unset_id=array_search($cart['product']['template_id'],$shipping_ids);
                                unset($shipping_ids[$unset_id]);
                            }
                        }
                    }
                }

            }

            if (empty($shipping_ids)) {
                return [
                    'shipping_fee' => 0,
                    'shipping_ids' => [],
                ];
            }
            array_unique($shipping_ids);
        }


        $shipping = M('shipping_goods_shipping_template')->where(['id'=>['in', $shipping_ids]])->select();
        $shipping_ids = [];
        foreach ($shipping as $v) {
            $shipping_ids[] = $v['id'];
        }

        $count = count($shipping_ids);

        if ($count == 0) {
            return [
                'shipping_fee' => 0,
                'shipping_ids' => [],
            ];
        }
        $shipping_way_model = M('shipping_way');
        if(empty($province)){
            $address = M('address')->where(['user_id' =>$carts[0]['uid'], 'default' => 1])->find();
            if (!$address) {
                return [
                    'shipping_fee' => 0,
                    'shipping_ids' => [],
                ];
            }
            //都是同一运费模板
            $province = $address['province'];
        }

        if ($count == 1){
            $where =[
                'template_id' => $shipping_ids[0],
                'area_name' => ['like', "%$province%"]
            ];
            $shipping_way = $shipping_way_model->where($where)->find();
            if (!$shipping_way) {
                //没有匹配到地区则按全国运费计算
                $shipping_way = $shipping_way_model->where(['template_id' => $shipping_ids[0],'area_id'=>1])->find();
            }
            return $this->depth_count($carts, $shipping_ids, $shipping_way);

        } else if ($count > 1){
            //不同商品不同运费模板计算
            $continue_fee = 0;//续件总运费
            $max_first_fee = 0;//最大首件运费
            foreach ($carts as $cart) {
                if (in_array($cart['product']['template_id'], $shipping_ids)) {
                    $where =[
                        'template_id' => $cart['product']['template_id'],
                        'area_name' => ['like', "%$province%"]
                    ];
                    $shipping_way = $shipping_way_model->where($where)->find();

                    if (!$shipping_way) {
                        //没有匹配到地区则按全国运费计算
                        $shipping_way = $shipping_way_model->where(['template_id' => $cart['product']['template_id'],'area_id'=>1])->find();
                    }
                    if (!$shipping_way) {
                        continue;
                    }
                    //得到最大首件运费
                    if ($shipping_way['first_fee'] > $max_first_fee) {
                        $max_first_fee = $shipping_way['first_fee'];
                    }
                    //得到续件总运费
                    $first_num = $cart['num']*$cart['product']['product_parameter'] - $shipping_way['first_num'];
                    if ($first_num > 0) {
                        $continue_num = ceil($first_num/$shipping_way['continue_num']);
                        $continue_fee += $continue_num * $shipping_way['continue_fee'];
                    }
                }
            }
            return [
                'shipping_fee' => $max_first_fee + $continue_fee,
                'shipping_ids' => $shipping_ids,
            ];
        }
    }

    //不同商品相同运费模板深入计算
    private function depth_count($carts, $shipping_ids, $shipping_way) {
        $num = 0;
        $product_parameter=0;
        if (!$shipping_way) {
            return [
                'shipping_fee' => 0,
                'shipping_ids' => [],
            ];
        }
        //计算产品数量
        foreach ($carts as $cart) {
            if (in_array($cart['product']['template_id'], $shipping_ids)) {
                $num += $cart['num'];
                //统计参数
                $product_parametered =$cart['num']*$cart['product']['product_parameter'];
                $product_parameter += $product_parametered;
            }
        }

        //计算运费
        $first_num = ceil($product_parameter) - $shipping_way['first_num'];
        if ($first_num <= 0) {
            return [
                'shipping_fee' => $shipping_way['first_fee'],
                'shipping_ids' => $shipping_ids,
            ];
        } else {
            $continue_num = ceil($first_num/$shipping_way['continue_num']);
            return [
                'shipping_fee' => $shipping_way['first_fee'] + $continue_num * $shipping_way['continue_fee'],
                'shipping_ids' => $shipping_ids,
            ];
        }
    }
}
