<?php
//后台管理员的模块化代码
header("Content-Type: text/html; charset=utf-8");

//云仓库存点
class Stock {
    
    public $open_stock = FALSE;//是否启用云仓库存功能（启用云仓库存功能需要关闭产品规格功能）
    
    public $check_stock = TRUE;//是否在云仓库存不足的情况下不允许审核下级订单
    
    public $can_refund_sotck = TRUE;//可以退库存

    public $is_top_supply = FALSE;//是否总部供货
    
    //级别对应0或1（0是上级供货，1是总部）
    public $is_top_supply_level = [
        1   =>  1,
        2   =>  2,
    ];

    public $type_name = array(
        'recharge'          =>  '云仓库存充入',
        'charge'            =>  '提货下单扣除',
        'radmin_recharge'   =>  '后台充入',
        'radmin_charge'     =>  '后台扣除',
        'audit_charge'      =>  '审核云仓库存单扣除',
        'admin_recharge'    =>  '代理转移充入',
        'admin_charge'      =>  '代理转移扣除',
        'recommend_charge'  =>  '发展代理扣除云仓库存',
        'recommend_recharge'=>  '授权充入云仓库存',
        'refund_charge'     =>  '云仓库存退回',
    );
    
    public $all_pay_type = array(
//      0   =>  '默认',//不需要支付截图
        1   =>  '虚拟币支付',
        2   =>  '支付截图',
    );
    
    //扣费属性
    public $charge_type = [
        'charge',
        'radmin_charge',
        'audit_charge',
        'admin_charge',
        'recommend_charge',
        'refund_charge',
    ];
    
    //充值属性
    public $recharge_type = [
        'recharge',
        'radmin_recharge',
        'admin_recharge',
        'recommend_recharge'
    ];
    
    //云仓退款申请状态
    public $stock_refund_apply_status = [
        '0' =>  '未审核',
        '1' =>  '已审核',
        '2' =>  '不通过',
    ];
    
    private $NewRebate_obj;
    
    
    private $stock_model;//云仓库存点
    private $stock_log_model;//云仓库存更改日志
    private $stock_order_model;//云仓库存订单
    private $stock_set_model;//云仓库存设置
    private $stock_refund_apply;//云仓退库存申请
    
    /**
     * 架构函数
     */
    public function __construct() {
        $this->stock_model = M('stock_point');
        $this->stock_log_model = M('stock_log');
        $this->stock_order_model = M('stock_order');
        $this->stock_set_model = M('stock_set');
        $this->stock_refund_apply = M('stock_refund_apply');
        
        import('Lib.Action.NewRebate','App');
        $this->NewRebate_obj = new NewRebate();
        
        $STOCK_ORDER = C('FUNCTION_MODULE')['STOCK_ORDER'];
        
        if( $STOCK_ORDER ){
            $this->open_stock = TRUE;
        }
        else{
            $this->open_stock = FALSE;
        }
    }
    
    
    //-------------------start 获取云仓库存记录---------------
    
    
    //获取云仓库存记录
    public function get_stock($page_info=array(),$condition=array(),$condition_special=array()){
        
        $stock_point_obj = M('stock_point');
        $templet_obj = M('templet');
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
        
        $key_for_temp_id = !isset($condition_special['key_for_temp_id'])?FALSE:$condition_special['key_for_temp_id'];
        $new_list = [];
        
        $count = $stock_point_obj->where($condition)->count();
        
        if( $count > 0 ){
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $stock_point_obj->where($condition)->order('uid desc')->page($page_con)->select();
            }
            else{
                $list = $stock_point_obj->where($condition)->order('uid desc')->select();
            }
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = array();
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
            array_values($pids);
            
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();
            
            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){
                
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }
            
            
            
            $condition_temp = array(
                'id'    =>  array('in',$pids),
            );
            $temp_info = $templet_obj->where($condition_temp)->select();
            
            $temp_key_info = array();
            foreach( $temp_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $temp_key_info[$v_tem_id] = $v_tem;
            }
            
            
            $dis_key_info['0']['name'] = '总部';
            
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_created = $v['created'];
                $v_updated = $v['updated'];
                
                
                
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['temp_info'] = $temp_key_info[$v_pid];
                $list[$k]['updated_foramt'] = !empty($v_updated)?date('Y-m-d H:i:s',$v_updated):0;
                
                
                if($key_for_temp_id){
                    $new_list[$v_pid] = $list[$k];
                }
                else{
                    $new_list[$k] = $list[$k];
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
        );
        
