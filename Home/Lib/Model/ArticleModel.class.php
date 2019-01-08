<?php

class ArticleModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function listArticle($firstRow = 0, $listRows = 20, $filter = array()) {

		$M_Article = M("Article");
		$where = '';
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_article_children($filter['cat_id']);
		}
		/* 获取文章数据 */
		$sql = 'SELECT a.article_id, a.cat_id, a.title, a.add_time, a.is_open,a.short, a.sort_order, ac.cat_name ,a.original_img ,a.thumb_img  '.
			   'FROM ' . C('DB_PREFIX') . 'article AS a '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'articlecat AS ac ON ac.cat_id = a.cat_id '.
			   'WHERE 1 ' .$where. ' ORDER BY a.sort_order ASC,a.article_id DESC'.
			   ' LIMIT '.$firstRow.','.$listRows;
		$result = $M_Article->query($sql);
		
		foreach($result as $key => $value){
			$result[$key]['title'] = String::msubstr($result[$key]['title'], 0, 30);
		}
		
        return $result;
    }
	/**
      +----------------------------------------------------------
     * 文章列表总数
      +----------------------------------------------------------
     */
    public function listArticleCount($filter = array()) {
		$M_Article = M("Article");
		$where = '';
		if ($filter['cat_id'])
		{
			$where .= " AND a." . get_article_children($filter['cat_id']);
		}
        $sql = 'SELECT COUNT(article_id) AS count FROM ' . C('DB_PREFIX') . 'article AS a '.
               'WHERE 1 ' .$where;
		
        $count = $M_Article->query($sql);

		return $count[0]['count'];
    }	
}

?>
