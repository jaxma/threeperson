<?php
//资金管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Mallfunds {
    
    public $is_charge_money = TRUE;//是否进行虚拟币系统的逻辑，如扣费，下单金额判断
    
    public $is_all_can_refund = TRUE;//是否充值金额都可以提现，即可提现金额等于充值金额
    
    public $is_get_min_apply_money = FALSE;//是否使用获取最低申请金额
    
    public $is_get_min_refund_money = FALSE;//是否使用获取最低提现金额
    
    public $is_parent_order = FALSE;//订单扣费时，扣费金额充回订单供货商时为TRUE，直接扣费不做充值操作为FALSE（针对非总部订单）
    
    public $is_parent_audit = FALSE;//是否由直属上级审核虚拟币，是则由上级审核下级充值申请，并上级相应余额转到下级。否则总部审核充值
    
    public $is_order_return = FALSE;//是否启用订单返还,注意，开启后经销商及总部审核时都会触发
    public $order_return_rank = 100;//订单返还循环的次数
    
    public $recharge_type_name = array(
        '1' =>  '申请充值',
        '2' =>  '后台充值',
        '3' =>  '订单返现',
        '4' =>  '下级下单',
        '5' =>  '返利返现',
    );
    
    public $charge_type_name = array(
        '1' =>  '订单扣费',
        '2' =>  '提现扣费',
        '3' =>  '下级转账',
        '4' =>  '支付返利',
        '5' =>  '库存扣费',
    );
    
    /**
     * 架构函数
     */
    public function __construct() {
        
    }
    
    
    //-------------获取信息-----------------
    
    
    //获取充值记录
    public function get_money_recharge_log($page_info=array(),$condition=array()){
        $money_recharge_log = M('mall_money_recharge_log');
        $distributor_obj = M('distributor');


        $type_name = $this->recharge_type_name;
        $note_name = array(
            '1' =>  '申请ID：',
//            '2' =>  '',
            '3' =>  '订单号：',
        );

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        $count = $money_recharge_log->where($condition)->count();
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $money_recharge_log->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $money_recharge_log->where($condition)->order('id desc')->select();
            }



            //-----整理添加相应其它表的信息-----
            $uids = array();
            $applys = array();
            $order_notes = array();

            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_source_id = $v['source_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_source_id]) ){
                    $uids[$v_source_id] = $v_source_id;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_source_id = $v['source_id'];
                $v_type = $v['type'];
                $v_note = $v['note'];
                $v_created = $v['created'];


                $list[$k]['note_name']  = $note_name[$v_type].$v_note;
                $list[$k]['type_name']  = $type_name[$v_type];
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
//                $list[$k]['source_info'] = $dis_key_info[$v_source_id];
                $list[$k]['dis_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['dis_phone'] = $dis_key_info[$v_uid]['phone'];
                $list[$k]['dis_wechatnum'] = $dis_key_info[$v_uid]['wechatnum'];
                $list[$k]['dis_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['source_name'] = $dis_key_info[$v_source_id]['name'];
                $list[$k]['created_format'] = date('Y-m-d H:i',$v_created);
                $list[$k]['cn_week_day'] = $this->cn_week_day(date('N',$v_created));
                $list[$k]['created_day'] = date('m-d',$v_created);

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
    }//end func get_money_recharge_log



    //获取扣费记录
    public function get_money_charge_log($page_info=array(),$condition=array()){

        $money_charge_log_obj = M('mall_money_charge_log');
        $distributor_obj = M('distributor');


        $type_name = $this->charge_type_name;


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];

        $count = $money_charge_log_obj->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $money_charge_log_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $money_charge_log_obj->where($condition)->order('id desc')->select();
            }


            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_type = $v['type'];
                $v_created = $v['created'];
                $v_order_num = $v['order_num'];

                $v_order_num_format = $v_order_num;
                if( $v_type == 1 ){
                    $v_order_num_format = '订单号：'.$v_order_num;
                }
                elseif( $v_type == 3 ){
                    $v_order_num_format = '<a href="__URL__/mall_money_apply?id='.$v_order_num.'">申请ID：'.$v_order_num.'</a>';
                }


                $list[$k]['type_name']  = $type_name[$v_type];
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['dis_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['dis_phone'] = $dis_key_info[$v_uid]['phone'];
                $list[$k]['dis_wechatnum'] = $dis_key_info[$v_uid]['wechatnum'];
                $list[$k]['dis_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['created_format'] = date('Y-m-d H:i',$v_created);
                $list[$k]['order_num_format'] = $v_order_num_format;
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
    }//end func get_money_charge_log


    //获取提现信息
    public function get_money_refund($page_info=array(),$condition=array()){

        $money_refund_obj = M('mall_money_refund');
        $distributor_obj = M('distributor');


        $type_name = array(
            '1' =>  '总部提现',
            '2' =>  '申请提现',
        );


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];

        $count = $money_refund_obj->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $money_refund_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $money_refund_obj->where($condition)->order('id desc')->select();
            }



            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_type = $v['type'];
                $v_created = $v['created'];

                $list[$k]['type_name']  = $type_name[$v_type];
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['dis_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['dis_phone'] = $dis_key_info[$v_uid]['phone'];
                $list[$k]['dis_wechatnum'] = $dis_key_info[$v_uid]['wechatnum'];
                $list[$k]['dis_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['created_format'] = date('Y-m-d H:i',$v_created);
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
    }//end func get_money_refund


    //代理充值申请记录
    public function get_money_apply($page_info=array(),$condition=array()){

        $money_apply_obj = M('mall_money_apply');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');


        $status_name = array(
            '0' =>  '未审核',
            '1' =>  '已审核',
            '2' =>  '不通过',
        );

        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $money_apply_obj->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $money_apply_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $money_apply_obj->where($condition)->order('id desc')->select();
            }

            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_audit_id]) ){
                    $uids[$v_audit_id] = $v_audit_id;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }

            $dis_key_info['0']['name'] = '总部';
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                $v_status = $v['status'];

                $list[$k]['status_name'] = $status_name[$v_status];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['audit_info'] = $dis_key_info[$v_audit_id];
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
    }//end func get_money_apply





    //代理提现申请记录
    public function get_money_refund_apply($page_info=array(),$condition=array()){

        $money_refund_apply_obj = M('mall_money_refund_apply');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');


        $status_name = array(
            '0' =>  '未审核',
            '1' =>  '已审核',
            '2' =>  '不通过',
        );

        $refund_type_name=array(
            '0'=>'转账到指定账号',
            '1'=>'提现到微信钱包',
        );

        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $money_refund_apply_obj->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $money_refund_apply_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $money_refund_apply_obj->where($condition)->order('id desc')->select();
            }


            //-----整理添加相应其它表的信息-----
            $uids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_audit_id]) ){
                    $uids[$v_audit_id] = $v_audit_id;
                }
            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();

            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){

                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }

            $dis_key_info['0']['name'] = '总部';

            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_audit_id = $v['audit_id'];
                $v_status = $v['status'];
                $v_refund_type=$v['refund_type'];
                $list[$k]['status_name'] = $status_name[$v_status];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['audit_info'] = $dis_key_info[$v_audit_id];
                $list[$k]['refund_type_name'] = $refund_type_name[$v_refund_type];
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
    }//end func get_money_refund_apply





    /**
     * 获取用户资金表，如果没有则创建
     * @param int $uid
     * @return boolean|array
     */
    public function get_user_funds_info($uid){

        if( empty($uid) ){
            return FALSE;
        }


        $money_funds_obj = M('mall_money_funds');


        $condition_funds = array(
            'uid'   =>  $uid,
        );

        $funds_info = $money_funds_obj->where($condition_funds)->find();

        if( empty($funds_info) ){
            $data = array(
                'uid'   =>  $uid,
                'created'   =>  time(),
            );

            $add_funds = $money_funds_obj->data($data)->add();

            if( !$add_funds ){
                return FALSE;
            }

            $funds_info = $money_funds_obj->where($condition_funds)->find();

            if( empty($funds_info) ){
                return FALSE;
            }
        }


        return $funds_info;
    }//end func get_user_funds_info




    /**
     * 获取最低申请金额
     *
     *
     * @return decimal $apply_money
     */
    public function get_min_apply_money($uid){

        $apply_money = 0;

        if( !$this->is_get_min_apply_money ){
            return $apply_money;
        }

        if( empty($uid) ){
            return $apply_money;
        }


        $money_min_refund = M('mall_money_min_refund');
        $distributor_obj = M('distributor');

        $dis_info = $distributor_obj->where(array('id'=>$uid))->find();

        $level = $dis_info['level'];

        $set_info = $money_min_refund->where(array('id'=>'1'))->find();

        $min_apply_key = 'level'.$level;
        $apply_money = isset($set_info[$min_apply_key])?$set_info[$min_apply_key]:0;

        if( $apply_money < 0 ){
            $apply_money = 0;
        }


        return $apply_money;
    }



    /**
     * 获取可提现金额
     *
     * @param int $level
     * @param decimal $recharge_money
     * @return decimal
     */
    public function get_user_can_refund_money($uid){

        $can_refund_money = 0;


        if( empty($uid) ){
            return $can_refund_money;
        }


        $distributor_obj = M('Distributor');
        $money_funds_obj = M('mall_money_funds');


//        $distributor_info = $distributor_obj->where(array('id' => $uid))->find();
//        if( empty($distributor_info) ){
//            return 0;
//        }
//        $level = $distributor_info['level'];


        //查看该代理的资金表
        $field = 'total_money,no_refund_money';
        $money_funds = $money_funds_obj->where(array('uid'=>$uid))->field($field)->find();

        if( empty($money_funds) ){
            return $can_refund_money;
        }


        $recharge_money = $money_funds['total_money'];
        $can_refund_money = $money_funds['no_refund_money'];


//        $can_refund_money = $this->get_can_refund_money($level,$recharge_money);


        return $can_refund_money;
    }//end func get_user_can_refund_money


    /**
     * 获取可提现金额
     *
     * @param int $level
     * @param decimal $recharge_money
     * @return decimal
     */
    public function get_can_refund_money($level,$recharge_money){

        if( !$this->is_get_min_refund_money ){
            return $recharge_money;
        }


        if( $recharge_money == 0 || $level == 0 || is_null($level) ){
            return 0;
        }

        $money_min_refund = M('mall_money_min_refund');
        $set_info = $money_min_refund->where(array('id'=>'1'))->find();

        $min_refund_key = 'level'.$level;
        $min_refund = isset($set_info[$min_refund_key])?$set_info[$min_refund_key]:0;


        $refund_money = bcsub($recharge_money,$min_refund,2);

        if( $refund_money < 0 ){
            $refund_money = 0;
        }


        return $refund_money;
    }


    /**
     * 检查用户是否有足够金额扣费
     * @param int $uid
     * @param decimal $money
     * @param bool $check_order 是否计算未审核订单
     * @return boolean
     */
    public function check_recharge_money($uid,$money,$check_order=FALSE){

        //判断该系统是否使用虚拟币系统
        if( !$this->is_charge_money ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '无需扣费！',
            );
            return $return_restult;
        }


        if( empty($uid) || $money<=0 ){
            return FALSE;
        }

        $money_funds_obj = M('mall_money_funds');
