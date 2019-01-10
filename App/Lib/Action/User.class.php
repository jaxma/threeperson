<?php
//用户管理的模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class User extends Common{


    public $is_multilayer = FALSE;//是否多层级，选择TRUE，该系统必须有“代理关系表”

    public $has_user_bind = TRUE;//是否生成用户关系

    public $is_cycle_multilayer = TRUE;//是否使用有限制次数的循环获得多层代理关系

    public $open_upgrade_apply = TRUE;//是否开启代理申请升级
    public $upgrade_apply_aduit = 1;//1为上级审核，0为总部审核
    
    public $check_phone_only = TRUE;//是否检查手机的唯一性
    
    public $is_same_level_superior = FALSE;//是否平级可以做为上级true平级可以作为上级


    //升级申请的状态
    public $upgrade_apply_status = [
        0   =>  '待上级审核',
        1   =>  '待总部审核',
        2   =>  '已审核',
        3   =>  '不通过',
    ];
    
    
    public $distributor_obj;
    public $distributor_bind_obj;
    public $distributor_upgrade_apply_obj;
    
    /**
     * 架构函数
     */
    public function __construct() {
        import("Wechat.Wechat", APP_PATH);
        $options = array(
            'token' => C('APP_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        $this->wechat_obj = new Wechat($options);


        $this->distributor_obj = M('distributor');
        $this->distributor_bind_obj = M('distributor_bind');
        $this->distributor_upgrade_apply_obj = M('distributor_upgrade_apply');
        
        $extra = C('extra');
        if( isset($extra['user']) ){
            $extra_info = $extra['user'];
            
            if( isset($extra_info['is_multilayer']) ){
                $this->is_multilayer = $extra_info['is_multilayer'];
            }
            if( isset($extra_info['has_user_bind']) ){
                $this->has_user_bind = $extra_info['has_user_bind'];
            }
            if( isset($extra_info['open_upgrade_apply']) ){
                $this->open_upgrade_apply = $extra_info['open_upgrade_apply'];
            }
            if( isset($extra_info['upgrade_apply_aduit']) ){
                $this->open_upgrade_apply = $extra_info['upgrade_apply_aduit'];
            }
            if( isset($extra_info['check_phone_only']) ){
                $this->check_phone_only = $extra_info['check_phone_only'];
            }
        }
        
    }



    //-----------------获取用户信息---------------------
    
    //获取用户的团队，这里也是定义团队的逻辑
    public function get_user_team($uid,$dis_info=array(),$has_myself=FALSE){
        
        if( empty($uid) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            return $return_result;
        }
        
        
        $level_num = C('LEVEL_NUM');
        
        if( empty($dis_info) ){
            $where_dis = array(
                'id'   =>  $uid,
            );

            $dis_info = $this->distributor_obj->where($where_dis)->find();
        }
        
        if( empty($dis_info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有获取到用户信息！',
            ];
            return $return_result;
        }

        
        $dis_level = $dis_info['level'];

        
        $team_info = array();

        //最低级别不用查询团队统计信息
        if( $dis_level == $level_num ){

            $team_info = array();

        }elseif( $this->is_multilayer ){//多层级团队

            $dis_bind_level_kdy = 'level'.$dis_level;
            $condition_dis_bind[$dis_bind_level_kdy] = $uid;

            $dis_bind_info = $this->distributor_bind_obj->where($condition_dis_bind)->select();


            if( !empty($dis_bind_info) ){
                $team_uids = array();
                foreach( $dis_bind_info as $k_bind => $v_bind ){
                    $team_uids[] = $v_bind['uid'];
                }

                $where_team = array(
                    'pid'   =>  array('in',$team_uids),
                    'audited' => 1,
                );

                $team_info = $this->distributor_obj->where($where_team)->order('time desc')->select();
            }

        }
        else if( $this->is_cycle_multilayer ){//使用有限制的循环获得多层代理

            $for_max = 100;

            $for_uids = array(
                $uid
            );


            for( $i=1;$i<=$for_max;$i++ ){


                $where_team = array(
                    'pid'   =>  array('in',$for_uids),
                    'audited' => 1,
                );

                $the_team_info = $this->distributor_obj->where($where_team)->select();

                if( empty($the_team_info) ){
                    break;
                }

                $for_uids = array();

                foreach( $the_team_info as $k => $v ){
                    $team_info[] = $v;

                    $for_uids[] = $v['id'];
                }
            }
        }
        else{//单层级团队
            $where_team = array(
                'pid'   =>  $uid,
                'audited' => 1,
            );

            $team_info = $this->distributor_obj->where($where_team)->select();
        }

        //团队人数
        $team_num = count($team_info);

        if( $has_myself ){
            //团队信息增加自己
            $team_info[] = $dis_info;
        }

        $team_uids = array();
        $team_dis_info = array();
        $team_level_num = array();

        for( $lev_i=1;$lev_i<=$level_num;$lev_i++ ){
            $team_level_num[$lev_i] = 0;
        }


        foreach( $team_info as $k => $v ){
            $v_uid = $v['id'];
            $v_level = $v['level'];
            $team_uids[] = $v_uid;
            $team_dis_info[$v_uid]  =   $v;
            for($le=1;$le<=$level_num;$le++){
                if($v_level == $le){
                    $team_level_num[$v_level]+=1;
                }
            }
            //本人的不计算团队级别人数的信息里
            if( $v_uid != $uid ){
                $team_level_num[$v_level] = empty($team_level_num[$v_level])?1:$team_level_num[$v_level]++;
            }

        }


        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取到用户信息！',
            'team_info' =>  $team_info,
            'team_num'  =>  $team_num,
            'team_uids' =>  $team_uids,
            'team_dis_info' =>  $team_dis_info,
            'team_level_num'=>  $team_level_num,
        ];
        return $return_result;
        
    }//end func get_user_team


    //获取用户信息
    public function get_distributor($page_info=array(),$condition=array()){

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        //$level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        $level_num_max = C('LEVEL_NUM_MAX');

        $count = $this->distributor_obj->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->distributor_obj->where($condition)->order('convert(name using gb2312) asc')->page($page_con)->select();

            }
            else{
                $list = $this->distributor_obj->where($condition)->order('convert(name using gb2312) asc')->select();

            }


            foreach( $list as $k => $v ){
                $v_time = $v['time'];

                $list[$k]['time_format'] = date('Y-m-d H:i',$v_time);
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
    }//end func get_distributor


    //获取充值记录
    public function get_distributor_bind($page_info=array(),$condition=array()){
        $distributor_bind_obj = M('distributor_bind');
        $distributor_obj = M('distributor');


        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        //$level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        $level_num_max = C('LEVEL_NUM_MAX');

        $count = $distributor_bind_obj->where($condition)->count();
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $distributor_bind_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $distributor_bind_obj->where($condition)->order('id desc')->select();
            }

            //-----整理添加相应其它表的信息-----
            $uids = array();

            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($uids[$v_pid]) ){
                    $uids[$v_pid] = $v_pid;
                }

                for( $i=$level_num;$i>0;$i-- ){
                    $bind_level_key = 'level'.$i;
                    if( !isset($uids[$bind_level_key]) ){
                        $uids[$bind_level_key] = $v_pid;
                    }
                }

            }

            array_values($uids);

            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );

            $field = 'id,name,levname';

            $dis_info = $distributor_obj->field($field)->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            $dis_key_info['0']['levname'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;
            }


            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_u_level = $v['u_level'];
                $v_p_level = $v['p_level'];
                $v_updated = $v['updated'];


                for( $i=$level_num;$i>0;$i-- ){
                    $bind_level_key = 'level'.$i;
                    $bind_level_key_name = $bind_level_key.'_name';
                    $bind_level_uid = $v[$bind_level_key];

                    $list[$k][$bind_level_key_name] = $dis_key_info[$bind_level_uid]['name'];
                }

