<?php
class InfoAction extends CommonAction {
	public function __construct() {
		parent::__construct();
        /******************************网站标题及面包屑导航******************************/
        $article_id = $_GET['id']+0;
        if($article_id) $cat_id = M('article')->where(array('article_id'=>$article_id))->getField('cat_id');
        if($_GET['cat_id']) $cat_id = $_GET['cat_id']+0;

        //转换父级cat_id为第一个子类cat_id
        if($cat_id==1) $cat_id=6;
        if($cat_id==2) $cat_id=11;
        if($cat_id==3) $cat_id=19;
        if($cat_id==4) $cat_id=25;
        if($cat_id==5) $cat_id=14;
        if($this->action=='fboIndex') $cat_id = 4;

        $this->cat_id = $cat_id;
        
        $this->cat_info = M('articlecat')->where(array('cat_id'=>$cat_id))->find();

        $parent_id = M('articlecat')->where("cat_id=$cat_id")->getField('parent_id');
        if($parent_id==0) $parent_id = $cat_id;

        //导航栏目关系
        if($cat_id==1 || in_array($cat_id,$this->sub_cat_ids(1,'articlecat',true))) $parent_id = 1;
        if($cat_id==2 || in_array($cat_id,$this->sub_cat_ids(2,'articlecat',true))) $parent_id = 2;
        if($cat_id==3 || in_array($cat_id,$this->sub_cat_ids(3,'articlecat',true))) $parent_id = 3;
        if($cat_id==4 || in_array($cat_id,$this->sub_cat_ids(4,'articlecat',true))) $parent_id = 4;
        if($cat_id==5 || in_array($cat_id,$this->sub_cat_ids(5,'articlecat',true))) $parent_id = 5;

        $this->parent_id = $parent_id;

        $this->parent_cat = M('articlecat')->where('cat_id='.$parent_id)->find();

        $article_cat = $this->subCat($parent_id,'articlecat');
        foreach($article_cat as $key=>$value){
            if(empty($value['sub_cat'])){
                $article_count = M('article')->where(array('cat_id'=>$value['cat_id']))->count();
                if($article_count==1){
                    $article_cat[$value['cat_id']]['only_article'] = M('article')->field('article_id')->where(array('cat_id'=>$value['cat_id']))->limit(1)->getField('article_id');
                }
            }
        }
        $this->assign('article_cat',$article_cat);

        $parent_cat= $this->supCat($cat_id,true,'articlecat');
        //面包屑导航
        if(($this->action == 'newsDetail') || ($this->action == 'mediaDetail')) $this->action = 'news';
        if(($this->action == 'aboutDetail') || ($this->action == 'courseDetail')) $this->action = 'about';

        $ur_here = '<a href="/">Home</a>';
        $article_site_title = array();
        foreach($parent_cat as $key=>$value){
            $ur_here .= " > <a href='". U('Info/'.$this->action,array('cat_id'=>$value['cat_id'])) ."'>" . strip_tags($value['cat_name']) . "</a>";
            $article_site_title[] = $value['cat_name'];
        }
        $this->article_site_title = implode('_',array_reverse($article_site_title));//产品页的标题
        $this->ur_here = $ur_here;
        /******************************网站标题及面包屑导航******************************/
    
        
        //Banner图及导航栏ID
        $this->nid       = 1;
        $banner_id       = 13;
        if($this->parent_id == 1){
            $this->nid = 2;
            $banner_id = 8;
        } 
        if($this->parent_id == 2){
            $this->nid = 3;
            $banner_id = 9;
        }
        if($this->parent_id == 3){
            $this->nid = 4;
            $banner_id = 10;
        }
        if($this->parent_id == 4){
            $this->nid = 5;
            $banner_id = 11;
        }
        if($this->parent_id == 5){
            $this->nid = 6;
            $banner_id = 12;
        }
		$this->banner  = M('ads')->where("ads_id=$banner_id")->find();

        //右边公告
        $this->right_notice = M('article')->find(56);
        //右边轮播图
        $this->right_lunbao = M('ads')->where('cat_id=5')->order('sort_order asc')->select();
	}

