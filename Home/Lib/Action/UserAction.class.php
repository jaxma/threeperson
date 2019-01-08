<?php
class UserAction extends CommonAction {
	public function __construct() {
		parent::__construct();
        
        /*****start   网站标题 关键字 描述   start*********/
        $this->site_title = 'Login & Register' . '_' .$this->site_info['title'];
        $this->site_keywords = $this->site_info['keyword'];
        $this->site_description = $this->site_info['description'];
        /*****end    网站标题 关键字 描述    end**********/

        $this->ur_here = '<a href="/">Home</a><span>></span><a href="'.U('User/index').'">会员中心</a>';
	}

    public function login(){
        $userInfo = session('userInfo');
        if($userInfo) $this->redirect('Info/cService', array('cat_id'=>19));

        $this->display('loginRegister');
    }

    public function do_login(){
        $email          =  $this->_post('email', 'trim', '');
        $password       =  $this->_post('password', 'md5','');
        $remember       =  $this->_post('remember', '', 'no');
        $back_url       =  $this->_post('back_url', '', '/');
        if(strpos($back_url, 'download') || strpos($back_url, 'uploadSurvey')) $back_url = U('Info/cService',array('cat_id'=>19));

        if(!is_email($email)) $this->error('请输入正确的邮箱！');

        $userInfo = M('users')->where(array("email"=>$email))->find();
        if(!$userInfo) $this->error('此会员不存在！');
        if($userInfo['password'] != $password) $this->error('密码不正确！');

        /*if($_SESSION['verify'] != md5(strtoupper($_POST['verify']))) {
           $this->error('验证码错误！');
        }*/

        //记录最后登录时间和ip地址
        $data['last_login_time']    = time();
        $data['last_login_ip']      = get_ip();

        $affected_rows = M('users')->where(array('user_id'=>$userInfo['user_id']))->save($data);

        if($affected_rows>0){
            //10天内免登录
            if($remember == 'yes') cookie('user_id', $userInfo['user_id'], 10*24*3600);

            //更新个人信息
            $this->update_userInfo($userInfo['user_id']);

            $this->success('登录成功，正在跳转...',$back_url);
        }else{
            $this->error('网络错误，请稍候再试！');
        }
    }

    /**
     * 检查用户名是否已经注册
     */
    public function is_registered($username){
        $user_id = M('users')->where("user_name='$username'")->getField('user_id');
        if($user_id) return true;
        return false;
    }

    /**
     * 检查手机是否已经注册
     */
    public function phone_registered($phone){
        $phone = trim($phone);
        $user_id = M('users')->where("phone='$phone'")->getField('user_id');
        if($user_id) return true;
        return false;
    }

    /**
     * 检查手机是否已经注册
     */
    public function email_registered($email){
        $email = trim($email);
    	$user_id = M('users')->where("email='$email'")->getField('user_id');
    	if($user_id) return true;
        return false;
    }

    //注册
    public function register(){
        $userInfo = session('userInfo');
        if($userInfo) $this->redirect('Info/cService', array('cat_id'=>19));

        $this->display('loginRegister');
    }

    //处理注册
    public function do_reg(){
        $data       = M('users')->create();
        if(empty($data['user_name']))                   $this->error('请填写用户名！');
        if($this->is_registered($data['user_name']))    $this->error('用户名已在存在！');

        if(empty($data['phone']))                       $this->error('请填写联系电话！');
        if(!is_phone($data['phone']) && !is_tel($data['phone'])) $this->error('联系电话格式错误！');
        if($this->phone_registered($data['phone']))     $this->error('此电话已经被注册了！');

        if(empty($data['email']))                       $this->error('请填写E-mail！');
        if(!is_email($data['email']))                   $this->error('E-mail格式不正确！');
        if($this->email_registered($data['email']))     $this->error('此E-mail已经被注册了！');

        if(empty($data['password']))                    $this->error('请填写密码！');
        if($_POST['agree']!='yes')                      $this->error('请同意《用户协议》！');

        $data['password']           = md5(trim($data['password']));
        $data['reg_time']           = time();
        $data['last_login_time']    = time();
        $data['last_login_ip']      = get_ip();

        $back_url                   =  $this->_post('back_url', '', '/');
        if(strpos($back_url, 'download') || strpos($back_url, 'uploadSurvey')) $back_url = U('Info/cService',array('cat_id'=>19));

        $user_id                    = M('users')->add($data);
        if($user_id>0){
            $this->update_userInfo($user_id);
            $this->success('成功注册，正在跳转...', $back_url);
        }else{
            $this->error('网络错误，请稍候再试！');
        }
    }

    /**
     * 找回密码
     */
    public function send_code(){
        $email=$_POST['email'];

        $code = rand(10000,99999);
        cookie('code', $code, 1800);//保存半个小时
        cookie('email', $email, 1800);//保存半个小时
        
        $url     = 'http://' . $_SERVER['HTTP_HOST'] . U('User/check_code',array('code'=>md5($code), 'email'=>base64_encode($email)));
        $usinfo  = M('users')->where(array('email'=>$email))->find();
        $name = $userInfo['user_name'];
        
$content = <<<EOT
尊敬的用户 $name：

   您好！ 欢迎加入【翼通商务】！<br/>

   请点击下面的链接进行重置密码：<br/>

   $url<br/>

   如果以上链接无法点击，请将上面的地址复制到您的浏览器（如IE）的地址栏进
EOT;

        if($usinfo){
            $res = send_mail($email, '翼通商务', '找回密码', $content);
            if($res==true){
                $this->success('验证邮箱已发送，请注意查收！');
            }else{
                $this->error('网络错误，请稍候再试！');
            }
        }else{
            $this->error('你还没有注册,请先注册！');
        }
    }

    //验证找回密码的链接
    public function check_code(){
        $code=$_GET['code'];
        $email=$_GET['email'];

        if(md5(cookie('code'))!=$code){
            $this->error('验证链接已过期，请重新验证！！', U('User/get_password'));
        }else{
            $this->success('验证成功，请进行密码修改！！', U('User/change_password',array('code'=>$code, 'email'=>$email)));
        }
    }

    public function change_password($code, $email){
        if(md5(cookie('code'))!=$code) exit('非法访问！');

        $this->display();
    }

    public function do_change_password(){
        $email=base64_decode($_POST['email']);
        
        if(empty($email)){
            $this->error('你的邮件不合法');
        }
            
        $info=M('users')->where(array('email'=>$email))->find();
        
        $new_password=$_POST['new_password'];
        $cfm_password=$_POST['cfm_password'];

      
        if(empty($new_password)){
            $this->error('新密码不能为空');
        }
        
        if($new_password != $cfm_password){
            $this->error('两次密码不一致！');
        }
    
        $data['password']   = md5($new_password);
        $ar = M('users')->where(array('user_id'=>$info['user_id']))->save($data);
        if($ar){
            $this->success('修改密码成功，请重新登录！',U('User/logout'));
        }else{
            $this->error('网络错误，请稍候再试！');
        }
    }

    //安全退出
    public function logout(){
        session('[destroy]');
        cookie('user_id', null);

        $this->redirect('User/login');
    }
}