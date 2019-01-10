<?php
//积分系统模块
header("Content-Type: text/html; charset=utf-8");


class Integral {
    
    public $Integral_obj = 'Integral';//用户积分信息
    
    public $Integral_log_obj = 'Integral_log';//用户积分记录
    
    public $Integral_rule_obj = 'Integral_rule';//积分规则
    
    public $Integral_level_obj = 'Integral_level';//积分门槛
    
    public $integral_open = FALSE;//是否开启积分功能
    
    public $integral_status = array(
        1   =>  '总部充入',
        2   =>  '总部扣除',
        3   =>  '经销商签到',
//        4   =>  '高发展低',
        5   =>  '平推',
        6   =>  '低推荐高',
        7   =>  '下单数量',
        8   =>  '下单金额',   
        9   =>  '直属代理升级',
//        10  =>  '被推荐人降级', //TODO
//        11  =>  '直属代理升级为团队官方',
//        12  =>  '官方当月未补货 扣分',
    );//日志类型
    
    public $integral_class = array(
        1   =>  '充入',
        2   =>  '扣除',
    );//日志分类
    
    
    public $integral_rule_type = array(
        3   =>  '经销商签到',
//        4   =>  '高发展低',
        5   =>  '平推',
        6   =>  '低推荐高',
        7   =>  '下单数量（每盒/瓶）',
        8   =>  '下单金额',      
        9   =>  '直属代理升级',
//        10  =>  '被推荐人降级',    //TODO
        11  =>  '直属代理升级为团队官方',
//        12  =>  '官方当月未补货 扣分',
    );//积分规则类型，数字应与日志类型的行为是一致的（描述可以略有不同，但是进行的系统业务是一致）
    
    /**
     * 架构函数
     */
    public function __construct() {
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        
        if( $FUNCTION_MODULE['INTEGRAL_SHOP'] == '1' ){
            $this->integral_open = TRUE;
        }
        
        $extra = C('extra');
        if( isset($extra['integral']) ){
            $extra_info = $extra['integral'];
            
//            if( isset($extra_info['integral_status']) ){
//                $this->integral_status = $extra_info['integral_status'];
//            }
//            if( isset($extra_info['integral_class']) ){
//                $this->integral_class = $extra_info['integral_class'];
//            }
//            if( isset($extra_info['integral_rule_type']) ){
//                $this->integral_rule_type = $extra_info['integral_rule_type'];
//            }
        }
    }
    
    
    //==================================积分信息获取===========================
    
    /**
     * 获取某用户的积分信息
     * @param int $uid
     * @param bool $is_create   //是否创建该条用户积分记录
     */
    public function get_user_integral_info($uid,$is_create=FALSE){
        
        if( empty($uid) ){
            return FALSE;
        }
        
        
        $Integral_obj = M($this->Integral_obj);
        
        
        $condition = array(
            'uid'   =>  $uid,
        );
        
        $info = $Integral_obj->where($condition)->find();
        
        if( empty($info) && $is_create ){
            $data = array(
                'uid'   =>  $uid,
                'created'   =>  time(),
            );

            $add_res = $Integral_obj->data($data)->add();
            
            if( !$add_res ){
                return FALSE;
            }
            
            $info = $Integral_obj->where($condition)->find();
            
            if( empty($info) ){
                return FALSE;
            }
        }
        
        return $info;
        
    }//end func get_user_integral_info
    
    
    //获取积分信息表
    public function get_integral_info($page_info=array(),$condition=array()){
        $Integral_obj = M($this->Integral_obj);
        $distributor_obj = M('distributor');
        
        
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        
        $count = $Integral_obj->where($condition)->count();
        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $Integral_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $Integral_obj->where($condition)->order('id desc')->select();
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
            
            $dis_key_info[0]['name'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){
                
                $v_dis_uid = $v_dis['id'];
                
                $dis_key_info[$v_dis_uid] = $v_dis;
            }
            
            $levname = $this->get_score_level_name();
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_score = $v['score'];
                $v_created = $v['created'];
                
                $the_dis_info = $dis_key_info[$v_uid];
                $v_level = $the_dis_info['level'];
                
                if( $v_level == 1 ){
                    $score_level = $this->get_level_by_score($v_score);
                    $list[$k]['score_level'] = $score_level;
                    $list[$k]['score_level_name'] = $levname[$score_level];
                }
                else{
                    $list[$k]['score_level'] = 0;
                    $list[$k]['score_level_name'] = null;
                }
                
                
                $list[$k]['dis_info'] = $the_dis_info;
                $list[$k]['dis_name'] = $the_dis_info['name'];
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
            'limit' => $page_list_num,
        );
        