        return $return_result;
    }//end func get_stock
    
    
    //获取云仓库存日志
    public function get_stock_log($page_info=array(),$condition=array()){
        $stock_log_obj = M('stock_log');
        $distributor_obj = M('distributor');
        $templet_opj = M('templet');
        
        $type_name = $this->type_name;
        
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?10:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        
        $count = $stock_log_obj->where($condition)->count();
        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $stock_log_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $stock_log_obj->where($condition)->order('id desc')->select();
                
                //return $list;
            }
            
            
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = array();
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_note = $v['note'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }
                if( is_numeric($v_note) && !isset($uids[$v_note]) ){
                    $uids[$v_note] = $v_note;
                }
            }
            
            //用户信息
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
            
            //
            $condition_temp = array(
                'id'    =>  array('in',$pids),
            );
            $templet_info = $templet_opj->where($condition_temp)->select();
            
            $templet_key_info[0]['name'] = '全部';
            foreach( $templet_info as $v_temp ){
                
                $v_pid = $v_temp['id'];
                
                $templet_key_info[$v_pid] = $v_temp;
            }
            
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_type = $v['type'];
                $v_note = $v['note'];
                $v_created = $v['created'];
                
                if(in_array($v_type, $charge_type)){
                    $list[$k]['charge_status'] = '1';
                }else{
                    $list[$k]['charge_status'] = '0';
                }
                
                if( $v_type == 'admin_recharge' && is_numeric($v_note) ){
                    $v_note = '经销商转移云仓库存自：'.$dis_key_info[$v_note]['name'];
                }
                elseif( $v_type == 'admin_charge' && is_numeric($v_note) ){
                    $v_note = '经销商转移云仓库存到：'.$dis_key_info[$v_note]['name'];
                }
                
                $list[$k]['type_name']  = $type_name[$v_type];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['temp_info'] = $templet_key_info[$v_pid];
                $list[$k]['note'] = $v_note;
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
            'count' => $count,
        );
        
        return $return_result;
    }//end func get_stock_log
    
    
    
    //获取云仓库存记录
    public function get_stock_refund_apply($page_info=array(),$condition=array(),$condition_special=array()){
        
        $templet_obj = M('templet');
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');
        
        $status_name = $this->stock_refund_apply_status;
        
        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        
        $key_for_temp_id = !isset($condition_special['key_for_temp_id'])?FALSE:$condition_special['key_for_temp_id'];
        $new_list = [];
        
        $count = $this->stock_refund_apply->where($condition)->count();
        
        if( $count > 0 ){
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $this->stock_refund_apply->where($condition)->order('uid desc')->page($page_con)->select();
            }
            else{
                $list = $this->stock_refund_apply->where($condition)->order('uid desc')->select();
            }
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = array();
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
            array_values($pids);
            
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );
            $dis_info = $distributor_obj->where($condition_dis)->select();
            
            $dis_key_info = array();
            foreach( $dis_info as $k_dis=>$v_dis ){
                
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }
            
            
            
            $condition_temp = array(
                'id'    =>  array('in',$pids),
            );
            $temp_info = $templet_obj->where($condition_temp)->select();
            
            $temp_key_info = array();
            foreach( $temp_info as $k_tem => $v_tem ){
                $v_tem_id = $v_tem['id'];
                
                $temp_key_info[$v_tem_id] = $v_tem;
            }
            
            
            $dis_key_info['0']['name'] = '总部';
            
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_created = $v['created'];
                $v_updated = $v['update'];
                
                
                
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['temp_info'] = $temp_key_info[$v_pid];
                $list[$k]['created_foramt'] = !empty($v_created)?date('Y-m-d H:i',$v_created):0;
                $list[$k]['updated_foramt'] = !empty($v_updated)?date('Y-m-d H:i',$v_updated):0;
                
                
                if($key_for_temp_id){
                    $new_list[$v_pid] = $list[$k];
                }
                else{
                    $new_list[$k] = $list[$k];
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
            'count' => $count,
            'limit' => $page_con,
        );
        
        return $return_result;
    }//end func get_stock_refund_apply
    
    
    
    
    
    
    
    
    /**
     * 检查是否有足够的云仓库存点扣除
     * 
     * @param int $uid
     * @param array $charge_stock_info
     * @param int $type     1为默认，2为把未审核订单也检查
     * @return array
     */
    public function check_can_charge($uid,$charge_stock_info,$type=1){
        
        
        if( !$this->open_stock ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '未开启云仓库存记录，无须检查云仓库存！'
            );
            return $return_result;
        }
        
        
        if( !$this->check_stock ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不用检查是否有足够的云仓库存点进行扣除！'
            );
            return $return_result;
        }
        
       
        if( empty($uid) || empty($charge_stock_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！'
            );
            return $return_result;
        }
        
        
        $stock_point_obj = M('stock_point');
        
        
        $pids = array();
        $not_find_pids = array();
        $change_pids_info = array();//改变的产品云仓库存，以pid为键值
        
        
        $is_num_error = FALSE;
        $is_num_error_info = '';
        $charge_stock_key_info = array();
        foreach( $charge_stock_info as $k_s => $v_s ){
            
            $v_s_pid = $v_s['p_id'];
            $v_s_num = $v_s['num'];
            $v_s_p_name = $v_s['p_name'];
            
            $not_find_pids[$v_s_pid] = $v_s_pid;
            
            if( !empty($v_s_num) && $v_s_num > 0 ){
                $pids[] = $v_s_pid;
            }
            
            if( $v_s_num == 0 ){
                continue;
            }
            
            if( $v_s_num < 0 ){
                $is_num_error = TRUE;
                
            }
            
            $charge_stock_key_info[$v_s_pid] = $v_s;
        }
        
        
        if( empty($pids) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有选择任何产品！'
            );
            return $return_result;
        }
        
        
        //更改的云仓库存数据不能为负数
        if( $is_num_error ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '扣除云仓库存不能小于或0'
            );
            return $return_result;
        }
        
        
        $condition = array(
            'uid'   =>  $uid,
            'pid'   =>  array('in',$pids),
        );

        $stock_point_info = $stock_point_obj->where($condition)->select();
        
