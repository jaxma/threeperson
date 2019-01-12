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
        $c_id = 2;
        $p_id = 5;
        $this->assign('c_id',$c_id);
        $this->assign('p_id',$p_id);
        $this->display();
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

        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $this->id = $id;
        $this->assign('row',$row)
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
            $this->success("操作成功",__URL__.'/'.'company');
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