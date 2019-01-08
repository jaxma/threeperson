<?php

class PublicAction extends Action {
    /**
      +----------------------------------------------------------
     * 初始化
      +----------------------------------------------------------
     */
    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
		define('IN_ECS', true);
    }

    /**
      +----------------------------------------------------------
     * 验证token信息
      +----------------------------------------------------------
     */
    private function checkToken() {
        if (!M("Admin")->autoCheckToken($_POST)) {
            die(json_encode(array('status' => 0, 'info' => '令牌验证失败')));
        }
        unset($_POST[C("TOKEN_NAME")]);
    }
	
	/**
      +----------------------------------------------------------
     * 登录页面
      +----------------------------------------------------------
     */
    public function index() {
		if (isset($_COOKIE[$this->loginMarked])) {
			$this->redirect("Index/index");
		}

		$systemConfig = include WEB_ROOT . 'Common/systemConfig.php';
		$this->assign("site", $systemConfig);
		$this->display("Common:login");
    }
	
	/**
      +----------------------------------------------------------
     * 验证登录信息
      +----------------------------------------------------------
     */
	public function checkLogin(){
		if(IS_POST){
			$returnLoginInfo = D("Public")->auth();
			if ($returnLoginInfo['status'] == 1) {
				// $this->redirect("Index/index");
				// exit();
				// $this->success('登录成功！',U("Index/index"));
			}
			echo json_encode($returnLoginInfo);
			
		}	
	}
	
	/**
      +----------------------------------------------------------
     * 退出登录
      +----------------------------------------------------------
     */
    public function loginOut() {
        /* 清除cookie */
		setcookie('ECSCP[admin_id]',   '', 1);
		setcookie('ECSCP[admin_pass]', '', 1);
        unset($_SESSION);
        session_destroy();
        $this->redirect("Index:index");
    }
}