<?php

class ArticlecatAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}
	/**
      +----------------------------------------------------------
     * 文章分类列表
      +----------------------------------------------------------
     */
    public function index($pid=0) {
		$this->assign("action",$_GET['action']);
		$this->assign("parent_id",$_GET['parent_id']);
		$cat_id = $_GET['cat_id']+0;

		if($pid==13) $cat_id=12;
		$this->assign("cat_id",$cat_id);
		
		$articlecat=D("Articlecat")->listArticlecat($pid);

		/*
		$action_list = $_SESSION['action_list'];
		$action_list_arr = explode(',', $action_list);
		$column = array();
		foreach($action_list_arr as $key=>$value){
			if(strpos($value, 'column_') !== false){
				$column[] = substr($value, 7);
			}
		}

		foreach ($articlecat as $key => $value) {
			if(!in_array($key, $column)){
				unset($articlecat[$key]);
			}
		}
		*/
		
		$html='';
		foreach($articlecat as $key=>$value){
			$html.='<li class="closed"><a href="'.U('Articlecat/edit',array('cat_id'=>$value['id'])).'" target="container"><span class="folder">'.$value['name'].'</span></a>';
				if($value['cat_id']){
					$html.='<ul>';
					foreach($value['cat_id'] as $key2=>$value2){
						$html.='<li class="closed"><a href="'.U('Articlecat/edit',array('cat_id'=>$value2['id'])).'" target="container"><span class="folder">'.$value2['name'].'</span></a>';
							if($value2['cat_id']){
							$html.='<ul>';
								foreach($value2['cat_id'] as $key3=>$value3){
									$html.='<li class="closed"><a href="'.U('Articlecat/edit',array('cat_id'=>$value3['id'])).'" target="container"><span class="folder">'.$value3['name'].'</span></a>';
										if($value3['cat_id']){
											$html.='<ul>';
											foreach($value3['cat_id'] as $key4=>$value4){
												$html.='<li class="closed"><a href="'.U('Articlecat/edit',array('cat_id'=>$value4['id'])).'" target="container"><span class="folder">'.$value4['name'].'</span></a></li>';
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
		// print_r($html);
		$this->assign("articlecat",$articlecat);
		$this->display();
    }



    public function index2($pid=0){
    	$this->assign("action",$_GET['action']);
		$this->assign("parent_id",$_GET['parent_id']);
		$cat_id = $_GET['cat_id']+0;
		$this->assign("cat_id",$cat_id);
		
		$articlecat=D("Articlecat")->listArticlecat($pid);
		/*
		$action_list = $_SESSION['action_list'];
		$action_list_arr = explode(',', $action_list);
		$column = array();
		foreach($action_list_arr as $key=>$value){
			if(strpos($value, 'article_') !== false){
				$column[] = substr($value, 8);
			}
		}
		
		foreach ($articlecat as $key => $value) {
			if(!in_array($key, $column)){
				unset($articlecat[$key]);
			}
		}
		*/
		
		$html='';
		foreach($articlecat as $key=>$value){
			$html.='<li class="closed"><a href="'.U('Article/index',array('cat_id'=>$value['id'])).'" target="container"><span class="folder">'.$value['name'].'</span></a>';
				if($value['cat_id']){
					$html.='<ul>';
					foreach($value['cat_id'] as $key2=>$value2){
						$html.='<li class="closed"><a href="'.U('Article/index',array('cat_id'=>$value2['id'])).'" target="container"><span class="folder">'.$value2['name'].'</span></a>';
							if($value2['cat_id']){
							$html.='<ul>';
								foreach($value2['cat_id'] as $key3=>$value3){
									$html.='<li class="closed"><a href="'.U('Article/index',array('cat_id'=>$value3['id'])).'" target="container"><span class="folder">'.$value3['name'].'</span></a>';
										if($value3['cat_id']){
											$html.='<ul>';
											foreach($value3['cat_id'] as $key4=>$value4){
												$html.='<li class="closed"><a href="'.U('Article/index',array('cat_id'=>$value4['id'])).'" target="container"><span class="folder">'.$value4['name'].'</span></a></li>';
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
		$this->assign("articlecat",$articlecat);
		$this->display();
    }

    public function index3($cat_id){
    	$this->list = M('articlecat')->field('cat_id,cat_name,sort_order')->where("parent_id=$cat_id")->order('sort_order asc,cat_id desc')->select();
    	//print_r(M('articlecat'));
    	$this->display();
    }
	
	/**
      +----------------------------------------------------------
     * ajax载入添加文章分类页面
      +----------------------------------------------------------
     */
	public function add(){
		/* 权限判断 */
	
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);
		
		$this->assign("cat_select", article_cat_list(0,$cat_id));
		$this->display();
	}
	
	
	/**
      +----------------------------------------------------------
     * ajax载入修改文章分类页面
      +----------------------------------------------------------
     */
	public function edit(){
		/* 权限判断 */
		$cat_id=empty($_GET['cat_id'])?0:intval($_GET['cat_id']);$M_Articlecat = M("Articlecat");

		$cat = $M_Articlecat->where("cat_id=".$cat_id)->find();
		$this->assign("cat", $cat);
		$this->assign("cat_id", $cat_id);
		
		$this->assign("cat_select", article_cat_list(0,$cat['parent_id']));

		$this->display();
	}
	
	/**
      +----------------------------------------------------------
     * 添加文章分类
      +----------------------------------------------------------
     */
	public function insert(){
		/* 权限判断 */
		$data = M('articlecat')->create();

		//分类图片
		if($_FILES['cat_img']['error']===0){
			$cat_img = "Uploads/articlecat/".date('YmdHis').'.'.pathinfo($_FILES['cat_img']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['cat_img']['tmp_name'], $cat_img);
			$data['cat_img'] = $cat_img; 
		}
		
		$M_Articlecat = M("Articlecat");
			
		$insertId=$M_Articlecat->data($data)->add();

		if($insertId){
			parent::admin_log($_POST['cat_name'],'add','articlecat');
			$this->success('添加成功！！',U('Articlecat/add',array('cat_id'=>$data['parent_id'])));
		}else{
			$this->error('添加失败！！');
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 更新文章分类
      +----------------------------------------------------------
     */
	public function update(){
		/* 权限判断 */
		$cat_id   = intval($_POST['cat_id']);
		
		$data = M('articlecat')->create();
		unset($data['cat_id']);

		//分类图片
		if($_FILES['cat_img']['error']===0){
			$cat_img = "Uploads/articlecat/".date('YmdHis').'.'.pathinfo($_FILES['cat_img']['name'],PATHINFO_EXTENSION);
			move_uploaded_file($_FILES['cat_img']['tmp_name'], $cat_img);
			$data['cat_img'] = $cat_img; 
		}
		
		$M_Articlecat = M("Articlecat");
			
		$updateId=$M_Articlecat->where(array('cat_id'=>$cat_id))->save($data);
		
		if($updateId){
			parent::admin_log($_POST['cat_name'],'edit','articlecat');
			$this->success('修改成功！！',U('Articlecat/edit',array('cat_id'=>$cat_id)));
		}else{
			$this->error('修改失败！！',U('Articlecat/edit',array('cat_id'=>$cat_id)));
		}
		exit();
	}
	/**
      +----------------------------------------------------------
     * 删除文章分类
      +----------------------------------------------------------
     */
	public function del() {
		/* 权限判断 */
		$M_Articlecat = M("Articlecat");
		$M_Article    = M("Article");
		$cat_id       = intval($_GET['cat_id']);
		
		$cat = $M_Articlecat->where("cat_id=".$cat_id)->find();
		$cat_type = $cat['cat_type'];
		if ($cat_type == 2 || $cat_type == 3 || $cat_type ==4){
			/* 系统保留分类，不能删除 */
			$this->error('系统保留分类，不能删除');
		}
		
		$countChildcat=$M_Articlecat->where(array('parent_id'=>$cat_id))->count();
		if ($countChildcat > 0){
			/* 还有子分类，不能删除 */
			$this->error('还有子分类，不能删除');
		}
		
		/* 非空的分类不允许删除 */
		$countArticle=$M_Article->where(array('cat_id'=>$cat_id))->count();
		if ($countArticle > 0)
		{
			$this->error('非空的分类不允许删除');
		}
		else
		{
			if ($M_Articlecat->where("cat_id=" . $cat_id)->delete()) {
				parent::admin_log($cat['cat_name'],'remove','articlecat');
				$this->success("成功删除");
			} else {
				$this->error("删除失败，可能是不存在该ID的记录");
			}
		}   
    }


	//校园生活（批量上传图片）
    public function batchimg($cat_id=0){
    	$this->cat_id = $cat_id;
    	$this->cat_list = $this->subCat(46);
    	$this->display();
    }

    public function insertSchoolImg(){
		$cat_id = $_POST['cat_id']+0;

		$ori_img = $_POST['ori_img'];
		$sort_id = $_POST['img_sort'];
		$title = $_POST['title'];

		foreach($ori_img as $k=>$v){
			$data = array(
				'cat_id'=>$cat_id,
				'title'=>$title[$k],
				'content' => "<img src='".__ROOT__.'/'.$v."'/>",
				'original_img'=>$v,
				'sort_order'=> $sort_id[$k]
			);
			M('article')->add($data);
		}
		$this->success('操作成功！');
	}

	public function delimg2(){
		$ori_img = $_GET['ori_img'];
		$thumb_img = $_GET['thumb_img'];
		@unlink($ori_img);
		@unlink($thumb_img);
	}
}