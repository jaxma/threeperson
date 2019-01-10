<?php

/**
 * 	topos经销商管理系统
 */
class StockAction extends CommonAction {

    //大中标签出库记录
    public function stock() {
        
        
        $product_model = M('Product');
        $distributor_model = M('distributor');
        
        
        $type = $this->_get('type');
        $start_time = $this->_get('start_time');
        $end_time = $this->_get('end_time');
        $mstock = $this->_get('mstock');
        $begbigstock = $this->_get('begbigstock');
        $endbigstock = $this->_get('endbigstock');
        $keyword = $this->_get('keyword');
        $is_sell = I('is_sell');
        $sellname = I('sellname');
        
        $condition_product = array();
        $condition = array();
        
        //标签属性
        if ($type == 'b') {
            $condition_product['statusbm'] = 'b';
        } else if ($type == 'm') {
            $condition_product['statusbm'] = 'm';
        } elseif( $type=='system' ){
            $condition_product['status'] = 'system';
        }
        
        //是否零售出货
        if( $is_sell == '1' ){
            $condition_product['is_sell'] = '1';
        }
        
        if( !empty($sellname) ){
            $condition = array(
                'sellname' =>  $sellname,
                '_logic'    =>  'or',
                'sellphone' =>  $sellname,
            );
        }
        
        
        //开始时间-结束时间
        if ( !empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            
            $condition['time'] = array(array('egt', $start_time), array('elt', $end_time));
        }
        
        //收货经销商/微信/手机
        if( !empty($keyword) ){
            if( is_numeric($keyword) ){
                $condition_dis['phone'] =   $keyword;
            }
            else{
                $condition_dis['name']  =   $keyword;
                $condition_dis['_logic'] = 'or';
                $condition_dis['wechatnum']  =   $keyword;
            }
            
            $distributor_info = $distributor_model->where($condition_dis)->find();
            
            $receive_id = $distributor_info['id'];
            
            $condition['receive_id'] = $receive_id;
        }
        
        //小标签
        if( !empty($mstock) ){
//            mbeg
//            mend
            $condition['mend'] = array('egt', $mstock);
            $condition['_logic'] = 'and';
            $condition['mbeg'] = array('elt', $mstock);
        }
        
        //起始大标签
        if( !empty($begbigstock) && !empty($endbigstock) ){
            $condition['ptag_name'] = array(array('egt',$begbigstock),array('elt',$endbigstock)) ;
//            $condition['ptag_name'] = array('egt', $begbigstock);
//            $condition['_logic'] = 'and';
//            $condition['ptag_name'] = array('elt', $endbigstock);
        }
        
        if( !empty($condition) ){
            $condition_product['_complex']  =   $condition;
        }
        
        
        
        $count = $product_model->where($condition_product)->count('id');
        $page_num=20;
        if ($count > 0 ) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $product_model->where($condition_product)->order('time desc')->limit($limit)->select();
            
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        
        $this->is_sell = $is_sell;
        $this->statusbm = $type;
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display('Stock/systemlog');
    }

