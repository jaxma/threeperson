<?php

/**
 *    topos经销商管理系统
 */
class DepotAction extends CommonAction
{

    private $model;

    public function _initialize()
    {
        parent::_initialize();
        
        import('Lib.Action.Depot','App');
        $Depot = new Depot();
        
        $this->model = M('depot');
    }
    
    //
    public function index(){
        $this->display();
    }
    
    
    //编辑
    public function edit(){
        $id = trim(I('id'));
        
        
        
        $list = $this->model->where(['id'=>$id])->find();
        
        $this->list = $list;
        $this->display();
    }
    
    
    public function edit_submit(){
        $id = trim(I('id'));
        $name = trim(I('name'));
        
        if( empty($id) ){
            
            $data = [
                'name'  =>  $name,
                'created'   =>  time(),
                'updated'   =>  time(),
            ];
            
            $res = $this->model->add($data);
            $log = '新增仓库：'.$name;
        }
        else{
            $condition = [
                'id'    =>  $id,
            ];
            $old_name = $this->model->where($condition)->getField('name');
            
            if( $name == $old_name ){
                $this->error('没有任何改动！');
            }
            
            $data = [
                'name'  =>  $name,
                'updated'   =>  time(),
            ];
            
            $res = $this->model->add($data);
            $log = '修改仓库：'.$old_name.'，改为：'.$name;
        }
        
        if( $res ){
            $this->add_active_log($log);
            $this->success('编辑成功！');
        }
        else{
            $this->error('编辑失败，请重试');
        }
    }
    
    
    
    
}

?>