    public function index($tpl='list'){
        $cat_id = $this->cat_id;
		
        /*****start   网站标题 关键字 描述   start*********/
        $this->site_title = $this->article_site_title . '_' .$this->site_info['title'];

        $cat_info = $this->$cat_info;

        if($cat_info['keywords']){
            $this->site_keywords = $cat_info['keywords'];
        }else{
            $this->site_keywords = $this->site_info['keyword'];
        }

        if($cat_info['cat_desc']){
            $this->site_description = $cat_info['cat_desc'];
        }else{
            $this->site_description = $this->site_info['description'];
        }
        /*****end    网站标题 关键字 描述    end**********/

        $where = "is_open=1";
        if($cat_id){
            $sub_cat_ids = $this->sub_cat_ids($cat_id,'articlecat');
            $where .= " and cat_id in ($sub_cat_ids)";
        }
        $keyword    = $this->_get('keyword');
        if($keyword)  $where .= " and title like '%$keyword%'"; 
        $limit      = $this->limit? $this->limit : 5;

        import('ORG.Util.Page2');// 导入分页类
        $count      = M('article')->where($where)->count();// 查询满足要求的总记录数 $map表示查询条件
        $Page       = new Page($count,$limit);// 实例化分页类 传入总记录数
        $Page->setConfig('theme',$this->pageTheme);
        $this->assign('page',$Page);

        $this->article_list = M('article')->where($where)->order(SO)->limit($Page->firstRow.','.$Page->listRows)->select();
		
	
        $this->assign('totalPage',ceil($count/$limit));

        $this->display($tpl);
        exit;
    }

    public function detail($id) {
    	$article = M('article')->field('title,en_title,add_time,content,cat_id')->where('is_open=1 and article_id='.$id)->find();

        $this->assign('article',$article);

        /*更新文章点击数*/
        M('article')->where("article_id=$id")->setInc('click_sum');

        //网站标题 关键字 描述
        $this->site_title = $this->article['title'].'_'.$this->site_info['title'];
        if($this->article['keywords']){
            $this->site_keywords = $this->article['keywords'];
        }else{
            $this->site_keywords = $this->site_info['keyword'];
        }
        if($this->article['description']){
            $this->site_description = $this->article['description'];
        }else{
            $this->site_description = $this->site_info['description'];
        }

        $this->ur_here = $this->ur_here . "<span>></span>" . $article['title'];

        $cat_id = $this->cat_id;
		$this->id=$id;

        $this->cat_info = M('articlecat')->find($this->cat_id);


        /*上一篇 下一篇*/
        $where = "is_open=1";
        if($cat_id){
            $sub_cat_ids = $this->sub_cat_ids($cat_id,'articlecat');
            $where .= " and cat_id in ($sub_cat_ids)";
        }
        $article_ids = M('article')->field('article_id')->where($where)->order(SO)->select();
        foreach ($article_ids as $key => $value) {
            if($id == $value['article_id']){
                $this->prev_article = M('article')->field(COLS)->where('article_id='.intval($article_ids[$key-1]['article_id']))->find();
                $this->next_article = M('article')->field(COLS)->where('article_id='.intval($article_ids[$key+1]['article_id']))->find();
            }
        }


        if($cat_id==29){//其他文章（全屏显示）
            $this->display('fullDetail');
        }else{
            $this->display('detail');
        }
        exit;
    }

    /**
     * 产品服务
     */
    public function pService(){
        $cat_id = $this->cat_id;
        $this->article_nav = array_values($this->article_cat[$cat_id]['sub_cat']);
        $only_article = $this->article_cat[$cat_id]['only_article'];

        //商务机租赁服务
        if($cat_id==10) $this->index('lease');

        //机型介绍
        if($cat_id==23){
            $this->site_title = '机型介绍_' . $this->site_info['name'];

            $where = "is_open=1 and cat_id=$cat_id";
            $limit = 5;
            import('ORG.Util.Page2');// 导入分页类
            $count      = M('article')->where($where)->count();// 查询满足要求的总记录数 $map表示查询条件
            $Page       = new Page($count,$limit);// 实例化分页类 传入总记录数
            $Page->setConfig('theme',$this->pageTheme);
            $this->assign('page',$Page);

            $article_list = M('article')->where($where)->order(SO)->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($article_list as $key => $value) {
                $article_list[$key]['album'] = M('album')->where("type='article' and id_value=".$value['article_id'])->order('sort_order asc')->select();
            }
            //pre($article_list);
            $this->article_list = $article_list;
          // print_r($article_list);exit;
            $this->display('jieshao');
        }

        //包机流程
        if($cat_id==24){
          $result = M('article')->field('content')->where("article_id=75")->find();
        //print_r($result);exit;
        $this->assign('result',$result);
        $this->display('flow');
        }
        //调机惊喜
        if($cat_id==22) $this->detail(59);

        //单篇文章
        if($only_article) $this->detail($only_article);
    }

