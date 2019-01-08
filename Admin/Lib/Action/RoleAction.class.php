<?php
class RoleAction extends CommonAction {

    /**
      +----------------------------------------------------------
     * 角色列表页面
      +----------------------------------------------------------
     */
    public function index() {	
		$this->assign('admin_list',  D('Privilege')->get_role_list());
        $this->display();
    }
	
	/**
      +----------------------------------------------------------
     * 添加角色页面
      +----------------------------------------------------------
     */
    public function add(){
		
		/* 检查权限 */
		
		$M_role = M('role');
		$M_admin_action = M('admin_action');
		
		require('./Admin/Lang/' .C('DEFAULT_LANG'). '/priv_action.php');
		
		$priv_str = '';
		
		/* 获取栏目管理分类 */
		$articlecat_role=M('articlecat')->where("parent_id=1")->select();

		foreach ($articlecat_role as $key => $value) {
			$priv_list=M('articlecat')->where("parent_id='$value[cat_id]'")->select();
			$articlecat_role[$key]['chid']=$priv_list;

		}

		$articlecat_role2=M('articlecat')->where("parent_id=138")->select();

		foreach ($articlecat_role2 as $key => $value) {
			$priv_list=M('articlecat')->where("parent_id='$value[cat_id]'")->select();
			$articlecat_role2[$key]['chid']=$priv_list;
			
		}
		//print_r($articlecat_role);exit();
		$this->assign('articlecat_role',$articlecat_role);
		$this->assign('articlecat_role2',$articlecat_role2);
		$this->assign('priv_arr',    $priv_arr);
		$this->assign('lang',        $_LANG);
		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加角色动作
      +----------------------------------------------------------
     */
	function insert()
	{
		/* 检查权限 */

		$act_list = @join(",", $_POST['action_code']);
		$M_role=M('role');
		$data['role_name']=trim($_POST['user_name']);
		$data['action_list']=$act_list;
		$data['role_describe']=trim($_POST['role_describe']);
		//print_r($data);exit();
		$new_id = $M_role->add($data);
		//print_r($new_id);exit();
		/* 记录管理员操作 */
		parent::admin_log($_POST['user_name'], 'add', 'role');
		$this->success($_POST['user_name'].'添加成功');
		
	}
	
	/**
      +----------------------------------------------------------
     * 修改管理员信息页面
      +----------------------------------------------------------
     */
    public function edit() {
		require('./Admin/Lang/' .C('DEFAULT_LANG'). '/priv_action.php');
		
		$M_role = M('role');
		$M_admin_action = M('admin_action');
		
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		/* 获得该管理员的权限 */
		$row = $M_role->field('action_list')->where(array('role_id'=>$id))->find();	
		$priv_str = $row['action_list'];

		/* 查看是否有权限编辑其他管理员的信息 */
		if ($_SESSION['admin_id'] != $id){
		}

		/* 获取角色信息 */
		$user_info = $M_role->where(array('role_id'=>$id))->find();
		$action_list=explode(',', $user_info['action_list']);
		
		/* 获取栏目管理分类 */
		$articlecat_role=M('articlecat')->where("parent_id=1")->select();

		foreach ($articlecat_role as $key => $value) {
			$priv_list=M('articlecat')->where("parent_id='$value[cat_id]'")->select();
			$articlecat_role[$key]['chid']=$priv_list;

		}

		$articlecat_role2=M('articlecat')->where("parent_id=138")->select();

		foreach ($articlecat_role2 as $key => $value) {
			$priv_list=M('articlecat')->where("parent_id='$value[cat_id]'")->select();
			$articlecat_role2[$key]['chid']=$priv_list;
			
		}
		//print_r($articlecat_role);exit();
		$this->assign('articlecat_role',$articlecat_role);
		$this->assign('action_list',$action_list);
		$this->assign('articlecat_role2',$articlecat_role2);

		
		
		



		
		// print_r($priv_arr);
		// exit();

		$this->assign('user',        $user_info);
		$this->assign('lang',        $_LANG);
		$this->assign('priv_arr',    $priv_arr);
		$this->assign('user_id',     $_GET['id']);
		$this->display();
    }
	
	
	/**
      +----------------------------------------------------------
     * 更新管理员信息
      +----------------------------------------------------------
     */
	function update()
	{
		/* 检查权限 */
		$id=intval($_POST['id']);
		$message = A("Commonfunction");
		$act_list = @join(",", $_POST['action_code']);
		$M_role=M('role');
		$data['role_name']=trim($_POST['user_name']);
		$data['action_list']=$act_list;
		$data['role_describe']=trim($_POST['role_describe']);
		$M_role->where(array('role_id'=>$id))->save($data);
		$this->success($_POST['user_name'].'修改成功');
	}

	
	/**
      +----------------------------------------------------------
     * 删除一个角色
      +----------------------------------------------------------
     */
	function del()
	{
		/* 检查权限 */
		
		$M_role=M('role');
		
		$id = intval($_GET['id']);
		$remove_num = M('admin_user')->where(array('role_id'=>$_GET['id']))->count();
		if($remove_num > 0){
			$this->error('存在该角色的管理员，不能删除！');
		}else{
			$M_role->where(array('role_id'=>$_GET['id']))->delete();
		}
		$this->success('删除角色成功！');
	}
	
}

?>