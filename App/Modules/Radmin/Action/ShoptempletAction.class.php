<?php

/**
 * 	雨丝燕经销商管理系统
 */
class ShoptempletAction extends CommonAction {

    private $model;
    private $cat_model;
    public function _initialize() {
        parent::_initialize();
        $this->model = M('shop_templet');
        $this->cat_model = M('shop_templet_category');
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
            $this->success("操作成功", index);
        }
    }

    //删除产品信息
    public function delete() {
        $id = $_GET['id'];
        $res = $this->model->where(array('id' => $id))->delete();
        if ($res) {
            $this->add_active_log('产品信息删除，编号：'.$id);
            $this->success('删除成功', index);
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

        return '品牌商城产品分类';
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
        $reduction_category = M('shop_templet_category');
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
            'time' => time()
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $this->success('添加成功');
            $name=$this->get_category_name();
            $this->add_active_log('添加'.$name.'信息');
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
        $id_info=$this->cat_model->where(array('id' => $id))->find();
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
            'time' => time()
        );

        $res =  $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功");
            $name=$this->get_category_name();
            $this->add_active_log('编辑'.$name.'信息');
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
            $this->success('删除成功');
            $name=$this->get_category_name();
            $this->add_active_log('删除'.$name.'信息');
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
        $image = $info[0]['savepath'] . $info[0]['savename'];
        return $image;

    }

    //显示模板列表
    //获取中文名字
    private function get_product_name()
    {

        return '店中店产品模板';
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
            $listres =  $this->model->where($condition)->limit($limit)->select();
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
            $cats = M('shop_templet_category')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $category_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['category_name'] = $category_info[$v['category_id']]['name'];
            }


            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();

    }
    //添加商品信息
    public function product_add() {
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $reduction_category = M('shop_templet_category');
        
        $templet = M('templet');
        import('Lib.Action.Sku','App');
        $Sku = new Sku();
        $templet_info = $templet->select();
        foreach($templet_info as $k => $v){
          if ($templet_info[$k]['has_property'] == 1) {
            $temp_id = $templet_info[$k]['id'];
            $properties = $Sku->get_templet_properties($temp_id);
            $properties_name = $Sku->recursion($properties,$v['name'],$v['id']);
            $templet_info['all_templet_name'][] = $properties_name;
          }else{
            $result['id'] = $v['id'];
            $result['name'] = $v['name'];
            $result['properties'] = '';
            $templet_info['all_templet_name'][][] = $result;
          }
        }
        
        $cate = $reduction_category->select();
//      var_dump($templet_info['all_templet_name']);die;
        $cateres=$Team->sortt($cate);
        $this->assign('cateres', $cateres);
        $this->assign('templet_info',$templet_info);
        //属性
        $this->properties = M('shop_templet_property')->select();
        $this->product = [];
        $this->propertyPrices = [];
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

        //属性
        $has_property = 0;
        $templet = json_decode($_POST['ProductForm'], true);
        if ($templet['stock']>0) {
            $quantity = $templet['stock'];
            $has_property = 1;
        } else {
            $quantity = I('quantity');
        }

        $bind_templet = I('templet_id');
        $bind_templet = explode(' ',$bind_templet);
        $bind_id = $bind_templet[0];
        $bind_property = '';
        if(!empty($bind_templet[1])){
          $temp_property = explode(';',$bind_templet[1]);
          foreach($temp_property as $k => $v){
            $bind_property_v = explode(':',$v);
            $bind_property .= $bind_property_v[0].':'.$bind_property_v[1];
            if($temp_property[$k+1] != NULL){
              $bind_property .= ';';
            }
          }
        }
        
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
            'price' => trim(I('post.price')),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time(),
            'ratio1' => trim(I('ratio1')),
            'ratio2' => trim(I('ratio2')),
            'ratio3' => trim(I('ratio3')),
            'mail_fee' => I('post.mail_fee'),
            'many_image' => $many_images,
            'bind_pid' => $bind_id,
            'bind_property' => $bind_property,
            'quantity' => $quantity,
            'has_property' =>$has_property,
            'product_parameter'=>trim(I('product_parameter')),
        );
        
