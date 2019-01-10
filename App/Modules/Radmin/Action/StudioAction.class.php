<?php

/**
 * 	topos经销商管理系统
 */
class StudioAction extends CommonAction {

    private $studio_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->studio_model = M('studio');
    }
    
    //获取表名
    private function get_model(){
        
        return 'Studio';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '摄影棚';
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
        $sequence = I('post.sequence');
//        $image = $this->upload();
        $image=I('post.image');
        $isopen=I('post.isopen');
        $data = array(
            'news' => $news,
            'isopen' => $isopen,
            'image' => $image,
            'time' => time(),
            'name' => trim(I('post.name')),
            'sequence' => $sequence,
        );

        if(!$image || empty($image)){
            $this->error('请添加摄影棚封面图片后，再提交');
        }
        $res = D($model_name)->add($data);
        if ($res) {
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
        if(!$image || empty($image)){
            $this->error('请添加摄影棚封面图片后，再提交');
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
            'image' => $image,
            'sequence' => $sequence,
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

}

?>