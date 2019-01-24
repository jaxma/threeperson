<?php

header("Content-type:text/html;charset=utf-8");

class CommonAction extends Action {

    public function _initialize() {
		$this->cat_id = I('cat_id');
		$this->id = I('id');
		$this->lang = I('lang');

		$this->news_model = M('news');
		$this->project_model = M('item');
		$this->company_model = M('company');
		$this->cat_model = M('cat');
		$this->about_model = M('about');
		$this->recruitment_model = M('recruitment');

		$title_arr = array(
			'index' => 'TOPOS', 
			'project' => '项目', 
			'news' => '新闻', 
			'about' => '关于TOPOS', 
            'contact' => '联系我们', 
			'search' => '搜索', 
		);
		$title_en_arr = array(
			'index' => 'TOPOS', 
			'project' => 'Project', 
			'news' => 'News', 
			'about' => 'About TOPOS', 
            'contact' => 'Contact', 
			'search' => 'Search', 
		);

    	$action_name = strtolower(ACTION_NAME);
    	$module_name = strtolower(MODULE_NAME);
    	$title_fix = " | Landscape Architecture & Planning";
    	if($action_name == 'index'){
    		$this->lang == 1?$this->title = $title_en_arr[$module_name].$title_fix:$this->title = $title_arr[$module_name].$title_fix;
    	}else{
    		switch($module_name) {
    			case 'project':
    				$res = $this->project_model->where('id = '.$this->id)->find();
    				break;
    			case 'news':
    				$res = $this->news_model->where('id = '.$this->id)->find();
    				break;
    			default:
    				break;
    		}
    		if(!$res){
    			$res['title'] = 'TOPOS'.$title_fix;
    			$res['title_en'] = 'TOPOS'.$title_fix;
    		}
    		$this->lang == 1?$this->title = $res['title_en'].$title_fix:$this->title = $res['title'].$title_fix;
    	}

        //公司信息 
        $this->desc =  $this->lang?C('TE_DESC'):C('T_DESC');
        $this->position =  $this->lang?C('TE_POSITION'):C('T_POSITION');
        $this->address =  $this->lang?C('TE_ADDRESS'):C('T_ADDRESS');
        $this->tel =  $this->lang?C('TE_TEL'):C('T_TEL');
        $this->email =  $this->lang?C('TE_EMAIL'):C('T_EMAIL');
        $this->en_desc =  $this->lang?C('TE_EN_DESC'):C('T_EN_DESC');
        $this->en_position =  $this->lang?C('TE_EN_POSITION'):C('T_EN_POSITION');
        $this->en_address =  $this->lang?C('TE_EN_ADDRESS'):C('T_EN_ADDRESS');
        $this->en_tel =  $this->lang?C('TE_EN_TEL'):C('T_EN_TEL');
        $this->en_email =  $this->lang?C('TE_EN_EMAIL'):C('T_EN_EMAIL');
    	$this->domain = C('YM_DOMAIN');
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