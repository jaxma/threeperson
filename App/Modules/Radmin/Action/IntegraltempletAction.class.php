<?php

/**
 * 	雨丝燕经销商管理系统
 */
class IntegraltempletAction extends CommonAction {

    private $model;
    private $cat_model;
    public function _initialize() {
        parent::_initialize();
        $this->model = M('integraltemplet');
        $this->cat_model = M('integraltemplet_category');
    }

    //产品信息列表
    public function index() {
        $count = $this->model->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->model->order('time desc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }

    //添加产品信息
//    public function add() {
//        $this->level_num = C('LEVEL_NUM');
//        $this->level_name = C('LEVEL_NAME');
//        $this->cats = $this->cat_model->select();
//        $this->display();
//    }
//
//    public function insert() {
//        $disc = I('post.disc');
//        $disc = stripslashes($disc);
//        $disc = preg_replace("/&amp;/", "&", $disc);
//        $disc = preg_replace("/&quot;/", "\"", $disc);
//        $disc = preg_replace("/&lt;/", "<", $disc);
//        $disc = preg_replace("/&gt;/", ">", $disc);
//        $image = $this->upload();
//        
//        $active = I('active');
//        if ($active != '1') {
//            $active = '0';
//        }
//        
//        $data = array(
//            'image' => $image,
//            'name' => I('post.name'),
//            'active' => $active,
//            'price' => I('post.price'),
//            'price1' => I('post.price1'),
//            'price2' => I('post.price2'),
//            'price3' => I('post.price3'),
//            'price4' => I('post.price4'),
//            'price5' => I('post.price5'),
//            'price6' => I('post.price6'),
//            'price7' => I('post.price7'),
//            'price8' => I('post.price8'),
//            'price9' => I('post.price9'),
//            'price10' => I('post.price10'),
//            'time' => time(),
//            'disc' => $disc,
//            'state' => I('post.state'),
//            'category_id' => I('post.category_id')
//        );
//        $res = $this->model->add($data);
//        if ($res) {
//            $this->add_active_log('产品信息添加：'.I('post.name'));
//            $this->success('产品信息添加成功', index);
//        } else {
//            $this->error('产品信息添加失败');
//        }
//    }

    //编辑产品信息
    public function edit() {
        $id = I('id');

        $row = [];
        if( !empty($id) ){
            $row = $this->model->find($id);
        }

        $this->row = $row;
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->cats = $this->cat_model->select();
        $this->display();
    }

    //产品信息编辑
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
            'integral'  =>  I('integral'),
            'price' => I('post.price'),
            'price1' => I('post.price1'),
            'price2' => I('post.price2'),
            'price3' => I('post.price3'),
            'price4' => I('post.price4'),
            'price5' => I('post.price5'),
            'price6' => I('post.price6'),
            'price7' => I('post.price7'),
            'price8' => I('post.price8'),
            'price9' => I('post.price9'),
            'price10' => I('post.price10'),
            'disc' => $disc,
            'state' => I('post.state'),
            'category_id' => I('post.category_id'),
        );

        if( !empty($id) ){
            $data['id'] =   $id;
            $res = $this->model->save($data);
        }
        else{
            $data['time'] = time();
            $data['category_id'] = I('post.category_id');
            $res = $this->model->add($data);
        }

        if ($res === false) {

            $this->error("操作失败");
        } else {
            $this->add_active_log('产品信息编辑：'.I('post.name'));
            $this->success("操作成功");
        }
    }