        return $return_result;
    }//end func get_integral_info
    
    
    
    
    //获取积分信息表
    public function get_integral_log($page_info=array(),$condition=array()){
        $Integral_log_obj = M($this->Integral_log_obj);
        $distributor_obj = M('distributor');
        
        $type_name = $this->integral_status;
        $class_name = $this->integral_class;
        
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        
        $count = $Integral_log_obj->where($condition)->count();
        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $Integral_log_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $Integral_log_obj->where($condition)->order('id desc')->select();
            }
            
            
            
            //-----整理添加相应其它表的信息-----
            $uids = array();
            
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_note = $v['note'];
                
                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( is_numeric($v_note) && !isset($uids[$v_note]) ){
                    $uids[$v_note] = $v_note;
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
                $v_type = $v['type'];
                $v_class = $v['class'];
                $v_note = $v['note'];
                $v_created = $v['created'];
                
                $v_note_format = $v_note;
                
                if( in_array($v_type, [4,5,6]) ){
                    $v_note_format = !isset($dis_key_info[$v_note])?$v_note:'被推荐人：'.$dis_key_info[$v_note]['name'];
                }
                elseif( $v_type == 11 ){
                    $v_note_format = !isset($dis_key_info[$v_note])?$v_note:'被推荐人：'.$dis_key_info[$v_note]['name'];
                }
                
                $list[$k]['type_name']  = $type_name[$v_type];
                $list[$k]['class_name']  = $class_name[$v_class];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['dis_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['created_format'] = date('Y-m-d H:i',$v_created);
                $list[$k]['note_format'] = $v_note_format;
                
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
            'limit' => $page_list_num,
        );
        
        return $return_result;
    }//end func get_integral_info
    
    
    
    //获取积分规则表
    public function get_integral_rule($page_info=array(),$condition=array()){
        $model_obj = M($this->Integral_rule_obj);
        $distributor_obj = M('distributor');
        
        $type_name = $this->integral_rule_type;
        
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        
        $count = $model_obj->where($condition)->count();
        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $model_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $model_obj->where($condition)->order('id desc')->select();
            }
            
            
            $level_name = C('LEVEL_NAME');
            $level_name[0] = '所有级别';
            
            //-----整理添加相应其它表的信息-----
//            $uids = array();
//            
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                
//                if( !isset($uids[$v_uid]) ){
//                    $uids[$v_uid] = $v_uid;
//                }
//            }
//            
//            array_values($uids);
//            
//            $condition_dis = array(
//                'id'    =>  array('in',$uids),
//            );
//            $dis_info = $distributor_obj->where($condition_dis)->select();
//            
//            $dis_key_info[0]['name'] = '总部';
//            foreach( $dis_info as $k_dis=>$v_dis ){
//                
//                $v_dis_uid = $v_dis['id'];
//                
//                $dis_key_info[$v_dis_uid] = $v_dis;
//            }
            
            
            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
                $v_type = $v['type'];
                $v_level = $v['level'];
                $v_created = $v['updated'];
                
                
                $list[$k]['type_name']  = $type_name[$v_type];