//                $list[$k]['u_info'] = $dis_key_info[$v_uid];
//                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['p_name'] = $dis_key_info[$v_pid]['name'];
                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['p_levname'] = $dis_key_info[$v_pid]['levname'];
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
    }//end func get_distributor_bind

    //-----------------end 获取用户信息---------------------



    //-----------------获取用户统计信息---------------------

    /**
     * 获取用户订单统计信息
     * @param type $condition
     * @param type $condition_user
     * @param type $page_info
     * @return array|int
     */
    public function get_users_order_count($condition=array(),$condition_user=array(),$page_info=array()){

        //各级别最低消费
        $min_chart = array(
            1   =>  72900,
            2   =>  24300,
            3   =>  8100,
            4   =>  2700,
            5   =>  900,
            6   =>  300,
            7   =>  0,
        );

//        $level_name = C('LEVEL_NAME');//级别名称

        $order_obj = M('Order');
        $distributor_obj = M('distributor');


        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        $count = 0;
        $page = '';



        if( !empty($page_info) ){
            $page_con = $page_num.','.$page_list_num;

            $count = $distributor_obj->where($condition_user)->count();

            $distributor_info = $distributor_obj->where($condition_user)->page($page_con)->select();

            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        else{
            $distributor_info = $distributor_obj->where($condition_user)->select();
        }


        if( empty($distributor_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有找到用户信息！',
            );

            return $return_result;
        }


        $uids = array();

        foreach( $distributor_info as $k_dis => $v_dis ){
            $v_dis_uid = $v_dis['id'];

            $uids[] = $v_dis_uid;

        }


        $condition_order = array(
            'user_id'   =>  array('in',$uids),
            'status'    =>  array('in',array('2','3','6')),
        );

        $this_month = '';

        //如果有选择月份
        if( isset($condition['month']) && strlen($condition['month'])==6 && is_numeric($condition['month']) ){

            $this_month = $condition['month'];
            $this_month_first_day = $this_month.'01';
            $this_month_first_day = $this->get_day_time_tmp($this_month_first_day);
            $next_month_first_day = $this->get_next_month_first_day($this_month);
            $this_month_last_time = $next_month_first_day - 1;

            $condition_order['paytime'] = array(array('gt',$this_month_first_day),array('lt',$this_month_last_time),'and');
        }

        $month = $this_month;


        //订单信息
        $order_info = $order_obj->where($condition_order)->order('time desc')->group('order_num')->select();


        $user_total_price = array();//各用户的订单金额

        foreach( $order_info as $k_order => $v_order ){
            $v_order_user_id = $v_order['user_id'];//下单人ID
            $v_order_user_level = $v_order['user_level'];//下单人级别
            $v_order_total_price = $v_order['total_price'];//订单总金额

            if( !isset($user_total_price[$v_order_user_id]) ){
                $user_total_price[$v_order_user_id] = 0;
            }

            $user_total_price[$v_order_user_id] = bcadd($user_total_price[$v_order_user_id],$v_order_total_price,2);

        }//end foreach

        $is_get_team_info = isset($condition['is_get_team_info'])?$condition['is_get_team_info']:FALSE;

        //写入用户数组用于显示
        foreach( $distributor_info as $k_dis => $v_dis ){
            $v_dis_uid = $v_dis['id'];
            $v_dis_level = $v_dis['level'];
            $v_dis_levname = $v_dis['levname'];

            $the_user_total_price = isset($user_total_price[$v_dis_uid])?$user_total_price[$v_dis_uid]:0;//用户订单金额
            $the_user_alert_info = '';//用户告警信息

            $the_min_chart = $min_chart[$v_dis_level];
            $the_min_dif = bcsub($the_min_chart,$the_user_total_price,2);//与最低消费相差金额

            if( $the_min_dif < 0 ){
                $the_min_dif = 0;
            }

            $team_user_order_total = 0;//团队进货量
            $user_team_info = array();//团队信息

            //是否获取该用户的团队信息
            if( $is_get_team_info ){

                $user_team_count_info = $this->get_user_team_count($v_dis_uid,$condition);

                if( $user_team_count_info['code'] == 1 ){
                    $team_user_order_total = $user_team_count_info['result']['team_money'];
                    $user_team_info = $user_team_count_info['result'];
                }

            }

            //告警信息
            if( bccomp($the_user_total_price,$the_min_chart,2) == -1 ){
                $the_user_alert_info = '未达到级别--'.$v_dis_levname.'的最低消费:'.$the_min_chart;
            }

            $distributor_info[$k_dis]['order_total']   =   $the_user_total_price;
            $distributor_info[$k_dis]['min_chart']   =   $the_min_chart;
            $distributor_info[$k_dis]['min_dif']   =   $the_min_dif;
            $distributor_info[$k_dis]['alert_info']   =   $the_user_alert_info;
            $distributor_info[$k_dis]['team_order_total']   =   $team_user_order_total;
            $distributor_info[$k_dis]['user_team_info']   =   $user_team_info;
        }


        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '查询成功！',
            'result'    =>  array(
                'dis_info'  =>  $distributor_info,
                'user_total_price'  =>  $user_total_price,
                'uids'  =>  $uids,
                'month' =>  $month,
                'page'  =>  $page,
                'count' =>  $count,
                'limit' => $page_list_num,
                
            ),
        );

        return $return_result;
    }//end func get_users_order_count



    /**
     * 获取某经销商团队统计
     * @param int $uid
     * @param array $condition
     * @param array $has_myself
     * @return array
     */
    public function get_user_team_count($uid,$condition=array(),$has_myself=FALSE){



        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );

            return $return_result;
        }



        $distributor_obj = M('distributor');
        $order_obj = M('order');

        $level_num = C('LEVEL_NUM');

        $where_dis = array(
            'id'   =>  $uid,
        );

        $dis_info = $distributor_obj->where($where_dis)->find();

        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '查无此经销商！',
            );
            return $return_result;
        }

        $dis_level = $dis_info['level'];

//        if( $dis_level == $level_num ){
//            $return_result = array(
//                'code'  =>  4,
//                'msg'   =>  '最低级别无法查询团队统计信息！',
//            );
//            return $return_result;
//        }




        $user_team_result = $this->get_user_team($uid,$dis_info,$has_myself);

        if( $user_team_result['code'] != 1 ){
            return $user_team_result;
        }
        $team_info = $user_team_result['team_info'];
        $team_num = $user_team_result['team_num'];
        $team_uids = $user_team_result['team_uids'];
        $team_dis_info = $user_team_result['team_dis_info'];
        $team_level_num = $user_team_result['team_level_num'];

//        if( empty($team_info) ){
//            $return_result = array(
//                'code'  =>  4,
//                'msg'   =>  '没有找到团队的信息！',
//            );
//            return $return_result;
//        }
        


        $where_order = array(
            'user_id'   =>  array('in',$team_uids),
            //'status'    =>  array('in',array('2','3')),
        );

        //如果有选择月份
        if( isset($condition['month']) && strlen($condition['month'])==6 && is_numeric($condition['month']) ){

            $this_month = $condition['month'];
            $this_month_first_day = $this_month.'01';
            $this_month_first_day = $this->get_day_time_tmp($this_month_first_day);
            $next_month_first_day = $this->get_next_month_first_day($this_month);
            $this_month_last_time = $next_month_first_day - 1;

            $where_order['paytime'] = array(array('gt',$this_month_first_day),array('lt',$this_month_last_time),'and');
        }

        $order_info = $order_obj->where($where_order)->group('order_num')->select();


        $order_valid_status = array(2,3);//用于统计的订单状态

        $order_total_money = 0;//团队订单总金额
        $order_profit = 0;//团队总利润

        $order_user_total_money = array();//团队各经销商的总金额
        $order_user_profit = array();//各经销商的上级订单利润

        //status开头后面数字的键根据订单模块定义订单状态，代表该状态的数量
        //我的订单信息
        $my_order_info = array(
            'total' =>  0,          //订单总量
            'total_money'   =>  0,  //订单总金额
            'total_num'     =>  0,  //购买产品总量
        );
        //我的团队订单信息
        $team_order_info = array(
            'total' =>  0,          //订单总量
            'total_money'   =>  0,  //订单总金额
            'total_num'     =>  0,  //购买产品总量
        );


        if( !empty($order_info) ){
            //我接单的订单状态
            $my_order_status = array();


            foreach( $order_info as $k_order => $v_order ){

                $v_order_user_id = $v_order['user_id'];
                $v_order_o_id   = $v_order['o_id'];     //接单经销商
                $v_order_status = $v_order['status'];
                $v_order_total_num = $v_order['total_num'];//订单总数
                $v_order_total_price = $v_order['total_price'];//订单总金额
                $v_order_total_par_profit = $v_order['total_par_profit'];//上级总利润

                $status_key = 'status'.$v_order_status;

                //我为接单经销商
                if( $v_order_o_id == $uid ){
                    $my_order_status[$status_key] = isset($my_order_status[$status_key])?$my_order_status[$status_key]++:1;
                }

                if( $v_order_user_id == $uid && !$has_myself ){
                    $my_order_info['total']++;
                    $my_order_info['total_money']+= $v_order_total_price;
                    $my_order_info['total_num']+= $v_order_total_num;

                    $my_order_info[$status_key] = isset($my_order_info[$status_key])?$my_order_info[$status_key]++:1;
                }
                else{
                    $team_order_info['total']++;
                    $team_order_info['total_money']+= $v_order_total_price;
                    $team_order_info['total_num']+= $v_order_total_num;

                    $team_order_info[$status_key] = isset($team_order_info[$status_key])?$team_order_info[$status_key]++:1;
                }


                //如果团队不计算自己
                if( $v_order_user_id == $uid ){
                    continue;
                }

                //非用于统计的订单状态不进行
                if( !in_array($v_order_status,$order_valid_status) ){
                    continue;
                }


                //团队订单金额
                if( !isset($order_user_total_money[$v_order_user_id]) ){
                    $order_user_total_money[$v_order_user_id] = 0;
                }
                $order_user_total_money[$v_order_user_id] += $v_order_total_price;

                $order_total_money+=$v_order_total_price;

                //团队利润
                if( !isset($order_user_profit[$v_order_user_id]) ){
                    $order_user_profit[$v_order_user_id] = 0;
                }
                $order_user_profit[$v_order_user_id] += $v_order_total_par_profit;

                $order_profit+=$v_order_total_par_profit;
            }
        }