//        $distributor_obj = M('distributor');


        $condition_funds = array(
            'uid'   =>  $uid,
        );
        $funds_info = $money_funds_obj->where($condition_funds)->find();

        //该用户没有资金信息则没有充值过
        if( empty($funds_info) ){
            return FALSE;
        }

        $recharge_money = $funds_info['no_refund_money'];//当前可用金额


        //减去未审核的订单才是真实可用金额
        if( $check_order ){
            $order_obj = M('mall_order');

            $condition_order = array(
                'user_id'   =>  $uid,
                'status'    =>  1,
            );

            $order_info = $order_obj->where($condition_order)->group('order_num')->select();

            $order_total_price = 0;

            if( !empty($order_info) ){
                foreach( $order_info as $k => $v ){
                    $v_total_price = $v['total_price'];

                    $order_total_price = bcadd($order_total_price, $v_total_price,2);
                }
            }

            $recharge_money = bcsub($recharge_money,$order_total_price,2);

            if( $recharge_money < 0 ){
                $recharge_money = 0;
            }
        }




        //如果金额不足扣费
        $contrast_res = bccomp($recharge_money,$money,2);//高精度比较
        //扣费金额比当前充值金额大，则不能进行扣费
        if(  $contrast_res == -1  ){
            return FALSE;
        }


        return TRUE;
    }


    //-------------end 获取信息-----------------





    //-------------功能模块-----------------


    /**
     * 添加充值记录
     * @param int $uid
     * @param int $audit_id
     * @param decimal $apply_money
     * @param string $apply_img
     * @return int
     */
    public function add_money_apply($uid,$audit_id,$apply_money,$apply_img=''){

        if( !$this->is_parent_audit ){
            $audit_id = 0;
        }


        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );

        if( empty($uid) || !is_numeric($uid) || !is_numeric($audit_id) || $apply_money <= 0 ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数提交错误！',
//                'error_info'    =>  array(
//                    $uid,$audit_id,$apply_money
//                ),
            );
            return $return_restult;
        }


        $money_apply_obj = M('mall_money_apply');
        $money_funds_obj = M('mall_money_funds');


        //-------更改用户资金记录表----------

        //更新其充值申请金额
        $funds_info = $this->get_user_funds_info($uid);

        if( !$funds_info ){
            $return_restult = array(
                'code'  =>  3,
                'msg'   =>  '无法找到用户的资金表！',
            );
            return $return_restult;
        }

        //原来资金信息里的申请金额
        $old_apply_money = $funds_info['apply_money'];

        //更新到资金信息里的申请金额
        $new_apply_money = bcadd($apply_money,$old_apply_money,2);

        $where_funds = array(
            'uid'   =>  $uid,
        );
        $save_funds = array(
            'apply_money'   =>  $new_apply_money,
        );

        $change_funds_result = $money_funds_obj->where($where_funds)->save($save_funds);

        if( !$change_funds_result ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '无法更新用户的资金信息，请重试！',
            );
            return $return_restult;
        }


        //-------更改用户资金记录表----------



        //-------添加充值申请------------

        $apply_info = array(
            'uid' => $uid,
            'audit_id' => $audit_id,
            'apply_money' => $apply_money,
            'apply_img' => $apply_img,
            'created'   =>  time(),
        );
        $apply_result = $money_apply_obj->add($apply_info);

        if( !$apply_result ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '无法添加充值记录！请重试！',
            );
            return $return_restult;
        }
        //-------end 添加充值申请------------



        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '提交充值申请成功！',
        );

        if ($audit_id) {
            //虚拟币模板消息
           $apply_info['status'] = 0;
           $apply_info['name'] = M('distributor')->where(['id' => $uid])->getField('name');
           import('Lib.Action.Message','App');
           $message = new Message();
           $openid = M('distributor')->where(['id' => $audit_id])->getField('openid');
           $message->push(trim($openid), $apply_info, $message->money);
        }

        return $return_restult;
    }//end func add_money_apply




    /**
     * 审核充值申请
     * @param int $id      申请ID
     * @param int $pass    审核结果 1为通过，2为不通过
     * @param int $cur_audit_id    当前审核人，0为总部
     * @return array
     */
    public function apply_pass($id,$pass,$cur_audit_id=0){

        $money_apply_obj = M('mall_money_apply');

        $return_result = array(
            'code'  =>  0,
            'msg'   =>  '',
        );

        if( empty($id) || is_null($pass) || !in_array($pass,array('1','2')) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数有误！',
            );
            return $return_result;
        }

        $condition = array(
            'id'    =>  $id,
            'status'    =>  '0',
        );

        $money_apply_info = $money_apply_obj->where($condition)->find();

        if( empty($money_apply_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有符合条件的充值申请！',
            );
            return $return_result;
        }

        $uid    =   $money_apply_info['uid'];
        $audit_id = $money_apply_info['audit_id'];
        $apply_money = $money_apply_info['apply_money'];


        if( $audit_id != $cur_audit_id ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '该条充值申请应该由审核人进行审核！',
            );
            return $return_result;
        }


        //审核通过则充值
        if( $pass == 1 ){
            //如果审核人不是总部，则要扣审核人的资金
            if( $audit_id != 0 ){

                $charge_money_result = $this->charge_money($audit_id,$apply_money,'transfer',$id);

                if( $charge_money_result['code'] != 1 ){
                    $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  $charge_money_result['msg'],
    //                'errror_info'   =>  $charge_money_result,
                    );
                    return $return_result;
                }
            }


            //审核人扣费，并给申请人充值
            $recharge_result = $this->recharge($uid,$apply_money,'apply',$money_apply_info);

            if( $recharge_result['code'] != 1 ){
                setlog('上级审核下级充值申请时充值失败--充值用户：'.$uid.'--充值金额：'.$apply_money.
                        '--申请ID：'.$id.'返回：'.var_dump($recharge_result,1),'apply_pass_recharge_error');
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  $recharge_result['msg'],
    //                'errror_info'   =>  M('money_charge_log')->getLastSql(),
                );
                return $return_result;
            }
        }
        elseif( $pass == 2 ){//不通过申请则更新当前申请金额
            //------------用户资金表操作------------
            $money_funds_obj = M('mall_money_funds');

            $funds_info = $this->get_user_funds_info($uid);

            if( empty($funds_info) ){
                $return_restult = array(
                    'code'  =>  3,
                    'msg'   =>  '获取用户资金信息失败，请重试！',
                );
                return $return_restult;
            }

            $funds_apply_money = $funds_info['apply_money'];//当前申请金额

            //更新历史充值余额
            $new_funds_apply_money = bcsub($funds_apply_money,$apply_money,2);


            //金额判断，不能为负
            if( $new_funds_apply_money < 0 ){
                $new_funds_apply_money = 0;
            }

            $funds_data = array(
                'apply_money'       =>  $new_funds_apply_money,
                'updated'   =>  time(),
            );

            $where_funds = array(
                'uid'   =>  $uid,
            );

            $funds_save_res = $money_funds_obj->data($funds_data)->where($where_funds)->save();

            if( !$funds_save_res ){
                $return_restult = array(
                    'code'  =>  4,
                    'msg'   =>  '更新用户资金信息失败，请重试！',
                );
                return $return_restult;
            }


            //------------end 用户资金表操作------------
        }


        //改写申请记录
        $condition_save['id']   =   $id;

        $data = array(
            'status'    =>  $pass,
            'updated'   =>  time(),
        );

        $res = $money_apply_obj->where($condition_save)->save($data);

        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '编号'.$id.'审核成功！！',
        );

        //审核虚拟币模板消息
        $money_apply_info['status'] = $pass;
        $money_apply_info['name'] = M('distributor')->where(['id' => $money_apply_info['uid']])->getField('name');
        import('Lib.Action.Message','App');
        $message = new Message();
        $openid = M('distributor')->where(['id' => $money_apply_info['uid']])->getField('openid');
        $message->push(trim($openid), $money_apply_info, $message->money);

        return $return_result;

    }//end func apply_pass






    /**
     * 添加提现记录
     * @param int $uid
     * @param int $audit_id
     * @param decimal $apply_money
     * @param string $apply_remark
     * @param string $apply_img
     * @return int
     */
    public function add_money_refund_apply($uid,$audit_id,$apply_money,$pay_type,$card_name,$card_number,$refund_type,$accountname){

        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );

        if( empty($uid) || !is_numeric($uid) || !is_numeric($audit_id) || $apply_money <= 0 ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数提交错误！',
//                'error_info'    =>  array(
//                    $uid,$audit_id,$apply_money
//                ),
            );
            return $return_restult;
        }
        if(!$refund_type){
            if( empty($pay_type) || empty($card_name) || empty($card_number) ){
                $return_restult = array(
                    'code'  =>  6,
                    'msg'   =>  '到账账户信息不完整！',
//
                );
                return $return_restult;
            }
        }


        $money_refund_apply_obj = M('mall_money_refund_apply');
