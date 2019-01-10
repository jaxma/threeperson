<?php
//返利的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Rebate {
    
    public $rerebate_model;
    public $distributor_model;
    
    
    public $state_name = [
        0   =>  '待审核',
        1   =>  '已通过',
        2   =>  '已确认',
        3   =>  '不通过',//返利一般没有此状态
    ];//返利状态
    
    
    public $order_rebate_type = [
        1   =>  '一级订单返利',
        2   =>  '三级订单返利',
        3   =>  '充值月统计返利',
        4   =>  '订单月统计返利',
        5   =>  '首次一级订单返利',
    ];
    

    /**
     * 架构函数
     */
    public function __construct() {
        $this->rerebate_model = M('Rerebate');
        $this->distributor_model = M('distributor');
    }
    
    
    //==========================start 获取返利信息==================================
    
    
    //获取返利记录
//    public function get_rerebate($page_info=array(),$condition=array()){
//
//        $list = array();
//        $page = '';
//        //每页的数量
//        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
//        //如果页码为空的话默认值为1
//        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
//
//
//        $count = $this->rerebate_model->where($condition)->count();
//        if( $count > 0 ){
//
//            if( !empty($page_info) ){
//
//                $page_con = $page_num.','.$page_list_num;
//
//                $list = $this->rerebate_model->where($condition)->order('id desc')->page($page_con)->select();
//            }
//            else{
//                $list = $this->rerebate_model->where($condition)->order('id desc')->select();
//            }
//
//
//
//            //-----整理添加相应其它表的信息-----
//            $uids = array();
//            $applys = array();
//            $order_notes = array();
//
//            foreach( $list as $k => $v ){
//                $v_user_id = $v['user_id'];
//                $v_x_id = $v['x_id'];
//                $v_pay_id = $v['pay_id'];
//
//                if( !isset($uids[$v_user_id]) ){
//                    $uids[$v_user_id] = $v_user_id;
//                }
//                if( !isset($uids[$v_x_id]) ){
//                    $uids[$v_x_id] = $v_x_id;
//                }
//                if( !isset($uids[$v_pay_id]) ){
//                    $uids[$v_pay_id] = $v_pay_id;
//                }
//            }
//
//            array_values($uids);
//            array_unique($uids);
//
//            $condition_dis = array(
//                'id'    =>  array('in',$uids),
//            );
//            $dis_info = $this->distributor_model->where($condition_dis)->select();
//
//            $dis_key_info[0]['name'] = '总部';
//            foreach( $dis_info as $k_dis=>$v_dis ){
//
//                $v_dis_uid = $v_dis['id'];
//
//                $dis_key_info[$v_dis_uid] = $v_dis;
//            }
//
//
//            foreach( $list as $k => $v ){
//                $v_user_id = $v['user_id'];
//                $v_x_id = $v['x_id'];
//                $v_pay_id = $v['pay_id'];
//                $v_time = $v['time'];
//                $v_state = $v['state'];
//                
//
////                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
////                $list[$k]['source_info'] = $dis_key_info[$v_source_id];
//                $list[$k]['dis_info'] = $dis_key_info[$v_user_id];
//                $list[$k]['x_info'] = $dis_key_info[$v_x_id];
//                $list[$k]['pay_info'] = $dis_key_info[$v_pay_id];
//                $list[$k]['time_format'] = date('Ymd His',$v_time);
//                $list[$k]['status_name'] = $this->state_name[$v_state];
//                $list[$k]['state_name'] = $this->state_name[$v_state];
//            }
//            //-----end 整理添加相应其它表的信息-----
//        }
//
//        if( !empty($page_info) ){
//            //*分页显示*
//            import('ORG.Util.Page');
//            $p = new Page($count, $page_list_num);
//            $page = $p->show();
//        }
//
//
//        $return_result = array(
//            'list'  =>  $list,
//            'page'  =>  $page,
//        );
//
//        return $return_result;
//    }//end func get_rerebate
    
    
    
    //==========================end 获取返利信息==================================
    
    
    
    //==========================订单返利（系统相关操作触发位置）==================================
    
    
    /**
     * 总部后台订单审核时返利
     * @param type $uid
     * @param type $order_info
     * @return string|int
     */
    public function radmin_order_audit_rebate($uid,$order_info){
        import('Lib.Action.NewRebate','App');
        (new NewRebate())->same_level_rebate($uid,$order_info[0]);
    }//end func radmin_order_audit_rebate
    
    
    
    /**
     * 经销商后台订单审核时返利
     * @param type $uid
     * @param type $order_info
     * @return string|int
     */
    public function admin_order_audit_rebate($uid,$order_info){
        import('Lib.Action.NewRebate','App');
        (new NewRebate())->same_level_rebate($uid,$order_info[0]);
    }//end func admin_order_audit_rebate
    
    
    
    /**
     * 经销商后台订单确认时返利
     * @param type $uid
     * @param type $order_info
     * @return string|int
     */
    public function confirm_order_audit_rebate($uid,$order_info){
        return $this->order_rebate_2($uid,$order_info);
    }//end func confirm_order_audit_rebate
    
    
    
    /**
     * 充值时触发的返利
     * @param int $uid                  //用户ID
     * @param decimal $recharge_money   //充值金额
     * @param int $recharge_id          //充值id
     * @param bool $is_first            //是否首次充值
     */
    public function recharge_rebate($uid,$recharge_log){
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        $rebate->same_level_rebate($uid,$recharge_log, $rebate->money_rebate);
//        return $this->apply_rebate_1($uid,$recharge_money);
    }//end func recharge_rebate


    /**
     * 品牌商城用户支付成功的时候触发
     */
    public function  pay_order_success_rebate($uid,$order_info){
        import('Lib.Action.Mallrebate','App');
        $Mallrebate = new Mallrebate();
        $Mallrebate->order_rebate($uid,$order_info, $Mallrebate->order_rebate);
    }
    
    /**
     * 订单返利（以订单计算返利的模式）
     * //----------start 这里写上该返利的具体政策----------------
     * 
     * 
     * //----------end 这里写上该返利的具体政策----------------
     * 
     */
    public function order_rebate_1($uid,$order_info){
        $distributor = M('distributor');
        $rerebate = M('Rerebate');
        
        $condition_cur_dis = array(
            'id' => $uid,
        );
        $cur_dis_info = $distributor->where($condition_cur_dis)->find();
        
        $partner_id = $cur_dis_info['pid'];
        $recommendID = $cur_dis_info['recommendID'];
        $isRecommend = $cur_dis_info['isRecommend'];
        $cur_level = $cur_dis_info['level'];
        $internal_level = $cur_dis_info['internal_level'];
        
        
        $order_num = $order_info['0']['order_num'];
        $order_total_price = $order_info['0']['total_price']; //该订单总货款
        $order_month = $order_info['0']['month']; //返利月份根据其订单生成月份
        
        
        $rebate_order_setting = M('rebate_order_setting');
        $rebate_order_set_info = $rebate_order_setting->where(array('id'=>'1'))->find();
        
        $rebate_order_key = 'level'.$cur_level;
        $rebate_percent = isset($rebate_order_set_info[$rebate_order_key])?$rebate_order_set_info[$rebate_order_key]:0;
        
        $set_info = '返利比例：'.$rebate_percent;
        
        $rerebate_money = bcmul($order_total_price,$rebate_percent,2);
        
        if( $rerebate_money > 0 ){
            $rerebate_info = array(
                'order_num' => $order_num,
                'user_id' => $uid, //获利人id
                'x_id' => $uid,
                'time' => time(),
                'money' => $rerebate_money,
                'set_info' => $set_info,
                'month' => $order_month,
                'status' => 0, //默认0为未审核
                'type'  =>  1,
            );

            $add_res = $rerebate->add($rerebate_info);
            
            return $add_res;
        }
        
        return FALSE;
    }//end func order_rebate_1
    
    
    /**
     * 订单返利（根据产品数量以及用户级别进行订单返利）
     * //----------start 这里写上该返利的具体政策----------------
     * 
     * //----------end 这里写上该返利的具体政策----------------
     * 
     */
    //订单三级返利
    public function order_rebate_2($uid,$order_info){
        

            if( empty($uid) || empty($order_info) ){
                $return_result = array(
                    'code'  =>  2,
                    'msg'   =>  '参数错误！',
                );
                return $return_result;
            }

            $distributor_obj = M('distributor');
            $rerebate_obj = M('Rerebate');
            $Templet_obj = M('Templet');


            //下单用户信息
            $contion_dis = array(
                'id'    =>  $uid,
            );

            $dis_info = $distributor_obj->where($contion_dis)->find();

            if( empty($dis_info) ){
                $return_result = array(
                    'code'  =>  3,
                    'msg'   =>  '查无此下单用户信息！',
                );
                return $return_result;
            }

            $dis_level = $dis_info['level'];
            $dis_recommendID = $dis_info['recommendID'];
            $dis_pid = $dis_info['pid'];

            //以下判断为最高两个级别才有的平级推荐返利
//            $can_rebate_levels = array(1,2,3);


//            if( !in_array($dis_level, $can_rebate_levels) ){
//                $return_result = array(
//                    'code'  =>  4,
//                    'msg'   =>  '不在返利等级范围！',
//                );
//                return $return_result;
//            }

            if( $dis_recommendID == 0 ){
                $return_result = array(
                    'code'  =>  5,
                    'msg'   =>  '推荐人为总部，不进行下一步操作！',
                );
                return $return_result;
            }

            //一级返利用户
            $contion_rec = array(
                'id'    =>  $dis_recommendID,
            );

            $rec_info = $distributor_obj->where($contion_rec)->find();

            if( empty($rec_info) ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  '查无此返利用户信息！',
                );
                return $return_result;
            }

            $rec_id = $rec_info['id'];
            $rec_recommendID = $rec_info['recommendID'];
            $rec_level = $rec_info['level'];
            $rec_pid=$rec_info['pid'];

