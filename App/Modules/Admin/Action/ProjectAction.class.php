<?php

/**
 * 	topos经销商后台
 */
class ProjectAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
        $this->lang_change = $this->lang==1?0:1;
    }

   
    public function index() {

        $this->lang_url = U('Project/index',array('cat_id'=>$this->cat_id,'lang'=>$this->lang_change));

        $this->cats = $this->cat_model->where('status = 1 and pid = 1')->select();

        if($this->cat_id){
            $project = $this->project_model->where('isopen = 1 and cat2 = '.$this->cat_id)->order('sequence desc')->limit(100)->select();
        }else{
            $project = $this->project_model->where('classical = 1 and isopen = 1')->order('sequence desc')->limit(100)->select();
        }

        foreach ($project as $k => $v) {
            $detail = $this->detail_arr($v['detail']);
            $detail_en = $this->detail_arr($v['detail_en']);
            $project[$k]['position'] = $detail[0];
            $project[$k]['position_en'] = $detail_en[0];
        }

        $this->project = $project;
        $this->display();
    }

    public function detail() {
        $this->lang_url = U('Project/detail',array('cat_id'=>$this->cat_id,'id'=>$this->id,'lang'=>$this->lang_change));
        $this->cat = $this->cat_model->where('id='.$this->cat_id)->find();
        $project_detail = $this->project_model->where('id='.$this->id)->find();
        $detail = $this->detail_arr($project_detail['detail']);
        $detail_en = $this->detail_arr($project_detail['detail_en']);
        //位置
        $project_detail['position'] = $detail[0];
        $project_detail['position_en'] = $detail_en[0];
        //规模
        $project_detail['size'] = $detail[1];
        $project_detail['size_en'] = $detail_en[1];
        //建成时间
        $project_detail['completedyear'] = $detail[2];
        $project_detail['completedyear_en'] = $detail_en[2];
        //图片
        $many_image = $project_detail['many_image'];
        $this->many_image = explode(',', $many_image);
        //其他项目
        $other_project = $this->project_model->where('isopen = 1 and id != '.$this->id)->order('sequence desc')->limit(4)->select();
        foreach ($other_project as $k => $v) {
            $detail = $this->detail_arr($v['detail']);
            $detail_en = $this->detail_arr($v['detail_en']);
            $other_project[$k]['position'] = $detail[0];
            $other_project[$k]['position_en'] = $detail_en[0];
        }
        $this->other_project  = $other_project;
        $this->project_detail = $project_detail;
        $this->display('projectdetail');
    }
}
?>