//        $money_funds_obj = M('money_funds');

        //已有未审核的提现申请无法再次提交申请

        $info = $money_refund_apply_obj->where(['uid'=>$uid,'status'=>'0'])->field('uid','apply_money')->find();

        if( !empty($info) ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '还有一笔金额为'.$info['apply_money'].'申请提现未审核，请耐心等待总部审核再进行提现申请！',
            );
            return $return_restult;
        }


        //-------添加提现申请------------

        $apply_info = array(
            'uid' => $uid,
            'audit_id' => $audit_id,
            'apply_money' => $apply_money,
            'pay_type' => $pay_type,
            'card_name' =>  $card_name,
            'card_number' => $card_number,
            'created'   =>  time(),
            'refund_type' =>$refund_type,
            'account_name' =>$accountname,
        );
        $apply_result = $money_refund_apply_obj->add($apply_info);

        if( !$apply_result ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '无法添加提现记录！请重试！',
            );
            return $return_restult;
        }
        //-------end 添加提现申请------------



        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '提交提现申请成功！',
        );
        return $return_restult;
    }//end func add_money_apply


    /**
     * 审核提现申请
     * @param int $id              申请ID
     * @param string $audit_remark    总部审核备注
     * @param int $pass            审核结果 1为通过，2为不通过
     * @param int $cur_audit_id    当前审核人，0为总部
     * @return array
     */
    public function apply_refund_pass($id,$audit_remark,$pass,$cur_audit_id=0){

        $money_refund_apply_obj = M('mall_money_refund_apply');

        $return_result = array(
            'code'  =>  0,
            'msg'   =>  '',
        );

        if( empty($id) || is_null($pass) || !in_array($pass,array('1','2')) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数有误！',
            );
            return $return_result;
        }


        //----------提现记录判断-----------
        $condition = array(
            'id'    =>  $id,
            'status'    =>  '0',
        );

        $money_apply_info = $money_refund_apply_obj->where($condition)->find();

        if( empty($money_apply_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有符合条件的提现申请！',
            );
            return $return_result;
        }

        $uid    =   $money_apply_info['uid'];
        $audit_id = $money_apply_info['audit_id'];
        $apply_money = $money_apply_info['apply_money'];
        $pay_type = $money_apply_info['pay_type'];
        $card_name = $money_apply_info['card_name'];
        $card_number =$money_apply_info['card_number'];
        $account_name=$money_apply_info['account_name'];
        $mall_refund_type=$money_apply_info['refund_type'];
        if( $audit_id != $cur_audit_id ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '该条提现申请应该由审核人进行审核！',
            );
            return $return_result;
        }
        //----------end 提现记录判断-----------



        //----------提现操作-----------

        //审核通过则充值
        if( $pass == 1 ){
            $refund_type = 2;//申请提现

            $refund_res = $this->refund($uid,$apply_money,$refund_type,$pay_type,$card_name,$card_number,$account_name,$mall_refund_type);

            if( $refund_res['code'] != 1 ){
                return $refund_res;
            }
        }

        //----------end 提现操作-----------

        //----------start调用转账到微信钱包的接口---------
