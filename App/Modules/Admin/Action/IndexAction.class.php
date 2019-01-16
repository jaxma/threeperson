<?php

/**
 * 	topos经销商后台
 */
class IndexAction extends CommonAction {

    private $company_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->company_model = M('company');
        $this->photo_model = M('photo');
        $this->cat_model = M('cat');
        $this->item_model = M('item');
        $this->news_model = M('news');
        $this->recruitment_model = M('recruitment');
        $this->lang = I('lang')?I('lang'):0;
    }

   
    public function index() {
        $lang = $this->lang;

    	$this->title = C('SYSTEM_NAME');

        //首页介绍
        $this->introduct = $this->company_model->where('id = 3 and status = 1')->find();
        //首页图片
        $index_photo  = $this->photo_model->where('type = 1')->find();
        //首页移动端图片
        $mobile_photo = $index_photo['many_image'];
        $mobile_photo = explode(',', $mobile_photo);

        //分类
        $cats = $this->cat_model->where('pid  = 0')->select();

        foreach ($cats as $k => $v) {
            $second_cats = $this->cat_model->where('pid = '.$v['id'])->select();
            if($v['id'] == 1){
                $classical = $this->item_model->where('classical = 1')->select();
                foreach ($classical as $kk => $vv) {
                    if(!empty($vv['detail'])){
                       $detail = $this->detail_arr($vv['detail']);
                    }
                    if(!empty($vv['detail_en'])){
                       $detail_en = $this->detail_arr($vv['detail_en']);
                    }
                    $classical[$kk]['position'] = $detail[0];
                    $classical[$kk]['position_en'] = $detail_en[0];
                }
                $cats[$k]['classical'] = $classical;
            }
            foreach ($second_cats as $kk => $vv) {
                if($vv['pid'] == 1){
                    //项目 
                    $model = $this->item_model;
                }elseif($vv['pid'] == 2){
                    //事务所
                    $model = $this->news_model;
                }elseif($vv['pid'] == 3){
                    //招聘
                    $model = $this->recruitment_model;
                }
                $second_cats_item = $model->where('cat2 = '.$vv['id'])->select();
                foreach ($second_cats_item as $kkk => $vvv) {
                    if(!empty($vvv['detail'])){
                       $detail = $this->detail_arr($vvv['detail']);
                    }
                    if(!empty($vvv['detail_en'])){
                       $detail_en = $this->detail_arr($vvv['detail_en']);
                    }
                    $second_cats_item[$kkk]['position'] = $detail[0];
                    $second_cats_item[$kkk]['position_en'] = $detail_en[0];
                    $second_cats_item[$kkk]['publish_time'] = date('Y-m-d',$vvv['publish_time']);
                }
                $second_cats[$kk]['items'] = $second_cats_item;
            }
            $cats[$k]['cat'] = $second_cats;
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

        $this->index_photo = $index_photo;
        $this->mobile_photo = $mobile_photo;
        $this->display();
    }
    public function project() {
        
        $this->display();
    }
    
     public function projectlist() {
        
        $this->display();
    }

    public function detail_arr($detail){
        if(!empty($detail)){
            $detail = str_replace("；",";",$detail);
            $detail = explode(";",$detail);
        }
        return $detail;
    }
}
?>