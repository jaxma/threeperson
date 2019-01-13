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
        $this->cat_model = M('cat');
        $this->item_model = M('item');
        $this->lang = I('lang')?I('lang'):'';
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