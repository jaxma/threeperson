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
    	$like_word = "%".$keyword."%";
    	if($this->lang){
    		$where['title_en'] = array('like',$like_word);
    		$where['content_en'] = array('like',$like_word);
    	}else{
    		$where['title'] = array('like',$like_word);
    		$where['content'] = array('like',$like_word);
    	}
    	$whre['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['isopen'] = 1;
		$plist = $Project_model->where($map)->select();
		$nlist = $News_model->where($map)->select();
    	var_dump($plist);
    	exit();
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
        $this->display();
    }
}
?>