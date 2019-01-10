<?php
/**
*	topos经销商管理系统--营销模块
*/
class SaleAction extends CommonAction
{
    //
    public function index(){
        $condition = array();
        
        $name = trim(I('get.name'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        
        if( !empty($name) ){
            $condition['name']  =   ['like',$name];
        }

        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_user($page_info,$condition);

        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
        $this->display();
    }
    
    //记录
    public function record(){
        $condition = array();
        
        $name = trim(I('get.name'));
        $sale_code = trim(I('sale_code'));
        $salename = trim(I('salename'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
        if( !empty($name) ){
            //        读取sale_user表
            $user_info = M('sale_user');
            $dis_List = $user_info->where(array('name'=>$name))->field('id,name')->select();
            if(!empty($dis_List)){
                $v_tem_id = array();
                foreach( $dis_List as $k_tem => $v_tem ){
                    $v_tem_id[] = $v_tem['id'];
                    $condition_temp[$v_tem_id] = $v_tem;
                    $condition['sale_id']  =   array('in',$v_tem_id);
                }
            }else{
                $condition['sale_id']  =  $name;
            }

        }
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;

            $condition['created'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        if( !empty($salename) ){
            $condition['salename'] = $salename;
        }
        
        if( !empty($sale_code) ){
            $condition['sale_code']  =   $sale_code;
        }
        
        
        
        //获取充值记录
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_record($page_info,$condition);
        
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
//        print_r($result);return;
        $this->display();
    }//end func record
    
    
    //订单
    public function order(){
        $condition = array();
        
        $name = trim(I('get.name'));
        $order_num = trim(I('order_num'));
        $status = trim(I('status'));
        $start_time = I('start_time');
        $end_time = I('end_time');
        $p_name = trim(I('p_name'));
        $sale_code = trim(I('sale_code'));


        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }



        if( !empty($name) ){
            //        读取sale_user表
            $user_info = M('sale_user');
            $dis_List = $user_info->where(array('name'=>$name))->field('id,name')->select();
            
            if(!empty($dis_List)){
                $v_tem_id = array();
                foreach( $dis_List as $k_tem => $v_tem ){
                    $v_tem_id[] = $v_tem['id'];
                    $condition_temp[$v_tem_id] = $v_tem;
                    $condition['sale_id']  =   array('in',$v_tem_id);
                }
            }else{
                $condition['sale_id']  =  $name;
            }

        }
        if( !empty($order_num) ){
            $condition['order_num']  =   $order_num;
        }
        if ( $status != NULL) {
            $condition['status'] = $status;
        }
        if( !empty($p_name) ){
            $condition['p_name'] = $p_name;
        }
        
        if( !empty($sale_code) ){
            $condition['sale_code']  =   $sale_code;
        }
        
        
        $page_info = array(
            'page_num' =>  I('get.p'),
        );
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_order($page_info,$condition);
        
        
        $con_url = '';
        if( !empty($condition) ){
            $con_url = serialize($condition);
        }
        $this->con_url = base64_encode($con_url);
        $this->status = $status;
        $this->page = $result['page'];
        $this->list = $result['list'];
        $this->sale_order_status = $Sale->sale_order_status;
        $this->p=I('p');
        $this->limit=$result['limit'];
        $this->count=$result['count'];
//        print_r($result);return;
        $this->display();
    }//end func order
    
    //订单审核
    public function order_audit(){
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        
        
        vendor("phpqrcode.phpqrcode");
        $mids = I('mids');
        $mids = substr($mids, 1);
        $order_nums = explode('_', $mids);
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $order_audit_result = $Sale->radmin_audit($order_nums);
        
        if( $order_audit_result['code'] == 1 ){
            $this->add_active_log('订单申请审核：'.$order_audit_result['msg']);
        }
        
        $this->ajaxReturn($order_audit_result, 'json');
    }//end func order_audit
    
    
    
    //轮盘基础配置
    public function lunpanbase(){
        
        $condition['type']  =   'lunpan';
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_sale_base([],$condition);
        
        $this->list = $result['list'];
        $this->display();
    }
    
    //获取轮盘信息
    public function get_lunpan_prize(){
        if(!IS_AJAX){
            return FALSE;
        }
        
        $sale_lunpan = M('sale_lunpan')->order('id')->select();
        
        $result = [
            'code' => '1',
            'info' => $sale_lunpan,
            'msg' => '获取成功!',
        ];
        
        $this->ajaxReturn($result);
        
    }
    
    //轮盘基础设置提交
    public function lunpanbase_post(){
        
        $lunpan_title = trim(I('lunpan_title'));
        $lunpan_active = trim(I('lunpan_active'));
        $lunpan_prizesinfo = trim(I('lunpan_prizesinfo'));
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        
        $result1 = $Sale->set_sale_base('lunpan_title','轮盘标题',$lunpan_title,'lunpan');
        $result2 = $Sale->set_sale_base('lunpan_active','轮盘活动说明',$lunpan_active,'lunpan');
        $result3 = $Sale->set_sale_base('lunpan_prizesinfo','轮盘奖品信息',$lunpan_prizesinfo,'lunpan');
        
        
        if( $result1['code'] != 1 ){
            $this->error($result1['msg']);
        }
        elseif( $result2['code'] != 1 ){
            $this->error($result2['msg']);
        }
        elseif( $result3['code'] != 1 ){
            $this->error($result3['msg']);
        }
        else{
            $this->success('设置成功!');
        }
        
    }//end func lunpanbase_post
    
    //轮盘设置
    public function lunpanset(){
        $condition['type']  =   'lunpan';
        
        //获取充值记录
        $page_info = array(
        );
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->get_lunpan_set($page_info,$condition);
        
        $this->list = $result['list'];
        $this->count = count($result['list']);
        $this->display();
    }
    
    //轮盘设置提交
    public function lunpanset_post(){
        
        $id = I('id');
        $name = I('name');
        $percent = I('percent');
        $total_num = I('total_num');
        $img_path = I('image_name');
        $money = I('money');

        $new_info = [
            'name'  =>  $name,
            'percent'   =>  number_format($percent,2),
            'total_num' =>  $total_num,
            'img'  =>  $img_path,
            'money' =>  $money,
        ];
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->set_lunpan($id,$new_info);
        
        $this->ajaxReturn($result);
        
    }//end func lunpanset_ajax
    
    
    //轮盘删除ajax
    public function lunpanset_del_ajax(){
        
        $id = I('id');
        
        import('Lib.Action.Sale','App');
        $Sale = new Sale();
        $result = $Sale->del_lunpan($id);
        
        $this->ajaxReturn($result);
        
    }//end func lunpanset_del_ajax
    
    
    
    
    
    
    
    function upload() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        //$upload->maxSize = 1048576;
        $upload->allowExts = array('jpg','jpeg','png'); // 设置附件上传类型
        $upload->savePath = './upload/lunpan/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = false;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        if (!$upload->upload()) {
            $msg = $this->error($upload->getErrorMsg());
            
            $return_result = array(
                'code'  =>  2,
                'msg'   =>  $msg,
            );
            
        } else {

            $info = $upload->getUploadFileInfo();
            $path = $info[0]['savepath'] . $info[0]['savename'];
            $path =  __ROOT__ . substr($path, 1);
            $return_result = array(
                'code'  =>  1,
                'msg'   =>  '成功导入！',
                'path'  =>  $path,
                'info'  =>  $info,
//                'extension' =>  $info['extension'],
            );
            
        }
        
        return $return_result;
    }//end func upload
    
    
    
    
}
?>