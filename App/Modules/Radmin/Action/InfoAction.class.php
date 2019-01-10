<?php

/**
 * 	topos经销商管理系统
 */
class InfoAction extends CommonAction {

    //获取表名
    private function get_model(){

        return 'Info';
    }

    //获取该栏目中文名字
    private function get_name(){

        return '授权查询设置';
    }
    //获取该栏目中文名字
    private function get_security_name(){

        return '防伪查询设置';
    }

    //获取列表信息
    public function index() {
        $model_name = $this->get_model();
        $type=trim(I('get.type'));
        $condition=[
            'type'=>$type,
        ];
        $count = D($model_name)->where($condition)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->where($condition)->order('id desc')->limit($limit)->select();

            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->type=$type;
        $this->display();
    }

    //添加
    public function add() {
        $type=trim(I('type'));
        $this->type=$type;
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();
        $type=trim(I('type'));
        $image=I('post.image');
        $status=trim(I('post.status'));
        //将正在使用的全改为未使用
        $condition=[
            'status'=>1,
            'type'=>$type,
        ];
    if($status == '1'){
        $count=D($model_name)->where($condition)->count('id');
        if($count>0){
            $res = D($model_name)->where($condition)->save(['status' => 0]);
        }
    }

        $data = array(
//            'name' => trim(I('post.name')),
            'image' => $image,
            'time' => time(),
            'status' => $status,
            'type'=>$type,
        );

        $res = D($model_name)->add($data);
        if ($res) {

            if($type == 1){
                $name = $this->get_name();
            }elseif ($type == 2){
                $name = $this->get_security_name();
            }
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
        $id_info=  M('info')->where(array('id' => $id))->find();
        $type=$id_info['type'];
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }
        $status=trim(I('post.status'));
        $condition=[
            'status'=>1,
            'type'=>$type,
        ];
        if($status == '1'){
            $count=D($model_name)->where($condition)->count('id');
            if($count>0){
                $res = D($model_name)->where($condition)->save(['status' => 0]);
            }
        }

        $data = array(
//            'name' => trim(I('post.name')),
            'image' => $image,
            'time' => time(),
            'status' => $status,
            'type'=>$type,
        );

        $res = D($model_name)->where(array('id' => $id))->save($data);

        if ($res === false) {
            $this->error("操作失败");
        } else {
            if($type == 1){
                $name = $this->get_name();
            }elseif ($type == 2){
                $name = $this->get_security_name();
            }

            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功");
        }
    }

    //删除
    public function delete() {
        $model_name = $this->get_model();

        $id = $_GET['id'];
        $info = M('info')->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__. $info[0]['image'];
        @unlink($url);
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            if($info[0]['type']==1){
                $name = $this->get_name();
            }elseif ($info[0]['type']==2){
                $name = $this->get_security_name();
            }
            $this->add_active_log('删除'.$name.'信息');

            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}

?>