<?php

/**
 * 	topos经销商后台
 */
class SearchAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index() {

    	$keyword = I('word','');
    	if(empty($keyword)) $this->redirect('Index/index',array('lang'=>$this->lang));
    	$Project_model = $this->project_model;
    	$News_model = $this->news_model;
    	$title = $title_en = $content_en = $content_en = $keyword;
    	if($this->lang){
    		$plist = $Project_model->where("isopen=1 and (  title_en like '%%d%' or content_en like '%%f%')",array($title_en,$content_en))->select();
    	}else{
    		$plist = $Project_model->where("isopen=1 and ( title like '%%d%' or content like '%%f%')",array($title,$content))->select();
    	}
    	
    	var_dump($plist);
    	exit();
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
        $this->display();
    }
}
?>