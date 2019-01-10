<?php
/**
*	topos经销商管理系统
*/
header("Content-Type: text/html; charset=utf-8");
class CountAction extends CommonAction
{
	//统计经销商
	public function index()
	{
		$distributor = M('distributor');
		$order = M('Order');
		$count = array();
		$count['a'] = $distributor->count();
		$count['b'] = $distributor->where(array('audited' => 1))->count();
		$count['c'] = $distributor->where(array('audited' => 0))->count();
		$count['1'] = $distributor->where(array('level' => 1))->count();
		$count['2'] = $distributor->where(array('level' => 2))->count();
		$count['3'] = $distributor->where(array('level' => 3))->count();
		$count['4'] = $distributor->where(array('level' => 4))->count();
		$count['5'] = $distributor->where(array('level' => 5))->count();
		$count['6'] = $distributor->where(array('level' => 6))->count();
		import('ORG.Util.Page');
		//取得满足条件的记录数
		if ($count['a'] > 0) {//总管理员
			//创建分页对象
			$p = new Page($count['a'], 10);
			$limit= $p->firstRow . "," . $p->listRows;
			$voList=$distributor->order('time desc')->field('id,name,levname')->limit($limit)->select();
			foreach ($voList as $k => $v) {
				//统计经销商订单
				$voList[$k]['orcount'] = $order->where(array('user_id' => $voList[$k]['id']))->count('distinct order_num');
				$applyList = $order->where(array('user_id' => $voList[$k]['id']))->field('total_num,total_price')->order('time desc')->group('order_num')->select();
				$total_num = 0;
				$total_price = 0;
				foreach ($applyList as $key => $value) {
					$total_num = $total_num+$applyList[$key]['total_num'];
					$total_price = $total_price+$applyList[$key]['total_price'];
				}
				$voList[$k]['total_num'] = $total_num;
				$voList[$k]['total_price'] = $total_price;
				//统计经销商发货件数
				$f_num = 0;
				$list=M('Product')->where(array('send_id' => $voList[$k]['id']))->field('product_num')->order('time desc')->select();
				foreach ($list as $ke => $val) {
					$f_num = $f_num + $list[$ke]['product_num'];
				}
				$voList[$k]['f_num'] = $f_num;
				//统计经销商收货件数
				$s_num = 0;
				$listone=M('Product')->where(array('receive_id' => $voList[$k]['id']))->field('product_num')->order('time desc')->select();
				foreach ($listone as $o => $i) {
					$s_num = $s_num + $listone[$o]['product_num'];
				}
				$voList[$k]['s_num'] = $s_num;
			}
			//*分页显示*
			$page = $p->show();
			$this->page = $page;
			$this->mList = $voList;
		}
		$this->count = $count;
		$this->level_name=C('LEVEL_NAME');
		$this->level_num=C('LEVEL_NUM');
		$this->display();
	}
	
	public function orderconut(){
		$this->display();
	}
}
?>