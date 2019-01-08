<?php

class GoodscatModel extends Model {
	/**
      +----------------------------------------------------------
     * 产品分类列表
      +----------------------------------------------------------
     */
    public function listGoodscat() {
		$catList=get_goods_categories_tree(0);
		return $catList;               //获取分类结构
    }
}

?>