    /**
     * 新闻中心
     */
    public function news(){
        $cat_id = $this->cat_id;
        if($cat_id==11 || $cat_id==13){
        	$this->limit = 12;
        	$this->index('news');
        }
        if($cat_id==12){
        	$this->limit = 5;
        	$this->index('media');
        }
    }

    /**
     * 新闻详情
     */
    public function newsDetail($id){
    	$this->detail($id);
    }

    /**
     * 媒体详情
     */
    public function mediaDetail($id){
    	$this->detail($id);
    }


    /**
     * 客户服务
     */
    public function cService(){
    	$cat_id = $this->cat_id;

    	if($cat_id==19){
	    	$this->limit = 12;
	    	$this->index('survey');
    	}elseif($cat_id==20){
            $this->detail(48);
    		$this->display('message');
    	}
    }

    /**
     * 下载调查问卷
     */
   	public function download($type,$id){
   		if($type == 'survey'){
            $this->check_login();

   			$file_name = M('article')->where("article_id=$id")->getField('file_url');
   			$file_name = base64_encode(str_replace(dirname($file_name).'/', '', $file_name));
   			parent::download($file_name);
   		}
   	}

    /**
     * 上传调查问卷
     */
    public function uploadSurvey(){
        $this->check_login();

        $savePath   = 'Uploads/survey/' . date('Ymd') . '/';
        $info       = $this->upload($allowExts=array('doc', 'docx', 'xls', 'xlsx'),$savePath,'uniqid',$thumb=false);

        $data = array();
        $data['file_name'] = $info[0]['name'];
        $data['file_url']  = $info[0]['savepath'] . $info[0]['savename'];
        $data['add_time']  = time();
        $data['user_id']   = $this->userInfo['user_id'];
        $data['user_name'] = $this->userInfo['user_name'];
        $data['email']     = $this->userInfo['email'];

        if(M('survey')->add($data)){
            $this->success('上传成功，感谢您的支持！');
        }else{
            $this->error('网络错误！请稍候再试！');
        }
    }

    /**
     * fbo服务首页
     */
    public function fboIndex(){
        $this->index('fboIndex');
    }


   	/**
   	 * fbo服务
   	 */
   	public function fbo(){
   		$this->index('fbo');
   	}

    /**
     * 关于翼通
     */
    public function about(){
        $cat_id         = $this->cat_id;
        $only_article   = $this->article_cat[$cat_id]['only_article'];
        if($cat_id==14 || $cat_id==18){
            $this->detail($only_article);
        }
        if($cat_id==15){
            $this->index('course');
        }
        $this->index('aboutList');
    }

    /**
     * 发展历程详细
     */
    public function courseDetail($id){
        $this->detail($id);
    }

    /**
     * 关于翼通其他列表的详细页
     */
    public function aboutDetail($id){
        $this->detail($id);
    }
	 

    //添加留言
    public function addMessage(){
        $data = M('guestbook')->create();
        foreach($data as $v){
            if(empty($v)){
                $this->error('请认真填写各项内容，再提交!');
                exit;
            }
        }

        if (!is_email($data['email'])) $this->error('邮箱格式错误！');

        if (!is_phone($data['phone'])) $this->error('电话格式错误！');

        $data['add_time'] = time();

        if(M('guestbook')->add($data)){
            $this->success('您的留言已经提交，感谢您的反馈！');
        }else{
            $this->error('网络错误！');
        }
    }


