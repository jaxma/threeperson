<?php

class ArticleAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}
	/**
      +----------------------------------------------------------
     * 文章列表
      +----------------------------------------------------------
     */
    public function index($cat_id=0) {
    	if($cat_id==2){//培训课程
    		$this->redirect('Goods/index',array('cat_id'=>1));exit;
    	}

		// 筛选条件及排序
		$filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		$filter['cat_id']     = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
		if($filter['cat_id']==0){
	        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.article_id' : trim($_REQUEST['sort_by']);
	        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		}else{
	        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.sort_order' : trim($_REQUEST['sort_by']);
	        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'asc' : trim($_REQUEST['sort_order']);
		}
		
		$M_Article = M("Article");
		$filter['record_count'] = $count = D("Article")->listArticleCount($filter);

        import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
		
		$this->assign("filter", $filter);
        $this->assign("page", $showPage);
        $this->assign("list", D("Article")->listArticle($page->firstRow, $page->listRows,$filter));

		$this->assign('cat_select',  article_cat_list(0,$filter['cat_id']));
        $this->display();
    }

	
	/**
      +----------------------------------------------------------
     * 添加文章页面
      +----------------------------------------------------------
     */
	public function add(){
		/* 权限判断 */
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		$this->assign('cat_id', $cat_id);

		$this->parent_id = M('articlecat')->where("cat_id=$cat_id")->getField('parent_id');
		$this->attr_list = $attr_list;

		$this->assign("cat_select", article_cat_list(0,$cat_id));

		if($cat_id==3){
			//名师答疑
			$this->display('faq_add');exit;
		}else if($cat_id==5){
			//名师答疑
			$this->display('student_add');exit;
		}

		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * 修改文章页面
      +----------------------------------------------------------
     */
	public function edit(){
		/* 权限判断 */
		//print_r($_POST);exit;
		$article_id 	= empty($_GET['id'])?0:intval($_GET['id']);
		$M_Article 		= M("Article");
		$info 			= $M_Article->find($article_id);

		$this->parent_id = M('articlecat')->where("cat_id=".$info['cat_id'])->getField('parent_id');

		$this->assign("cat_select", article_cat_list(0,$info['cat_id']));
		$this->assign("info", 	$info);
		$this->assign('cat_id', $info['cat_id']);

		$this->album_list = M('album')->where("type='article' and id_value=$article_id")->order('sort_order asc')->select();

		if($info['cat_id']==3){
			//名师答疑
			$this->display('faq_edit');exit;
		}else if($info['cat_id']==5){
			//名师答疑
			$this->display('student_edit');exit;
		}
		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加文章
      +----------------------------------------------------------
     */
	public function insert(){
		/* 权限判断 */
		$data = M('article')->create();

		$data['content']    = stripslashes(htmlspecialchars_decode($_POST['content']));
		$data['add_time']   = $_POST['add_time']? strtotime($_POST['add_time']) : time();
		$data['attr']		= serialize($data['attr']);//属性
		
		if($_FILES['article_img']['error']===0){
			$originalPath='Uploads/article/original_img/'.time().'.'.pathinfo($_FILES['article_img']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['article_img']['tmp_name'], $originalPath);
			$data['original_img']  = $originalPath;
		}


		if($_FILES['file_url']['error']===0){
			$filename = 'Uploads/download/'.$_FILES['file_url']['name'];
			$file_url = iconv('utf-8','gbk',$filename);
			move_uploaded_file($_FILES['file_url']['tmp_name'], $file_url);
			$data['file_url'] = $filename;
		}

		$M_Article = M("Article");
		$insertId=$M_Article->data($data)->add();

		if($insertId){
			//添加相册
	        if(is_array($_POST['ori_img'])){
	            foreach($_POST['ori_img'] as $key=>$value){
	                $album = array();
	                $album['original_img']  = $value;
	                $album['thumb_img']     = $_POST['thumb_img'][$key];
	                $album['sort_order']    = $_POST['img_sort'][$key];
	                $album['description']   = $_POST['img_description'][$key];
	                $album['id_value']      = $insertId;
	                $album['type']     		= 'article';

	                M('album')->add($album);
	            }
	        }

			$this->success('添加成功！！',U('Article/add/',array('cat_id'=>$data['cat_id'])));
		}else{
			$this->error('添加失败！！',U('Article/add/',array('cat_id'=>$data['cat_id'])));
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
		
		$M_Article = M("Article");
	
		$article_id         = intval($_POST['id']);
		
		$data = M('article')->create();

		$data['content']    = stripslashes(htmlspecialchars_decode($_POST['content']));
		$_POST['add_time']? $data['add_time'] = strtotime($_POST['add_time']) : '';
		$data['attr']		= serialize($data['attr']);//属性
			
		$oldRow=$M_Article->where(array('article_id'=>$article_id))->find();
		if(!empty($_FILES['article_img']['tmp_name'])){
			@unlink($oldRow['thumb_img']);
			@unlink($oldRow['original_img']);
			$originalPath='Uploads/article/original_img/'.time().'.'.pathinfo($_FILES['article_img']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['article_img']['tmp_name'], $originalPath);
			$data['original_img']  = $originalPath;
		}
		if($_FILES['file_url']['error']==0){
			$filename = 'Uploads/download/'.$_FILES['file_url']['name'];
			$file_url = iconv('utf-8','gbk',$filename);
			move_uploaded_file($_FILES['file_url']['tmp_name'], $file_url);
			$data['file_url'] = $filename;

			@unlink($oldRow['file_url']);
		}


		//更新相册
        $new_img_sort = $_POST['new_img_sort'];
        if(is_array($new_img_sort)){
            $old_img_id   = $_POST['old_img_id'];
            foreach($new_img_sort as $k=>$v){
                $data = array();
                $data['sort_order']    = $v;
                $data['description']   = $_POST['new_img_description'][$k];
                M('album')->where('id='.$old_img_id[$k])->save($data);
            }
        }

        //添加相册
        if(is_array($_POST['ori_img'])){
            foreach($_POST['ori_img'] as $key=>$value){
                $album = array();
                $album['original_img']  = $value;
                $album['thumb_img']     = $_POST['thumb_img'][$key];
                $album['sort_order']    = $_POST['img_sort'][$key];
                $album['description']   = $_POST['img_description'][$key];
                $album['id_value']      = $article_id;
                $album['type']     		= 'article';

                M('album')->add($album);
            }
        }
		
		$M_Article->where(array('article_id'=>$article_id))->save($data);
		
		$this->success('修改成功！！');
	}
	/**
      +----------------------------------------------------------
     * 删除文章
      +----------------------------------------------------------
     */
	public function del() {
		$M_Article = M("Article");
		$article_id= intval($_GET['article_id']);
		$row = $M_Article->where("article_id=" . $article_id)->find();

		//删除文章内容图片
		if ($M_Article->where("article_id=" . $article_id)->delete()) {
			parent::admin_log(addslashes($row['title']),'remove','article');
			@unlink($row['original_img']);
			@unlink($row['thumb_img']);
			@unlink($row['file_url']);

			remove_content_img($row['content']);
			$this->success("成功删除");
		} else {
			$this->error("删除失败，可能是不存在该ID的记录");
		} 
    }

    /**
      +----------------------------------------------------------
     * 删除发布的信息
      +----------------------------------------------------------
     */
	public function f_del($id) {
	   $row     = M('formdata')->find($id);
       $content = $row['content0'] . $row['content1'] . $row['content2'] . $row['content3'] . $row['content4'];
       
       //删除内容图片
       remove_content_img($content); 

       //删除相册
       $album_list   = M('album')->where('id_value='.$row['id'])->select();
       foreach($album_list as $key=>$value){
            @unlink($value['original_img']);
            @unlink($value['thumb_img']);
       }

       //删除属性
       M('form_attr')->where("fid=".$row['id'])->delete();

       //最后删除记录
       M('formdata')->where("id=$id")->delete();
	

	   $this->success('批量操作成功！');
    }
	
	
	/**
      +----------------------------------------------------------
     * 文章批量操作
      +----------------------------------------------------------
     */

	function batch(){
		$M_Article = M("Article");
		/* 批量删除 */
		if (isset($_POST['type']))
		{
			$in_article_ids = 'article_id '.db_create_in(join(',', $_POST['checkboxes']));

			if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
			{
				$this->error('请选择文章！');exit;
			}

			if ($_POST['type'] == 'button_remove')
			{
				$article_list = M('article')->where($in_article_ids)->select();

				M('article')->where($in_article_ids)->delete();

				foreach($article_list as $value){
					@unlink($value['original_img']);
					@unlink($value['thumb_img']);
					@unlink($value['file_url']);
					remove_content_img($value['content']);
				}
			}

			/* 批量隐藏 */
			if ($_POST['type'] == 'button_hide')
			{
				 $M_Article->where($in_article_ids)->save(array('is_open'=>0));
			}

			/* 批量显示 */
			if ($_POST['type'] == 'button_show')
			{
				$M_Article->where($in_article_ids)->save(array('is_open'=>1));
			}

			//批量推荐
			if($_POST['type'] == 'button_recommend_yes'){
				M('article')->where($in_article_ids)->save(array('is_recommend'=>1));
			}

			//取消推荐
			if($_POST['type'] == 'button_recommend_no'){
				M('article')->where($in_article_ids)->save(array('is_recommend'=>0));
			}

			/* 批量移动分类 */
			if ($_POST['type'] == 'move_to')
			{
				if(!$_POST['target_cat'])
				{
					$this->error('请选择要转移的分类！');
				}
				
				foreach ($_POST['checkboxes'] AS $key => $id)
				{
				  $M_Article->where($in_article_ids)->save(array('cat_id'=>$_POST['target_cat']));
				}
			}
		}

		/* 清除缓存 */
		$this->success('批量操作成功！');
	}


	/**
	 * 信息批量操作
	 */
	 function f_batch(){
		/* 批量删除 */
		if (isset($_POST['type']))
		{
			$ids = "id in (". implode(',', $_POST['checkboxes']) .")";

			if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
			{
				$this->error('请选择至少一条记录！');exit;
			}

			if ($_POST['type'] == 'button_remove')
			{
				$list = M('formdata')->where($ids)->select();

				foreach($list as $value){
					@unlink($value['original_img']);
					@unlink($value['thumb_img']);
					remove_content_img($value['content0'].$value['content1'].$value['content2'].$value['content3'].$value['content4']);
				}


				//删除相册
				$album_list   = M('album')->where("id_value in ($ids)")->select();
				M('album')->where("id_value in ($ids)")->delete();
				foreach($album_list as $key=>$value){
					@unlink($value['original_img']);
					@unlink($value['thumb_img']);
				}

				//删除属性
				M('form_attr')->where("fid in ($ids)")->delete();

				M('formdata')->where($ids)->delete();
			}

			/* 批量审核通过 */
			if ($_POST['type'] == 'button_pass_yes')
			{
				 M('formdata')->where($ids)->save(array('state'=>3));
			}

			/* 批量审核不通过 */
			if ($_POST['type'] == 'button_pass_no')
			{
				M('formdata')->where($ids)->save(array('state'=>1));
			}

			//批量推广
			if($_POST['type'] == 'button_recommend_yes'){
				M('formdata')->where($ids)->save(array('is_recommend'=>2,'recommend_time'=>time()));
			}

			//取消推广
			if($_POST['type'] == 'button_recommend_no'){
				M('formdata')->where($ids)->save(array('is_recommend'=>0));
			}
		}

		/* 清除缓存 */
		$this->success('批量操作成功！');
	}
}