    //删除产品信息
    public function delete() {
        $id = $_GET['id'];
        $res = $this->model->where(array('id' => $id))->delete();
        if ($res) {
            $this->add_active_log('产品信息删除，编号：'.$id);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }



    //显示二维码
    public function show() {
        $this->level_name = C('LEVEL_NAME');
        $this->ym_domain = C('YM_DOMAIN');

        $level = I('level');

        $this->level = $level;
        $this->display();
    }

//--------*********产品分类*****------------
//获取中文名字
    private function get_category_name()
    {

        return '积分商城产品分类';
    }
    //产品分类列表
    public function category_index() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();

        $count =  $this->cat_model->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres =  $this->cat_model->select();
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
        $reduction_category = M('integraltemplet_category');
        $cate = $reduction_category->select();
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);
        $this->display();
    }

    public function category_insert()
    {
//        $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'name' => trim(I('post.name')),
            'image' =>$image,
            'statu' => I('post.statu'),
            'pid' => I('post.pid'),
            'time' => time(),
            'active' => I('post.active')
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $name=$this->get_category_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功');
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

        $id_info= $this->cat_model->where(array('id' => $id))->find();
        $old_image=$id_info['image'];

        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $statu = I('statu');
        $statu = !empty($statu)?1:0;


        $data = array(
            'name' => trim(I('post.name')),
            'image' => $image,
            'statu' => $statu,
            'pid' =>I('post.pid'),
            'time' => time(),
            'active' => I('post.active')
        );

        $res =  $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $name=$this->get_category_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->error("操作失败");
        } else {
            $this->success("操作成功");
        }
    }


    //删除
    public function category_delete()
    {

        $id = I('id');
        $model_info =$this->cat_model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__. $model_info[0]['image'];
        @unlink($url);
        $res = $this->cat_model->delete($id);

        if ($res) {
            $name=$this->get_category_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
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

        $upload->savePath = './upload/integraltemplet/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $imaget = $info[0]['savepath'] . $info[0]['savename'];
        $image=substr($imaget, 1);
        return $image;

    }


//----------***********-----------
    //显示模板列表
    //获取中文名字
    private function get_product_name()
    {

        return '积分商城产品模板';
    }
    public function product_index(){
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $name=trim(I('get.name'));
        $active=trim(I('get.active'));

        if(!empty($name)){
            $condition=[
                'name'=>$name,
            ];
        }
        if(!empty($active)){
            $condition=[
                'active'=>$active,
            ];
        }

        $count =  $this->model->where($condition)->count('id');

        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres =  $this->model->where($condition)->order('id desc')->limit($limit)->select();
            //排序
            $list=$Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示

            //联表查询
            $category_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['category_id']])) {
                    $ids[$v['category_id']] = $v['category_id'];
                }
            }

            //将取出来的id在另外的表根据id查询
            $cats = M('integraltemplet_category')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $category_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['category_name'] = $category_info[$v['category_id']]['name'];
            }


            $this->assign('list', $list);
            $this->assign("page", $page);

        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();

    }
    //添加产品信息
    public function product_add() {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $reduction_category = M('integraltemplet_category');
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

//        $image = $this->upload();
        $image=I('post.image');
        $many_image=I('many_image');
        $many_images=implode(',',$many_image);

        $data = array(
            'image' =>$image,
            'name' => trim(I('post.name')),
            'category_id' => I('post.category_id'),
            'active' => I('post.active'),
            'statu' => I('post.statu'),
            'mail_fee' => I('post.mail_fee'),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time(),
            'integral'  =>  trim(I('integral')),
            'price' => trim(I('post.price')),
            'price1' => trim(I('post.price1')),
            'price2' => trim(I('post.price2')),
            'price3' => trim(I('post.price3')),
            'price4' => trim(I('post.price4')),
            'price5' => trim(I('post.price5')),
            'price6' => trim(I('post.price6')),
            'price7' => trim(I('post.price7')),
            'price8' => trim(I('post.price8')),
            'price9' => trim(I('post.price9')),
            'price10' => trim(I('post.price10')),
            'many_image' => $many_images,

        );

        $res = $this->model->add($data);
        if ($res) {
            $name = $this->get_product_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',__URL__.'/'.'product_index');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function product_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $id = $_GET['id'];
        $row = $this->model->find($id);
        $list =  $this->model->select();

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

        $category_info = M('integraltemplet_category');
        $dis_category = $category_info->select();
        $dis_category=$Team->sortt($dis_category);
        $this->assign('dis_category', $dis_category);

        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->arr = $row_arr;
        $this->list = $list;
        $this->row = $row;

        $this->id = $id;
        $this->display();
    }

    public function product_update()
    {
        $id = I('post.id');

        $id_info= $this->model->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $many_image=I('many_image');
        $many_images= implode(',', $many_image);

//        if ($_FILES['image']['size'] == 0) {
//            $image = I('post.old_image');
//        } else {
//            $model_info =$this->cat_model->where(array('id'=>$id))->select();
//            $url = $_SERVER['DOCUMENT_ROOT'] . $model_info[0]['image'];
//            @unlink($url);
//            $image = $this->upload();
//        }

        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);

        $data = array(
            'image' =>$image,
            'name' => trim(I('post.name')),
            'category_id' => I('post.category_id'),
            'active' => I('post.active'),
            'statu' => I('post.statu'),
            'mail_fee' => I('post.mail_fee'),
            'description'=>I('post.description'),
            'content' =>$content,
            'disc'  =>  $content,
            'pid' => I('post.pid'),
            'time' => time(),
            'integral'  =>  trim(I('integral')),
            'price' => trim(I('post.price')),
            'price1' => trim(I('post.price1')),
            'price2' => trim(I('post.price2')),
            'price3' => trim(I('post.price3')),
            'price4' => trim(I('post.price4')),
            'price5' => trim(I('post.price5')),
            'price6' => trim(I('post.price6')),
            'price7' => trim(I('post.price7')),
            'price8' => trim(I('post.price8')),
            'price9' => trim(I('post.price9')),
            'price10' => trim(I('post.price10')),
            'many_image' => $many_images,
        );

        $res = $this->model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_product_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'product_index');
        }
    }

    //删除
    public function product_delete()
    {
        $id = I('id');

        $model_info =$this->model->where('id='.$id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $model_info[0]['image'];
        @unlink($url);
        $res = $this->model->delete($id);

        if ($res) {
            $name = $this->get_product_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }



    //---------------***广告模块**--------------
    //获取表名
    private function get_model()
    {

        return 'integral_advert';
    }
    //获取广告中文名字
    private function get_adv_name()
    {

        return '积分商城广告';
    }
    //列表页
    public function advert_index(){
        $model_name = M('integral_advert');
        $count =$model_name->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $model_name->order('id desc')->limit($limit)->select();

            //联表查询
            $material_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['integraltemplet_category_id']])) {
                    $ids[$v['integraltemplet_category_id']] = $v['integraltemplet_category_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $mats = M('integraltemplet_category')->where(['id' => ['in', $ids]])->select();
            //取出数据
            foreach ($mats as $v) {
                $material_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['integraltemplet_category_name'] = $material_info[$v['integraltemplet_category_id']]['name'];
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

    //添加
    public function advert_add(){
        $info=M('integraltemplet_category')->field('id,name,pid')->select();
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $list=$Team->sortt($info);
        $this->list=$list;

        $this->display();
    }

    public function advert_insert(){

        $model_name = $this->get_model();
        // $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'name' => trim(I('post.name')),
            'image' => $image,
            'status' => I('post.status'),
            'sequence'=>trim(I('post.sequence')),
            'time' => time(),
            'type' =>I('post.type'),
            'integraltemplet_category_id'=>I('post.integraltemplet_category_id'),
        );

        $res = M($model_name)->add($data);
        if ($res) {
            $name=$this->get_adv_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function advert_edit(){

        $id = $_GET['id'];
        $row=M('integral_advert')->where(array('id'=>$id))->find();
        $lists=M('integraltemplet_category')->select();
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $list=$Team->sortt($lists);
        $this->list=$list;
        $this->row = $row;
        $this->display();
    }
    public function advert_update(){

        $id = I('post.id');

        $id_info= M('integral_advert')->where(array('id' => $id))->find();
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
            'integraltemplet_category_id'=>I('post.integraltemplet_category_id'),
        );


        $res = M('integral_advert')->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name=$this->get_adv_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功");
        }
    }

    //删除
    public function advert_delete()
    {
        $id = I('id');
        $businessl_info =M('integral_advert')->where(array('id'=>$id))->select();
        $url = $_SERVER['DOCUMENT_ROOT']  .__ROOT__ .  $businessl_info[0]['image'];
        @unlink($url);
        $res = M('integral_advert')->delete($id);
        if ($res) {
            $name=$this->get_adv_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}

?>