//        if($mall_refund_type){
//
//        }
        //----------end调用转账到微信钱包的接口---------


        //---------改写申请记录----------
        $condition_save['id']   =   $id;

        $data = array(
            'status'    =>  $pass,
            'updated'   =>  time(),
        );

        if( !empty($audit_remark) ){
            $data['audit_remark']   =   $audit_remark;
        }

        $res = $money_refund_apply_obj->where($condition_save)->save($data);

        $return_result = array(
            'code'  =>  1,
            'msg'   => '审核完成！！',
        );
        return $return_result;
        //---------end 改写申请记录----------



    }//end func apply_refund_pass


    /**
     * 更改审核备注
     * @param int $id
     * @param string $remark
     * @param type $cur_audit_id
     * @return int
     */
    public function change_refund_audit_remark($id,$remark,$cur_audit_id=0){

        if( empty($id) || empty($remark) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数有误！',
            );
            return $return_result;
        }


        if( iconv_strlen($remark,'utf-8') > 30 ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '备注不超过30个字符！',
                'str'   =>  iconv_strlen($remark,'utf-8'),
            );
            return $return_result;
        }


        $money_refund_apply_obj = M('mall_money_refund_apply');

        $condition = array(
            'id'    =>  $id
        );

        $apply_info = $money_refund_apply_obj->where($condition)->find();

        if( empty($apply_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有找到该条提现记录！',
            );
            return $return_result;
        }


        $audit_id = $apply_info['audit_id'];
        $old_audit_remark = $apply_info['audit_remark'];

        if( $audit_id != $cur_audit_id ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '该条提现申请记录应该由审核人进行更改！',
            );
            return $return_result;
        }

        if( $old_audit_remark == $remark ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '没有做出更改！',
            );
            return $return_result;
        }

        $save_info = array(
            'audit_remark'  =>  $remark,
//            'update'    =>  time(),
        );

        $save_result = $money_refund_apply_obj->where($condition)->save($save_info);

        if( !$save_result ){
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '备注失败，请重试！',
            );
            return $return_result;
        }


        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '编号'.$id.'备注成功！',
        );
        return $return_result;
    }//end func change_refund_audit_remark





    /**
     * 充值
     * @param int $uid
     * @param decimal $money
     * @param string $type
     * @param array $recharge_info
     * @return array
     */
    public function recharge($uid,$money,$type='apply',$recharge_info){

        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );

        if( empty($uid) || $money <= 0 || !is_numeric($money) ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数错误！',
            );
            return $return_restult;
        }

        if( $type == 'apply' && empty($recharge_info) ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数错误！',
            );
            return $return_restult;
        }


        $money_recharge_log_obj = M('mall_money_recharge_log');
        $money_funds_obj = M('mall_money_funds');


        //-----------充值日志-----------------

        $new_can_refund_money = 0;
        $note = isset($recharge_info['note'])?$recharge_info['note']:'';

        //全金额可提现
        if( $this->is_all_can_refund ){
            $new_can_refund_money = $money;
        }

        if( $type == 'apply' ){
            $apply_id = $recharge_info['id'];
            $source_id = $recharge_info['audit_id'];
            $recharge_type = 1;//申请充值
        }
        else if( $type == 'order_return' ){
            $apply_id = 0;
            $source_id = $recharge_info['source_id'];
//            $note = $recharge_info['note'];
            $recharge_type = 3;//订单返现

            //订单返还金额为可提现金额
            $new_can_refund_money = $money;
        }
        else if( $type == 'order_charge' ){
            $apply_id = 0;
            $source_id = $recharge_info['source_id'];
//            $note = $recharge_info['note'];
            $recharge_type = 4;//下级下单
        }
        else if( $type == 'rebate_recharge' ){
            $apply_id = 0;
            $source_id = $recharge_info['source_id'];
            $recharge_type = 5;//返利返现

            $new_can_refund_money = $money;
        }
        else{
            $source_id = 0;
            $apply_id = 0;
            $recharge_type = 2;//后台充值
        }


        $recharge_log = array(
            'uid'   =>  $uid,
            'source_id' =>  $source_id,//虚拟币数的来源，总部或代理
            'apply_id'    =>  $apply_id,//申请表的ID
            'money'     =>  $money,//充值金额
            'type'  =>  $recharge_type,//1为申请充值，2为后台充值
            'note'  =>  $note,
            'created'   =>  time(),
        );

        $recharge_log_result = $money_recharge_log_obj->data($recharge_log)->add();
        
        if( !$recharge_log_result ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '充值记录失败，请重试！',
                'error_info'    =>  $recharge_log,
            );
            return $return_restult;
        }
        
        //如果开启了虚拟币功能并且由总部审核充值，则进行充值月统计
        if ($this->is_charge_money) {
            if (!$this->is_parent_audit) {
                $this->month_count($recharge_log);
            } else {
                //由上级审核充值的模式还需要沟通，待以后开发

            }
        }
        
        //-----------end 充值日志-----------------
        
        
        
        //------------用户资金表操作------------
        $funds_info = $this->get_user_funds_info($uid);
        
        if( empty($funds_info) ){
            $return_restult = array(
                'code'  =>  3,
                'msg'   =>  '获取用户资金信息失败，请重试！',
            );
            return $return_restult;
        }
        
        $funds_recharge_money = $funds_info['recharge_money'];//当前充值金额
        $funds_apply_money = $funds_info['apply_money'];//当前申请金额
        $funds_can_refund_money = $funds_info['can_refund_money'];//当前可提现金额
        $funds_his_recharge_money = $funds_info['his_recharge_money'];//历史充值金额