    //系统大标出库
    public function systemBigStock() {
        $templet = M('Templet')->field("id,name")->select();
        $this->templet = $templet;
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //系统小标出库
    public function systemMStock() {
        $templet = M('Templet')->field("id,name")->select();
        $this->templet = $templet;
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //获取大标签
    public function BigPtag() {
        $ptag_name = I('post.m_url');
        $field = 'ptag_name,ptag_beg,ptag_end,ptag_total';
        $row = M('Ptag')->field($field)->where(array('ptag_name' => $ptag_name))->find();
        $this->ajaxReturn($row, 'JSON');
    }

    //获取小标签
    public function mPtag() {
        $mtag_name = I('post.m_url');
        $row = M('Mtag')->field("mtag_name")->where(array('mtag_name' => $mtag_name))->find();
        $this->ajaxReturn($row, 'JSON');
    }

    //获取模板
    public function templet() {
        $m_search = toValid(I('term'));
        $where['name'] = array('LIKE', '%' . $m_search . '%');
        $list = D('Templet')->field("id,name")->where($where)->limit(0, 12)->select();
        $this->ajaxReturn($list, 'JSON');
    }

    //确定大标出库
    public function e_bigStock() {
        $pbeg = I('pbeg');
        $m_pend = I('pend');
        $ptag_name = I('ptag_name');
        
//        print_r($this->_post());return;
        
        $product = M('Product');
        if (!$ptag_name) {
            setLog('标签不能为空', 'bigTagStock');
            $this->error('标签不能为空!');
        }
        //同一标签不能重复出库
        $ptag_count = array_count_values($ptag_name);
        foreach ($ptag_count as $v) {
            if ($v > 1) {
                setLog('不能有重复标签', 'bigTagStock');
                $this->error("不能有重复标签！");
                break;
            }
        }
        $count_tag = count($pbeg);
        $count_end = count($m_pend);
        if ($count_tag != $count_end) {
            setLog('首尾小标数量不一致', 'bigTagStock');
            $this->error("首尾小标数量不一致");
        }
        for ($j = 0; $j < $count_tag; $j ++) {
            $where['ptag_name'] = $ptag_name[$j];
            $where['status'] = 'system';
            $count = $product->where($where)->count('id');
            if ($count > 0) {
                setLog($ptag_name[$j] . '标签重复出库', 'bigTagStock');
                $this->error($ptag_name[$j] . "标签已经出库,不能重复出库！");
                break;
            }

            //new code
            $wherea["mbeg"] = array('elt', $pbeg[$j]); //ok
            $wherea["mend"] = array('egt', $pbeg[$j]); //ok
            //$wherea["status"]= 'system';
            $wherea["statusbm"] = 'm'; //ok
            $listcont = $product->where($wherea)->count();
            if ($listcont > 0) {
                $this->error("该标签已经出库,不能重复出库！");
                break;
            }
            $wherea["mbeg"] = array('elt', $m_pend[$j]); //ok
            $wherea["mend"] = array('egt', $m_pend[$j]); //ok
            $wherea["statusbm"] = 'm'; //ok
            $listcont = $product->where($wherea)->count();
            if ($listcont > 0) {
                $this->error("该标签已经出库,不能重复出库！");
                break;
            }

            //old code
//			if(($m_pend[$j] - $pbeg[$j] + 1) != C('TAG_LEN')){
//				setLog($pbeg[$j]."~".$m_pend[$j]."标签范围有误",'bigTagStock');
//				$this->error($pbeg[$j]."~".$m_pend[$j]."标签范围有误！");
//				break;
//			}
//			//add by z 具体到哪个小标已经出库
//			$num = $pbeg[$j];
//			for($c = 0;$c < C('TAG_LEN');$c++){
//				$wherea["mbeg"]= array('elt',$num);
//				$wherea["mend"]= array('egt',$num);
//				$wherea["status"]= 'system';
//				$wherea["statusbm"]= 'm';
//				$listcont = $product->where($wherea)->count();
//				if($listcont > 0){
//					setLog($num.'小标签已经出库,对应的大标不能出库','bigTagStock');
//					$this->error($num."小标签已经出库,该大标不能出库！");
//					break;
//				}
//				$num++;
//			}
        }
        $m_bid = toValid(I('templet_id'));
        $m_agent_id = toValid(I('receive_id'));
        $m_templet_id = toValid(I('templet_id'));
        if (!$m_agent_id) {
            setLog('经销商不能为空', 'bigTagStock');
            $this->error('经销商不能为空!');
        }
        if (!$m_templet_id) {
            $m_templet_id = 1;
        }
        $m_product = M('Product');
        if ($m_product->create()) {
            $m_pbeg = $pbeg;
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
                    'time' => time(),
                    'status' => 'system',
                    'pid' => 0,
                    'statusbm' => 'b'
                );

                $m_result[] = $m_product->add($data);
            }
            if (count($m_result) == $count_tag && count($m_result) > 0) {
                $this->success("出库成功！");
            } else {
                setLog('出库失败', 'bigTagStock');
                $this->error("出库失败！");
            }
        } else {
            $this->error($m_product->getError());
        }
    }

