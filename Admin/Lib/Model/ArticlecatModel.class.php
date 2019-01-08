<?php

class ArticlecatModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章分类列表
      +----------------------------------------------------------
     */
    public function listArticlecat($pid=0) {
		$catList=get_article_categories_tree($pid);
		return $catList;               //获取分类结构
    }
}

?>
