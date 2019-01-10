<?php

/**
 * 	topos代理管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class UpgradeAction extends CommonAction {

    private $distributor_model;
    private $upgrade_apply_model;
    private $upgrade_setting_model;
    private $upgrade_obj;
    public function _initialize() {
        parent::_initialize();
        import('Lib.Action.Upgrade', 'App');
        $this->upgrade_obj = new Upgrade();
        $this->upgrade_apply_model = M('distributor_upgrade_apply');
        $this->upgrade_setting_model = M('distributor_upgrade_setting');
        $this->distributor_model = M('distributor');

    }

    //升级申请
    public function index()
    {
        $name = trim(I('get.name'));
        $status = trim(I('get.status'));
        if (!empty($name)) {
            $where = [
                'name' => $name,
                '_logic' => 'or',
                'wechatnum' => $name,
                'phone' => $name
            ];
            $sear_dis_info = $this->distributor_model->where($where)->find();

            $condition['uid'] = !empty($sear_dis_info) ? $sear_dis_info['id'] : '0';
        }
        if ($status != null) {
            $condition['status'] = $status;
        }

        $page_info = array(
            'page_num' => I('get.p'),
        );
        $result = $this->upgrade_obj->get_distributor_upgrade_apply($page_info, $condition);

//        print_r($result);return;
        $this->upgrade_apply_status = $this->upgrade_obj->status_name;
        $this->count = $result['count'];
        $this->p = I('p');
        $this->limit = $result['limit'];
        $this->list = $result['list'];
        $this->levnames = C('LEVEL_NAME');
        $this->display();
    }


    //审核通过
    public function upgrade_apply_pass()
    {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        $pass = trim(I('pass'));
        $mids = I('mids');
        $mids = explode('_',substr($mids, 1));

        $result = $this->upgrade_obj->upgrade_apply_pass($mids, $pass);
        $this->ajaxReturn($result, 'json');
    }
    
    //升级说明
    public function upgrade_desc_index(){
        $distributor_upgrade_desc=M('distributor_upgrade_desc');
        $level_name=C('LEVEL_NAME');
        $count =$distributor_upgrade_desc->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $distributor_upgrade_desc->order('id desc')->limit($limit)->select();
            foreach ($list as $k=>$v){
                $list[$k]['level_name']=$level_name[$v['level']];
            }
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $level_num=C('LEVEL_NUM');
        $this->p=I('p');
        $this->limit=$page_num;
        $this->level_num=$level_num;
        $this->display();
    }

    public function upgrade_desc_add(){
        $level_name=C('LEVEL_NAME');
        array_pop($level_name);
        $this->level_name=$level_name;
        $this->display();
    }

    public function upgrade_desc_insert(){
        $distributor_upgrade_desc=M('distributor_upgrade_desc');
        $level=trim(I('level'));
        $money=trim(I('money'));
        $desc=trim(I('desc'));
        $img=trim(I('image'));
        if(!is_numeric($money) || $money <= 0){
            $this->error('金额不符合标准');
        }
        $is_find=$distributor_upgrade_desc->where(['level'=>$level])->find();
        if($is_find){
            $this->error('该级别已存在');
        }
        $data=[
            'level'=>$level,
            'money' => $money,
            'desc'=>$desc,
            'img' => $img,
            'created'=>time(),
        ];
        $res=$distributor_upgrade_desc->add($data);
        if($res){
            $this->add_active_log('添加一次性条件设置');
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    public function upgrade_desc_edit(){
        $distributor_upgrade_desc=M('distributor_upgrade_desc');
        $id=trim(I('id'));
        $row=$distributor_upgrade_desc->where(['id'=>$id])->find();
        $row['level']=(int)$row['level'];
        $level_name=C('LEVEL_NAME');
        array_pop($level_name);
        $keys=array_keys($level_name);
        $this->level_name=$level_name;
        $this->keys=$keys;
        $this->row=$row;
        $this->display();
    }

    public function upgrade_desc_update(){
        $distributor_upgrade_desc=M('distributor_upgrade_desc');
        $id=trim(I('id'));
        $level=trim(I('level'));
        $money=trim(I('money'));
        $desc=trim(I('desc'));
        $img=trim(I('image'));
        if(!is_numeric($money) || $money <= 0){
            $this->error('金额不符合标准');
        }
        $condition=[
            'id'=>['neq',$id],
            'level'=>$level
        ];
        $is_find=$distributor_upgrade_desc->where($condition)->find();
        if($is_find){
            $this->error('该级别已存在');
        }
        $data=[
            'level'=>$level,
            'money' => $money,
            'desc'=>$desc,
            'img' => $img,
            'updated'=>time(),
        ];
        $res=$distributor_upgrade_desc->where(['id'=>$id])->save($data);
        if($res){
            $this->add_active_log('添加一次性条件设置');
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
        }
    }

    public function upgrade_desc_delete(){
        $id=trim(I('id'));
        $res=M('distributor_upgrade_desc')->where(['id'=>$id])->delete();
        if($res){
            $this->add_active_log('删除一次性条件设置');
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //查看保证金截图
    public function detail(){
        $id=trim(I('id'));
        $info=M('distributor_upgrade_apply')->find($id);
        $this->depositimg=$info['depositimg'];
        $this->display();
    }
    
    //任务升级设置
    public function setting() {
        $list = [];
        $level_name = C('LEVEL_NAME');
        unset($level_name[1]);
        $setting = $this->upgrade_setting_model->where(['type' => $this->upgrade_obj->upgrade_people])->select();
        foreach ($setting as $v) {
            $list[$v['level']] = $v['parameter'];
        }
        $this->list = $list;
        $this->level_name = $level_name;
        $this->display();
    }
    
    //人数任务设置提交
    public function people_setting_submit() {
        $status = I('status');
        $level = $_POST['level'];
        $type = I('type');
        $parameter = $_POST['parameter'];
        foreach ($parameter as $v) {
            if ($v < 0) {
                $this->error('人数不能小于0');
            }
        }
        //删除重新写入
        $this->upgrade_setting_model->where(['type' => $type])->delete();
        foreach ($level as $k => $v) {
            $data = [
                'level' => $v,
                'type' => $type,
                'status' => 1,
                'parameter' => $parameter[$k],
                'time' => time(),
            ];
            $res = $this->upgrade_setting_model->add($data);
        }
        if ($res) {
            $this->add_active_log('人数任务升级设置成功');
            $this->success('人数任务升级设置成功');
        } else {
            $this->error('人数任务升级设置失败');
        }
    }
}

?>