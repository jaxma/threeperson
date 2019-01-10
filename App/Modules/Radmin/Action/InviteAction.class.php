<?php

/**
 * 	topos经销商管理系统
 */
class InviteAction extends CommonAction {
    

    //显示二维码
    public function index() {
        $this->level_name = C('LEVEL_NAME');
        $this->ym_domain = C('YM_DOMAIN');

        $level = I('level');

        if( empty($level) ){
            $level = 1;
        }
        
        $this->level = $level;
        $this->display();
    }
    
    //生成链接
    public function generate_link_ajax(){
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $name = trim(I('name'));                //姓名
        $level = I('level');                    //级别
        $wechatnum = trim(I('wechatnum'));      //微信号
        $phone = trim(I('phone'));              //手机号
        $idennum = trim(I('idennum'));          //身份证
        $email = trim(I('email'));              //邮箱
        $deadline = I('deadline');              //截止时间
        $pid = I('pid');
        $sear_pid = I('sear_pid');
        
        
        if( empty($level) || empty($wechatnum) || empty($phone) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '参数传递错误！',
            ];

            $this->ajaxReturn($return_result);
            return;
        }
        
        if( !is_numeric($deadline) || strlen($deadline) != 8 ){
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '时间格式错误！',
            ];

            $this->ajaxReturn($return_result);
            return;
        }
        
        
        $distributor_obj = M('distributor');
        
        $condition = [
            'wechatnum' =>  $wechatnum,
        ];
        $dis_info = $distributor_obj->field('id,name')->where($condition)->find();
        
        if( !empty($dis_info) ){
            $return_result = [
                'code'  =>  4,
                'msg'   =>  '该微信号已经被《'.$dis_info['name'].'》申请为代理，无法再次申请',
            ];

            $this->ajaxReturn($return_result);
            return;
        }
        
        if( empty($pid) ){
            $pid = $sear_pid;
        }
        
        
        
        $link = 'http://'.C('YM_DOMAIN').'/admin/development/auto_sign_up';
        
        
        $data = [
            'name'      =>  $name,
            'pid'       =>  $pid,
            'level' =>  $level,
            'wechatnum' =>  $wechatnum,
            'phone'     =>  $phone,
            'idennum'   =>  $idennum,
            'email'     =>  $email,
            'deadline'  =>  $deadline,
        ];
        
        $data_str = serialize($data);
        
        $encode_data = tiriEncode($data_str);
        
        
        $invite_link = $link.'?data='.$encode_data;
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $invite_link,
        ];
        
        
        $this->ajaxReturn($return_result);
    }//end func generate_link
    
    
    
}

?>