<?php

/**
 * 	topos经销商管理系统
 */
class CompanyAction extends CommonAction {

    private $cat_model;
    private $company_model;

    public function _initialize()
    {
        parent::_initialize();

        $this->company_model = M('company');
    }
    //获取表名
    private function get_model(){

        return 'Company';
    }

    //获取该栏目中文名字
    private function get_name(){
        return '公司信息';
    }

    //公司详情主页
    public function company() {
        $where = array();
        $count = $this->company_model->where($where)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->company_model->where($where)->order('id asc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display('company');
    }

    //首页介绍
    public function int() {
        $where = array();
        $count = $this->company_model->where($where)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->company_model->where($where)->order('id asc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display('company');
    }

    //删除公司信息
    public function delete_con() {
        
        $id = I('id');
        $res = $this->company_model->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }



    //摄影图片
    //添加产品信息
    public function add() {
        
        $this->display();
    }

    public function wechat() {
        $this->row1 = M('company')->where('status = 104')->find();
        $this->row2 = M('company')->where('status = 105')->find();
        $this->display();
    }

    public function update_wechat() {

        $we_account = I('post.we_account');
        $we_account_en = I('post.we_account_en');

        $ins = I('post.ins');
        $ins_en = I('post.ins_en');

        $we_account2 = I('post.we_account2');
        $we_account2_en = I('post.we_account2_en');

        $ins2 = I('post.ins2');
        $ins2_en = I('post.ins2_en');

        if(empty($we_account)||empty($we_account_en)||empty($ins)||empty($ins_en)){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        if(empty($we_account2)||empty($we_account2_en)||empty($ins2)||empty($ins2_en)){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $content_res=M('company')->where('status=104')->find();
        $data = array(
            'name' => $we_account,
            'name_en' => $ins,
            'content' => $we_account_en,
            'content_en' => $ins_en,
            'status' => 104,
        );
        if($content_res){
            $content_res = M('company')->where('status = 104')->save($data);
        }else{
            $content_res = M('company')->add($data);
        }

        $content_res=M('company')->where('status=105')->find();
        
        $data = array(
            'name' => $we_account2,
            'name_en' => $ins2,
            'content' => $we_account2_en,
            'content_en' => $ins2_en,
            'status' => 105,
        );
        if($content_res){
            $content_res = M('company')->where('status = 105')->save($data);
        }else{
            $content_res = M('company')->add($data);
        }

        if ($content_res === false) {
            $this->error("操作失败");
        } else {
            $this->success("操作成功",__URL__.'/'.'wechat');
        }
    }

    public function insert() {
        $model_name = $this->get_model();
        $name = trim(I('post.name',''));
        $name_en = trim(I('post.name_en',''));
        $city_cn = I('post.city_cn','');
        $city_usa = I('post.city_usa','');
        $city_cn_en=I('post.city_cn_en');
        $city_usa_en=I('post.city_usa_en');
        $address_cn = trim(I('post.address_cn',''));
        $address_cn_en = trim(I('post.address_cn_en',''));
        $address_usa = trim(I('post.address_usa',''));
        $address_usa_en = trim(I('post.address_usa_en',''));
        $tel_en = trim(I('post.tel_en',''));
        $tel_usa = trim(I('post.tel_usa',''));
        $info_en = trim(I('post.info_en',''));
        $info_uas = trim(I('post.info_uas',''));
        $status = trim(I('post.status'));
        $content = trim(I('post.content',''));
        $content = $this->formateStr($content);
        $content_en = trim(I('post.content_en',''));
        $content_en = $this->formateStr($content_en);
        
        $data = array(
            'name' => $name,
            'name_en' => $name_en,
            'city_cn' => $city_cn,
            'city_usa' => $city_usa,
            'city_cn_en' => $city_cn_en,
            'city_usa_en' => $city_usa_en,
            'content' => $content,
            'content_en' => $content_en,
            'address_cn_en' => $address_cn_en,
            'address_cn' => $address_cn,
            'address_usa' => $address_usa,
            'address_usa_en' => $address_usa_en,
            'tel_en' => $tel_en,
            'tel_usa' => $tel_usa,
            'address_cn' => $address_cn,
            'info_en' => $info_en,
            'status' => $status,
            'sequence' => $sequence,
            'time' => time(),
            'info_uas'=> $info_uas,
        );

        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',__URL__.'/'.'company');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑产品信息
    public function edit() {
        $model_name = $this->get_model();

        $id = I('id');

        $row = D($model_name)->find($id);
        $this->id = $id;
        $this->row = $row;
        $this->display();
    }

    //编辑产品信息
    public function photo() {
        $model_name = $this->get_model();

        $type = I('type');

        $row = M('photo')->where('type = '.$type)->find();

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
        $this->arr = $row_arr;

        $this->type = $type;
        $this->row = $row;
        $this->display();
    }

    //编辑产品信息
    public function introduct_edit() {
        $model_name = $this->get_model();

        $id = I('id');

        $row = D($model_name)->find($id);
        $this->id = $id;
        $this->row = $row;
        $this->display();
    }


    public function update() {
        $model_name = $this->get_model();
        
        $id = I('post.id');

        $name = trim(I('post.name',''));
        $name_en = trim(I('post.name_en',''));
        $city_cn = I('post.city_cn','');
        $city_usa = I('post.city_usa','');
        $city_cn_en=I('post.city_cn_en');
        $city_usa_en=I('post.city_usa_en');
        $address_cn = trim(I('post.address_cn',''));
        $address_cn_en = trim(I('post.address_cn_en',''));
        $address_usa = trim(I('post.address_usa',''));
        $address_usa_en = trim(I('post.address_usa_en',''));
        $tel_en = trim(I('post.tel_en',''));
        $tel_usa = trim(I('post.tel_usa',''));
        $info_en = trim(I('post.info_en',''));
        $info_uas = trim(I('post.info_uas',''));
        $status = trim(I('post.status'));
        $content = trim(I('post.content',''));
        $content = $this->formateStr($content);
        $content_en = trim(I('post.content_en',''));
        $content_en = $this->formateStr($content_en);
        
        $data = array(
            'name' => $name,
            'name_en' => $name_en,
            'city_cn' => $city_cn,
            'city_usa' => $city_usa,
            'city_cn_en' => $city_cn_en,
            'city_usa_en' => $city_usa_en,
            'content' => $content,
            'content_en' => $content_en,
            'address_cn_en' => $address_cn_en,
            'address_cn' => $address_cn,
            'address_usa' => $address_usa,
            'address_usa_en' => $address_usa_en,
            'tel_en' => $tel_en,
            'tel_usa' => $tel_usa,
            'address_cn' => $address_cn,
            'info_en' => $info_en,
            'status' => $status,
            'sequence' => $sequence,
            'time' => time(),
            'info_uas'=> $info_uas,
        );

        
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            if($id == 2){
                $this->success("操作成功",U(__URL__.'/'.'edit',array('id'=>2)));
            }elseif($id == 3){
                $this->success('操作成功',U(__URL__.'/'.'introduct_edit',array('id'=>3)));
            }else{
                $this->success("操作成功",__URL__.'/'.'company');
            }
        }
    }

    public function update_photo() {
        $model_name = M('photo');
        
        // $id = I('post.id');
        $type = I('type');
        
        $id_info= $model_name->where(array('type' => $type))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $name = I('post.name');
        $name_en = I('post.name_en');

        $image=I('post.image');
       
        $many_image=I('many_image');
        $many_images = implode(',',$many_image);

        if($type == 1){
            if(empty($image) || empty($many_image)){
                $this->error('红色带星项目必须填写，请检查后重新提交');
                exit();
            }
        }
        if($type == 2){
            if(empty($name) || empty($name_en) || empty($image)){
                $this->error('红色带星项目必须填写，请检查后重新提交');
                exit();
            }
        }

       
        $data = array(
            'image' => $image,
            'name' => $name,
            'name_en' => $name_en,
            'many_image' => $many_images,
            'type' => $type,
            'time' => time(),
        );

        
        $res = $model_name->where(array('type' => $type))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'photo?type='.$type);
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

}

?>