//                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
//                $list[$k]['dis_name'] = $dis_key_info[$v_uid]['name'];
                $list[$k]['levname'] = $level_name[$v_level];
                $list[$k]['updated_format'] = date('Y-m-d H:i',$v_created);
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
            'limit' => $page_list_num,
        );
        
        return $return_result;
    }//end func get_integral_rule
    
    
    //获取某个用户的积分信息
    public function get_user_score_level($uid){
        
        $condition = array(
            'uid'   =>  $uid
        );
        
        $info = $this->get_integral_info([],$condition);
        
        $list = $info['list'];
        
        $levname = $this->get_score_level_name();
        
        $new_list = [];
        foreach( $list as $k => $v ){
            $v_uid = $v['uid'];
            $v_score = $v['score'];
            
            $score_level = $this->get_level_by_score($v_score);
            
            $v['score_level'] = $score_level;
            $v['score_level_name'] = $levname[$score_level];
            
            
            $new_list = $v;
        }
        
        return $new_list;
    }//end func get_user_score_level
    
    
    //获取多个用户的积分信息
    public function get_users_score_level($uids){
        
        $condition = array(
            'uid'   =>  ['in',$uids]
        );
        
        $info = $this->get_integral_info([],$condition);
        
        $levname = $this->get_score_level_name();
        
        $list = $info['list'];
        
        $list_key = [];
        $new_list = [];
        
        foreach( $list as $k => $v ){
            $v_uid = $v['uid'];
            $v_score = $v['score'];
            $list_key[$v_uid] = $v;
        }
        
        foreach( $uids as $v_uid ){
            
            if( isset($list_key[$v_uid]) ){
                $v = $list_key[$v_uid];
            }
            else{
                $v['uid']   =   $v_uid;
                $v['score'] = 0;
            }
            
            $v_score = $v['score'];
            
            $score_level = $this->get_level_by_score($v_score);
            
            $v['score_level'] = $score_level;
            $v['score_level_name'] = $levname[$score_level];
            
            $new_list[$v_uid] = $v;
        }
        
        
        return $new_list;
    }//end func get_user_score_level
    
    
    //获取积分相应级别
    public function get_score_level(){
        
        $info = $this->get_integral_level();
        
        $score_level = [];
        
        foreach( $info as $k => $v ){
            $v_level = $v['level'];
            $v_name = $v['name'];
            $v_score = $v['score'];
            
            $score_level[$v_score] = $v_level;
        }
        
//        $score_level = array(
//            1       =>  1,
//            600     =>  2,
//            2000    =>  3,
//        );
        
        return $score_level;
    }//end func get_score_level
    
    //获取相应级别的名字
    public function get_score_level_name(){
        
        $info = $this->get_integral_level();
        
        $levname = [];
        
        foreach( $info as $k => $v ){
            $v_level = $v['level'];
            $v_name = $v['name'];
            $v_score = $v['score'];
            
            $levname[$v_level] = $v_name;
        }
        $levname[0] = '无';
        
//        $levname = array(
//            0       =>  '无',
//            1       =>  '执行官方',
//            2       =>  '团队官方',
//            3       =>  '核心官方',
//        );
        
        return $levname;
    }//end func get_score_level_name
    
    
    //获得积分级别信息
    public function get_integral_level(){
        
        $model_obj = M($this->Integral_level_obj);
        
        $info = $model_obj->field('id,level,name,score')->cache(true,300)->select();
        
        return $info;
    }//end func get_integral_level
    
    
    
    
    //根据积分得到积分级别
    public function get_level_by_score($num){
        
        $score_level = $this->get_score_level();
        
        $score_level_key = array_keys($score_level);
        
        
        $level_key = binarySearch($score_level_key,$num);
        
        $true_level_key = $score_level_key[$level_key];
        
        $level = isset($score_level[$true_level_key])?$score_level[$true_level_key]:'0';
        
        return $level;
    }//end func get_level_by_score
    
    
    
    
    
    //==================================end 积分信息获取===========================
    
    
    //==================================根据经销商的操作进行积分增减===========================
    
    
    
    
    //经销商签到
    public function admin_sign_in($uid){
        
        $info['day']    =   date('Ymd');
        
        $result = $this->execute_rule($uid,[],3,$info);
        
        return $result;
    }//end func admin_sign_in
    
    
    //经销商下单审核
    public function aduit_order($uid,$order_info){
        
        $info['order_info'] = $order_info;
        
        $result = $this->execute_rule($uid,[],7,$info);
        
        if( $result['code'] != 1 ){
            return $result;
        }
        
        $result = $this->execute_rule($uid,[],8,$info);
        
        return $result;
        
    }//end func aduit_order
    
    
    //经销商推荐的代理审核时
    public function admin_recommend_aduit($uid,$dis_info,$recommend_info=array()){
        
        if( empty($uid) || empty($dis_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            
            return $return_result;
        }
        
        
        $level = $dis_info['level'];
        $recommendID = $dis_info['recommendID'];
        
        if( empty($recommend_info) ){
            $condition_rec = array(
                'id'    =>  $recommendID,
            );
            
            $recommend_info = M('distributor')->where($condition_rec)->find();
        }
        
        if( empty($recommend_info) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '无推荐人信息！',
            );
            
            return $return_result;
        }
        
//        return [$dis_info,$recommend_info];
        
        
        $recommend_level = $recommend_info['level'];
        
        //高发展低
        if( $level > $recommend_level ){
            $type = 4;
        }
        //平推
        elseif( $level == $recommend_level ){
            $type = 5;
        }
        elseif( $level < $recommend_level ){//低推荐高
            $type = 6;
        }
        
        $info = [
            'recommend_info'    =>  $recommend_info,
        ];
//        return $type;
        $result = $this->execute_rule($uid,$dis_info,$type,$info);
        
        return $result;
    }//end func admin_recommend_aduit
    
    
    //经销商的推荐代理升级
    public function recommend_upgrade_up($dis_info,$upgrade_level){
        
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            
            return $return_result;
        }
        
        $recommendID = $dis_info['recommendID'];
        
        $info = [
            'recommend_info'  =>  $dis_info,
            'upgrade_level' =>  $upgrade_level,
        ];
        
        $result = $this->execute_rule($recommendID,[],9,$info);
        
        return $result;
        
        
    }//end func recommend_upgrade_up
    
    
    //积分充入发生变化时触发
    public function integral_recharge($uid,$score){
        
        $condition = ['uid' =>  $uid];
        
        $list = M($this->Integral_obj)->where($condition)->field('score')->find();
        
        $new_score = $list['score'];
        $old_score = bcsub($new_score,$score);
        
        $info['old_score'] = $old_score;
        $info['new_score'] = $new_score;
        
        $result = $this->execute_rule($uid,[],11,$info);
        
        return $result;
    }//end func integral_recharge
    
    
    
    //==================================end 根据经销商的操作进行积分增减===========================
    
    
    //==================================积分规则操作===========================
    
    
    public function add_rule($info){
        
        $id = $info['id'];
        $level = $info['level'];
        $rec_level = $info['rec_level'];
        $type = $info['type'];
        $score = $info['score'];
        
        if( $level == NULL || $score <= 0 || !is_numeric($score) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '不在类型范围内！',
            );
            
            return $return_result;
        }
        
        
        $model_obj = M($this->Integral_rule_obj);
        $all_type = $this->integral_rule_type;
        
        $all_type_ids = array_keys($all_type);
        
        
        if( !in_array($type, $all_type_ids) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '不在类型范围内！',
            );
            
            return $return_result;
        }
        
        //
        $rec_type_arr = array(4,5,6,9,10);
        if( in_array($type, $rec_type_arr) ){
            if( $rec_level == 0 ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  '被推荐人必须设定级别！',
                );

                return $return_result;
            }
            
            $level = $rec_level;
        }
        
        
        $save_info = array(
            'level' =>  $level,
            'score'  =>  $score,
            'type' =>  $type,
            'updated'   =>  time(),
        );
        
        
        if( empty($id) ){
            $condition_old = array(
                'type' =>  $type,
                'level' =>  $level,
            );
            $old_order_limit = $model_obj->where($condition_old)->find();
            
            if( !empty($old_order_limit) ){
                $return_result = array(
                    'code'  =>  4,
                    'msg'   =>  '该类型和级别已设置规则！',
                );
                
                return $return_result;
            }
            
            
            $save_result = $model_obj->add($save_info);
            $id = $save_result;
        }
        else{
            $condition = array(
                'id'    =>  $id,
            );
            $save_result = $model_obj->where($condition)->save($save_info);
        }
        
        if( !$save_result ){
            $return_result = array(
                'code'  =>  5,
                'msg'   =>  '编辑积分规则失败！',
            );

            return $return_result;
        }
        
        $return_result = array(
            'code'  =>  1,
            'msg'   =>  '编辑积分规则成功！',
            'id'    =>  $id,
        );

        return $return_result;
        
    }//end func add_rule
    
    
    //==================================end 积分规则操作===========================
    
    
    
    //==================================积分增减===========================
    
    
    /**
     * 根据规则执行积分增删
     * 
     * @param type $uid
     * @param type $dis_info
     * @param type $type
     * @param type $info
     * @return string|int
     */
    public function execute_rule($uid,$dis_info,$type,$info=array()){
        
        if( !$this->integral_open ){
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '不需要执行！',
            );
            
            return $return_result;
        }
        
        if( empty($uid) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            
            return $return_result;
        }
        
        
        
        $integral_rule_type = $this->integral_rule_type;
        
        
        $all_type = array_keys($integral_rule_type);
        
        if( !in_array($type, $all_type) ){
            $return_result = array(
                'code'  =>  3,
                'msg'   =>  '非可用的类型！',
            );
            
            return $return_result;
        }
        
        if( empty($dis_info) ){
            $distributor_obj = M('distributor');
            $condition_dis = ['id'  => $uid ];
            $dis_info = $distributor_obj->where($condition_dis)->find();
        }
        if( empty($dis_info) ){
            $return_result = array(
                'code'  =>  4,
                'msg'   =>  '没有找到该用户信息！',
            );
            
            return $return_result;
        }
        
        
        
        $Integral_obj = M($this->Integral_obj);
        $Integral_rule_obj = M($this->Integral_rule_obj);
        $Integral_log_obj = M($this->Integral_log_obj);
        
        
        $level = $dis_info['level'];
        $is_recharge = FALSE;//执行是否充值的判断
        $recharge_score = 0;
        $msg = '未知错误';
        $recharge_uid = $uid;//一般规则都是触发人是充入积分者
        
        $recharge_info = [];
        
        //签到
        if( $type == 3 ){
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  ['in',[$level,0]],
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->order('level asc')->find();

            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  5,
                    'msg'   =>  '没有定义规则，无法获得积分！',
                );

                return $return_result;
            }

            $score = $rule_info['score'];
            
            if( !isset($info['day']) || empty($info['day']) || !is_numeric($info['day']) || strlen($info['day']) != 8 ){
                $return_result = array(
                    'code'  =>  2,
                    'msg'   =>  '时间参数错误！',
                );

                return $return_result;
            }
            
            $day = $info['day'];
            
            $day_time = strtotime($day);
            
            $day_time_end = $day_time+24*60*60-1;
            
            $condition = array(
                'uid'       =>  $uid,
                'created'   =>  array(
                    array('EGT',$day_time),array('ELT',$day_time_end),'and'
                ),
                'type'  =>  3,
            );
            $log_info = $Integral_log_obj->field('uid')->where($condition)->find();
            
            //已有该时间的签到
            if( !empty($log_info) ){
                $return_result = array(
                    'code'  =>  6,
                    'msg'   =>  '已有该时间的签到！',
                    'info'  =>  $Integral_log_obj->getLastSql(),
                );

                return $return_result;
            }
            $is_recharge = TRUE;
            
            $recharge_score = $score;
        }
        //高发展低、平推、低推荐高
        elseif( $type == 4 || $type == 5 || $type == 6 ){
            
            if( empty($info['recommend_info']) ){
                $return_result = array(
                    'code'  =>  7,
                    'msg'   =>  '推荐人信息获取错误！',
                );

                return $return_result;
            }
            
            $recommend_info = $info['recommend_info'];
            
            $recommend_id = $recommend_info['id'];
            $recommend_level = $recommend_info['level'];
            
            $recharge_uid = $recommend_id;
            
            $condition_rule = array(
                'level' =>  $level,
                'type'  =>  $type,
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->find();
            
            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  8,
                    'msg'   =>  '找不到积分规则！',
                );

                return $return_result;
            }
            
            $score = $rule_info['score'];
            $recharge_info['note'] = $uid;
            
            
            //增加检测，该用户已经增加过此推荐人用户升级积分的，不能再增加
            $condition = [
                'uid'   =>  $recommend_id,
                'type'  =>  $type,
                'note'  =>  $uid,
            ];
            $log_info = $Integral_log_obj->where($condition)->find();
