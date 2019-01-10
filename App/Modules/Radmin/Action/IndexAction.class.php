<?php
/**
*	topos经销商管理系统主页
*/
class IndexAction extends CommonAction
{
	//topos经销商管理系统主页
	public function index()
	{
            
//            var_dump($this->all_action);
//            var_dump($this->admin_auth_action);
//            var_dump($_SESSION['admin_auth_action']);
            
//            import('Lib.Action.Admin','App');
//            $Admin = new Admin();
//            print_r($Admin->admin_auth_action);
            
//            $agentObj = D('Distributor');
//            $level = C('LEVEL_NAME');
//            $mulCount = array();
//            $agentSum = $agentObj->count('id');
//            $auditSum = $agentObj->where(array('audited'=>1))->count('id');
//            $mulCount['agentSum'] = $agentSum;
//            $mulCount['auditSum'] = $auditSum;
//            $mulCount['notauditSum'] =$agentSum-$auditSum;
//            foreach($level as $k=>$v){
//                    $mulCount['agent'][$k] = $agentObj->where(array('level'=>$k))->count('id');
//            }
//            $this->assign('mulCount',$mulCount);
//            $this->assign('level',$level);
//            
//            $this->level_num=C('LEVEL_NUM');
//            $this->level_name=C('LEVEL_NAME');
            
            //显示未处理数量
            // $this->data = $this->count_handle();
            $this->display();
	}

	//欢迎页面
	public function welcome()
	{
		$this->display();
	}

	//退出系统登录
	public function logout()
	{
		session('aid',null);
		session('aname',null);
		$this->redirect(GROUP_NAME . '/Login/index');
	}
	
	//进入后台
	public function htgl(){
		$this->level_num=C('LEVEL_NUM');
		$this->level_name=C('LEVEL_NAME');
		$this->display();
	}
        
        //统计未处理数量
        public function count_handle() {
            //返回的数据结构
//            $data = [
//                //模块名称
//                'money' => [
//                    'yes_audit' => 2,//已审核数量
//                    'no_audit' => 5,//未审核数量
//                ],
//                'agent' => [
//                    'yes_audit' => 2,//已审核数量
//                    'no_audit' => 5,//未审核数量
//                ],
//                //..........
//            ];
            $data = [];
            //代理管理
            //未处理的升级申请数量
            $upgrade_apply_count = M('distributor_upgrade_apply')->where(['status' => 0])->count();
            if ($upgrade_apply_count > 0) {
                $data['agent']['upgrade_apply_count'] = $upgrade_apply_count;
            }
            
            //审核代理
            //未处理的审核代理数量(待总部审核)
            $condition['audited'] = ['in', [2, 4]];
            $condition['_logic'] = 'or';
            //推荐的时候，上级是总部
            $where=[
                'audited' => 0,
                'pid' => 0,
            ];
            $condition['_complex'] = $where;
            $audit_head_count = M('distributor')->where($condition)->count();
            if ($audit_head_count > 0) {
                $data['agent_audit']['audit_head_count'] = $audit_head_count;
            }
            //未处理的审核代理数量(待上级审核)
            $audit_boss_count = M('distributor')->where(['audited'=>0, 'pid'=>['gt',0]])->count();
            if ($audit_boss_count > 0) {
                $data['agent_audit']['audit_boss_count'] = $audit_boss_count;
            }
            
            //审核虚拟币
            //未处理的充值申请数量
            $money_apply_count = M('money_apply')->where(['status'=>'0', 'audit_id'=>0])->count();
            if ($money_apply_count > 0) {
                $data['money']['apply_count'] = $money_apply_count;
            }
            //未处理的提现申请数量
            $money_refund_count = M('money_refund_apply')->where(['status' => '0', 'audit_id'=>0])->count();
            if ($money_refund_count > 0) {
                $data['money']['refund_count'] = $money_refund_count;
            }
            
            //代理商城管理
            //未审核订单数量
            $agent_shop_order_count = M('order')->where(['status'=>1, 'o_id'=>0])->count('distinct order_num');
            if ($agent_shop_order_count > 0) {
                $data['agent_shop']['not_audit_order_count'] = $agent_shop_order_count;
            }
            
            //云仓
            //未审核订单数量
            $stock_order_count = M('stock_order')->where(['status'=>1, 'o_id'=>0])->count('distinct order_num');
            if ($stock_order_count > 0) {
                $data['stock']['not_audit_order_count'] = $stock_order_count;
            }
            
//            var_dump($money_apply_count);die;
            
            return $data;
        }
}
?>