//        $funds_his_charge_money = $funds_info['his_charge_money'];//历史扣费金额
        
        
        //如果属于申请充值，更新其申请充值的资金信息
        if( $type=='apply' ){
            $funds_apply_money = bcsub($funds_apply_money,$money,2);
        }
        
        //更新当前充值余额
        $funds_recharge_money = bcadd($funds_recharge_money,$money,2);
        
        //更新历史充值余额
        $funds_his_recharge_money = bcadd($funds_his_recharge_money,$money,2);
        
        //更新可提现金额
        if( $new_can_refund_money != 0 ){
            $funds_can_refund_money = bcadd($funds_can_refund_money,$new_can_refund_money,2);
        }
        
        //金额判断，不能为负
        if( $funds_apply_money < 0 ){
            $funds_apply_money = 0;
        }
        
        $funds_data = array(
            'recharge_money'    =>  $funds_recharge_money,
            'apply_money'       =>  $funds_apply_money,
            'can_refund_money'  =>  $funds_can_refund_money,
            'his_recharge_money'    =>  $funds_his_recharge_money,
            'updated'   =>  time(),
        );
        
        $where_funds = array(
            'uid'   =>  $uid,
        );
        
        $funds_save_res = $money_funds_obj->data($funds_data)->where($where_funds)->save();
        
        if( !$funds_save_res ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '更新用户资金信息失败，请重新！',
            );
            return $return_restult;
        }
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '充值成功！',
        );
        return $return_restult;
        
        
        //------------end 用户资金表操作------------
        
    }//end func recharge
    
    
    
    
    
    
    
    
    /**
     * 提现操作
     * @param int $uid
     * @param decimal $refund_money
     * @param int $type
     * @return array
     */
    public function refund($uid,$refund_money,$type,$pay_type,$card_name,$card_number,$account_name,$mall_refund_type){
        
        $return_result = array(
            'code'  =>   0,
            'msg'   =>  '',
        );
        
        
        if( empty($uid) || empty($refund_money) || !is_numeric($refund_money) ){
            $return_result = array(
                'code'  =>   2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        //判断该用户可提取金额
        $can_refund_money = $this->get_user_can_refund_money($uid);
        
//        echo bccomp($can_refund_money,$refund_money,2);
        
        //可提现金额不能小于提交的提现金额
        if( bccomp($can_refund_money,$refund_money,2) == -1 ){
            $return_result = array(
                'code'  =>   3,
                'msg'   =>  '提现金额超过了可提现范围！',
            );
            return $return_result;
        }
        
        
        //--------生成提现日志------------
        $money_refund_obj = M('mall_money_refund');
        
        $money_refund_data = array(
            'uid'   =>  $uid,
            'money' =>  $refund_money,
            'type'  =>  $type,
            'pay_type' => $pay_type,
            'card_name' => $card_name,
            'card_number' => $card_number,
            'created'   =>  time(),
            'account_name'=>$account_name,
            'refund_type'=>$mall_refund_type,
        );
        
        $add_money_refund_res = $money_refund_obj->data($money_refund_data)->add();
        
        if( !$add_money_refund_res ){
            $return_result = array(
                'code'  =>   4,
                'msg'   =>  '更新提现记录失败！请重试！',
            );
            return $return_result;
        }
        
        
        //--------end 生成提现日志------------
        
        //--------更新用户资金信息表------------
        $money_funds_obj = M('mall_money_funds');
        
        //更新其充值申请金额
        $funds_info = $this->get_user_funds_info($uid);
        
        if( !$funds_info ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '无法找到用户的资金信息！',
            );
            return $return_restult;
        }
        
        //原来资金信息里的申请金额
        $old_recharge_money = $funds_info['total_money'];
        $old_can_refund_money = $funds_info['no_refund_money'];
        $old_his_refund_money = $funds_info['yes_refund_money'];
        
        //更新到资金信息
        $new_recharge_money = bcsub ($old_recharge_money,$refund_money,2);
        $new_can_refund_money = bcsub($old_can_refund_money,$refund_money,2);
        $new_his_refund_money = bcadd ($old_his_refund_money,$refund_money,2);
        
        $funds_data = array(
            'no_refund_money'  =>  $new_can_refund_money,
            'yes_refund_money'  =>  $new_his_refund_money,
            'updated'   =>  time(),
        );
        
        $where_funds = array(
            'uid'   =>  $uid,
        );
        
        $funds_save_res = $money_funds_obj->where($where_funds)->data($funds_data)->save();
        
        if( !$funds_save_res ){
            $return_restult = array(
                'code'  =>  6,
                'msg'   =>  '更新用户资金信息失败，请重试！',
            );
            return $return_restult;
        }
        //--------end 更新用户资金信息表------------
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '用户编号：'.$uid.'提现成功！',
        );
        return $return_restult;
    }//end func refund
    
    
    
    /**
     * 扣费
     * @param int $uid
     * @param decimal $money
     * @param string $charge_type
     * @param string $order_num
     * @return array
     */
    public function charge_money($uid,$money,$charge_type='order',$order_num=''){
        
        //判断该系统是否使用虚拟币系统
        if( !$this->is_charge_money ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '无需扣费！',
            );
            return $return_restult;
        }
        
        
        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );
        
        if( empty($uid) || $money<=0 ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数提交错误！',
            );
            return $return_restult;
        }
        
        //如果是订单扣费，并且订单号信息为空
        if( $charge_type=='order' && empty($order_num) ){
            $return_restult = array(
                'code'  =>  3,
                'msg'   =>  '订单参数提交错误！',
            );
            return $return_restult;
        }
        
        //检查用户是否能进行扣费
        $check_recharge_money_res = $this->check_recharge_money($uid,$money);
        
        if( !$check_recharge_money_res ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '请检查余额是否足够扣费！',
            );
            return $return_restult;
        }
        
        
        //----------用户扣费记录表添加------------------
        //money_charge_log,type:1为订单扣费,2为提现退款,3为下级转账，4为支付返利
        
        $money_charge_log_obj = M('mall_money_charge_log');
        
        $can_refund_money = 0;
        if( $charge_type == 'order' ){
            $new_charge_type = 1;
        }
        elseif( $charge_type =='transfer' ){
            $new_charge_type = 3;
        }
        elseif( $charge_type == 'rebate_recharge' ){
            $new_charge_type = 4;
            $can_refund_money = $money;
        }
        elseif( $charge_type == 'stock' ){
            $new_charge_type = 5;
            $can_refund_money = $money;
        }
        else{
            $new_charge_type = 2;
            $can_refund_money = $money;
        }
        
        
        $charge_log_info = array(
            'uid'   =>  $uid,
            'type'  =>  $new_charge_type,
            'order_num' =>  $order_num,
            'money' =>  $money,
            'created'   =>  time(),
        );
        
        $add_charge_log_res = $money_charge_log_obj->data($charge_log_info)->add();
        
        if( !$add_charge_log_res ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '添加扣费记录失败，请重试！',
            );
            return $return_restult;
        }
        
        
        //----------end 用户扣费记录表添加--------------
        
        
        
        //----------用户资金信息表更新--------------
        //money_charge_log,type:1为订单扣费,2为提现退款
        $money_funds_obj = M('mall_money_funds');
        
        $condition_funds = array(
            'uid'   =>  $uid,
        );
        
        $funds_info = $money_funds_obj->where($condition_funds)->find();
        
        //资金信息
        $old_recharge_money = $funds_info['total_money'];
        $old_can_refund_money = $funds_info['no_refund_money'];
        //$old_apply_money = $funds_info['apply_money'];
        //$old_his_recharge_money = $funds_info['his_recharge_money'];
        $old_his_charge_money = $funds_info['yes_refund_money'];
        
        //计算扣费后的资金信息
        $new_recharge_money = bcsub($old_recharge_money,$money,2);//当前充值金额
        $new_can_refund_money = bcadd($old_can_refund_money,$can_refund_money,2);//可提现金额
        $new_his_charge_money = bcadd($old_his_charge_money,$money,2);//历史总扣费金额
        
        //特别的情况，如果可用充值金额扣费后少于当前可提现金额，则可提现金额也应该与当前充值金额相等（可提现金额是默认最后使用）
        //当前可用金额必然大于或等于可提现金额，如果小于，则更改可提现金额为当前可用充值金额
        if( bccomp($new_recharge_money,$new_can_refund_money,2) == -1 ){
            $new_can_refund_money = $new_recharge_money;
        }
        
        $money_funds_save_info = array(
            'total_money'    =>  $new_recharge_money,
            'yes_refund_money'  =>  $new_his_charge_money,
            'no_refund_money'  =>  $new_can_refund_money,
        );
        
        $money_funds_save_res = $money_funds_obj->where($condition_funds)->save($money_funds_save_info);
        
        if( !$money_funds_save_res ){
            $return_restult = array(
                'code'  =>  6,
                'msg'   =>  '无法更新用户资金信息！',
//                'error_info'    =>  array(
//                    'condition_funds'   =>  $condition_funds,
//                    'money_funds_save_info' =>  $money_funds_save_info,
//                    'last_sql'  =>  $money_charge_log_obj->getLastSql(),
//                ),
            );
            return $return_restult;
        }
        
        //----------end 用户资金信息表更新--------------
        
        
        //--------订单扣费金额充值回该订单发货的经销商-----
        if( $this->is_parent_order && $charge_type == 'order' ){
            $order_obj = M('mall_order');
            
            $condition_order = array(
                'order_num' =>  $order_num,
            );
            
            $order_info = $order_obj->where($condition_order)->find();
            
            if( empty($order_info) ){
                setLog('订单扣费金额充值回该订单发货的经销商时，无法找到订单！订单号：'.$order_num
                        .'，用户：'.$uid.'，时间：'.date('Ymd H:i:s'),'charge_money_order_recharge_can_not_find_order');
            }
            else{
                $order_o_id = ($order_info['o_id']);//订单发货商，如果订单发货商更改，这里必须更换
            
                //订单发货商为0（总部），则无须进行充值
                if( !empty($order_o_id) ){
                    $recharge_info = array(
                        'source_id' =>  $uid,
                        'note'  =>  $order_num,
                    );

                    $recharge_result = $this->recharge($order_o_id,$money,'order_charge',$recharge_info);
                    if( $recharge_result['code'] != 1 ){
                        setLog('订单扣费金额充值回该订单发货的经销商时，充值失败！订单号：'
                                .$order_num.'，用户：'.$uid.'，时间：'.date('Ymd H:i:s').'，充值返回：'
                                . var_dump($recharge_result,1),'charge_money_order_recharge_can_not_recharge');
                    }
                }
            }
        }
        //--------end 订单扣费金额充值回该订单接收的经销商-----
        
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '扣费成功！',
        );
        return $return_restult;
    }//end func charge_money
    
    
    
    
    
    
    
    
    //lxs特别的订单虚拟币反还逻辑
    public function monery_order_return($uid,$order_num,$order_info){
        
        if( empty($uid) || empty($order_num) || empty($order_info) ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数提交错误',
            );
            return $return_restult;
        }
        
        
        $distributor_obj = M('distributor');
        
        $condition_dis = array(
            'id'    =>  $uid
        );
        
        $dis_info = $distributor_obj->where($condition_dis)->find();
        
        $pid = $dis_info['pid'];//当前代理的上级
        $cur_level = $dis_info['level'];//当前代理的等级
        
        //如果该代理的上级为总部或其为最高等级
        if( $pid == 0 || $cur_level == 1 ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '该代理无需进行订单返还',
            );
            return $return_restult;
        }
        
        $new_pid = $pid;
        $parent_level = $cur_level;
        $condition_parent = array();
        
        $price_key_name = "price" . $cur_level;//该用户相应等级的产品单价key名
        
        $recharge_error = array();
        
        //循环得到该代理链的代理，这里限制，无论什么情况，最多只循环一百次
        for( $i=1;$i<=$this->order_return_rank;$i++ ){
            
            //上级是总部，退出循环
            if( $new_pid == 0 ){
                break;
            }
            
            $condition_parent['id'] =   $new_pid;
            $parent_info = $distributor_obj->where($condition_parent)->find();
            
            //如果找不到上级的信息，可以直接退出循环
            if( empty($parent_info) ){
                break;
            }
            
            $new_parent_level = $parent_info['level'];//当前代理的等级
            $cur_uid = $parent_info['id'];
            $new_pid = $parent_info['pid'];//每次循环都赋值
            
            //如果代理的等级与上级代理的等级相同，结束本次循环
            if( $parent_level == $new_parent_level ){
                continue;
            }
            
            $new_price_key_name = "price" . $new_parent_level;
            
            $the_profit = 0;//返利
            
            foreach( $order_info as $o_info ){
                $the_p_id = $o_info['p_id'];        //产品ID
                $the_num = $o_info['num'];          //产品数量
                $the_price = $o_info['price'];      //下单的的产品单价
                $the_tem_info = $o_info['tem_info'];//该产品所有信息
                
                $the_old_price  =   $the_tem_info[$price_key_name];//该等级的下级的产品单价金额
                $the_new_price  =   $the_tem_info[$new_price_key_name];//该等级的产品单价金额
                
                //用该次循环的代理的下级产品价格减去这次的产品价格，再乘以产品数量即该产品的返利
                $the_price_bcsub = bcsub($the_old_price,$the_new_price);
                
                if( $the_price_bcsub > 0 ){
                    $the_profit = $the_price_bcsub*$the_num + $the_profit;
                }
            }
            
            
            $price_key_name = $new_price_key_name;//结束时记录该次循环的产品价格KEY值
            
            
            if( $the_profit > 0 ){
                $recharge_info = array(
                    'source_id' =>  $uid,
                    'note'  =>  $order_num,
                );
                
                $recharge_result = $this->recharge($cur_uid,$the_profit,'order_return',$recharge_info);
                
                if( $recharge_result['code'] != 1 ){
                    $recharge_error[] = array(
                        'uid'   =>  $cur_uid,
                        'the_profit'    =>  $the_profit,
                        'recharge_info' =>  $recharge_info,
                        'return'    =>  $recharge_result,
                    );
                }
            }
        }
        
        if( !empty($recharge_error) ){
            setlog('订单返还充值失败：'.  var_dump($recharge_error,1),'order_return_recharge_error');
        }
        
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '订单返还成功！',
            'error_info'    =>  $recharge_error,
        );
        return $return_restult;
        
    }//end func monery_order_return
    
    
    
    //-------------end 功能模块-----------------
    
    
    //转换未中文的星期
    public function cn_week_day($day_num) {

        $cn_week_day = '未知';
        switch ($day_num) {
            case 1:$cn_week_day = "周一";
                break;
            case 2: $cn_week_day = "周二";
                break;
            case 3:$cn_week_day = "周三";
                break;
            case 4:$cn_week_day = "周四";
                break;
            case 5:$cn_week_day = "周五";
                break;
            case 6:$cn_week_day = "周六";
                break;
            case 7:$cn_week_day = "周日";
                break;
        }
        
        return $cn_week_day;
    }//end func cn_week_day

    //虚拟币充值月统计
    public function month_count($data) {
        $model = M('mall_money_month_count');
        $month = get_month();
        
        $where = [
            'uid' => $data['uid'],
            'month' => $month
        ];
        $month_count = $model->where($where)->find();
        if ($month_count) {
            $res = $model->where($where)->setInc('money', $data['money']);
        } else {
            $data = [
                'uid' => $data['uid'],
                'money' => $data['money'],
                'month' => $month,
                'day' => date('Ymd')
            ];
            $res = $model->add($data);
        }
        
        if (!$res) {
            setLog('虚拟币月统计失败:'.json_encode($data), 'mall_money_count');
            return false;
        }
        return true;
    }
}