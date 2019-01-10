<?php 
class PublicAction extends Action{
	public function header(){
		$this->display();
	}
	
	public function left(){
		$this->level_num=C('LEVEL_NUM');
		$this->level_name=C('LEVEL_NAME');
		$this->display();
	}
	
	public function desktop(){
		$agentObj = D('Distributor');
		$level = C('LEVEL_NAME');
		$mulCount = array();
		$agentSum = $agentObj->count('id');
		$auditSum = $agentObj->where(array('audited'=>1))->count('id');
		$mulCount['agentSum'] = $agentSum;
		$mulCount['auditSum'] = $auditSum;
		$mulCount['notauditSum'] =$agentSum-$auditSum;
		foreach($level as $k=>$v){
			$mulCount['agent'][] = $agentObj->where(array('level'=>$k))->count('id');
		}
		$this->assign('mulCount',$mulCount);
		$this->assign('level',$level);
		$this->display();
	}
	
	public function footer(){
		$this->display();
	}
}

?>