<?php

/**
 * 	topos经销商后台
 */
class NewsAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index() {
        $model = $this->news_model;
        $con = array('isopen'=>1);

        $tmp_list = $model->where($con)->field('id,cat1,cat2,title,title_en,image,publish_time')->order('sequence desc')->select();
        $list = array();
        if(!empty($tmp_list)){
            foreach ($tmp_list as $k => $val) {
                $tmp = array();
                $tmp['title'] = $this->lang?htmlspecialchars_decode($val['title_en']):htmlspecialchars_decode($val['title']);
                $tmp['image'] = $val['image'];
                $tmp['id'] = $val['id'];
                $tmp['cat2'] = $val['cat2'];
                $tmp['cat1'] = $val['cat1'];
                $tmp['publish_time'] = date("Y-m-d",$val['publish_time']);
                $list[] = $tmp;
            }
        }
        $this->title = $this->lang?"NEWS":"新闻";
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('News/index',array('lang'=>$lang_change));
        $this->list = $list;
        $this->display();
    }

    public function detail() {
        $this->display('newsdetail');
    }
}
?>