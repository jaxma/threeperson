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
        '3'=>'recruitment'
    );
    private $detail_des = array('地点','用地面积','建成时间','发布时间');
    private $detail_des_en = array('Location','Site Area','Completion','Release time');
    private $limit = 100; //列表页限制显示文章篇数为100篇
    public function _initialize()
    {
        parent::_initialize();
        $this->company_model = M('company');
        $this->cat_model = M('cat');
        $this->lang = I('lang')=='1'?1:0;
        $this->cat_id = I('cat_id',"0");
        $this->a_id = I('a_id',0);
    }
    public function project() {
        $cat_id = $this->cat_id;
        $a_id = $this->a_id;
        $lang = $this->lang;
        if(empty($cat_id)){
            $this->redirect('Index/index',array('lang'=>$this->lang));
        }
        $cat_id = intval($cat_id);
        $cat_info = $this->cat_model->where('status = 1 and id = '.$cat_id)->field('name,name_en,pid')->find();
        $pid = $cat_info['pid'];
        $keys = array_keys($this->cat_table);
        if(empty($cat_info)||(!in_array($pid,$keys))){
            $this->redirect('Index/index',array('lang'=>$this->lang));
        }
        $model_name = $this->cat_table[$pid];
        $model = M($model_name);
        $info = $model->where('isopen = 1 and id = '.$a_id)->find();

        if(empty($info)||$info['cat2']!=$cat_id){
            $this->redirect('Detail/projectlist',array('lang'=>$this->lang,'cat_id'=>$pid));
            exit();
        }
        $res = array();
        $p_info = $this->cat_model->where('status = 1 and id = '.$pid)->field('name,name_en')->find();
        if(empty($p_info)){
            $p_info['name'] = "项目";
            $p_info['name_en'] = "Item";
        }
        $detail_des = $this->detail_des;
        $detail_des_en = $this->detail_des_en;
        $res = array();
        if(empty($lang)){
            $res['head'] = $p_info['name'];
            $res['title'] = $info['title'];
            $res['title_news'] = $info['title_news'];
            $res['content'] = $info['content'];
            $res['publish_time'] = date('Y-m-d',$info['publish_time']);
            if(!empty($info['detail'])){
                $info['detail'] = str_replace("；",";",$info['detail']);
                $detail = explode(";",$info['detail']);
                $res['type'] = 1;//项目
            }else{
                $info['detail'] = date('Y-m-d',$info['publish_time']);
                $res['type'] = 0;//新闻
            }
            $res['detail'] = $detail;
            $res['detail_des'] = $detail_des;

        }else{
            $res['head'] = $p_info['name_en'];
            $res['title'] = $info['title_en'];
            $res['title_news'] = $info['title_news_en'];
            $res['content'] = $info['content_en'];
            if(!empty($info['detail_en'])){
                $info['detail_en'] = str_replace("；",";",$info['detail_en']);
                $detail = explode(";",$info['detail_en']);
                $res['type'] = 1;//项目
            }else{
                $info['detail_en'] = date('Y-m-d',$info['publish_time']);
                $res['type'] = 0;//新闻
            }
            $res['detail'] = $detail;
            $res['detail_des'] = $detail_des_en;
        }
        $res['publish_time'] = date('Y-m-d',$info['publish_time']);
        $res['image'] = $info['image'];
        $res['image2'] = $info['image2'];
        if(!empty($info['many_image'])){
            $images = explode(",",$info['many_image']);
            if(count($images)<=1){
                $images = array();
            }else{
                unset($images[0]);
            }
        }
        $res['many_image_open'] = count($images)>=1?true:false;
        $res['many_image'] = $images;
        $this->lang_url = U('Detail/project',array('lang'=>$this->lang,'cat_id'=>$cat_id,'a_id'=>$a_id));
        var_dump(U('project',array('lang'=>$this->lang,'cat_id'=>$cat_id,'a_id'=>$a_id)));
        var_dump($this->lang_url);exit();
        $this->res = $res;
        $this->lang = $lang;
        $this->display();

    }

    public function projectlist() {
        $cat_id = $this->cat_id;
        $lang = $this->lang;
        if(empty($cat_id)){
            $this->redirect('Index/index',array('lang'=>$this->lang));
        }
        if($cat_id=='classical'){
            $photo_model = M('photo');
            $cat_info = $photo_model->where('type=2 and isopen =1')->field('name,name_en,image')->find();
            $pid = 1;
            $switch = true;
        }else{
            $cat_id = intval($cat_id);
            $cat_info = $this->cat_model->where('status = 1 and id = '.$cat_id)->field('name,pid,name_en,image')->find();
            $pid = $cat_info['pid'];
            $switch = false;
        }
        $keys = array_keys($this->cat_table);


        //目前只有项目有详情页
        if(empty($cat_info)||(!in_array($pid,$keys)||empty($cat_info['image']))){
            $this->redirect('Index/index',array('lang'=>$this->lang));
        }
        $res = array();
        $res['head'] = $lang?$cat_info['name_en']:$cat_info['name'];
        $res['image'] = $cat_info['image'];
        $model_name = $this->cat_table[$pid];
        $model = M($model_name);
        $field = array('image,title,title_en,detail,detail_en');
        if($switch){
            $info = $model->where('isopen = 1 and classical=1')->field('id,cat2,image,title,title_en,detail,detail_en')->order('sequence desc,time desc')->limit($this->limit)->select();
        }else{
            $info = $model->where('isopen = 1 and cat2 = '.$cat_id)->field('id,cat2,image,title,title_en,detail,detail_en')->order('sequence desc,time desc')->limit($this->limit)->select();
        }

        if(empty($info)){
            $this->redirect('Index/index',array('lang'=>$this->lang));
        }
        $list = array();
        foreach ($info as $key => $val) {
            $tmp = array();
            $tmp['id'] = $val['id'];
            $tmp['cat2'] = $val['cat2'];
            $tmp['image'] = $val['image'];
            if($lang){
                $tmp['title'] = $val['title_en'];
                $val['detail_en'] = str_replace("；",";",$val['detail_en']);
                $detail = explode(";",$val['detail_en']);
            }else{
                $tmp['title'] = $val['title'];
                $val['detail'] = str_replace("；",";",$val['detail']);
                $detail = explode(";",$val['detail']);
            }
            $tmp['address'] = $detail[0];
            $list[] = $tmp;
        }
        $this->res = $res;
        $this->list = $list;
        $this->lang = $lang;
        $this->display();
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
}
?>