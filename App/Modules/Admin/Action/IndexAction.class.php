<?php

/**
 * 	topos经销商后台
 */
class IndexAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
        $project = $this->project_model->where('classical = 1')->order('sequence desc')->limit(8)->select();
        foreach ($project as $k => $v) {
        	$detail = $this->detail_arr($v['detail']);
        	$detail_en = $this->detail_arr($v['detail_en']);
        	$project[$k]['position'] = $detail[0];
        	$project[$k]['position_en'] = $detail_en[0];
        }
        $this->project = $project;
        $this->display();
    }
}
?>