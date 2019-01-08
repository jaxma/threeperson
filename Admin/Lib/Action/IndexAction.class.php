<?php

class IndexAction extends CommonAction {
	/**
      +----------------------------------------------------------
     * 后台首页
      +----------------------------------------------------------
     */
    public function index() {
		parent::logined();
		$this->display();
    }
	
	public function main(){
		$M_admin_user = M('admin_user');
		$info = $M_admin_user->where(array('user_id'=>$_SESSION['admin_id']))->find();

		switch(PHP_OS)
		{
			case "Linux":
				$sysReShow = (false !== ($sysInfo = sys_linux()))?"show":"none";
			break;
			
			case "FreeBSD":
				$sysReShow = (false !== ($sysInfo = sys_freebsd()))?"show":"none";
			break;
		/*	
			case "WINNT":
				$sysReShow = (false !== ($sysInfo = sys_windows()))?"show":"none";
			break;
		*/	
			default:
			break;
		}
		$session_start=isfun("session_start");
		
		$this->assign('info',$info);
		$this->assign('sysReShow',$sysReShow);
		$this->assign('sysInfo',$sysInfo);
		$this->assign('session_start',$session_start);
		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 头部导航菜单
      +----------------------------------------------------------
     */
	 public function topMenu() {
		parent::logined();
		$this->display();
    }
	
	/**
      +----------------------------------------------------------
     * 左边导航菜单
      +----------------------------------------------------------
     */
	 public function leftMenu() {
		parent::logined();
		$this->display();
    }
	
	
	/**
      +----------------------------------------------------------
     * 清除缓存目录
      +----------------------------------------------------------
     */
	function clearCache($type=0,$path=NULL) {
		parent::logined();
		D('Index')->deldir(dirname(RUNTIME_PATH));
		$this->success('更新缓存文件成功！');
    }
	

}