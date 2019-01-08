<?php

class TagscatModel extends Model {
	/**
      +----------------------------------------------------------
     * 产品分类列表
      +----------------------------------------------------------
     */
    public function listTagscat() {
		$catList=get_Tags_categories_tree(0);
		return $catList;               //获取分类结构
    }
}

?>
