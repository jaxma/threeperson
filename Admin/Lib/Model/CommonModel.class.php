<?php

class PrivilegeModel extends Model {
	/**
      +----------------------------------------------------------
     * 判断管理员对某一个操作是否有权限。
      +----------------------------------------------------------
     */
	function admin_priv($priv_str, $msg_type = '' , $msg_output = true){
		
		if ($_SESSION['action_list'] == 'all'){
			return true;
		}

		if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false){
			return false;
		}else{
			return true;
		}
	}

}

?>