//        return $stock_point_info;
        
        if( empty($stock_point_info) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '没有找到云仓库存记录，请先进货！'
            );
            return $return_result;
        }
        
        //把未审核订单也计算在内
        $not_order_info = [];
        if( $type == 2 ){
            $order_model = M('order');
            
            $condition_order = [
                'user_id'   =>  $uid,
                'status'    =>  1,
             ];
            $order_info = $order_model->where($condition_order)->select();
            
            foreach( $order_info as $order_v ){
                $order_v_p_id = $order_v['p_id'];
                $order_v_num = $order_v['num'];
                
                $not_order_info[$order_v_p_id] = $order_v_num;
            }
        }
        
        
        $error_str = '';
        $can_not_charge = array();//不足扣的产品
        foreach( $stock_point_info as $k => $v ){
            
            $v_pid = $v['pid'];
            $v_num = $v['num'];
            
            if( isset($not_find_pids[$v_pid]) ){
                unset($not_find_pids[$v_pid]);
            }
            
            $the_charge_info = isset($charge_stock_key_info[$v_pid])?$charge_stock_key_info[$v_pid]:array();
            
            $charge_num = isset($the_charge_info['num'])?$the_charge_info['num']:0;
            $not_order_charge = isset($not_order_info[$v_pid])?$not_order_info[$v_pid]:0;
            $charge_num = bcadd($charge_num,$not_order_charge,0);
            
            $the_charge_p_name = isset($the_charge_info['p_name'])?$the_charge_info['p_name']:'';
            
            //扣除的云仓库存点少于可扣的云仓库存
            if( $v_num < $charge_num ){
                $error_str = $error_str.'产品'.$the_charge_p_name.'现有云仓库存'.'不足以扣除云仓库存点；';
                $can_not_charge[] = $v_pid;
            }
        }
        
        
        if( !empty($not_find_pids) ){
            $return_result = array(
                'code'  =>  6,
                'msg'   =>  '您没有足够的云仓库存，请查看云仓库存进行云仓库存下单增加云仓库存点！'
            );
            
            if( $type == 2 ){
                $return_result['msg'] = '您没有足够的云仓库存，请取消未审核订单或进行云仓库存下单增加云仓库存点！';
            }
            
            return $return_result;
        }
        
        
        if( !empty($error_str) ){
            $return_result = array(
                'code'  =>  7,
                'msg'   =>  $error_str
            );
            return $return_result;
        }
        
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '可扣除云仓库存点！'
        );
        return $return_result;
    }//end func check_can_charge
    
    
    //-------------------end 获取云仓库存记录---------------
    
    
    //-------------------start 记录云仓库存记录---------------
    
    
    
    
    
    /**
     * 转让云仓库存
     * @param type $uid     转让人
     * @param type $tid     被转让人
     * @param type $transfer_info       产品及产品数量
     */
    public function transfer_stock($uid,$tid,$transfer_info,$type='admin'){
        
        if( empty($uid) || empty($tid) || empty($transfer_info) ){
            $return_result = array( 
                'code'  =>  2,
                'msg'   =>  '参数错误！',
                'info'  =>  $transfer_info,
            );
            
            return $return_result;
        }
        
        if( $type == 'admin' ){
            $charge_type = 'admin_charge';
            $recharge_type = 'admin_recharge';
        }
        elseif( $type == 'recommend' ){
            $charge_type = 'recommend_charge';
            $recharge_type = 'recommend_recharge';
        }
        
        
        $check_can_charge = $this->check_can_charge($uid,$transfer_info);
        
        if( $check_can_charge['code'] != 1 ){
            return $check_can_charge;
        }
        
        //扣除自己云仓库存
        $charge_note = $tid;
        $admin_charge_result = $this->stock_point($uid,$transfer_info,$charge_type,$charge_note);
        
        if( $admin_charge_result['code'] != 1 ){
            return $admin_charge_result;
        }
        
        
        //增加转移对象云仓库存
        $recharge_note = $uid;
        $admin_recharge_result = $this->stock_point($tid,$transfer_info,$recharge_type,$recharge_note);
        
        if( $admin_recharge_result['code'] != 1 ){
            setLog(var_export($admin_recharge_result,1),'lib-stock-transfer_stock-error');
            return $admin_recharge_result;
        }
        
        
        $return_result = array( 
            'code'  =>  1,
            'msg'   =>  '转移成功！',
        );

        return $return_result;
    }//end func transfer_stock
    
    
    //推荐审核
    public function rec_aduit($dis_info){
        if( empty($dis_info) ){
            return $result = [
                'code'  =>  2,
                'msg'   =>  '参数错误!',
                'info'  =>  $dis_info,
            ];
        }
        
        $uid = $dis_info['id'];
        $recommendID = $dis_info['recommendID'];
        $pid = $dis_info['pid'];
        $level = $dis_info['level'];
        $templet_obj = M('templet');
        
        $condition = [
            'active'    =>  '1',
        ];
        $tempinfo = $templet_obj->field('id,name')->where($condition)->order('id desc')->find();
        
        if( empty($tempinfo) ){
            return $result = [
                'code'  =>  3,
                'msg'   =>  '没有产品信息!',
                'info'  =>  $tempinfo,
            ];
        }
            

        $condition_stock = [
            'level' =>  $level,
        ];
        $info = $this->stock_set_model->where($condition_stock)->find();

        if( empty($info) || empty($info['point']) ){
            return $result = [
                'code'  =>  4,
                'msg'   =>  '没有定义云仓库存设置!',
                'info'  =>  $info,
            ];
        }
        
        if( !empty($pid) ){
            $transfer_info = [
                [   
                    'p_id'   => $tempinfo['id'], 
                    'num'   => $info['point'] ,
                    'p_name'=>  $tempinfo['name'],
                ]
            ];

            $result = $this->transfer_stock($pid,$uid,$transfer_info,'recommend');
        }
        else{
            $transfer_info = [
                [   
                    'p_id'   => $tempinfo['id'], 
                    'num'   => $info['point'] ,
                    'p_name'=>  $tempinfo['name'],
                ]
            ];
            $result = $this->stock_point($uid,$transfer_info,'recommend_recharge');
        }
        
        
        
        return $result;
    }
    
    
    
    
    //使用云仓库存下单
    public function stock_to_order($uid,$write_info){
        
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        
        $templet_obj = M('templet');
        $distributor_obj = M('distributor');
        $order_obj = M('Order');
        
        $address = M('address')->where(['user_id' => $uid, 'default' => 1])->find();
        //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
        
        //参数判断
        if( empty($p_ids) || empty($p_nums) || !is_array($p_ids) 
                || !is_array($p_nums) || empty($address) ){
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
        $order_num = rand(10000,99999).mktime();
        
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
        
        
//        $o_id = $manager['pid'];
        $o_id = 0;
        $tallestID = $manager['tallestID'];
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        
//        //是否总部供货
//        if( $Order->is_top_supply ){
//            $o_id = 0;
//        }
//        
//        //如果属于最高级别经销商，供货商都是总部
//        if ($manager['level'] == 1) {
//            $o_id = 0;
//        }
        
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
        
        $condition_temp = [
            'id'    =>  ['in',$p_ids],
        ];
        
        $templet_info = $templet_obj->where($condition_temp)->select();
        
        if( empty($templet_info) ){
            $return_result = array(
                'code'  =>  -2,
                'msg'   =>  '没有产品信息！',
                'info'  =>  $templet_obj->getLastSql(),
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
            $p_image = $templet_key_info[$p_id]['image'];//产品名称
            
            
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
                'image' =>  $p_image,
                'num'   =>  $p_num,
                'price' =>  $p_price,
                'par_price' =>  $p_partent_price,//上级单价
                'par_profit'   =>  $p_price_profit,//上级利润
                'tem_info'  =>  $p_price_all_info,
            );
        }
        
        
//        $this_order_info = array(
//            'total_num' =>  $total_num,
//            'total_money'   =>  $total_price,
//        );
//        
//        $order_limit_result = $this->order_limit($uid,$manager,$this_order_info);
//        
//        if( $order_limit_result['code'] != 1 ){
//            $return_result = array(
//                'code'  =>  '-6',
//                'msg'   =>  !empty($order_limit_result['msg'])?$order_limit_result['msg']:'订单限制未通过',
//            );
//            return $return_result;
//        }
        
        
        //判断金额
        if( $total_price <= 0 ){
            $return_result = array(
                'code'  =>  '-3',
                'msg'   =>  '订单总金额不能小于或等于0！',
                'info'  =>  $add_order_info,
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
        
        //下单限制，只针对招商代理
        $ratio = C('FIRST_ORDER_NUM');
        if ($manager['level'] == 6) {
            $order = $order_obj->where(['user_id' => $uid])->find();
            if (!$order) {
                if ($total_num < $ratio) {
                    $return_result = array(
                        'code'  =>  '-6',
                        'msg'   =>  '首次下单数量必须大于'.$ratio
                    );
                    return $return_result;
                }
            }
        }
        
        
        //判断上级利润
        if( $total_partent_profit <= 0 ){
            $total_partent_profit = 0;
        }
        
        
        //--------end 计算订单相关信息------
        
        
        //检查云仓库存是否足够扣
        $check_can_charge = $this->check_can_charge($uid,$add_order_info);
        
        if( $check_can_charge['code'] != 1 ){
            return $check_can_charge;
        }
        
        
        
        
        
        //----------生成订单------------
        $error_info = array();
        $order_info = array();
        foreach( $add_order_info as $the_order_info ){
            $the_p_name = $the_order_info['name'];
            $the_p_image = $the_order_info['image'];
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
                'p_image' => $the_p_image,
                'status' => 6,              //订单状态，默认1为未审核
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
                'month' => date('Ym'),
                'province'=>$address['province'],
                'city'=>$address['city'],
                'county'=>$address['area'],
            );
            
            
            $addorder = $order_obj->add($arr);
            
//            $arr['last_sql']    =   $order_obj->getLastSql();
//            $error_info[]   =   $arr;
            
            
            if( !$addorder ){
                break;
            }
            
            $order_info[]  = $arr;
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
            
            
            $record_by_order_result = $this->record_by_order_info($uid,$order_info,2);
            
//            return $record_by_order_result;
            
            if( $record_by_order_result['code'] != 1 ){
                setLog(var_export($record_by_order_result,1).',$order_info:'.var_export($order_info,1),'lib-stock-stock_to_order-record_by_order_info-error');
            }
            
            
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '下单成功！',
                'order_num' =>  $order_num,
            );
            
            return $return_result;
        }
    }//end func stock_to_order
    
    
    /**
     * 云仓库存订单
     * （代理后台云仓库存下单，总部后台审核，代理增加相应的云仓库存）
     * 
     */
    public function stock_order($uid,$write_info){
        $order_num = $write_info['order_num'];
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        $cart_ids = explode('|', $write_info['cart_ids']);
        $pay_type = $write_info['pay_type'];
        $pay_photo = $write_info['pay_photo'];
        $templet_obj = M('templet');
        $distributor_obj = M('distributor');
        $buy_way = $write_info['buy_way']; //1为向上级买货  2为向总部买货

        //属性
        $sku_ids = $write_info['sku_ids'];
        
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        
        import('Lib.Action.Order','App');
        $Order = new Order();

        foreach ($sku_ids as $key => $id) {

            $sku_info = $sku->get_templet_sku($id);
            if (!$sku->check_templet_quantity($sku_info, $id, $p_ids[$key], $p_nums[$key])) {
                $return_result = array( 
                    'code'  =>  -1,
                    'msg'   =>  '云仓库存不足，请重新下单!',
                );
                
                return $return_result;
            }
        }

        
        $address = M('address')->where(['user_id' => $uid, 'default' => 1])->find();
        //2016.10.17重构，逻辑：订单提交应该只传产品ID，及对应数量，后端进行相应的运算
        
        //参数判断
        if( empty($order_num) || empty($p_ids) || empty($p_nums) || !is_array($p_ids) 
                || !is_array($p_nums)){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '请确认您是否已选择商品，并填写完整的收货信息！',
//                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }

//      if(empty($address['phone']) || empty($address['name']) || empty($address['province']) || empty($address['city']) ||empty($address['area'])||empty($address['address'])){
//          $return_result = array(
//              'code'  =>  -1,
//              'msg'   =>  '收货信息不完整！',
//          );
//          return $return_result;
//      }
//      if(strlen($address['phone']) != '11'){
//          $return_result = array(
//              'code'  =>  -1,
//              'msg'   =>  '手机号码长度有误！',
//          );
//          return $return_result;
//      }

          if( $pay_type == 2 && empty($pay_photo) ){
              $return_result = array( 
                  'code'  =>  -1,
                  'msg'   =>  '请提交您的支付截图！',
              );
              
              return $return_result;
          }
//        
//        if( !isset($this->all_pay_type[$pay_type]) ){
//            $return_result = array( 
//                'code'  =>  -1,
//                'msg'   =>  '非可用的支付类型！',
//                'pay_type'  =>  $pay_type,
//            );
//            
//            return $return_result;
//        }
        
        
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

        //用于计算向上级还是总部买货
        if($buy_way == 1){
            $o_id=$o_id;
        }elseif ($buy_way == 2 ){
            $o_id=0;
        }else{
            //是否总部供货
            $is_top_supply_level = $this->is_top_supply_level;
            //如果该级别有特殊的限定则根据规则进行
            if( isset($is_top_supply_level[$manager_level]) && $is_top_supply_level[$manager_level] == 1 ){
                $o_id = 0;
            }
            elseif( isset($is_top_supply_level[$manager_level]) && $is_top_supply_level[$manager_level] == 0 ){
                $o_id = $manager['pid'];
            }
            elseif( $this->is_top_supply && !isset($is_top_supply_level[$manager_level]) ){
                $o_id = 0;
            }
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
        $total_price = 0;//该次下单总金额
        $total_num = 0;//该次下单的总数量
        $total_partent_profit = 0;//该次下单上级的总利润
        $add_order_info = array();//写入订单的部分信息
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
            );
        }
        
        
        $this_order_info = array(
            'total_num' =>  $total_num,
            'total_money'   =>  $total_price,
            //'total_shipping_fee'=>$total_shipping_fee,
            //'sum_price'=>$sum_price,
            'order_detail' => $add_order_info_key,
        );
        
        $order_limit_result = $Order->order_limit($uid,$manager,$this_order_info);
        
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

        if( $pay_type == 1 || $pay_type == 0 ){
            import('Lib.Action.Funds','App');
            $Funds = new Funds();
            $check_money = $Funds->check_recharge_money($uid,$total_price);
            
            if( !$check_money ){
                $return_result = array(
                    'code'  =>  '-5',
                    'msg'   =>  '请检查您的余额！'
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
                //属性
                'sku_id' => $the_sku_id,    //商品属性ID
                'properties' => $properties,
                'style' => $style
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
            
            $addorder = $this->stock_order_model->add($arr);
            
            $arr['last_sql']    =   $this->stock_order_model->getDbError();
            $error_info[]   =   $arr;
            
            if( !$addorder ){
                break;
            }

            //属性
            //如果是向上级发货并且代理有云仓库存功能，则加减云仓库存需要判断

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
                    'total_price' => $total_price
                ];
                $message->push(trim($openid), $content , $message->stock_order_new);
            }
            
            //如果是购物车就删除id
            if ($cart_ids) {
                M('order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            }
            return $return_result;
        }
    }
    
    
    
    
    
    
    
    
    
    //购买时直接转化为云仓库存
    public function conversion_stock($uid,$write_info){
        
        $p_ids = $write_info['p_ids'];
        $p_nums = $write_info['p_nums'];
        $note = isset($write_info['note'])?$write_info['note']:NULL;
        $cart_ids = explode('|', $write_info['cart_ids']);
        
        $templet_obj = M('templet');
        $order_obj = M('Order');
        $distributor_obj = M('distributor');
        
        //参数判断
        if( empty($p_ids) || empty($p_nums) || !is_array($p_ids) 
                || !is_array($p_nums) ){
            $return_result = array( 
                'code'  =>  -1,
                'msg'   =>  '请确认您是否已选择商品！',
            );
            
            return $return_result;
        }
        
        
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
        $total_price = 0;//该次下单总金额
        $total_num = 0;//该次下单的总数量
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
            $p_image = $templet_key_info[$p_id]['image'];//产品名称
            
            
            //如果产品数量选择小于或等于0，不进行计算
            if( $p_num <= 0 ){
                continue;
            }
            
            
            $p_price_all_info = $templet_key_info[$p_id];//该产品的所有信息
            
            $the_total_price = bcmul($p_price,$p_num,2);
            $total_price = bcadd($total_price,$the_total_price,2);
            
            $total_num+=$p_num;
            
            $add_order_info[] = array(
                'p_id'  =>  $p_id,//产品ID
                'name'  =>  $p_name,
                'image' => $p_image,
                'num'   =>  $p_num,
                'price' =>  $p_price,
                'tem_info'  =>  $p_price_all_info,
            );
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
        
        //--------end 计算订单相关信息------
        
        
        if( empty($add_order_info) ){
            $return_result = array(
                'code'  =>  '-5',
                'msg'   =>  '无产品信息！'
            );
            return $return_result;
        }
        
        $add_order_info[0]['order_num'] = '';
        $add_order_info[0]['total_price'] = $total_price;
        $add_order_info[0]['total_num'] = $total_num;
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        //----------扣费逻辑------------
        $charge_money_result = $Funds->charge_money($uid,$total_price,'stock');

        if( $charge_money_result['code'] != 1 ){
            $charge_money_result['status']  =   0;
            return $charge_money_result;
        }
        //----------end 扣费逻辑------------
        
        
        //存入云仓库存
        $stock_result = $this->stock_point($uid,$add_order_info,'recharge',$note);
        
        if( $stock_result['code'] != 1 ){
            $stock_result['code'] = 'stock-'.$stock_result['code'];
            return $stock_result;
        }
        else{
            //生成返利
            import('Lib.Action.Rebate','App');
            $Rebate = new Rebate();
            $Rebate->radmin_order_audit_rebate($uid,$add_order_info);
            
            //----------积分触发----------------------
            import('Lib.Action.Integral','App');
            $Integral = new Integral();
            $aduit_order_result = $Integral->aduit_order($uid,$add_order_info);
            
            if( $aduit_order_result['code'] != 1 ){
                setLog(var_export($aduit_order_result,1),'lib-stock-stock_to_order-aduit_order-error');
            }
            //----------end 积分触发----------------------
            
            
            //新订单模板消息
            if ($o_id) {
                import('Lib.Action.Message','App');
                $message = new Message();
                $openid = $distributor_obj->where(['id' => $o_id])->getField('openid');
                $content = [
                    'customer_info' => $manager,
                    'total_price' => $total_price
                ];
                $message->push(trim($openid), $content , $message->stock_order_new);
            }
            
            
            //如果是购物车就删除id
            if ($cart_ids) {
                M('order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();
            }
            
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '云仓库存下单成功！',
            );
            
            return $return_result;
        }
        
        
        
    }//end func conversion_stock
    
    
    
    /**
     * 审核云仓库存订单通过
     * @param array $order_nums //ps:$order_nums = ['123456789213','85153333333'];
     * @param string $type      //总部后台审核为radmin，代理后台审核为admin
     * @return array
     */
    public function audit_order($order_nums,$type=''){
        
        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        if( !in_array($type,['radmin','admin']) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '请输入审核的主体！',
            );
            return $return_result;
        }
        
        
        $templet_obj = M('templet');
        
        //--------订单信息-----------------
        $condition_order['order_num']   =   array('in',$order_nums);
        
        //这里获取的会出现例如两个order_num一样的订单，注意分清
        $order_info = $this->stock_order_model->where($condition_order)->select();
        
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
        $is_record_by_order = FALSE;
        $charge_money_result = array();
        
        import('Lib.Action.Funds','App');
        $Funds = new Funds();
        
        import('Lib.Action.Rebate','App');
        $Rebate = new Rebate();
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
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
            //----------start 云仓库存点-----------

            $record_by_order = $this->record_by_order_info($uid,$order_info_key2[$order_num]);

            if( $record_by_order['code'] != 1 ){
                $is_record_by_order = TRUE;
                break;
            }
            //----------end 云仓库存点-----------
            
            //----------扣费逻辑------------
            $charge_money_result = $Funds->charge_money($uid,$total_price,'order',$order_num);
            
            if( $charge_money_result['code'] != 1 ){
                $charge_money_result['status']  =   0;
                $is_charge_money_break = TRUE;
                break;
            }
            //----------end 扣费逻辑------------
            
            //----------订单返还-----------
            //如果可以退库存，那订单返还只能在提货时
            if( $Funds->is_order_return && !$this->can_refund_sotck ){
                
                $monery_order_return_result = $Funds->monery_order_return($uid,$order_num,$order_info_key2[$order_num]);


                if( $monery_order_return_result['code'] != 1 ){
                    setLog('云仓订单号：'.$order_num.'----返回信息为：'.var_dump($monery_order_return_result,1), 'order_return_money');
                }
            }
            //----------end 订单返还-----------
            
            




            $order_save_info = array(
                'status'    =>  2,
                'paytime'    =>  time(),
            );
            
            $this->stock_order_model->where(array('order_num' => $order_num))->save($order_save_info);//6为已审核未配送状态
            
            
            //订单审核模板消息
            import('Lib.Action.Message','App');
            $message = new Message();
            $openid = $dis_info['openid'];
            $message->push(trim($openid), $order_info_key[$order_num] , $message->stock_audit);
            
            
            //---------生成订单统计-------------
            if( !$this->can_refund_sotck ){
                $Order->generate_order_count($order_info_key2[$order_num]);
            }
            //---------end 生成订单统计-------------

            //----------生成返利--------------
            $Rebate->radmin_order_audit_rebate($uid,$order_info_key2[$order_num]);
            //----------end 生成返利--------------
            
            
            //----------积分触发----------------------
//            $Integral->aduit_order($uid,$order_info_key2[$order_num]);
            //----------end 积分触发----------------------
            
            
            //------------触发团队返利----------------
            //如果开启团队业绩并且是实时统计的，则触发
            if(C('REBATE')['ORDINARY_TEAM'] && (!C('REBATE')['CLICK_TEAM_REBATE'])){
                $Order->NewRebate_obj->create_team_rebate($uid);
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
        elseif( $is_record_by_order ){
//          var_dump(3);die;
            $record_by_order['codetip'] = 'audit_order';
            return $record_by_order;
        }
        else{
            $order_num_str = implode(',', $order_nums);
            
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '订单号：'.$order_num_str.'的云仓库存订单审核通过！',
                'status'=>  1,
            );
            return $return_result;
        }
        
    }//end func audit_order
    
    
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
        
        $order_info = $this->stock_order_model->where($where)->group('order_num')->select();
        
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
                'msg'   =>  '删除的订单有未审核的订单，不能删除！',
            );
            return $return_result;
        }
        
        $all_order_info = $this->stock_order_model->where($where)->select();
        
        $del_res = $this->stock_order_model->where($where)->delete();
        
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
                'msg'   =>  '云仓订单'.$order_num_format.'删除成功！',
            );
            return $return_result;
        }
        
        
    }//end func delorder
    
    
    
    //根据订单号进行记录
    public function record_by_order_num($uid,$order_num,$type=1){
        
        if( !$this->open_stock ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不进行云仓库存记录！',
            );
            return $return_result;
        }
        
        
        if( empty($uid) || empty($order_num) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误',
            );
            return $return_result;
        }
        
        $order_obj = M('order');
        
        $condition = array(
            'order_num' =>  $order_num,
        );
        
        $order_info = $order_obj->where($condition)->select();
        
        if( empty($order_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '找不到订单！',
            );
            return $return_result;
        }
        
        
        $result = $this->record_by_order_info($uid,$order_info,$type);
        
        
        return $result;
    }//end func record
    
    
    //根据订单信息进行记录（订单信息必须为该订单号的所有信息，多维数组）
    public function record_by_order_info($uid,$order_info,$type=1){
        
        if( !$this->open_stock ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不进行云仓库存记录！',
            );
            return $return_result;
        }
        
        if( empty($order_info) || empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误',
            );
            return $return_result;
        }
        
        $status_error = FALSE;//订单状态
        foreach( $order_info as $k => $v ){
            
            $v_o_id = $v['o_id'];//接单供货商
            $v_p_id = $v['p_id'];//产品ID
            $v_p_name = $v['p_name'];//产品名字
            $v_num = $v['num'];//产品数量
            $v_status = $v['status'];//订单状态
            
            
            //未审核
            if( $v_status != 1 ){
                $status_error = TRUE;
                break;
            }
        }
        
        //云仓库存下单
        if( $type == 1 ){
            
            if( $v_o_id != 0 ){
                $check_can_charge_result = $this->check_can_charge($v_o_id,$order_info);
            
                if( $check_can_charge_result['code'] != 1 ){
                    return $check_can_charge_result;
                }
            }
            
            //对于下单的经销商是充入云仓库存点
            $recharge_result = $this->stock_point($uid,$order_info,'recharge');
            
            if( $recharge_result['code'] != 1 ){
                return $recharge_result;
            }
            $charge_result = array();
            if( $v_o_id != 0 ){
                $charge_result = $this->stock_point($v_o_id,$order_info,'audit_charge');
            }
        }
        //提货单下单
        elseif( $type == 2 ){
            $check_can_charge_result = $this->check_can_charge($uid,$order_info);
            
            if( $check_can_charge_result['code'] != 1 ){
                return $check_can_charge_result;
            }
            
            //对于下单的经销商是扣除云仓库存点
            $recharge_result = $this->stock_point($uid,$order_info,'charge');

            if( $recharge_result['code'] != 1 ){
                return $recharge_result;
            }
            
            $charge_result = array();
//            if( $v_o_id != 0 ){
//                $charge_result = $this->stock_point($v_o_id,$order_info,'charge');
//            }
        }
        
        
        
        if( $status_error ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '订单状态有误',
            );
            return $return_result;
        }
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '云仓库存点更新成功！',
//            'order_info' =>  $order_info,
            'all_info'  =>  array(
                'recharge' => $recharge_result,
                'charge' => $charge_result
            ),
        );
        return $return_result;
        
    }//end func record_by_order_info
    
    
    
    
    /**
     * 充入云仓库存点
     * 
     * @param int $uid
     * @param array $stock_info //$stock_info = array( array( 'p_id'=> 1 , 'num' => 3 ,'p_name'=>'X产品' ),array( 'p_id'=> 2 , 'num' => 6,'p_name'=>'Y产品' ) )
     * @param array $type
     * @return array
     */
    public function stock_point($uid,$stock_info,$type,$note=''){
        
        if( !$this->open_stock ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不进行云仓库存记录！',
            );
            return $return_result;
        }
        
        if( empty($uid) || empty($stock_info) || empty($type) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误'
            );
            return $return_result;
        }
        
        
        $pids = array();
        $add_pids = array();//需要添加的产品云仓库存信息
        $change_pids_info = array();//改变的产品云仓库存，以pid为键值
        
        $is_num_error = FALSE;
        $stock_error = '';
        
        foreach( $stock_info as $k_s => $v_s ){
            
            if( !is_array($v_s) || !isset($v_s['p_id']) || !isset($v_s['num']) ){
                $stock_error = '充入库存的产品信息格式错误！';
                break;
            }
            
            $v_s_pid = $v_s['p_id'];
            $v_s_num = $v_s['num'];
            
            $add_pids[$v_s_pid] = $v_s_pid;
            
            if( !empty($v_s_num) && $v_s_num > 0 ){
                $pids[] = $v_s_pid;
            }
            
            if( $v_s_num == 0 ){
                continue;
            }
            
            if( $v_s_num < 0 ){
                $is_num_error = TRUE;
                break;
            }
            
            $change_pids_info[$v_s_pid] = $v_s_num;
        }
        
        if( !empty($stock_error) ){
            $return_result = array(
                'code'  =>  9,
                'msg'   =>  $stock_error,
            );
            return $return_result;
        }
        
        
        //更改的云仓库存数据不能为负数
        if( $is_num_error ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '更改的云仓库存数值不能为小于0'
            );
            return $return_result;
        }
        
        if( empty($pids) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '没有任何云仓库存数据'
            );
            return $return_result;
        }
        
        //云仓库存点
        if( !in_array($type, $this->charge_type) && !in_array($type, $this->recharge_type) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '执行的类型超出范围！'
            );
            return $return_result;
        }
        
        //执行的
        if( in_array($type, $this->recharge_type) ){
            $true_type = 'recharge';
            $true_type_name = '充入';
        }
        elseif( in_array($type, $this->charge_type) ){
            $true_type = 'charge';
            $true_type_name = '扣除';
        }
        
        
        $order_num = isset($stock_info['0']['order_num'])?$stock_info['0']['order_num']:'';
        
        
        
        $stock_point_obj = M('stock_point');
        
        $condition = array(
            'uid'   =>  $uid,
            'pid'   =>  array('in',$pids),
        );
        
        $stock_point_info = $stock_point_obj->where($condition)->select();
        
        
        //如果是云仓库存点的扣除操作，要检查是否有足够扣的云仓库存点
        if( $type == 'charge' && !empty($stock_point_info) ){
            $check_can_charge_result = $this->check_can_charge($uid,$stock_info);
            
            if( $check_can_charge_result['code'] != 1 ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  $check_can_charge_result['msg'],
                );
                return $return_result;
            }
        }
        
        $test_result = array(
            'code'  =>  -2,
        );
        
        //如果有产品的云仓库存信息，就直接修改
        if( !empty($stock_point_info) ){
            
            $condition_save['uid']  =   $uid;
            
            foreach( $stock_point_info as $k_p => $v_p ){
                
                $v_p_pid = $v_p['pid'];
                $v_p_num = $v_p['num'];
                
                //得到没有产品云仓库存记录的产品ID
                if( isset($add_pids[$v_p_pid]) ){
                    unset($add_pids[$v_p_pid]);
                }
                
                
                $condition_save['pid']  =   $v_p_pid;
                
                $change_num = isset($change_pids_info[$v_p_pid])?$change_pids_info[$v_p_pid]:0;
                
                $save_info['updated'] = time();
                
                $save_num = $v_p_num;
                
                //充入
                if( $true_type == 'recharge' ){
                    $save_num = bcadd ($v_p_num,$change_num);
                }
                //扣除
                elseif( $true_type == 'charge' ){
                    $save_num = bcsub($v_p_num,$change_num);;
                }

                //无更改不记录
                if( $save_num == $v_p_num ){
                    continue;;
                }
                
                $save_info['num'] = $save_num;
                $save_info['updated'] = time();
                
                $test_result[] = $save_info;

                $save_result = $stock_point_obj->where($condition_save)->save($save_info);
                if( !$save_result ){
                    setLog($uid.'---云仓库存记录编辑失败（stock_point），condition:'.print_r($condition_save,1).'，save_info：'.print_r($save_info,1).'，sql：'.$stock_point_obj->getDbError(),'stock_point_error');
                }
                else{
                    //添加日志
                    $add_log_res = $this->add_stock_log($uid,$v_p_pid,$change_num,$type,$order_num,$note);
                    
                    if( $add_log_res['code'] != 1 ){
                        setLog($uid.'---云仓库存添加记录失败（stock_point）:'.var_export($add_log_res,1), 'add_stock_log_error' );
                    }
                }
            }
            
            array_filter($add_pids);
        }//end if( !empty($stock_point_info) )
        
        