//            if( $rec_level != $dis_level ){
//                $return_result = array(
//                    'code'  =>  7,
//                    'msg'   =>  '非平级无法返利！',
//                );
//                return $return_result;
//            }


            //---------订单相关信息得到返利金额--------

            $rebate_money1_level1 = $rebate_money2_level1 = $rebate_money3_level1 = 0;//等级1返利
            $rebate_money1_level2 = $rebate_money2_level2 = $rebate_money3_level2 = 0;//等级2返利
            $rebate_money1_level3 = $rebate_money2_level3 = $rebate_money3_level3 = 0;//等级3返利
            $rebate_money1 = 0;//一级返利
            $rebate_money2 = 0;//二级返利
            $set_info1_levle1 = $set_info2_levle1 = $set_info3_levle1 = $set_info1_levle2 = $set_info2_levle2 = $set_info1_levle3 = $set_info2_levle3 = '';
            $set_info1 = '';//一级返利信息
            $set_info2 = '';//二级返利信息

            $p_ids = array();
            $p_info = array();
            foreach( $order_info as $k => $v ){
                $order_num = $v['order_num'];
                $v_p_id = $v['p_id'];
                $v_num = $v['num'];

                $p_info[$v_p_id] = $v_num;
                $p_ids[] = $v_p_id;
            }

            $condition_temp = array(
                'id'    =>  array('in',$p_ids),
            );

            $templet_info = $Templet_obj->where($condition_temp)->select();

            if( empty($templet_info) ){
                $return_result = array(
                    'code'  =>  12,
                    'msg'   =>  '找不到产品信息！',
                );
                return $return_result;
            }

            foreach( $templet_info as $k_t => $v_t ){
                $v_t_id = $v_t['id'];
                $v_t_name = $v_t['name'];
                $v_t_rebate1_level1 = $v_t['rebate1_level1'];
                $v_t_rebate2_level1 = $v_t['rebate2_level1'];
                $v_t_rebate3_level1 = $v_t['rebate3_level1'];
                $v_t_rebate1_level2 = $v_t['rebate1_level2'];
                $v_t_rebate2_level2 = $v_t['rebate2_level2'];
                $v_t_rebate1_level3 = $v_t['rebate1_level3'];
                $v_t_rebate2_level3 = $v_t['rebate2_level3'];
                $v_t_rebate1 = $v_t['rebate1'];
                $v_t_rebate2 = $v_t['rebate2'];


                $the_p_order_num = isset($p_info[$v_t_id])?$p_info[$v_t_id]:0;

                $the_rebate_money1_level1 = bcmul($v_t_rebate1_level1,$the_p_order_num,2);
                $the_rebate_money2_level1 = bcmul($v_t_rebate2_level1,$the_p_order_num,2);
                $the_rebate_money3_level1 = bcmul($v_t_rebate3_level1,$the_p_order_num,2);
                $the_rebate_money1_level2 = bcmul($v_t_rebate1_level2,$the_p_order_num,2);
                $the_rebate_money2_level2 = bcmul($v_t_rebate2_level2,$the_p_order_num,2);
                $the_rebate_money1_level3 = bcmul($v_t_rebate1_level3,$the_p_order_num,2);
                $the_rebate_money2_level3 = bcmul($v_t_rebate2_level3,$the_p_order_num,2);

                $the_rebate_money1 = bcmul($v_t_rebate1,$the_p_order_num,2);
                $the_rebate_money2 = bcmul($v_t_rebate2,$the_p_order_num,2);




                $the_str = '产品名'.$v_t_name.'，产品数量'.$the_p_order_num.'，返利（元/件）：';


                $set_info1_levle1 = $set_info1_levle1.$the_str.$v_t_rebate1_level1.'；';
                $set_info2_levle1 = $set_info2_levle1.$the_str.$v_t_rebate2_level1.'；';
                $set_info3_levle1 = $set_info3_levle1.$the_str.$v_t_rebate3_level1.'；';
                $set_info1_levle2 = $set_info1_levle2.$the_str.$v_t_rebate1_level2.'；';
                $set_info2_levle2 = $set_info2_levle2.$the_str.$v_t_rebate2_level2.'；';
                $set_info1_levle3 = $set_info1_levle3.$the_str.$v_t_rebate1_level3.'；';
                $set_info2_levle3 = $set_info2_levle3.$the_str.$v_t_rebate2_level3.'；';

                $set_info1 = $set_info1.$the_str.$v_t_rebate1.'；';
                $set_info2 = $set_info2.$the_str.$v_t_rebate2.'；';


                $rebate_money1_level1 = bcadd($rebate_money1_level1,$the_rebate_money1_level1,2);
                $rebate_money2_level1 = bcadd($rebate_money2_level1,$the_rebate_money2_level1,2);
                $rebate_money3_level1 = bcadd($rebate_money3_level1,$the_rebate_money3_level1,2);
                $rebate_money1_level2 = bcadd($rebate_money1_level2,$the_rebate_money1_level2,2);
                $rebate_money2_level2 = bcadd($rebate_money2_level2,$the_rebate_money2_level2,2);
                $rebate_money1_level3 = bcadd($rebate_money1_level3,$the_rebate_money1_level3,2);
                $rebate_money2_level3 = bcadd($rebate_money2_level3,$the_rebate_money2_level3,2);

                $rebate_money1 = bcadd($rebate_money1,$the_rebate_money1,2);
                $rebate_money2 = bcadd($rebate_money2,$the_rebate_money2,2);
            }



            //---------end 订单相关信息得到返利金额------------



        $rerebate1_info = array(
            'order_num' => $order_num,
            'user_id' => $rec_id, //获利人id
            'x_id' => $uid,
            'pay_id'    =>  $dis_pid,
            'month' => date(Ym),
            'day' => date(Ymd),
            'time' => time(),
            'status' => 0, //默认0为未审核
            'type'  =>  2,
        );

        if( $dis_level == 1 &&  $rebate_money1_level1 > 0 ){
            $rerebate1_info['money'] = $rebate_money1_level1;
            $rerebate1_info['set_info'] = $set_info1_levle1;

            $add_res = $rerebate_obj->add($rerebate1_info);
        }
        elseif( $dis_level == 2 &&  $rebate_money1_level2 > 0 ){
            $rerebate1_info['money'] = $rebate_money1_level2;
            $rerebate1_info['set_info'] = $set_info1_levle2;

            $add_res = $rerebate_obj->add($rerebate1_info);
        }
        elseif( $dis_level == 3 && $rebate_money1_level3 > 0 ){
            $rerebate1_info['money'] = $rebate_money1_level3;
            $rerebate1_info['set_info'] = $set_info1_levle3;

            $add_res = $rerebate_obj->add($rerebate1_info);
        }


        //二级返利用户
        $contion_rec2 = array(
            'id'    =>  $rec_recommendID,
        );

        $rec2_info = $distributor_obj->where($contion_rec2)->find();

        if( empty($rec2_info) ){
            $return_result = array(
                'code'  =>  8,
                'msg'   =>  '查无此二级返利用户信息！',
            );
            return $return_result;
        }

        $rec2_id = $rec2_info['id'];
        $rec2_level = $rec2_info['level'];
        $rec2_recommendID = $rec2_info['recommendID'];


