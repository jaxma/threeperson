<?php

class CommonAction extends Action {

    public $loginMarked;

    /**
      +----------------------------------------------------------
     * 初始化
     * 如果 继承本类的类自身也需要初始化那么需要在使用本继承类的类里使用parent::_initialize();
      +----------------------------------------------------------
     */
    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
		    $this->logined();
        
        $systemConfig = include WEB_ROOT . 'Common/systemConfig.php';
		if (empty($systemConfig['TOKEN']['admin_marked'])) {
            $systemConfig['TOKEN']['admin_marked'] = "cyx计算机科技";
            $systemConfig['TOKEN']['admin_timeout'] = 3600;
            $systemConfig['TOKEN']['member_marked'] = "http://www.tp.com";
            $systemConfig['TOKEN']['member_timeout'] = 3600;
            F("systemConfig", $systemConfig, WEB_ROOT . "Common/");
        }
        
		    $this->assign("site", $systemConfig);
    }
	
	/**
      +----------------------------------------------------------
     * 判断管理员对某一个操作是否有权限。
      +----------------------------------------------------------
     */
	public function admin_priv($priv_str, $msg_type = '' , $msg_output = true){
		
		if ($_SESSION['action_list'] == 'all'){
			return true;
		}

		if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false){
			$this->error('您没有权限进行此操作！');
		}else{
			return true;
		}
	}
	
	/**
      +----------------------------------------------------------
     * 记录管理员的操作内容
      +----------------------------------------------------------
     */
	function admin_log($sn = '', $action, $content)
	{
		$M_admin_log = M('admin_log');
		require('./Admin/Lang/' .C('DEFAULT_LANG'). '/log_action.php');
		$log_info = $_LANG['log_action'][$action] . $_LANG['log_action'][$content] .': '. addslashes($sn);

		$data['log_time']=time();
		$data['user_id'] =$_SESSION['admin_id'];
		$data['log_info']=stripslashes($log_info);
		$data['ip_address']=real_ip();
		$M_admin_log->add($data);
	}
	/**
      +----------------------------------------------------------
     * 验证登陆
      +----------------------------------------------------------
     */
	public function logined(){
		if(empty($_SESSION['admin_id'])){
			// $this->error('您的登录信息已过期或者还未登录！',U('Public/index'));
			$this->redirect('Public/index');
		}
	}
	/**
      +----------------------------------------------------------
     * 图片上传
      +----------------------------------------------------------
     */
	function upload($allowExts=array('jpg', 'gif', 'png', 'jpeg'),$savePath,$saveRule,$thumb=true,$thumbPath,$thumbMaxWidth,$thumbMaxHeight,$thumbPrefix){ 
		
		$message = A("Commonfunction");
		import("ORG.Util.UploadFile"); 
		import("ORG.Util.File");
		$upload = new UploadFile(); // 实例化上传类 
		if(!is_dir($savePath)){
			File::make_dir('./'.$savePath);
		}
		$smPath = explode(',',$thumbPath);
		foreach($smPath as $key => $value){
			if(!is_dir($value)){
				File::make_dir('./'.$value);
			}
		}
		
		$upload->maxSize = 3145728 ; // 设置附件上传大小 
		$upload->allowExts = $allowExts; // 设置附件上传类型 
		$upload->savePath = $savePath; // 设置附件上传目录 
		$upload->saveRule = $saveRule; // 上传文件的保存规则 
		$upload->thumb = true; // 是否需要对图片文件进行缩略图处理，默认为false  		
		$upload->thumbPath = $thumbPath;  //缩略图的保存路径，留空的话取文件上传目录本身
		$upload->thumbMaxWidth=$thumbMaxWidth;   //缩略图的最大宽度，多个使用逗号分隔
		$upload->thumbMaxHeight=$thumbMaxHeight;   //缩略图的最大高度，多个使用逗号分隔
		$upload->thumbPrefix=$thumbPrefix;   //缩略图的文件前缀，默认为thumb_  （如果你设置了多个缩略图大小的话，请在此设置多个前缀）
		// $upload->autoSub=true;   //是否使用子目录保存上传文件
		// $upload->subType='date'; 
		
		
		
		if(!$upload->upload()) { // 上传错误 提示错误信息 
			$this->error($upload->getErrorMsg());
		}else{ // 上传成功 获取上传文件信息 
			$info = $upload->getUploadFileInfo(); 
		}
		
		return $info;
	}

    
    //家谱树
    public function supCat($cat_id,$includeself=true,$model = 'articlecat'){
        $tree = array();
        $url = array();
        while($cat_id){
            $cat = M($model)->where(array('cat_id'=>$cat_id))->find();
            $tree[] = $cat;
            $cat_id = $cat['parent_id'];
        }
        if(!$includeself)array_shift($tree);
        return array_reverse($tree);
    }

    //子孙树
    public function subCat($cat_id,$model='articlecat'){
        $cat_list = M($model)->where(array('parent_id'=>$cat_id))->select();
        $tree=array();
        foreach($cat_list as $cat){
            if($cat['parent_id'] == $cat_id){
                $cat['sub_cat'] = $this->subCat($cat['cat_id'],$model);
                $tree[]=$cat;
            }
        }
        return $tree;
    }

    //查找子孙树2
    public function subCat2($model='articlecat',$list,$cat_id=0,$includeself=false,$level=0){
        $level++;
        $tree = array();
        foreach($list as $key=>$value){
            if($value['parent_id']==$cat_id){
                $value['level'] = $level;
                $tree[] = $value;
                $tree = array_merge($tree,$this->subCat2($model,$list,$value['cat_id'],false,$level));
            }
        }
        if($includeself){
            $temp = M($model)->where(array('cat_id'=>$cat_id))->find();
            array_unshift($tree, $temp);
        }
        return $tree;
    }


    //子类ID
    public function sub_cat_ids($cat_id,$model='articlecat',$array=false){
        $sub_cat_tree = is_array($cat_id)? $cat_id : $this->subCat($cat_id,$model);
        $cat_ids = $this->get_sub_cat_ids($sub_cat_tree);
        return $cat_ids?($array? $cat_ids : implode(',',$cat_ids)) : ($array? array($cat_id) : $cat_id);
    }

    //递归求子类cat_id
    private function get_sub_cat_ids($sub_cat_tree){
        $cat_ids = array();
        foreach($sub_cat_tree as $value){
            $cat_ids[] = $value['cat_id'];
            if($value['sub_cat'])$cat_ids = array_merge($cat_ids,$this->get_sub_cat_ids($value['sub_cat']));
        }
        return $cat_ids;
    }
}


/* 权限判断，模版里用 */
function admin_priv2($priv_str){
  if ($_SESSION['action_list'] == 'all'){
    return true;
  }
  
  if (strpos($_SESSION['action_list'], $priv_str) === false){
    return false;
  }else{
    return true;
  }
}