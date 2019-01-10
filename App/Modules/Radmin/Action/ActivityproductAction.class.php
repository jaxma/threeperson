<?php

/**
 * 	topos经销商管理系统
 * 活动中心商品
 */
class ActivityproductAction extends CommonAction {
    
    private $model;
    private $type = 0;
    public function __construct() {
        parent::__construct();
        $this->model = M('activity_product');
    }
    
    //产品信息列表
    public function index() {
        $where = [
            'type' => $this->type
        ];
        $count = $this->model->where($where)->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->model->where($where)->order('time desc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }

    //添加产品信息
    public function add() {
        $this->display();
    }

    public function insert() {
        $disc = I('post.disc');
        $disc = stripslashes($disc);
        $disc = preg_replace("/&amp;/", "&", $disc);
        $disc = preg_replace("/&quot;/", "\"", $disc);
        $disc = preg_replace("/&lt;/", "<", $disc);
        $disc = preg_replace("/&gt;/", ">", $disc);
        $image = $this->upload();
        
        $active = I('active');
        if ($active != '1') {
            $active = '0';
        }
        $data = array(
            'image' => $image,
            'name' => I('post.name'),
            'active' => $active,
            'price' => I('post.price_'),
            'time' => time(),
            'state' => I('post.state'),
            'disc' => $disc,
        );
        $res = $this->model->add($data);
        if ($res) {
            $this->add_active_log('活动产品信息添加：'.I('post.name'));
            $this->success('产品信息添加成功', index);
        } else {
            $this->error('产品信息添加失败');
        }
    }

    //编辑产品信息
    public function edit() {
        $id = $_GET['id'];
        $row = $this->model->find($id);
        $this->row = $row;
        $this->display();
    }

    public function update() {
        if ($_FILES['image']['size'] == 0) {
            $image = I('post.old_image');
        } else {
            $image = $this->upload();
        }
        $id = I('post.id');
        $active = I('active');
        if ($active != '1') {
            $active = '0';
        }
        
        $disc = I('post.disc');
        $disc = stripslashes($disc);
        $disc = preg_replace("/&amp;/", "&", $disc);
        $disc = preg_replace("/&quot;/", "\"", $disc);
        $disc = preg_replace("/&lt;/", "<", $disc);
        $disc = preg_replace("/&gt;/", ">", $disc);
        $data = array(
            "id" => $id,
            "image" => $image,
            'name' => I('post.name'),
            'active' => $active,
            'price' => I('post.price_'),
            'disc' => $disc,
            'state' => I('post.state'),
        );
        $res = $this->model->where(['id' => $id])->save($data);
        if ($res === false) {
            
            $this->error("操作失败");
        } else {
            $this->add_active_log('活动中心产品信息修改：'.I('post.name'));
            $this->success("操作成功", index);
        }
    }

    //删除产品信息
    public function delete() {
        $id = $_GET['id'];
        $res = $this->model->where(array('id' => $id))->delete();
        if ($res) {
            $this->add_active_log('活动中心产品信息删除，编号：'.$id);
            $this->success('删除成功', index);
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
        $upload->savePath = './img/activity';
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