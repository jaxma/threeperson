<?php

/**
 * 	topos经销商后台
 */
class AboutAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {
    	$model = $this->about_model;
    	$list = $model->order('id asc')->limit(5)->select();
    	$res = array();
    	foreach ($list as $key => $val) {
    		$tmp = array();
    		//英文
    		if($this->lang){
    			$tmp['title'] = $val['title_en'];
    			$tmp['title_des'] = $val['title_des_en'];
    			$tmp_content = explode("------",$val['content_en']);
    		//中文
    		}else{
    			$tmp['title'] = $val['title'];
    			$tmp['title_des'] = $val['title_des'];
    			$tmp_content = explode("------",$val['content']);
    		}
    		$tmp['content'] = $tmp_content;
    		$tmp['images'] = explode(",",$val['many_image']);
    		$res[] = $tmp;
    	}
    	var_dump($res);exit();
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('About/index',array('lang'=>$lang_change));
        $this->display();
    }

}
?>