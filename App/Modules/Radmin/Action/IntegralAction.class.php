<?php

/**
 * 	乐家帮代理管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class IntegralAction extends CommonAction {

    
    //代理资金记录
    public function index(){
        
        $condition = array();
        $distributor_obj = M('distributor');
        
        $uid = I('uid');
        $name =trim(I('get.name')) ;
        
        if( !empty($name) ){
            $sear_dis_info = $distributor_obj->where(array('name'=>$name))->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        if( !empty($uid) ){
            $condition['uid'] = $uid;
        }
        
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        $result = $Integral->get_integral_info($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
        $this->display();
    }
    
    
    
    
    
    //充值记录
    public function log(){
        
//        $money_recharge_log = M('money_recharge_log');
        $distributor_obj = M('distributor');
//        import('ORG.Util.Page');
        $condition = array();
        
        $type = trim(I("get.type"));
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        if( is_numeric($type) ){
            $condition['type'] = $type;
        }
        
        if( !empty($name) ){
            $sear_dis_info = $distributor_obj->where(array('name'=>$name))->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        $result = $Integral->get_integral_log($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->integral_status = $Integral->integral_status;
        $this->integral_class = $Integral->integral_class;

        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
        $this->display();
    }//end func log
    
    
    //积分规则
    public function rule(){
        
//        $money_recharge_log = M('money_recharge_log');
        $distributor_obj = M('distributor');
//        import('ORG.Util.Page');
        $condition = array();
        
        $type = I("get.type");
        $name = trim(I('get.name'));
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        if( is_numeric($type) ){
            $condition['type'] = $type;
        }
        
        if( !empty($name) ){
            $sear_dis_info = $distributor_obj->where(array('name'=>$name))->find();
            
            $condition['uid']  =   !empty($sear_dis_info)?$sear_dis_info['id']:'0';
        }
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        $result = $Integral->get_integral_rule($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->integral_rule_type = $Integral->integral_rule_type;
        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
        $this->display();
    }//end func rule
    
    
    //订单限制限制编辑
    public function rule_edit(){
        $id = I('id');
        
        $list = array(
            
        );
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        if( !empty($id) ){
            $model_obj = M($Integral->Integral_rule_obj);
        
            $condition = array(
                'id'    =>  $id,
            );

            $list = $model_obj->where($condition)->find();
        }
        
        $level_name = C('LEVEL_NAME');
        $level_name[0] = '所有级别';
        
        $this->types = $Integral->integral_rule_type;
        $this->level_name = $level_name;
        $this->list = $list;
        $this->display();
    }//end func rule_edit
    
    
    //订单限制提交
    public function rule_post(){
        
        $info = I();
        
//        print_r($info);return;
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        
        $result = $Integral->add_rule($info);
        
        if( $result['code'] == 1  ){
            $id = $result['id'];
            
            $this->add_active_log('积分规则编辑，序号：'.$id);
            $this->success('编辑积分规则成功！');
        }
        else{
            $this->error($result['msg']);
        }
        
        
    }//end func rule_post
    
    
    //订单限制删除
    public function rule_delete(){
        $id = I('id');
        
        if( empty($id) ){
            $this->error('参数错误！');
        }
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        $model_obj = M($Integral->Integral_rule_obj);
        
        $condition = array(
            'id'    =>  $id,
        );
        $result = $model_obj->where($condition)->delete();
        
        
        if( $result ){
            $this->add_active_log('积分规则删除，序号：'.$id);
            $this->success('删除成功！');
        }
        else{
            $this->error('删除失败，请重试！');
        }
        
    }//end func rule_delete
    
    //显示扣除积分页面
    public function recharge_refund_submit(){
        $uid = I('get.uid');
        $score = trim(I('get.score'));
        $name = trim(I('get.name'));
        $this->uid=$uid;
        $this->score=$score;
        $this->name=$name;
        $this->display();
    }
    
    //总部扣除积分提交
    public function refund_submit(){
        
        $uid = I('post.uid');
        $score = trim(I('post.score'));
        $note = trim(I('post.note'));

//        print_r(I());return;
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        $res = $Integral->charge($uid,$score,2,['note'=>$note]);
        
//        print_r($res);return;
        
        if( $res['code'] != 1 ){
            $this->error($res['msg']);
            return;
        }
        
        $this->add_active_log('总部扣除积分，用户ID：.'.$uid.'，积分：'.$score);
        
        $msg = !empty($res['msg'])?$res['msg']:'扣除积分成功！';
        $this->success($msg);
    }
    
    
    
    
    //总部充入积分
    public function recharge(){
        $uid = I('get.uid');
        
        if( empty($uid) ){
            $this->error('请选择用户！');
        }
        
        $distributor_obj = M('distributor');
        $Integral_obj = M('Integral');
        
        $where_dis = array(
            'id'   =>  $uid,
        );
        $dis_info = $distributor_obj->where($where_dis)->find();
        
        //查看该代理的资金表
        $info = $Integral_obj->where(array('uid'=>$uid))->find();
        $score = empty($info)?0:$info['score'];
        
        
        $this->score = $score;//积分
        $this->dis_info =   $dis_info;
        $this->display();
    }
    
    //总部充入积分提交
    public function recharge_submit(){
        
        $uid = I('post.uid');
        $score = trim(I('post.score'));
        $note = trim(I('post.note'));
        
        // print_r($this->_post());return;
        
        import('Lib.Action.Integral','App');
        $Integral = new Integral();
        
        $recharge_info['note']  =   $note;
        
        $result = $Integral->recharge($uid,$score,'1',$recharge_info);
        
//        print_r($result);return;
        
        if( $result['code'] != 1 ){
            $this->error($result['msg']);
        }
        else{
            $this->add_active_log('充入积分，用户ID:'.$uid.'，积分：'.$score);
            
            $msg = !empty($result['msg'])?$result['msg']:'充入积分成功！';
            
            $this->success($msg);
        }
    }//end func recharge_submit
    
    
    //积分门槛
    public function level(){
        $model_obj = M('integral_level');
        
        
        $condition = [];
        
        $list = array();
        
        //每页的数量
        $page_list_num = 10;
        //如果页码为空的话默认值为1
        $page_num = I('get.p');
        $page_num = empty($page_num)?1:$page_num;
        
        
        $count = $model_obj->where($condition)->count();
        if( $count > 0 ){
            
            $page_con = $page_num.','.$page_list_num;

            $list = $model_obj->where($condition)->order('id desc')->page($page_con)->select();
        }
        
        //*分页显示*
        import('ORG.Util.Page');
        $p = new Page($count, $page_list_num);
        $page = $p->show();
        
        
        
        $this->list =   $list;
        $this->page =   $page;
        
        $this->display();
    }//end func level
    
    
    //订单限制限制编辑
    public function level_edit(){
        $id = I('id');
        
        $model_obj = M('integral_level');
        
        $list = [];
        if( !empty($id) ){
        
            $condition = array(
                'id'    =>  $id,
            );

            $list = $model_obj->where($condition)->find();
        }
        
        $this->list = $list;
        $this->display();
    }//end func rule_edit
    
    
    //订单限制提交
    public function level_post(){
        
        $name = trim(I('name'));
        $score = trim(I('score'));
        $id = I('id');
        
//        print_r($info);return;
        
        $model_obj = M('integral_level');
        
        $condition = [
            'id'    =>  $id,
        ];
        
        $save_info = [
            'name'  =>  $name,
            'score' =>  $score,
        ];
        
        $save_result = $model_obj->where($condition)->save($save_info);
        
        
        if( $save_result ){
            
            $this->add_active_log('积分门槛编辑，序号：'.$id);
            $this->success('编辑积分门槛成功！',__URL__.'/level');
        }
        else{
            $this->error('编辑积分门槛失败！');
        }
        
        
    }//end func rule_post
    
    

}

?>