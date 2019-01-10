<?php

/**
 *  topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class ManagerAction extends CommonAction
{

    //级别经销商列表
    public function index()
    {
        $distributor_obj = M('distributor');
        import('ORG.Util.Page');

        $level = trim(I('get.level'));
        $pname = trim(I('get.pname'));
        $address = trim(I('get.address'));
        $name = trim(I('get.name'));
        $phone = trim(I('phone'));
        $wechatnum = trim(I('wechatnum'));
        $audited = trim(I('audited'));
        $uid = trim(I('uid'));
        $upgrade_apply_id = trim(I('upgrade_apply_id'));//升级申请id

        $this->config_internal_level = C('internal_level');


        $condition['audited'] = 1;

        if (!empty($level)) {
            $condition['level'] = $level;
        }

        if (!empty($uid)) {
            $condition['id'] = $uid;
        }

        if ($audited != null) {
            $condition['audited'] = $audited;
        }


        if (!empty($pname)) {
            $condition_search['pname'] = $pname;
        }

        if (!empty($name)) {
            $condition_search['phone'] = array('like', "%$name%");
            $condition_search['name'] = array('like', "%$name%");
            $condition_search['wechatnum'] = array('like', "%$name%");
            $condition_search['_logic'] = 'or';

        }
//
        if (!empty($address)) {
            $condition_search['address'] = array('like', "%$address%");
        }

        if (!empty($phone)) {
            $condition_search['phone'] = $phone;
        }


        if (!empty($condition_search)) {
            $condition = $condition_search;
        }


        //取得满足条件的记录数
        $count = $distributor_obj->where($condition)->count('id');

        //测试topos版本的特殊规则，不影响其它项目
        if ($_SESSION['aname'] == 'topostest' && $_SESSION['aid'] == 2) {
            $count = 0;
        }
        //每页显示数量
        $page_num = 20;
        if ($count > 0) {           //总管理员
            //创建分页对象
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $voList = $distributor_obj->where($condition)->order('audited asc,time desc')->limit($limit)->select();


            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->mList = $voList;
        }
        $this->count = $count;
        $this->p = I('p');
        $this->limit = $page_num;
        $this->level = $level;
        $this->level_name = C('LEVEL_NAME');
        $this->level_num = C('LEVEL_NUM');
        $this->upgrade_apply_id = $upgrade_apply_id;
        $this->display();

    }


    //用户关系信息
    public function user_bind()
    {

        $distributor_obj = M('distributor');


        $condition = array();


        $name = I('get.name');

        if (!empty($name)) {
            $sear_dis_info = $distributor_obj->where(array('name' => $name))->find();

            $condition['uid'] = !empty($sear_dis_info) ? $sear_dis_info['id'] : '0';
        }


        //获取充值记录
        $page_info = array(
            'page_num' => I('get.p'),
        );

        import('Lib.Action.User', 'App');
        $User = new User();
        $result = $User->get_distributor_bind($page_info, $condition);

//        print_r($result);return;

        $this->page = $result['page'];
        $this->list = $result['list'];

        $this->display();
    }


    //经销商级别升级表单处理
    public function upgrade_post()
    {

        $distributor_model = M('distributor');


        $mid = I('mid');
        $level = I('level');//升级后的级别
        $type = I('type');
        $b_id = I('b_id');
        $upgrade_apply_id = trim(I('upgrade_apply_id'));//升级申请id
//        $b_name = I('b_name');

//        print_r($this->_post());return;


        import('Lib.Action.User', 'App');
        $User = new User();

        $info = array(
            'uid' => $mid,
            'level' => $level,
            'type' => $type,
            'b_id' => $b_id,
            'upgrade_apply_id' => $upgrade_apply_id,
        );

        $result = $User->upgrade($info);

        if ($result['code'] == 1) {
            $this->add_active_log('用户级别操作：' . $result['msg']);
            $this->success($result['msg']);
        } else {
            $this->error($result['msg']);
        }
    }


    public function repair($id, $level)
    {
        if ($level > 1) {
            $d = M('distributor');
            $pid = $d->where(array('id' => $id))->getField('pid');
            $row = $d->field('id,pid,level,name')->where(array('id' => $pid))->find();
            if (!$row) {
                return -1;
            }
            if ($row['level'] < $level) {
                return $row;
            } else {
                return self::repair($row['id'], $level);
            }
        } else {
            return 0;
        }
    }


    //注释日期2018-1-30 by qjq 原因：用新的审核列表
    //总经销商申请信息列表
//    public function applyList()
//    {
//        $distributor_obj = M('distributor');
//        $condition = array(
////            'managed' => 1,
////            'isRecommend' => '0', //非推荐最高级经销商才在这里审核
//            'audited' => ['in', [0, 2, 4]],
//            'recommendID' => 0,
//        );
//        //取得满足条件的记录数
//        $count = $distributor_obj->where($condition)->count('id');
//        //每页显示数量
//        $page_num = 20;
//        if ($count > 0) {
//            import('ORG.Util.Page');
//            //创建分页对象
//            $p = new Page($count, $page_num);
//            $limit = $p->firstRow . "," . $p->listRows;
//            $applyList = $distributor_obj->where($condition)->order('time desc')->limit($limit)->select();
//            //*分页显示*
//            $page = $p->show();
//            $this->page = $page;
//            $this->count = $count;
//            $this->p = I('p');
//            $this->limit = $page_num;
//            $this->applyList = $applyList;
//        }
//        $this->level_name = C('LEVEL_NAME');
//        $this->display();
//    }

    //审核经销商旗下经销商
//    public function qtapplyList()
//    {
////        第二高经销商审核
////        $applyList = M('distributor')->where(array('audited' => 0, 'level' => 2))->select();
////        $this->level_name = C('LEVEL_NAME');
////        $this->applyList = $applyList;
////        $this->display();
//
//        $level = I('level');
//        $this->level = $level;
//        $isRecommend = I('isRecommend');
//
//        //$applyList = M('distributor')->where(array('audited' => 2,'level'=>$level))->select();
//
//        $audited = ['in', [2, 4]];
//
//
//        //审核状态为2，并且属于不被推荐（即向下发展）的用户
//        $condition_a = array(
//            'audited' => $audited,
//            'isRecommend' => $isRecommend,
//            'recommendID' => ['neq', 0],
//        );
//
//        $distributor = M('distributor');
//
//        //取得满足条件的记录数
//        $count = $distributor->where($condition_a)->count('id');
//        //每页显示数量
//        $page_num = 20;
//        if ($count > 0) {
//            import('ORG.Util.Page');
//            //创建分页对象
//            $p = new Page($count, $page_num);
//            $limit = $p->firstRow . "," . $p->listRows;
//            $applyList = $distributor->where($condition_a)->order('time desc')->limit($limit)->select();
//
//            $pid_list = array(); //pid数组
//            $pid_list_con = array(); //pid数组，用于sql查询
//            $applyList_haspid = array(); //有上级的审核用户数组信息
//
//            foreach ($applyList as $key => $list) {
//                $list['parent_info'] = ''; //上级信息
//                $list_pid = $list['pid'];
//
//                //如果有上级就列出上级经销商的信息
//                if (!empty($list_pid)) {
//                    $pid_list[$key] = $list['pid'];
//                    $pid_list_con[] = $list['pid'];
//
//                    $applyList_haspid[$list_pid][$key] = $list;
//                }
//                $applyList[$key] = $list;
//            }
//
//            //如果有上级
//            if (!empty($pid_list)) {
//                //sql查询获取待审核的经销商上级
//                $condition_b['id'] = array('in', $pid_list_con);
//                $parent_applyList = $distributor->where($condition_b)->select();
//                $parent_applyList_pidkey = array(); //已ID为键值
//
//                if (!empty($parent_applyList)) {
//                    foreach ($parent_applyList as $list) {
//                        $parent_applyList_pidkey[$list['id']] = $list;
//                    }
//
//                    foreach ($pid_list as $key => $pid) {
//                        $parent_name = $parent_applyList_pidkey[$pid]['name']; //上级名字
//                        $parent_levname = $parent_applyList_pidkey[$pid]['levname']; //等级
//
//                        $applyList[$key]['parent_info'] = $parent_name . '（' . $parent_levname . '）';
//                    }
//                }
//            }
//
//            //*分页显示*
//            $page = $p->show();
//            $this->page = $page;
//            $this->count = $count;
//            $this->p = I('p');
//            $this->limit = $page_num;
//
//            $this->applyList = $applyList;
//        }
//        $this->isRecommend = $isRecommend;
//        $this->level_name = C('LEVEL_NAME');
//        $this->display();
//    }

//注释日期2018-1-30 by qjq 原因：用新的审核列表

//    //审核经销商推荐的经销商
//    public function recommendAudit() {
//        $level = I('level');
//        $this->level = $level;
//        //$applyList = M('distributor')->where(array('audited' => 2,'level'=>$level))->select();
//        //审核状态为2，并且属于被推荐的用户
//        $condition_a = array(
//            'audited' => 2,
//            'isRecommend' => '1',
//        );
//
//        $distributor = M('distributor');
//        $applyList = $distributor->where($condition_a)->select();
//
//        if (!empty($applyList)) {
//            $pid_list = array(); //pid数组
//            $pid_list_con = array(); //pid数组，用于sql查询
//            $applyList_haspid = array(); //有上级的审核用户数组信息
//
//            foreach ($applyList as $key => $list) {
//                $list['parent_info'] = ''; //上级信息
//                $list_pid = $list['pid'];
//
//                //如果有上级就列出上级经销商的信息
//                if (!empty($list_pid)) {
//                    $pid_list[$key] = $list['pid'];
//                    $pid_list_con[] = $list['pid'];
//
//                    $applyList_haspid[$list_pid][$key] = $list;
//                }
//                $applyList[$key] = $list;
//            }
//
//            //如果有上级
//            if (!empty($pid_list)) {
//                //sql查询获取待审核的经销商上级
//                $condition_b['id'] = array('in', $pid_list_con);
//                $parent_applyList = $distributor->where($condition)->select();
//                $parent_applyList_pidkey = array(); //已ID为键值
//
//                if (!empty($parent_applyList)) {
//                    foreach ($parent_applyList as $list) {
//                        $parent_applyList_pidkey[$list['id']] = $list;
//                    }
//
//                    foreach ($pid_list as $key => $pid) {
//                        $parent_name = $parent_applyList_pidkey[$pid]['name']; //上级名字
//                        $parent_levname = $parent_applyList_pidkey[$pid]['levname']; //等级
//
//                        $applyList[$key]['parent_info'] = $parent_name . '（' . $parent_levname . '）';
//                    }
//                }
//            }
//        }
//
//
//
//        $this->level_name = C('LEVEL_NAME');
//        $this->applyList = $applyList;
//        $this->display();
//    }

//注释日期2018-1-30 by qjq 原因：用新的审核方法
//    //总经销商申请审核
//    public function audit()
//    {
//        if (!IS_AJAX) {
//            halt('页面不存在！');
//        }
//
//        vendor("phpqrcode.phpqrcode");
//
//        $mids = I('mids');
//        $mids = substr($mids, 1);
//        $managers = explode('_', $mids);
//        $field = 'id,recommendID,level';
//        $distributor = M('distributor');
//
//        import("Wechat.Wechat", APP_PATH);
//        $options = array(
//            'token' => C('APP_TOKEN'), //填写您设定的key
//            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
//            'appid' => C('APP_ID'), //填写高级调用功能的app id
//            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
//        );
//        $this->wechat_obj = new Wechat($options);
//
//        $levnames = C('LEVEL_NAME');
//
//        foreach ($managers as $m) {
//            //look
//            $signature = $distributor->where(array('id' => $m))->find();
//            $location = $signature['signature'];
//
//            $touser = $signature['openid'];
//            $uName = $signature['name'];
//            $keyword1 = $signature['name'];
//            $phone = $signature['phone'];
//            $bname = $signature['bossname'];
//            $signature_level = $signature['level'];
//
//            if ($location == NULL) {   //若没名片则生成名片
//                $appid = C('APP_ID');
//                $callback = 'http://' . C('YM_DOMAIN') . '/index.php/Admin/Signature/getsign';
//                $text = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($callback) . "&response_type=code&scope=snsapi_base&state=" . $m . "#wechat_redirect";
//                $level = "L";
//                $size = "10";
//                $location = __ROOT__ . '/signatures/' . $m . '.png';
//                $url = './signatures/' . $m . '.png';
//                //生成图片
//                QRcode::png($text, $url, $level, $size);
//            }
//            $arr = array(
//                'id' => $m,
////                'managed' => 2,
//                'audited' => 1,
//                'signature' => $location,
////                'level' => 1,
////                'levname' => $levnames[1]
//            );
//
//            if ($signature_level == 1) {
//                $arr['managed'] = 2;
//            } else if ($signature_level == null) {
//                $signature_level = C('LEVEL_NUM');
//            }
//
//            $res = $distributor->save($arr);
//            if ($res) {
//                $path = C('DEFAULT_TEAM');
//                if ($path == 'path') {
//                    $distributor->where(['id' => $signature['pid']])->save(['is_lowest' => 0]);
//                } else {
//                    $distributor->where(['id' => $signature['recommendID']])->save(['is_lowest' => 0]);
//                }
//
//                $this->add_active_log('审核经销商：' . $uName . '成功');
//            } else {
//                $this->add_active_log('审核经销商：' . $uName . '失败');
//                $return_result = [
//                    'code' => 2,
//                    'msg' => '审核' . $uName . '失败！',
//                ];
//                $this->ajaxReturn($return_result, 'json');
//            }
//
//
//            //公众号推送
//            $sendTime = date("Y-m-d H:i:s");
//            $template_id = C('SH_MB');
//            $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//
//            $SYSTEM_NAME = C('SYSTEM_NAME');
//            $sendData = array(
//                'first' => array('value' => ("$uName,您的" . $SYSTEM_NAME . "微商管理系统经销商审核成功！"), 'color' => "#CC0000"),
//                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
//                'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
//                'remark' => array('value' => ("欢迎您加入" . $SYSTEM_NAME . "微商管理系统。您的直属上级:" . $bname . "。"), 'color' => '#CC0000')
//            );
////            import('ORG.Net.OrderPush');
////            $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
////            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');
//
//
//            $template = array(
//                'touser' => $touser,
//                'template_id' => $template_id,
//                'url' => $url,
//                'topcolor' => '#7B68EE',
//                'data' => $sendData
//            );
//
//            $this->wechat_obj->sendTemplateMessage($template);
//
//
////                //这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面的就可以开启此方法）
///             import('Lib.Action.Message','App');
////            $message = new Message();
////            $message->push(trim($touser), $signature, $message->audit_manager);
////                //调用结束
//
//
//
////            //------------推荐人的返利-------------
////            //这里要根据需求改返利判断条件
////            if ($signaturea['recommendID'] != 0 && $signaturea['level'] == 1) {
////                $recommend = M('recommend_rebate');
////                $a = M('returnrate')->where(array('id' => 3))->find();
////                $le = $distributor->field($field)->where(array('id' => $signaturea['recommendID']))->find();
////                if ($le['level'] == 1) {
////                    $add = array(
////                        'user_id' => $le['id'],
////                        'x_id' => $signaturea['id'],
////                        'time' => time(),
////                        'money' => $a['remoney1'],
////                        'status' => 0
////                    );
////                    $recommend->add($add);
////                    if ($le['recommendID'] != 0) {
////                        $lee = $distributor->field($field)->where(array('id' => $le['recommendID']))->find();
////                        if ($lee['level'] == 1) {
////                            $addtwo = array(
////                                'user_id' => $lee['id'],
////                                'x_id' => $signaturea['id'],
////                                'time' => time(),
////                                'money' => $a['remoney'],
////                                'status' => 0
////                            );
////                            $recommend->add($addtwo);
////                        }
////                    }
////                }
////            }
////            //------------end推荐人的返利-------------
//
//
//        }
//
//        //清除团队缓存
//        clean_team_path_cache();
//
//        $return_result = [
//            'code' => 1,
//            'msg' => '审核成功',
//        ];
//        $this->ajaxReturn($return_result, 'json');
//    }

//注释日期2018-1-30 by qjq 原因：用新的审核方法

//    //审核通过经销商申请
//    public function qtaudit()
//    {
//        if (!IS_AJAX) {
//            halt('页面不存在！');
//        }
//
//        vendor("phpqrcode.phpqrcode");
//
//        $mids = I('mids');
//        $mids = substr($mids, 1);
//        $managers = explode('_', $mids);
//        $recommend_setting = M('recommend_setting');
//        $recommend_rebate = M('recommend_rebate');
//        $distributor = M('distributor');
//
//        import("Wechat.Wechat", APP_PATH);
//        $options = array(
//            'token' => C('APP_TOKEN'), //填写您设定的key
//            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
//            'appid' => C('APP_ID'), //填写高级调用功能的app id
//            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
//        );
//        $this->wechat_obj = new Wechat($options);
//
//        import('Lib.Action.Rebate', 'App');
//        $Rebate = new Rebate();
//
//        foreach ($managers as $m_id) {
//            //查申请人的信息
//            $signaturea = $distributor->where(array('id' => $m_id))->find();
//
//            //公众号推送
//            $signaturea_id = $signaturea['id'];
//            $touser = $signaturea['openid'];
//            $uName = $signaturea['name'];
//            $keyword1 = $signaturea['name'];
//            $phone = $signaturea['phone'];
//            $bname = $signaturea['bossname'];
//            $isRecommend = $signaturea['isRecommend'];
//            $level = $signaturea['level'];
//            $recommendID = $signaturea['recommendID'];
//
//            $sendTime = date("Y-m-d H:i:s");
//            $template_id = C('SH_MB');
//            $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//            $SYSTEM_NAME = C('SYSTEM_NAME');
//            $sendData = array(
//                'first' => array('value' => ("$uName,您的" . $SYSTEM_NAME . "微商管理系统经销商审核成功！"), 'color' => "#CC0000"),
//                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
//                'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
//                'remark' => array('value' => ("欢迎您加入" . $SYSTEM_NAME . "微商管理系统。您的直属上级:" . $bname . "。"), 'color' => '#CC0000')
//            );
//            import('ORG.Net.OrderPush');
////            $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
////            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');
//
//            $template = array(
//                'touser' => $touser,
//                'template_id' => $template_id,
//                'url' => $url,
//                'topcolor' => '#7B68EE',
//                'data' => $sendData
//            );
//
//            $this->wechat_obj->sendTemplateMessage($template);
//
//
//            //------------推荐人的返利--------------
//
//            //----------生成返利--------------
//
//            //新版本的统一调用此审核方式，如果推荐人为总部，则不调用推荐返利的方法，修改时间为2018/1/24 by qjq
//            if ($recommendID) {
//                $rebate_result = $Rebate->radmin_user_audit_rebate($m_id, $signaturea);
//            }
//
////            if( $rebate_result['code'] != 1 ){
////                $this->ajaxReturn(array('status' => 2), 'json');
////            }
//
//            //----------end 生成返利--------------
//
//
//            //------------end 推荐人的返利--------------
//
//            //通过审核
//            if (!$distributor->where(array('id' => $m_id))->setField('audited', 1)) {
//                $return_result = [
//                    'code' => 2,
//                    'msg' => '审核失败',
//                ];
//                $this->ajaxReturn($return_result, 'JSON');
//            }
//
//            $path = C('DEFAULT_TEAM');
//            if ($path == 'path') {
//                $distributor->where(['id' => $signaturea['pid']])->save(['is_lowest' => 0]);
//            } else {
//                $distributor->where(['id' => $signaturea['recommendID']])->save(['is_lowest' => 0]);
//            }
////                //这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面的就可以开启此方法）
            /// import('Lib.Action.Message','App');
////            $message = new Message();
////            $message->push(trim($touser), $signaturea, $message->audit_manager);
////                //调用结束
//            $this->add_active_log('审核经销商通过：' . $uName);
//        }
//
//        //清除团队缓存
//        clean_team_path_cache();
//
//        $return_result = [
//            'code' => 1,
//            'msg' => '审核成功',
//        ];
//        $this->ajaxReturn($return_result, 'JSON');
//    }//end func qtaudit


    //删除经销商
    public function delete()
    {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }
        $mids = I('mids');
        $mids = substr($mids, 1);
        $managers = explode('_', $mids);
        $obj = D('Distributor');
        import('Lib.Action.User', 'App');
        $user_obj = new User();
        
        foreach ($managers as $m) {
            $row = $obj->where(array('id' => $m))->find();
            setLog('删除的代理:'.  json_encode($row),'delete-agent');
            $touser = $row['openid'];
            $uName = $row['name'];

            if (!$obj->where(array('id' => $m))->delete()) {
                $return_result = [
                    'code' => 2,
                    'msg' => '删除失败',
                ];
                $this->ajaxReturn($return_result, 'json');
            }
            //删除代理后清除/更新数据
            $this->delete_agent_after($row, $user_obj);

//            这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面旧的模板消息就可以开启此方法）
            import('Lib.Action.Message','App');
            $message = new Message();
            $message->push(trim($touser), $row, $message->not_audit_manager);
//            调用结束

            $this->add_active_log('删除经销商：' . $uName);
        }

        //清除团队缓存
        clean_team_path_cache();

        $return_result = [
            'code' => 1,
            'msg' => '删除成功',
        ];
        $this->ajaxReturn($return_result, 'json');
    }

    //删除经销商
//    public function del()
//    {
//        $model = M('distributor');
//        $order = M('order');
//        $recommend_rebate = M('recommend_rebate');
//        $rebate = M('rebate');
//        $rerebate = M('rerebate');
//        $id = I('get.id');
//        $where['id'] = $id;
//        $row = $model->field('pid,name')->where($where)->find();
//        $pname = $model->where(array('id' => $row['pid']))->getField('name');
//        if ($row) {
//            $data = array(
//                'pid' => $row['pid'],
//                'bossname' => $pname,
//                'pname' => $pname,
//                'recommendID' => 0
//            );
//            $model->where(array('pid' => $id))->save($data);
//        }
//        $flag = $model->where($where)->delete();
//
//        $this->add_active_log('删除经销商：' . $row['name']);
//
//        //add by z
//        setLog($id, 'del-agent');
//        //look
//        //查看订单是否存在相关信息
//        $arr['user_id'] = $id;
//        $orrow = $order->where($arr)->limit(1)->select();
//        if ($orrow) {
//            $order->where($arr)->delete();
//        }
//        //查看推荐返利是否存在相关信息
//        $rererow = $recommend_rebate->where($arr)->limit(1)->select();
//        if ($rererow) {
//            $recommend_rebate->where($arr)->delete();
//            //查看推荐人下单返利是否存在相关信息
//            $lirow = $rerebate->where($arr)->limit(1)->select();
//            if ($lirow) {
//                $rerebate->where($arr)->delete();
//            }
//        }
//        $rearr['x_id'] = $id;
//        $rererowone = $recommend_rebate->where($rearr)->limit(1)->select();
//        if ($rererowone) {
//            $recommend_rebate->where($rearr)->delete();
//        }
//        //查看下单返利是否存在相关信息
//        $rerow = $rebate->where($arr)->limit(1)->select();
//        if ($rerow) {
//            $rebate->where($arr)->delete();
//        }
//        if ($flag) {
//
//            //清除团队缓存
//            clean_team_path_cache();
//
//            $this->success('删除成功');
//        } else {
//            $this->error('删除失败');
//        }
//    }

    //搜索
    public function search()
    {
        import('ORG.Util.Page');
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
        } else if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        }
        if (!empty($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
        } else if (!empty($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
        }
        $level = I('level');
        $did = session("did");
        if ($id == 1) {      //搜索经销商
            $map['id'] = $keyword;

            $row = M('Distributor')->where($map)->select();
            $this->dist = $row['name'];
            $this->dista = $keyword;
            if ($did != 0) {
                if ($rs = S($did)) {
                    foreach ($rs as $v) {
                        foreach ($v as $val) {
                            if ($val['name'] == $row[0]['name'] && $val['phone'] == $row[0]['phone'] && $val['wechatnum'] == $row[0]['wechatnum']) {
                                $flag = true;
                            }
                        }
                    }
                } else {
                    $pid = $row[0]['pid'];
                    $flag = $this->sonTree($pid, $did);
                    $flag = session('flag');
                }
                if ($flag !== true) {
                    $row = "";
                }
            }
        } else if ($id == 3) {
            $this->addr = $keyword;
            $map['address'] = array('like', "%$keyword%");
            $map['level'] = array('eq', "$level");
            $list = M('Distributor')->where($map)->select();
            if (is_array($list)) {
                $row = M('Distributor')->where($map)->select();
            } else {
                $row = "";
            }
        } else {//搜索审核人
            $this->audit = $keyword;
            $map = array(
                'name' => $keyword,
                '_logic' => 'or',
                'phone' => $keyword,
                '_logic' => 'or',
                'wechatnum' => $keyword
            );
            $list = M('Distributor')->where($map)->select();
            if ($did != 0) {
                if ($rs = S($did)) {
                    foreach ($rs as $v) {
                        foreach ($v as $val) {
                            if ($val['pname'] == $list[0]['name']) {
                                $flag = true;
                            }
                        }
                    }
                } else {
                    $pid = $list[0]['pid'];
                    $flag = $this->sonTree($pid, $did);
                    $flag = session('flag');
                }
                if ($flag !== true) {
                    $list = "";
                }
            }
            if (is_array($list)) {
                $row = M('Distributor')->where(array('pname' => $list[0]['name']))->select();
            } else {
                $row = "";
            }
        }
        $count = count($row);
        $p = new Page($count, 20);
        if (!empty($row)) {
            $row = array_splice($row, $p->firstRow, $p->listRows);
        }
        $this->page = $p->show();
        $this->row = $row;
        $this->level_name = C('LEVEL_NAME');
        $this->level_num = C('LEVEL_NUM');
        $this->display();
    }

    //查找经销商是否属于当前联盟总代
    public function sonTree($pid, $did)
    {
        $row = M('Distributor')->field('id,pid')->where(array('id' => $pid))->find();
        if (!empty($row)) {
            if ($row['id'] != $did) {
                self::sonTree($row['pid'], $did);
            } else {
                session('flag', true);
                return true;
            }
        } else {
            session('flag', false);
            return false;
        }
    }

    //编辑经销商
    public function edit()
    {
        $id = I('id');
//      $field = "id,name,wechatnum,phone,email,idennum,address,province,city,county";
        $row = M('distributor')->where(array('id' => $id))->find();
        $this->vo = $row;
        $this->display();
    }


    //编辑经销商
    public function internal_edit()
    {
        $id = I('id');
        $field = "id,name,wechatnum,internal_level";
        $row = M('distributor')->field($field)->where(array('id' => $id))->find();

        $this->config_internal_level = C('internal_level');
        $this->vo = $row;
        $this->display();
    }


    public function update()
    {
        $model = M('distributor');
        if (false == $model->create()) {
            $this->error($model->getError());
        }
        $sex = I('sex');
        $province = trim(I('province'));
        $city = trim(I('city'));
        $county = trim(I('county'));
        $str = '请选择';
        if ($province == $str || $city == $str || $county == $str || $province == '' || $city == '') {
            $this->error('地址信息不完整');
        }
        
        $data = [
            'id' => I('id'),
            'name' => trim(I('name')),
            'wechatnum' => trim(I('wechatnum')),
            'phone' => trim(I('phone')),
            'email' => trim(I('email')),
            'idennum' => trim(I('idennum')),
            'province' => $province,
            'city' => $city,
            'county' => $county,
            'address' => trim(I('address')),
        ];
        if(!empty($sex)){
            $data['sex'] = $sex;
        }
        if ($model->save($data)) {
            $this->add_active_log('编辑经销商信息：' . I('name'));
            $this->success('编辑成功');
        } else {
            $this->error('编辑失败');
        }
    }

    //更改其内部人员等级
    public function change_internal_level()
    {
        if (!$this->isPost()) {
            $this->error('错误的提交！');
            return;
        }

        $internal_level = $this->_post('internal_level');
        $dis_id = $this->_post('id');

        if ($internal_level === NULL || empty($dis_id)) {
            $this->error('请选择级别后再提交！');
            return;
        }


        $distributor = M('distributor');

        $condition['id'] = $dis_id;

        $data = array(
            'internal_level' => $internal_level,
        );

        $save_res = $distributor->where($condition)->save($data);

        if (!$save_res) {
            $this->error('编辑失败');
        }

        $this->add_active_log('更改其内部人员等级');
        $this->success('编辑成功');
    }


    public function xgmm()
    {
        $password = md5(123456);
        $data['password'] = $password;
        $save = M('distributor')->where(array('id' => I('id')))->save($data);
        if ($save) {
//            $this->ajaxReturn('1', 'JSON');
            $this->add_active_log('经销商重置密码');
            $this->success('重置成功！');
        } else {
//            $this->ajaxReturn('2', 'JSON');
            $this->error('已重置，请不用再重置！');
        }
    }

    //树形图
    public function tree()
    {
        $condition = array(
            "pid" => 0
        );
        $row = M('distributor')->where($condition)->select();
        foreach ($row as $k => $v) {
            $row[$k]['count'] = M('distributor')->where(array('pid' => $row[$k]['id']))->count();
        }

        $this->row = $row;
        $this->YM_DOMAIN = C('YM_DOMAIN');
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //推荐人树形图
    public function recommend_tree()
    {
        $condition = array(
            "recommendID" => 0
        );
        $row = M('distributor')->where($condition)->select();
        foreach ($row as $k => $v) {
            $row[$k]['count'] = M('distributor')->where(array('recommendID' => $row[$k]['id']))->count();
        }

        $this->row = $row;
        $this->YM_DOMAIN = C('YM_DOMAIN');
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //查找推荐人
    public function treedirect_recommend()
    {
        $id = I('post.mid');
        $condition = array(
            "recommendID" => $id
        );
        $distributor = M('distributor');
        $cont = $distributor->where($condition)->count();
        if ($cont == 0) {
            $this->ajaxReturn(1, 'JSON');
        }
        $list = $distributor->where($condition)->select();
        foreach ($list as $k => $v) {
            $list[$k]['count'] = M('distributor')->where(array('recommendID' => $list[$k]['id']))->count();
        }
        $this->ajaxReturn($list, 'JSON');
    }

    //个人详情
    public function persondetail()
    {
        $id = I('id');
        $manager = M('distributor')->where(array('id' => $id))->find();

//        $totime = $row['time'] + 3600 * 24 * 365;
//        $row['time'] = date("Y-m-d", $row['time']);
//        $row['totime'] = date("Y-m-d", $totime);


        if (!empty($manager)) {
            $manager['idennum'] = substr($manager['idennum'], 0, 6) . "******" . substr($manager['idennum'], -4, 4);
            $manager['authnum'] = substr($manager['authnum'], 0, 3) . '*****';
            $manager['phone'] = substr($manager['phone'], 0, 7) . "****";
            $manager['wechatnum'] = substr($manager['wechatnum'], 0, 2) . "****";
        }

        if (empty($manager['start_time'])) {
            $manager['start_time'] = date('Y-m-d', $manager['time']);
        }
        if (empty($manager['end_time'])) {
            $manager['end_time'] = date('Y-m-d', $manager['time'] + 3600 * 24 * 365);
        }


        $this->ajaxReturn($manager, 'JSON');
    }

    //查找下属
    public function treedirect()
    {
        $id = I('post.mid');
        $condition = array(
            "pid" => $id
        );
        $distributor = M('distributor');
        $cont = $distributor->where($condition)->count();
        if ($cont == 0) {
            $this->ajaxReturn(1, 'JSON');
        }
        $list = $distributor->where($condition)->select();
        foreach ($list as $k => $v) {
            $list[$k]['count'] = M('distributor')->where(array('pid' => $list[$k]['id']))->count();
        }
        $this->ajaxReturn($list, 'JSON');
    }

    //转移经销商
    public function transmit()
    {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //转移推荐经销商
    public function transmit_recommend()
    {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    public function replace()
    {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }

    //替换推荐人
    public function rec_replace()
    {
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }


    //转移下级经销商处理
    public function setup()
    {
        //要转移的经销商id(只是下属经销商转移了,自己不转移)
        $o_id = I('post.o_id');
        $user_id = I('post.user_id');
        $o_name = I('post.o_name');
        $user_name = I('post.user_name');


        import('Lib.Action.User', 'App');
        $User = new User();

        $result = $User->transfer_dis($o_id, $user_id);

        if ($result['code'] == 1) {
            $this->add_active_log('转让经销商：' . $result['msg']);
        }


        $this->ajaxReturn($result['code'], 'json');
    }


    //替换上级
    public function setupone()
    {
        //要换上级的经销商id
        $o_id = I('post.o_id');
        $user_id = I('post.user_id');
        $distributor = M('distributor');
        $o_name = I('post.o_name');
        $user_name = I('post.user_name');

        import('Lib.Action.User', 'App');
        $User = new User();

        $result = $User->change_parent($o_id, $user_id);

        if ($result['code'] == 1) {
            $this->add_active_log('经销商替换上级：' . $result['msg']);
        }


        $this->ajaxReturn($result, 'json');
    }

    //转移下级经销商处理
    public function setup_recommend()
    {
        //要转移的经销商id(只是下属经销商转移了,自己不转移)
        $o_id = I('post.o_id');
        $user_id = I('post.user_id');
        $o_name = I('post.o_name');
        $user_name = I('post.user_name');


        import('Lib.Action.User', 'App');
        $User = new User();

        $result = $User->transfer_recommend($o_id, $user_id);

        if ($result['code'] == 1) {
            $this->add_active_log('转让推荐经销商：' . $result['msg']);
        }


        $this->ajaxReturn($result['code'], 'json');
    }

    //替换推荐人
    public function rec_replace_submit()
    {
        //要换上级的经销商id
        $o_id = I('post.o_id');
        $user_id = I('post.user_id');
        $distributor = M('distributor');
        $o_name = I('post.o_name');
        $user_name = I('post.user_name');

        import('Lib.Action.User', 'App');
        $User = new User();

        $result = $User->change_recommend($o_id, $user_id);

        if ($result['code'] == 1) {
            $this->add_active_log('经销商更换推荐人：' . $result['msg']);
        }

        $this->ajaxReturn($result, 'json');
    }//end func rec_replace_submit


    //改变招募状态
    public function zstatu()
    {
        $data['statu'] = I('statu');
        $res = M('distributor')->where(array('id' => I('id')))->save($data);
        if ($res) {
            if ($data['statu'] == 1) {
                $this->add_active_log('编号' . I('post.id') . '的经销商状态改为不可招募经销商');
            } else {
                $this->add_active_log('编号' . I('post.id') . '的经销商状态改为可招募经销商');
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    //禁用状态
    public function disable()
    {
        $data['disable'] = I('statu');
        $res = M('distributor')->where(array('id' => I('id')))->save($data);
        if ($res) {
            if ($data['disable'] == 1) {
                $this->add_active_log('编号' . I('post.id') . '的经销商状态改为禁用中');
            } else {
                $this->add_active_log('编号' . I('post.id') . '的经销商状态改为可用中');
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }


    //添加经销商
    public function user_add()
    {
//        echo __URL__.'/manager/index?level=';return;

        $this->display();
    }


    //添加经销商提交
    public function user_add_submit()
    {
        $info = I();

        $IS_SUBMIT_ID_CARD_IMG = C('IS_SUBMIT_ID_CARD_IMG');


//        $image = $this->upload();


        //后台不必一定要提交图片
//        if( $image['code'] != 1 && $IS_SUBMIT_ID_CARD_IMG ){
//            $this->error($image['msg']);
//            return;
//        }

//        $savepath1 = $savepath2 = $savepath3 = '';

//        if( $image['code'] == 1 ){
//            $image_url = array();
//
//            foreach ( $image['info'] as $k => $v ){
//                $v_url = '';
//                if( !empty($v) ){
//                    $savepath = $v['savepath'];
//                    $savename = $v['savename'];
//
//                    $v_url = __ROOT__.'/img/radminidenum/'.$savename;
//                }
//
//
//                $image_url[] = $v_url;
//            }
//
////            print_r($image_url);return;
//
//            $savepath1 = $image_url[0];
//
//            if( $IS_SUBMIT_ID_CARD_IMG ){
//                $savepath2 = $image_url[1];
//                $savepath3 = $image_url[2];
//
////
//            }


        $image_head = I('post.image');
        $image_iden = I('post.image2');
        $image_live = I('post.image3');

        $savepath1 = $image_head;
        if ($IS_SUBMIT_ID_CARD_IMG) {
            $savepath2 = $image_iden;
            $savepath3 = $image_live;
        }

        if (empty($savepath1)) {
            $savepath1 = __PUBLIC__ . '/images/headimg.png';
        }

        $info['headimgurl'] = $savepath1;
        $info['idennumimg'] = $savepath2;
        $info['liveimg'] = $savepath3;
        $info['audited'] = 1;

//        print_r($info);return;

        import('Lib.Action.User', 'App');
        $User = new User();

        $result = $User->add($info, 'radmin');

//        print_r($result);return;

//        $result['status'] = 1;
//        $result['msg']  =   'test';

        if ($result['status'] == 1) {
            $level = I('level');
            $this->add_active_log('后台增加经销商：' . $result['msg']);
            $return_url = __URL__ . '/index?level=' . $level;
            $this->success($result['msg'], $return_url);
        } else {
            $this->error($result['msg']);
        }

    }//end func user_add_submit

    function upload()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize = 1048576;
        $upload->allowExts = array('jpg', 'png', 'jpeg'); // 设置附件上传类型
        $upload->savePath = './img/radminidenum/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = false;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->return_null = true;//空的上传也返回null
        $upload->saveRule = 'com_create_guid';

        if (!$upload->upload()) {
//            return $upload->getUploadFileInfo();
//            return $upload->getErrorMsg();


            $return_result = array(
                'code' => 2,
                'msg' => $upload->getErrorMsg(),
            );

        } else {
            $return_result = array(
                'code' => 1,
                'msg' => $upload->getErrorMsg(),
                'info' => $upload->getUploadFileInfo(),
            );

//            $image = $info[0]['savepath'] . $info[0]['savename'];
//            return __ROOT__ . substr($image, 1);
        }

        return $return_result;
    }


    //用户统计
    public function cont_user()
    {

        $month = I('month');
        $name = I('name');
        $level = I('level');

        import('Lib.Action.User', 'App');
        $User = new User();


        if (empty($month) || !is_numeric($month) || strlen($month) != 6) {
            $month = date('Ym');
        }

        $condition = array();
        $condition_user = array();
        $page_info = array(
            'page_num' => I('get.p'),
        );

        if (!empty($month)) {
            $condition['month'] = $month;
        }

        if (!empty($level)) {
            $condition_user['level'] = $level;
        }

        if (!empty($name)) {
            $condition_user['_complex'] = array(
                'name' => $name,
                'wechatnum' => $name,
                '_logic' => 'or',
            );
        }

        $condition['is_get_team_info'] = TRUE;

        $user_order_count = $User->get_users_order_count($condition, $condition_user, $page_info);

//        print_r($user_order_count);return;

        $page = '';
        $reba_data_info = array();
        $all_user_order_money = array();
        $all_user_rebate_percent = array();

        if ($user_order_count['code'] != 1) {
            $all_dis_info = array();
        } else {
            $count_result = $user_order_count['result'];

            $dis_info = $count_result['dis_info'];
            $page = $count_result['page'];
            $month = $count_result['month'];
            $count = $count_result['count'];
            $limit = $count_result['limit'];
        }

        $this->page = $page;
        $this->month = $month;
        $this->list = $dis_info;

        $this->count = $count;
        $this->limit = $limit;
        $this->p = I('p');

        $this->display();

    }//end func cont_min_chart


    //获取代理信息
    public function get_distributor_info()
    {
        if (!IS_AJAX) {
            return false;
        }

        $return_res = array(
            'status' => 0,
            'msg' => '',
            'data' => array(),
            'levenames' => array(),
        );

        $name = $this->_post('name');

        if (empty($name)) {
            $return_res['status'] = 1;
            $return_res['msg'] = '提交的信息不能为空!';
        } else {

            $condition['name'] = $name;
            $condition['audited'] = 1;

            $data = M('distributor')->where($condition)->order('level asc')->select();


            if (empty($data)) {
                $return_res['status'] = 2;
                $return_res['msg'] = '没有查找到该代理!';
            } else {
                $levenames = array(); //搜索到的代理级别数组
                foreach ($data as $val) {
                    $val_level = $val['level'];
                    $val_level_name = $val['levname'];

                    if (!isset($levenames[$val_level])) {
                        $levenames[$val_level] = array(
                            'level' => $val_level,
                            'levname' => $val_level_name,
                        );
                    }
                }

                krsort($levenames);

                $return_res['status'] = 'succ';
                $return_res['data'] = $data;
                $return_res['levenames'] = $levenames;
            }
        }
        $this->ajaxReturn($return_res, 'JSON');
    }//end func get_distributor_info


    //导入快递单号
    public function impordernumber()
    {

        $upload_res = $this->upload_excel();

        if ($upload_res['code'] != 1) {
            $this->error($upload_res['msg']);
            return;
        }

        $info = $upload_res['info'];
        $path = $upload_res['path'];
        $extension = $info[0]['extension'];

        $res = $this->impexcel($info, 'manager', $extension);

        print_r($res);

    }//end func impordernumber


    function upload_excel()
    {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        //$upload->maxSize = 1048576;
        $upload->allowExts = array('xls', 'xlsx'); // 设置附件上传类型
        $upload->savePath = './upload/excel/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        if (!$upload->upload()) {
            $msg = $this->error($upload->getErrorMsg());

            $return_result = array(
                'code' => 2,
                'msg' => $msg,
            );

        } else {

            $info = $upload->getUploadFileInfo();
            $path = $info[0]['savepath'] . $info[0]['savename'];
            //return __ROOT__ . substr($path, 1);
            $return_result = array(
                'code' => 1,
                'msg' => '成功导入！',
                'path' => $path,
                'info' => $info,

            );

        }

        return $return_result;
    }//end func upload

    //查看代理详情
    public function detail()
    {
        $agent = M('distributor')->find(I('id'));
        $this->agent = $agent;
        $this->recommendID = $agent['recommendID'];
        $this->display();
    }

    //查看代理详情
    public function tree_detail()
    {
        $this->agent = M('distributor')->find(I('id'));
        $this->YM_DOMAIN = C('YM_DOMAIN');
        $this->display();
    }

    //代理升/降级页面
    public function level_handle()
    {
        $type = I('get.type');
        $upgrade_apply_id = trim(I('upgrade_apply_id'));//升级申请id
        $level_num = C('LEVEL_NUM');
        $agent = M('distributor')->find(I('get.id'));
        if ($agent['level'] == '1' && $type == 'up') {
            $this->error('最高级别无法升级！');
        }
        if ($agent['level'] == $level_num && $type == 'down') {
            $this->error('最低级别无法降级！');
            return;
        }
        $this->agent = $agent;
        $this->type = $type;
        $this->upgrade_apply_id = $upgrade_apply_id;
        $this->display();
    }


    //-----------****新版经销商树状图***---------
    /**
     * add by qjq
     */

    //获取树形图
    public function get_tree()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $page_num = trim(I('page_num'));
        $type = trim(I('type'));
        $page_list_num = trim(I('page_list_num'));

