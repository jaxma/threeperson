<?php

/**
 * 	topos经销商管理系统
 */
class ReductionAction extends CommonAction {

    private $model;
    private $cat_model;
    public function _initialize() {
        parent::_initialize();
        $this->model = M('reduction_product');
        $this->cat_model = M('reduction_category');
    }

//--------*********产品分类*****------------

    //产品分类列表
    public function category_index() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $count =  $this->cat_model->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres =  $this->cat_model->limit($limit)->select();
            //排序
            $list=$Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }

    //添加分类
    public function category_add() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $reduction_category = M('reduction_category');
        $cate = $reduction_category->select();
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);
            $this->display();
    }

    public function category_insert()
    {
        $image = $this->upload();
        $data = array(
            'name' => I('post.name'),
            'image' =>$image,
            'pid' => I('post.pid'),
            'time' => time()
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $this->success('添加成功', category_index);
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function category_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $id = $_GET['id'];
        $row = $this->cat_model->find($id);
        $listres =  $this->cat_model->select();
        $list=$Team->sortt($listres);


        $this->list = $list;
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function category_update()
    {
        $id = I('post.id');
        if ($_FILES['image']['size'] == 0) {
            $image = I('post.old_image');
        } else {
            $model_info =$this->cat_model->where(array('id'=>$id))->select();
            $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
            @unlink($url);
            $image = $this->upload();
        }

        $data = array(
            'name' => I('post.name'),
            'image' => $image,
            'pid' =>I('post.pid'),
            'time' => time()
        );

        $res =  $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功", category_index);
        }
    }


    //删除
    public function category_delete()
    {

        $id = I('id');
        $model_info =$this->cat_model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
        @unlink($url);
        $res = $this->cat_model->delete($id);

        if ($res) {
            $this->success('删除成功', category_index);
        } else {
            $this->error('删除失败');
        }
    }



        public function upload()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/reduction/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = $info[0]['savepath'] . $info[0]['savename'];
        return $image;

    }

    //--------*********产品模板信息*****------------

    //显示模板列表
    public function product_index(){
        $this->display();
    }
    //添加产品信息
    public function product_add() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $reduction_category = M('reduction_category');
        $cate = $reduction_category->select();
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);

        $this->display();
    }

    public function  product_insert()
    {
        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);

        $image = $this->upload();
        $data = array(
            'image' =>$image,
            'name' => I('post.name'),
            'category_id' => I('post.category_id'),
            'status' => I('post.status'),
            'price' => I('post.price'),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time()
        );
        $res = $this->model->add($data);
        if ($res) {
            $this->success('添加成功', product_index);
        } else {
            $this->error('添加失败');
        }
    }

}

?>