//        else{
//            $return_result = array(
//                'code'  =>  5,
//                'msg'   =>  '没有找到团队订单的信息！',
//                'team_uids' =>  $team_uids,
//            );
//            return $return_result;
//        }



        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '',
            'result'    =>  array(
                'team_num'      =>  $team_num,                  //团队人数
                'team_level_num'=>  $team_level_num,            //团队各级别人数
                'team_money'    =>  $order_total_money,         //团队订单总金额
                'user_money'    =>  $order_user_total_money,    //团队各经销商的总金额
                'team_profit'   =>  $order_profit,              //团队总利润
                'user_profit'   =>  $order_user_profit,         //各经销商的上级订单利润
                'team_dis_info' =>  $team_dis_info,             //团队经销商信息
                'my_order_info' =>  $my_order_info,             //我的订单信息
                'team_order_info'   =>  $team_order_info,       //我的团队订单信息
                'my_order_status'   =>  $my_order_status,       //我接单的订单状态
            ),
        );

        return $return_result;
    }//end func get_user_team













    //-----------------end 获取用户统计信息---------------------


    //-----------------获取用户的特别信息---------------

    //获取推荐人代理链上级别比自己高至少一级的经销商
    //一般用于低级别发展高级别时，给经销商寻找一位合适的经销商
    //以下方法不适用于平级作为上级的模式
    public function get_recommend_hight_level_parent($dis_level,$recommend_info){

        if( empty($dis_level) || empty($recommend_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );

            return $return_result;
        }

        $rid = $recommend_info['id'];
        $rec_name = $recommend_info['name'];
        $recommend_level = isset($recommend_info['level'])?$recommend_info['level']:0;
        $recommend_pid = isset($recommend_info['pid'])?$recommend_info['pid']:NULL;

        if( $dis_level == 0 || $recommend_level == 0 ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '级别无法获取！',
            );

            return $return_result;
        }

        if( $recommend_pid == NULL ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '参数错误！',
            );

            return $return_result;
        }

        $distributor_obj = M('distributor');

        $pid = 0;
        $parent_info = array();

        //最高级别的话，上级一般为总部
        if( $dis_level == 1 ){
            $pid = 0;
            $parent_info = array();
        }
        //如果推荐人级别比被推荐人级别低，一般让推荐人作为上级
        else if( $dis_level > $recommend_level ){
            $pid = $rid;
            $parent_info = $recommend_info;

        }
        //一般情况下，两者平级，用推荐人的上级就行
        elseif( $dis_level == $recommend_level ){
            if ($this->is_same_level_superior) {
                //平级可以做为上级
                $pid = $rid;
            } else {
                $pid = $recommend_pid;
            }

//            $condition_dis = array(
//                'id'    =>  $pid,
//            );
//            $parent_info = $distributor_obj->where($condition_dis)->find();
        }
        //有用户关系表的情况下
        else if( $this->has_user_bind ){
            $distributor_bind_model = M('distributor_bind');

            $condition_dis_bind = array(
                'uid'   =>  $rid,
            );

            $dis_bind_info = $distributor_bind_model->where($condition_dis_bind)->find();

            if( empty($dis_bind_info) ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  '参数错误！',
                );

                return $return_result;
            }

            $recommend_pid = isset($dis_bind_info['pid'])?$dis_bind_info['pid']:0;
            $p_level = isset($dis_bind_info['p_level'])?$dis_bind_info['p_level']:0;


            if( $p_level < $dis_level ){
                $pid = $recommend_pid;
            }
            else{
                for( $i=$p_level;$i>0;$i-- ){

                    $level_key = 'level'.$i;

                    if( $i < $dis_level ){
                        $pid = isset($dis_bind_info[$level_key])?$dis_bind_info[$level_key]:0;
                        break;
                    }
                }
            }
        }

        if( $pid != $rid ){
            $condition_par = array(
                'id'    =>  $pid,
            );
            $par_info = $distributor_obj->where($condition_par)->find();

            $par_name = $par_info['name'];
            $par_openid = $par_info['openid'];
            $par_path = $par_info['path'];
            $par_rec_path = $par_info['rec_path'];
            $par_level = $par_info['level'];
            $par_pid = $par_info['pid'];
        }
        else{
            $par_name = $rec_name;
            $par_openid = '';
            $par_rec_path = '';
            $par_level = 0;
            $par_pid = 0;
            $par_rec_path = $par_path = '0';
        }


        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '',
            'info'  =>  array(
                'pid'   =>  $pid,
                'name'  =>  $par_name,
                'openid'=>  $par_openid,
                'path'  =>  $par_path,
                'rec_path'  =>  $par_rec_path,
                'level' =>  $par_level,
                'parpid'=>  $par_pid,
            ),
        );

        return $return_result;
    }//end func get_recommend_hight_level_parent




    //-----------------end 获取用户的特别信息---------------



    //-----------------用户相关业务操作---------------------

    //添加经销商
    public function add($info,$for=''){
        $returnInfo = array();

        if( empty($info) ){
            $return_result = [
                'code'      =>  2,
                'msg'       =>  '提交的信息有误',
            ];
            return $return_result;
        }

        //提交的数据
        $openid = $info['openid'];
        $headimgurl = $info['headimgurl'];
        $pid = $info['pid'];//PID其实是邀请人的信息的ID，一般来说邀请人就是推荐人，是否为上级下面有逻辑判断
        $nickname = $info['nickname'];
        
        $level = $info['level'];
        $name = $info['name'];
        $wechatnum = $info['wechatnum'];
        $phone = $info['phone'];
        $email = $info['email'];
        $idennum = $info['idennum'];
        $address = $info['address'];
        $idennumimg = $info['idennumimg'];
        $liveimg = $info['liveimg'];
        $password = $info['password'];
        $set_audited = $info['audited'];
        $province = $info['province'];
        $city = $info['city'];
        $county=$info['county'];
        $authnum = $info['authnum'];
        $sex = $info['sex'];//性别，微信接口获取，1为男性，2为女性
        $recommendID = isset($info['recommendID'])?$info['recommendID']:$pid; //推荐人
        $recommendname = isset($info['recommendname'])?$info['recommendname']:''; //推荐人姓名
        
        //
        $level_name = C("LEVEL_NAME");
        $distributor_model = $this->distributor_obj;
        
        $GROW_MODEL = C('GROW_MODEL');
        $GROW_MODEL_LEVEL = C('GROW_MODEL_LEVEL');
        $GROW_MODEL_SPCAIL_LEVEL = C('GROW_MODEL_SPCAIL_LEVEL');
        
        
        if( $pid === NULL || !is_numeric($pid) ){
            $return_result = [
                'code'      =>  3,
                'msg'       =>  '找不到您的推荐人信息，请保持良好的网络信号再重试！',
            ];
            return $return_result;
        }
        
        if( $level == NULL ){
            $return_result = [
                'code'      =>  3,
                'msg'       =>  '级别信息不能为空，请保持良好的网络信号再重试！！',
            ];
            return $return_result;
        }
        
        
        if( substr($idennumimg,0,1) == '.' ){
            $idennumimg = substr($idennumimg, 1);
        }
        if( substr($liveimg,0,1) == '.' ){
            $liveimg = substr($liveimg, 1);
        }

        if( empty($recommendID) ){
            $recommendID = 0;
        }
        
        
        
        $parent = [];

        if( $for == 'radmin' && empty($openid) ){
            $openid = 'radmin'.time().md5($info['wechatnum']);
        }
        

        //---------------------start 先查询提交的经销商是否已有--------------------------
        $condition_sear = [
            'openid'    =>  $openid,
            '_logic'    =>  'or',
            'wechatnum'  =>  $wechatnum,
        ];
        $manager = $distributor_model->where($condition_sear)->find();

        if (!empty($manager)) {
            
            if( $manager['openid'] == $openid ){
                setLog('openid' . $openid . '的代理再次提交申请','openid-the_same');
            }
            elseif( $manager['wechatnum'] == $wechatnum ){
                $return_result = [
                    'code'      =>  4,
                    'msg'       =>  '您填写的微信号（'.$manager['wechatnum'].'）已被申请经销商，如有疑问，请联系总部！',
                ];
                return $return_result;
            }
            
            if ($manager['audited'] == 1) {
                //该微信号已是经销商
                $return_result = [
                    'code'      =>  5,
                    'msg'       =>  '您当前的微信（'.$manager['name'].'）已申请成为经销商或还在登录（'.$manager['name'].'），无法再次申请！',
                ];
                return $return_result;
            } else {
                //该微信号待审核
                $return_result = [
                    'code'      =>  8,
                    'msg'       =>  '您当前的微信已申请成为经销商，正在审核中，请耐心等待！',
                ];
                return $return_result;
            }
        }
        //---------------------end 先查询提交的经销商是否已有--------------------------
        
        //---------------------start 查询推荐人的情况--------------------------
        if( !empty($pid) ){
            //查询未来授权人的级别和姓名
            $parent = $distributor_model->where(array('id' => $pid))->find();

            if( empty($parent) ){
                $return_result = [
                    'code'      =>  9,
                    'msg'       =>  '无法找到您的推荐人信息，请让您的推荐人重新登录发送链接！',
                ];
                return $return_result;
            }
            else if( $parent['statu'] == 1 ){
                $return_result = [
                    'code'      =>  10,
                    'msg'       =>  '您的推荐人无法推荐经销商，请联系您的推荐人或总部反馈！',
                ];
                return $return_result;
            }

            //$parent_bossname = $parent['name'];
            $pname = $parent['name'];

            //$parent_pid = $parent['pid'];
            $parent_openid = $parent['openid'];
            $parent_path = $parent['path'];
            $parent_level = $parent['level'];
            $parent_pid = $parent['pid'];
            $parent_rec_path = $parent['rec_path'];
        }
        else{
            $pname = '总部';
            $parent_openid = '';
            $parent_path = 0;
            $parent_level = 0;
            $parent_pid = 0;
            $parent_rec_path = 0;
        }
        
        //一般来说推荐人就是pid获取的一样，但是后台导入代理可能会导入推荐人，这时候跟上级的就不一致了
        if( $recommendID != $pid ){
            $parent_rec_path = $distributor_model->where(array('id' => $recommendID))->getField('rec_path');
        }
        
        //---------------------end 查询推荐人的情况--------------------------
        
        
        //---------------------start 得到部分参数--------------------------
        $recommendname = !empty($recommendname)?$recommendname:$pname;//推荐人姓名
        $bossname = $pname;
        
        $levname = $level_name[$level];
        $path = 0;
        $isRecommend = '0'; //是否被推荐，默认为0
        $audited = 0; //审核状态默认为0（未审核）
        $isInternal = '0';//是否内部人员，默认为0

        
        
        $search = '/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/';
        if ( !empty($phone) && !preg_match($search, $phone)) {
            $return_result = [
                'code'      =>  19,
                'msg'       =>  '您的手机号不不符合规则，无法申请注册！',
            ];
            return $return_result;
        }
        elseif( !empty($phone) && $this->check_phone_only ){
            $search = $distributor_model->where(['phone'=>$phone])->find();
            
            if( !empty($search) ){
                $return_result = [
                    'code'      =>  19,
                    'msg'       =>  '您的手机号已经被注册，无法申请注册！',
                ];
                return $return_result;
            }
        }
        
        //生成授权号
        if( !empty($authnum) ){
            $search = $distributor_model->where(['authnum'=>$authnum])->find();
            
            if( !empty($search) ){
                $return_result = [
                    'code'      =>  23,
                    'msg'       =>  '请注意授权编号已经被使用！',
                ];
                return $return_result;
            }
        }
        elseif( empty($authnum) ){
            if( empty($phone) ){
                $authnum = !empty($authnum)?$authnum:substr(time(), -4) . substr(md5(uniqid()),-6);
            }
            else{
                $authnum = !empty($authnum)?$authnum:substr($phone, -6) . substr(md5(uniqid()),-4);
            }
        }
        
        
        if( empty($password) ){
            $password = md5(substr($phone, -6)); //默认密码
        }
        //---------------------end 得到部分参数--------------------------
        
        

        //----------根据不同系统的需求更改------------------
        //改为所有级别都能并只能推荐最高级别
//            //只有最高级别才能推荐最高级别
//            if( $level == 1 && $parent_level != 1 ){
//                $returnInfo = array('status' => 2,'judgment'=>'e');
//                return $returnInfo;
//            }
//            
//            //除了最高级别能推荐同级外，其它级别不能推荐同级
//            if( $level != 1 && $level == $parent_level ){
//                $returnInfo = array('status' => 2,'judgment'=>'f');
//                return $returnInfo;
//            }


        //判断是推荐还是发展下级
        //级别由高到低是1,2,3...
//            if ( $level == 1 ) {
//                $isRecommend = '1'; //记录为被推荐用户
//                $audited = '2';//直接由总部审核
//            }
        //----------end 根据不同系统的需求更改------------------
        
        
        //----------start 推荐人与被推荐人的级别判断------------------
        //如果是后台添加，不进行上级的级别判断以及直接审核通过
        if( $for == 'radmin' ){
            $audited = 1;
        }
        elseif( $pid != 0 ){
            if( $GROW_MODEL == 5 ){
                $spe_grow_model = isset($GROW_MODEL_SPCAIL_LEVEL[$parent_level])?$GROW_MODEL_SPCAIL_LEVEL[$parent_level]:$GROW_MODEL_SPCAIL_LEVEL[0];
                if( !in_array($level, $spe_grow_model) ){
                    $return_result = [
                        'code'      =>  10,
                        'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                    ];
                    return $return_result;
                }
            }
            
            
            if( $level < $parent_level ){
                //在没有低推高的情况下是不允许申请的
                $not_grow = [1,2];//不能发展的情况
                if(in_array($GROW_MODEL, $not_grow) ){
                    $return_result = [
                        'code'      =>  10,
                        'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                    ];
                    return $return_result;
                }
                elseif( $GROW_MODEL == 4 ){

                    $spe_grow_model = isset($GROW_MODEL_LEVEL[$parent_level])?$GROW_MODEL_LEVEL[$parent_level]:$GROW_MODEL_LEVEL[0];

                    if( in_array($spe_grow_model,$not_grow) ){
                        $return_result = [
                            'code'      =>  12,
                            'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                        ];
                        return $return_result;
                    }
                }


                $isRecommend = '1';
                import('Lib.Action.User','App');
                $User = new User();
                $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);

                if( $get_pid_result['code'] != 1 ){
    //                $returnInfo = array('status' => 2,'judgment'=>'j');
                    return $get_pid_result;
                }

                $pid = $get_pid_result['info']['pid'];
                $pname = $bossname = $get_pid_result['info']['name'];
                $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
                $parent_path = $get_pid_result['info']['path'];
                //$parent_rec_path = $get_pid_result['info']['rec_path'];
                $par_level = $get_pid_result['info']['level'];
                $parent_pid = $get_pid_result['info']['parpid'];
            }


            if( $level == $parent_level ){
                //在没有平级推的情况下是不允许申请的
                $not_grow = [1];//不能发展的情况
                if(in_array($GROW_MODEL, $not_grow) ){
                    $return_result = [
                        'code'      =>  11,
                        'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                    ];
                    return $return_result;
                }
                elseif( $GROW_MODEL == 4 ){
                    $GROW_MODEL_LEVEL = C('GROW_MODEL_LEVEL');

                    $spe_grow_model = isset($GROW_MODEL_LEVEL[$parent_level])?$GROW_MODEL_LEVEL[$parent_level]:$GROW_MODEL_LEVEL[0];

                    if( in_array($spe_grow_model,$not_grow) ){
                        $return_result = [
                            'code'      =>  14,
                            'msg'       =>  '对不起，您的推荐人无权推荐该级别的经销商，请联系您的推荐人反馈！',
                        ];
                        return $return_result;
                    }
                }
                

                $isRecommend = '1';
                import('Lib.Action.User','App');
                $User = new User();
                $get_pid_result = $User->get_recommend_hight_level_parent($level,$parent);

                if( $get_pid_result['code'] != 1 ){
    //                $return_result = array('status' => 2,'judgment'=>'j');
                    return $get_pid_result;
                }

                $pid = $get_pid_result['info']['pid'];
                $pname = $bossname = $get_pid_result['info']['name'];
                $parent_openid = !empty($get_pid_result['info']['openid'])?$get_pid_result['info']['openid']:$parent_openid;
                $parent_path = $get_pid_result['info']['path'];
                //$parent_rec_path = $get_pid_result['info']['rec_path'];
                $par_level = $get_pid_result['info']['level'];
                $parent_pid = $get_pid_result['info']['parpid'];
            }
        }

        //----------end 推荐人与被推荐人的级别判断------------------


        //最高级的归属肯定是总部
        if ($level == 1) {
            //如果是最高级别的经销商申请
            $managed = 1;

            //如果是推荐后最高级的上级还是推荐人则这里屏蔽
            //$pid = 0;
            //$bossname = '总部';
        } else {
            $managed = 0;
        }

        if( $pid != 0 ){
            $pdpath = explode('-', $parent_path);
            if ($pdpath[1]) {
                $tallest = $pdpath[1];
            } else {
                $tallest = $pid;
            }

            //当前申请经销商的path（PATH是经销商链）
            $path = $parent_path . '-' . $pid;
        }
        else{
            $bossname = '总部';
            $pname = '总部';
            $tallest = 0;
        }

        
        
        if( empty($parent_rec_path) ){
            $parent_rec_path = 0;
        }
        
        if( $recommendID == 0 ){
            $rec_path = '0';
        }
        else{
            $rec_path = $parent_rec_path.'-'.$recommendID;
        }

        //如果添加时直接为通过状态
        if( $audited == 1 ){
            $managed = 2;
        }
        
        //add by zbs 2018-01-15
        $audit_way = C('AUDIT_WAY');//1.直接上级审核通过;2.上级审核后总部审;4.直接总部审核;
        if ($audit_way == 3) {
            $audit_way_level = C('AUDIT_WAY_LEVEL');
            $audited = isset($audit_way_level[$level])?$audit_way_level[$level]:$audit_way_level[0];
            if($audited == 1 || $audited == 2){
                $audited = 0;
            }
        }
        if ($audit_way == 4) {
            $audited = 4;
        }
        
        //如果上级为总部，那审核状态必然是总部直接审核
        if( $pid == 0 ){
            $audited = 4;
        }
        
        //特殊设定：如果提交的时候已经提交审核状态
        if( $set_audited != NULL && in_array($set_audited, [0,1,2,4]) ){
            $audited = $set_audited;
        }
        
        //增加代理树状图层级
        $depth = 1;
        if (C('DEFAULT_TEAM') == 'path') {
            if ($pid) {
                $parent_user = $distributor_model->field('id,depth')->find($pid);
                $depth = $parent_user['depth'] + 1;
            }
        } else {
            $recommend_user = $distributor_model->field('id,depth')->find($recommendID);
            $depth = $recommend_user['depth'] + 1;
        }

        $data = array(
            'managed' => $managed,
            'audited' => $audited,
            'pid' => $pid,
            'openid' => $openid,
            'name' => $name,
            'wechatnum' => $wechatnum,
            'phone' => $phone,
            'email' => $email,
            'idennum' => $idennum,
            'address' => $address,
            'time' => time(),
            'level' => $level,
            'levname' => $levname,
            'bossname' => $bossname,
            'pname' => $pname,
            'headimgurl' => $headimgurl,
            'nickname' => $nickname,
            'path' => $path,
            'rec_path'  =>  $rec_path,
            'password' => $password,
            'idennumimg' => $idennumimg,
            'liveimg' => $liveimg,
            'recommendID' => $recommendID,
            'recommendname' =>  $recommendname,//20180713-FENG新增
            'tallestID' => $tallest,
            'isRecommend' => $isRecommend,
            'isInternal' => $isInternal,
            'authnum' => $authnum,
            'province'  =>  $province,
            'city'      =>  $city,
            'county'    =>  $county,
            'depth'     =>  $depth,
            //'indirectId' => $indirectId,
            'sex'       =>  $sex,
            'statu'     =>  0,
        );
        
        $add_res = $distributor_model->add($data);

        if ($add_res) {//提交申请成功
            //清除团队缓存
            clean_team_path_cache();
            
            if( $this->has_user_bind ){
//                $condition_dis = array(
//                    'name'      =>  $name,
//                    'wechatnum' =>  $wechatnum,
//                    'phone'     =>  $phone,
//                );
//
//                $dis_info = $distributor_model->where($condition_dis)->field('id')->find();
//                $uid = $dis_info['id'];

                import('Lib.Action.User','App');
                $User = new User();
                $result = $User->update_distributor_bind($add_res);
                if( $result['code'] != 1 ){
                    setLog(var_export($result,1),'add_user-update_distributor_bind_error');
                }
            }


            //如果是后台添加的账户，不需要发送提示审核通知
            if( $for != 'radmin' && !empty($parent_openid) ){
                //----------公众号推送给申请人的上级--------
                $touser = $parent_openid;
//                $keyword1 = $name;
//                $sendTime = date("Y-m-d H:i:s");
//                $template_id = C('SQ_MB');
//                $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//                $sendData = array(
//                    'first' => array('value' => ("经销商申请通知"), 'color' => "#CC0000"),
//                    'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                    'keyword2' => array('value' => ("联系方式：" . $phone . "，申请时间：" . $sendTime), 'color' => '#000'),
//                    'remark' => array('value' => ("点击进行审核"), 'color' => '#CC0000')
//                );
//
//                $template = array(
//                    'touser' => $touser,
//                    'template_id' => $template_id,
//                    'url' => $url,
//                    'topcolor' => '#7B68EE',
//                    'data' => $sendData
//                );
//
//                $this->wechat_obj->sendTemplateMessage($template);

                //这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面旧的模板消息就可以开启此方法）
                import('Lib.Action.Message','App');
                $message = new Message();
                $message->push(trim($touser), $data, $message->development_apply);
                //调用结束

                //----------end公众号推送给申请人的上级--------
            }
            
            //优化，注册自动增加收货地址
            if( !empty($name) && !empty($phone) && !empty($province) && !empty($city) && !empty($county) && !empty($address) ){
                $data = array(
                    'user_id'=>$add_res,
                    'name' => $name,
                    'phone' => $phone,
                    'province' => $province,
                    'city'=>$city,
                    'area'=>$county,
                    'address'=>$address,
                    'default'=>1,
                    'add_time' => time()
                );
                M('address')->add($data);
            }
            
            

            $returnInfo = array('status' => 1,'judgment'=>'h','msg'=>'经销商《'.$name.'》提交申请成功！');
            $return_result = [
                'code'      =>  1,
                'msg'       =>  '经销商《'.$name.'》提交申请成功！',
            ];
            return $return_result;
            
        } else {      //提交申请失败
            $return_result = [
                'code'      =>  2,
                'msg'       =>  '提交申请失败，请重试！',
                'error_model'   =>  $distributor_model->getDbError(),
            ];
            return $return_result;
        }
    }//end func add


    //用户升降级
    public function upgrade($info){
        
        if( empty($info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $uid = $info['uid'];
        $level = $info['level'];
        $type = $info['type'];
        $b_id = $info['b_id'];
        $upgrade_apply_id = $info['upgrade_apply_id'];//升级申请id

        $distributor_model = M('distributor');

        $level_name = C("LEVEL_NAME");

        //------判断的提交的信息逻辑----------

        //升级代理的信息
        $one = $distributor_model->find($uid);

        if( empty($one) ){
            $return_result   =   array(
                'msg'   =>  '查无此代理！',
                'code'  =>  2,
            );

            return $return_result;
        }

        $cur_level = $one['level'];//当前代理等级
        $cur_name = $one['name'];//当前代理名字
        $cur_authnum = $one['authnum'];//当前代理授权编号

        //判断级别
        if( $level > $cur_level && $type == 'up' ){
            $return_result   =   array(
                'msg'   =>  '升级的级别不能低于现在级别！',
                'code'  =>  3,
            );

            return $return_result;
        }
        if( $level < $cur_level && $type == 'down' ){
            $return_result   =   array(
                'msg'   =>  '降级的级别不能高于现在级别！',
                'code'  =>  4,
            );

            return $return_result;
        }
        if( $level == $cur_level ){
            $return_result   =   array(
                'msg'   =>  '更改的级别不能等于当前级别！',
                'code'  =>  5,
            );

            return $return_result;
        }


        //-1即为原上级
        if( $b_id==-1 ){
            $b_id = $one['pid'];
            $bossname = $one['bossname'];
            $bossinfo = $distributor_model->where(array('id'=>$b_id))->find();
        }
        elseif( $b_id==0 ){
            $bossname = "总部";
        }
        else{
            $bossinfo = $distributor_model->where(array('id'=>$b_id))->find();
            $bossname = $bossinfo['name'];
        }

        if( empty($level) || !is_numeric($b_id) ){
            $return_result   =   array(
                'msg'   =>  '提交的信息有误！',
                'code'  =>  6,
            );

            return $return_result;
        }
        if( $type != 'up' && $type!='down' ){
            $return_result   =   array(
                'msg'   =>  '提交的信息有误！',
                'code'  =>  7,
            );

            return $return_result;
        }
        //------end 判断的提交的信息逻辑----------


        if ($level == 1 && $type == 'up') {
            $managed = 2;
        } else {
            $managed = 0;
        }
        if ($type == 'down') {
            $managed = 0;
        }

        $levname = $level_name[$level];

        if ($type == 'up') {
            if($this->is_same_level_superior) {
                //平级可以做为上级
                if ($bossinfo['level'] > $level) {
                    $return_result   =   array(
                        'msg'   =>  '上级级别不能比下级低！',
                        'code'  =>  10,
                    );

                    return $return_result;
                }
            } else {
                if ($bossinfo['level'] >= $level) {
                    $return_result   =   array(
                        'msg'   =>  '上级级别不能比下级低！',
                        'code'  =>  11,
                    );

                    return $return_result;
                }
            }
            if ($level == 1 && $b_id == 0) {
                $bossname = "总部";
            }

            //升级前记住他原本的pid
            if ($one['num'] >= 1 || $one['upId'] != 0) {
                $upId = $one['upId'];
                $num = $one['num'] + 1;
            } else {
                $upId = $one['pid'];
                $num = 1;
            }
            
            //上级改变了
            if ($one['pid'] != $b_id) {
                $parent = $distributor_model->find($b_id);
                if (!$parent || $b_id == 0) {
                    $path = 0;  
                } else {
                    $path = $parent['path']. '-' . $b_id;
                }
            } else {
                $path = $one['path'];
            }
           

            $arr = array(
                'id' => $uid,
                'pid' => $b_id,
                'level' => $level,
                'levname' => $levname,
                'bossname' => $bossname,
                //'pname' => $pname,
                'managed' => $managed,
                'upId' => $upId,
                'num' => $num,
                'path' => $path,
            );
            import('Lib.Action.NewRebate', 'App');
            $rebate_obj = new NewRebate();
            if (in_array($one['level'], $rebate_obj->rebate_agent_level)) {
                //升级之前要更新团队业绩和返利
                $rebate_obj->create_team_rebate($one['id']);
            }
        } else {
            $arr = array(
                'id' => $uid,
                'level' => $level,
                'levname' => $levname,
                //'bossname' => $bossname,
                'managed' => $managed
            );
        }

        $arr['upgrade_time'] = time();//最后一次升降级的时间


        $save_result = $distributor_model->save($arr);

        if ( $save_result ) {//生成成功
            if ($type == 'up') {
                //更改升级申请状态为已审核
                if ($upgrade_apply_id) {
                    M('distributor_upgrade_apply')->where(['id'=>$upgrade_apply_id])->save(['status'=>1]);
                }
                
                if ($one['pid'] != $b_id) {
                    import('Lib.Action.Team', 'App');
                    $team_obj = new Team();
                    $team_path = get_team_path_by_cache();
                    $uids = $team_obj->get_team_ids($one['id'], $team_path);
                    //找出需要更改上级的代理
                    $uids = $team_obj->filter_team_ids($uids, ['pid' => $one['pid']]);
                    foreach ($uids as $id) {
                        $this->change_parent($id, $one['id']); 
                   }
                }
                
                import('Lib.Action.Integral','App');
                $Integral = new Integral();
                $Integral->recommend_upgrade_up($one,$level);

                setLog('id为' . $uid . '的代理升级成为' . $levname . '--up---', 'up-down-agent');
                $return_result   =   array(
                    'msg'   =>  $cur_name.'升级成功！授权编号：'.$cur_authnum,
                    'code'  =>  1,
                );
                
                //模板
                import('Lib.Action.Message','App');
                $message = new Message();
                $one['apply_levname'] = $levname;//升级后的等级名称
                $message->push(trim($one['openid']), $one, $message->upgrade_pass);
            }
            else if ($type == 'down') {
                setLog('id为' . $uid . '的经销商降级成为' . $levname . '--down---', 'up-down-agent');
                $return_result   =   array(
                    'msg'   =>  $cur_name.'降级成功！授权编号：'.$cur_authnum,
                    'code'  =>  1,
                );
            }


            //更改关系表
            $this->update_distributor_bind($uid);

        } else {

            $return_result   =   array(
                'msg'   =>  '改变代理级别失败，请重试！',
                'code'  =>  8,
            );
        }

        return $return_result;
    }//end func upgrade


    //更改上级
    public function change_parent($uid,$pid){

        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        $distributor_model = M('distributor');


        $condition_user = array(
            'id' => $uid,
        );
        $user_info = $distributor_model->where($condition_user)->find();

        if (empty($user_info)) {
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到此更换上级的经销商！',
            );
            return $return_result;
        }

        $user_name = $user_info['name'];
        $old_user_pid = $user_info['pid'];

        if( $old_user_pid == $pid ){
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '原上级与新上级为同一经销商，无须更改！',
            );
            return $return_result;
        }


        if( $pid == 0 ){
            $tallestID = 0;
            $parent_name = '总部';
        }
        else{
            $condition_parent = array(
                'id' => $pid,
            );

            $parent_info = $distributor_model->where($condition_parent)->find();

            if (empty($parent_info)) {
                $return_result = array(
                    'code'  =>  4,
                    'msg'   =>  '找不到该经销商上级的信息！',
                );
                return $return_result;
            }

            $tallestID = $parent_info['tallestID'];
            $parent_name = $parent_info['name'];


            //如果该上级的最高负责人为0，则转移的最高经销商改为该经销商的ID
            if ( $tallestID == 0 ) {
                $tallestID = $pid;
            }
        }


        $where = array(
            'id' => $uid,
            'audited' => 1
        );
        $arr = array(
            'tallestID' => $tallestID,
            'pid' => $pid,
            'bossname' => $parent_name
        );

        $save_result = $distributor_model->where($where)->save($arr);

        if ( $save_result ) {
            //add by z
            //改变代理及所有下级path字段
            $this->update_agent_path($user_info, $parent_info, $old_user_pid);
            setLog('id为' . $uid . '的经销商所有下级经销商转移给了id为' . $pid . '的经销商', 'replace-higher-agent');

            $update_result = $this->update_distributor_bind($uid);

            $return_result = array(
                'code'  =>  1,
                'msg'   =>  $user_name.'更换上级成功！',
            );
        } else {
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  $user_name.'更换上级失败！',
//                'info'  =>  $distributor_model->getDbError(),
            );
        }

        return $return_result;
    }//change_parent


    //更换推荐人
    public function change_recommend($uid,$recommendid){

        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        $distributor_model = M('distributor');


        $condition_user = array(
            'id' => $uid,
        );
        $user_info = $distributor_model->where($condition_user)->find();

        if (empty($user_info)) {
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到此更换推荐人的经销商！',
            );
            return $return_result;
        }

        $old_recommendID = $user_info['recommendID'];
        $user_name = $user_info['name'];

        if( $old_recommendID == $recommendid ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '原推荐人与新推荐人为同一经销商，无须更改！',
            );
            return $return_result;
        }


        if( $recommendid != 0 ){
            $condition_recommend = array(
                'id' => $recommendid,
            );

            $recommend_info = $distributor_model->field('id,name')->where($condition_recommend)->find();

            if (empty($recommend_info)) {
                $return_result = array(
                    'code'  =>  4,
                    'msg'   =>  '找不到该经销商推荐人的信息！',
                );
                return $return_result;
            }
            
            $recommendname = $recommend_info['recommendname'];
        }
        else{
            $recommendname = '总部';
        }


        $where = array(
            'id' => $uid,
//            'audited' => 1
        );
        $arr = array(
            'recommendID'   =>  $recommendid,
            'recommendname' =>  $recommendname,
        );

        $save_result = $distributor_model->where($where)->save($arr);


        if ( $save_result ) {
            //add by z
            //改变代理及所有下级rec_path字段
            $this->update_agent_path($user_info, $recommend_info, $old_recommendID, 'rec_path');
            setLog('id为' . $uid . '的经销商推荐人改为id为' . $recommendid . '的经销商', 'replace-recommend');

            //$update_result = $this->update_distributor_bind($uid);

            $return_result = array(
                'code'  =>  1,
                'msg'   =>  $user_name.'更换推荐人成功！',
            );
        } else {
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  $user_name.'更换推荐人失败！',
            );
        }

        return $return_result;


    }//end func change_recommend


    //转让代理
    public function transfer_dis($uid,$tid){
        //要转移的经销商id(只是下属经销商转移了,自己不转移)

        if ( empty($uid) || empty($tid) ) {
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        $field = 'tallestID,name';

        $distributor_obj = M('distributor');

        $user_info = $distributor_obj->where(array('id' => $uid))->field($field)->find();

        if ( empty($user_info) ) {
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有找到该用户信息！',
            );
            return $return_result;
        }

        $user_name = $user_info['name'];


        $transfer_info = $distributor_obj->where(array('id' => $tid))->field($field)->find();

        if ( empty($transfer_info) ) {
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有找到被转让经销商的信息！',
            );
            return $return_result;
        }

        $tallestID = $transfer_info['tallestID'];
        $parent_name = $transfer_info['name'];


        $field_all_parent = 'id';
        $condition_all_parent = array(
            'pid'   =>  $uid,
        );
        $all_parent_info = $distributor_obj->where($condition_all_parent)->field($field_all_parent)->select();

        if( empty($all_parent_info) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '没有找到要转让下级的经销商的下级！',
            );
            return $return_result;
        }

        $par_uids = array();
        foreach( $all_parent_info as $k => $v ){
            $par_uids[] = $v['id'];
        }



        //如果该上级的最高负责人为0，则转移的最高经销商改为该经销商的ID
        if ($tallestID == 0) {
            $tallestID = $tid;
        }

        $where = array(
            'pid' => $uid,
            'audited' => 1
        );
        $arr = array(
            'tallestID' => $tallestID,
            'pid' => $tid,
            'bossname' => $parent_name
        );

        $result = $distributor_obj->where($where)->save($arr);

        if ( $result ) {

            $this->update_uids_bind($par_uids);

            setLog('id为' . $uid . '(' . $user_name . ')' . '旗下的经销商转移给了'
                . $tid . '(' . $parent_name . ')', 'transfer-low-agent');

            $return_result = array(
                'code'  =>  1,
                'msg'   =>  $user_name.'转让旗下经销商成功！',
            );

        } else {
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  $user_name.'转让旗下经销商失败！',
//                'error_info'    =>  $distributor_obj->getLastSql(),
            );
        }

        return $return_result;
    }//end func transfer_dis


    //转让代理推荐人
    public function transfer_recommend($uid,$tid){
         //要转移的经销商id(只是下属经销商转移了,自己不转移)
        
        if ( empty($uid) || empty($tid) ) {
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $field = 'tallestID,name';
        
        $distributor_obj = M('distributor');
        
        $user_info = $distributor_obj->where(array('id' => $uid))->field($field)->find();
        
        if ( empty($user_info) ) {
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有找到该用户信息！',
            );
            return $return_result;
        }
        
        $user_name = $user_info['name'];
        
        
        $transfer_info = $distributor_obj->where(array('id' => $tid))->field($field)->find();
        
        if ( empty($transfer_info) ) {
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有找到被转让经销商的信息！',
            );
            return $return_result;
        }

        $tallestID = $transfer_info['tallestID'];
        $parent_name = $transfer_info['name'];
        
        
        $field_all_parent = 'id';
        $condition_all_parent = array(
            'recommendID'   =>  $uid,
        );
        $all_parent_info = $distributor_obj->where($condition_all_parent)->field($field_all_parent)->select();
        
        if( empty($all_parent_info) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '没有找到要转让下级的经销商的下级！',
            );
            return $return_result;
        }
        
        $par_uids = array();
        foreach( $all_parent_info as $k => $v ){
            $par_uids[] = $v['id'];
        }
        
        
        
        $where = array(
            'recommendID' => $uid,
            'audited' => 1
        );
        $arr = array(
            'recommendID' => $tid,
            'recommendname' =>  $parent_name,
        );
        
        $result = $distributor_obj->where($where)->save($arr);
        
        if ( $result ) {
            
            $this->update_uids_bind($par_uids);
            
            setLog('id为' . $uid . '(' . $user_name . ')' . '推荐的经销商转移给了' 
                    . $tid . '(' . $parent_name . ')', 'transfer-low-agent');
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  $user_name.'转让推荐经销商成功！',
            );
            
        } else {
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  $user_name.'转让推荐经销商失败！',
//                'error_info'    =>  $distributor_obj->getLastSql(),
            );
        }
        
        return $return_result;
    }//end func transfer_recommend


    //添加/修改用户关系表
    public function update_distributor_bind($uid){

        if( !$this->has_user_bind ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不需要生成用户关系信息！',
            );
            return $return_result;
        }

        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            );
            return $return_result;
        }

        $distributor_bind_model = M('distributor_bind');
        $distributor_model = M('distributor');

        $level_num = C('LEVEL_NUM');
        $level_num_max = C('LEVEL_NUM_MAX');

        $condition_dis = array(
            'id'    =>  $uid,
        );

        $dis_info = $distributor_model->where($condition_dis)->find();


        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到该用户！',
            );
            return $return_result;
        }


        $dis_level = $dis_info['level'];
        $pid = $dis_info['pid'];
        $p_level = 0;


        if( !empty($pid) ){
            $condition_par = array(
                'id'    =>  $pid,
            );

            $par_info = $distributor_model->where($condition_par)->find();

            if( !empty($par_info) ){
                $p_level = $par_info['level'];

                //如果有上级，那么除了以及上级的该等级的信息，其它是一样的(id键删除)
                $condition_bind_par = array(
                    'uid'    =>  $pid,
                );
                $parent_bind_info = $distributor_bind_model->where($condition_bind_par)->find();

                $new_dis_bind_info = $parent_bind_info;
                unset($new_dis_bind_info['id']);

//                //更改信息应该只更改这个经销商级别以上的关系信息
//                for( $i=$dis_level;$i>0;$i-- ){
//                    $parent_bind_key = 'level'.$i;
//
//                    if( isset($parent_bind_info[$parent_bind_key]) ){
//                        $new_dis_bind_info[$parent_bind_key] = $parent_bind_info[$parent_bind_key];
//                    }
//                }

            }
            else{
                setLog('id为' . $uid . '的代理关系添加失败', 'add_user_bind_not_find_parent');
                $pid = 0;
            }
        }

        //如果上级改为总部，则所有等级的关系都改为总部
        if( $pid == 0 ){

            for( $i=$level_num_max;$i>0;$i-- ){
                $bind_level_key = 'level'.$i;

                $new_dis_bind_info[$bind_level_key] = 0;
            }
        }

        /**
         * TODO:特殊情况，上级比下级低级或平级
         */


        $new_dis_bind_info['uid'] = $uid;
        $new_dis_bind_info['u_level'] = $dis_level;
        $new_dis_bind_info['pid']   =   $pid;
        $new_dis_bind_info['p_level']   =   $p_level;
        $new_dis_bind_info['updated']   =   time();


        //如果其上级等级不为0（为0则上级为总部）
        if( $pid!=0 && $p_level != 0 ){
            $level_key = 'level'.$p_level;
            $new_dis_bind_info[$level_key] = $pid;
        }

        $condition_bind = array(
            'uid'   =>  $uid
        );

        $dis_bind = $distributor_bind_model->where($condition_bind)->find();

        $set_log_info = '';

        //已有则进行更改
        if( !empty($dis_bind) ){

            //已有用户关系信息的经销商，更新上级信息时，也要把其代理链上的用户给更新
            $dis_bind_level_key = 'level'.$dis_level;
            $condition_underling[$dis_bind_level_key] = $uid;

            //更改信息应该只更改这个经销商级别以上的关系信息
            for( $i=$dis_level;$i>0;$i-- ){
                $underling_bind_key = 'level'.$i;

                if( isset($new_dis_bind_info[$underling_bind_key]) ){
                    $underling_bind_info[$underling_bind_key] = $new_dis_bind_info[$underling_bind_key];
                }
            }
            $underling_bind_info['updated'] = time();

            $change_underling_result = $distributor_bind_model->where($condition_underling)->save($underling_bind_info);

            $change_result = $distributor_bind_model->where($condition_bind)->save($new_dis_bind_info);
        }
        else{
            $change_result = $distributor_bind_model->add($new_dis_bind_info);
        }


        if( $change_result ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '用户关系更新成功！',
            );
            return $return_result;
        }
        else{
            setLog('id为' . $uid . '的代理关系添加失败.'.$set_log_info, 'add_user_bind_error');

            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '用户关系更新失败，请重试！',
            );
            return $return_result;
        }

    }//end func update_distributor_bind


    //更新多用户信息
    public function update_uids_bind($uids){
        if ( empty($uids) ) {
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        $transfer_result = TRUE;
        $error_uid = '';

        //直接更新其旗下经销商的关系信息
        foreach( $uids as $k => $uid ){

            $update_result = $this->update_distributor_bind($uid);

            if( $update_result['code'] != 1 ){
                $error_uid = $error_uid.','.$uid;
                $transfer_result = FALSE;
            }
        }


        if( $transfer_result ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '转让经销商成功！',
            );
        }
        else{
            setLog('id为' . $error_uid . '的代理关系链转移失败.', 'transfer_dis_bind_error');

            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '转让经销商失败！',
            );
        }

        return $return_result;
    }//end func update_uids_bind




    //删除用户
    public function delete(){
        
    }//end func delete

    
    /**
     * 添加升级申请
     * 
     * @param int $uid
     * @param array $dis_info
     * @param int $apply_level  //申请级别
     * @param string $note
     * @return array
     */
    public function add_upgrade_apply($uid,$dis_info,$apply_level,$note=''){
        if( !$this->open_upgrade_apply ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '不能添加，该模块还未开启！',
            ];
            return $result;
        }
        
        $condition = [
            'id'    => $uid,
        ];
        
        
        if( empty($dis_info) ){
            $dis_info = $this->distributor_obj->where($condition)->find();
        }
        if( empty($dis_info) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '没有代理信息！',
            ];
            return $result;
        }
        
        $condition = [
            'uid'    => $uid,
        ];
        
        
        $list = $this->distributor_upgrade_apply_obj->where($condition)->order('updated desc')->find();

        $updated = $list['updated'];
        $updated_time = $updated+60*60*24;
        
        if( $updated_time>time() ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '请等待24小时再提交升级申请！',
            ];
            return $result;
        }
        
        $audit_id = 0;
        $status = 1;
        if( $this->upgrade_apply_aduit ){
            $audit_id = $dis_info['pid'];
            $status = 0;
        }
        
        $new_info = [
            'uid'   =>  $uid,
            'cur_level' =>  $dis_info['level'],
            'apply_level'   =>  $apply_level,
            'status'        =>  $status,
            'audit_id'      =>  $audit_id,
            'note'          =>  $note,
            'created'       =>  time(),
            'updated'       =>  time(),
            'depositimg'    =>$depositimg,
        ];

        $res = $this->distributor_upgrade_apply_obj->add($new_info);

        if( !$res ){
            $result = [
                'code'  =>  3,
                'msg'   =>  '申请失败，请重试！',
            ];
            return $result;
        }
        
        //如果有升级申请有审核人
        if( !empty($audit_id) ){
            import('Lib.Action.Message', 'App');
            $message = new Message();
            
            $aduit_info = $this->distributor_obj->field('name','phone')->where(['id' => $audit_id])->find();
            $openid = $aduit_info['openid'];
            $aduit_info['time'] = $new_info['created'];
            $aduit_info['apply_level'] = $$apply_level;
            $aduit_info['apply_time'] = date('Y-m-d H时');
            
            $message->push(trim($openid), $aduit_info, $message->upgrade_apply);
        }
        
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '申请成功！',
        ];
        return $result;
    }

    /**
     * 修改经销商级别名称
     * @param array $update_info    //数组必须是array('级别'=>'级别名')
     */
    public function update_dis_level($update_info){

        if( empty($update_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }

        $distributor_obj = M('distributor');


        $level_num = C('LEVEL_NUM');
        $level_name = C('LEVEL_NAME');

        $new_level_num = count($update_info);

        



        foreach( $update_info as $k => $v ){

            if( $level_name[$k] ==  $v ){
                continue;
            }




        }








    }//end func update_dis_level

    
    
    


    /**
     *
     * @param type $new_array
     */
    private function merge_config_file($update_config){

        $old_config = C();

        $new_config = $old_config;

        //只有特定配置允许改变
        $new_config['YM_DOMAIN'] = isset($update_config['YM_DOMAIN'])?$update_config['YM_DOMAIN']:$old_config['YM_DOMAIN'];//域名
        $new_config['APP_ID'] = isset($update_config['APP_ID'])?$update_config['APP_ID']:$old_config['APP_ID'];//公众号AppID
        $new_config['APP_SECRET'] = isset($update_config['APP_SECRET'])?$update_config['APP_SECRET']:$old_config['APP_SECRET'];//公众号APP_SECRET
        $new_config['SH_MB'] = isset($update_config['SH_MB'])?$update_config['SH_MB']:$old_config['SH_MB'];//审核模板
        $new_config['SQ_MB'] = isset($update_config['SQ_MB'])?$update_config['SQ_MB']:$old_config['SQ_MB'];//申请模板
        $new_config['LEVEL_NUM'] = isset($update_config['LEVEL_NUM'])?$update_config['LEVEL_NUM']:$old_config['LEVEL_NUM'];//经销商级别数
        $new_config['LEVEL_NAME'] = isset($update_config['LEVEL_NAME'])?$update_config['LEVEL_NAME']:$old_config['LEVEL_NAME'];//经销商级别名
        $new_config['GROW_MODEL'] = isset($update_config['GROW_MODEL'])?$update_config['GROW_MODEL']:$old_config['GROW_MODEL'];//发展模式
        $new_config['IS_SUBMIT_ID_CARD_IMG'] = isset($update_config['IS_SUBMIT_ID_CARD_IMG'])?$update_config['IS_SUBMIT_ID_CARD_IMG']:$old_config['IS_SUBMIT_ID_CARD_IMG'];//是否需要提交身份证图片
        $new_config['IS_TEST'] = isset($update_config['IS_TEST'])?$update_config['IS_TEST']:$old_config['IS_TEST'];//是否测试模式
        $new_config['SHOW_PAGE_TRACE'] = isset($update_config['SHOW_PAGE_TRACE'])?$update_config['SHOW_PAGE_TRACE']:$old_config['SHOW_PAGE_TRACE'];//显示页面Trace信息





    }//end func merge_config





    //-----------------end 用户相关业务操作---------------------







    /**
     * 获取上个月
     * @return string $lastmonth (ps:201607)
     */
    private function getlastMonth(){

        $firstday=date('Y-m-01');
        $lastmonth_time = strtotime($firstday)-60*60*24;
        $lastmonth = date('Ym',$lastmonth_time);//ps:201607

        return $lastmonth;
    }


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

    /**
     *
     * 改变代理及所有下级path字段
     * @param type $user_info 要更换的代理信息
     * @param type $parent_info 新上级/推荐人代理信息
     * @param type $old_id 要更换上级/推荐人代理的原来上级/推荐人
     * @param type $path
     */
    private function update_agent_path($user_info, $parent_info , $old_id, $path='path') {
        $distributor_model = M('distributor');
        $default = C('DEFAULT_TEAM');
        $where = [
            'id' => $user_info['id'],
//            'audited' => 1
        ];
        if ($parent_info) {
            $new_parent_path = $parent_info[$path] . '-' . $parent_info['id'];
        } else {
            $new_parent_path = 0;
        }
        $result = $distributor_model->where($where)->save([$path => $new_parent_path]);
        
        //更改下级代理树状图层级
        $depth = 0;
        if ($default == $path) {
            if (($user_info['depth'] - 1) != $parent_info['depth']) {
                $depth = ($parent_info['depth']+1) - $user_info['depth'];
                $distributor_model->where($where)->save(['depth' => $parent_info['depth']+1]);
            }
        }

        if ($result) {
            $old_child_path = $user_info[$path] . '-' . $user_info['id'];
            $new_child_path = $new_parent_path . '-' . $user_info['id'];

            //找到所有需要更新path字段的下级代理id
            $ids = [];
            $users = $distributor_model->field("id, $path")->where(['is_lowest' => 1, $path => ['like', "$old_child_path%"]])->select();
            foreach ($users as $user) {
                $temp = explode('-', $user[$path]);
                foreach ($temp as $v) {
                    if (!isset($ids[$v])) {
                        $ids[$v] = $v;
                    }
                }
                $ids[$user['id']] = $user['id'];
            }

            $old_ids = explode('-', $old_child_path);
            foreach ($old_ids as $id) {
                if (in_array($id, $ids)) {
                    unset($ids[$id]);
                }
            }
//            var_dump($ids);die;
            $list = $distributor_model->field("id, $path")->where(['id' => ['in', $ids]])->select();

            foreach ($list as $v) {
                $new_path = preg_replace("/$old_child_path/", $new_child_path, $v[$path]);
                $res = $distributor_model->where(['id' => $v['id']])->save([$path => $new_path]);
                 if ($depth != 0) {
                     $distributor_model->where(['id' => $v['id']])->setInc('depth', $depth);
                 }
                if (!$res) {
                    setLog('改变代理及所有下级path字段失败:'.json_encode($v),'path');
                }
            }

        }
        if ($default == $path) {
            //改变新上级的is_lowest字段为0
            $this->distributor_obj->where(['id' => $parent_info['id'], 'audited' => 1])->save(['is_lowest' => 0]);
            
            //判断到原本代理更换后没有下级代理，则is_lowest置1
            if ($default == 'path') {
                $count = $this->distributor_obj->where(['pid' => $old_id, 'audited' => 1])->count('id');
            } else {
                $count = $this->distributor_obj->where(['recommendID' => $old_id, 'audited' => 1])->count('id');
            }
            if ($count <= 0) {
                $this->distributor_obj->where(['id' => $old_id, 'audited' => 1])->save(['is_lowest' => 1]);
            }
        }
//        //保险起见，再重新找is_lowest没有置0的，并且置0(影响团队业绩)
//        import('Lib.Action.Team', 'App');
//        (new team())->is_yes_lowest();
    }
    
    //获取最高级别代理
//    public function get_top_one_users() {
//        return $this->distributor_obj->where(['level' => ['lt', 2], 'audited' => 1])->select();
//    }

    //获取产生返利的用户
    public function get_rebate_users($level) {
        return $this->distributor_obj->where(['level' => ['in', $level], 'audited' => 1])->select();
    }
    /**
     * 获取用户订单统计信息
     * @param type $condition
     * @param type $condition_user
     * @param type $page_info
     * @return array|int
     */
    public function get_users_count($condition=array(),$condition_user=array(),$page_info=array()){
        
        $distributor_obj = M('distributor');
        
        
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        $count = 0;
        $page = '';
        

        
        if( !empty($page_info) ){
            $page_con = $page_num.','.$page_list_num;
            
            $count = $distributor_obj->where($condition_user)->count();
            
            $distributor_info = $distributor_obj->where($condition_user)->page($page_con)->select();
            
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        else{
            unset($condition_user['month']);
            $distributor_info = $distributor_obj->where($condition_user)->select();
        }

        if( empty($distributor_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有找到用户信息！',
            );
            
            return $return_result;
        }
        
        
        $uids = array();
        $month = isset($condition['month'])?$condition['month']:date('Ym');
        
        import('Lib.Action.Team','App');
        $team_obj = new Team();
        
        //读取缓存团队
        $team_path = get_team_path_by_cache();
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        //
        //写入用户数组用于显示
        foreach( $distributor_info as $k_dis => $v_dis ){
            $v_dis_uid = $v_dis['id'];
            $v_dis_level = $v_dis['level'];
            $v_dis_levname = $v_dis['levname'];

            //个人业绩
            $person_money = $team_obj->get_team_money($v_dis_uid, $month);

            //团队人数
            $team_uids = $team_obj->get_team_ids($v_dis_uid, $team_path);
            $team_num = count($team_uids);
            //获取实际参与团队业绩计算的团队id
            $uids = $team_obj->get_team_count_ids($count_way, $v_dis, $team_path);
            $team_money = $team_obj->get_team_money($uids, $month);

            //团队各个级别人数
            $team_level_num = $team_obj->get_team_level_number($team_uids);
            $distributor_info[$k_dis]['team_num'] = $team_num;
            $distributor_info[$k_dis]['person_money'] = $person_money;
            $distributor_info[$k_dis]['team_money'] = $team_money;
            $distributor_info[$k_dis]['team_level_num'] = $team_level_num;
        }
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '查询成功！',
            'result'    =>  array(
                'dis_info'  =>  $distributor_info,
                'uids'  =>  $uids,
                'month' =>  $month,
                'page'  =>  $page,
                'count' => $count,
                'limit' => $page_list_num,
            ),
        );
        
        return $return_result;
    }//end func get_users_count
    
    
    //修改级别名称
    public function update_level_name($new_level_name){
        
        if( empty($new_level_name) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            return $return_result;
        }
        
        $OLD_LEVEL_NAME = C('LEVEL_NAME');
        
        $format_error = FALSE;
        foreach( $new_level_name as $level => $name ){
            
            //错误的格式
            if( !is_numeric($level) ){
                $format_error = TRUE;
                break;
            }
            elseif( empty($name) ){
                $format_error = TRUE;
                break;
            }
            //如果旧的和新的没区别也不用做更改了
            if( $OLD_LEVEL_NAME[$level] == $name ){
                continue;
            }
            
            
            $condition = [
                'level' =>  $level,
            ];
            
            $save_info = [
                'levname'   =>  $name,
            ];
            
            $this->distributor_obj->where($condition)->save($save_info);
            
        }
        
        
        if( $format_error ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '修改代理级别的格式有误！',
                'info'  =>  $new_level_name,
            ];
        }
        else{
            $return_result = [
                'code'  =>  1,
                'msg'   =>  '修改成功！',
            ];
        }
        
        
        
        return $return_result;
    }//end func update_level_name
    
    
    public function get_user_by_id($uid) {
        return $this->distributor_obj->where(['audited' => 1])->find($uid);
    }
    
    
}