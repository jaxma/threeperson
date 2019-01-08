<?php
class IndexAction extends CommonAction {
	public function __construct() {
		parent::__construct();
	}

    public function index() {
        //导航索引ID
        $this->nid = 1;

        //网站标题 关键字 描述
        $this->site_title       = 'Home_'.$this->site_info['title'];
        $this->site_keywords    = $this->site_info['keyword'];
        $this->site_description = $this->site_info['description'];
        
        /************************轮播图*****************************/
        $this->banner_list      = M('ads')->where('cat_id=2')->order('ads_id asc')->select();

        //FBO项目
        //$this->fbo_projects     = array_slice($this->all_cats[4]['sub_cat'], 0, 3);
        $this->fbo_projects     = M('ads')->where('cat_id=6')->limit(3)->select();

        //产品服务
        $this->rec_services     = $this->all_cats[1]['sub_cat'];

        //新闻中心
        $this->rec_articles     = M('article')->field('article_id,title,short,original_img')->where('is_recommend=1')->order('sort_order asc,article_id desc')->limit(4)->select();

        $this->display();
    }


    public function test(){
        $result = send_mail('1058514799@qq.com','tujia','测试','邮件测试!');
        var_dump($result);
    }
}
?>