    //小标出库
    public function m_mStock() {
        $pbeg = I('pbeg');
        $m_pend = I('pend');
        $m_agent_id = toValid(I('receive_id'));
        $m_templet_id = toValid(I('templet_id'));

        $count_tag = count($pbeg);
        $count_end = count($m_pend);
        if ($count_tag != $count_end) {
            setLog('首尾标签数量不一致', 'mTagStock');
            $this->error("首尾标签数量不一致");
        }
        if (!$m_agent_id) {
            setLog('经销商不能为空', 'mTagStock');
            $this->error('经销商不能为空!');
        }
        if (!$m_templet_id) {
            $m_templet_id = 1;
        }
        $product = M('Product');
        for ($j = 0; $j < $count_tag; $j ++) {
            if ($pbeg[$j] == $m_pend[$j]) {
                $where["mbeg"] = array('elt', $pbeg[$j]);
                $where["mend"] = array('egt', $pbeg[$j]);
                $listcont = $product->where($where)->count();
                if ($listcont > 0) {
                    setLog($pbeg[$j] . '标签已经出库', 'mTagStock');
                    $this->error($pbeg[$j] . "标签已经出库,不能重复出库！");
                    break;
                }
            } else {
//				//标签不能超过一个大标的长度
//				$tag_len = $m_pend[$j] - $pbeg[$j] + 1;
//				if($tag_len >= C('TAG_LEN')){
//					setLog($pbeg[$j]."~".$m_pend[$j]."的标签长度超过了一个大标的长度,请使用大标出库功能出库",'mTagStock');
//					$this->error($pbeg[$j]."~".$m_pend[$j]."的标签长度超过了一个大标的长度,请使用大标出库功能出库！");
//					break;
//				}
                $where["mbeg"] = array('elt', $pbeg[$j]); //ok
                $where["mend"] = array('egt', $pbeg[$j]); //ok
                $listcont = $product->where($where)->count();
                if ($listcont > 0) {
                    setLog($pbeg[$j] . "~" . $m_pend[$j] . "范围内有标签已经出库,或者大标已经出库,不能重复出库", 'mTagStock');
                    $this->error($pbeg[$j] . "~" . $m_pend[$j] . "范围内有标签已经出库,或者大标已经出库,不能重复出库！");
                    break;
                }
                $where["mbeg"] = array('elt', $m_pend[$j]); //ok
                $where["mend"] = array('egt', $m_pend[$j]); //ok
                $listcont = $product->where($where)->count();
                if ($listcont > 0) {
                    setLog($pbeg[$j] . "~" . $m_pend[$j] . "范围内有标签已经出库,或者大标已经出库,不能重复出库222", 'mTagStock');
                    $this->error($pbeg[$j] . "~" . $m_pend[$j] . "范围内有标签已经出库,或者大标已经出库,不能重复出库！");
                    break;
                }
            }
            //大标出库了小标就不能出库了

            if ($m_pend[$j] == "" || $pbeg[$j] == "") {
                setLog('标签不能为', 'mTagStock');
                $this->error("标签不能为空！");
                break;
            }
            if (0 > $m_pend[$j] - $pbeg[$j]) {
                setLog($pbeg[$j] . "起始标签不能大于" . $m_pend[$j] . "结束标签", 'mTagStock');
                $this->error($pbeg[$j] . "起始标签不能大于" . $m_pend[$j] . "结束标签！");
                break;
            }
            $count = M('Mtag')->where(array('mtag_name' => $m_pend[$j]))->count('mtag_id');
            $counta = M('Mtag')->where(array('mtag_name' => $pbeg[$j]))->count('mtag_id');
            if ($count == 0 || $counta == 0) {
                setLog($pbeg[$j] . "~" . $m_pend[$j] . "标签不存在", 'mTagStock');
                $this->error($pbeg[$j] . "~" . $m_pend[$j] . "标签不存在！");
                break;
            }
        }

        $m_product = M('Product');
        if ($m_product->create()) {
            $m_pbeg = $pbeg;
            for ($i = 0; $i < $count_tag; $i ++) {
                $m_product_num[$i] = $m_pend[$i] - $m_pbeg[$i] + 1;
                $data = array(
                    'send_id' => 0,
                    'receive_id' => $m_agent_id,
                    'templet_id' => $m_templet_id,
                    'ptag_name' => 'mtag',
                    'mbeg' => $m_pbeg[$i],
                    'mend' => $m_pend[$i],
                    'product_num' => $m_product_num[$i],
                    'orderNumber' => 0,
                    'time' => time(),
                    'status' => 'system',
                    'pid' => 0,
                    'statusbm' => 'm'
                );
                $m_result[] = $m_product->add($data);
            }
            if (count($m_result) == $count_tag && count($m_result) > 0) {
                $this->success("出库成功！");
            } else {
                setLog('出库失败', 'mTagStock');
                $this->error("出库失败！");
            }
        } else {
            $this->error($m_product->getError());
        }
    }

