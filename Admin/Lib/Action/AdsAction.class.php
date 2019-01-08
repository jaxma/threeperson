<?php

class AdsAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}
	/**
      +----------------------------------------------------------
     * 广告图列表
      +----------------------------------------------------------
     */
    public function index() {
		
		$cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);

		if($cat_id==13) $cat_id=10;
		if($cat_id==14) $cat_id=11;

		$M_Ads  = M('Ads');
		$adsList = $M_Ads->where(array('cat_id'=>$cat_id))->order('sort_order asc')->select();
		$counts = count($adsList);
		$this->assign("counts",$counts);
		$this->assign("cat_id",$cat_id);
		$this->assign("adsList",$adsList);
        $this->display();
    }
	

	
	/**
      +----------------------------------------------------------
     * 添加广告图页面
      +----------------------------------------------------------
     */
	public function add(){
		/* 权限判断 */
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		$this->assign("cat_id",$cat_id);
		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * 修改广告图页面
      +----------------------------------------------------------
     */
	public function edit(){
		/* 权限判断 */
		$ads_id=empty($_GET['id'])?0:intval($_GET['id']);
		$M_Ads = M("Ads");
		$info = $M_Ads->where("ads_id=".$ads_id)->find();
		
		$this->assign("cat_id", $info['cat_id']);
		$this->assign("info", $info);
		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加广告图
      +----------------------------------------------------------
     */
	public function insert(){
		/* 权限判断 */
		
		$M_Ads = M("Ads");
		
		$data['link']       = $this->_post("link","","");
		$data['description']= $this->_post("description","","");
		$data['title']		= $this->_post("title","","");
		$data['cat_id']     = $this->_post("cat_id","intval",0);
		$data['sort_order'] = $this->_post("sort_order","intval",0);
		$data['is_open']    = $this->_post("is_open","intval",1);
		$data['add_time']   = time();
		$data['img_size']   = $this->_post('img_size','trim','');
		
		
		if(!empty($_FILES['ads_img']['tmp_name'])){
			$thumbPath='Uploads/Banner/thumb_img/';
			$originalPath='Uploads/Banner/original_img/';
			$thumbPrefix='ads_';
			$upfile=parent::upload(array('jpg', 'gif', 'png', 'jpeg'),$originalPath,'time',false);
			$data['original_img']  = $upfile[0]['savepath'].$upfile[0]['savename'];
			$data['thumb_img']     = $thumbPath.$thumbPrefix.$upfile[0]['savename'];
		}
		
		
		$insertId=$M_Ads->data($data)->add();
		if($insertId){
			parent::admin_log($_POST['description'],'add','ads');
			$this->success('添加成功！！',U('Ads/add/',array('cat_id'=>$data['cat_id'])));
		}else{
			$this->error('添加失败！！',U('Ads/add/',array('cat_id'=>$data['cat_id'])));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 更新广告图
      +----------------------------------------------------------
     */
	public function update(){
		/* 权限判断 */
		
		$M_Ads = M("Ads");
		$ads_id         	= $this->_post("ads_id","intval",0);
		$data['link']       = $this->_post("link","","");
		$data['description']= $this->_post("description","","");
		$data['title']		= $this->_post("title","","");
		$data['cat_id']     = $this->_post("cat_id","intval",0);
		$data['sort_order'] = $this->_post("sort_order","intval",0);
		$data['is_open']    = $this->_post("is_open","intval",1);
		$data['img_size']   = $this->_post('img_size','trim','');
		//print_r($data);exit;
		if(!empty($_FILES['ads_img']['tmp_name'])){
			$thumbPath='Uploads/Banner/thumb_img/';
			$originalPath='Uploads/Banner/original_img/';
			$upfile=parent::upload(array('jpg', 'gif', 'png', 'jpeg'),$originalPath,'time',false);
			$data['original_img']  = $upfile[0]['savepath'].$upfile[0]['savename'];
			$data['thumb_img']     = $thumbPath.$thumbPrefix.$upfile[0]['savename'];
			
			/* 删除旧图片 */
			$oldRow=$M_Ads->where(array('ads_id'=>$ads_id))->find();
			if ($oldRow['thumb_img'] != ''){
				@unlink("./".$oldRow['thumb_img']);
				@unlink("./".$oldRow['original_img']);
			}
		}
		
		
		$updateId=$M_Ads->where(array('ads_id'=>$ads_id))->save($data);
		
		if($updateId){
			parent::admin_log($_POST['description'],'edit','ads');
			$this->success('修改成功！！',U('Ads/index/',array('cat_id'=>$_POST['cat_id'])));
		}else{
			$this->error('修改失败！！',U('Ads/index/',array('cat_id'=>$_POST['cat_id'])));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 删除广告图
      +----------------------------------------------------------
     */
	public function del() {
		/* 权限判断 */
		$M_Ads = M("Ads");
		$ads_id = $_REQUEST['ads_id']+0;
		$oldRow = $M_Ads->where("ads_id=" . $ads_id)->find();
		if ($M_Ads->where("ads_id=" . $ads_id)->delete()) {
			parent::admin_log(addslashes($oldRow['description']),'remove','ads');
			/* 删除旧图片 */
			@unlink($oldRow['thumb_img']);
			@unlink($oldRow['original_img']);
			$this->success("成功删除");
		} else {
			$this->error("删除失败，可能是不存在该ID的记录");
		} 
    }

}