<?php
//营销管理模块
header("Content-Type: text/html; charset=utf-8");


class Sale {
    
    //营销用户
    public $sale_user_model = 'sale_user';
    //营销基本设置
    public $sale_base_model = 'sale_base';
    //中奖记录
    public $sale_record_model = 'sale_record';
    //轮盘抽奖设置
    public $sale_lunpan_set_model = 'sale_lunpan';
    //销售订单
    public $sale_order_model = 'sale_order';
    //随机码
    public $sale_code_model = 'sale_code';
    //营销模块
    public $sale_base_types = ['lunpan'];
    //营销模块命名
    public $sale_types_name = [
        'lunpan'    =>  '轮盘抽奖',
    ];
    //中奖记录状态
    public $sale_record_status = [
        0   =>  '未领取',
        1   =>  '已领取',
    ];
    //订单记录状态
    public $sale_order_status = [
        0   =>  '未付款',
        1   =>  '待发货',
        2   =>  '已发货',
//        3   =>  '已收货',
    ];
    
    /**
     * 架构函数
     */
    public function __construct() {
        
    }
    
    
    
    //==================start 获取记录=================
    
    //获取营销用户
    public function get_sale_user($page_info=array(),$condition=array()){
        /**
         * 营销用户model结构
         * id
         * name     nickname
         * sex      sex
         * area     country,province,city
         * headimgurl   headimgurl
         * subscribe_time   subscribe_time 用户关注时间
         * subscribe_source 关注来源
         * lunpan_lottery_num   //轮盘抽奖次数
         * 
         */
        $model_obj = M($this->sale_user_model);
        
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
    }//end func get_sale_user
    
    
    //获取中奖记录
    public function get_sale_record($page_info=array(),$condition=array(),$other=array()){
        $model_obj = M($this->sale_record_model);
        $sale_user_obj = M($this->sale_user_model);
        
        $sale_base_types_name = $this->sale_types_name;
        $sale_record_status = $this->sale_record_status;
        
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        
        $get_lunpan_win = isset($other['get_lunpan_win'])?$other['get_lunpan_win']:0;
        
        $count = $model_obj->where($condition)->count();
        if( $count > 0 ){
            
            if( !empty($page_info) ){
                
                $page_con = $page_num.','.$page_list_num;
                
                $list = $model_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $model_obj->where($condition)->order('id desc')->select();
            }
            
            $sale_ids = [];
            
            foreach( $list as $k => $v ){
                $v_sale_id = $v['sale_id'];
                if( !isset($sale_ids[$v_sale_id]) ){
                    $sale_ids[$v_sale_id] = $v_sale_id;
                }
                
            }
            
            array_values($sale_ids);
            
            $condition_sale_user = [
                'id'=>['in',$sale_ids],
            ];
            $sale_users = $sale_user_obj->where($condition_sale_user)->select();
            
            $sale_key_users = [];
            foreach( $sale_users as $v ){
                $v_id = $v['id'];
                $sale_key_users[$v_id] = $v;
            }
            
            
            if( $get_lunpan_win ){
                foreach( $list as $k => $v ){
                    $v_sale_id = $v['sale_id'];
                    $v_type = $v['type'];
                    $v_status = $v['status'];

                    $list[$k]['status_name'] = $sale_record_status[$v_status];
                    $list[$k]['type_name'] = $sale_base_types_name[$v_type];
                    $list[$k]['sale_user'] = $sale_key_users[$v_sale_id];
                }
            }
            else{
                foreach( $list as $k => $v ){
                    $v_sale_id = $v['sale_id'];
                    $v_type = $v['type'];
                    $v_status = $v['status'];

                    $list[$k]['status_name'] = $sale_record_status[$v_status];
                    $list[$k]['type_name'] = $sale_base_types_name[$v_type];
                    $list[$k]['sale_user'] = $sale_key_users[$v_sale_id];
                }
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
    }//end func get_sale_record
    
    
    //获取中奖订单
    public function get_sale_order($page_info=array(),$condition=array()){
        $model_obj = M($this->sale_order_model);
        $sale_user_obj = M($this->sale_user_model);
        
        $sale_base_types_name = $this->sale_types_name;
        $sale_order_status = $this->sale_order_status;
        
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
                
                $list = $model_obj->where($condition)->order('time desc')->page($page_con)->select();
            }
            else{
                $list = $model_obj->where($condition)->order('time desc')->select();
            }
            
            
            $sale_ids = [];
            foreach( $list as $k => $v ){
                $v_sale_id = $v['sale_id'];
                if( !isset($sale_ids[$v_sale_id]) ){
                    $sale_ids[$v_sale_id] = $v_sale_id;
                }
            }
            array_values($sale_ids);
            
            $condition_sale_user = [
                'in' => ['id',$sale_ids],
            ];
            $sale_users = $sale_user_obj->where($condition_sale_user)->select();
            
            
            $sale_key_users = [];
            foreach( $sale_users as $v ){
                $v_id = $v['id'];
                $sale_key_users[$v_id] = $v;
            }
            
            foreach( $list as $k => $v ){
                $v_sale_id = $v['sale_id'];
                $v_type = $v['type'];
                $v_status = $v['status'];
                $v_s_phone = $v['s_phone'];
                $v_time = $v['time'];
                
                $list[$k]['s_phone_encrypt']    = substr($v_s_phone, 0,3).'*****'.substr($v_s_phone, -3,3);
                $list[$k]['status_name'] = $sale_order_status[$v_status];
                $list[$k]['type_name'] = $sale_base_types_name[$v_type];
                $list[$k]['sale_user'] = $sale_key_users[$v_sale_id];
                $list[$k]['sale_name'] = $sale_key_users[$v_sale_id]['name'];
                $list[$k]['timeformat']   =   date('Y-m-d H:i:s');
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
    }//end func get_sale_order
    
    
    
    //获取营销基本设置
    public function get_sale_base($page_info=array(),$condition=array()){
        /**
         * 营销基础模块model结构
         * id
         * name 设置名字（英文）
         * cnname   设置中文名字，用于注释
         * desc 内容
         * type 所属类型
         * 
         */
        $model_obj = M($this->sale_base_model);
        $sale_user_obj = M($this->sale_user_model);
        
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
            
            $new_list = [];
            foreach( $list as $k => $v ){
                $v_name = $v['name'];
                
                $new_list[$v_name] = $v;
            }
            
            $list = $new_list;
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
    }//end func get_sale_base
    
    
    
    
    
    //获取轮盘抽奖设置
    public function get_lunpan_set($page_info=array(),$condition=array()){
        $model_obj = M($this->sale_lunpan_set_model);
        $distributor_obj = M('distributor');
        
        
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
                
                $list = $model_obj->where($condition)->order('id asc')->page($page_con)->select();
            }
            else{
                $list = $model_obj->where($condition)->order('id asc')->select();
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
    }//end func get_lunpan_set
    
    
    //==================end 获取记录===================
    
    
    
    //=================start 公用===========================
    
    //查询轮盘随机码
    public function check_code($code,$type='',$sail_id){
        
        if( empty($code) || !is_numeric($code) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            return $return_result;
        }
        
        $sale_code_obj = M($this->sale_code_model);
        $sale_record_obj = M($this->sale_record_model);
        
        
        $condition = [
            'code'  =>  $code,
        ];
        $code_res = $sale_code_obj->where($condition)->find();
        
        if( empty($code_res) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '查无此标签，请注意！',
            ];
            return $return_result;
        }
        
        $condition = [
            'sale_code'  =>  $code,
        ];
        $record_res = $sale_record_obj->where($condition)->find();
        
        if( !empty($record_res) && $record_res['status'] == 1 ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '此商品二维码已被扫码，无法再次扫码！',
            ];
            return $return_result;
        }
        elseif( !empty($record_res) && $record_res['status'] == 0 ){
            if( !empty($sail_id) && $sail_id != $record_res['sale_id'] ){
                $return_result = [
                    'code'  =>  5,
                    'msg'   =>  '此二维码已被抽奖，系统检测非抽奖用户登录，无法领奖！',
                ];
                
                return $return_result;
            }
            else{
                $return_result = [
                    'code'  =>  1,
                    'msg'   =>  '此二维码有效并未领奖！',
                    'record_id' =>  $record_res['id'],
                    'record_info'   =>  '恭喜你此二维码中了'.$record_res['salename'].'，请尽快领取您的奖品！',
                ];

                return $return_result;
            }
        }
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '此二维码有效并未被扫码！',
        ];
        
