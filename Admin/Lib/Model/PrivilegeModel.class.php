<?php

class PrivilegeModel extends Model {
	/**
      +----------------------------------------------------------
     * ����Ա�б�
      +----------------------------------------------------------
     */
	function get_admin_userlist()
	{
		$list=M('Admin_user')->field('user_id,user_name,email,add_time,last_login')->order('user_id DESC')->select();
		return $list;
	}
	
	/**
      +----------------------------------------------------------
     * ��ȡ��ɫ�б�
      +----------------------------------------------------------
     */
	function get_role_list()
	{
		$list=M('Role')->field('role_id, role_name, action_list,role_describe')->select();
		return $list;
	}

}

?>
