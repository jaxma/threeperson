<?php

/**
 * 	topos经销商管理系统
 */
class GoodsAction extends CommonAction {

    
    //获取表名
    private function get_model(){
        
        return 'Goods';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '产品';
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

    //添加产品信息
    public function add() {
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();
        
        $news = I('post.disc');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
//        $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'presents' => I('post.presents'),
            'news' => $news,
            'time' => time()
        );
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
        $id_info=M('Goods')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $news = I('post.disc');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'presents' => I('post.presents'),
            'news' => $news,
            'time' => time()
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






    //---------------***轮播图模块**--------------
    //获取表名
    private function get_advert_model()
    {

        return 'goods_advert';
    }
    //列表页
    public function advert_index(){
        $model_name = $this->get_advert_model();
        $count = M($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('id desc')->limit($limit)->select();

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

    //添加
    public function advert_add(){
        $this->display();
    }

    public function advert_insert(){

        $model_name = $this->get_advert_model();
        // $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'name' => trim(I('post.name')),
            'image' => $image,
            'status' => I('post.status'),
            'sequence'=>trim(I('post.sequence')),
            'time' => time(),
            'type' =>I('post.type'),
        );

        $res = M($model_name)->add($data);
        if ($res) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function advert_edit(){
        $id = $_GET['id'];
        $row=M('goods_advert')->where(array('id'=>$id))->find();
        $this->row = $row;
        $this->display();
    }
    public function advert_update(){
        $id = I('post.id');
        $id_info= M('goods_advert')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }
//        if ($_FILES['image']['size'] == 0) {
//            $image = I('post.old_image');
//        } else {
//            $business_info =M('integral_advert')->where('id=' . $id)->select();
//            $url = $_SERVER['DOCUMENT_ROOT'] . $business_info[0]['image'];
//            unlink($url);
//            $image = $this->upload();
//        }

        $data = array(
            'id'=>$id,
            'name' => trim(I('post.name')),
            'image' => $image,
            'status' => I('post.status'),
            'sequence'=>trim(I('post.sequence')),
            'time' => time(),
            'type' =>I('post.type'),
        );

        $res = M('goods_advert')->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功");
        }
    }

    //删除
    public function advert_delete()
    {
        $id = I('id');
        $businessl_info =M('goods_advert')->where(array('id'=>$id))->select();
        $url = $_SERVER['DOCUMENT_ROOT']  .__ROOT__ .  $businessl_info[0]['image'];
        @unlink($url);
        $res = M('goods_advert')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * +-------------------------------------------------
     * 上传图片
     * +-------------------------------------------------
     * @param string $name
     * +-------------------------------------------------
     * @return string $info(中文提示)
     * +-------------------------------------------------
     */
    function upload() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize = 1048576;
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型
        $upload->savePath = './img/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {

            $info = $upload->getUploadFileInfo();
            $image = $info[0]['savepath'] . $info[0]['savename'];
            return __ROOT__ . substr($image, 1);
        }
    }

}

?>