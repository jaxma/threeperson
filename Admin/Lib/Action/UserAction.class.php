<?php
class UserAction extends CommonAction {
    public function index($user_type=null) {
    	$where = '1';
    	$count = M('users')->where($where)->count();

    	import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();

    	$user_list = M('users')->where($where)->limit($page->firstRow.','.$page->listRows)->select();

    	foreach($user_list as $key=>$value){
    		$user_list[$key]['company'] 		= $value['company']? $value['company'] : '无';
    		$user_list[$key]['last_login_time']	= date('Y-m-d H:i:s', $value['last_login_time']);
    		$user_list[$key]['reg_time']		= date('Y-m-d H:i:s', $value['reg_time']);
    	}

        $this->assign("page", $showPage);
        $this->assign('list', $user_list);

		$this->display();
    }


    public function edit($user_id){
    	$info = M('users')->find($user_id);
    	$this->assign('info', $info);

    	$this->display();
    }

    //更新用户资料
    public function update(){
    	$data 		= M('users')->create();
    	$user_id 	= $data['user_id']+0;
    	unset($data['user_id']);

    	M('users')->where("user_id=$user_id")->save($data);

    	$this->success('个人信息更新成功！');
    }

    public function drop($user_id, $p=1){
    	M('users')->where("user_id=$user_id")->delete();
    	$this->redirect('User/index', array('p'=>$p));
    }

    public function batch(){

    }
}