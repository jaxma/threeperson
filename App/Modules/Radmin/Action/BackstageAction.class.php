<?php

/**
 * 	topos经销商管理系统
 */
class BackstageAction extends CommonAction {

    private $backstage_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->backstage_model = M('backstage');
    }
    
    //获取表名
    private function get_model(){
        
        return 'Backstage';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '后台花絮';
    }
    
    //产品信息列表
    public function index() {
        $model_name = $this->get_model();
        
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

    //摄影棚
    public function add() {
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();
        
        $news = I('post.news');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
//        $image = $this->upload();
        $image=I('post.image');
        $isopen=I('post.isopen');
        $sequence = I('post.sequence');
        
        if(!$image || empty($image)){
             $this->error('请添加摄影封面图片后,再提交!');
        }
        
        $data = array(
            'news' => $news,
            'isopen' => $isopen,
            'image' => $image,
            'time' => time(),
            'sequence' => $sequence,
            'name' => trim(I('post.name')),
        );
        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',U(__URL__.'/'.'edit',array('id'=>$res)));
            // $this->redirect(GROUP_NAME . '/backstage/edit',array('id' => $res));
        } else {
            $this->error('添加失败');
        }
    }

    //编辑产品信息
    public function edit() {
        $model_name = $this->get_model();
        
        $id = I('id');
        $row = D($model_name)->find($id);
        // p(D($model_name)->_sql());

        $imgs = M('backstage_img')->where('pid='.$id)->select();
        foreach ($imgs as $k => $v) {
            $images = explode(',', $v['many_image']);
            $imgs[$k]['images'] = $images;
        }
        $this->imgs = $imgs;
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function update() {
        $model_name = $this->get_model();
        
        $id = I('post.id');
        $id_info=D($model_name)->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;
        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $old_image1=$id_info['image1'];
        $image1=I('image1');
        if(strcmp($old_image1,$image1)==0){
            $image1=$image1;
        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image1'];
            @unlink($url);
            $image1 = $image1;
        }

        
        if(!$image || empty($image)){
             $this->error('请添加摄影封面图片后,再提交!');
        }
        if(!$image1 || empty($image1)){
             $this->error('请添加摄影封面展示图片后,再提交!');
        }
        
        $news = I('post.news');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $isopen = I('post.isopen');
        $sequence = I('post.sequence');
        $data = array(
            'name' => trim(I('post.name')),
            'news' => $news,
            'isopen' => $isopen,
            'time' => time(),
            'sequence' => $sequence,
            'image' => $image,
            'image1' => $image1,
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

    public function delete() {
        $model_name = $this->get_model();
        
        $id = I('id');
        $res = D($model_name)->where(array('id' => $id))->delete();
        $img_res = M('backstage_img')->where('pid='.$id)->select();
        foreach ($img_res as $k => $v) {
            M('backstage_img')->where(array('id' => $v['id']))->delete();
            $many_image = explode(',', $v['many_image']);
            foreach ($many_image as $kk => $vv) {
                $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $vv;
                @unlink($url);
            }
        }
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function del_bi() {
        $id = I('id');
        $pid = I('pid');
        $oid_info = M('backstage_img')->where(array('id' => $id))->getField('many_image');
        $oid_info = explode(',', $oid_info);
        foreach ($oid_info as $k => $v) {
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $v;
            @unlink($url);
        }
        $res = M('backstage_img')->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function img() {
        $id = I('id');//backstage_img
        $pid = I('pid');//backstage
        $row = M('backstage_img')->find($id);
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
        $this->row = $row;
        $this->arr = $row_arr;
        $this->id = $id;
        $this->pid = $pid;
        $this->display();
    }

    public function image_update(){
        
        $id = I('id');//backstage_img
        $pid = I('pid');//backstage
        $many_image=I('many_image');
        $many_images=implode(',',$many_image);
        if(empty($many_image)){
            M('backstage_img')->where(array('id' => $id))->delete();
            $this->error("图片不能为空！");
        }
        $sequence=I('sequence');
        $status=I('status');
        $data = array(
            'many_image' => $many_images,
            'sequence' => $sequence,
            'status' => $status,
            'add_time' => time()
        );
        $res = M('backstage_img')->where(array('id' => $id))->save($data);
        if(!$res){
            $data = array(
                'many_image' => $many_images,
                'sequence' => $sequence,
                'status' => $status,
                'add_time' => time(),
                'pid'=>$pid
            );
            $res = M('backstage_img')->add($data);
        }
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->redirect(__URL__.'/'.'edit',array('id' => $pid));
        }
    }

}

?>