    /*查询文章*/
    public function getArticle($id){
        $result = M('article')->field('title,content')->where("article_id=$id")->find();
        exit(json_encode($result));
    }

    /*查询文章列表*/
    public function get_article_list($cat_id,$tpl,$p,$page_size,$contain){
        $this->article_list = M('article')->field(COLS)->where(array('cat_id'=>$cat_id))->order('add_time desc')->page($p,$page_size)->select();
        
        //容器
        $this->cat_id = $cat_id;
        $this->tpl = $tpl;
        $this->page = $p;
        $this->page_size = $page_size;
        $this->contain = $contain;

        import('ORG.Util.Page');
        $count = M('article')->where(array('cat_id'=>$cat_id))->count();
        $Page = new Page($count,$page_size);
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $this->assign('show',$Page->show());

        $html = $this->fetch('Pages:'.$tpl);
        exit($html);
    }

    /*查询评论列表*/
    public function get_comment_list($id_value, $rank_type,$tpl,$p,$page_size,$contain,$type='article'){
        $cat_ids                      = $this->sub_cat_ids($cat_id);
        if($rank_type == 1) $rank_str = ' and rank in (4,5)';
        if($rank_type == 2) $rank_str = ' and rank in (2,3)';
        if($rank_type == 3) $rank_str = ' and rank=1';

        $sql                = "select count(*) as ct from ".table('comment')." c inner join ".table('users')." u on c.uid=u.user_id where type='$type' and id_value=$id_value and tid=0 and is_check=1".$rank_str;
        
        $count              = M()->query($sql);

        import('ORG.Util.Page2');
        $count              = $count[0]['ct'];
        $Page               = new Page($count,$page_size);
        $Page->setConfig('theme', $this->pageTheme);
        $this->assign('show',$Page->show());
        $this->assign('totalPage',ceil($count/$page_size));

        $sql                = "select c.*,u.user_id,u.user_name,u.head from ".table('comment')." c inner join ".table('users')." u on c.uid=u.user_id where type='$type' and id_value=$id_value and tid=0 and is_check=1".$rank_str." order by add_time desc limit ".($p-1)*$page_size.','.$page_size;
        $comment_list       = M()->query($sql);

        foreach ($comment_list as $key => $value) {
            $comment_list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
        }

        $this->comment_list = $comment_list;

        //容器
        $this->id_value     = $id_value;
        $this->rank_type    = $rank_type;
        $this->tpl          = $tpl;
        $this->page         = $p;
        $this->page_size    = $page_size;
        $this->contain      = $contain;
        $this->type         = $type;

        $html               = $this->fetch('Pages:'.$tpl);
        exit($html);
    }


    //添加评论
    public function addComment(){
        $userInfo           = session('userInfo');
        if(empty($userInfo)) $this->error('登录之后才能评论！');
        $data               = M('comment')->create();
        if(empty($data['content'])) $this->error('请输入评论内容！');
        if(strlen($data['content'])<15) $this->error('评论内容长度太短！');
        if(mb_strlen($data['content'], 'utf-8')>128) $this->error('评论长度不可多于128个字符！');

        if($data['rank']==0) $this->error('请选择评分！');

        $data['add_time']   = time();
        $data['uid']        = $userInfo['user_id'];
        $data['ip']         = get_ip();
        $data['booking_id'] = $_POST['bid'] + 0;
        $data['type']       = $this->_post('type','trim','article');

        if(M('comment')->add($data)){
            if($data['type']=='aunt'){//更新预约状态
                M('booking')->where('id='.$_POST['bid'])->setField('status',5);
            }elseif($data['type']=='package'){//更新订单状态
                M('order_info')->where("order_id=".$_POST['order_id'])->save(array('order_status'=>7,'comment_sum'=>1));
            }
            $this->success('评论成功，评论审核中！');
        }else{
            $this->error('未知错误！');
        }
    }

    /**
     * 文章搜索
     * @return [type] [description]
     */
    public function search(){
        $this->limit    = 12;
        $this->article_site_title  = $_GET['keyword'] . '_文章搜索';
        $this->index('search');
    }
}