    //确定小标出库
    public function e_mStock() {
        $ptag_name = I('mtag_name');
        if (!$ptag_name) {
            $this->error('标签不能为空!');
        }
        //同一标签不能重复出库
        $ptag_count = array_count_values($ptag_name);
        foreach ($ptag_count as $v) {
            if ($v > 1) {
                $this->error("不能有重复标签！");
                break;
            }
        }
        $count_tag = count($ptag_name);
        for ($j = 0; $j < $count_tag; $j ++) {
            $where['ptag_name'] = $ptag_name[$j];
            $where['status'] = 'system';
            $count = M('Product')->where($where)->count('id');
            if ($count > 0) {
                $this->error("该标签已经出库,不能重复出库！");
                break;
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
            for ($i = 0; $i < $count_tag; $i ++) {
                $data = array(
                    'send_id' => 0,
                    'receive_id' => $m_agent_id,
                    'templet_id' => $m_templet_id,
                    'ptag_name' => $ptag_name[$i],
                    'product_num' => 1,
                    'orderNumber' => 0,
                    'time' => time(),
                    'status' => 'system',
                    'pid' => 0
                );

                $m_result[] = $m_product->add($data);
            }
            if (count($m_result) == $count_tag && count($m_result) > 0) {
                $this->success("出库成功！", 'stock/?type=system');
            } else {
                $this->error("出库失败！");
            }
        } else {
            $this->error($m_product->getError());
        }
    }

    public function getAgent() {
        $level = I("level");
        $list = M('Distributor')->field("id,name,level,levname")->where(array('level' => $level, 'audited' => 1))->order('convert(name using gbk) asc')->select();
        if ($list) {
            $this->ajaxReturn($list, 'json');
        } else {
            $this->ajaxReturn('none', 'json');
        }
    }

    public function freeze() {
        $this->display();
    }

    //冻结标签
    public function dongjie() {
        $mtag = I('mtag');
        $cont = M('code')->where(array('mtag' => $mtag))->count();
        if ($cont != 0) {
            $add['freeze'] = 1;
            if (M('code')->where(array('mtag' => $mtag))->save($add)) {
                $this->ajaxReturn(2, 'JSON');
            } else {
                $this->ajaxReturn(3, 'JSON');
            }
        } else {
            $this->ajaxReturn(1, 'JSON');
        }
    }

    //解冻标签
    public function jiedong() {
        $mtag = I('mtag');
        $cont = M('code')->where(array('mtag' => $mtag))->count();
        if ($cont != 0) {
            $add['freeze'] = 0;
            if (M('code')->where(array('mtag' => $mtag))->save($add)) {
                $this->ajaxReturn(2, 'JSON');
            } else {
                $this->ajaxReturn(3, 'JSON');
            }
        } else {
            $this->ajaxReturn(1, 'JSON');
        }
    }
    
    
    //零售发货
    public function selllist() {
        $id = I('id');
        $tid = I('tid');
        $pfid = I('pfid');
        import('ORG.Util.Page');
        if ($pfid == 2) {
            $map = array(
                "send_id" => $id,
                "templet_id" => $tid
            );
        } else if ($pfid == 1) {
            $map = array(
                "receive_id" => $id,
                "templet_id" => $tid
            );
        } else {
            $where['send_id'] = $id;
            $where['receive_id'] = $id;
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            $map['templet_id'] = $tid;
        }
        $disbutor = M('distributor');
        $count = M('recordlist')->count('id');
        if ($count > 0) {
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $field = "id,send_id,receive_id,templet_id,ptag_name,mbeg,mend,product_num,orderNumber,time,remark,name,phone";
            $list = M('recordlist')->field($field)->order('time desc')->limit($limit)->select();
            foreach ($list as $k => $v) {
                if (!empty($v['ptag_name']) && empty($v['mbeg']) && empty($v['mend']) && $v['product_num'] == 1) {
                    $list[$k]['mbeg'] = $v['ptag_name'];
                    $list[$k]['mend'] = $v['ptag_name'];
                    $list[$k]['ptag_name'] = 0;
                }
            }
            $page = $p->show();
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $man = $disbutor->field('id,name,levname')->where(array('id' => $id))->find();
        $this->assign('man', $man);
        $this->display();
    }

    //删除零售发货记录
    public function delsell() {
        $id = I('id');
        $condition = array('id' => $id);
        $sellinfo = M('recordlist')->where($condition)->find();
        $res = M('recordlist')->where($condition)->delete();
        if ($res) {
            M('product')->where(array('send_id' => $sellinfo['send_id'], 'receive_id' => 33, 'orderNumber' => $sellinfo['orderNumber']))->delete();
            $this->success("删除成功", U('Radmin/Inventory/index'), 1);
        } else {
            $this->error("删除失败");
        }
    }

}

?>