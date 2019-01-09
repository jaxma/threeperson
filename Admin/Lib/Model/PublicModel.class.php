<?php

class PublicModel extends Model {
	
	/**
      +----------------------------------------------------------
     * 验证管理员登录信息
      +----------------------------------------------------------
     */
	public function auth($datas){
		$datas = $_POST;
		if ($_SESSION['verify'] != md5(strtoupper($_POST['verify']))) {
            die(json_encode(array('status' => 0, 'info' => '验证码错误啦，再输入吧！')));
        }
		$M = M("admin_user");
		if ( $M->where("`user_name`='" . $datas['username'] . "'")->count()>=1) {
			$info = $M->where("`user_name`='" . $datas["username"] . "'")->find();		
			if ($info['password'] == md5($datas['password']) && $info['user_name'] == $datas['username']) {
				// 登录成功
				$action_list = M('role')->where('role_id='.$info['role_id'])->getField('action_list');
				self::set_admin_session($info['user_id'], $info['user_name'], $action_list, $info['last_login']);
				// 更新最后登录时间和IP
				$data['last_login']=time();
				$data['last_ip']=real_ip();
				$M->where(array('user_id='.$_SESSION['admin_id']))->save($data);
				if (isset($_POST['remember']))
				{
					$time = time() + 3600 * 24 * 365;
					setcookie('Hunuo[admin_id]',   $info['user_id'],        $time);
					setcookie('Hunuo[admin_pass]',  md5($info['password']), $time);
				}
                return array('status' => 1, 'info' => "登录成功！");
            } else {
                return array('status' => 0, 'info' => "账号或密码错误！");
            }
		} else {
            return array('status' => 0, 'info' => "账号或密码错误！");
        }
	}

	/**
      +----------------------------------------------------------
     * 设置管理员的session内容
      +----------------------------------------------------------
     */
	private function set_admin_session($user_id, $username, $action_list, $last_time,$info)
	{
		session('admin_id',   $user_id);
		session('admin_name', $username);
		session('action_list',$action_list);
		session('last_check', $last_time);// 用于保存最后一次检查订单的时间
	}
}

?>