//        if( $rec2_level != $dis_level ){
//            $return_result = array(
//                'code'  =>  9,
//                'msg'   =>  '非平级无法返利！',
//            );
//            return $return_result;
//        }


        $rerebate2_info = array(
            'order_num' => $order_num,
            'user_id' => $rec2_id, //获利人id
            'x_id' => $uid,
            'month' => date(Ym),
            'day' => date(Ymd),
            'pay_id'    =>  $dis_pid,
            'time' => time(),
            'status' => 0, //默认0为未审核
            'type'  =>  2,
        );
        if( $dis_level == 1 && $rebate_money2_level1 > 0 ){
            $rerebate2_info['money'] = $rebate_money2_level1;
            $rerebate2_info['set_info'] = $set_info2_levle1;

            $add_res = $rerebate_obj->add($rerebate2_info);
        }
        elseif( $dis_level == 2 &&  $rebate_money2_level2 > 0 ){
            $rerebate2_info['money'] = $rebate_money2_level2;
            $rerebate2_info['set_info'] = $set_info2_levle2;

            $add_res = $rerebate_obj->add($rerebate2_info);
        }
        elseif( $dis_level == 3 && $rebate_money2_level3 > 0 ){
            $rerebate2_info['money'] = $rebate_money2_level3;
            $rerebate2_info['set_info'] = $set_info2_levle3;

            $add_res = $rerebate_obj->add($rerebate2_info);
        }