//return $condition;
            if( !empty($log_info) ){
                $return_result = array(
                    'code'  =>  17,
                    'msg'   =>  '该积分用户已经增加过！',
                );

                return $return_result;
            }
            
            
            //高发展低
            if(  $type == 4 ){
                $is_recharge = TRUE;
                $recharge_score = $score;
            }
            elseif( $type == 5 ){//平推
                $is_recharge = TRUE;
                $recharge_score = $score;
            }
            elseif( $type == 6 ){//低推荐高
                $is_recharge = TRUE;
                $recharge_score = $score;
            }
            else{
                $return_result = array(
                    'code'  =>  9,
                    'msg'   =>  '不符合条件的积分触发！',
                );

                return $return_result;
            }
            
            
//            if( $recommend_level == 1 ){
//                $user_score_level = $this->get_user_score_level($recommend_id);
//                
//                if( !empty($user_score_level) && $user_score_level['level'] == 3 ){
//                    
//                }
//            }
            
            
        }
        elseif( $type == 7 ){//下单数量
            
            if( empty($info['order_info']) ){
                $return_result = array(
                    'code'  =>  10,
                    'msg'   =>  '无订单信息！',
                );

                return $return_result;
            }
            
            $order_info = $info['order_info'];
            
            $total_num = $order_info['0']['total_num'];
            
            if( $total_num <= 0 ){
                $return_result = array(
                    'code'  =>  11,
                    'msg'   =>  '错误的订单信息！',
                );

                return $return_result;
            }
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  ['in',[$level,0]],
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->order('level asc')->find();

            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  12,
                    'msg'   =>  '没有定义规则，无法获得积分！',
                );

                return $return_result;
            }
            
            $score = $rule_info['score'];
            
            $is_recharge = TRUE;
            $recharge_score = $score*$total_num;
        }
        elseif( $type == 8 ){//下单金额
            if( empty($info['order_info']) ){
                $return_result = array(
                    'code'  =>  10,
                    'msg'   =>  '无订单信息！',
                );

                return $return_result;
            }
            
            $order_info = $info['order_info'];
            
            $total_price = $order_info['0']['total_price'];
            
            if( $total_price <= 0 ){
                $return_result = array(
                    'code'  =>  11,
                    'msg'   =>  '错误的订单信息！',
                );

                return $return_result;
            }
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  ['in',[$level,0]],
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->order('level asc')->find();

            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  12,
                    'msg'   =>  '没有定义规则，无法获得积分！',
                );

                return $return_result;
            }
            
            $score = $rule_info['score'];
            
            $is_recharge = TRUE;
            $recharge_score = $score*$total_price;
        }
        elseif( $type == 9 ){//被推荐人升级
            
            if( empty($info['recommend_info']) || empty($info['upgrade_level']) ){
                $return_result = array(
                    'code'  =>  13,
                    'msg'   =>  '无升级信息！',
                );
                
                return $return_result;
            }
            
            $recommend_info = $info['recommend_info'];
            $upgrade_level = $info['upgrade_level'];
            
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  $upgrade_level,
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->find();
            
            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  14,
                    'msg'   =>  '无规则信息！',
                );
                
                return $return_result;
            }
            
            
            $score = $rule_info['score'];
            
            $is_recharge = TRUE;
            $recharge_score = $score;
        }
        elseif( $type == 10 ){//推荐人降级
            $return_result = array(
                'code'  =>  19,
                'msg'   =>  '暂未开放',
            );

            return $return_result;
        }
        elseif( $type == 11 ){//乐家帮特殊要求---升级到团队官方（当官方B升级为“团队官方”时，推荐人A为（团队官方or 核心官方）时，A +500分（若A为执行官方不加分））
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  0,
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->find();

            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  17,
                    'msg'   =>  '该规则还未限定',
                );

                return $return_result;
            }
            
            $score = $rule_info['score'];
            
            $old_score = $info['old_score'];
            $new_score = $info['new_score'];
            
            $old_score_level = $this->get_level_by_score($old_score);
            $new_score_level = $this->get_level_by_score($new_score);
            
            //如果升级为团队官方
            if( $old_score_level == 1 && $new_score_level == 2 ){
                $recommendID = $dis_info['recommendID'];
                $distributor_obj = M('distributor');
                
                $condition_dis = array(
                    'id'    =>  $recommendID
                );
                
                $recommend_info = $distributor_obj->field('level')->where($condition_dis)->find();
                
                $recommend_level = $recommend_info['level'];
                
                if( $recommend_level != 1 ){
                    $return_result = array(
                        'code'  =>  15,
                        'msg'   =>  '未触发规则！',
                    );
                    
                    return $return_result;
                }
                
                $list = $this->get_user_score_level($recommendID);
                
                $score_level = $list['score_level'];
                $recharge_info['note'] = $uid;
                $recharge_uid = $recommendID;
                
                //（当官方B升级为“团队官方”时，推荐人A为（团队官方or 核心官方）时，A +500分（若A为执行官方不加分））
                if( $score_level == 1 ){
                    $return_result = array(
                        'code'  =>  16,
                        'msg'   =>  '为执行官方不加分！',
                    );
                    
                    return $return_result;
                }
                
                $Integral_log_obj = M($this->Integral_log_obj);
                
                //增加检测，该用户已经增加过此推荐人用户升级积分的，不能再增加
                $condition = [
                    'uid'   =>  $recommendID,
                    'type'  =>  $type,
                    'note'  =>  $uid,
                ];
                $log_info = $Integral_log_obj->where($condition)->find();
                
                if( !empty($log_info) ){
                    $return_result = array(
                        'code'  =>  17,
                        'msg'   =>  '该积分用户已经增加过！',
                    );
                    
                    return $return_result;
                }
                
                $is_recharge = TRUE;
                $recharge_score = $score;
            }
            
            
        }
        elseif( $type == 12 ){//官方当月未补货 扣分
            
            $condition_rule = array(
                'type'  =>  $type,
                'level' =>  0,
            );

            $rule_info = $Integral_rule_obj->where($condition_rule)->find();
            
            if( empty($rule_info) ){
                $return_result = array(
                    'code'  =>  14,
                    'msg'   =>  '无规则信息！',
                );
                
                return $return_result;
            }
            
            
            $score = $rule_info['score'];
            
            $is_recharge = TRUE;
            $recharge_score = $score;
            
        }
        else{
            $return_result = array(
                'code'  =>  21,
                'msg'   =>  '未定义的积分规则！',
            );

            return $return_result;
        }
        
        
        if( $is_recharge ){
            $recharge_result = $this->recharge($recharge_uid,$recharge_score,$type,$recharge_info);
            
            return $recharge_result;
        }
        else{
            $return_result = array(
                'code'  =>  20,
                'msg'   =>  $msg,
            );

            return $return_result;
        }
    }//end func execute_rule
    
    
    
    //充入积分
    public function recharge($uid,$score,$type,$recharge_info=array()){
        
        if( !$this->integral_open ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '积分系统未开启！',
            );
            return $return_restult;
        }
        
        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );
        
        if( empty($uid) || $score <1 || !is_numeric($score) ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '提交的参数错误！',
            );
            return $return_restult;
        }
        
        $all_integral_status = $this->integral_status;
        
        if( !is_numeric($type) || !isset($all_integral_status[$type]) ){
            $return_restult = array(
                'code'  =>  3,
                'msg'   =>  '提交的参数错误！',
            );
            return $return_restult;
        }
        
        
        $Integral_obj = M($this->Integral_obj);
        $Integral_log_obj = M($this->Integral_log_obj);
        
        
        //-----------积分日志-----------------
        
        $note = isset($recharge_info['note'])?$recharge_info['note']:'';
        
        
        
        $log = array(
            'uid'   =>  $uid,
            'score'     =>  $score,//积分
            'type'  =>  $type,//类型
            'class' =>  1,
            'note'  =>  $note,//备注
            'created'   =>  time(),
        );
        
        $recharge_log_result = $Integral_log_obj->data($log)->add();
        
        if( !$recharge_log_result ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '积分记录失败，请重试！',
                'error_info'    =>  $log,
            );
            return $return_restult;
        }
        //-----------end 积分日志-----------------
        
        
        
        //------------用户积分信息操作------------
        $info = $this->get_user_integral_info($uid,TRUE);
        
        if( empty($info) ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '无法获取用户积分信息，请重试！',
            );
            return $return_restult;
        }
        
        
        $condition = array(
            'uid'   =>  $uid,
        );
        
        $save_res = $Integral_obj->where($condition)->setInc('score',$score);
        
        if( !$save_res ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '更新用户积分信息失败，请重新充入！',
            );
            return $return_restult;
        }
        
        $this->integral_recharge($uid, $score);
        
        $Integral_obj->where($condition)->setInc('his_score',$score);
        
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '充入成功！',
        );
        return $return_restult;
        
        
        //------------end 用户积分信息操作------------
        
        
        
    }//end func recharge
    
    
    
    
    
    //扣除积分
    public function charge($uid,$score,$type,$charge_info=array()){
        //判断该系统是否使用积分系统
        if( !$this->integral_open ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '积分系统未开启！',
            );
            return $return_restult;
        }
        
        
        $return_restult = array(
            'code'  =>  0,
            'msg'   =>  '',
        );
        
        if( empty($uid) || $score<=0 ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数提交错误！',
            );
            return $return_restult;
        }
        
        $Integral_obj = M($this->Integral_obj);
        $Integral_log_obj = M($this->Integral_log_obj);
        
        
        
        
        
        
        //----------用户积分信息判断--------------
        $info = $this->get_user_integral_info($uid);
        
        if( empty($info) ){
            $return_restult = array(
                'code'  =>  5,
                'msg'   =>  '无法获取用户积分信息，请重试！',
            );
            return $return_restult;
        }
        
        $old_score = $info['score'];
        
        $bccomp_res = bccomp($score,$old_score,0);
        if( $bccomp_res == 1 ){
            $return_restult = array(
                'code'  =>  6,
                'msg'   =>  '扣除的积分大于实际可扣除的积分，无法进行下一步操作！',
//                'error_info'    =>  $bccomp_res,
            );
            return $return_restult;
        }
        //----------用户积分信息判断--------------
        
        
        //-----------积分日志-----------------
        
        $note = isset($charge_info['note'])?$charge_info['note']:'';
        
        $log = array(
            'uid'   =>  $uid,
            'score'     =>  $score,//充入积分
            'type'  =>  $type,//充入类型
            'class' =>  2,
            'note'  =>  $note,
            'created'   =>  time(),
        );
        
        $recharge_log_result = $Integral_log_obj->data($log)->add();
        
        if( !$recharge_log_result ){
            $return_restult = array(
                'code'  =>  4,
                'msg'   =>  '积分记录失败，请重试！',
                'error_info'    =>  $log,
            );
            return $return_restult;
        }
        //-----------end 积分日志-----------------
        
        
        //----------用户积分信息操作--------------
        $condition = array(
            'uid'   =>  $uid,
        );
        
        $save_res = $Integral_obj->where($condition)->setDec('score',$score);
        
        if( !$save_res ){
            $return_restult = array(
                'code'  =>  7,
                'msg'   =>  '更新用户积分信息失败，请重新充入！',
            );
            return $return_restult;
        }
        //----------end 用户积分信息操作--------------
        
        
        
        
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '扣费成功！',
        );
        return $return_restult;
    }//end func charge
    
    
    /**
     * 检查用户是否有足够积分扣除
     * @param int $uid
     * @param decimal $integral
     * @param bool $check_order 是否计算未审核订单
     * @return boolean
     */
    public function check_enough_integral($uid,$integral,$check_order=FALSE){
        
        //判断该系统是否使用积分系统
        if( !$this->integral_open ){
            $return_restult = array(
                'code'  =>  1,
                'msg'   =>  '无需扣费！',
            );
            return $return_restult;
        }
        
        
        if( empty($uid) || $integral<=0 ){
            return FALSE;
        }
        
        $model = M($this->Integral_obj);
//        $distributor_obj = M('distributor');
        
        
        $condition = array(
            'uid'   =>  $uid,
        );
        $info = $model->where($condition)->find();
        
        //该用户没有资金信息则没有充值过
        if( empty($info) ){
            return FALSE;
        }
        
        $score = $info['score'];//当前可用金额
        
        
        //减去未审核的订单才是真实可用金额
        if( $check_order ){
            $order_obj = M('integralorder');
            
            $condition_order = array(
                'user_id'   =>  $uid,
                'status'    =>  1,
            );
            
            $order_info = $order_obj->where($condition_order)->group('order_num')->select();
            
            $order_total = 0;
            
            if( !empty($order_info) ){
                foreach( $order_info as $k => $v ){
                    $v_total = $v['total_integral'];
                    
                    $order_total = bcadd($order_total, $v_total,2);
                }
            }
            
            $recharge_score = bcsub($score,$order_total,2);

            if( $recharge_score < 0 ){
                $recharge_score = 0;
            }
        }
        
        //如果金额不足扣费
        $contrast_res = bccomp($recharge_score,$integral,2);//高精度比较
        //扣费金额比当前充值金额大，则不能进行扣费
        if(  $contrast_res == -1  ){
            return FALSE;
        }
        
        
        return TRUE;
    }
    
    
    
    
    
    
    //删除用户积分信息
    public function delete($uid){
        
        if( empty($uid) ){
            $return_restult = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_restult;
        }
        
        $Integral_obj = M($this->Integral_obj);
        $Integral_log_obj = M($this->Integral_log_obj);
        
        $condition = [
            'uid'   =>  $uid,
        ];
        
        $delete_result = $Integral_obj->where($condition)->delete();
        
        if( !$delete_result ){
            $return_restult = array(
                'code'  =>  3,
                'msg'   =>  '删除失败！',
            );
            return $return_restult;
        }
        
        $Integral_log_obj->where($condition)->delete();
        
        
        $return_restult = array(
            'code'  =>  1,
            'msg'   =>  '删除成功！',
        );
        return $return_restult;
    }//end func delete
    
    
    
    
    
    //===============================end 积分增减==============================
    
    
    
}