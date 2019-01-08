<?php

class GoodscatAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}
	/**
      +----------------------------------------------------------
     * 产品分类列表
      +----------------------------------------------------------
     */
    public function index() {
		$this->assign("action",$_GET['action']);
		$this->assign("parent_id",$_GET['parent_id']);
		$this->assign("cat_id",$_GET['cat_id']);
		
		$goodscat=D("Goodscat")->listGoodscat();
		
		$html='';
		foreach($goodscat as $key=>$value){
			$html.='<li class="closed"><a href="javascript:void(0);" onclick="goodscatInfo('.$value['id'].')"><span class="folder">'.$value['name'].'</span></a>';
				if($value['cat_id']){
					$html.='<ul>';
					foreach($value['cat_id'] as $key2=>$value2){
						$html.='<li class="closed"><a href="javascript:void(0);" onclick="goodscatInfo('.$value2['id'].')"><span class="folder">'.$value2['name'].'</span></a>';
							if($value2['cat_id']){
							$html.='<ul>';
								foreach($value2['cat_id'] as $key3=>$value3){
									$html.='<li class="closed"><a href="javascript:void(0);" onclick="goodscatInfo('.$value3['id'].')"><span class="folder">'.$value3['name'].'</span></a>';
										if($value3['cat_id']){
											$html.='<ul>';
											foreach($value3['cat_id'] as $key4=>$value4){
												$html.='<li class="closed"><a href="javascript:void(0);" onclick="goodscatInfo('.$value4['id'].')"><span class="folder">'.$value4['name'].'</span></a></li>';
											}
											$html.='</ul>';
										}
									$html.='</li>';
								}
								$html.='</ul>';
							}
						$html.='</li>';
					}
					$html.='</ul>';
				}
			$html.='</li>';
		}
		
		$this->assign("html",$html);
		$this->assign("goodscat",$goodscat);
		$this->display();
    }


    public function index2() {
		$this->assign("action",$_GET['action']);
		$this->assign("parent_id",$_GET['parent_id']);
		$this->assign("cat_id",$_GET['cat_id']);
		
		$goodscat=D("Goodscat")->listGoodscat();
		
		$html='';
		foreach($goodscat as $key=>$value){
			$html.='<li class="closed"><a href="'. U('Goods/index',array('cat_id'=>$value['id'])) .'" target="container"><span class="folder">'.$value['name'].'</span></a>';
				if($value['cat_id']){
					$html.='<ul>';
					foreach($value['cat_id'] as $key2=>$value2){
						$html.='<li class="closed"><a href="'. U('Goods/index',array('cat_id'=>$value2['id'])) .'"" target="container"><span class="folder">'.$value2['name'].'</span></a>';
							if($value2['cat_id']){
							$html.='<ul>';
								foreach($value2['cat_id'] as $key3=>$value3){
									$html.='<li class="closed"><a href="'. U('Goods/index',array('cat_id'=>$value3['id'])) .'" target="container"><span class="folder">'.$value3['name'].'</span></a>';
										if($value3['cat_id']){
											$html.='<ul>';
											foreach($value3['cat_id'] as $key4=>$value4){
												$html.='<li class="closed"><a href="'. U('Goods/index',array('cat_id'=>$value4['id'])) .'" target="container"><span class="folder">'.$value4['name'].'</span></a></li>';
											}
											$html.='</ul>';
										}
									$html.='</li>';
								}
								$html.='</ul>';
							}
						$html.='</li>';
					}
					$html.='</ul>';
				}
			$html.='</li>';
		}
		
		$this->assign("html",$html);
		$this->assign("goodscat",$goodscat);
		$this->display();
    }
	
	/**
      +----------------------------------------------------------
     * ajax载入添加产品分类页面
      +----------------------------------------------------------
     */
	public function add(){
		
		/* 权限判断 */
	
		$M_Tagscat = M("Tagscat");
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		$tagscat = $M_Tagscat->where("is_open=1 and parent_id=0")->select();
		$this->assign("tagscat",$tagscat);
		$this->assign("cat_select", goods_cat_list(0,$cat_id));
		
		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * ajax载入修改产品分类页面
      +----------------------------------------------------------
     */
	public function edit(){
		/* 权限判断 */
		$M_Tagscat = M("Tagscat");
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		$M_Goodscat = M("Goodscat");
		$cat = $M_Goodscat->where("cat_id=".$cat_id)->find();
		// $cat['tag_cat'] = explode(',', $cat['tag_cat']);
		$tagscat = $M_Tagscat->where("is_open=1 and parent_id=0")->select();
		$this->assign("tagscat",$tagscat);
		$this->assign("cat_select", goods_cat_list(0,$cat['parent_id']));
		$this->assign("cat", $cat);
		$this->assign("cat_id", $cat_id);
		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加产品分类
      +----------------------------------------------------------
     */
	public function insert(){
		/* 权限判断 */
		$data['cat_name']   = $_POST['cat_name'];
		$data['cat_type']   = 1;
		$data['cat_desc']   = $_POST['cat_desc'];
		$data['cat_attr']   = $_POST['cat_attr'];
		$data['keywords']   = $_POST['keywords'];
		$data['tag_cat']   	= $_POST['tag_cat'];
		if ($data['tag_cat']) {
			$data['tag_cat'] = implode(',', $data['tag_cat']);
		}else{
			$data['tag_cat']='';
		}
		$data['parent_id']  = intval($_POST['parent_id']);
		$data['sort_order'] = intval($_POST['sort_order']);
		if(!empty($_FILES['goodscat_img']['tmp_name'])){
			$thumbPath='Uploads/goodscat/thumb_img/';
			$originalPath='Uploads/goodscat/original_img/';
			$thumbPrefix='goodscat_';
			$widthSize='1153';
			$heightSize='271';
			
			$upfile=parent::upload(array('jpg', 'gif', 'png', 'jpeg'),$originalPath,'time',true,$thumbPath,$widthSize,$heightSize,$thumbPrefix);
			$data['original_img']  = $upfile[0]['savepath'].$upfile[0]['savename'];
			$data['thumb_img']     = $thumbPath.$thumbPrefix.$upfile[0]['savename'];
		}else{
			$data['original_img']  = '';
			$data['thumb_img']     = '';
		}
		$M_Goodscat = M("Goodscat");

		$insertId=$M_Goodscat->data($data)->add();

		if($insertId){
			parent::admin_log($_POST['cat_name'],'add','goodscat');
			$this->success('添加成功！！',U('Goodscat/index/',array('action'=>'add','parent_id'=>$data['parent_id'])));
		}else{
			$this->error('添加失败！！',U('Goodscat/index/',array('action'=>'add','parent_id'=>$data['parent_id'])));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 更新产品分类
      +----------------------------------------------------------
     */
	public function update(){
		/* 权限判断 */
		$cat_id   = intval($_POST['cat_id']);
		
		$data['cat_name']   = $_POST['cat_name'];
		$data['cat_type']   = 1;
		$data['cat_desc']   = $_POST['cat_desc'];
		$data['cat_attr']   = $_POST['cat_attr'];
		$data['keywords']   = $_POST['keywords'];
		$data['parent_id']  = intval($_POST['parent_id']);
		$data['sort_order'] = intval($_POST['sort_order']);
		$data['tag_cat']   	= $_POST['tag_cat'];
		if ($data['tag_cat']) {
			$data['tag_cat'] = implode(',', $data['tag_cat']);
		}else{
			$data['tag_cat']='';
		}
		$M_Goodscat = M("Goodscat");
		if(!empty($_FILES['goodscat_img']['tmp_name'])){
			$oldRow=$M_Goodscat->where(array('cat_id'=>$cat_id))->find();
			@unlink($oldRow['thumb_img']);
			@unlink($oldRow['original_img']);
			$thumbPath='Uploads/goodscat/thumb_img/';
			$originalPath='Uploads/goodscat/original_img/';
			$thumbPrefix='goodscat_';
			$widthSize='1153';
			$heightSize='271';

			$upfile=parent::upload(array('jpg', 'gif', 'png', 'jpeg'),$originalPath,'time',true,$thumbPath,$widthSize,$heightSize,$thumbPrefix);
			$data['original_img']  = $upfile[0]['savepath'].$upfile[0]['savename'];
			$data['thumb_img']     = $thumbPath.$thumbPrefix.$upfile[0]['savename'];
		}
		$updateId=$M_Goodscat->where(array('cat_id'=>$cat_id))->save($data);

		if($updateId){
			parent::admin_log($_POST['cat_name'],'edit','goodscat');
			$this->success('修改成功！！',U('Goodscat/index/',array('action'=>'edit','cat_id'=>$cat_id)));
		}else{
			$this->error('修改失败！！',U('Goodscat/index/',array('action'=>'edit','cat_id'=>$cat_id)));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 删除产品分类
      +----------------------------------------------------------
     */
	public function del() {
		/* 权限判断 */
		$M_Goodscat = M("Goodscat");
		$M_Goods    = M("Goods");
		$cat_id       = intval($_GET['cat_id']);
		
		$cat = $M_Goodscat->where("cat_id=".$cat_id)->find();
		$cat_type = $cat['cat_type'];
		if ($cat_type == 2 || $cat_type == 3 || $cat_type ==4){
			/* 系统保留分类，不能删除 */
			$this->error('系统保留分类，不能删除');
		}
		
		$countChildcat=$M_Goodscat->where(array('parent_id'=>$cat_id))->count();
		if ($countChildcat > 0){
			/* 还有子分类，不能删除 */
			$this->error('还有子分类，不能删除');
		}
		
		/* 非空的分类不允许删除 */
		$countGoods=$M_Goods->where(array('cat_id'=>$cat_id))->count();
		if ($countGoods > 0)
		{
			$this->error('非空的分类不允许删除');
		}
		else
		{
			if ($M_Goodscat->where("cat_id=" . $cat_id)->delete()) {
				parent::admin_log($cat['cat_name'],'remove','goodscat');
				$this->success("成功删除");
			} else {
				$this->error("删除失败，可能是不存在该ID的记录");
			}
		}   
    }

}