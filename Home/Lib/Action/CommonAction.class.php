<?php
// 公共类
class CommonAction extends Action {
    protected $site_config = null;
    public function __construct() {
        header('Content-Type:text/html; charset=utf-8');
        $site_config = include WEB_ROOT . 'Common/systemConfig.php';
		
        //网站信息
        $this->site_info = $site_config['SITE_INFO'];
        $this->site_config = $site_config;
		
		//网站logo
		$this->logo=M('ads')->where('ads_id=7')->find();
		
        //分页设置
        $this->pageTheme = "%upPage% %first% %linkPage% %downPage% %end%";

        $this->assign('action', ACTION_NAME);

        //来源页
        $this->back_url = $_SERVER['HTTP_REFERER'];
        if(strpos($this->back_url, 'logout') || strpos($this->back_url, 'change_password')) $this->back_url = '/';

        //用户信息
        $userInfo = session('userInfo');
        if(empty($userInfo)){
            $user_id = cookie('user_id');
            if($user_id) $this->update_userInfo($user_id);
        }
        //if(empty($userInfo['head'])) $userInfo['head'] = "Public/Home/images/defaulthead.gif";
        $this->assign('userInfo', $userInfo);

        //所有分类
        $this->all_cats = $this->subCat(0);

        //年
        $this->Year     = date('Y', time());
    }
	
	

    //检查是否登录
    public function check_login(){
        $userInfo = session('userInfo');
        if(!$userInfo){
            echo "<script>location.href='". U('User/login') ."';</script>";
            exit;
        }
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
    public function subCat($cat_id,$model='articlecat',$includeself=false){
        $cat_list = M($model)->where(array('parent_id'=>$cat_id))->order('sort_order asc')->select();
        $tree=array();
        foreach($cat_list as $cat){
            if($cat['parent_id'] == $cat_id){
                $cat['sub_cat'] = $this->subCat($cat['cat_id'],$model);
                $tree[$cat['cat_id']]=$cat;
            }
        }
        if($includeself){
            $temp = M($model)->where(array('cat_id'=>$cat_id))->find();
            $temp['sub_cat'] = $tree;
            $tree = $temp;
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
        array_unshift($cat_ids, $cat_id);
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

    //搜索中转
    public function search(){
        $keyword    = trim($_REQUEST['keyword']);
		$url        = U('Info/search', array('keyword'=>$keyword));
        exit("<script>location.href = '$url';</script>");
    }

    //友情链接
    public function get_friendly_link(){
        $link_str = $this->site_config['OTHER']['friendly_link'];
        $link_arr = explode("\n",$link_str);
        $friendly_link = array();
        foreach($link_arr as $key=>$value){
            $arr = explode('|',$value);
            $friendly_link[$key]['name'] = $arr[0];
            $friendly_link[$key]['link'] = $arr[1]; 
        }
        return $friendly_link;
    }

    //网站地图
    public function sitemap(){
        //banner图
        $this->banner = M('ads')->where("ads_id=15")->find();
        $all_cats = $this->subCat(0,'articlecat');
        $this->all_cats = $all_cats;
        $this->display('Public:sitemap');
    }

    
  
    /**
      +----------------------------------------------------------
     * 文件上传
      +----------------------------------------------------------
     */
    function upload($allowExts=array('jpg', 'gif', 'png', 'jpeg'),$savePath,$saveRule,$thumb=false,$thumbPath,$thumbMaxWidth,$thumbMaxHeight,$thumbPrefix){
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
        $upload->thumb = $thumb; // 是否需要对图片文件进行缩略图处理，默认为false       
        $upload->thumbPath = $thumbPath;  //缩略图的保存路径，留空的话取文件上传目录本身
        $upload->thumbMaxWidth = $thumbMaxWidth;   //缩略图的最大宽度，多个使用逗号分隔
        $upload->thumbMaxHeight = $thumbMaxHeight;   //缩略图的最大高度，多个使用逗号分隔
        $upload->thumbPrefix = $thumbPrefix;   //缩略图的文件前缀，默认为thumb_  （如果你设置了多个缩略图大小的话，请在此设置多个前缀）
        // $upload->autoSub=true;   //是否使用子目录保存上传文件
        // $upload->subType='date';
        //$upload->is_fixed = true;   //是否生成固定比例的缩略图
        
        
        if(!$upload->upload()) { // 上传错误 提示错误信息 
            $this->error($upload->getErrorMsg(), $this->back_url);
        }else{ // 上传成功 获取上传文件信息 
            $info = $upload->getUploadFileInfo(); 
        }
        
        return $info;
    }

    //下载附件
    public function download($file_name=''){
        $file_name = base64_decode($file_name);
        $file_name = iconv('utf-8', 'gbk', $file_name);
        $file_dir = 'Uploads/download/';
        $file = fopen($file_dir . $file_name,"r"); // 打开文件
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".filesize($file_dir . $file_name));
        Header("Content-Disposition: attachment; filename=" . $file_name);
        // 输出文件内容
        echo fread($file,filesize($file_dir . $file_name));
        fclose($file);
        exit;
    }

    //更新用户信息
    public function update_userInfo($user_id=0){
        if(!$user_id) $user_id = $this->userInfo['user_id'];
        $userInfo = M('users')->find($user_id);
        $userInfo['city_name'] = M('region')->where("region_id=".$userInfo['city'])->getField('region_name');
        session('userInfo',$userInfo);
        session('user_id' ,$userInfo['user_id']);
        session('user_name' ,$userInfo['user_name']);
    }
}