<?php

class GoodsModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function listGoods($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Goods = M("Goods");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_goods_children($filter['cat_id']);
		}
		if ($filter['group_id'])
		{
			$where .= " AND a.group_id= '" . $filter['group_id']."'";
		}
		/* 获取文章数据 */
		$sql = 'SELECT a.*, ac.cat_name  '.
			   'FROM ' . C('DB_PREFIX') . 'goods AS a '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'goodscat AS ac ON ac.cat_id = a.cat_id '.
			   'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'].',a.add_time desc'.
			   ' LIMIT '.$firstRow.','.$listRows;

		$result = $M_Goods->query($sql);
		
        return $result;
    }
	
	/**
      +----------------------------------------------------------
     * 文章总数
      +----------------------------------------------------------
     */
    public function listGoodsCount($filter = array()) {
		$M_Goods = M("Goods");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_goods_children($filter['cat_id']);
		}
		if ($filter['group_id'])
		{
			$where .= " AND a.group_id= '" . $filter['group_id']."'";
		}
        $sql = 'SELECT COUNT(goods_id) AS count FROM ' . C('DB_PREFIX') . 'goods AS a '.
               'LEFT JOIN ' . C('DB_PREFIX') . 'goodscat AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where;
		
        $count = $M_Goods->query($sql);

		return $count[0]['count'];
    }	

    public function getGroupId($filter = array()) {
		$M_Goods = M("Goods");
		$where = '';

		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_goods_children($filter['cat_id']);
		}
        $sql = 'SELECT a.group_id FROM ' . C('DB_PREFIX') . 'goods AS a '.
               'LEFT JOIN ' . C('DB_PREFIX') . 'goodscat AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where ." group by group_id";
		
        $count = $M_Goods->query($sql);

		return $count;
    }	
}

?>
