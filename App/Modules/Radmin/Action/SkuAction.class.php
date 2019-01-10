<?php
/**
*	topos经销商管理系统
*/
header("Content-Type: text/html; charset=utf-8");
class SkuAction extends CommonAction
{
    private $property_model;
    private $property_to_value_model;
    public function _initialize() {
        parent::_initialize();
        $this->property_model = M('templet_property');
        $this->property_to_value_model = M('templet_property_to_value');
    }

    public function index()
	{
        $name = trim(I('get.name'));
        if ($name != null) {
            $condition['name'] = ['like', '%' . $name . '%'];
        }

        $count = $this->property_model->where($condition)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;

            $properties = $this->property_model->where($condition)->limit($limit)->order('id desc')->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->properties = $properties;
            $this->page = $page;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
		$this->display();
	}
    
    public function add() {
        if (IS_POST) {
            $res = $this->property_model->add(['name' => trim(I('name'))]);
            if ($res) {
                $this->success('添加产品属性成功');
            } else {
              $this->error('添加产品属性失败'); 
            }
        }
        $this->display();
    }
    
    public function edit() {
        if (IS_POST) {
            $res = $this->property_model->where(['id' => I('post.id')])->save(['name' => trim(I('name'))]);
            if ($res) {
                $this->success('编辑产品属性成功');
            } else {
              $this->error('编辑产品属性失败'); 
            }
        }
        $this->row = $this->property_model->find(I('get.id'));
        $this->display();
    }
    
    public function delete() {
        if (IS_AJAX) {
            $id = I('id');
            if ($this->property_to_value_model->where(['pid' => $id])->find()) {
                $this->error('有产品使用了该属性，不能删除');
            }
            $res = $this->property_model->delete($id);
            if ($res) {
                $this->success('删除产品属性成功');
            } else {
              $this->error('删除产品属性失败'); 
            }
        }
    }
}
?>