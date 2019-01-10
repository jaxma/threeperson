<?php

class SearchAction extends CommonAction {

    //系统出库查询
    public function systemSearch() {
        $type = I('get.type');
        $typea = I('get.typea');
        $beg = I('get.beg');
        $end = I('get.end');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        if (!is_numeric($start_time) && !is_numeric($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
        }
        $keyword = I('get.keyword');
        $keyword3 = I('get.keyword3');
        $d = 'distributor';
        $p = 'product';
        $t = 'templet';
        $field = "$p.id,$p.ptag_name,$p.mbeg,$p.mend,$p.product_num,$p.time,$t.name as tname,$d.name as dname";
        $join1 = "$d on $p.receive_id=$d.id";
        $join2 = "$t on $p.templet_id=$t.id";
        $order = "$p.time desc";
        //系统出库记录查询
        if ($type == 'systemtag') {
            if ($typea == 'b') {
                $condition = array(
                    'type' => 'systemtag',
                    'beg' => $beg,
                    'end' => $end
                );
                $where = "$p.statusbm='b' and $p.ptag_name>=$beg and $p.ptag_name<=$end";
            } else if ($typea == 'm') {
                $condition = array(
                    'type' => 'systemtag',
                    'keyword3' => $keyword3
                );
                $where = "$p.mend>=$keyword3 and $p.mbeg<=$keyword3";
            } else {
                $condition = array(
                    'type' => 'systemtag',
                    'beg' => $beg,
                    'end' => $end
                );
                $where = "$p.status='system' and $p.ptag_name>=$beg and $p.ptag_name<=$end";
            }
            $count = M($p)->where($where)->count('id');
        } else if ($type == 'systemtime') {
            $condition = array(
                'type' => 'systemtime',
                'start_time' => $start_time,
                'end_time' => $end_time
            );
            if ($typea == 'b') {
                $where = "$p.statusbm='b' and $p.time>=$start_time and $p.time<=$end_time";
            } else if ($typea == 'm') {
                $where = "$p.statusbm='m' and $p.time>=$start_time and $p.time<=$end_time";
            } else {
                $where = "$p.status='system' and $p.time>=$start_time and $p.time<=$end_time";
            }
            $count = M($p)->where($where)->count('id');
        } else if ($type == 'systemagent') {
            $condition = array(
                'type' => 'systemagent',
                'keyword' => $keyword
            );
            $map = array(
                'name' => $keyword,
                '_logic' => 'OR',
                'phone' => $keyword,
                '_logic' => 'OR',
                'wechatnum' => $keyword
            );
            $agent_id = M($d)->where($map)->getField('id');
            $where["$p.receive_id"] = $agent_id;

            if ($typea == 'b') {
                $where["$p.statusbm"] = 'b';
            } else if ($typea == 'm') {
                $where["$p.statusbm"] = 'm';
            } else {
                $where["$p.status"] == 'system';
            }
            $count = M($p)->where($where)->count('id');
        }
        if ($count > 0) {
            import('ORG.Util.Page');
            $pg = new Page($count, 20);
            $limit = $pg->firstRow . "," . $pg->listRows;
            //var_dump($where);
            $list = M($p)->field($field)->where($where)->join($join1)->join($join2)->order($order)->limit($limit)->select();
            //分页跳转的时候保证查询条件
            foreach ($condition as $key => $val) {
                //urlencode编码url字符串
                if (!is_array($val)) {
                    $pg->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $pg->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->typea = $typea;
        $this->display();
    }

    //大标出库查询
    public function tagSearch() {
        $type = I('get.type');
        $tag = I('get.tag');
        $agent = I('get.agent');
        $beg = I('get.beg');
        $end = I('get.end');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        if (!is_numeric($start_time) && !is_numeric($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
        }
        $keyword = I('get.keyword');
        //$keyword = iconv("gbk","utf-8",$keyword);
        $d = 'distributor';
        $p = 'product';
        $t = 'templet';
        $field = "$p.id,$p.ptag_name,$p.mbeg,$p.mend,$p.product_num,$p.orderNumber,$p.time,$t.name as tname,$d.name as dname,m.name as mname";
        $join1 = "$d on $p.receive_id=$d.id ";
        $join2 = "$d as m on $p.send_id=m.id";
        $join3 = "$t on $p.templet_id=$t.id";
        $order = "$p.time desc";
        //系统出库记录查询
        if ($type == 'bigtag') {
            $condition = array(
                'type' => $type,
                'tag' => $tag,
                'beg' => $beg,
                'end' => $end
            );
            if ($tag == 'big') {
                $where = "$p.status='big' and $p.ptag_name>=$beg and $p.ptag_name<=$end";
            } else if ($tag == 'm') {
                $where = "$p.status='m' and $p.ptag_name>=$beg and $p.ptag_name<=$end";
            }
            $count = M($p)->where($where)->count('id');
        } else if ($type == 'bigtime') {
            $condition = array(
                'type' => $type,
                'tag' => $tag,
                'start_time' => $start_time,
                'end_time' => $end_time
            );
            if ($tag == 'big') {
                $where = "$p.status='big' and $p.time>=$start_time and $p.time<=$end_time";
            } else if ($tag == 'm') {
                $where = "$p.status='m' and $p.time>=$start_time and $p.time<=$end_time";
            }
            $count = M($p)->where($where)->count('id');
        } else if ($type == 'bigagent') {
            $condition = array(
                'type' => $type,
                'tag' => $tag,
                'keyword' => $keyword,
                'agent' => $agent
            );
            $map = array(
                'name' => $keyword,
                '_logic' => 'OR',
                'phone' => $keyword,
                '_logic' => 'OR',
                'wechatnum' => $keyword
            );
            $agent_id = M($d)->where($map)->getField('id');
            //echo $agent_id;die;
            if ($tag == 'big') {
                if ($agent == 'send') {
                    $where["$p.send_id"] = $agent_id;
                    $where["$p.status"] = 'big';
                } else {
                    $where["$p.receive_id"] = $agent_id;
                    $where["$p.status"] = 'big';
                }
            } else if ($tag == 'm') {
                if ($agent == 'send') {
                    $where["$p.send_id"] = $agent_id;
                    $where["$p.status"] = 'm';
                } else {
                    $where["$p.receive_id"] = $agent_id;
                    $where["$p.status"] = 'm';
                }
            }
            $count = M($p)->where($where)->count('id');
        }
        if ($count > 0) {
            import('ORG.Util.Page');
            $pg = new Page($count, 20);
            $limit = $pg->firstRow . "," . $pg->listRows;
            $list = M($p)->field($field)->where($where)->join($join1)->join($join2)->join($join3)->order($order)->limit($limit)->select();

            //分页跳转的时候保证查询条件
            foreach ($condition as $key => $val) {
                //urlencode编码url字符串
                if (!is_array($val)) {
                    $pg->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $pg->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        if ($tag == 'big') {
            $this->display('bigSearch');
        } else if ($tag == 'm') {
            $this->display('mSearch');
        }
    }

    //追踪产品流向
    public function traceLine() {
        if (IS_GET) {
            $ptag_name = I('get.search');
            $where = array('ptag_name' => $ptag_name, 'status' => 'system');
            $row = M('Product')->field('receive_id,mbeg,mend')->where($where)->find();
            if ($row) {
                $d = 'distributor';
                $p = 'product';
                $field = "$d.name,$d.levname,$p.ptag_name,$p.status,$p.time";
                $pid = $row['receive_id'];
                $beg = $row['mbeg'];
                $end = $row['mend'];
                $sql = "select $field from $p left join $d on $p.receive_id=$d.id
				where ($p.pid = $pid and $p.ptag_name = $ptag_name)or
				($p.pid = $pid and $p.ptag_name >= $beg and $p.ptag_name <= $end)
				order by time asc";
                $list = M($p)->query($sql);
                foreach ($list as $k => $v) {
                    if ($v['status'] == 'big') {
                        $big[] = $v;
                    } else {
                        $m[] = $v;
                    }
                }
                for ($i = $beg; $i <= $end; $i++) {
                    foreach ($m as $key => $val) {
                        if ($val['ptag_name'] == $i) {
                            $arr[$i][] = $val;
                        }
                    }
                }
                dump($big);
                echo '<br/>';
                dump($arr);
                echo '<br/>';
                dump($m);
                die;
                $this->list = $list;
            }
        }
        $this->display();
    }

}

?>