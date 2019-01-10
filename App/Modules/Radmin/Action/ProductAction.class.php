<?php

//此action主要是编辑，删除出库记录
class ProductAction extends CommonAction {

    //编辑
    public function edit() {
        $id = $_GET['id'];
        $type = I('get.type');
        $product = M('Product');
        $row = $product->find($id);
        $count = $product->where(array('send_id' => $row['receive_id'], 'ptag_name' => $row['ptag_name']))->count('id');
        if ($count > 0) {
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('标签已出库给下个经销商,无法编辑!');history.go(-1);</script>";
            exit();
        }
        $row = M('Distributor')->field('id,pid,level')->find($row['receive_id']);
        $templet = M('Templet')->field("id,name")->select();
        $this->templet = $templet;
        $this->id = $id;
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    public function update() {
        $pbeg = I('pbeg');
        $ptag_name = I('ptag_name');
        $id = I('post.id');
        if (!$ptag_name) {
            $this->error('标签不能为空!');
        }
        //不能多标签修改
        $count_tag = count($pbeg);
        if ($count_tag > 1) {
            $this->error('只能扫描一个标签!');
        }
        $pg = D('Product')->where(array('id' => $id))->getField('ptag_name');
        if ($ptag_name[0] != $pg) {
            //只有一个标签
            for ($j = 0; $j < $count_tag; $j ++) {
                $where['ptag_name'] = $ptag_name[$j];
                $where['status'] = 'system';
                $count = M('Product')->where($where)->count('id');
                if ($count > 0) {
                    $this->error("该标签已经出库,不能重复出库！");
                    break;
                }
            }
        }
        $m_bid = toValid(I('templet_id'));
        $m_agent_id = toValid(I('receive_id'));
        $m_templet_id = toValid(I('templet_id'));
        if (!$m_agent_id) {
            $this->error('经销商不能为空!');
        }
        if (!$m_templet_id) {
            $m_templet_id = 1;
        }
        $m_product = M('Product');
        if ($m_product->create()) {
            $m_pbeg = $pbeg;
            $m_pend = I('pend');
            $m_ptag_name = $ptag_name;
            $m_product_num = I('product_num');
            for ($i = 0; $i < $count_tag; $i ++) {
                $data = array(
                    'send_id' => 0,
                    'receive_id' => $m_agent_id,
                    'templet_id' => $m_templet_id,
                    'ptag_name' => $m_ptag_name[$i],
                    'mbeg' => $m_pbeg[$i],
                    'mend' => $m_pend[$i],
                    'product_num' => $m_product_num[$i],
                    'orderNumber' => 0,
                    'status' => 'system',
                    'time' => time(),
                    'pid' => 0
                );

                $m_result[] = $m_product->where(array('id' => $id))->save($data);
            }
            if (count($m_result) == $count_tag && count($m_result) > 0) {
                $this->add_active_log('标签出库');
                $this->success("出库修改成功！", __APP__ . '/Radmin/Stock/stock/?type=system');
            } else {
                $this->error("出库修改失败！");
            }
        } else {
            $this->error($m_product->getError());
        }
    }

    //修改大标记录
    public function change() {
        $id = I('post.id');
        $type = I('get.type');
        $m_templet_id = I('post.templet_id');
        $m_agent_id = I('post.receive_id');
        if (!$m_agent_id) {
            $this->error('经销商不能为空!');
        }
        if (!$m_templet_id) {
            $m_templet_id = 1;
        }
        
        $send_id = M('Product')->where(array('id' => $id))->getField('send_id');
        
        if( $send_id == $m_agent_id ){
            $this->error("发货经销商和收货经销商不能是同一人！");return;
        }
        
        $data['receive_id'] = $m_agent_id;
        $data['templet_id'] = $m_templet_id;
        $flag = M('Product')->where(array('id' => $id))->save($data);
        if ($flag) {
            if ($type == 'big') {
                $this->add_active_log('修改大标记录');
                $this->success("出库修改成功！");
            } else {
                $this->add_active_log('修改标签记录');
                $this->success("出库修改成功！");
            }
        } else {
            $this->error("出库修改失败！");
        }
    }

    //删除
    public function delete() {
        $id = $_GET['id'];
        $product = D('Product');
        //标签如果出库就不能删除
        $row = $product->find($id);
        $count = $product->where(array('send_id' => $row['receive_id'], 'ptag_name' => $row['ptag_name']))->count('id');
        if ($count > 0) {
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('标签已出库给下个经销商,无法删除!');history.go(-1);</script>";
            exit();
        }
        $res = $product->where(array('id' => $id))->delete();
        if ($res) {
            $this->add_active_log('删除标签记录');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //获得模板
    public function templet() {
        $list = M('Templet')->field('id,name')->select();
        $this->ajaxReturn($list, 'JSON');
    }

    //找下属经销商
    public function getAgent($row) {
        if (!$row) {
            return false;
        } else {
            //找出属于自己发的链接并等级小于自己的经销商， 并且找出发给自己链接并且等级小于自己的经销商
            $where['level'] = array('egt', $row['level']);
            $where['pid'] = $row['id'];
            $map['level'] = array('egt', $row['level']);
            $map['id'] = $row['pid'];
            $a['_complex'] = $where;
            $condition[] = $a;
            $condition['_logic'] = 'OR';
            $condition['_complex'] = $map;
            $agent = M('Distributor')->field("id,name")->where($condition)->order('level desc')->select();
            return $agent;
        }
    }

}

?>