<?php

/**
 * 	topos经销商管理系统
 */
class UserAction extends CommonAction {

    //管理员用户列表
    public function index() {
        import('ORG.Util.Page');

        $admin = M('Admin')->where('did != 0')->select();
        $count = count($admin);
        for ($i = 0; $i < $count; $i++) {
            $id = $admin[$i]['did'];
            $dist = M('Distributor')->field('name')->find($id);
            $admin[$i]['name'] = $dist['name'];
        }
        $p = new Page($count, 20);
        $admin = array_splice($admin, $p->firstRow, $p->listRows);
        $this->admin = $admin;
        $this->page = $p->show();
        $this->display();
    }

    //添加管理员用户
    public function add() {
        //查询尚未成为管理员的联盟总代
        $d = M('Distributor')->where(array('level' => 1))->field(array('id', 'name'))->select();
        $a = M('Admin')->field(array('did'))->select();
        $admin = array();
        foreach ($a as $v) {
            $admin[] = $v['did'];
        }
        $dist = array();
        foreach ($d as $v) {
            if (!in_array($v['id'], $admin)) {
                $dist[] = $v;
            }
        }
        $this->dist = $dist;
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    public function addUser() {
        $did = I('did');
        if ($did == "") {
            $this->error('请选择' . C('LEVEL_NAME')[1]);
        }
        $username = I('username');
        if ($username == "") {
            $this->error('用户名不能为空');
        }
        $password = I('password');
        if ($password == "") {
            $this->error('请设置密码');
        }
        //检查用户名是否已存在
        $admin = M('Admin')->where(array('username' => $username))->find();
        if ($admin) {
            $this->error('该用户名已存在');
        }
        $user = array(
            'username' => $username,
            'password' => md5($password),
            'phone' => I('phone'),
            'email' => I('email'),
            'did' => $did
        );
        $res = M('Admin')->add($user);
        if ($res) {
            $this->add_active_log('添加管理员：' . $username);
            $this->success('用户添加成功', index);
        } else {
            $this->error('用户添加失败');
        }
    }

    //重置密码
    public function edit() {
        $id = $_GET['aid'];
        $admin = M('Admin')->find($id);
        $dist = M('Distributor')->field('name')->find($admin['did']);
        $admin['name'] = $dist['name'];
        $this->admin = $admin;
        $this->display();
    }

    public function editUser() {
        $id = $_GET['aid'];
        $pwd = I('password');
        if ($pwd == "") {
            $this->error('请填写新密码');
        }
        $con = I('confirm');
        if ($con == "") {
            $this->error("请填写确认密码");
        }
        if ($con != $pwd) {
            $this->error("新密码与确认密码不一致");
        }
        $username = I('username');
        if ($username == "") {
            $this->error('用户名不能为空');
        }
        $admin = array(
            "id" => $id,
            "username" => $username,
            'phone' => I('phone'),
            'email' => I('email'),
            "password" => md5($pwd)
        );
        $res = M('Admin')->save($admin);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $this->add_active_log('管理员修改密码：' . $username);
            $this->success("操作成功", index);
        }
    }

    //删除用户
    public function delete() {
        $id = $_GET['aid'];
        $res = M('Admin')->where(array('id' => $id))->delete();
        if ($res) {
            $this->add_active_log('管理员删除');
            $this->success('删除成功', index);
        } else {
            $this->error('删除失败');
        }
    }


    // 网站链接
    public function weblink(){
        $this->display();
    }

}

?>