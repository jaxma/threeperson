<?php

header("Content-type:text/html;charset=utf-8");

/**
 * 	topos经销商管理系统主页
 */
class AdminAction extends CommonAction {

    private $model;
    
    public function _initialize(){
        parent::_initialize();
        
        $this->model = M('admin');
    }
    
    //修改密码
    public function editPsw() {
        $this->display();
    }

    //修改密码
    public function changePsw() {
        checklogin();
        $user = M('Admin');
        $m_passw = I('post.old_psw');
        //旧
        $m_passw = md5($m_passw);
        $u_name = $_SESSION['aname'];
        $aid = $_SESSION['aid'];
        
        if( $_SESSION['aname'] == 'topos' ){
//            echo "<script>alert('该账号禁止修改');history.go(-1);</script>";
//            exit();
            $this->error('该账号禁止修改！');
        }
        
        
        if ($u_name != "") {
            $m_row = $user->where(array('id' => $aid))->find();
        } else {
            $this->redirect('Radmin/Index/logout');
        }
        if ($m_row['password'] == $m_passw) {
            if (($_POST['password'] != $_POST['twopassword']) || ($_POST['password'] == "") || strlen($_POST['password']) < 5 || strlen($_POST['password']) > 16) {
//                echo "<script>alert('两次输入密码不相同，或密码格式不对！');history.go(-1);</script>";
//                exit();
                $this->error('两次输入密码不相同，或密码格式不对！');
            }
            $m_newpass = I('post.password');
            $data['password'] = md5($m_newpass);
            if ($user->where(array('id' => $aid))->save($data)) {
                $this->add_active_log('管理员修改密码：' . $u_name);
                $this->success('修改成功,请重新登录！',  __GROUP__ . "/index/logout");
//                echo "<script>alert('修改成功,请重新登录！');window.location.href='" . __APP__ . "/Radmin/Index/logout';</script>";
//                exit();
            } else {
//                echo "<script>alert('修改失败！');history.go(-1);</script>";
//                exit();
                $this->error('修改失败！');
            }
        } else {
            $this->error('旧密码错误，请重新输入！');
//            echo "<script>alert('旧密码错误，请重新输入！');history.go(-1);</script>";
//            exit();
        }
    }

    //编辑资料
    public function edit() {

        $aid = $_SESSION['aid'];
        $row = M('Admin')->where(array('id' => $aid))->find();
        $this->vo = $row;
        $this->display();
    }