//        $page_num=1;
//        $type='recommendID';

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        if ($type == 'pid') {
            $condition = array(
                "pid" => '0',
                'audited' => 1
            );
        } elseif ($type == 'recommendID') {
            $condition = array(
                "recommendID" => '0',
                'audited' => 1
            );
        }

        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $result = $Team->dis_tree($page_info, $condition, $type);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //新版查找下属
    public function get_treedirect()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id = I('post.mid');
        $page_num = trim(I('page_num'));
        $type = trim(I('type'));
//        $type='recommendID';
//        $id=6;
//        $page_num=1;

        $page_list_num = trim(I('page_list_num'));
        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        if ($type == 'pid') {
            $condition = array(
                "pid" => $id,
                'audited' => 1
            );
        } elseif ($type == 'recommendID') {
            $condition = array(
                "recommendID" => $id,
                'audited' => 1
            );
        }

        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $result = $Team->dis_treedirect($page_info, $condition, $type);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }


    /**
     * 树状图的搜索
     */
    public function search_tree()
    {
        $id = trim(I('mid'));
        $type = trim(I('type'));

        $page_num = trim(I('page_num'));//点击查找下属，才需要这个页码


//       $id=1;
////      $id=85;
//      $type='recommendID';
        if (empty($id)) {
            $return_result = [
                'code' => 2,
                'msg' => 'mid不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        //获取父类的信息
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $Parent_info = $Team->findParent($id, $type);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'Parent_info' => $Parent_info,
        ];
        $this->ajaxReturn($return_result);
    }

//-----------****新版经销商树状图结束***---------

    public function count_user()
    {
        $month = I('month');
        $name = I('name');
        $level = I('level');


        import('Lib.Action.User', 'App');
        $User = new User();


        if (empty($month)) {
            $month = date('Ym');
        }

        $condition = array();
        $condition_user = array();
        $page_info = array(
            'page_num' => I('get.p'),
        );

        if (!empty($month)) {
            $condition['month'] = $month;
        }
        if (!empty($level)) {
            $condition['level'] = $level;
        }

        if (!empty($level)) {
            $condition_user['level'] = $level;
        }
        $condition_user['audited'] = 1;
        if (!empty($name)) {
            $condition_user['_complex'] = array(
                'name' => $name,
                'wechatnum' => $name,
                '_logic' => 'or',
            );
            $users = M('distributor')->where($condition_user)->select();
        }
        $user_order_count = $User->get_users_count($condition, $condition_user, $page_info);

        if ($users) {
            foreach ($users as $user) {
                $uids[] = $user['id'];
                $condition['id'] = ['in', $uids];
            }

        }

        if ($user_order_count['code'] != 1) {
            $dis_info = array();
            $page = '';
        } else {
            $count_result = $user_order_count['result'];

            $dis_info = $count_result['dis_info'];
            $page = $count_result['page'];
            $month = $count_result['month'];
        }
        $con_url = '';
        if (!empty($condition)) {
            $con_url = serialize($condition);
        }
        $this->con_url = base64_encode($con_url);

        import('Lib.Action.Team', 'App');
        $this->total_money = (new Team())->get_total_money($month);
        $this->page = $page;
        $this->month = $month;
        $this->list = $dis_info;
        $this->name = $name;

        $this->count = $user_order_count['result']['count'];
        $this->p = I('p');
        $this->limit = $user_order_count['result']['limit'];
        
        $this->display();

    }//end func count_user

    //查看团队
    public function show_team()
    {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        import('Lib.Action.Team', 'App');
        $team_obj = new Team();
        $month = $_GET['month'];
        $uid = $_GET['uid'];
        if (!$month) {
            $month = date('Ym');
        }
        $count = M('distributor')->where(['recommendID' => $uid])->count('id');
        $page_num = 20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $field = 'id,name,wechatnum,phone,level,levname,audited,time,update_time';
            $lower_users = M('distributor')->field($field)->where(['recommendID' => $uid])->limit($limit)->select();

            //读取缓存团队
            $team_path = get_team_path_by_cache();

            foreach ($lower_users as $key => $user) {
                //个人业绩
                $lower_users[$key]['person_money'] = $team_obj->get_team_money($user['id'], $month);
                //团队业绩
                //获取实际参与团队业绩计算的团队id
                $uids = $team_obj->get_team_count_ids($count_way, $user, $team_path);
                $lower_users[$key]['team_money'] = $team_obj->get_team_money($uids, $month);
            }
        }
        $this->list = $lower_users;
        $this->month = $month;
        $this->uid = $uid;

        $this->count = $count;
        $this->p = I('p');
        $this->limit = $page_num;
        $this->display();
    }

    //新的审核页面
    public function new_qtapplyList()
    {
        $distributor_obj = M('distributor');
        $audited = trim(I('audited'));
        $name = trim(I('name'));
        $pname = trim(I('pname'));
        $recname = trim(I('recname'));
        
        
        if (!empty($name)) {
            $condition_name = [
                'name' => ['like', "%$name%"],
                'phone' => ['like', "%$name%"],
                '_logic' => 'OR',
                'wechatnum' => ['like', "%$name%"],
            ];
            $condition['_complex'] = $condition_name;
        }
        if (!empty($pname)) {
            $condition['bossname'] = $pname;
        }
        
        if (!empty($recname)) {
            if ($recname != '总部') {
                $condition_rec['name'] = $recname;
                $dis_info = $distributor_obj->where($condition_rec)->select();
                $search_uids = [];

                foreach ($dis_info as $v_ser) {
                    $v_ser_id = $v_ser['id'];
                    $search_uids[] = $v_ser_id;
                }
                if (!empty($search_uids)) {
                    $condition['recommendID'] = ['in', $search_uids];
                } else {
                    //没找到数据
                    $condition['id'] = 0;
                }
            } else {
                $condition['recommendID'] = 0;
            }
        }
        
        if( !empty($condition) ){
            $condition['audited'] = ['neq',1];
        }
        elseif ($audited) {
            //上级审核，推到总部审核
            $condition['audited'] = ['in', [2, 4]];
            $condition['_logic'] = 'or';
            //推荐的时候，上级是总部
            $wheres=[
                'audited'=>0,
                'pid'=>0,
            ];
            $condition['_complex'] = $wheres;
        } else {
            $condition['audited'] = 0;
            $condition['pid'] = ['gt',0];
        }
        
        
        //取得满足条件的记录数
        $count = $distributor_obj->where($condition)->count('id');
        //每页显示数量
        $page_num = 20;
        if ($count > 0) {
            import('ORG.Util.Page');
            //创建分页对象
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $applyList = $distributor_obj->where($condition)->order('time desc')->limit($limit)->select();
            $applyList = $this->get_related_data($applyList, 'distributor', 'recommendID');
            //*分页显示*
            $page = $p->show();
            $this->page = $page;
            $this->count = $count;
            $this->p = I('p');
            $this->limit = $page_num;
            $this->applyList = $applyList;
        }
        $this->level_name = C('LEVEL_NAME');
        $this->audited = $audited;
        $this->display();
    }


    //新的审核
    public function new_audit()
    {
        if (!IS_AJAX) {
            halt('页面不存在！');
        }

        vendor("phpqrcode.phpqrcode");

        $mids = I('mids');
        $mids = substr($mids, 1);
        $managers = explode('_', $mids);
        $recommend_setting = M('recommend_setting');
        $recommend_rebate = M('recommend_rebate');
        $distributor = M('distributor');

        import("Wechat.Wechat", APP_PATH);
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        $this->wechat_obj = new Wechat($options);

        import('Lib.Action.Rebate', 'App');
        $Rebate = new Rebate();

        foreach ($managers as $m_id) {

            $signaturea = $distributor->where(array('id' => $m_id))->find();

            //公众号推送
            $signaturea_id = $signaturea['id'];
            $touser = $signaturea['openid'];
            $uName = $signaturea['name'];
            $keyword1 = $signaturea['name'];
            $phone = $signaturea['phone'];
            $bname = $signaturea['bossname'];
            $isRecommend = $signaturea['isRecommend'];
            $level = $signaturea['level'];
            $recommendID = $signaturea['recommendID'];
            if (!$recommendID) {
                $location = $signaturea['signature'];

                if ($location == NULL) {   //若没名片则生成名片
                    $appid = C('APP_ID');
                    $callback = 'http://' . C('YM_DOMAIN') . '/index.php/Admin/Signature/getsign';
                    $text = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($callback) . "&response_type=code&scope=snsapi_base&state=" . $m_id . "#wechat_redirect";
                    $level = "L";
                    $size = "10";
                    $location = __ROOT__ . '/signatures/' . $m_id . '.png';
                    $url = './signatures/' . $m_id . '.png';
                    //生成图片
                    QRcode::png($text, $url, $level, $size);
                }
                $arr = array(
                    'id' => $m_id,
//                'managed' => 2,
                    'audited' => 1,
                    'signature' => $location,
//                'level' => 1,
//                'levname' => $levnames[1]
                    'audit_time' => time(),
                    'end_times'=>strtotime("+1 year",time()),
                );
                if ($level == 1) {
                    $arr['managed'] = 2;
                } else if ($level == null) {
                    $level = C('LEVEL_NUM');
                }
                $res = $distributor->save($arr);
            } else {
                $save=[
                    'audit_time' => time(),
                    'end_times'=>strtotime("+1 year",time()),
                    'audited'=>1,
                ];
                $res = $distributor->where(array('id' => $m_id))->save($save);
            }



            //通过审核
            if ($res) {
                $path = C('DEFAULT_TEAM');
                if ($path == 'path') {
                    $distributor->where(['id' => $signaturea['pid']])->save(['is_lowest' => 0]);
                } else {
                    $distributor->where(['id' => $signaturea['recommendID']])->save(['is_lowest' => 0]);
                }
                $this->add_active_log('审核经销商：' . $uName . '成功');
                
                //代理任务升级
                import('Lib.Action.Upgrade', 'App');
                $user = $distributor->where(['id' => $signaturea['recommendID'], 'audited' => 1])->find();
                (new Upgrade())->upgrade($user);
            } else {
                $this->add_active_log('审核经销商：' . $uName . '失败');
                $return_result = [
                    'code' => 2,
                    'msg' => '审核' . $uName . '失败！',
                ];
                $this->ajaxReturn($return_result, 'json');
            }

//            $sendTime = date("Y-m-d H:i:s");
//            $template_id = C('SH_MB');
//            $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/";
//            $SYSTEM_NAME = C('SYSTEM_NAME');
//            $sendData = array(
//                'first' => array('value' => ("$uName,您的" . $SYSTEM_NAME . "微商管理系统经销商审核成功！"), 'color' => "#CC0000"),
//                'keyword1' => array('value' => ("$keyword1"), 'color' => '#000'),
//                'keyword2' => array('value' => ("$phone"), 'color' => '#000'),
//                'keyword3' => array('value' => ("$sendTime"), 'color' => '#000'),
//                'remark' => array('value' => ("欢迎您加入" . $SYSTEM_NAME . "微商管理系统。您的直属上级:" . $bname . "。"), 'color' => '#CC0000')
//            );
//            import('ORG.Net.OrderPush');
////            $sendMsg = new OrderPush(C('APP_ID'), C('APP_SECRET'));
////            $sendMsg->doSend($touser, $template_id, $url, $sendData, $topcolor = '#7B68EE');
//
//            $template = array(
//                'touser' => $touser,
//                'template_id' => $template_id,
//                'url' => $url,
//                'topcolor' => '#7B68EE',
//                'data' => $sendData
//            );
//
//            $this->wechat_obj->sendTemplateMessage($template);



            //这里是调用message的模板消息 edit by qjq 2018-1-30（注释上面旧的模板消息就可以开启此方法）
            import('Lib.Action.Message','App');
            $message = new Message();
            $message->push(trim($touser), $signaturea, $message->audit_manager);
            //调用结束

            //------------推荐人的返利--------------

            //----------生成返利--------------

            //新版本的统一调用此审核方式，如果推荐人为总部，则不调用推荐返利的方法，修改时间为2018/1/24 by qjq
            if ($recommendID) {
                $rebate_result = $Rebate->radmin_user_audit_rebate($m_id, $signaturea);
            }

//            if( $rebate_result['code'] != 1 ){
//                $this->ajaxReturn(array('status' => 2), 'json');
//            }

            //----------end 生成返利--------------


            //------------end 推荐人的返利--------------


        }
//        //保险起见，再重新找is_lowest没有置0的，并且置0(影响团队业绩)
//        import('Lib.Action.Team', 'App');
//        (new team())->is_yes_lowest();
        
        //清除团队缓存
        clean_team_path_cache();
        
        $return_result = [
            'code' => 1,
            'msg' => '审核成功',
        ];
        $this->ajaxReturn($return_result, 'JSON');
    }
    
    //删除代理后清除/修改数据
    private function delete_agent_after($row, $user_obj) {
        //被删除的代理直属下级/被推荐人默认继承被删除的代理的参数
        //更新团队path和rec_path
        $path = C('DEFAULT_TEAM');
        if ($path == 'path') {
            $where['pid'] = $row['id'];
        } else {
            $where['recommendID'] = $row['id'];
        }
        $lower_user = M('distributor')->where($where)->select();
        if ($lower_user) {
            foreach ($lower_user as $v) {
               $user_obj->change_recommend($v['id'], $row['recommendID']);
               $user_obj->change_parent($v['id'], $row['pid']);
           }
        }
        //删除无关紧要数据
        $map['user_id'] = $row['id'];
        $condition['uid'] = $row['id'];
        M('address')->where($map)->delete();
        M('activity_order')->where($map)->delete();
        M('distributor_bank')->where($condition)->delete();
        M('distributor_bind')->where($condition)->delete();
        //积分
        M('integral_log')->where($condition)->delete();
        M('integralorder')->where($map)->delete();
        M('integralorder_count')->where($condition)->delete();
        M('integralorder_month_count')->where($condition)->delete();
        M('integralorder_shopping_cart')->where($condition)->delete();
        //虚拟币
        M('money_apply')->where($condition)->delete();
        $charge_log = M('money_charge_log')->where($condition)->select();
        if ($charge_log) {
            setLog('扣费记录'.json_encode($charge_log),'delete-agent-after');
            M('money_charge_log')->where($condition)->delete();
        }
        $funds = M('money_funds')->where($condition)->select();
        if ($funds) {
            setLog('资金记录'.json_encode($funds),'delete-agent-after');
            M('money_funds')->where($condition)->delete();
        }
        $count = M('money_month_count')->where($condition)->select();
        if ($count) {
            setLog('资金统计'.json_encode($count),'delete-agent-after');
            M('money_month_count')->where($condition)->delete();
        }
        $recharge_log = M('money_recharge_log')->where($condition)->select();
        if ($recharge_log) {
            setLog('充值记录'.json_encode($recharge_log),'delete-agent-after');
            M('money_recharge_log')->where($condition)->delete();
        }
        $refund_log = M('money_refund')->where($condition)->select();
        if ($refund_log) {
            setLog('提现记录'.json_encode($refund_log),'delete-agent-after');
            M('money_refund')->where($condition)->delete();
        }
        //订单
        $order = M('order')->where($map)->select();
        if ($order) {
            setLog('订单记录'.json_encode($order),'delete-agent-after');
            M('order')->where($map)->delete();
        }
        $order_count = M('order_count')->where($condition)->select();
        if ($order_count) {
            setLog('订单统计'.json_encode($order_count),'delete-agent-after');
            M('order_count')->where($condition)->delete();
        }
        //返利
        $rebate_count = M('rebate_count')->where($condition)->select();
        if ($rebate_count) {
            setLog('返利统计'.json_encode($rebate_count),'delete-agent-after');
            M('rebate_count')->where($condition)->delete();
        }
        $rebate_other = M('rebate_other')->where($condition)->select();
        if ($rebate_other) {
            setLog('其它返利'.json_encode($rebate_other),'delete-agent-after');
            M('rebate_other')->where($condition)->delete();
        }
        $rebate_team = M('rebate_team')->where($condition)->select();
        if ($rebate_team) {
            setLog('团队返利'.json_encode($rebate_team),'delete-agent-after');
            M('rebate_team')->where($condition)->delete();
        }
        $distributor_upgrade_apply = M('distributor_upgrade_apply')->where($condition)->select();
        if ($distributor_upgrade_apply) {
            setLog('升级申请'.json_encode($distributor_upgrade_apply),'delete-agent-after');
            M('distributor_upgrade_apply')->where($condition)->delete();
        }
        
    }

    //授权时间的修改
    public function set_aduit_time(){
        $id=trim(I('id'));
        $info=M('distributor')->find($id);
        $this->info=$info;
        $this->display();
    }
    public function post_set_aduit_time(){
        $id=trim(I('id'));
        $end_time=trim(I('end_times'));
        if(!empty($id)){
            $info=M('distributor')->find($id);
            $e_time=$info['end_times'];
            if($end_time == 1){
                $e_times=strtotime("+1month",$e_time);
            }
            elseif ($end_time == 2){
                $e_times=strtotime("+3month",$e_time);
            }
            elseif ($end_time == 3){
                $e_times=strtotime("+6month",$e_time);
            }
            elseif ($end_time == 4){
                $e_times=strtotime("+1year",$e_time);
            }
            $res=M('distributor')->where(array('id'=>$id))->save(['end_times' => $e_times]);
        }
        if($res){
            $this->success('修改成功');
            $this->add_active_log('修改授权期限');
        }else{
            $this->error('修改失败');
        }

    }

    //授权期限相关
    public function set_time(){
        $this->level_num = C('LEVEL_NUM');
        $this->level_name = C('LEVEL_NAME');
        $this->display();
    }
    public function get_time_id(){
        $id=trim(I('id'));
        $info=M('distributor')->find($id);
        $aduit_time=date("Y-m-d",$info['audit_time']);
        $end_times=date("Y-m-d",$info['end_times']);
        $return_result=[
            'code'=>1,
            'msg'=>'获取成功',
            'audit_time'=>$aduit_time,
            'end_times'=>$end_times
        ];
        $this->ajaxReturn($return_result);
    }
    public function add_time(){
        $id=trim(I('receive_id'));
        $end_time=trim(I('end_times'));
        if($id == 'a'){
            $this->error('代理不能为空');
        }
        if(!empty($id)){
            $info=M('distributor')->find($id);
            $e_time=$info['end_times'];

            if($end_time == 1){
                $e_times=strtotime("+1month",$e_time);
            }
            elseif ($end_time == 2){
                $e_times=strtotime("+3month",$e_time);
            }
            elseif ($end_time == 3){
                $e_times=strtotime("+6month",$e_time);
            }
            elseif ($end_time == 4){
                $e_times=strtotime("+1year",$e_time);
            }
            $res=M('distributor')->where(array('id'=>$id))->save(['end_times' => $e_times]);
        }else{
            $info=M('distributor')->select();
            foreach ($info as $key => $value){
                $e_time=$value['end_times'];
                if($end_time == 1){
                    $e_times=strtotime("+1month",$e_time);
                }
                elseif ($end_time == 2){
                    $e_times=strtotime("+3month",$e_time);
                }
                elseif ($end_time == 3){
                    $e_times=strtotime("+6month",$e_time);
                }
                elseif ($end_time == 4){
                    $e_times=strtotime("+1year",$e_time);
                }

                $res=M('distributor')->where(array('id'=>$value['id']))->save(['end_times' => $e_times]);
            }
        }
        if($res){
            $this->success('修改成功');
            $this->add_active_log('修改授权期限');
        }else{
            $this->error('修改失败');
        }

    }

}

?>