//
//        if( $dis_level != 1 ){
//            $return_result = array(
//                'code'  =>  1,
//                'msg'   =>  '非最高级别无三级返利！',
//            );
//            return $return_result;
//        }



        //三级返利用户
        $contion_rec3 = array(
            'id'    =>  $rec2_recommendID,
        );

        $rec3_info = $distributor_obj->where($contion_rec3)->find();

        if( empty($rec3_info) ){
            $return_result = array(
                'code'  =>  10,
                'msg'   =>  '查无此三级返利用户信息！',
            );
            return $return_result;
        }

        $rec3_id = $rec3_info['id'];
        $rec3_level = $rec3_info['level'];


        if( $rec3_level != $dis_level ){
            $return_result = array(
                'code'  =>  11,
                'msg'   =>  '非平级无法返利！',
            );
            return $return_result;
        }


        if( $dis_level == 1 && $rebate_money3_level1 > 0 ){
            $rerebate_info = array(
                'order_num' => $order_num,
                'user_id' => $rec3_id, //获利人id
                'x_id' => $uid,
                'pay_id'    =>  $dis_pid,
                'time' => time(),
                'month' => date(Ym),
                'day' => date(Ymd),
                'money' => $rebate_money3_level1,
                'set_info' => $set_info3_levle1,
                'status' => 0, //默认0为未审核
                'type'  =>  2,
            );

            $add_res = $rerebate_obj->add($rerebate_info);
        }


            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '返利逻辑完！',
                'add_res' => $add_res,
            );
            return $return_result;



    }//end func order_rebate_2

    
    
    /**
     * 根据充值月统计进行返利
     * //----------start 这里写上该返利的具体政策----------------
     * PS：仅针对总代级别：1、10万 返5%；2、20万 返6%；3、30万 返7%；4、40万 返8%；5、60万 9%； 6、80万 10%；
     * //----------end 这里写上该返利的具体政策----------------
     * 
     */
    public function order_rebate_3($uid,$dis_info=[],$month=''){
        
        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        import('Lib.Action.User','App');
        $User = new User();
        
        if( empty($dis_info) ){
            $where_dis = array(
                'id'   =>  $uid,
            );

            $dis_info = $User->distributor_obj->where($where_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有获取到用户信息！',
            ];
            return $return_result;
        }
        
        
        $dis_level = $dis_info['level'];
        
        //限制特定级别才可以返利
        if( $dis_level == 2 ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '非特定级别无法返利！',
            ];
            return $return_result;
        }
        
        
        //默认为上个月
        if( empty($month) ){
            $timestamp = strtotime(date('Ym01'));
            $lastmonth = date('Ym', strtotime('-1 day',$timestamp));
            $month = $lastmonth;
        }
        
        
        $rebate_percent = [
            100000  =>  0.05,
            200000  =>  0.06,
            300000  =>  0.07,
            400000  =>  0.08,
            600000  =>  0.09,
            800000  =>  0.1,
        ];
        