    public function update() {
        
        if( $_SESSION['aname'] == 'topos' ){
//            echo "<script>alert('该账号禁止修改');history.go(-1);</script>";
//            exit();
        }
        $id=trim(I('id'));
        $admin = M('Admin');
        if (!$admin->create()) {
            $this->error($admin->getError());
        }
        $flag = $admin->where(['id'=>$id])->save();
        if ($flag) {
            $res = $this->add_active_log('编辑管理员资料');
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    //操作日志
    public function active_log() {
        $admin_obj = M('admin');
        $active_log_obj = M('admin_active_log');
        import('ORG.Util.Page');

        $aid = I('aid');
        $log = trim(I('log'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));

        $condition = array();
        $list = array();

        if($this->aid == 1){
          if (!empty($aid)) {
            $condition['aid'] = $aid;
          }
        }else{
          $condition['aid'] = array('neq',1);
          if (!empty($aid)) {
            if($aid != 1){
              $condition['aid'] = $aid;
            }
          }
        }
        
        if( !empty($log) ){
            $condition['log'] = ['like',"%$log%"];
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        

        $count = $active_log_obj->where($condition)->count();

        $conditon_admin = [];
        if( $this->aid != 1 ){
            $conditon_admin['id'] = ['neq',1];
        }
        
        $admin_info = $admin_obj->where($conditon_admin)->field('id,username')->select();
        
        $page_num = 20;
        if ($count > 0) {
            //创建分页对象
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $active_log_obj->where($condition)->order('created desc')->limit($limit)->select();
            
            $admin_auser = array();
            foreach ($admin_info as $k_a => $v_a) {
                $v_a_id = $v_a['id'];

                $admin_aid_info[$v_a_id] = $v_a;
            }
            
            
            foreach ($list as $k_l => $v_l) {
                $v_l_aid = $v_l['aid'];
                $v_l_created = $v_l['created'];

                $the_admin_info = $admin_aid_info[$v_l_aid];



                $list[$k_l]['username'] = $the_admin_info['username'];
                $list[$k_l]['created_format'] = date('Y-m-d H:i', $v_l_created);
            }
            
            
            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->list = $list;
        }
        $this->count=$count;
        $this->p = I('p');
        $this->limit = $page_num;
        $this->admin_info = $admin_info;

        $this->display();
    }

    
    //管理
    public function manage(){
        
        $condition = [
            'id'    =>  ['neq',1]
        ];
        
        $admin_info = $this->model->where($condition)->order('id desc')->select();
        
        
        
        $this->admin_info = $admin_info;
        $this->display();
    }
    
    
    //权限分配
    public function auth(){
        import('Lib.Action.Admin','App');
        $Admin = new Admin();
        $this->id = I('id');
        $condition = [
            'id'    =>  $this->id,
        ];
        $list = $this->model->where($condition)->find();
        $list['auth_mat'] = explode(',', $list['auth']);
        print_r($Admin->admin_auth);
        $this->list = $list;
        $this->admin_auth = $Admin->admin_auth;
        $this->display();
    }
    
    //权限修改
    public function auth_update(){
        
        $id = I('id');
        $name = I('name');
        $auth = I('auth');
        
        $auth = implode(',', $auth);
        
        $info = [
            'auth'  =>  $auth,
        ];
        
        $condition = [
            'id'    =>  $id,
        ];
        
        $res = $this->model->where($condition)->save($info);
        
        if( $res ){
            $this->add_active_log('编辑管理员：'.$name.' 的权限成功！');
            $this->success('保存成功！');
        }
        else{
            $this->error('保存失败，请重试！');
        }
    }
    
    
    //添加管理员
    public function add_admin(){
        
        $this->display();
    }
    
    //执行添加管理员
    public function excu_add_admin(){
        
        $username = trim(I('username'));
        $pw = trim(I('pw'));
        $repw = trim(I('repw'));
        $email = trim(I('email'));
        $phone = trim(I('phone'));
        
        
        if( empty($username) ){
            $this->error('需要提交用户名！');
        }
        if( empty($pw) || empty($repw) || $pw != $repw ){
            $this->error('请输入密码，并确认两次输入密码是否一致！');
        }  else if (strlen($pw) < 5 || strlen($repw) < 5 ) {
            $this->error('密码长度不得小于5位！');
        } else if (strlen($pw) > 16 || strlen($repw) > 16) {
            $this->error('密码长度不得大于16位！');
        }
        
        $add_info = [
            'username'  =>  $username,
            'password'  =>  md5($pw),
            'email'     =>  $email,
            'phone'     =>  $phone,
        ];
        
        $res = $this->model->add($add_info);
        
        if( $res ){
            $this->add_active_log('添加管理员：'.$username.'成功，序号为'.$res.'！');
            $this->success('添加成功！');
        }
        else{
            $this->error('添加失败，请重试！');
        }

    }
    

    public function edit_admin(){
        $this->id = trim(I('id'));
        $this->display();
    }

    public function edit_message(){
        $this->id = trim(I('id'));
        $user = $this->model->where(array('id' => $this->id))->find();
        $this->username = $user['username'];
        $this->display();
    }

    // 修改管理员密码

    public function edit_admin_password(){

        $id = trim(I('id'));
        $user = $this->model->where(array('id' => $id))->find();
        $name = $user['username'];
        
        if(empty($user)){
            $this->error('没有该管理员!');
        }
        if( !in_array($this->aid, $this->superids)){
            $oldpw = trim(I('oldpw'));
            if(empty($oldpw) || md5($oldpw) != $user['password']){
                $this->error('旧密码不正确!');
                return;
            }
        }
        
        $newpw = trim(I('newpw'));
        $repw = trim(I('repw'));
        
        if( empty($newpw) || empty($repw) || $newpw != $repw){
            $this->error('请输入密码，并确认两次输入密码是否一致！');
        } else if (strlen($newpw) < 5 || strlen($repw) < 5 ) {
            $this->error('密码长度不得小于5位！');
        } else if (strlen($newpw) > 16 || strlen($repw) > 16) {
            $this->error('密码长度不得大于16位！');
        }

        $newpw = md5($newpw);
        
        if( $newpw == $user['password'] ){
            $this->error('新密码与原密码一致!');return;
        }
        
        
        $data['password'] = $newpw;
        if ($this->model->where(array('id' => $id))->save($data)) {
            $this->add_active_log('管理员'. $name .'修改密码');
            $this->success('修改密码成功！');
        } else {
            $this->error('修改失败！');
        }
        
    }

    // 修改管理员资料
    public function edit_admin_message(){
        $id = trim(I('id'));
        $user = $this->model->where(array('id' => $id))->find();

        if(empty($user)){
            $this->error('没有该管理员!');
        } else {
            $oldname = $user['username'];
            $username = trim(I('username'));
            $phone = trim(I('phone'));
            $email = trim(I('email'));


    
            if(!empty($phone) && !preg_match('/^(1[1|2|3|4|5|6|7|8|9][0-9])\d{8}$/', $phone)){
                $this->error('手机格式不正确！');
            }

            if(!empty($email) && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email)){
                $this->error('邮箱格式不正确！');
            }

            $all_username = $this->model->field('username')->select();

            foreach($all_username as $key=>$value){ 
                foreach($value as $key2=>$value2){ 
                    if($username == $value2) {
                        $this->error('该用户名已经存在了！');
                    }
                } 
            }

            if(!empty($username)){
                $data['username'] = $username;
            }

            if(!empty($phone)){
                $data['phone'] = $phone;
            }

            if(!empty($email)){
                $data['email'] = $email;
            }
    

            $res = $this->model->where(array('id' => $id))->save($data);
            if ($res) {
                $this->add_active_log('管理员'. $oldname .'修改资料');
                $this->success('修改资料成功！');
            } else {
                $this->error('修改资料失败！');
            }
            
        }
    }

    // 删除管理员
    public function delete_admin(){
        
        $id = trim(I('id'));
        $user = $this->model->where(array('id' => $id))->find();
        $username = $user['username'];
        if(empty($user)){
            $this->error('没有该管理员!');
        } else if (in_array($id, $this->superids)){
            $this->error('无法删除超级管理员！');
        } else {
            $res = $this->model->where(array('id' => $id))->delete();
            if($res) {
                $this->success('删除管理员成功！');
                $this->add_active_log('删除管理员'.$username);
            } else {
                $this->error('删除管理员失败！');
            }
        } 
    }
}

?>