//      var_dump($data);die;
        $res = $this->model->add($data);
        if ($res) {
          
            //属性
            //保存商品属性库存
            import('Lib.Action.Shopsku','App');
            $shopsku = new Shopsku();
            $shopsku->save_templet_info($templet, $res);
          
            $this->success('添加成功',__URL__.'/'.'product_index');
            $name = $this->get_product_name();
            $this->add_active_log('添加'.$name.'信息');
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
        
        $templet = M('templet');
        import('Lib.Action.Sku','App');
        $Sku = new Sku();
        $templet_info = $templet->select();
        foreach($templet_info as $k => $v){
          if ($templet_info[$k]['has_property'] == 1) {
            $temp_id = $templet_info[$k]['id'];
            $properties = $Sku->get_templet_properties($temp_id);
            $properties_name = $Sku->recursion($properties,$v['name'],$v['id']);
            $templet_info['all_templet_name'][] = $properties_name;
          }else{
            $result['id'] = $v['id'];
            $result['name'] = $v['name'];
            $result['properties'] = '';
            $templet_info['all_templet_name'][][] = $result;
          }
        }
        
        $row_image = $row['many_image'];
        $arr = explode(',', $row_image);
        
        //属性
        //商品属性库存
        import('Lib.Action.Shopsku','App');
        $shopsku = new Shopsku();
        $product = $shopsku->init_properties($row);
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
        
        //在每个前面加上__ROOT__,用在编辑时本机显示图片
        array_walk(
            $arr,
            function (&$s, $k, $prefix = '__ROOT__') {
                $s = str_pad($s, strlen($prefix) + strlen($s), $prefix, STR_PAD_LEFT);
            }
        );
        $row_arr=implode(',',$arr);
        
        $category_info = M('shop_templet_category');
        $dis_category = $category_info->select();
        $dis_category=$Team->sortt($dis_category);
        $this->assign('templet_info',$templet_info);
        $this->assign('dis_category', $dis_category);
        $this->arr=$row_arr;
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
        $parameter=trim(I('product_parameter'));
        $product_parameter=$parameter;
        

        $many_image=I('many_image');
        $many_images=implode(',',$many_image);
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
            'price' => trim(I('post.price')),
            'description'=>I('post.description'),
            'content' =>$content,
            'pid' => I('post.pid'),
            'time' => time(),
            'ratio1' => trim(I('ratio1')),
            'ratio2' => trim(I('ratio2')),
            'ratio3' => trim(I('ratio3')),
            'mail_fee' => I('post.mail_fee'),
            'many_image' => $many_images,
            'bind_pid' => I('post.templet_id'),
            'quantity' => $quantity,
            'has_property' => $has_property,
            'product_parameter'=>$product_parameter,
        );
//      setLog(json_encode($templet));
        $res = $this->model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
          //属性
            //保存商品属性库存
            import('Lib.Action.Shopsku','App');
            $shopsku = new Shopsku();
            $shopsku->save_templet_info($templet, $id, I('post.show_stock'));
            $this->success("操作成功",__URL__.'/'.'product_index');
            $name = $this->get_product_name();
            $this->add_active_log('编辑'.$name.'信息');
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
            $this->success('删除成功');
            $name = $this->get_product_name();
            $this->add_active_log('删除'.$name.'信息');
        } else {
            $this->error('删除失败');
        }
    }

    //------------------******广告列表******----------------
    private function get_model()
    {

        return 'shop_advert';
    }
    //获取广告中文名字
    private function get_adv_name()
    {

        return '品牌商城广告';
    }
    //列表页
    public function advert_index(){
        $model_name = M('shop_advert');
        $count = $model_name->count('id');

        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list =$model_name->order('id desc')->limit($limit)->select();

            //联表查询
            $material_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['malltemplet_category_id']])) {
                    $ids[$v['malltemplet_category_id']] = $v['malltemplet_category_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $mats = M('shop_templet_category')->where(['id' => ['in', $ids]])->select();
            //取出数据
            foreach ($mats as $v) {
                $material_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['malltemplet_category_name'] = $material_info[$v['malltemplet_category_id']]['name'];
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
        $info=M('shop_templet_category')->field('id,name,pid')->select();
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $list=$Team->sortt($info);
        $this->list=$list;
        $this->display();
    }

    public function advert_insert(){

        $model_name = $this->get_model();

//        $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'name' => trim(I('post.name')),
            'image' => $image,
            'status' => I('post.status'),
            'sequence'=>trim(I('post.sequence')),
            'time' => time(),
            'type' =>I('post.type'),
            'malltemplet_category_id'=>I('post.malltemplet_category_id'),
        );

        $res = M($model_name)->add($data);
        if ($res) {
            $this->success('添加成功');
            $name=$this->get_adv_name();
            $this->add_active_log('添加'.$name.'信息');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
    public function advert_edit(){

        $id = $_GET['id'];
        $row=M('shop_advert')->where(array('id'=>$id))->find();
        $lists=M('shop_templet_category')->select();
        import('Lib.Action.Team', 'App');
        $Team= new Team();
        $list=$Team->sortt($lists);
        $this->list=$list;
        $this->row = $row;
        $this->display();
    }
    public function advert_update(){

        $id = I('post.id');
        $id_info= M('shop_advert')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }
        $data = array(
            'id'=>$id,
            'name' => trim(I('post.name')),
            'image' => $image,
            'status' => I('post.status'),
            'sequence'=>trim(I('post.sequence')),
            'time' => time(),
            'type' =>I('post.type'),
            'malltemplet_category_id'=>I('post.malltemplet_category_id'),
        );


        $res = M('shop_advert')->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功");
            $name=$this->get_adv_name();
            $this->add_active_log('编辑'.$name.'信息');
        }
    }

    //删除
    public function advert_delete()
    {
        $id = I('id');
        $businessl_info =M('shop_advert')->where(array('id'=>$id))->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__ . $businessl_info[0]['image'];
        @unlink($url);
        $res = M('shop_advert')->delete($id);
        if ($res) {
            $this->success('删除成功');
            $name=$this->get_adv_name();
            $this->add_active_log('删除'.$name.'信息');
        } else {
            $this->error('删除失败');
        }
    }
    
        //属性
    //获取属性组合
    function get_properties_value_combination() {
        $product = json_decode($_GET['product'],true);
        import('Lib.Action.Shopsku','App');
        $shopsku = new Shopsku();
        $res = $shopsku->get_properties_value_combination($product);
        $this->ajaxReturn($res, 'JSON');
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
    
}

?>