<?php
class Adjust
{
	//递归查询联盟总代下属
	function findBelow($pid)
	{
		$where = array('pid' => $pid);
		$field = array("id,name,audited,phone,wechatnum,bossname,pname,level,levname,time");
		$arr = M('Distributor')->where($where)->field($field)->select();
		$newArr = array();
		$newArr[] = $arr;
		foreach($arr as $v) {
			$newArr = array_merge($newArr,self::findBelow($v['id']));
		}
		return $newArr;
	}
}
?>