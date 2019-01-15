<?php

/**
 * 	topos经销商后台
 */
class DetailAction extends CommonAction {

    private $company_model;
    private $cat_model;
    public $lang = 0;
    public $cat_id = 0;
    public $cat_table = array(
        '1'=>'item',
        '2'=>'news',
        '3'=>'recruitment',
    );
    public function _initialize()
    {
        parent::_initialize();
        $this->company_model = M('company');
        $this->cat_model = M('cat');
        $this->lang = I('lang')=='1'?1:0;
        $this->cat_id = I('cat_id',0);
        $this->a_id = I('a_id',0);
    }
    public function project() {
        if(empty($cat_id)) $this->redirect('Admin/Index/index',array('lang'=>$this->lang));
        $cat_id = intval($cat_id);
        $cat_info = $this->cat_model->where('status = 1 and id = '.$cat_id)->field('name,pid')->find();
        $pid = $cat_info['pid'];
        $keys = array_keys($this->cat_table);
        if(empty($cat_info)||(!in_array($pid,$keys))) $this->redirect('Admin/Index/index',,array('lang'=>$this->lang));
        $model_name = $this->cat_table[$pid];
        $model = M($model_name);
        $info = $model->where('isopen = 1 and id = '.$a_id)->find();

        if(empty($info)||$info['cat2']!=$this->cat_id){
            $this->redirect('Admin/Detail/projectlist',array('lang'=>$this->lang,'cat_id'=>$pid));
            exit();
        }
        $res = array();
        $p_info = $this->cat_model->where('status = 1 and id = '.$pid)->field('name')->find();
        if(empty($p_info)){
            $p_info['name'] = "项目";
            $p_info['name_en'] = "Item";
        }
        /*
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_news` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_news_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image2` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` blob,
  `content_en` blob,
  `publish_time` int(10) unsigned DEFAULT '0' COMMENT '发表时间',
  `time` int(10) unsigned DEFAULT '0',
  `isopen` tinyint(10) DEFAULT '0' COMMENT '是否开启',
  `many_image` text CHARACTER SET utf8 COMMENT '多图上传',
  `sequence` int(11) DEFAULT '0' COMMENT '优先级',
  `cat1` int(10) NOT NULL DEFAULT '0' COMMENT '一级分类',
  `cat2` int(10) DEFAULT '0' COMMENT '二级分类',
  `classical` int(11) NOT NULL,
         */
        $res = array()
        if(empty($lang)){
            $res['head'] = $p_info['name'];
            $res['title'] = $info['title'];
            $res['title_news'] = $info['title_news'];
            $res['content'] = $info['content'];
            $res['publish_time'] = date('Y-m-d',$info['publish_time']);
            if(!empty($info['detail'])){
                $info['detail'] = str_replace("；",";",$info['detail']);
                $detail = implode(";",$info['detail']);
                $res['type'] = 1;//项目
            }else{
                $info['detail'] = date('Y-m-d',$info['publish_time']);
                $res['type'] = 0;//新闻
            }
            $res['detail'] = $info['detail'];


        }else{
            $res['head'] = $p_info['name_en'];
            $res['title'] = $info['title_en'];
            $res['title_news'] = $info['title_news_en'];
            $res['content'] = $info['content_en'];
            if(!empty($info['detail_en'])){
                $info['detail_en'] = str_replace("；",";",$info['detail_en']);
                $detail = implode(";",$info['detail_en']);
                $res['type'] = 1;//项目
            }else{
                $info['detail_en'] = date('Y-m-d',$info['publish_time']);
                $res['type'] = 0;//新闻
            }
            $res['detail'] = $info['detail_en'];
        }
        $res['publish_time'] = date('Y-m-d',$info['publish_time']);
        $res['image'] = $info['image'];
        $res['image2'] = $info['image2'];
        if(!empty($res['many_image'])){
            $images = implode(",",$res['many_image']);
            if(count($images)<=1){
                $images = array();
            }else{
                unset($images[0]);
            }
        }
        $res['many_image_open'] = count($images)>=1?true:false;

        $res['many_image'] = $images;
        $this->res = $res;
        $this->lang = $lang;
        $this->display();

    }

    public function projectlist() {

    }


    public function index() {
        $lang = $this->lang;

    	$this->title = C('SYSTEM_NAME');

        //首页介绍
        $this->introduct = $this->company_model->where('id = 3 and status = 1')->find();

        //分类
        $cats = $this->cat_model->where('pid  = 0')->select();

        foreach ($cats as $k => $v) {
            $cats[$k]['cat'] = $this->cat_model->where('pid = '.$v['id'])->select();
        }

        //公司信息 
        $this->position =  $lang?C('TE_POSITION'):C('T_POSITION');
        $this->address =  $lang?C('TE_ADDRESS'):C('T_ADDRESS');
        $this->tel =  $lang?C('TE_TEL'):C('T_TEL');
        $this->email =  $lang?C('TE_EMAIL'):C('T_EMAIL');
        $this->en_position =  $lang?C('TE_EN_POSITION'):C('T_EN_POSITION');
        $this->en_address =  $lang?C('TE_EN_ADDRESS'):C('T_EN_ADDRESS');
        $this->en_tel =  $lang?C('TE_TEL'):C('T_EN_TEL');
        $this->en_email =  $lang?C('TE_EMAIL'):C('T_EN_EMAIL');

        $this->cats = $cats;

        //项目
        $this->items = $this->item_model->where(1)->select();

       $this->display();
    }
    public function project() {
        
        $this->display();
    }
    
     public function projectlist() {
        
        $this->display();
    }
}
?>