        return $return_result;
    }//end func check_code
    
    
    //设置销售基础设置
    public function set_sale_base($name,$cnname,$desc,$type){
        
        if( empty($name) || empty($desc) || empty($type) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            return $return_result;
        }
        
        if( !in_array($type, $this->sale_base_types) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '不在类型范围内！',
            ];
            return $return_result;
        }
        
        if( empty($cnname) ){
            $cnname = $name;
        }
        
        
        $desc = stripslashes($desc);
        $desc = preg_replace("/&amp;/", "&", $desc);
        $desc = preg_replace("/&quot;/", "\"", $desc);
        $desc = preg_replace("/&lt;/", "<", $desc);
        $desc = preg_replace("/&gt;/", ">", $desc);
        
        $model = M($this->sale_base_model);
        
        
        $condition = [
            'name'  =>  $name,
            'type'  =>  $type
        ];
        
        $old_info = $model->where($condition)->find();
        
        $save_info = [
            'name'  =>  $name,
            'cnname'    =>  $cnname,
            'desc'  =>  $desc,
            'type'  =>  $type,
            'updated'   =>  time(),
        ];
        
        if( !empty($old_info) ){
            $res = $model->where($condition)->save($save_info);
        }
        else{
            $save_info['created'] = time();
            $res = $model->where($condition)->add($save_info);
        }
        
        if( !$res ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '设置失败，请重试！',
            ];
            return $return_result;
        }
        else{
            $return_result = [
                'code'  =>  1,
                'msg'   =>  '设置成功！',
            ];
            return $return_result;
        }
        
    }//end func set_sale_base
    
    
    
    //设置用户
    /**
    * 营销用户model结构
    * id
    * name     nickname
    * sex      sex
    * area     country,province,city
    * headimgurl   headimgurl
    * subscribe_time   subscribe_time 用户关注时间
    * subscribe_source 关注来源
    * lunpan_lottery_num   //轮盘抽奖次数
    * 
    */
    public function set_user($openid,$wechat_info,$type){
        
        if( empty($openid) || empty($wechat_info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有获得用户信息',
            ];
            return $return_result;
        }
        
        if( empty($type) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误',
            ];
            return $return_result;
        }
        
        if( empty($wechat_info['nickname']) || empty($wechat_info['headimgurl']) ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '可能是未关注，请重试！',
            ];
            return $return_result;
        }
        
        
        $model_obj = M($this->sale_user_model);
        
        $condition = [
            'openid'    =>  $openid,
        ];
        $old_info = $model_obj->where($condition)->field('openid,headimgurl,name')->find();
        
        if( !empty($old_info)){
            $save_info = [];
            if( !empty($wechat_info['nickname']) && $wechat_info['nickname'] != $old_info['name'] ){
                $save_info['name'] = $wechat_info['nickname'];
            }
            if( !empty($wechat_info['headimgurl']) && $wechat_info['headimgurl'] != $old_info['headimgurl'] ){
                $save_info['headimgurl'] = $wechat_info['headimgurl'];
            }
            
            if( !empty($save_info) ){
                $save_result = $model_obj->where($condition)->save($save_info);

                if( !$save_result ){
                    $return_result = [
                        'code'  =>  3,
                        'msg'   =>  '修改用户失败，请重试！',
                        'info'  =>  $save_info,
                        'old_info'  =>  $old_info,
//                        'condition' =>  $condition,
                    ];
                    return $return_result;
                }
            }
        }
        else{
            $add_info = [
                'name'  =>  $wechat_info['nickname'],
                'openid'    =>  $openid,
                'sex'   =>  $wechat_info['sex'],
                'area'  =>  $wechat_info['country'].$wechat_info['province'].$wechat_info['city'],
                'subscribe_time'    =>  $wechat_info['subscribe_time'],
                'headimgurl'    =>  $wechat_info['headimgurl'],
                'subscribe_source'  =>  $type,
                'lunpan_lottery_num'    =>  0,
                'created'   =>  time(),
            ];
            $add_result = $model_obj->add($add_info);

            if( !$add_result ){
                $return_result = [
                    'code'  =>  4,
                    'msg'   =>  '添加用户失败，请重试！',
                ];
                return $return_result;
            }
        }
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '用户登录信息成功！',
        ];
        return $return_result;
    }//end func set_user
    
    
    //中奖记录
    public function set_record($info,$type,$code=''){
        
        
        if( empty($info) || empty($type)  ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误',
            ];
            return $return_result;
        }
        
        if( !in_array($type, $this->sale_base_types) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '不在类型范围内！',
            ];
            return $return_result;
        }
        
        
        $model_obj = M($this->sale_record_model);
        $sale_user_obj = M($this->sale_user_model);
        
        $condition['openid'] = $info['openid'];
        
        $user_info = $sale_user_obj->where($condition)->find();
        
        if( empty($user_info) ){
            setLog('无记录用户进行抽奖:info:'.print_r($info,1),'sale_lunpan');
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '没有用户信息，请确认已经关注并使用微信登录！',
                'user_info' =>  $sale_user_obj->getLastSql(),
            ];
            return $return_result;
        }
        
        
        if( !empty($code) ){
            $condition_code = [
                'sale_code'  =>  $code,
            ];
            $record_info = $model_obj->where($condition_code)->find();
            
            if( !empty($record_info) ){
                $return_result = [
                    'code'  =>  7,
                    'msg'   =>  '此二维码已经被扫码领取了，无法再次扫码！',
                ];
                return $return_result;
            }
        }
        
        
        
        $add_info = [
            'sale_id'   =>  $info['sale_id'],
            'salename'     =>  $info['salename'],//产品名字
            'p_id'          =>  $info['p_id'],
            'sale_code'      =>  $code,
            'type'      =>  $type,
            'created'   =>  time(),//中奖时间
        ];
        
        $add_res = $model_obj->add($add_info);
        
        if( !$add_res ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '添加用户抽奖记录失败，请重试！',
                'error'    =>  $model_obj->getLastSql(),
            ];
            return $return_result;
        }
        
        