//        return $test_result;
        
        
        //在扣除的情况下如果还有未记录的的产品云仓库存信息，需要排查（在云仓库存需要使用的情况下）
        if( !empty($add_pids) && $true_type == 'charge' && $this->check_stock ){
            setLog($uid.'---云仓库存记录错误（stock_point），$add_pids:'.print_r($add_pids,1).'，$type：'.$type,'stock_point_error');
        }
        //如果还有未记录的产品云仓库存信息
        elseif( !empty($add_pids) ){
            $add_info['uid']    =   $uid;
            
            foreach( $add_pids as $the_add_pid ){
                
                $change_num = isset($change_pids_info[$the_add_pid])?$change_pids_info[$the_add_pid]:0;
                
                if( $true_type == 'charge' ){
                    $change_num =  bcsub(0,$change_num);
                }
                
                
                $add_info['pid']    =   $the_add_pid;
                $add_info['num']    =   $change_num;
                $add_info['created'] = time();
                
                $add_result = $stock_point_obj->add($add_info);
                
                if( !$add_result ){
                    setLog($uid.'---云仓库存记录添加失败（stock_point），add_info：'.print_r($add_info,1).'，sql：'.$stock_point_obj->getDbError(),'stock_point_error');
                }
                else{
                    $add_log_res = $this->add_stock_log($uid,$the_add_pid,$change_num,$type,$order_num,$note);
                    if( $add_log_res['code'] != 1 ){
                        setLog($uid.'---云仓库存添加记录失败（stock_point）:'.var_export($add_log_res,1), 'add_stock_log_error' );
                    }
                }
            }
        }//end if( !empty($add_pids) )
        
        
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '用户id'.$uid.','.$true_type_name.'云仓库存点更新成功',
            'info'  =>  $add_pids,
        );
        return $return_result;
    }//end func stock_point
    
    
    
    
    
    
    
    /**
     * 添加云仓库存日志
     * @param int $uid
     * @param int $pid
     * @param int $point
     * @param string $type
     * @param string $order_num
     * @param string $note
     * @return array
     */
    public function add_stock_log($uid,$pid,$point,$type,$order_num='',$note=''){
        
        //
        if( empty($uid) || empty($pid) || empty($point) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
                'info'  =>  [
                    $uid,$pid,$point,$type,$order_num,$note
                ],
            );
            return $return_result;
        }
        
        
        $stock_log_obj = M('stock_log');
        
        
        $add_log = array(
            'uid'   =>  $uid,
            'pid'   =>  $pid,
            'point' =>  $point,
            'type'  =>  $type,
            'order_num' =>  $order_num,
            'note'      =>  $note,
            'created'   =>  time(),
        );
        
        
        $add_result = $stock_log_obj->add($add_log);
        
        if( $add_result ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '添加日志成功！',
                'add_id'=>  $add_result,
            );
            return $return_result;
        }
        else{
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '添加日志失败！',
                'info'  =>  $stock_log_obj->getDbError(),
            );
            return $return_result;
        }
    }//end func add_stock_log
    
    
    
    /**
     * 添加云仓库存退回申请
     * @param type $uid
     * @param type $stock_info  
     * @return string|int
     */
    public function add_stock_refund_apply($uid,$stock_info){
        
        if( !$this->can_refund_sotck ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '还没开放换退库存申请！',
            );
            return $return_result;
        }
        
        if( empty($uid) || empty($stock_info) || !is_array($stock_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $error_msg = '';
        foreach( $stock_info as $k_s => $v_s ){
            
            $v_s_pid = $v_s['p_id'];
            $v_s_num = $v_s['num'];
            
            if( $v_s_num <= 0 ){
                $error_msg = '申请退库存的产品数量不能少于或等于0';
            }
        }
        
        if( !empty($error_msg) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  $error_msg,
            );
            return $return_result;
        }
        
        
        $old_info = $this->stock_refund_apply->where(['uid'=>$uid,'status'=>0])->field('id')->find();
        
        if( !empty($old_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '已经有未审核的库存申请，必须要审核后才能继续提交申请！',
            );
            return $return_result;
        }
        
        $check_can_charge = $this->check_can_charge($uid,$stock_info,2);
        
        if( $check_can_charge['code'] != 1 ){
            return $check_can_charge;
        }
        
        $error_info = [];
        foreach( $stock_info as $k_s => $v_s ){
            
            $v_s_pid = $v_s['p_id'];
            $v_s_num = $v_s['num'];
            
            $new_info = [
                'uid'   =>  $uid,
                'pid'   =>  $v_s_pid,
                'num'   =>  $v_s_num,
                'status'=>  0,
                'created'   =>  time(),
                'update'    =>  time(),
            ];
            
            $add_res = $this->stock_refund_apply->add($new_info);
            
            if( !$add_res ){
                $error_info[$k_s]['add'] = $new_info;
                $error_info[$k_s]['db'] = $this->stock_refund_apply->getDbError();
            }
        }
        
        if( !empty($error_info) ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '提交申请失败！',
                'info'  =>  $error_info,
            );
        }
        else{
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '提交申请成功！',
            );
        }
        
        return $return_result;
    }
    
    
    /**
     * 通过云仓库存退回
     * @param type $apply_id    审核的申请ID
     * @param type $pass        1为通过，2为不通过
     * @param type $note        备注
     * @return array
     */
    public function pass_stock_refund_apply($apply_id,$pass,$note=''){
        
        if( empty($apply_id) || !in_array($pass, [1,2]) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        $condition = [
            'id'    =>  $apply_id,
        ];
        
        
        $apply_info = $this->stock_refund_apply->where($condition)->find();
        
        if( empty($apply_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '没有找到此条申请信息！',
            );
            return $return_result;
        }
        else if( $apply_info['status'] != 0 ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '只有未审核的申请才能再进行审核！',
            );
            return $return_result;
        }
        
        $save_result = FALSE;
        if( $pass == 2 ){
            
            $save_info = [
                'status'    =>  $pass,
                'note'      =>  $note,
                'update'    =>  time(),
            ];
            
            $save_result = $this->stock_refund_apply->where($condition)->save($save_info);
        }
        else if( $pass == 1 ){
            $uid = $apply_info['uid'];
            $pid = $apply_info['pid'];
            $num = $apply_info['num'];
            
            
            //--------start 判断----------
            
            if( $num <= 0 ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  '申请的数量必须大于0！',
                );
                return $return_result;
            }
            
            $condition_dis = [
                'id'   =>  $uid,
            ];
            $dis_level = M('distributor')->where($condition_dis)->getField('level');
            
            if( empty($dis_level) ){
                $return_result = array(
                    'code'  =>  4,
                    'msg'   =>  '没有找到该条用户！',
                );
                return $return_result;
            }
            
            $condition_temp = [
                'id'    =>  $pid,
            ];
            
            $templet = M('templet')->where($condition_temp)->find();
            
            if( empty($templet) ){
                $return_result = array(
                    'code'  =>  5,
                    'msg'   =>  '没有找到该云仓对应的产品信息！',
                );
                return $return_result;
            }
            
            $temp_key = 'price'.$dis_level;
            
            $templet_price = $templet[$temp_key];
            
            $total_money = bcmul($templet_price,$num,2);
            
            
            
            //--------end 判断----------
            
            
            //--------start 退库存-------------
            $stock_info[] = [
                'p_id'  =>  $pid,
                'num'   =>  $num,
                'p_name'    =>  $templet['name'],
            ];
            
            $stock_point_result = $this->stock_point($uid,$stock_info,'refund_charge','云仓库存退回');
            
            if( $stock_point_result['code'] != 1 ){
                return $stock_point_result;
            }
            //--------end 退库存-------------
            
            
            //--------start 充值回相应的金额金额-------------
            //此产品金额为0，直接退库存！
            if( $total_money > 0 ){
                import('Lib.Action.Funds','App');
                $Funds = new Funds();


                $recharge_result = $Funds->recharge($uid,$total_money,'refund_stock_recharge');

                if( $recharge_result['code'] != 1 ){
                    return $recharge_result;
                }
            }
            
            
            
            //--------end 充值回相应的金额金额-------------
            
            
            $save_info = [
                'status'    =>  $pass,
                'note'      =>  $note,
                'update'    =>  time(),
            ];
            
            $save_result = $this->stock_refund_apply->where($condition)->save($save_info);
        }
        
        
        
        
        if( !$save_result ){
            $return_result = array(
                'code'  =>  10,
                'msg'   =>  '审核申请id:'.$apply_id.' 失败，请重试！',
            );
            
            return $return_result;
        }
        
        if( $pass == 2 ){
            $msg = '为不通过！';
        }
        else{
            $msg = '为通过！';
        }
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '审核申请id:'.$apply_id.$msg,
            'stock_point_result'    =>  $stock_point_result,
        );
        return $return_result;
    }
    
    
    
    
    //-------------------end 记录云仓库存记录---------------
    
    
    
    
    
    
    
    
    
}