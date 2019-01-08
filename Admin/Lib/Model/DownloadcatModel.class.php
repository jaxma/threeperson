<?php

class DownloadcatModel extends Model {
	/**
      +----------------------------------------------------------
     * 文章分类列表
      +----------------------------------------------------------
     */
    public function listDownloadcat() {
		$catList=get_article_categories_tree(0,'downloadcat');
		return $catList;               //获取分类结构
    }
}

?>
