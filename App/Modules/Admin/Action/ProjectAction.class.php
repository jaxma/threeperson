<?php

/**
 * 	topos经销商后台
 */
class ProjectAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));

        $this->project_cat = $this->cat_model->where('status = 1 and pid = 1')->select();

        $project = $this->project_model->where('classical = 1')->order('sequence desc')->limit(100)->select();
        foreach ($project as $k => $v) {
            $detail = $this->detail_arr($v['detail']);
            $detail_en = $this->detail_arr($v['detail_en']);
            $project[$k]['position'] = $detail[0];
            $project[$k]['position_en'] = $detail_en[0];
        }
        $this->display();
    }

    public function detail() {
        $this->display('projectdetail');
    }
}
?>