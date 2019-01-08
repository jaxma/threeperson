<?php
class PrivilegeAction extends CommonAction {

    /**
      +----------------------------------------------------------
     * 管理员列表
      +----------------------------------------------------------
     */
    public function index() {	
		$this->assign('admin_list',  D('Privilege')->get_admin_userlist());
        $this->display();
    }
	
	/**
      +----------------------------------------------------------
     * 添加管理员信息页面
      +----------------------------------------------------------
     */
    public function add(){
		$M_admin_user=M('admin_user');
		
		$this->assign('select_role',  D('Privilege')->get_role_list());
		$this->display();
    }
	
	/**
      +----------------------------------------------------------
     * 添加管理员动作
      +----------------------------------------------------------
     */
	public function insert(){
	
		$M_admin_user = M('admin_user');
		$M_role = M('role');
		
		$admin_name  = !empty($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
		$admin_email = !empty($_REQUEST['email'])     ? trim($_REQUEST['email'])     : '';

		/* 判断管理员是否已经存在 */
		if (!empty($_POST['user_name'])){
			$is_only = $M_admin_user->field('user_name')->where(array('user_name'=>$admin_name))->count();
			if ($is_only == 1){
				$this->error('已存在该用户名的管理员！');
			}
		}

		/* Email地址是否有重复 */
		if (!empty($admin_email)){
			$is_only = $M_admin_user->field('email')->where(array('email'=>$admin_email))->count();
			if ($is_only == 1){
				$this->error('已存在该邮箱的管理员！');
			}
		}

		/* 获取添加日期及密码 */
		$add_time = time();
		
		$password  = md5(trim($_POST['new_password']));
		$role_id = '';
		$action_list = '';
		if (!empty($_POST['select_role']))
		{
			$row = $M_role->field('action_list')->where(array('role_id'=>$_POST['select_role']))->find();		
			$action_list = $row['action_list'];
			$role_id = $_POST['select_role'];
		}
			   
		$data['user_name']   = trim($_POST['user_name']);
		$data['email']       = trim($_POST['email']);
		$data['password']    = $password;
		$data['add_time']    = $add_time;
		$data['nav_list']    = $nav_list;
		$data['action_list'] = $action_list;
		$data['role_id']     = $role_id;
		$new_id = $M_admin_user->add($data);
		
		/* 记录管理员操作 */
		parent::admin_log($_POST['user_name'], 'add', 'privilege');

		$this->success('添加管理员成功！');
	}
	
	/**
      +----------------------------------------------------------
     * 修改管理员信息页面
      +----------------------------------------------------------
     */
    public function edit() {
		$M_admin_user=M('admin_user');
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		/* 查看是否有权限编辑其他管理员的信息 */
		if ($_SESSION['admin_id'] != $id){
		}
		$user_info = $M_admin_user->where(array('user_id'=>$id))->field('user_id, user_name, email, password, agency_id, role_id')->find();
		/* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
		if ($user_info['action_list'] != 'all'){
		   $this->assign('select_role',  D('Privilege')->get_role_list());
		}
		$this->assign('user',        $user_info);
		$this->display();
    }
	
	
	/**
      +----------------------------------------------------------
     * 更新管理员信息
      +----------------------------------------------------------
     */
	function update()
	{
		
		$M_admin_user = M('admin_user');
		$M_role = M('role');
		
		$admin_id    = !empty($_REQUEST['id'])        ? intval($_REQUEST['id'])      : 0;
		$admin_name  = !empty($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
		$admin_email = !empty($_REQUEST['email'])     ? trim($_REQUEST['email'])     : '';

		$password = !empty($_POST['new_password']) ? ", password = '".md5($_POST['new_password'])."'"    : '';

		/* 查看是否有权限编辑其他管理员的信息 */
		if ($_SESSION['admin_id'] != $_REQUEST['id']){
		}
		
		$nav_list = '';

		/* 判断管理员是否已经存在 */
		if (!empty($admin_name)){
			$is_only = $M_admin_user->field('user_name')->where(array('user_name'=>$admin_name,'user_id'=>array('neq'=>$admin_id)))->count();
			if ($is_only == 1){
				$this->error('已存在该用户名的管理员！');
			}
		}

		/* Email地址是否有重复 */
		if (!empty($admin_email)){
			$is_only = $M_admin_user->field('email')->where(array('email'=>$admin_email,'user_id'=>array('neq'=>$admin_id)))->count();
			if ($is_only == 1){
				$this->error('已存在该邮箱的管理员！');
			}
		}

		//如果要修改密码
		$pwd_modified = false;

		if (!empty($_POST['new_password'])){
			/* 查询旧密码并与输入的旧密码比较是否相同 */
			$row = $M_admin_user->field('password')->where(array('user_id'=>$admin_id))->find();	
			$old_password = $row['password'];
			if ($old_password <> (md5($_POST['old_password']))){
				$this->error('您输入的旧密码与原密码不一致！');
			}

			/* 比较新密码和确认密码是否相同 */
			if ($_POST['new_password'] <> $_POST['pwd_confirm']){
				$this->error('新密码与确认密码不一致！');
			}else{
				$pwd_modified = true;
			}
		}

		$role_id = '';
		$action_list = '';
		if (!empty($_POST['select_role'])){
			$row = $M_role->field('action_list')->where(array('role_id'=>$_POST['select_role']))->find();			
			$action_list = ', action_list = \''.$row['action_list'].'\'';
			$role_id = ', role_id = '.$_POST['select_role'].' ';
		}
		//更新管理员信息
		$sql = "UPDATE " . C('DB_PREFIX') . "admin_user SET ".
			   "user_name = '$admin_name', ".
			   "email = '$admin_email' ".
			   $action_list.
			   $role_id.
			   $password.
			   $nav_list.
			   "WHERE user_id = '$admin_id'";

	    $M_admin_user->query($sql);
	   /* 记录管理员操作 */
	   parent::admin_log($_POST['user_name'], 'edit', 'privilege');
	   $this->success('修改管理员信息成功！',U('Privilege/index'));
	}

	
	/**
      +----------------------------------------------------------
     * 删除一个管理员
      +----------------------------------------------------------
     */
	function del(){
	
		$M_admin_user = M('admin_user');
		

		$id = intval($_GET['id']);

		/* 获得管理员用户名 */
		$user_info = $M_admin_user->where(array('user_id'=>$id))->field('user_id, user_name, email, password, agency_id, role_id')->find();
		$admin_name= $user_info['user_name'];

		/* ID为1的不允许删除 */
		if ($id == 1){
			$this->error('不允许删除该管理员！');
		}

		/* 管理员不能删除自己 */
		if ($id == $_SESSION['admin_id']){
			$this->error('不能删除自己！');
		}
		if ($M_admin_user->where(array('user_id'=>$id))->delete()){
			parent::admin_log(addslashes($admin_name), 'remove', 'privilege');
			$this->success('删除管理员成功！');
		}
		
		
	}
	
	
	/**
      +----------------------------------------------------------
     * 为管理员分配权限
      +----------------------------------------------------------
     */
	function allot()
	{	
		if ($_SESSION['admin_id'] == $_GET['id'])
		{
		}
	
		require('./Admin/Lang/' .C('DEFAULT_LANG'). '/priv_action.php');		
		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表	
		
		/* 获得该管理员的权限 */
		$roleRow=M('admin_user')->where(array('user_id'=>$_REQUEST['id']))->find();
		$priv_str = $roleRow['action_list'];

		/* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
		if ($priv_str == 'all')
		{
		   $message->sys_msg('您不能对此管理员的权限进行任何操作！',0,$link,true);
		}
		
		/* 获取权限的分组数据 */
		$sql_query = "SELECT action_id, parent_id, action_code, relevance FROM " . C('DB_PREFIX') . "admin_action".
					 " WHERE parent_id = 0";
					 
		$res = $Model->query($sql_query);
		

		
		foreach($res as $k=>$v){
			$priv_arr[$v['action_id']] = $v;
		}

		/* 按权限组查询底级的权限名称 */
		$sql = "SELECT action_id, parent_id, action_code, relevance FROM " . C('DB_PREFIX') . "admin_action".
			   " WHERE parent_id " .db_create_in(array_keys($priv_arr));
		$result = $Model->query($sql);

		foreach($result as $i=>$j){
			$priv_arr[$j["parent_id"]]["priv"][$j["action_code"]] = $j;
		}

		// 将同一组的权限使用 "," 连接起来，供JS全选
		foreach ($priv_arr AS $action_id => $action_group)
		{
			$priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

			foreach ($action_group['priv'] AS $key => $val)
			{
				$priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
			}
		}
		$this->assign('priv_arr',    $priv_arr);
		$this->assign('lang',        $_LANG);
		$this->assign('user_id',     $_GET['id']);

		/* 显示页面 */
		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * 更新管理员的权限
      +----------------------------------------------------------
     */
	function update_allot()
	{
		$M_admin_user=M('admin_user');
		/* 取得当前管理员用户名 */
		$adminUser = $M_admin_user->where(array('user_id'=>$_POST['id']))->field('user_name')->find();
		$admin_name=$adminUser['user_name'];
		/* 更新管理员的权限 */
		$act_list = @join(",", $_POST['action_code']);
		$data['action_list']=$act_list;
		$data['role_id']='';
		$M_admin_user->where(array('user_id'=>$_POST['id']))->save($data);
		/* 动态更新管理员的SESSION */
		if ($_SESSION["admin_id"] == $_POST['id'])
		{
			$_SESSION["action_list"] = $act_list;
		}
		
		/* 记录管理员操作 */
		parent::admin_log(addslashes($admin_name), 'edit', 'privilege');
		
		$this->success('更新管理员权限成功！');
	}
	
}

?>