//        import('Lib.Action.Order','App');
//        $Order = new Order();
        
        $money_month_count_model = M('money_month_count');
        
        $condition = [
            'uid'   =>  $uid,
            'month' =>  $month,
        ];
        
        $count = $money_month_count_model->where($condition)->select();
        
//        $order_count = $Order->get_order_count([],$condition);
        
        if( empty($count) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '没有获取到统计信息！',
            ];
            return $return_result;
        }
        
        $buy_money = 0;
        
        foreach( $count as $v ){
            $v_money = $v['money'];
            
            $buy_money = bcadd($buy_money, $v_money,2);
        }
        
        
        if( $buy_money <= 0 ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '统计信息获取错误或无统计信息！',
            ];
            return $return_result;
        }
        
        $stage_data = array_keys($rebate_percent);
        $stage_data_key = binarySearch($stage_data,$buy_money);
        
        $key = $stage_data[$stage_data_key];
        $percent = $rebate_percent[$key];
        $percent_fm = $percent*100;
        
        $rebate_money = bcmul($buy_money, $percent,2);
        
        $set_info = $month.'月统计充值金额为'.$buy_money.',返利百分比为：'.$percent_fm.'%';
        
        $rerebate_info = array(
            'user_id'   => $uid, //获利人id
            'x_id'      => $uid,
            'pay_id'    =>  0,
            'time' => time(),
            'money' => $rebate_money,
            'set_info' => $set_info,
            'status' => 0, //默认0为未审核
            'type'  =>  3,
        );

        $rerebate_obj = M('Rerebate');
        $add_res = $rerebate_obj->add($rerebate_info);
        
        if( !$add_res ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '添加返利失败！',
            ];
            return $return_result;
        }
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '添加返利成功！',
        ];
        return $return_result;
    }//end func order_rebate_3

    
    /**
     * 根据订单月统计进行返利
     * //----------start 这里写上该返利的具体政策----------------
     * PS：
     * //----------end 这里写上该返利的具体政策----------------
     * 
     */
    public function order_rebate_4($uid,$dis_info=[],$month=''){
        
        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        import('Lib.Action.User','App');
        $User = new User();
        
        if( empty($dis_info) ){
            $where_dis = array(
                'id'   =>  $uid,
            );

            $dis_info = $User->distributor_obj->where($where_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有获取到用户信息！',
            ];
            return $return_result;
        }
        
        
        $dis_level = $dis_info['level'];
        
        //限制特定级别才可以返利
        if( $dis_level == 2 ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '非特定级别无法返利！',
            ];
            return $return_result;
        }
        
        
        //默认为上个月
        if( empty($month) ){
            $timestamp = strtotime(date('Ym01'));
            $lastmonth = date('Ym', strtotime('-1 day',$timestamp));
            $month = $lastmonth;
        }
        
        
        $rebate_percent = [
            100000  =>  0.05,
            200000  =>  0.06,
            300000  =>  0.07,
            400000  =>  0.08,
            600000  =>  0.09,
            800000  =>  0.1,
        ];
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        $condition = [
            'uid'   =>  $uid,
            'month' =>  $month,
            'day'   =>  0,
            'pid'   =>  0,
        ];
        
        $order_count = $Order->get_order_count([],$condition);
        
        if( empty($order_count) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '没有获取到统计信息！',
            ];
            return $return_result;
        }
        
        $order_count_info = $order_count['list'];
        $count = $order_count['count'];

        if( $count != 1 ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '统计信息获取错误！',
            ];
            return $return_result;
        }
        $buy_money = $order_count_info[0]['buy_money'];
        $buy_num = $order_count_info[0]['buy_num'];
        
        
        if( $buy_money <= 0 ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '统计信息获取错误或无统计信息！',
            ];
            return $return_result;
        }
        
        $stage_data = array_keys($rebate_percent);
        $stage_data_key = binarySearch($stage_data,$buy_money);
        
        $key = $stage_data[$stage_data_key];
        $percent = $rebate_percent[$key];
        $percent_fm = $percent*100;
        
        $rebate_money = bcmul($buy_money, $percent,2);
        
        $set_info = $month.'月统计订单金额为'.$buy_money.',返利百分比为：'.$percent_fm.'%';
        
        $rerebate_info = array(
            'user_id'   => $uid, //获利人id
            'x_id'      => $uid,
            'pay_id'    =>  0,
            'time' => time(),
            'money' => $rebate_money,
            'set_info' => $set_info,
            'status' => 0, //默认0为未审核
            'type'  =>  4,
        );

        $rerebate_obj = M('Rerebate');
        $add_res = $rerebate_obj->add($rerebate_info);
        
        if( !$add_res ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '添加返利失败！',
            ];
            return $return_result;
        }
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '添加返利成功！',
        ];
        return $return_result;
    }//end func order_rebate_4
    
    
    /**
     * 1.订单返利，针对所有级别平级推荐和跨级推荐获得首次下单金额5%的返利（首次下单有金额限制，具体看制度图片）
     */
    public function order_rebate_5($uid,$dis_info=[],$order_info){
        
        if( empty($uid) || empty($order_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        $condition_is_first = [
            'user_id'   =>  $uid,
            'status'    =>  ['neq',0],
        ];
        
        $order_result = $this->order_obj->where($condition_is_first)->field('id')->find();
        
        if( !empty($order_result) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '非首次下单，无法返利！',
            );
            return $return_result;
        }
        
        import('Lib.Action.User','App');
        $User = new User();
        
        if( empty($dis_info) ){
            
            
            $where_dis = array(
                'id'   =>  $uid,
            );

            $dis_info = $User->distributor_obj->where($where_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有获取到用户信息！',
            ];
            return $return_result;
        }
        
        
        
        $partner_id = $dis_info['pid'];
        $recommendID = $dis_info['recommendID'];
        $isRecommend = $dis_info['isRecommend'];
        $dis_level = $dis_info['level'];
        $internal_level = $dis_info['internal_level'];
        
        $order_num = $order_info['0']['order_num'];
        $order_total_price = $order_info['0']['total_price']; //该订单总货款
        $order_month = $order_info['0']['month']; //返利月份根据其订单生成月份
        
        
        if( empty($recommendID) ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '推荐人为总部！',
            ];
            return $return_result;
        }
        
        
        $rec_info = $User->distributor_obj->where(['id'=>$recommendID])->find();
        
        if( empty($rec_info) ){
            $return_result = [
                'code'  =>  7,
                'msg'   =>  '查无推荐人！',
            ];
            return $return_result;
        }
        
        $rec_level = $rec_info['level'];
        
        if( $dis_level > $rec_level ){
            $return_result = [
                'code'  =>  8,
                'msg'   =>  '推荐人级别比被推荐人高无法返利！',
            ];
            return $return_result;
        }
        
        
        $rebate_order_setting = M('rebate_order_setting');
        $rebate_order_set_info = $rebate_order_setting->where(array('id'=>'1'))->find();
        
        $rebate_order_key = 'level'.$dis_level;
        $rebate_percent = isset($rebate_order_set_info[$rebate_order_key])?$rebate_order_set_info[$rebate_order_key]:0;
        
        $set_info = '返利比例：'.$rebate_percent;
        
        $rerebate_money = bcmul($order_total_price,$rebate_percent,2);
        
        if( $rerebate_money > 0 ){
            $rerebate_info = array(
                'order_num' => $order_num,
                'user_id' => $recommendID, //获利人id
                'x_id' => $uid,
                'time' => time(),
                'money' => $rerebate_money,
                'set_info' => $set_info,
                'month' => $order_month,
                'status' => 0, //默认0为未审核
                'type'  =>  5,
            );
            
            $add_res = $this->rerebate_model->add($rerebate_info);
            
            if( $add_res ){
                $return_result = [
                    'code'  =>  1,
                    'msg'   =>  '添加返利成功！',
                ];
                return $return_result;
            }
        }
        
        $return_result = [
            'code'  =>  5,
            'msg'   =>  '返利失败！',
        ];
        return $return_result;
        
    }//end func order_rebate_5
    
    
    
    public function order_rebate_6(){
        
        
        
    }//end func order_rebate_6


    //==========================end 订单返利（系统相关操作触发位置）==================================
    
    
    //==========================订单返利（实际的返利逻辑）==================================
    
    
    
    public function order_temp_num_rebate(){
        
        
        
    }//end func order_temp_num_rebate
    
    
    
    //==========================订单返利（实际的返利逻辑）==================================
    
    
    
    
    
    //==========================推荐返利（系统相关操作触发位置）==================================
    
    
    /**
     * 总部后台订单确认时返利
     * @param type $uid
     * @param type $dis_info
     * @return string|int
     */
    public function radmin_user_audit_rebate($uid,$dis_info=array()){
        $result = $this->audit_rebate($uid,$dis_info);
        
        return $result;
    }//end func radmin_user_audit_rebate
    
    
    /**
     * 经销商后台用户确认时返利
     * @param type $uid
     * @param type $dis_info
     * @return string|int
     */
    public function admin_user_audit_rebate($uid,$dis_info=array()){
        $result = $this->audit_rebate($uid,$dis_info);
        
        return $result;
    }//end func admin_user_audit_rebate
    
    
    
    
    /**
     * 实际的推荐返利
     */
    public function audit_rebate($uid,$dis_info){
        import('Lib.Action.NewRebate','App');
        $rebate = new NewRebate();
        //低推高一次性返利
        $rebate->once_rebate($dis_info, $rebate->once_rebate);
        
        //高发展低一次性返利
        $rebate->once_rebate($dis_info, $rebate->development_rebate);

        //平级发展一次性返利
        $rebate->once_rebate($dis_info, $rebate->same_development_rebate);
//        return $this->recommend_rebate_1($uid,$dis_info);
    }//end func audit_rebate
    
    
    
    /**
     * 推荐返利（以推荐发展计算返利的模式）
     * //----------start 这里写上该返利的具体政策----------------
     * 低级别推荐第二高级别的一级推荐返利
     * 来源：邦程项目
     * 
     * //----------end 这里写上该返利的具体政策----------------
     * 
     */
    public function recommend_rebate_1($uid,$dis_info){
        
        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误'
            );
            return $return_result;
        }
        
        $recommend_setting = M('recommend_setting');
        $distributor_obj = M('distributor');
        
        if( empty($dis_info) ){
            $condition_dis = array(
                'id'    =>  $uid,
            );
            $dis_info = $distributor_obj->where($condition_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '参数错误'
            );
            return $return_result;
        }
        
        
        
        $level = $dis_info['level'];
        
        $recommend_set_info = $recommend_setting->where(array('id'=>'1'))->find();

        if( empty($recommend_set_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有推荐返利设置！'
            );
            return $return_result;
        }
        
        $recommend_set_key = 'level'.$level;
        //推荐返利金额
        $recommend_money = isset($recommend_set_info[$recommend_set_key])?$recommend_set_info[$recommend_set_key]:0;
        
        if ($dis_info['level'] == 2) {
            $recommend = M('recommend_rebate');
            $field = 'id,recommendID,level';
            $le = $distributor_obj->field($field)->where(array('id' => $dis_info['recommendID']))->find();
            if ($le['level'] > 2) {
                if( $recommend_money > 0 ){
                    $add = array(
                        'user_id' => $le['id'],
                        'x_id' => $dis_info['id'],
                        'time' => time(),
                        'money' => $recommend_money,
                        'status' => 0,
                        'day' => date('Ymd'),
                        'month' => date('Ym')
                    );
                    $recommend->add($add);
                }


                $return_result = array(
                    'code'  =>  1,
                    'msg'   =>  '返利成功！'
                );
            }
            return $return_result;
        }
        
    }//end func recommend_rebate_1
    
    
    /**
     * 多级推荐返利
     * //----------start 这里写上该返利的具体政策----------------
     * 低级别推荐第二高级别的一级推荐返利
     * 来源：JW项目
     * 推荐一次性奖励
     * ①总代理A推荐总代理B，总代理B推荐总代理C，总代理C推荐总代理D。C获得一级推荐返利5000，B获得二级推荐返利2000，A获得三级推荐返利3000；
     * ②创始人A推荐创始人B，创始人B推荐创始人C，创始人C推荐创始人D。C获得一级推荐返利10000，B获得二级推荐返利5000，A获得三级推荐返利6000；
     * ③创始人A推荐总代理B，总代理B推荐总代理C，总代理C推荐总代理D。C获得一级推荐返利5000，B获得二级推荐返利2000，A获得三级推荐返利3000；
     * //----------end 这里写上该返利的具体政策----------------
     */
    public function recommend_rebate_2($uid,$dis_info){
        
        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误'
            );
            return $return_result;
        }
        
        $recommend_setting = M('recommend_setting');
        $distributor_obj = M('distributor');
        $recommend = M('recommend_rebate');
        
        $field = 'id,level,recommendID';
        
        if( empty($dis_info) ){
            $condition_dis = array(
                'id'    =>  $uid,
            );
            $dis_info = $distributor_obj->where($condition_dis)->field($field)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '参数错误'
            );
            return $return_result;
        }
        
        
        
        $level = $dis_info['level'];
        $recommendID = $dis_info['recommendID'];
        
        if( empty($recommendID) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '推荐人为总部，不进行下一步！'
            );
            return $return_result;
        }
        
        $recommend_set_info = $recommend_setting->where(array('id'=>'1'))->find();

        if( empty($recommend_set_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有推荐返利设置！'
            );
            return $return_result;
        }
        
        //_2或_3代表层级
        $level1 = $recommend_set_info['level1'];
        $level1_2 = $recommend_set_info['level1_2'];
        $level1_3 = $recommend_set_info['level1_3'];
        $level2 = $recommend_set_info['level2'];
        $level2_2 = $recommend_set_info['level2_2'];
        $level2_3 = $recommend_set_info['level2_3'];
        
        
        $add = array(
            'user_id' => $recommendID,
            'x_id' => $uid,
            'time' => time(),
            'status' => 0,
            'day' => date('Ymd'),
            'month' => date('Ym')
        );
        
        if( $level == 1 ){
            $add['money'] = $level1;
            $recommend->add($add);
        }
        elseif( $level == 2 ){
            $add['money'] = $level2;
            $recommend->add($add);
        }
        
        $condition_rec1['id'] = $recommendID;
        $recommend1 = $distributor_obj->where($condition_rec1)->field($field)->find();
        
        $recommendID2 = $recommend1['recommendID'];
        
        $condition_rec2['id'] = $recommendID2;
        $recommend2 = $distributor_obj->where($condition_rec2)->field($field)->find();
        
        if( empty($recommend2) ){
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '没有找到二级推荐人！'
            );
            return $return_result;
        }
        
        
        $add = array(
            'user_id' => $recommendID2,
            'x_id' => $uid,
            'time' => time(),
            'status' => 0,
            'day' => date('Ymd'),
            'month' => date('Ym')
        );
        
        if( $level == 1 ){
            $add['money'] = $level1_2;
            $recommend->add($add);
        }
        elseif( $level == 2 ){
            $add['money'] = $level2_2;
            $recommend->add($add);
        }
        
        
        $recommendID3 = $recommend2['recommendID'];
        
        if( empty($recommendID3) ){
            $return_result = array(
                'code'  =>  7,
                'msg'   =>  '推荐人为总部，不进行下一步！'
            );
            return $return_result;
        }
        
        
        $add = array(
            'user_id' => $recommendID3,
            'x_id' => $uid,
            'time' => time(),
            'status' => 0,
            'day' => date('Ymd'),
            'month' => date('Ym')
        );
        
        if( $level == 1 ){
            $add['money'] = $level1_3;
            $recommend->add($add);
        }
        elseif( $level == 2 ){
            $add['money'] = $level2_3;
            $recommend->add($add);
        }
        
        
    }//end func recommend_rebate_2
    
    
    
    //==========================end 推荐返利（系统相关操作触发位置）==================================



    //==========================充值返利（系统相关操作触发位置）==================================


    /**
     * 充值成功时返利
     *  * //----------start 这里写上该返利的具体政策----------------
     * 总代2层返利，第一层6%，第二层4%，省代、市代、特约只有1层返利，返利都是由上家支付，没上家就是公司（返利比例后台设置）
     * 来源：邦程项目
     *
     * //----------end 这里写上该返利的具体政策----------------
     * @param type $uid
     * @param type $recharge_money
     * @return string|int
     */
    public function apply_rebate_1($uid,$recharge_money){

        $distributor =M('distributor');
        $rerebate=M('rebate_apply');
        $rebate_apply_setting=M('rebate_apply_setting');

        //申请充值的金额


        //申请人id
        $condition_id=[
            'id' => $uid,
        ];
        //申请人信息
        $condition_info=$distributor->where($condition_id)->find();
        $condition_info_level=$condition_info['level'];
        $condition_info_pid=$condition_info['pid'];
        $condition_info_p_recommedID=$condition_info['recommendID'];
        //一级返利人id
        $condition_p_id=[
            'id' => $condition_info_p_recommedID,
        ];
        //一级返利人信息
        $condition_info_partner=$distributor->where($condition_p_id)->find();
        $condition_info_partner_id=$condition_info_partner['id'];
        $condition_info_partner_level=$condition_info_partner['level'];
        $condition_info_partner_p_recommendID=$condition_info_partner['recommendID'];
        $condition_info_partner_pid=$condition_info_partner['pid'];

        $can_rebate_levels = array(2,3,4,5);

        if( !in_array($condition_info_level, $can_rebate_levels) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '不在返利等级范围！',
               );
            return $return_result;
        }
        if($condition_info_p_recommedID == 0){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '推荐人为总部，不进行下一步操作！',
            );
            return $return_result;
        }

        if($condition_info_level != $condition_info_partner_level){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '等级不同，不能进行返利！',
            );
            return $return_result;
        }
        //二级返利人信息
            $condition_info_partner_p_id=[
                'id' => $condition_info_partner_p_recommendID
            ];
            $dis_info=$distributor->where($condition_info_partner_p_id)->find();
            $dis_info_id=$dis_info['id'];
            $dis_info_level=$dis_info['level'];
            $dis_info_pid=$dis_info['pid'];

        if($condition_info_partner_level == $condition_info_level){
            //一层返利比例
            $rebate_order_set_info = $rebate_apply_setting->where(array('id'=>'1'))->find();
            $rebate_money_key = 'rebate1_level'.$condition_info_level;
            $rebate_percent = isset($rebate_order_set_info[$rebate_money_key])?$rebate_order_set_info[$rebate_money_key]:0;
            $rebate_percent_bcdiv=bcdiv($rebate_order_set_info[$rebate_money_key],100,2);
            //返利金额
            $rebate_money1=bcmul($recharge_money,$rebate_percent_bcdiv,2);
            //返利信息
            $set_info = '充值'.$recharge_money.','.'返利比例：'.$rebate_percent_bcdiv;

            $rerebate1_info = array(
                'user_id' => $condition_info_partner_id, //获利人id
                'x_id' => $uid,
                'money' => $rebate_money1,
                'set_info' => $set_info,
                'pay_id'    =>  $condition_info_pid,
                'month' => date(Ym),
                'day' => date(Ymd),
                'time' => time(),
                'status' => 0, //默认0为未审核
            );
               $add_res = $rerebate->add($rerebate1_info);
                if($dis_info_level == 2){
                    $rebate_money2_key = 'rebate2_level'.$condition_info_level;
                    $rebate2_percent = isset($rebate_order_set_info[$rebate_money2_key])?$rebate_order_set_info[$rebate_money2_key]:0;
                    $rebate2_percent_bcdiv=bcdiv($rebate_order_set_info[$rebate_money2_key],100,2);
                    //返利金额
                    $rebate_money2=bcmul($recharge_money,$rebate2_percent_bcdiv,2);
                    //返利信息
                    $set2_info = '充值'.$recharge_money.','.'返利比例：'.$rebate2_percent_bcdiv;
                    $rerebate2_info = array(
                        'user_id' => $dis_info_id, //获利人id
                        'x_id' => $uid,
                        'money' => $rebate_money2,
                        'set_info' => $set2_info,
                        'pay_id'    =>  $condition_info_pid,
                        'month' => date(Ym),
                        'day' => date(Ymd),
                        'time' => time(),
                        'status' => 0, //默认0为未审核
                    );
                    $add_res = $rerebate->add($rerebate2_info);
                }
        }
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '返利逻辑完！',
            'add_res' => $add_res,
        );

        return $return_result;


    }//end func radmin_apply_audit_rebate

}