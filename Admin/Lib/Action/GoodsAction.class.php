<?php

class GoodsAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function index() {
		// 筛选条件及排序
		$filter = array();
        $filter['keyword']    	= empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		$filter['cat_id']     	= empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by']    	= empty($_REQUEST['sort_by']) ? 'a.sort_order' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] 	= empty($_REQUEST['sort_order']) ? 'asc' : trim($_REQUEST['sort_order']);
        $filter['group_id'] 	= empty($_REQUEST['group_id']) ? '' : trim($_REQUEST['group_id']);
		$M_Goods = M("Goods");
		$filter['record_count'] = $count = D("Goods")->listGoodsCount($filter);

        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
		$this->assign("group_id", D("Goods")->getGroupId($filter));
		$this->assign("other_img", $other_img);
		$this->assign("filter", $filter);
        $this->assign("page", $showPage);
        $this->assign("list", D("Goods")->listGoods($page->firstRow, $page->listRows,$filter));
		$this->assign('cat_select',  goods_cat_list(0,$filter['cat_id']));
		
        $this->display();
    }
	
    public function update_type(){
    	$id = $this->_get("id","intval",0);
    	if ($id==0) {
    		$this->error("没有找到该条记录",U("Goods/index"));
    		exit();
    	}
    	$M_Goods = M("Goods");
    	$check = $M_Goods->where("type=1")->select();
    	$info = $M_Goods->where("goods_id=".$id)->find();
    	if (count($check) >=10 && $info["type"]==0) {
    		$this->error("最多推荐10件商品,您已经推荐了10件了.请先取消之前的推荐.",U("Goods/index"));
    		exit();
    	}
    	if ($info["type"]==1) {
    		$data["type"]=0;
    	}else{
    		$data["type"]=1;
    	}
    	$updateId = $M_Goods->where("goods_id=".$id)->save($data);
    	if ($updateId) {
    		$this->success("更新成功",U("Goods/index"));
    	}else{
    		$this->error("推荐失败,请返回重试",U("Goods/index"));
    	}
    	exit();

    }
	
	/**
      +----------------------------------------------------------
     * 添加文章页面
      +----------------------------------------------------------
     */
	public function add(){
		/* 权限判断 */
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		$this->assign("cat_select", goods_cat_list(0,$cat_id));

		//$this->download_list = M('article')->where('cat_id in (11,12)')->select();//下载列表

		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * 修改文章页面
      +----------------------------------------------------------
     */
	public function edit(){
		$goods_id=empty($_GET['id'])?0:intval($_GET['id']);
		$M_Goods = M("Goods");
		$info = $M_Goods->where("goods_id=".$goods_id)->find();

		$this->assign("cat_select", goods_cat_list(0,$info['cat_id']));
		$this->assign("info", $info);


		//$this->download_list = M('article')->where('cat_id in (11,12)')->select();//下载列表
		//$this->related_downloads = M('download')->where('goods_id='.$info['goods_id'])->select();
		$this->goods_excontent_list = M('goods_excontent')->where('goods_id='.$goods_id)->order('sort_order asc,id desc')->select();

		//pre($this->goods_excontent_list);exit;

		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加文章
      +----------------------------------------------------------
     */
	public function insert(){
		$M_Goods = M("Goods");
		$now = time();
		$data['title']       = $this->_post("title","","");
		$data['title_en']= $this->_post("title_en","","");
		$data['cat_id']     = $this->_post("cat_id","intval",0);
		$data['sort_order'] = $this->_post("sort_order","intval",0);
		$data['is_open']    = $this->_post("is_open","intval",1);
		$data['add_time']   = $now;
		$data['publish_time']   = $this->_post("publish_time","intval",$now);

		if(
			empty($data['title']) ||
			empty($data['title_en']) ||
			empty($data['cat_id']) ||
			empty($data['content'])||
			empty($data['content_en'])||
			empty($data['publish_time'])
		){
			$this->error("所有带<font color='red'>*</font>的表单项都是必填的！");
		}
		//产品图片 主页小图
		if($_FILES['goods_img_index']['error']===0){
			$goods_img_index = 'Uploads/goods/'.time().'.'.pathinfo($_FILES['goods_img_index']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['goods_img_index']['tmp_name'], $goods_img_index);
			$data['goods_img_index'] = $goods_img_index;
		}else{
			$this->error("主页小图未上传或上传失败，请重试");
		}
		//产品图片 详情页大图
		if($_FILES['goods_img_detail']['error']===0){
			$goods_img_detail = 'Uploads/goods/'.time().'.'.pathinfo($_FILES['goods_img_detail']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['goods_img_detail']['tmp_name'], $goods_img_detail);
			$data['goods_img_detail'] = $goods_img_detail;
		}else{
			$this->error("详情页大图未上传或上传失败，请重试");
		}

		$data['content']    = stripslashes(htmlspecialchars_decode($_POST['content']));
		$data['content_en']    = stripslashes(htmlspecialchars_decode($_POST['content_en']));
		if(empty($data['sort_order']))$data['sort_order'] = 50;
		$data['add_time'] = time();

		$insertId=$M_Goods->add($data);
		if($insertId){
			parent::admin_log($_POST['title'],'add','goods');

			$this->success('添加成功！！',U('Goods/index'));
		}else{
			$this->error('添加失败！！',U('Goods/add/',array('cat_id'=>$data['cat_id'])));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 更新文章
      +----------------------------------------------------------
     */
	public function update(){
		/* 权限判断 */
		$M_Goods = M("Goods");
		$data = $M_Goods->create();
		if(
			empty($data['title']) || 
			empty($data['cat_id']) || 
			empty($data['content'])
		){
			$this->error("所有带<font color='red'>*</font>的表单项都是必填的！");
		}
		
		$data['content']    = stripslashes(htmlspecialchars_decode($_POST['content']));

		if(empty($data['sort_order']))$data['sort_order'] = 50;
		$data['add_time'] = time();

		$goods_id = $data['goods_id']+0;
		unset($data['goods_id']);

		//$data['related_downloads'] = implode(',',$_POST['related_downloads']);//相关下载

		//产品图片
		if($_FILES['goods_img']['error']===0){
			$old_goods_img = $M_Goods->where(array('goods_id'=>$goods_id))->getField('goods_img');
			@unlink($old_goods_img);
			$goods_img = 'Uploads/goods/'.time().'.'.pathinfo($_FILES['goods_img']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['goods_img']['tmp_name'], $goods_img);
			$data['goods_img'] = $goods_img;
		}
		
		$afterrow=$M_Goods->where(array('goods_id'=>$goods_id))->save($data);

		/*
		//相关下载
		foreach($_FILES['related_downloads']['error'] as $key=>$value){
			if($value===0){
				$filename = 'Uploads/download/'.$_FILES['related_downloads']['name'][$key];
				$file_url = iconv('utf-8','gbk',$filename);
				move_uploaded_file($_FILES['related_downloads']['tmp_name'][$key], $file_url);

				$download_data = array();
				$download_data['file_url'] = $filename;
				$download_data['goods_id'] = $goods_id;
				$download_data['file_desc'] = $_FILES['related_downloads']['name'][$key];
				$download_data['add_time'] = time();
				M('download')->add($download_data);
			}
		}
		*/

		//更新旧的扩展内容
		$old_ex_img_url = $_FILES['old_ex_img_url'];
		$old_ex_title = $_POST['old_ex_title'];
		$old_ex_content = $_POST['old_ex_content'];
		$old_ex_sort_order = $_POST['old_ex_sort_order'];
		$old_ex_id = $_POST['old_ex_id'];
		foreach($old_ex_title as $key=>$value){
			if(empty($value))continue;//标题为空的直接跳过
			if($old_ex_img_url['error'][$key]===0){
				$img_url = 'Uploads/exgoods/'.date('Ymd',time()).'/'.date('His',time()).rand(0,100000).'.'.pathinfo($old_ex_img_url['name'][$key],PATHINFO_EXTENSION);
				@mkdir(dirname($img_url),0777,true);
				move_uploaded_file($old_ex_img_url['tmp_name'][$key], $img_url);
				$update_ex_data['img_url'] = $img_url;
				$img_url = '';
			}
			$update_ex_data['title'] = $value;
			$update_ex_data['content'] = $old_ex_content[$key];
			$update_ex_data['sort_order'] = $old_ex_sort_order[$key];

			M('goods_excontent')->where('id='.$old_ex_id[$key])->save($update_ex_data);
		}

		//插入新的扩展内容
		$ex_img_url = $_FILES['ex_img_url'];
		$ex_title = $_POST['ex_title'];
		$ex_content = $_POST['ex_content'];
		$ex_sort_order = $_POST['ex_sort_order'];
		foreach($ex_title as $key=>$value){
			if(empty($value))continue;//标题为空的直接跳过
			$img_url = '';
			if($ex_img_url['error'][$key]===0){
				$img_url = 'Uploads/exgoods/'.date('Ymd',time()).'/'.date('His',time()).rand(0,100000).'.'.pathinfo($ex_img_url['name'][$key],PATHINFO_EXTENSION);
				@mkdir(dirname($img_url),0777,true);
				move_uploaded_file($ex_img_url['tmp_name'][$key], $img_url);
			}
			$ex_data  = array(
				'goods_id'=>$goods_id,
				'img_url'=>$img_url,
				'title'=>$value,
				'content'=>$ex_content[$key],
				'sort_order'=>$ex_sort_order[$key]
			);

			M('goods_excontent')->add($ex_data);
		}
		
		if($afterrow){
			parent::admin_log($data['title'],'edit','goods');
			$this->success('修改成功！！',U('Goods/index/',array('action'=>'edit','cat_id'=>$data['cat_id'])));
		}else{
			$this->error('修改失败！！',U('Goods/index/',array('action'=>'edit','cat_id'=>$data['cat_id'])));
		}
		exit();
	}

	/**
      +----------------------------------------------------------
     * 删除文章
      +----------------------------------------------------------
     */
	public function del() {
		$M_Goods = M("Goods");
		$goods_id= intval($_GET['goods_id']);
		$row = $M_Goods->where("goods_id=" . $goods_id)->find();

		//删除文章内容图片
		$this->remove_article_img($goods_id);

		if ($M_Goods->where("goods_id=" . $goods_id)->delete()) {
			//删除相册
			$goods_img = M('goodsimg')->where("goods_id=" . $goods_id)->select();
			foreach($goods_img as $v){
				@unlink($v['original_img']);
				@unlink($v['thumb_img']);
			}
			M('goodsimg')->where("goods_id=" . $goods_id)->delete();

			/*
			//删除相关下载
			$download_list = M('download')->where("goods_id=" . $goods_id)->select();
			foreach($download_list as $v){
				@unlink($v['file_url']);
			}
			*/

			//删除扩展内容
			$goods_excontent_list = M('goods_excontent')->where('goods_id='.$goods_id)->select();
			foreach($goods_excontent_list as $goods_excontent){
				@unlink($goods_excontent['img_url']);
				@rmdir(dirname($goods_excontent['img_url']));//如果文件夹为空就删除文件夹
			}
			M('goods_excontent')->where('goods_id='.$goods_id)->delete();


			M('download')->where("goods_id=" . $goods_id)->delete();

			parent::admin_log(addslashes($row['title']),'remove','goods');
			$this->success("成功删除");
		} else {
			$this->error("删除失败，可能是不存在该ID的记录");
		} 
    }
	
	
	/**
      +----------------------------------------------------------
     * 删除文章
      +----------------------------------------------------------
     */

	function batch(){
		$M_Goods = M("Goods");
		/* 批量删除 */
		if (isset($_POST['type']))
		{
			if ($_POST['type'] == 'button_remove')
			{
				if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
				{
					$this->error('请选择至少一条记录！');
				}
				$M_Goods->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->delete();
				//删除相册
				$goods_img = M('goodsimg')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->select();
				foreach($goods_img as $v){
					@unlink($v['original_img']);
					@unlink($v['thumb_img']);
				}
				M('goodsimg')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->delete();

				/*
				//删除相关下载
				$download_list = M('download')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->select();
				foreach($download_list as $v){
					@unlink($v['file_url']);
				}
				*/

				//删除扩展内容
				$goods_excontent_list = M('goods_excontent')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->select();
				foreach($goods_excontent_list as $goods_excontent){
					@unlink($goods_excontent['img_url']);
					@rmdir(dirname($goods_excontent['img_url']));//如果文件夹为空就删除文件夹
				}
				M('goods_excontent')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->delete();

				//删除文章内容图片
				$this->remove_article_img(implode(',', $_POST['checkboxes']));
				M('download')->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->delete();
			}

			/* 批量隐藏 */
			if ($_POST['type'] == 'button_hide')
			{
				if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
				{
					$this->error('请选择至少一条记录！');
				}

				foreach ($_POST['checkboxes'] AS $key => $id)
				{
				  $M_Goods->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->save(array('is_open'=>0));
				}
			}

			/* 批量显示 */
			if ($_POST['type'] == 'button_show')
			{
				if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
				{
					$this->error('请选择至少一条记录！');
				}

				foreach ($_POST['checkboxes'] AS $key => $id)
				{
				  $M_Goods->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->save(array('is_open'=>1));
				}
			}

			/* 批量移动分类 */
			if ($_POST['type'] == 'move_to')
			{
				if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']) )
				{
					$this->error('请选择至少一条记录！');
				}

				if(!$_POST['target_cat'])
				{
					$this->error('请选择要转移的分类！');
				}
				
				foreach ($_POST['checkboxes'] AS $key => $id)
				{
				  $M_Goods->where('goods_id '.db_create_in(join(',', $_POST['checkboxes'])))->save(array('cat_id'=>$_POST['target_cat']));
				}
			}
		}

		/* 清除缓存 */
		$this->success('批量操作成功！');
	}

	//产品相册
	public function addGoodsimg(){
		$goods_id = $_GET['id']+0;
		$this->detail = M('goods')->where('goods_id='.$goods_id)->find();
		$this->goodsimg_list = M('goodsimg')->where('goods_id='.$goods_id)->order('sort_order asc,id desc')->select();
		$this->display('goodsimg');
	}

	
	public function updateGoodsimg(){
		$goods_id = $_POST['id']+0;

		$new_img_sort = $_POST['new_img_sort'];
		$old_img_id	  = $_POST['old_img_id'];
		foreach($new_img_sort as $k=>$v){
			M('goodsimg')->where('id='.$old_img_id[$k])->save(array('sort_order'=>$v));
		}

		$ori_img = $_POST['ori_img'];
		$thumb_img = $_POST['thumb_img'];
		$sort_id = $_POST['img_sort'];

		foreach($ori_img as $k=>$v){
			$imgdata = array(
				'original_img' => $v,
				'thumb_img' => $thumb_img[$k],
				'goods_id' => $goods_id,
				'sort_order'=> $sort_id[$k]
			);
			M('goodsimg')->add($imgdata);
		}
		$this->success('操作成功！');
	}

	public function delimg(){
		$id = $_GET['id']+0;
		$oldrow = M('goodsimg')->where('id='.$id)->find();
		M('goodsimg')->where('id='.$id)->delete();
		@unlink($oldrow['original_img']);
		@unlink($oldrow['thumb_img']);
	}

	public function delimg2(){
		$ori_img = $_GET['ori_img'];
		$thumb_img = $_GET['thumb_img'];
		@unlink($ori_img);
		@unlink($thumb_img);
	}


	public function del_download(){
		$id = $_GET['id'] + 0;
		$file_url = M('download')->where(array('id'=>$id))->getField('file_url');
		if(M('download')->where(array('id'=>$id))->delete()){
			@unlink($file_url);
			echo 1;
		}
	}


	public function del_ex_content(){
		$ex_id = $_GET['id'] + 0;
		$img_url = M('goods_excontent')->where(array('id'=>$ex_id))->getField('img_url');
		if(M('goods_excontent')->where(array('id'=>$ex_id))->delete()){
			@unlink($img_url);
			@rmdir(dirname($img_url));//如果文件夹为空，也把文件夹给删除了
			echo 1;
		}else{
			echo '网络错误！';
		}
	}



	/**
     * 删除文章时，把文章内容图片也删除（也就是删除编辑器上传的图片）
     * @return void
     * @author create by AIJ(tujia)
     */
     protected function remove_article_img($ids){
        //匹配并删除图片
        $imgreg = "/<img.*src=\"([^\"]+)\"/U";
        $images = M('goods')->field('content,content1,content2')->where("goods_id in ($ids)")->select();

        foreach($images as $detail){
        	$content = $detail['content'] . $detail['content1'] . $detail['content2'];
	        $matches = array();
	        preg_match_all($imgreg, $content, $matches);

	        foreach($matches[1] as $img_url){
	            if(strpos($img_url, 'Public/static/')===false){
	            	$web_root = 'http://' . $_SERVER['HTTP_HOST'] . '/';
	            	$filepath = str_replace($web_root,'',$img_url);
	            	if($filepath == $img_url) $filepath = substr($img_url, 1);
	                @unlink($filepath);
	                $filedir  = dirname($filepath);
	                $files = scandir($filedir);
	                if(count($files)<=2)@rmdir($filedir);//如果只剩下./和../,就删除文件夹
	            }
	        }
        	unset($matches);
        }
     }
}