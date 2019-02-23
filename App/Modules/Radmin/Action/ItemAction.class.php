<?php

/**
 * 	topos经销商管理系统
 */
class ItemAction extends CommonAction {

    private $cat_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->cat_model = M('cat');
    }
    
    //获取表名
    private function get_model(){
        
        return 'Item';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '项目';
    }

    //产品信息列表
    public function index() {
        // $cat1 = I('category_id1');
        $cat2 = I('category_id2');
        // $cat1 ? $where1 = ' and cat1= '.$cat1 : $where1 = '';
        $cat2 ? $where = ' and cat2= '.$cat2 : $where = '';
        $model_name = $this->get_model();
        $count = D($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('time desc')->where('cat1 = 1 '.$where)->limit($limit)->select();
            foreach ($list as $k => $v) {
                $this_cat2 = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->field('name,pid')->find();
                if($this_cat2){
                    $list[$k]['cat2'] = $this_cat2['name'];
                    $this_cat1 = $this->cat_model->where('status = 1 and id = '.$this_cat2['pid'])->field('name')->find();
                    $list[$k]['cat1'] = $this_cat1['name'];
                }else{
                    $this_cat1 = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->field('name')->find();
                    $list[$k]['cat1'] = $this_cat1['name'];
                }
            }
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
    }



    //摄影图片
    //添加产品信息
    public function add() {
        $c_id = 1;
        $p_id = 4;
        //共享
        $icons = M('icon')->where('isopen=1')->order('sequence desc')->select();
        $this->assign('c_id',$c_id);
        $this->assign('p_id',$p_id);
        $this->icons = $icons;
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();
        $title = trim(I('post.title',''));
        $title_en = trim(I('post.title_en',''));
        $category_id1 = I('post.category_id1',0);
        $category_id2 = I('post.category_id2',0);
        $isopen=I('post.isopen');
        $image=I('post.image');
        $image_icon=I('post.image_icon');
        $publish_time = I('post.publish_time',date('Y-m-d',time()));
        $sequence = I('post.sequence');
        $detial_title = trim(I('post.detial_title',''));
        $detial_title_en = trim(I('post.detial_title_en',''));
        $detail = trim(I('post.detail',''));
        $detail_en = trim(I('post.detail_en',''));
        $classical = trim(I('post.classical',''));
        $many_image=I('many_image');
        $image2 = $many_image[0];
        $many_images = implode(',',$many_image);
        $content = trim(I('post.content',''));
        $content = $this->formateStr($content);
        $content_en = trim(I('post.content_en',''));
        $content_en = $this->formateStr($content_en);
        if(empty($title)||empty($title_en)||empty($category_id1)||empty($category_id2)||empty($image)||empty($image_icon)||empty($detial_title)||empty($detial_title_en)||empty($image2)||$category_id1=='a'||$category_id2=='a'){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $data = array(
            'title' => $title,
            'title_en' => $title_en,
            'title_news' => $detial_title,
            'title_news_en' => $detial_title_en,
            'image' => $image,
            'image_icon' => $image_icon,
            'image2' => $image2,
            'content' => $content,
            'content_en' => $content_en,
            'detail' => $detail,
            'detail_en' => $detail_en,
            'publish_time' => strtotime($publish_time),
            'isopen' => $isopen,
            'many_image' => $many_images,
            'sequence' => $sequence,
            'time' => time(),
            'cat1'=> $category_id1,
            'cat2'=> $category_id2,
            'classical'=> $classical,
        );

        $res = D($model_name)->add($data);

        if ($res) {

            $item_icon_arr = I('post.item_icon');
            $item_icon_id_arr = I('post.item_icon_id');
            foreach ($item_icon_arr as $k => $v) {
                if(empty($v)){
                    $this->error('红色带星项目必须填写，请检查后重新提交');
                    exit();
                }
            }
            foreach ($item_icon_id_arr as $k => $v) {
                $icon_data = array(
                    'type' => 1,
                    'iconid' => $v,
                    'itemid' => $res,
                    'url' => $item_icon_arr[$k],
                    'time' => time(),
                );
                M('item_icon')->add($icon_data);
            }

            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',__URL__.'/'.'index');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑产品信息
    public function edit() {
        $model_name = $this->get_model();
        
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $row_image = $row['many_image'];
        $arr = explode(',', $row_image);
        //在每个前面加上__ROOT__,用在编辑时本机显示图片
        array_walk(
            $arr,
            function (&$s, $k, $prefix = '__ROOT__') {
                $s = str_pad($s, strlen($prefix) + strlen($s), $prefix, STR_PAD_LEFT);
            }
        );
        $row_arr=implode(',',$arr);
        $cat1 = $this->cat_model->where('id='.$row['cat1'])->find();
        $cat2 = $this->cat_model->where('id='.$row['cat2'])->find();
        //共享
        $icons = M('icon')->where('isopen=1')->order('sequence desc')->select();
        foreach ($icons as $k => $v) {
            $icons[$k]['url'] = M('item_icon')->where('type=1 and itemid = '.$id.' and iconid = '.$v['id'])->getField('url');
        }

        $this->row = $row;
        $this->arr = $row_arr;
        $this->id = $id;
        $this->icons = $icons;
        $this->display();
    }

    public function update() {
        $model_name = $this->get_model();
        
        $id = I('post.id');
        $id_info=M($model_name)->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        //分享图片
        $old_image_icon=$id_info['image_icon'];
        $image_icon=I('image_icon');
        if(strcmp($old_image_icon,$image_icon)==0){
            $image_icon=$image_icon;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image_icon'];
            @unlink($url);
            $image_icon = $image_icon;
        }

        $item_icon_arr = I('post.item_icon');
        $item_icon_id_arr = I('post.item_icon_id');
        foreach ($item_icon_arr as $k => $v) {
            if(empty($v)){
                $this->error('红色带星项目必须填写，请检查后重新提交');
                exit();
            }
        }
        foreach ($item_icon_id_arr as $k => $v) {
            $icon_data = array(
                'type' => 1,
                'iconid' => $v,
                'itemid' => $id,
                'url' => $item_icon_arr[$k],
                'time' => time(),
            );
            $icon_res = M('item_icon')->where(array('type' => 1,'iconid' => $v,'itemid' => $id))->find();
            if($icon_res){
                $res = M('item_icon')->where(array('type' => 1,'iconid' => $v,'itemid' => $id))->save($icon_data);
            }else{
                $res = M('item_icon')->add($icon_data);
            }
        }

        $title = trim(I('post.title',''));
        $title_en = trim(I('post.title_en',''));
        $category_id1 = I('post.category_id1',0);
        $category_id2 = I('post.category_id2',0);
        $isopen=I('post.isopen');
        $image=I('post.image');
        $image_icon=I('post.image_icon');
        $publish_time = I('post.publish_time',date('Y-m-d',time()));
        $sequence = I('post.sequence');
        $detial_title = trim(I('post.detial_title',''));
        $detial_title_en = trim(I('post.detial_title_en',''));
        $detail = trim(I('post.detail',''));
        $detail_en = trim(I('post.detail_en',''));
        $classical = trim(I('post.classical',''));
        $many_image=I('many_image');
        $image2 = $many_image[0];
        $many_images = implode(',',$many_image);
        $content = trim(I('post.content',''));
        $content = $this->formateStr($content);
        $content_en = trim(I('post.content_en',''));
        $content_en = $this->formateStr($content_en);
        if(empty($title)||empty($title_en)||empty($category_id1)||empty($category_id2)||empty($image)||empty($image_icon)||empty($detial_title)||empty($detial_title_en)||empty($image2)||$category_id1=='a'||$category_id2=='a'){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $data = array(
            'title' => $title,
            'title_en' => $title_en,
            'title_news' => $detial_title,
            'title_news_en' => $detial_title_en,
            'image' => $image,
            'image_icon' => $image_icon,
            'image2' => $image2,
            'content' => $content,
            'content_en' => $content_en,
            'detail' => $detail,
            'detail_en' => $detail_en,
            'publish_time' => strtotime($publish_time),
            'isopen' => $isopen,
            'many_image' => $many_images,
            'sequence' => $sequence,
            'time' => time(),
            'cat1'=> $category_id1,
            'cat2'=> $category_id2,
            'classical'=> $classical,
        );

        
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'index');
        }
    }
    //删除产品信息
    public function delete() {
        $model_name = $this->get_model();
        
        $id = I('id');
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 数据根据pid排序
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     * add by qjq
     */
    public function sortt($data,$pid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sortt($data,$v['id'],$level+1);
            }
        }
        return $arr;

    }
    private function formateStr($content){
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
        return $content;
    }


    //封面图信息列表
    public function picindex() {
        $model_name = 'overpicture';
        $count = D($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('time desc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
    }
    public function picadd() {
        $this->display();
    }
    public function picedit() {
        $model_name = 'overpicture';
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $this->row = $row;
        $this->arr = $row_arr;
        $this->id = $id;
        $this->display();

    }
    public function picdelete() {
        $model_name = 'overpicture';
        $id = I('id');
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = '封面图片';
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function picinsert() {
        $model_name = 'overpicture';
        $title = trim(I('post.title',''));
        $title_en = trim(I('post.title_en',''));
        $isopen=I('post.isopen');
        $image=I('post.image');
        $sequence = I('post.sequence');
        $href = trim(I('post.href',''));
        if(empty($image)){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $data = array(
            'title' => $title,
            'title_en' => $title_en,
            'image' => $image,
            'href' => $href,
            'detail' => $detail,
            'isopen' => $isopen,
            'sequence' => $sequence,
            'time' => time()
        );
        $res = D($model_name)->add($data);
        if ($res) {
            $name = "封面图";
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',__URL__.'/'.'picindex');
        } else {
            $this->error('添加失败');
        }
    }

    public function picupdate() {
        $model_name = 'overpicture';
        $id = I('post.id');
        $id_info=M($model_name)->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $title = trim(I('post.title',''));
        $title_en = trim(I('post.title_en',''));
        $isopen=I('post.isopen');
        $image=I('post.image');
        $sequence = I('post.sequence');
       $href = trim(I('post.href',''));
        if(empty($image)){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $data = array(
            'title' => $title,
            'title_en' => $title_en,
            'image' => $image,
            'href' => $href,
            'detail' => $detail,
            'isopen' => $isopen,
            'sequence' => $sequence
        );

        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = "封面图";
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'picindex');
        }
    }

    public function edit_icon(){
        $this->list = M('icon')->order('sequence desc')->select();
        $this->display();
    }

}

?>