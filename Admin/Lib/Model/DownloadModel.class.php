<?php

class DownloadModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function listDownload($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Download = M('Download');
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND " . get_goods_children($filter['cat_id']);
		}
		/* 获取文章数据 */
		$sql = 'select * from '. C('DB_PREFIX') . 'download '.
			   'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'].
			   ' LIMIT '.$firstRow.','.$listRows;

		$result = $M_Download->query($sql);
		
        return $result; 
    }
	
	/**
      +----------------------------------------------------------
     * 文章总数
      +----------------------------------------------------------
     */
    public function listDownloadCount($filter = array()) {
		$M_Download = M('Download');
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND " . get_goods_children($filter['cat_id']);
		}
        $sql = 'select count(id) from '.C('DB_PREFIX').'download '.
               'WHERE 1 ' .$where;
		
        $count = $M_Download->query($sql);

		return $count[0]['count'];
    }	
}

?>
