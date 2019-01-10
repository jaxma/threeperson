<?php

/**
 * 	topos经销商管理系统
 */
class InformAction extends CommonAction {

    //获取表名
    private function get_model(){

        return 'Inform';
    }

    //获取该栏目中文名字
    private function get_name(){

        return '代理首页滚动通知';
    }


    //获取列表信息
    public function index() {
        $model_name = $this->get_model();

        $count = D($model_name)->count('id');
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
    public function add() {
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();

        $content = I('post.content');
        $status=trim(I('post.status'));
        //将正在使用的全改为未使用
    if($status == '1'){
        $count=D($model_name)->where(array('status'=> 1))->count();
        if($count>0){
            $res = D($model_name)->where(array('status'=> 1))->save(['status' => 0]);
        }
    }

        $data = array(
//            'name' => trim(I('post.name')),
            'content' => $content,
            'time' => time(),
            'status' => $status,
        );

        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑
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

        $id = I('id');
        $content = I('post.content');
        $status=trim(I('post.status'));
        if($status == '1'){
            $count=D($model_name)->where(array('status'=> 1))->count();
            if($count>0){
                $res = D($model_name)->where(array('status'=> 1))->save(['status' => 0]);
            }
        }

        $data = array(
//            'name' => trim(I('post.name')),
            'content' => $content,
            'time' => time(),
            'status' => $status,
        );

        $res = D($model_name)->where(array('id' => $id))->save($data);

        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功");
        }
    }

    //删除
    public function delete() {
        $model_name = $this->get_model();

        $id = $_GET['id'];
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');

            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    
        //获取用户列表信息
    public function user() {
        $model_name = $this->get_model();
        //获取用户通知消息
        $inform_dis = M('inform_dis');
        $inform_dis_all = $inform_dis->select();
        
        $count = $inform_dis->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $inform_dis_all = $inform_dis->order('time desc')->limit($limit)->select();
//          $inform_dis_all = $this->get_related_data($inform_dis_all,'distributor','openid');
            foreach($inform_dis_all as $k => $v){
                $inform_dis_all[$k]['dis_info'] = M('distributor')->where(['openid'=>$v['openid']])->find();
            }
//          var_dump($inform_dis_all);die;
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('inform_dis_all', $inform_dis_all);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
    }
    
        //获取列表信息
    public function system() {
        $model_name = $this->get_model();
        
        $count = D($model_name)->count('id');
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
    
    public function delete_msg(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $id = I('id');
        $inform_dis = M('inform_dis');
        $result = [
            'code'=> 0,
            'msg' => '参数错误'
        ];
        
        if(!empty($id)){
            $where = 'id in('.implode(',',$id).')';
            $res = $inform_dis->where($where)->delete();
            if(!$res){
                $result = [
                    'code' => 1,
                    'msg' => '删除失败'
                ];
            }else{
                $result = [
                    'code' => 1,
                    'msg' => '删除成功'
                ];
            }
            
        }
        
        $this->ajaxReturn($result);
    }
    
}

?>