<?php

class InventoryAction extends CommonAction {

    public function index() {
        $inventory = M('inventory');
        $product_model = M('Product');
        $count = $inventory->count('id');
        import('ORG.Util.Page');
        $p = new Page($count, 10);
        $limit = $p->firstRow . "," . $p->listRows;
        
        
        $list = $inventory->limit($limit)->select();
        
        if( !empty($list) ){
//            $p_ids = array();//所有产品ID
//            foreach( $list as $k => $v ){
//                $v_uid = $v['uid'];
//                $v_pid = $v['pid'];
//                
//            }
            
            /**
             * 查询库存记录流出数更新时间以后的出库记录，
             * 查询一次又记录则更新进库存记录out_num出库数，减少查询
             */
            foreach( $list as $k => $v ){
                $v_uid = $v['uid'];
                $v_pid = $v['pid'];
                $v_out_num_updated = $v['out_num_updated'];
                $v_out_num = $v['out_num'];
                $v_total_num = $v['total_num'];

                $v_u_name = '';
                if( $v_uid == 0 ){
                    $v_u_name = '总部';
                }
                else{
                    $v_u_name = $v_uid;
                }
                
                
                //--------统计某产品出库数----------
//                $condition_product = array(
//                    'send_id'   =>  $v_uid,
//                    'templet_id'    =>  $v_pid,
//                    'time'  =>  array('egt',$v_out_num_updated),
//                );
//
//                //得到以产品ID为KEY的总出货数量
//                $product_list = $product_model->where($condition_product)->select();
//                
//                $new_out_num = 0;
//                foreach( $product_list as $p_k => $p_v ){
//                    //$p_v_templet_id = $p_v['templet_id'];
//                    $p_v_product_num = $p_v['product_num'];
//                    
//                    $new_out_num+=$p_v_product_num; 
//                }
//                
                //更新
//                if( $new_out_num > 0 ){
//                    $v_out_num = $v_out_num+$new_out_num;
//                    
//                    $condition_inventory_save = array(
//                        'uid'   =>  $v_uid,
//                        'pid'   =>  $v_pid,
//                    );
//                    
//                    $inventory_new_save = array(
//                        'out_num'   =>  $v_out_num,
//                        'out_num_updated'   =>  time(),
//                    );
//                    
//                    $save_result = $inventory->where($condition_inventory_save)->data($inventory_new_save)->save();
//                    
//                    if( !$save_result ){
////                        echo $product_model->getLastSql();return;
////                        print_r($product_new_save);return;
//                    }
//                }
                //--------end 统计某产品出库数----------
                
                //剩余量
                $v_surplus = $v_total_num - $v_out_num;
                if( $v_surplus < 0 ){
                    $v_surplus = 0;
                }
                
                
                $list[$k]['surplus'] = $v_surplus;
                $list[$k]['out_num'] = $v_out_num;
                $list[$k]['u_name'] =   $v_u_name;
            }
            
        }
        
        $page = $p->show();
        $this->page = $page;
        $this->start = $p->firstRow;
        $this->assign('list', $list);
        $this->display();
    }
    
    
    
    //修改库存记录页面
    public function edit() {
        $id = I('id');

        $one = M('inventory')->where(array('id' => $id))->find();
        

        $this->assign('one', $one);
        $this->display();
    }

    //删除库存记录
    public function del() {
        $id = I('id');
        $condition = array('id' => $id);
        $res = M('inventory')->where($condition)->delete();
        if ($res) {
            $this->success("删除成功", U('Radmin/Inventory/index'), 1);
        } else {
            $this->error("删除失败");
        }
    }

    //修改库存记录
    public function update() {
        $id = I('id');
        $Mw = M('inventory');
        $condition = array('id' => $id);
        $total_num = trim(I('total_num'));
        
        if( !is_numeric($total_num) ){
            $this->error("更新失败");
        }
        
        
        $data = array(
            "total_num" => $total_num,
            'updated'   =>  time(),
        );
        $res = $Mw->where($condition)->save($data);
        if ($res) {
            $this->success("更新成功", U('Radmin/Inventory/index'), 1);
        } else {
            $this->error("更新失败");
        }
    }

    //库存录入
    public function entering() {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        
        $templet_info = D('Templet')->order('time desc')->select();
        
        
        $this->templet_info =   $templet_info;
        $this->display();
    }

    //添加库存记录
    public function add() {
        $pid = trim(I('pid'));
        $number = trim(I('number'));
        
        $inventory_obj = M('inventory');
        
        
        if( empty($pid) ){
            $this->error("请选择产品！");
            return;
        }
        
        
        $inventory_info = $inventory_obj->where(array('pid'=>$pid))->find();
        
        if( !empty($inventory_info) ){
            $this->error("该产品已经录入，请勿重复录入！");
            return;
        }
        
        
        $data = array(
            'pid'   =>  $pid,
            'uid' => 0,
            'total_num' => $number,
            'created'   =>  time(),
            'updated'   =>  time(),
        );
        $res = $inventory_obj->add($data);

        if ($res) {
            $this->success("录入成功", U('Radmin/Inventory/index'), 1);
        } else {
            $this->error("录入失败");
        }
    }

    
    
    public function transmit() {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    
    public function transform() {
        $inventory = M('inventory');
        $o_id = I('o_id');
        $user_id = I('user_id');
        $o_id = $inventory->where(array('user_id' => $o_id))->find();
        $user_id = $inventory->where(array('user_id' => $user_id))->find();

        $inventory->startTrans();
        $number_user = $user_id['number'] + $o_id['number'];
        $number_oid = $o_id['number'] - $o_id['number'];

        $data_user = array('number' => $number_user);
        $data_oid = array('number' => $number_oid);

        $res1 = $inventory->where(array('user_id' => $user_id))->save($data_user);
        $res2 = $inventory->where(array('user_id' => $o_id))->save($data_oid);

        if ($res1 && $res2) {
            $inventory->commit();
            $this->ajaxReturn(1, JSON);
        } else {
            $inventory->rollback();
            $this->ajaxReturn(2, JSON);
        }
    }

    public function search() {
        $id = I('keyword');

        $inventory = M('inventory');
        $count = $inventory->where(array('user_id' => $id))->count('id');
        import('ORG.Util.Page');
        $p = new Page($count, 7);
        $limit = $p->firstRow . "," . $p->listRows;
        $list = $inventory->where(array('user_id' => $id))->join('distributor on inventory.user_id = distributor.id')->field('inventory.id as id,inventory.unit as unit,inventory.number as number,distributor.name as name,distributor.levname as levname')->limit($limit)->select();
        $page = $p->show();
        $this->page = $page;
        $this->start = $p->firstRow;
        $this->assign('list', $list);
        $this->display('index');
    }

}

?>