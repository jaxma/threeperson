<?php

class PrivilegeModel extends Model {
	/**
      +----------------------------------------------------------
     * �жϹ���Ա��ĳһ�������Ƿ���Ȩ�ޡ�
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
