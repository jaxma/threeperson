<?php

/**
 * 	topos经销商管理系统登录
 */
class LoginAction extends Action {

    //登录页面
    public function index() {
        
        $webset = M('website_set');
        $login_html = $webset->where(array('name'=>'radmin_login'))->field('value')->find();
        
        if(empty($login_html)){
          $this->display();
        }else{
          $this->display($login_html['value']);
        }
        
    }

    //验证码图像生成函数
    public function verify() {
        import('ORG.Util.Image');
        //读取配置文件中关于验证码图像的参数配置
        //$length = C('VERIFY_LENGTH');
        $length = 4;
        $mode = C('VERIFY_MODE');
        $width = C('VERIFY_WIDTH');
        $height = C('VERIFY_HEIGHT');
        //生成验证码图像
        Image::buildImageVerify($length, $mode, 'png', $width, $height);
    }
    

    //登录表单处理
    public function login() {
        if (!IS_AJAX) {
            // halt("页面不存在");
        }
        
        $username = $this->_post('username');
        $password = $this->_post('password');
        $code = $this->_post('code');

        //验证码错误
//      if ( md5($code) != session('verify')) {
//          $this->error('验证码错误');
//      }
        $admin = M('admin')->where(array('username' => $username))->find();
        
        $count = M('admin')->count('id');
        //自动创建管理员逻辑
        if( $count == 0 && $username == 'toposxitong' ){
            
            import('Lib.Action.Admin','App');
            $Admin = new Admin();
            $admin_auth = $Admin->admin_auth;
            $admin_auth_key = array_keys($admin_auth);
            $admin_auth_str = implode(',', $admin_auth_key);
            
            $new_info = [
                [
                    'id'        =>  1,
                    'username'  =>  'toposxitong',
                    'password'  =>  '7cbb3252ba6b7e9c422fac5334d22054',
                    'auth'      => '',
                ],
                [
                    'id'        =>  2,
                    'username'  =>  'xitong',
                    'password'  =>  '05dc4be3550a5f2ec6bdb5e3a2fc5059',
                    'auth'      => '',
                ],
                [
                    'id'        =>  3,
                    'username'  =>  'admin',
                    'password'  =>  '05dc4be3550a5f2ec6bdb5e3a2fc5059',
                    'auth'      =>  $admin_auth_str,
                ],
            ];
            $res = M('admin')->addAll($new_info);
            
            if( $res ){
                $admin = M('admin')->where(array('username' => $username))->find();
            }
            else{
                $this->error('无法首次登录添加后台管理员！');
            }
        }
        

        //$this->ajaxReturn($admin);return;
        //用户名或密码错误
        if (!$admin || $admin['password'] != md5($password)) {
            $this->error('用户名或密码错误');
//            $this->ajaxReturn(array('status' => 2), 'json');
        } else {   //登录成功
            //session记录
            session('aid', $admin['id']);
            session('aname', $admin['username']);
            session('did', $admin['did']);
            session('auth', explode(',',$admin['auth']));
//            //开启SESSION时间
//            session('session_start_time', time());
//
            import('Lib.Action.Admin','App');
            $Admin = new Admin();
            
            $Admin->add_active_log($admin['id'],'登录后台，IP：'.get_ip());
            
            $this->success('登录成功！', __APP__.'/radmin/index/index/#' . __APP__.'/radmin/analysis/index?spm=m-0-0');
        }
    }
    
        //登录表单处理
    public function login_code() {
        if (!IS_AJAX) {
            halt("页面不存在");
        }
        
        $username = $this->_post('username');
        $password = $this->_post('password');
        $code = $this->_post('code');

        $IS_TEST = C('IS_TEST');
        
        //验证码错误
        if ( md5($code) != session('verify') && !$IS_TEST ) {
            //$this->error('验证码错误');
            $return_result=[
                'code'=>'-1',
                'msg' => '验证码错误',
            ];
            $this->ajaxReturn($return_result);
        }
        
        $admin = M('admin')->where(array('username' => $username))->find();
        
        $count = M('admin')->count('id');
        
        //自动创建管理员逻辑
        if( $count == 0 && $username == 'toposxitong' ){
            
            import('Lib.Action.Admin','App');
            $Admin = new Admin();
            $admin_auth = $Admin->admin_auth;
            $admin_auth_key = array_keys($admin_auth);
            $admin_auth_str = implode(',', $admin_auth_key);
            
            $new_info = [
                [
                    'id'        =>  1,
                    'username'  =>  'toposxitong',
                    'password'  =>  '7cbb3252ba6b7e9c422fac5334d22054',
                    'auth'      => '',
                ],
                [
                    'id'        =>  2,
                    'username'  =>  'xitong',
                    'password'  =>  '05dc4be3550a5f2ec6bdb5e3a2fc5059',
                    'auth'      => '',
                ],
                [
                    'id'        =>  3,
                    'username'  =>  'admin',
                    'password'  =>  '05dc4be3550a5f2ec6bdb5e3a2fc5059',
                    'auth'      =>  $admin_auth_str,
                ],
            ];
            $res = M('admin')->addAll($new_info);
            
            if( $res ){
                $admin = M('admin')->where(array('username' => $username))->find();
            }
            else{
                $return_result=[
                    'code'=>'-2',
                    'msg' => '无法首次登录添加后台管理员',
                ];
                $this->ajaxReturn($return_result);
//                $this->error('无法首次登录添加后台管理员！');
            }
            
            //初始化一些数据
            import('Lib.Action.Admin', 'App');
            (new Admin())->init_data();
        }
        

        //$this->ajaxReturn($admin);return;
        //用户名或密码错误
        if (!$admin || $admin['password'] != md5($password)) {
            $return_result=[
                'code'=>'-3',
                'msg' => '用户名或密码错误',
            ];
            $this->ajaxReturn($return_result);
//            $this->error('用户名或密码错误');
//            $this->ajaxReturn(array('status' => 2), 'json');
        } else {   //登录成功
            //session记录
            session('aid', $admin['id']);
            session('aname', $admin['username']);
            session('did', $admin['did']);
            session('auth', explode(',',$admin['auth']));
//            //开启SESSION时间
//            session('session_start_time', time());
//
            import('Lib.Action.Admin','App');
            $Admin = new Admin();
            
            $Admin->add_active_log($admin['id'],'登录后台，IP：'.get_ip());
            $return_result=[
                'code'=>'1',
                'msg' => '登录成功',
            ];
            $this->ajaxReturn($return_result);
//            $this->success('登录成功！', __APP__.'/radmin/index/index/#' . __APP__.'/radmin/analysis/index?spm=m-0-0');
        }
    }
    
    
    

}

?>