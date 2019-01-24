<?php

header("Content-type:text/html;charset=utf-8");

/**
 * 	经销商权限控制
 */
class CommonAction extends Action {

    public function _initialize() {
        $this->company_model = M('company');
        $this->photo_model = M('photo');
        $this->cat_model = M('cat');
        $this->item_model = M('item');
        $this->news_model = M('news');
        $this->recruitment_model = M('recruitment');
        $this->lang = I('lang')?I('lang'):0;

        $this->title = C('SYSTEM_NAME');

        //首页介绍
        $this->introduct = $this->company_model->where('id = 3 and status = 1')->find();
        //首页图片
        $index_photo  = $this->photo_model->where('type = 1')->find();
        //首页移动端图片
        $mobile_photo = $index_photo['many_image'];
        $mobile_photo = explode(',', $mobile_photo);

        //分类
        $cats = $this->cat_model->where('status = 1 and pid  = 0')->select();

        foreach ($cats as $k => $v) {
            $second_cats = $this->cat_model->where('status = 1 and pid = '.$v['id'])->order('sequence desc')->select();
            if($v['id'] == 1){
                $classical = $this->item_model->where('isopen = 1 and classical = 1')->order('sequence desc')->limit(12)->select();
                foreach ($classical as $kk => $vv) {
                    if(!empty($vv['detail'])){
                       $detail = $this->detail_arr($vv['detail']);
                    }
                    if(!empty($vv['detail_en'])){
                       $detail_en = $this->detail_arr($vv['detail_en']);
                    }
                    $classical[$kk]['position'] = $detail[0];
                    $classical[$kk]['position_en'] = $detail_en[0];
                }
                $cats[$k]['classical'] = $classical;
            }
            foreach ($second_cats as $kk => $vv) {
                if($vv['pid'] == 1){
                    //项目 
                    $model = $this->item_model;
                }elseif($vv['pid'] == 2){
                    //事务所
                    $model = $this->news_model;
                }elseif($vv['pid'] == 3){
                    //招聘
                    $model = $this->recruitment_model;
                }
                $second_cats_item = $model->where('isopen = 1 and cat2 = '.$vv['id'])->order('sequence desc')->select();
                foreach ($second_cats_item as $kkk => $vvv) {
                    if(!empty($vvv['detail'])){
                       $detail = $this->detail_arr($vvv['detail']);
                    }
                    if(!empty($vvv['detail_en'])){
                       $detail_en = $this->detail_arr($vvv['detail_en']);
                    }
                    $second_cats_item[$kkk]['position'] = $detail[0];
                    $second_cats_item[$kkk]['position_en'] = $detail_en[0];
                    $second_cats_item[$kkk]['publish_time'] = date('Y-m-d',$vvv['publish_time']);
                }
                $second_cats[$kk]['items'] = $second_cats_item;
            }
            $cats[$k]['cat'] = $second_cats;
        }
        
        //公司信息 
        $this->position =  $lang?C('TE_POSITION'):C('T_POSITION');
        $this->address =  $lang?C('TE_ADDRESS'):C('T_ADDRESS');
        $this->tel =  $lang?C('TE_TEL'):C('T_TEL');
        $this->email =  $lang?C('TE_EMAIL'):C('T_EMAIL');
        $this->en_position =  $lang?C('TE_EN_POSITION'):C('T_EN_POSITION');
        $this->en_address =  $lang?C('TE_EN_ADDRESS'):C('T_EN_ADDRESS');
        $this->en_tel =  $lang?C('TE_EN_TEL'):C('T_EN_TEL');
        $this->en_email =  $lang?C('TE_EN_EMAIL'):C('T_EN_EMAIL');

        $this->cats = $cats;

        //项目
        $this->items = $this->item_model->where('isopen = 1')->order('sequence desc')->select();

        $this->index_photo = $index_photo;
        $this->mobile_photo = $mobile_photo;
        $this->index_url = C('YM_DOMAIN');
    }

    public function search(){
        $this->display();
    }

    //获取当前登录经销商信息
    public function get_manager() {
        $distributor_obj = M('distributor');

        $uid = $this->uid;

        $condition_man = array(
            'id' => $uid,
        );

        $manager = $distributor_obj->where($condition_man)->find();


        return $manager;
    }//end func get_manager
    
    
    
    //获取用户统计信息
    public function get_user_count(){
        
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        $uid = $this->uid;
        
        $count_module_name = array(
            'index',
        );
        //'funds','rebate','order'
        
        $count_action_name = array(
            'index',''
        );
        
        //限定几个页面进行统计，其它页面不进行额外计算
        if( !in_array($the_module_name, $count_module_name) && !in_array($the_action_name, $count_action_name) ){
            return False;
        }
        
        $key = 'get_user_count';
        $key_suffix = $uid.'-'.$the_module_name.'-'.$the_action_name;
        
        $redis = $this->Newredis->get($key.$key_suffix);
        
        if( empty($redis) ){
            $user_count = array(
                'team_num'  =>  0,          //团队人数
                'my_total_money'    =>  0,  //我的业绩
                'team_total_money'  =>  0,  //团队业绩
                'status2'           =>  0,  //待审核
                'status3'           =>  0,  //已发货
                'recharge_money'    =>  0,  //虚拟币
            );

            import('Lib.Action.User','App');
            $User = new User();


            $user_team_info = $User->get_user_team_count($uid);


            if( $user_team_info['code'] == 1 ){
                $user_count['team_num']         =   $user_team_info['result']['team_num'];
                $user_count['my_total_money']   =   $user_team_info['result']['my_order_info']['total_money'];
                $user_count['team_total_money'] =   $user_team_info['result']['team_order_info']['total_money'];
                $user_count['status2']          =   isset($user_team_info['result']['my_order_status']['status2'])?$user_team_info['result']['my_order_status']['status2']:0;
                $user_count['status3']          =   isset($user_team_info['result']['my_order_status']['status3'])?$user_team_info['result']['my_order_status']['status3']:0;
            }
            
            $redis = serialize($user_count);
            $expire_time = 300;
            $this->Newredis->set($key,$redis,$expire_time,$key_suffix);
        }
        else{
            $redis = unserialize($user_count);
        }
        
        //查看该经销商的资金表
        $money_funds = M('money_funds')->where(array('uid'=>$uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];
        $user_count['recharge_money'] = $recharge_money;//虚拟币金额
        
        
        return $user_count;
    }//end func get_user_count
    
    
    public function base_url($array){
        $base_str = base64_encode($array);
        return $base_str;
    }
    
    public function get_base_url($base_str){
        $base_url = base64_decode($base_str);
        return $base_url;
    }
    
    
    
    //获取微信接口的access_token
    public function get_access_token_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        import("Wechat.Wechat", APP_PATH);
        $this->wechat_obj = new Wechat($options);
        
        
        $access_token = $this->wechat_obj->get_access_token();
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'access_token'  =>  $access_token,
        ];
        
        echo $this->ajaxReturn($return_result);
    }//end func get_access_token
    
    
    //获取微信基本信息
    public function get_wechat_info_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
//            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
//            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        
        $signPackage = get_jsapi_ticket();
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'options'  =>  $options,
            'signPackage'   =>  $signPackage,
        ];
        
        echo $this->ajaxReturn($return_result);
    }//end func get_wechat_info_ajax
     

    public function detail_arr($detail){
        if(!empty($detail)){
            $detail = str_replace("；",";",$detail);
            $detail = explode(";",$detail);
        }
        return $detail;
    }
}

?>