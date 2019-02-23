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
        $news_id = I('id',0);
        if(empty($news_id)) $this->redirect('News/index',array('lang'=>$this->lang));
        $model = $this->news_model;
        $tmp_list = $list = array();
        $tmp_list = $model->where(array('id'=>$news_id,'isopen'=>1))->find();
        if(empty($tmp_list)) $this->redirect('News/index',array('lang'=>$this->lang));
        if($this->lang){
            $list['title'] = htmlspecialchars_decode($tmp_list['title_en']);
            $list['content'] = $tmp_list['content_en'];

        }else{
            $list['title'] = htmlspecialchars_decode($tmp_list['title']);
            $list['content'] = $tmp_list['content'];
        }
        $list['publish_time'] = date("Y-m-d",$tmp_list['publish_time']);
        $list['id'] = $tmp_list['id'];
        if(!empty($tmp_list['many_image'])) $list['images'] = explode(",",$tmp_list['many_image']);

        $this->title = $this->lang?"NEWS":"新闻";
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('News/detail',array('id'=>$list['id'],'lang'=>$lang_change));
        $this->list = $list;
        $icons = M('icon')->where('isopen=1')->order('sequence desc')->select();
        $this_url =  $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        foreach ($icons as $k => $v) {
            $url = M('item_icon')->where('type = 2 and iconid = '.$v['id'].' and itemid = '.$news_id)->getField('url');
            $icons[$k]['loaction_url'] = $v['href'].'?url='.$this_url.$url;
        }
        $this->icons = $icons;
        $this->display('newsdetail');
    }
}
?>