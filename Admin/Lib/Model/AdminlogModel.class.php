<?php

class AdminlogModel extends Model {
	/**
      +----------------------------------------------------------
     * ����Ա��־�б�
      +----------------------------------------------------------
     */
	function get_admin_logs($user_id=0,$admin_ip='',$firstRow,$listRows,$filter)
	{
		$M_adminlog=M('admin_log');
		//��ѯ����
		$where = " WHERE 1 ";
		if (!empty($user_id))
		{
			$where .= " AND al.user_id = '$user_id' ";
		}
		elseif (!empty($admin_ip))
		{
			$where .= " AND al.ip_address = '$admin_ip' ";
		}
		
		/* ��ȡ����Ա��־��¼ */
		$list = array();
		$sql  = 'SELECT al.*, u.user_name FROM ' . C('DB_PREFIX') . 'admin_log AS al '.
				'LEFT JOIN ' . C('DB_PREFIX') . 'admin_user AS u ON u.user_id = al.user_id '.
				$where .' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'].
				' LIMIT '.$firstRow.','.$listRows;
		$list = $M_adminlog->query($sql);
		return array('list' => $list, 'filter' => $filter, 'page_count' =>  $filter['page_count'], 'record_count' => $filter['record_count']);
	}
	
	/**
      +----------------------------------------------------------
     * ����Ա��־����
      +----------------------------------------------------------
     */
    public function listAdminlogCount($filter = array()) {
		$M_Adminlog = M("Adminlog");
		$filter = array();
		//��ѯ����
		$where = " WHERE 1 ";
		if (!empty($user_id))
		{
			$where .= " AND al.user_id = '$user_id' ";
		}
		elseif (!empty($admin_ip))
		{
			$where .= " AND al.ip_address = '$admin_ip' ";
		}

		$sql = 'SELECT COUNT(*) AS count FROM ' . C('DB_PREFIX') . 'admin_log AS al ' . $where;
		$count = $M_Adminlog->query($sql);		
		return $count[0]['count'];
    }
	
	

}

?>
