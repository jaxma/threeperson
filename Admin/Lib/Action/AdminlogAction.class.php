<?php
class AdminlogAction extends CommonAction {

    /**
      +----------------------------------------------------------
     * 管理员日志列表
      +----------------------------------------------------------
     */
    public function index() {
		$M_adminlog=M('admin_log');
		parent::admin_priv('logs_manage');
		$user_id   = !empty($_REQUEST['id'])       ? intval($_REQUEST['id']) : 0;
		

		$filter = array();
		$filter['admin_ip'] = $admin_ip  = !empty($_REQUEST['ip'])       ? $_REQUEST['ip']         : '';
		$filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'al.log_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		/* 查询IP地址列表 */
		$ip_list = array();
		$res = $M_adminlog->Distinct(true)->field('ip_address')->select();
		foreach ($res as $key => $value)
		{
			$ip_list[$value['ip_address']] = $value['ip_address'];
		}
		
		$filter['record_count'] = $count = D("Adminlog")->listAdminlogCount($filter);
        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
		
		$this->assign("list", D("Adminlog")->get_admin_logs($user_id,$admin_ip,$page->firstRow, $page->listRows,$filter));
		$this->assign('admin_ip',   $admin_ip);
		$this->assign('ip_list',   $ip_list);
		$this->assign("filter", $filter);
		$this->display();
    }
	
	/**
      +----------------------------------------------------------
     * 批量删除日志记录
      +----------------------------------------------------------
     */
	function batch_drop(){
		$M_adminlog=M('admin_log');
		parent::admin_priv('logs_drop');

		$log_date = isset($_POST['log_date']) ? $_POST['log_date'] : '';

		/* 按日期删除日志 */
		if ($log_date){
			if ($log_date == '0'){
				$this->error('请选择要删除日志的日期！');
			}elseif ($_POST['log_date'] > '0'){
				$where = " WHERE 1 ";
				switch ($_POST['log_date']){
					case '1':
						$a_week = time()-(3600 * 24 * 7);
						$where .= " AND log_time <= '".$a_week."'";
						break;
					case '2':
						$a_month = time()-(3600 * 24 * 30);
						$where .= " AND log_time <= '".$a_month."'";
						break;
					case '3':
						$three_month = time()-(3600 * 24 * 90);
						$where .= " AND log_time <= '".$three_month."'";
						break;
					case '4':
						$half_year = time()-(3600 * 24 * 180);
						$where .= " AND log_time <= '".$half_year."'";
						break;
					case '5':
						$a_year = time()-(3600 * 24 * 365);
						$where .= " AND log_time <= '".$a_year."'";
						break;
				}
				$sql = "DELETE FROM " . C('DB_PREFIX') . "admin_log".$where;
				$res = $M_adminlog->query($sql);
				parent::admin_log('','remove', 'adminlog');
				$this->success('删除管理员日志成功！');
			}
		}
		/* 如果不是按日期来删除, 就按ID删除日志 */
		else
		{
			$checkboxes=$_POST['checkboxes'];
			$checkboxesArr=implode(',',$checkboxes);
			if ($M_adminlog->where("log_id IN ({$checkboxesArr})")->delete()){
				parent::admin_log('','remove', 'adminlog');
				$this->success('删除管理员日志成功！');
			}else{
				$this->error('删除管理员日志失败！');
			}
		}
	}
	
	
}

?>