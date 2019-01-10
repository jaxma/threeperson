<?php

/**
 *    topos经销商管理系统
 */
class TempletAction extends CommonAction
{

    private $model;
    private $cat_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = M('templet');
        $this->cat_model = M('templet_category');
    }

    //显示模板列表
    //获取中文名字
    private function get_product_name()
    {

        return '代理商城产品模板';
    }
    public function product_index()
    {
        import('Lib.Action.Team', 'App');
        $Team = new Team();

        $condition = array();

        $name = trim(I('get.name'));
        $price = trim(I('get.price'));
        $category_id1 = I('get.category_id1');
        $category_id2 = I('get.category_id2');
        $active = I('get.active');

        if ($name != null) {
            $condition['name'] = ['like', '%' . $name . '%'];
        }

        if (!empty($price)) {
            $condition['price'] = $price;
        }

        if (!empty($category_id1)) {
            $condition['category_id'] = $category_id1;
        }

        if (!empty($category_id2)) {
            $condition['category_id'] = $category_id2;
        }

        if ( $active != null ) {
            $condition['active'] = $active;
        }

        $dis_templet_List = $this->cat_model->select();
        $dis_templet_List1 = $Team->sortt($dis_templet_List);
        $condition_temp = array();
        if (!empty($dis_templet_List1)) {
            foreach ($dis_templet_List1 as $k_tem => $v_tem) {
                $v_tem_id = $v_tem['id'];
                $condition_temp[$v_tem_id] = $v_tem;
            }
        }
        $this->assign('dis_templet_List1', $dis_templet_List1);

        $count = $this->model->where($condition)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;

            $list = $this->model->where($condition)->limit($limit)->order('id desc')->select();
            //排序
//            $list=$Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示

            //联表查询
            $category_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['template_id']])) {
                    $ids[$v['template_id']] = $v['template_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $cats = M('shipping_goods_shipping_template')->where(['id' => ['in', $ids]])->select();
            //取出数据
            foreach ($cats as $v) {
                $category_info[$v['id']] = $v;
            }
            foreach ($list as $k => $v) {
                $list[$k]['template_name'] = $category_info[$v['template_id']]['template_name'];
            }

            //联表查询
            $category_info_two = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($idss[$v['category_id']])) {
                    $idss[$v['category_id']] = $v['category_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $catss =  $this->cat_model->where(['id' => ['in', $idss]])->select();
            //取出数据
            foreach ($catss as $v) {
                $category_info_two[$v['id']] = $v;
            }
            foreach ($list as $k => $v) {
                $list[$k]['category_name'] = $category_info_two[$v['category_id']]['name'];
            }
            $list=$this->get_related_data($list,'shipping_goods_shipping_template','template_id');

            $this->assign('list', $list);

            $this->assign("page", $page);
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();

    }

    //属性
    //获取属性组合
    function get_properties_value_combination() {
        $product = json_decode($_GET['product'],true);
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        $res = $sku->get_properties_value_combination($product);
        $this->ajaxReturn($res, 'JSON');
    }

    //添加商品信息
    public function product_add()
    {
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');

        $reduction_category = M('templet_category');
        $cate = $reduction_category->select();
        $cateres = $Team->sortt($cate);

        $shipping_goods_shipping_template=M('shipping_goods_shipping_template');
        $info=$shipping_goods_shipping_template->field('id,template_name')->select();

        $this->assign('cateres', $cateres);

        $this->assign('info',$info);
        $this->uedit_name='disc';

        //属性
        $this->properties = M('templet_property')->select();
        $this->product = [];
        $this->propertyPrices = [];

        $this->display();
    }

    public function product_insert()
    {
//        var_dump($_POST);die;
        $disc = I('post.disc');
        $disc = stripslashes($disc);
        $disc = preg_replace("/&amp;/", "&", $disc);
        $disc = preg_replace("/&quot;/", "\"", $disc);
        $disc = preg_replace("/&lt;/", "<", $disc);
        $disc = preg_replace("/&gt;/", ">", $disc);

        $category_id1 = I('post.category_id1');
        $category_id2 = I('post.category_id2');
        $category_id3 = I('post.category_id3');
        if ($category_id2 == "a") {
            $category_id = $category_id1;
        } elseif ($category_id3 == "a") {
            $category_id = $category_id2;
        } else {
            $category_id = $category_id3;
        }

        //属性
        $has_property = 0;
        $templet = json_decode($_POST['ProductForm'], true);
        if ($templet['stock']>0) {
            $quantity = $templet['stock'];
            $has_property = 1;
        } else {
            $quantity = I('quantity');
        }


//        $image = $this->upload();
        $image = I('image');
        $many_image=I('post.many_image');
        $many_images= implode(',', $many_image);
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }

        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'category_id' => $category_id,
            'active' => trim(I('post.active')),
            'sequence' =>$sequence,
            'status' => trim(I('post.status')),
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
            'state' => I('post.state'),
            'disc' => $disc,
            'pid' => trim(I('post.pid')),
            'time' => time(),
            'template_id' => I('post.template_id'),
            'many_image'=>$many_images,
            'quantity' => $quantity,
            'has_property' =>$has_property,
            'product_parameter'=>trim(I('product_parameter')),

        );


        if (($category_id2 == 'a' && $category_id3 == 'a') || $category_id3 != "a") {
            $res = $this->model->add($data);
            if ($res) {

                //属性
                //保存商品属性库存
                import('Lib.Action.Sku','App');
                $sku = new Sku();
                $sku->save_templet_info($templet, $res);
                $name = $this->get_product_name();
                $this->add_active_log('添加'.$name.'信息');
                $this->success('添加成功',__URL__.'/'.'product_index');

            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('添加失败,二级分类不能添加产品');
        }

    }

    //编辑
    public function product_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $id = $_GET['id'];
        $row = $this->model->find($id);

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
        $category_info = M('templet_category');
        $dis_category = $category_info->select();
        $list = $Team->sortt($dis_category);

        $shipping_goods_shipping_template=M('shipping_goods_shipping_template');
        $info=$shipping_goods_shipping_template->field('id,template_name')->select();

        //属性
        //商品属性库存
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        $product = $sku->init_properties($row);
        if (!$product) {
            $product = [];
            $propertyPrices = [];
        } else {
            $propertyPrices = $product['propertyPrices'];
        }
        $this->product = $product;
        $this->propertyPrices = $propertyPrices;
        $this->properties = M('templet_property')->select();
        $this->has_property = $row['has_property'];

        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');

        $this->arr = $row_arr;
        $this->list = $list;
        $this->row = $row;
        $this->id = $id;

        $this->assign('info',$info);
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

        $disc = I('post.disc');
        $disc = stripslashes($disc);
        $disc = preg_replace("/&amp;/", "&", $disc);
        $disc = preg_replace("/&quot;/", "\"", $disc);
        $disc = preg_replace("/&lt;/", "<", $disc);
        $disc = preg_replace("/&gt;/", ">", $disc);

        $category_id1 = I('post.category_id1');
        $category_id2 = I('post.category_id2');
        $category_id3 = I('post.category_id3');
        if ($category_id2 == "a") {
            $category_id = $category_id1;
        } elseif ($category_id3 == "a") {
            $category_id = $category_id2;
        } else {
            $category_id = $category_id3;
        }

        //属性
        $has_property = 0;
        $this->level_name = C('LEVEL_NAME');
        $templet = json_decode($_POST['ProductForm'], true);
        if ($templet['stock']>0) {
            $quantity = $templet['stock'];
            $has_property = 1;
        } else {
            $quantity = I('quantity');
        }
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }
        $parameter=trim(I('product_parameter'));
        $price_way=trim(I('price_way'));
        if($price_way == 1){
            $product_parameter=1;
        }else{
            $product_parameter=$parameter;
        }

        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'category_id' => $category_id,
            'active' => trim(I('post.active')),
            'sequence' => $sequence,
            'state' => I('post.state'),
            'disc' => $disc,
            'pid' => I('post.pid'),
            'time' => time(),
            'template_id' => I('post.template_id'),
            'many_image' => $many_images,
            'quantity' => $quantity,
            'has_property' => $has_property,
            'product_parameter'=>$product_parameter,

        );
        if (isset($_POST['price'])) {
            $data['price'] = $_POST['price'];
        }
        for($i=0;$i<11;$i++) {
            if (isset($_POST["price$i"])) {
                $data["price$i"] = $_POST["price$i"];
            }
        }
        setLog(json_encode($data));
        if (($category_id2 == 'a' && $category_id3 == 'a') || $category_id3 != "a") {
            $res = $this->model->where(array('id' => $id))->save($data);
            if ($res) {
                //属性
                //保存商品属性库存
                import('Lib.Action.Sku','App');
                $sku = new Sku();
                $sku->save_templet_info($templet, $id, I('post.show_stock'));
                $name = $this->get_product_name();
                $this->add_active_log('编辑'.$name.'信息');
                $this->success('操作成功',__URL__.'/'.'product_index');

            } else {
                $this->error('操作失败');
            }
        } else {
            $this->error('操作失败,二级分类不能添加产品');
        }


    }

    //删除
    public function product_delete()
    {
        $id = I('id');

        $model_info = $this->model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__ .  $model_info[0]['image'];
        @unlink($url);
        $res = $this->model->delete($id);

        if ($res) {
            //属性
            //删除属性/库存
            import('Lib.Action.Sku','App');
            $sku_obj = new Sku();
            $sku_obj->delete_properties($id);
            $name = $this->get_product_name();
            $this->add_active_log('删除'.$name.'信息');
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
    public function upload()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/templet/';// 设置附件上传目录

        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        return $image;

    }

    //显示二维码
    public function show()
    {
        $this->level_name = C('LEVEL_NAME');
        $this->ym_domain = C('YM_DOMAIN');

        $level = I('level');
        $can_pick = I('can_pick');//是否可自选级别

        $this->can_pick = $can_pick;
        $this->level = $level;
        $this->display();
    }

    //--------*********商品分类*****------------

    //商品分类列表
    //获取中文名字
    private function get_category_name()
    {

        return '代理商城产品分类';
    }
    public function category_index()
    {
        import('Lib.Action.Team', 'App');
        $Team = new Team();

        $count = $this->cat_model->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $listres = $this->cat_model->select();
            //排序
            $list = $Team->sortt($listres);
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);

//            $this->assign("page", $page);
        }
        $this->display();
    }

    //添加分类
    public function category_add()
    {
        $p_id=I('get.p_id');
        $c_id=I('get.c_id');
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $reduction_category = M('templet_category');
        $cate = $reduction_category->select();
        $cateres = $Team->sortt($cate);
        $this->assign('cateres', $cateres);
        $this->p_id=$p_id;
        $this->c_id=$c_id;
        $this->display();
    }

    public function category_insert()
    {

//        $image = $this->upload();
        $image=I('post.image');

        $pid1 = trim(I('post.pid1'));
        $pid2 = trim(I('post.pid2'));
        if ($pid1 == "a" && $pid2 == "a") {
            $pid = 0;
        } elseif ($pid2 == "a" || $pid2 == '') {
            $pid = $pid1;
        } elseif ($pid1 != "a" && $pid2 != "a") {
            $pid = $pid2;
        }
        $name=trim(I('post.name'));
        if(empty($name)){
            $this->error('名称不能为空！');die;
        }
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }
        $data = array(
            'name' => $name,
            'image' => $image,
            'pid' => $pid,
            'sequence' => $sequence,
            'add_time' => time()
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $name = $this->get_category_name();
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
        $Team = new Team();

        $id = $_GET['id'];
        $row = $this->cat_model->find($id);
        $listres = $this->cat_model->select();
        $list = $Team->sortt($listres);


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


        $condition_one=[
            'id' => $id,
        ];
        $condition_two=[
            'pid' => $id,
        ];
        $info= $this->cat_model->where($condition_one)->find();
        $info_count=$this->cat_model->where($condition_two)->count();

        $info_pid=$info['pid'];
        $pid1 = trim(I('post.pid1'));
        $pid2 = trim(I('post.pid2'));

        if($info_pid == 0){
            if($info_count){
                if($pid1 != "a"){
                    $this->error('原分类下面还有子分类，禁止进行此操作');die;
                }
            }
        }
        if ($pid1 == "a" && $pid2 == "a"  ) {
            $pid = 0;
        } elseif ($pid2 == "a") {
            $pid = $pid1;
        } elseif ($pid1 != "a" && $pid2 != "a") {
            $pid = $pid2;
        }
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }
        $data = array(
            'name' => I('post.name'),
            'image' => $image,
            'pid' => $pid,
            'sequence' => $sequence,
            'add_time' => time()
        );

        $res = $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_category_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功");
        }

    }


    //删除
    public function category_delete()
    {

        $id = I('id');
        $model_info = $this->cat_model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__ .  $model_info[0]['image'];
        @unlink($url);
        $res = $this->cat_model->delete($id);

        if ($res) {
            $name = $this->get_category_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //*********************旧版*******************
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

//    //编辑产品信息
//    public function edit() {
//        $id = I('id');
//
//        $row = [];
//        if( !empty($id) ){
//            $row = $this->model->find($id);
//        }
//
//        $this->row = $row;
//        $this->level_num = C('LEVEL_NUM');
//        $this->level_name = C('LEVEL_NAME');
//        $this->cats = $this->cat_model->select();
//        $this->display();
//    }
//
//    //产品信息编辑
//    public function update() {
//        if ($_FILES['image']['size'] == 0) {
//            $image = I('post.old_image');
//        } else {
//            $image = $this->upload();
//        }
//        $id = I('post.id');
//        $active = I('active');
//        if ($active != '1') {
//            $active = '0';
//        }
//
//        $disc = I('post.disc');
//        $disc = stripslashes($disc);
//        $disc = preg_replace("/&amp;/", "&", $disc);
//        $disc = preg_replace("/&quot;/", "\"", $disc);
//        $disc = preg_replace("/&lt;/", "<", $disc);
//        $disc = preg_replace("/&gt;/", ">", $disc);
//        $data = array(
//            "id" => $id,
//            "image" => $image,
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
//            'rebate1_level1' => I('post.rebate1_level1'),
//            'rebate2_level1' => I('post.rebate2_level1'),
//            'rebate3_level1' => I('post.rebate3_level1'),
//            'rebate1_level2' => I('post.rebate1_level2'),
//            'rebate2_level2' => I('post.rebate2_level2'),
//            'rebate1_level3' => I('post.rebate1_level3'),
//            'rebate2_level3' => I('post.rebate2_level3'),
//            'rebate1' => I('post.rebate1'),
//            'rebate2' => I('post.rebate2'),
//            'disc' => $disc,
//            'state' => I('post.state'),
//            'category_id' => I('post.category_id'),
//        );
//
//        if( !empty($id) ){
//            $data['id'] =   $id;
//            $res = $this->model->save($data);
//        }
//        else{
//            $data['time'] = time();
//            $data['category_id'] = I('post.category_id');
//            $res = $this->model->add($data);
//        }
//
//        if ($res === false) {
//
//            $this->error("操作失败");
//        } else {
//            $this->add_active_log('产品信息编辑：'.I('post.name'));
//            $this->success("操作成功", index);
//        }
//    }
//
//    //删除产品信息
//    public function delete() {
//        $id = $_GET['id'];
//        $res = $this->model->where(array('id' => $id))->delete();
//        if ($res) {
//            $this->add_active_log('产品信息删除，编号：'.$id);
//            $this->success('删除成功', index);
//        } else {
//            $this->error('删除失败');
//        }
//    }


    //产品分类
    public function templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $cats = $this->cat_model->order('id desc')->select();
        //一级分类
        foreach ($cats as $cat) {
            if ($cat['pid'] == 0) {
                $one[] = $cat;
                $one_id[] = $cat['id'];
            }
        }
        //二级分类关联一级分类
        foreach ($cats as $cat) {
            if (in_array($cat['pid'], $one_id)) {
                $two[$cat['pid']] = $cat;
                $two_id[] = $cat['id'];
            }
        }
        //三级分类关联二级分类
        foreach ($cats as $cat) {
            if (in_array($cat['pid'], $two_id)) {
                $three[$cat['pid']] = $cat;
            }
        }

        //判断是否有子分类
        foreach ($one as $k => $v) {
            $one[$k]['has_child'] = 0;
            if (isset($two[$v['id']])) {
                $one[$k]['has_child'] = 1;
            }
        }
        foreach ($two as $k => $v) {
            $two[$k]['has_child'] = 0;
            if (isset($three[$v['id']])) {
                $two[$k]['has_child'] = 1;
            }
        }
        $result = [
            'one' => $one,
            'two' => $two,
            'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);

    }

    //获取产品子分类
    public function get_son_templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id=I('id');
        $pid = I('pid');
        //全部
        if ($pid == -1) {
            $one_cats = $this->cat_model->order('id desc')->where(['pid' => 0])->select();
            foreach ($one_cats as $cat) {
                $one_ids[] = $cat['id'];
            }
            $where['pid'] = ['in', $one_ids];
        } else {
            $where['pid'] = $pid;
        }

        //二级分类
        $two = $this->cat_model->order('id desc')->where($where)->select();
        foreach ($two as $v) {
            $two_ids[] = $v['id'];
        }
        //三级分类关联二级分类
        $cats = $this->cat_model->order('id desc')->where(['pid' => ['in', $two_ids]])->select();
        foreach ($cats as $cat) {
            $three[$cat['pid']][] = $cat;
        }
        //判断是否有子分类
        foreach ($two as $k => $v) {
            $two[$k]['has_child'] = 0;
            if (isset($three[$v['id']])) {
                $two[$k]['has_child'] = 1;
            }
        }
        $result = [
            'two' => $two,
            'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //获取3级列表的名称
    public function get_category_ajax()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $id = I('post.id');
//      $id=10;
        $condition = [
            'id' => $id,
        ];
        $is_two_cat = false;
        $info_one = $this->cat_model->where($condition)->find();
        $info_two_pid = $info_one['pid'];
        if ($info_two_pid != 0) {

            $info_two = $this->cat_model->where(array('id' => $info_two_pid))->find();
            $info_three_pid = $info_two['pid'];
            if ($info_three_pid != 0) {
                $info_three = $this->cat_model->where(array('id' => $info_three_pid))->find();
            } else {
                $is_two_cat = true;
            }
        }

        if ($info_two == "" && $info_three == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_two,
                'info_two' => $info_three,
                'is_two_cat' => $is_two_cat
            ];
        } elseif ($info_three == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_two,
                'info_two' => $info_three,
                'is_two_cat' => $is_two_cat
            ];
        } else {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_three,
                'info_two' => $info_two,
                'is_two_cat' => $is_two_cat
            ];
        }

        $this->ajaxReturn($return_result);
    }


    //获取3级列表的名称
    public function get_product_ajax()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $id = I('id');
