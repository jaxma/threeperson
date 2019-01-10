<?php
/**
* description:统计经销商数量
* date:2016/06/25
* author:z
*/
class IndexModel extends Model
{
	//统计经销商
	function get_agent_number(){
		$agentObj = D('Distributor');
		$mulCount = array();
		$levelList = C('LEVEL_NAME');
		$leveCount = count($levelList);
		$agentSum = $agentObj->count('id');
		$auditSum = $agentObj->where(array('audited'=>1))->count('id');
		$mulCount['agentSum'] = $agentSum;
		$mulCount['auditSum'] = $auditSum;
		foreach($leveCount as $k=>$v){
			$mulCount['agent'] = $agentObj->where(array('level'=>$k))->count('id');
		}
		return $mulCount;
	}
}