//        $res = $sale_user_obj->where($condition)->setDec('lunpan_lottery_num',1);
//        
//        if( !$res ){
//            $return_result = [
//                'code'  =>  6,
//                'msg'   =>  '用户抽奖次数扣减失败，请重试！',
//            ];
//            return $return_result;
//        }
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '记录成功！',
            'record_id' =>  $add_res,
        ];
        return $return_result;
    }//end func set_record
    
    
    //设置营销基础信息
    public function set_sale_bas($name,$type,$cnname='',$desc=''){
        
        if( empty($name) || empty($type)  ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误',
            ];
            return $return_result;
        }
        
        if( !in_array($type, $this->sale_base_types) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '不在类型范围内！',
            ];
            return $return_result;
        }
        
        
        $model_obj = M($this->sale_lunpan_base);
        
        
        $condition = [
            'name'  =>  $name,
            'type'  =>  $type,
        ];
        $old_info = $model_obj->where($condition)->find();
        
        if( !empty($old_info) ){
            if( $old_info['desc'] == $desc && $old_info['cnname'] == $cnname ){
                $return_result = [
                    'code'  =>  4,
                    'msg'   =>  '没有做出改动！',
                ];
                return $return_result;
            }
            
            $new_info = [
                'cnname'    =>  $cnname,
                'desc'      =>  $desc,
                'updated'   =>  time(),
            ];
            $res = $model_obj->where($condition)->save($new_info);
            
            if( !$res ){
                $return_result = [
                    'code'  =>  5,
                    'msg'   =>  '设置失败，请重试！',
                ];
                return $return_result;
            }
            
        }
        else{
            $new_info = [
                'name'      =>  $name,
                'type'      =>  $type,
                'cnname'    =>  $cnname,
                'desc'      =>  $desc,
                'updated'   =>  time(),
            ];
            $res = $model_obj->add($new_info);
            
            if( !$res ){
                $return_result = [
                    'code'  =>  5,
                    'msg'   =>  '设置失败，请重试！',
                ];
                return $return_result;
            }
        }
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '设置成功！',
        ];
        return $return_result;
        
    }//end func set_sale_bas
    
    //设置轮盘
    public function set_lunpan($id,$new_info){
        
//        if( empty($id) ){
//            $return_result = [
//                'code'  =>  2,
//                'msg'   =>  '参数错误！',
//            ];
//            return $return_result;
//        }
        
        if( empty($new_info['name']) || $new_info['percent'] == NULL || !is_numeric($new_info['percent']) || $new_info['percent'] < 0 || $new_info['total_num'] == null || !is_numeric($new_info['total_num']) || $new_info['total_num'] < 0 ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '提交的信息不完整！',
            ];
            return $return_result;
        }
        
        if( isset($new_info['img']) && empty($new_info['img']) ){
            unset($new_info['img']);
        }
        
        if( !is_numeric($new_info['money']) || $new_info['money'] == 0 ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '金额不能设置为0并且必须要数字！',
            ];
            return $return_result;
        }
        
        $sale_lunpan_set_obj = M($this->sale_lunpan_set_model);
        
        
        $condition = [
            'id'    =>  $id,
        ];
        $old_info = [];
        if( !empty($id) ){
            $old_info = $sale_lunpan_set_obj->where($condition)->order('id asc')->select();
        }
        
        
        
        if( !empty($old_info) ){
            
            if( $new_info['name'] == $old_info['name'] && $new_info['percent'] == $old_info['percent'] && $new_info['total_num'] == $old_info['total_num'] ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '没有做出改动！',
                ];
                return $return_result;
            }
            
            $new_info['updated']  = time();
            $res = $sale_lunpan_set_obj->where($condition)->save($new_info);
        }
        else{
            $new_info['updated']  = time();
            $res = $sale_lunpan_set_obj->where($condition)->add($new_info);
        }
        
        
        if( !$res ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '修改错误，请重试！',
            ];
            return $return_result;
        }
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '修改成功！',
        ];
        return $return_result;
    }//end func set_lunpan
    
    //删除轮盘
    public function del_lunpan($id){
        if( empty($id) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            ];
            return $return_result;
        }
        
        $sale_lunpan_set_obj = M($this->sale_lunpan_set_model);
        
        
        $condition = [
            'id'    =>  $id,
        ];
        
        $res = $sale_lunpan_set_obj->where($condition)->delete();
        
        if( !$res ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '修改错误，请重试！',
            ];
            return $return_result;
        }
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '修改成功！',
        ];
        return $return_result;
    }//end func del_lunpan
    
    
    
    /**
     * 写入订单
     * 
     * @param type $sale_id     用户ID
     * @param type $write_info  订单信息
     * @return array
     */
    public function write_order($sale_id,$write_info){
        
//        $p_id = $write_info['p_id'];        //产品id
        $record_id = $write_info['record_id'];   
//        $p_name = $write_info['p_name'];    //产品名字
        $p_type = $write_info['p_type'];    //产品属性
        $s_name = $write_info['s_name'];    
        $s_addre = $write_info['s_addre'];
        $s_phone = $write_info['s_phone'];
        $notes = $write_info['notes'];
        
        
        $sale_user_obj = M($this->sale_user_model);
        $order_obj = M($this->sale_order_model);
        $sale_record_obj = M($this->sale_record_model);
        $sale_lunpan_obj = M($this->sale_lunpan_set_model);
        
        
        if( empty($sale_id) ){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '查不到登录状态，可能已过期！',
                'sale_id'   =>  $sale_id,
            );
            
            return $return_result;
        }
        
        //参数判断
        if( empty($s_name) || empty($s_addre) || empty($s_phone) ){
            $return_result = array(
                'code'  =>  -1,
                'msg'   =>  '请确认您是否已选择商品，并填写完整的收货信息！',
                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }
        
        
        if( !in_array($p_type, $this->sale_base_types) ){
            $return_result = array(
                'code'  =>  -2,
                'msg'   =>  '非可用的下单类型！',
//                'error_info'    =>  $write_info,
            );
            
            return $return_result;
        }
        
        if( empty($record_id) ){
            $return_result = array(
                'code'  =>  -7,
                'msg'   =>  '找不到中奖记录，无法下单！',
            );
            
            return $return_result;
        }
        
        
        
        
        
        /**
         * TODO:优化$order_num生成，在后端生成，并确保为唯一值
         */
//        $order_num_len = strlen($order_num);
//        $order_num_sub_len = $order_num_len-2;
//        $order_num = substr($order_num,2,$order_num_sub_len);
        $order_num = rand(0,99).time();
        
        //经销商信息
        $where['id'] = $sale_id;
        $users = $sale_user_obj->where($where)->find();
        
        
        if( empty($sale_id) || empty($users) ){
            $return_result = array(
                'code'  =>  -3,
                'msg'   =>  '没有找到用户信息！'
            );
            
            return $return_result;
        }
        
        $condition_record = [
            'id'        =>  $record_id,
            'sale_id'   =>  $sale_id,
            'status'    =>  0,
            'type'      =>  $p_type,
        ];
        
        $record_info = $sale_record_obj->where($condition_record)->find();
        
        if( empty($record_info) ){
            $return_result = array(
                'code'  =>  -4,
                'msg'   =>  '已领取奖品或没有中奖，无法填写订单！'
            );
            
            return $return_result;
        }
        
        $record_code = $record_info['sale_code'];
        $p_id = $record_info['p_id'];
        $p_name = $record_info['salename'];
        
        
        $condition_lunpan = [
            'id'    =>  $p_id,
        ];
        $lunpan_info = $sale_lunpan_obj->where($condition_lunpan)->find();
        
        if( empty($lunpan_info) ){
            $return_result = array(
                'code'  =>  -8,
                'msg'   =>  '已领取奖品或没有中奖，无法填写订单！'
            );
            
            return $return_result;
        }
        
        $pay_money = $lunpan_info['money'];
        
        
        
        $condition_record_edit = [
            'id'    =>  $record_id,
        ];
        $record_save = [
            'status'    =>  1,
        ];
        $record_save_res = $sale_record_obj->where($condition_record_edit)->save($record_save);
            
        if( !$record_save_res ){
            $return_result = array(
                'code'  =>  -5,
                'msg'   =>  '更改领奖记录失败，请重试！'
            );
            
            return $return_result;
        }
        
        
        //----------生成订单------------
        
        $status = 0;//订单状态，默认1为已付款
            
        $arr = array(
            'order_num' => $order_num,  //订单号
            'sale_id' => $sale_id,        //下单用户
            'p_name' => $p_name,        //产品名字
            'p_type'    =>  $p_type,    //产品类型
            'pay_money' =>  $pay_money, //支付金额
            'status' => $status,        //订单状态
            's_name' => $s_name,        //收货人名字
            's_addre' => $s_addre,      //收货人地址
            's_phone' => $s_phone,      //收货人手机
            'notes' => $notes,          //订单备注
            'sale_code' =>  $record_code,//二维码号
            'time' => time(),           //订单生成日期
        );


        $addorder = $order_obj->add($arr);
        
        //----------end 生成订单------------
        
        
        if( !$addorder ){
            $return_result = array(
                'code'  =>  -6,
                'msg'   =>  '创建订单失败！',
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
    
    
    //后台审核订单
    public function radmin_audit($order_nums){
        
        if( empty($order_nums) || !is_array($order_nums) ){
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  '参数错误！',
            );
            return $return_result;
        }
        
        
        $order_obj = M($this->sale_order_model);
        
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
            

            $order_save_info = array(
                'status'    =>  2,
//                'paytime'    =>  time(),
            );
            
            $order_obj->where(array('order_num' => $order_num))->save($order_save_info);//6为已审核未配送状态
            
            
            
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
    
    
    //=================end 公用===========================
    
    
    //=================start 轮盘===========================
    
    
    //轮盘抽奖
    public function lottery($sale_openid,$code=''){
        
        if( empty($sale_openid) ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '没有抽奖用户信息，可能已经过期，请重新进入页面！',
            ];
            return $return_result;
        }
        
        if( empty($code) ){
            $return_result = [
                'code'  =>  7,
                'msg'   =>  '无扫码信息，无法抽奖！',
            ];
            return $return_result;
        }
        
        
        $model = M($this->sale_lunpan_set_model);
        
        $condition = [];
        
        /**
         * 奖品设置
         * id
         * name         //名称
         * today_num    //今天送出数量
         * total_num    //总数量
         * send_num     //送出数量
         * percent      //概率
         * updated      //修改时间
         */
        $info = $model->where($condition)->select();
        
        
        
        $win_id = 0;//抽奖奖品ID
        $win_info = [];
        $key_info = [];
        
        if( empty($info) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '没有奖品信息！',
            ];
            return $return_result;
        }
        
        $prize_arr = [];
        $can_winids = [];
        foreach ( $info as $k => $v ){
            $v_id = $v['id'];
//                $v_today_num = $v['today_num'];
            $v_total_num = $v['total_num'];
            $v_send_num = $v['send_num'];
            $v_percent = $v['percent'];
            $v_percent = bcmul($v_percent,100,2);
            

            $remain_num = bcsub($v_total_num,$v_send_num);

            if( $remain_num > 0 && $v_percent > 0 ){
                $prize_arr[$v_id] = $v_percent;
                $can_winids[] = $v_id;
            }
            
            $key_info[$v_id] = $v;
        }
        
        if( empty($prize_arr) ){
            $return_result = [
                'code'  =>  6,
                'msg'   =>  '本轮抽奖已经结束，奖品池暂时为空，谢谢您的参与！',
            ];
            return $return_result;
        }
        

        $win_id = get_rand($prize_arr);
        $win_info = $key_info[$win_id];
        
        
        if( $win_id == 0 || empty($win_info) ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '抽奖失败，请重试！',
            ];
            return $return_result;
        }
        
        //非可用
        if( !in_array($win_id,$can_winids) ){
            $return_result = [
                'code'  =>  7,
                'msg'   =>  '本轮抽奖已经结束，奖品池暂时为空，谢谢您的参与。',
            ];
            return $return_result;
        }

        
        $sale_user_obj = M($this->sale_user_model);
        
        $condition_user = [
            'openid'    =>  $sale_openid,
        ];
        $user_info = $sale_user_obj->where($condition_user)->find();
        
        if( empty($user_info) ){
            $return_result = [
                'code'  =>  5,
                'msg'   =>  '找不到用户信息！',
            ];
            return $return_result;
        }
        
        //添加中奖记录
        $record_info['sale_id'] = $user_info['id'];
        $record_info['p_id']    =   $win_id;
        $record_info['salename'] = $win_info['name'];
        $record_info['openid'] = $sale_openid;
        $set_record_result = $this->set_record($record_info,'lunpan',$code);
        
        if( $set_record_result['code'] != 1 ){
            return $set_record_result;
        }
        
        $record_id = $set_record_result['record_id'];
        
        $condition_save=[
            'id'    =>  $win_id,
        ];
        $set_res = $model->where($condition_save)->setInc('send_num',1);
            
        
        if( !$set_res ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '抽奖出错，请重试！',
            ];
            return $return_result;
        }
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '抽奖成功！',
            'win_id'    =>  $win_id,
            'record_id' =>  $record_id,
        ];
        return $return_result;
    }//end func lottery
    
    
    
    //=================end 轮盘===========================
    
    
}