//    $id=1;
        $condition = [
            'id' => $id,
        ];
        $info = $this->model->where($condition)->find();
        $info_category_id['id'] = $info['category_id'];

        $info_three = $this->cat_model->where($info_category_id)->find();
        $info_two_pid = $info_three['pid'];
        if ($info_two_pid != 0) {
            $info_two = $this->cat_model->where(array('id' => $info_two_pid))->find();
            $info_onee_pid = $info_two['pid'];
            if ($info_onee_pid) {
                $info_one = $this->cat_model->where(array('id' => $info_onee_pid))->find();
            }
        }
        if ($info_two == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_three,
                'info_two' => $info_two,
                'info_three' => $info_one,
            ];
        } elseif ($info_one == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_two,
                'info_two' => $info_three,
                'info_three' => $info_one,
            ];
        } else {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_one,
                'info_two' => $info_two,
                'info_three' => $info_three,
            ];
        }

        $this->ajaxReturn($return_result);
    }

    //改变状态---开启或者关闭
    public function set_status()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id = I('id');
        $status = I('status');

        //  $id=6;
        //   $status=1;
        if(empty($id)){
            $return_result = [
                'code' => 2,
                'msg' => 'id获取失败',

            ];
            $this->ajaxReturn($return_result);
        }
        if(empty($status)){
            $return_result = [
                'code' => 3,
                'msg' => '状态获取失败',

            ];
            $this->ajaxReturn($return_result);
        }
        elseif ($status == '1') {
            $status = 0;
        } elseif($status == '2'){
            $status = 1;
        }
        $this->cat_model->where(array('id' => $id))->save(['status' => $status]);
        $this->model->where(array('category_id'=>$id))->save(['active'=>$status]);
        $pid_info_one = $this->cat_model->where(array('pid' => $id))->select();
        if ($pid_info_one) {
            foreach ($pid_info_one as $v) {
                $ids = $v['id'];
                $this->cat_model->where(array('id' => $ids))->save(['status' => $status]);
                $this->model->where(array('category_id'=>$ids))->save(['active'=>$status]);
                $pid_info_two[] = $this->cat_model->where(array('pid' => $ids))->select();
            }

            if ($pid_info_two) {
                foreach ($pid_info_two as $table => $row) {
                    foreach ($row as $col) {
                        $idss[] = $col['id'];
                    }
                }
                $condition = [
                    'id' => array('in', $idss)
                ];
                $condition_two=[
                    'category_id' => array('in', $idss)
                ];
                $this->cat_model->where($condition)->save(['status' => $status]);
                $this->model->where($condition_two)->save(['active'=>$status]);
            }

        }
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',

        ];
        $this->ajaxReturn($return_result);
    }

    //属性
    //获取产品属性和库存
    public function goods_detail(){
        $id=trim(I('id'));
        $product = M('Templet')->find($id);
        $product['price'] = $product['price'.$this->manager['level']];
        //商品属性
        import('Lib.Action.Sku','App');
        $sku = new Sku();
        $properties = $sku->get_templet_properties($id);
        $skus = $sku->get_templet_skus($id);
        $this->product=$product;
        $this->properties=$properties;
        $this->skus=$skus;
        $this->display();

    }

    //后台加入购物车
    public function add_cars(){
        $templet_sku=M('templet_sku');
        $templet_property_value=M('templet_property_value');
        $order_shopping_cart=M('order_shopping_cart');
        $sku_id=trim(I('sku_id'));//属性id
        $tid=trim(I('templey_id'));//产品id

        $quantity=trim(I('quantity'));//库存
        $price1=trim(I('price'));
        $price2=trim(I('price2'));
        $num=trim(I('num'));
        $properties_vlues ='';
        if(empty($sku_id)){
            $price=$price1;
        }else{
            $price=$price2;
        }

        if($quantity < $num){
            $this->error("库存不足");
        }
        if($num <=0 ){
            $this->error("数量不能小于0");
        }
        //查找出相应的属性值
        if($sku_id){
            $tem_sku_info=$templet_sku->find($sku_id);
            $properties=$tem_sku_info['properties'];//属性的组合值
            $quantity=$tem_sku_info['quantity'];//库存
            //取出属性的组合值名称
            $exp=explode(';',$properties);
            foreach ($exp as $k=>$v){
                $id=explode(':',$exp[$k]);
                $templet_property_value_info=$templet_property_value->where(['id'=>$id[1],'pid'=>$id['0']])->find();
                $property_value=$templet_property_value_info['value'];
                $exp[$k]=$property_value;
            }
            $properties_vlues=implode(' ',$exp);
        }

        $condition_count=[
            'uid'=>'0'
        ];
        $order_shopping_cart_count = $order_shopping_cart->where($condition_count)->count();
        if( $order_shopping_cart_count >= 100 ){
            $this->error( '您的购物车太满了，请先整理您的购物车！');
        }
        $condition=[
            'uid'=>'0',
            'tid'=>$tid,
            'sku_id'=>$sku_id,
        ];
        $old_cart=$order_shopping_cart->where($condition)->find();

        //如果能找到对应的
        if(!empty($old_cart)){
            $old_cart_num = $old_cart['num'];
            $new_cart_num = bcadd($num,$old_cart_num,0);
            $save_info = array(
                'num'   =>  $new_cart_num,
                'updated'   =>  time(),
            );
            $save_res = $order_shopping_cart->where($condition)->save($save_info);
        }else{
            $add_info = array(
                'uid'   => '0',
                'tid'   =>  $tid,
                'num'   =>  $num,
                'created'   =>  time(),
                //商品属性/库存代码
                'sku_id' => $sku_id,
                'properties' => $properties_vlues,
                'price' => $price,
            );
            $save_res = $order_shopping_cart->add($add_info);
        }
        if($save_res){
            $this->success( '添加购物车成功！');
        }else{
            $this->error( '添加购物车失败！');
        }
    }

    //后台购物车显示（没有选择用户前）
    public function show_shopping_cart()
    {

        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();


    }

    //后台购物车显示（选择用户后）
    public function show_cart()
    {
        $order_shopping_cart=M('order_shopping_cart');
        $templet=M('templet');
        $distributor=M('distributor');
        $templet_sku=M('templet_sku');

        $tids = "";
        $nums = "";
        $sku_ids = "";
        $cart_ids = "";

        $uid=trim(I('uid'));
        $total_money = 0;
        $tid = [];
        $temp_products = [];
        $temp_products_img = [];
//        $uid=6;
        $carts = $order_shopping_cart->where(['uid' =>0])->select();
        if ($carts) {
            foreach ($carts as $cart) {
                $tid[] = $cart['tid'];
            }
        }
        array_unique($tid);
        //删除已经不存在的产品
        foreach ($tid as $id) {
            if (!$templet->find($id)) {
                $order_shopping_cart->where(['tid' => $id])->delete();
            }
        }

        $carts = $order_shopping_cart->where(['uid' =>0])->select();

        $products = $templet->where(['id' => ['in', $tid]])->select();
        foreach ($products as $key => $product) {
            $temp_products[$product['id']] = $product['name'];
            $temp_products_img[$product['id']] = $product['image'];
        }

        //获取真实的价格

        if($uid){
            //得出代理等级价格信息
            $dis_info=$distributor->where(['id'=>$uid,'audited'=>'1'])->find();
            $level=$dis_info['level'];
            $price="price".$level;
            foreach ($carts as $key=>$value){
                if($value['sku_id']){
                    //如果找到规格信息则改价格，没有则删除
                    $info=$templet_sku->where(['id'=>$value['sku_id']])->find();
                    if($info){
                        $order_shopping_cart->where(['id'=>$value['id']])->save(['price'=>$info[$price]]);
                    }else{
                        $order_shopping_cart->where(['id'=>$value['id']])->delete();
                    }
                }else{
                    $infos=$templet->where(['id'=>$value['tid']])->find();
                    $order_shopping_cart->where(['id'=>$value['id']])->save(['price'=>$infos[$price]]);
                }
            }
        }
        //显示库存
        $carts = $order_shopping_cart->where(['uid' =>0])->select();
        foreach ($carts as $k=>$cart) {
            $total_money += bcmul($cart['price'], $cart['num'], 2);
            if($cart['sku_id']){
                $info=$templet_sku->where(['id'=>$cart['sku_id']])->find();
                $carts[$k]['quantity']=$info['quantity'];
            }else{
                $infos=$templet->where(['id'=>$cart['tid']])->find();
                $carts[$k]['quantity']=$infos['quantity'];
            }
            $carts[$k]['products_name']=$temp_products[$cart['tid']];
            $carts[$k]['products_image']=$temp_products_img[$cart['tid']];
            $tids .= $cart['tid'] . '|';
            $nums .= $cart['num'] . '|';
            $sku_ids .= $cart['sku_id'] . '|';
            $cart_ids .=$cart['id'] . '|';
        }

        $return_result=[
            'code'=>'1',
            'msg'=>'获取成功！',
            'carts'=>$carts,
            'total_money'=>$total_money,
            'tids'=>$tids,
            'nums'=>$nums,
            'sku_ids'=>$sku_ids,
            'cart_ids'=>$cart_ids,
        ];
        $this->ajaxReturn($return_result);

    }

    //购物车真正结算，写入order表
    public function orderhand(){
        import('Lib.Action.Order', 'App');
        $Order = new Order();

        $uid=trim(I('post.agent_list'));

        $order_num=I('post.order_num');//订单号
        $tids = I('post.tids');//产品id,数组形式["49","49","43"]
        $p_ids=explode('|',$tids);
        $nums = I('post.nums');//数量 数组形式["11","4","2"]
        $p_nums = explode('|',$nums);
        $cart_ids = I('post.cart_ids');//购物车产品的id "240|239|238|"
        $sku_ids = explode('|',I('sku_ids')); // 属性数组形式 ["52","0","0"]
        $note = I('post.note'); // 备注，可以空
        $pay_photo = I('post.pay_photo'); //可为空
        $name = trim(I('agent_name'));//收货人名称
        $phone = trim(I('phone'));//收货人电话号码
        $address_id=trim(I('address_id'));
        $province=trim(I('province'));
        $city=trim(I('city'));
        $area = trim(I('area'));
        $address = trim(I('address'));
        if(empty($uid)){
            $this->error('代理不能为空');
        }

        $res_address=$this->check_address($uid,$name,$phone,$province,$city,$area,$address,$address_id);
        if($res_address['status'] != 1){
            $this->error($res_address['message']);
        }
        $pay_type = 0;
        //运费相关（暂不使用）
        $shipping_way_id=I('post.shipping_way_id');
        $shipping_way=I('post.shipping_way');
        $total_money_fee = 0;

        //检查规格属性
        if($sku_ids){
            $res=$this->is_null_sku_id($cart_ids);
            if($res['code'] == '-1'){
                $this->error($res['mgs']);
                die;
            }
        }


        $write_info = array(
            'order_num' => $order_num,
            'p_ids' => $p_ids,
            'p_nums' => $p_nums,
            'cart_ids' => $cart_ids,
            'note' => $note,
            'pay_type'  =>  $pay_type,
            'pay_photo' =>  $pay_photo,
            'sku_ids' => $sku_ids,
            'shipping_way_id'=>$shipping_way_id,
            'shipping_way'=>$shipping_way,
            'total_money_fee' => $total_money_fee,
        );

        $return_result = $Order->write_order($uid, $write_info);

        if($return_result['code'] == 1){
            $this->add_active_log('总部为'.$uid.'下单成功');
            $this->success($return_result['msg'],__URL__.'/'.'product_index');
        }else{
            $this->error($return_result['msg']);
        }

    }

    //获取个人信息的接口
    public function get_user_info(){
        $uid = trim(I('uid'));
        $distributor_obj = M('distributor');
        $money_funds_obj = M('money_funds');
        $address_obj = M('address');

        //用户信息
        $where_dis = array(
            'id'    =>  $uid,
        );

        $dis_info = $distributor_obj->where($where_dis)->find();
        if(empty($dis_info)){
            $return_result=[
                'code'=>'2',
                'msg'=>'没有找到该用户！',
            ];
            $this->ajaxReturn($return_result);
        }

        //收货信息
        $where_rec = array(
            'user_id'   =>  $uid,
        );

        $address_info = $address_obj->where($where_rec)->select();
        foreach($address_info as $k=>$v){
             $address_info[$k]['default_name']=!empty($address_info[$k]['default'])?"默认地址":'';
        }
        //查看该经销商的资金表
        $money_funds = $money_funds_obj->where(array('uid'=>$uid))->find();
        $recharge_money = empty($money_funds)?0:$money_funds['recharge_money'];

        $return_result=[
            'code'=>'1',
            'msg'=>'获取成功！',
            'recharge_money'=>$recharge_money,
            'dis_info'=>$dis_info,
            'address_info'=>$address_info,
        ];
        $this->ajaxReturn($return_result);
    }//end func set_order

    //判断购物车里sku_id是否存在
    public function is_null_sku_id($cart_ids) {
        import('Lib.Action.Sku', 'App');
        $sku = new Sku();
        $templet=M('templet');
        $order_shopping_cart=M('order_shopping_cart');
        $tids = [];
        $sku_ids = [];
        $name = "";
        $cart_ids = explode('|', $cart_ids);

        $carts = $order_shopping_cart->where(['uid' => 0, 'id' => ['in',$cart_ids]])->select();

        foreach ($carts as $cart) {
            $tids[] = $cart['tid'];
            if ($cart['sku_id']) {
                $sku_ids[] = $cart['sku_id'];
            }
        }

        //得到不存在库存id集合
        $null_sku_ids = $sku->is_null_sku_id($sku_ids);

        if ($null_sku_ids) {
            $tids = $order_shopping_cart->field('tid')->where(['uid' => 0, 'sku_id' => ['in',$null_sku_ids]])->select();
            foreach ($tids as $id) {
                $null_tids[] = $id['tid'];
            }
            $templets = $templet->where(['id' => ['in', $null_tids]])->select();

            foreach ($templets as $v) {
                $name .= ' '. $v['name'];
            }
            $msg = "$name 产品已失效，请清除";
            $return_result = [
                'code' => -1,
                'msg' => $msg,
            ];

        } else {
            $return_result = [
                'code' => 1,
            ];
        }
      return $return_result;
    }

    //删除购物车
    public function del_cart() {
            $cart_ids = explode('|', I('cart_ids'));
            $res = M('order_shopping_cart')->where(['id' => ['in', $cart_ids]])->delete();

            if(IS_AJAX){
                if($res){
                    $return_res=[
                        'code'=>'1',
                        'msg' => '删除成功'
                    ];
                }else{
                    $return_res=[
                        'code'=>'2',
                        'msg' => '删除失败'
                    ];
                }
                $this->ajaxReturn($return_res, 'JSON');
            }else{
                if($res){
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
          }
    }

    //购物车填写地址验证
    public function check_address($uid,$name,$phone,$province,$city,$area,$address,$address_id){
        $address_model=M('address');
        if($address_id =='add'){
            if(empty($name)){
                $response['status'] = 2;
                $response['message'] ="收货人名字不能为空！";
                return  $response;
            }
            if(empty($phone)){
                $response['status'] = 3;
                $response['message'] ="手机号码不能为空！";
                return $response;
            }
            elseif(strlen($phone) != '11'){
                $response['status'] = 4;
                $response['message'] ="手机号码长度应为11位！";
                return  $response;
            }
            elseif(!(preg_match('/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/', $phone))){
                $response['status'] = 5;
                $response['message'] ="手机号码格式不正确！";
                return  $response;
            }elseif(empty($address)){
                $response['status'] = 6;
                $response['message'] ="地址不能为空！";
                return  $response;
            }
        }
        
        $count=$address_model->where(['user_id' =>$uid])->count('id');

        if($count >0){
            //全部改为0  （0为不默认，1为默认）
            $address_model->where(['user_id' =>$uid])->save(['default' => 0]);
            if($address_id == 'add'){
                $data = array(
                    'user_id'=>$uid,
                    'name' => $name,
                    'phone' => $phone,
                    'province' =>$province,
                    'city'=>$city,
                    'area'=>$area,
                    'address'=>$address,
                    'default'=>1,
                    'add_time' => time()
                );
                $res = $address_model->add($data);
                if($res){
                    $response['status'] = 1;
                }else{
                    $response['status'] = 7;
                    $response['message'] ="添加地址失败！";
                }
            }else{
                $res=$address_model->where(['id' =>$address_id])->save(['default' => 1]);
                if($res){
                    $response['status'] = 1;
                }else{
                    $response['status'] = 8;
                    $response['message'] ="选择地址失败！";
                }
            }
        }else{
            $data = array(
                'user_id'=>$uid,
                'name' => $name,
                'phone' => $phone,
                'province' =>$province,
                'city'=>$city,
                'area'=>$area,
                'address'=>$address,
                'default'=>1,
                'add_time' => time()
            );
            $res = $address_model->add($data);
            if($res){
                $response['status'] = 1;
            }else{
                $response['status'] = 9;
                $response['message'] ="添加地址失败！";
            }
        }
        return  $response;

    }
}

?>