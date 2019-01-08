<?php

class TagsModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function listTags($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Goods = M("Tags");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.tags_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_goods_children($filter['cat_id']);
		}
		/* 获取文章数据 */
		$sql = 'SELECT a.*, ac.cat_name  '.
			   'FROM ' . C('DB_PREFIX') . 'tags AS a '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'tagscat AS ac ON ac.cat_id = a.cat_id '.
			   'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'].
			   ' LIMIT '.$firstRow.','.$listRows;
		$result = $M_Goods->query($sql);
		
        return $result;
    }
	
	/**
      +----------------------------------------------------------
     * 文章总数
      +----------------------------------------------------------
     */
    public function listTagsCount($filter = array()) {
		$M_Goods = M("Tags");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.tags_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_goods_children($filter['cat_id']);
		}
        $sql = 'SELECT COUNT(tags_id) AS count FROM ' . C('DB_PREFIX') . 'tags AS a '.
               'LEFT JOIN ' . C('DB_PREFIX') . 'tagscat AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where;
		
        $count = $M_Goods->query($sql);

		return $count[0]['count'];
    }	
}

?>
