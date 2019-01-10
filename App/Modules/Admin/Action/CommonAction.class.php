<?php

header("Content-type:text/html;charset=utf-8");

/**
 * 	经销商权限控制
 */
class CommonAction extends Action {

    public function _initialize() {
        // $the_module_name = strtolower(MODULE_NAME);
        // $the_action_name = strtolower(ACTION_NAME);
        
        
        // if( $the_module_name == 'index' &&  empty($_SESSION['oid']) ){
        //     //$this->redirect(C('YM_DOMAIN').__GROUP__.'/Login/index');
        // }
        // if( empty($_SESSION['oid'])) {
        //     $cur_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //     $redirect_url = base64_encode($cur_url);
        //     $return_url = C('YM_DOMAIN').__GROUP__.'/index/index?redirect_url='.$redirect_url;
        //     checkAuth('index','','',$return_url);
        // }
        
        // import('Lib.Action.Newredis', 'App');
        // $this->Newredis = new Newredis();
        
        // $key = 'get_admin_Common_manager';
        // $key_suffix = $_SESSION['oid'];
        
        // $redis = $this->Newredis->get($key.$key_suffix);
        
        // if( empty($redis) ){
        //     $condition = [
        //         'openid' => $_SESSION['oid'],
        //         'disable'   =>  0,
        //     ];

        //     $manager = M('distributor')->where($condition)->find();
            
        //     $manager['start_time'] = $manager['time'];
        //     $manager['end_time'] = $manager['time']+3600*24*365;
            
        //     $redis = serialize($manager);
        //     $expire_time = 360;
        //     $this->Newredis->set($key, $redis,$expire_time,$key_suffix);
        // }
        // else{
        //     $manager = unserialize($redis);
        // }
        
        

        
        // //必须要审核后才能进系统，或者禁用状态的不能直接登录
        // if( empty($manager) || $manager['audited'] != 1 || $manager['disable'] == 1 ){
        //     $this->redirect(C('YM_DOMAIN').__GROUP__.'/Login/index');
        // }
        
        // $this->uid = $manager['id'];
        
        // session('managerid', $this->uid);
        // $this->manager = $manager;
        // $this->user_count   = $this->get_user_count();
        // $this->level_name = C('LEVEL_NAME');
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
     
}

?>