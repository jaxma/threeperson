<?php

class ArticleModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function listArticle($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Article = M("Article");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_article_children($filter['cat_id']);
		}
		/* 获取文章数据 */
		$sql = 'SELECT a.article_id, a.cat_id, a.title, a.add_time, a.is_open, a.is_recommend, a.sort_order, ac.cat_name ,a.original_img ,a.thumb_img '.
			   'FROM ' . C('DB_PREFIX') . 'article AS a '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'articlecat AS ac ON ac.cat_id = a.cat_id '.
			   'WHERE 1 ' .$where. ' ORDER by is_recommend desc,'.$filter['sort_by'].' '.$filter['sort_order']. ',a.add_time desc' . 
			   ' LIMIT '.$firstRow.','.$listRows;

		//echo $sql;

		$result = $M_Article->query($sql);
		
        return $result;
    }
	
	/**
      +----------------------------------------------------------
     * 文章总数
      +----------------------------------------------------------
     */
    public function listArticleCount($filter = array()) {
		$M_Article = M("Article");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_article_children($filter['cat_id']);
		}
        $sql = 'SELECT COUNT(article_id) AS count FROM ' . C('DB_PREFIX') . 'article AS a '.
               'LEFT JOIN ' . C('DB_PREFIX') . 'articlecat AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where;
		
        $count = $M_Article->query($sql);

		return $count[0]['count'];
    }	
}

?>
