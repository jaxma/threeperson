<?php
class ProductAction extends CommonAction {
	public function __construct() {
		parent::__construct();

        /******************************网站标题及面包屑导航******************************/
        $cat_id = $_GET['cat_id']+0;
        $goods_id = $_GET['id']+0;
        if($goods_id)$cat_id = M('goods')->where(array('goods_id'=>$goods_id))->getField('cat_id');

        $this->cat_id = $cat_id;

        $this->parent_id = M('goodscat')->where(array('cat_id'=>$cat_id))->getField('parent_id');

        $this->product_cat = $this->subCat(0,'goodscat');
        //print_r($this->product_cat);

        $parent_cat= $this->supCat($cat_id,true,'goodscat');
        //面包屑导航
        $home = '<a href="/">Home</a>';
        $ur_here = '';
        $product_site_title = array();
        foreach($parent_cat as $key=>$value){
            $ur_here .= " > <a href='". U('Product/index',array('cat_id'=>$value['cat_id'])) ."'>" . $value['cat_name'] . "</a>";
            $product_site_title[] = $value['cat_name'];
        }
        $this->product_site_title = implode('_',array_reverse($product_site_title));//产品页的标题
        if(!$ur_here)$ur_here = " > Product Center";
        $this->ur_here = $home . $ur_here;
        /******************************网站标题及面包屑导航******************************/

        //banner图
        $this->banner = M('ads')->find(5);

        //左边小广告图
        $this->ads1 = M('ads')->where('ads_id=31')->find();
	}

    public function index($cat_id=0) {
        //网站标题 关键字 描述
        $this->site_title = $this->product_site_title . '_' .$this->site_info['title'];
        $cat_info = M('goodscat')->where(array('cat_id'=>$cat_id))->find();

        $this->assign('cat_info',$cat_info);

        if($cat_info['keywords']){
            $this->site_keywords = $cat_info['keywords'];
        }else{
            $this->site_keywords = $this->site_info['keyword'];
        }

        if($cat_info['description']){
            $this->site_description = $cat_info['description'];
        }else{
            $this->site_description = $this->site_info['description'];
        }

        $keyword = $_REQUEST['keyword'];
        $keyword = iconv('gbk', 'utf-8', $keyword);
    	
        $sort_by = $_REQUEST['sort_by'];
    	$limit = $_REQUEST['limit']? $_REQUEST['limit']+0 : 6;

    	$where = "is_open=1";
        if($cat_id){
            $sub_cat_ids = $this->sub_cat_ids($cat_id,'goodscat');
            $where .= " and cat_id in ($sub_cat_ids)";
        }
        $order = 'sort_order asc,add_time desc';
    	if($keyword)$where .= " and ((title like '%$keyword%') or (cat_name like '%$keyword%'))";

    	import('ORG.Util.Page2');// 导入分页类
	    $count      = M('goods')->where($where)->count();// 查询满足要求的总记录数 $map表示查询条件
	    $Page       = new Page($count,$limit);// 实例化分页类 传入总记录数
	    $Page->setConfig('theme',$this->pageTheme);

    	$product_list = M('goods')->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

    	foreach($product_list as $k=>$v){
    		$product_list[$k]['thumb_img'] = M('goodsimg')->where(array('goods_id'=>$v['goods_id']))->order('sort_order asc,id desc')->getField('thumb_img');
    	}
    	$this->assign('product_list',$product_list);
        $this->assign('page',$Page);
    	$this->assign('totalPage',ceil($count/$limit));

        $this->display();
    }


    public function detail(){
        $goods_id = $_GET['id']+0;
        $detail = M('goods')->where("goods_id=$goods_id and is_open=1")->find();

        //面包屑导航
        $this->ur_here = $this->ur_here . ' > ' . $detail['title'];

        //网站标题 关键字 描述
        $this->site_title = $detail['title'] . '_' . $this->product_site_title . '_' . $this->site_info['title'];
        if($detail['keywords']){
            $this->site_keywords = $detail['keywords'];
        }else{
            $this->site_keywords = $this->site_info['keyword'];
        }

        if($detail['description']){
            $this->site_description = $detail['description'];
        }else{
            $this->site_description = $this->site_info['description'];
        }


        if($detail){
            $detail['images'] = M('goodsimg')->where("goods_id=".$detail['goods_id'])->order('sort_order asc,id desc')->select();
            $this->assign('detail',$detail);

            $cat_info = M('goodscat')->where(array('cat_id'=>$detail['cat_id']))->find();
            $this->assign('cat_info',$cat_info);

            M('goods')->where("goods_id=".$detail['goods_id'])->setInc('hit');
        }else{
            $this->error('错误：你的访问的产品不存在或未开放！');
        }

        $this->display();
    }
}