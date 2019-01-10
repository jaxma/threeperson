<?php
//返利的模块化代码
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class NewRebate extends Common{

    private $team_obj;
    private $user_obj;
    private $funds_obj;
    private $order_obj;
    private $money_month_model;
    private $rebate_team_model;
    private $rebate_other_model;
    private $rebate_count_model;
    private $distributor_model;
    private $month;
//    private $users;
    private $order_count_model;

//    private $is_parent_audit;//虚拟币是否总部充值
//    private $is_top_supply;//是否向上级拿货
//    private $count_way;//业绩统计方式0虚拟币1订单金额2订单数量

    private $rebate_other_setting_model;
    private $rebate_team_setting_model;
    public $order_rebate = 0; //平级推荐订单返利
    public $money_rebate = 1; //平级推荐虚拟币返利
    public $once_rebate = 2; //低推高一次性返利
    public $development_rebate = 3; //高发展低一次性返利
    public $same_development_rebate = 4; //平级推荐一次性返利
    public $open = 1; //返利开启
    public $close = 0; //返利关闭
    public $head_pay = 0; //总部支付
    public $sup_pay = 1; //上级支付
    public $percent_way = 0; //订单金额x百分比计算返利
    public $money_way = 1; //订单产品数量x金额计算返利
    public $yes_pay = 1; //已结算
    public $not_pay = 0; //未结算

    private $one_rebate = 1; //一级返利
    private $two_rebate = 2; //二级返利
    private $three_rebate = 3; //三级返利

    public $status_name = [];//返利状态
    public $rebate_name = [];//返利类型
    
    public $team_setting;//团队返利参数
    public $ordinary_team_rebate = 3;//普通团队返利
    public $person_setting;//个人返利参数
    public $personal_rebate = 5;//个人业绩返利
    public $money_count_way = 0;//按虚拟币统计
    public $order_count_way = 1;//按订单金额统计
    public $order_num_count_way = 2;//按订单数量统计
    
    //定义产生$rebate_agent_level返利的代理等级
    public $rebate_agent_level = [1,2];
    /**
     * 架构函数
     */
    public function __construct() {
        import('Lib.Action.User','App');
        import('Lib.Action.Team','App');
        $this->user_obj = new User();
        $this->team_obj = new Team();
        $this->money_month_model = M('money_month_count');
        $this->order_count_model = M('order_count');
        $this->rebate_count_model = M('rebate_count');
        $this->rebate_team_model = M('rebate_team');
        $this->rebate_other_model = M('rebate_other');
        $this->rebate_other_setting_model = M('rebate_other_setting');
        $this->rebate_team_setting_model = M('rebate_team_setting');
        $this->distributor_model = M('distributor');
        //定义
//        $this->personal_team_users = $this->user_obj->get_rebate_users($this->rebate_agent_level);

        $this->month = get_month();

        $this->status_name[$this->not_pay] = '未结算';
        $this->status_name[$this->yes_pay] = '已结算';

        $this->rebate_name[$this->order_rebate]= '平级推荐订单奖励';
        $this->rebate_name[$this->money_rebate]= '平级推荐充值奖励';
        $this->rebate_name[$this->once_rebate]= '低推高一次性奖励';
        $this->rebate_name[$this->development_rebate]= '高发展低一次性奖励';
        $this->rebate_name[$this->same_development_rebate]= '平级发展一次性奖励';

        $this->type=[
            $this->ordinary_team_rebate => '团队奖',
            $this->personal_rebate => '个人业绩奖',
        ];
        
        $map = [
            'status'=>$this->open,
            'type'=> $this->ordinary_team_rebate
        ];
        $map_person = [
            'status'=>$this->open,
            'type'=> $this->personal_rebate
        ];
        $this->team_setting = $this->rebate_team_setting_model->where($map)->order('achievement asc')->select();//团队返利比例设置参数
        if ($this->team_setting) {
            $this->rebate_agent_level = json_decode($this->team_setting[0]['level'], true);
        }
        $this->person_setting = $this->rebate_team_setting_model->where($map_person)->order('achievement asc')->select();
    }

    //生成团队奖
    public function create_team_rebate($id, $month) {
        if ($month) {
            $this->month = $month;
        }
//        if ($users) {
//            $this->users = $users;
//        }
        //统计团队业绩
        $this->team_money_count($id);

        //团队奖
        $this->team_rebate_count($id);//统计返利

        //个人业绩奖
        $this->personal_rebate_count();
    }

    //统计代理团队业绩
    private function team_money_count($id) {
        //读取缓存团队
        $team_path = get_team_path_by_cache();
        /**
         * 自定义
         * 生成返利的代理
         */
        if (!$this->rebate_agent_level) {
            return;
        }
//        $level = json_decode($this->team_setting[0]['level'], true);
        //如果团队业绩是点击更新的
        if(C('REBATE')['CLICK_TEAM_REBATE']){
            if (!$id) {
                $condition = ['audited'=>1];
            } else {
                //更新一个代理的团队业绩和返利也要更新整个团队的
                $ids = $this->team_obj->get_team_ids($id, $team_path);
                $condition = ['id'=>['in',$ids], 'audited'=>1];
            }
            $users = M('distributor')->where($condition)->select();
        }
        //如果是实时更新的
        else{
            //先获取个人信息
            $in_info=$this->distributor_model->find($id);
            //找出上级的信息
            //判断团队按照什么形式来找
            $rec_path=C('REBATE')['DEFAULT_TEAM'];
            $pid=explode('-',$in_info[$rec_path]);
            //将自己本身追加到数组后面
            array_push($pid,$id);
//            foreach ($pid as $k=>$v){
//                      $infos=$this->distributor_model->find($v);
//                      if(in_array($infos['level'],$level)){
//                          $ids[]=$infos['id'];
//                      }
//            }
            $users = $this->distributor_model->where(['id'=>['in', $pid], 'audited'=>1,'level'=>['in', $level]])->select();
//            setLog(json_encode($users));
        }
        $achievement_way = $this->team_setting[0]['achievement_way'];//返利设置的统计方式0虚拟币1订单金额2订单数量
        foreach ($users as $user) {
            //获取实际参与团队业绩计算的团队id
            $uids = $this->team_obj->get_team_count_ids($achievement_way, $user, $team_path);

            //团队业绩
            $team_money = $this->team_obj->get_team_money($uids, $this->month);
            $team_money = empty($team_money) ? 0.00 :$team_money;
//            if (!$total_money) {
//                continue;
//            }
            //写进团队返利明细表
            $map = [
                'uid' => $user['id'],
                'month' => $this->month,
            ];
            $ratio = $this->get_team_rebate_ratio($team_money);//返利比例
            $rebate_team = $this->rebate_count_model->where($map)->find();
            if (!$rebate_team) {
                $data = [
                    'uid' => $user['id'],
                    'team_money' => $team_money,
                    'rebate_money' => $team_money * $ratio,
                    'ratio' => $ratio,
                    'month' => $this->month,
                ];
                if (!$this->rebate_count_model->add($data)) {
                    setLog('写进团队返利明细表失败:'.json_encode($data), 'detail');
                }
            } else {
                $data = [
                    'team_money' => $team_money,
                    'rebate_money' => $team_money * $ratio,
                    'ratio' => $ratio,
                ];
                $res = $this->rebate_count_model->where($map)->save($data);
                if ($res === false) {
                    setLog('更新团队返利明细表出错:'.json_encode($data).'where:'.  json_encode($map), 'detail');
                }
            }

//        echo memory_get_usage().'字节';die;
        }
    }

    /**
     * 获取团队业绩返利比例
     * @param decimal $money
     * @return decimal
     */
    private function get_team_rebate_ratio($money) {
//        $ratio = C('TEAM_REBATE_RATIO');
        $count = count($this->team_setting);
        if ($money < $this->team_setting[0]['achievement']) {
            return 0;
        }
        for($i = 0;$i < $count-1;$i++) {
            if ($money >= $this->team_setting[$i]['achievement'] && $money < $this->team_setting[$i+1]['achievement']) {
                return $this->team_setting[$i]['parameter'];
            }
        }
        if ($money >= $this->team_setting[$count-1]['achievement']) {
            return $this->team_setting[$count-1]['parameter'];
        }
    }

    /**
     * 团队返利统计
     * $id 代理id
     */
    private function team_rebate_count($id) {
        if (!$this->rebate_agent_level) {
            return;
        }
        $distributor_model = M('distributor');
        if ($id) {
            //更新个人的返利
            $rebate_team = $this->rebate_count_model->where(['uid'=>$id,'month' => $this->month])->select();
        } else {
            $rebate_team = $this->rebate_count_model->where(['month' => $this->month])->select();
        }
        foreach ($rebate_team as $team) {
            //排除不产生返利的等级
            $rebate_user = $this->distributor_model->find($team['uid']);
            if(!in_array($rebate_user['level'], $this->rebate_agent_level)) {
                continue;
            }
            //找到直属下级
            $lower_users = $distributor_model->field('id')->where(['recommendID' => $team['uid']])->select();
            $data = [
                'uid' => $team['uid'],
                'status' => 0,
                'type' => 3,
                'month' => $this->month,
                'time' => time()
            ];

            $where = [
                'uid' => $team['uid'],
                'month' => $this->month,
                'type' => 3,
                'status' => 0,
            ];
            $rebate_count = $this->rebate_team_model->where($where)->find();
            if (empty($lower_users)) {
                //团队只有自己一个人
                if ($team['rebate_money'] <= 0) {
                    if ($rebate_count) {
                        //如果之前有返利，现在没有则删除
                        $this->rebate_team_model->where($where)->delete();
                    }
                    continue;
                }
                $team_money = [
                    'total_money' => $team['team_money'],
                    'rebate_money' => $team['rebate_money'],
                    'ratio' => $team['ratio'],
                ];
                if (!$rebate_count) {
                    if(!$this->rebate_team_model->add(array_merge($data, $team_money))) {
                        setLog('统计团队返利失败(单人):'.json_encode(array_merge($data, $team_money)), 'count');
                    }
                } else {
                    $res = $this->rebate_team_model->where($where)->save($team_money);
                    if ($res === false) {
                        setLog('更新团队返利出错(单人):'.json_encode($team_money).'where:'.  json_encode($where), 'count');
                    }
                }
            } else {
                //多人组成的团队A返利算法=A团队返利 - A直属团队返利之和
                $lower_ids = [];
                foreach ($lower_users as $user) {
                    $lower_ids[] = $user['id'];
                }
                $rebate_money = $this->rebate_count_model->where([ 'uid' => ['in',$lower_ids], 'month' => $this->month])->sum('rebate_money');
                $rebate_money = empty($rebate_money) ? 0 : $rebate_money;
                $team_money = [
                    'total_money' => $team['team_money'],
                    'rebate_money' => $team['rebate_money']-$rebate_money,
                    'ratio' => $team['ratio'],
                ];

                //如果统计出错了要重新统计则要注释这段代码在执行更新
                if ($team_money['rebate_money'] <= 0) {
                    continue;
                }
                if (!$rebate_count) {
                    if(!$this->rebate_team_model->add(array_merge($data, $team_money))) {
                        setLog('统计团队返利失败(多人):'.json_encode(array_merge($data, $team_money)), 'count');
                    }
                } else {
                    $res = $this->rebate_team_model->where($where)->save($team_money);
                    if ($res === false) {
                        setLog('更新团队返利出错(多人):'.json_encode($team_money).'where:'.  json_encode($where), 'count');
                    }
                }
            }
        }
    }



    //统计代理个人业绩（旧版本）
//    public function personal_rebate_count(){
//        foreach ($this->personal_team_users as $user) {
//                $uid=$user['id'];
//            //获取个人业绩
//            $team_money = $this->team_obj->get_team_money($uid,$this->month);
//
//            $team_money = empty($team_money) ? 0.00 :$team_money;
//
//            //直接写进团队返利表
//            $map = [
//                'uid' => $user['id'],
//                'month' => $this->month,
//                'type' => 5,
//                'status' => 0,
//            ];
//
//            $ratio = $this->get_personal_rebate_ratio($team_money);
//            $rebate_team = $this->rebate_team_model->where($map)->find();
//            //如果统计出错了要重新统计则要注释这段代码在执行更新
//            if ($team_money <= 0) {
//                continue;
//            }
//            if (!$rebate_team) {
//                $data = [
//                    'uid' => $user['id'],
//                    'type'=>5,
//                    'total_money' => $team_money,
//                    'rebate_money' => $team_money * $ratio,
//                    'ratio' => $ratio,
//                    'month' => $this->month,
//                    'time' =>time(),
//                    'status'=>0,
//                ];
//                if (!$this->rebate_team_model->add($data)) {
//                    setLog('个人业绩写进团队返利表失败:'.json_encode($data), 'detail');
//                }
//            } else {
//                $data = [
//                    'team_money' => $team_money,
//                    'rebate_money' => $team_money * $ratio,
//                    'ratio' => $ratio,
//                    'time' =>time(),
//                ];
//                $res = $this->rebate_team_model->where($map)->save($data);
//                if ($res === false) {
//                    setLog('个人业绩更新团队返利表出错:'.json_encode($data).'where:'.  json_encode($map), 'detail');
//                }
//            }
//        }
//
//    }
    /**
     * 获取个人业绩返利比例（旧版本配套）
     * @param decimal $money
     * @return decimal
     */
//    private function get_personal_rebate_ratio($money) {
//        $ratio = C('PERSONAL_REBATE_RATIO');
//        $count = count($ratio['MONEY']);
//
//        if ($money < $ratio['MONEY'][0]) {
//            return 0;
//        }
//        for($i = 0;$i < $count-1;$i++) {
//            if ($money >= $ratio['MONEY'][$i] && $money < $ratio['MONEY'][$i+1]) {
//                return $ratio['RATIO'][$i];
//            }
//        }
//        if ($money >= $ratio['MONEY'][$count-1]) {
//            return $ratio['RATIO'][$count-1];
//        }
//    }

    //统计代理个人业绩
    public function personal_rebate_count(){
        //读取缓存团队
        $team_path = get_team_path_by_cache();
        /**
         * 自定义
         * 生成返利的代理
         */
        if (!$this->person_setting) {
            return;
        }
        $level = json_decode($this->person_setting[0]['level'], true);
        $users = M('distributor')->where(['level'=>['in', $level], 'audited'=>1])->select();
        foreach ($users as $user) {
            $uid=$user['id'];
            //获取个人业绩
            $team_money = $this->team_obj->get_team_money($uid,$this->month);

            $team_money = empty($team_money) ? 0.00 :$team_money;

            //直接写进团队返利表
            $map = [
                'uid' => $user['id'],
                'month' => $this->month,
                'type' => 5,
                'status' => 0,
            ];

            $ratio = $this->get_personal_rebate_ratio($team_money);
            $rebate_team = $this->rebate_team_model->where($map)->find();
            //如果统计出错了要重新统计则要注释这段代码在执行更新
            if ($team_money <= 0) {
                continue;
            }
            if (!$rebate_team) {
                $data = [
                    'uid' => $user['id'],
                    'type'=>5,
                    'total_money' => $team_money,
                    'rebate_money' => $team_money * $ratio,
                    'ratio' => $ratio,
                    'month' => $this->month,
                    'time' =>time(),
                    'status'=>0,
                ];
                if (!$this->rebate_team_model->add($data)) {
                    setLog('个人业绩写进团队返利表失败:'.json_encode($data), 'detail');
                }
            } else {
                $data = [
                    'total_money' => $team_money,
                    'rebate_money' => $team_money * $ratio,
                    'ratio' => $ratio,
                    'time' =>time(),
                ];
                $res = $this->rebate_team_model->where($map)->save($data);
                if ($res === false) {
                    setLog('个人业绩更新团队返利表出错:'.json_encode($data).'where:'.  json_encode($map), 'detail');
                }
            }
        }

    }

    /**
     * 获取个人业绩返利比例
     * @param decimal $money
     * @return decimal
     */
    private function get_personal_rebate_ratio($money) {
        $count = count($this->person_setting);
        if ($money < $this->person_setting[0]['achievement']) {
            return 0;
        }
        for($i = 0;$i < $count-1;$i++) {
            if ($money >= $this->person_setting[$i]['achievement'] && $money < $this->person_setting[$i+1]['achievement']) {
                return $this->person_setting[$i]['parameter'];
            }
        }
        if ($money >= $this->person_setting[$count-1]['achievement']) {
            return $this->person_setting[$count-1]['parameter'];
        }
    }




    /**
     * 生成平级推荐订单/充值返利
     * $type为0订单返利1充值返利
     */
    public function same_level_rebate($uid,$data, $type = 0) {
        $user = $this->user_obj->get_user_by_id($uid);
        $level = $user['level'];
        $where = [
            'level' => $level,
            'type' => $type,
            'status' => $this->open,
        ];
        $setting = $this->rebate_other_setting_model->where($where)->find();
        if (!$setting || !$user['recommendID']) {
            return;
        }
        $rec_user = $this->user_obj->get_user_by_id($user['recommendID']);
        //不是平级直接终止
        if ($rec_user['level'] != $level) {
            return;
        }
        //支付者
        if ($setting['pay_way'] == $this->head_pay) {
            $payer_id = 0;
        }
        //返利计算方式
        if ($setting['count_way'] == $this->percent_way) {
            if ($type == $this->order_rebate) {
                //订单返利
                $total = $data['total_price'];
                $other_info = $data['order_num'];
            } else if ($type == $this->money_rebate) {
                //充值返利
                $total = $data['money'];
                $other_info = $data['id'];
            }
            $ratio_info = ' 百分比';
        } else if ($setting['count_way'] == $this->money_way) {
            $total = $data['total_num'];
            $ratio_info = '元/件';
            $other_info = $data['order_num'];
        }
        //一层返利
        if (!isset($payer_id)) {
            //上级支付返利
            $payer_id = $rec_user['pid'];
        }
        $data = [
            'uid' => $rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param1'],
            'ratio_info' => $setting['param1'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];
        $this->rebate_other_model->add($data);

        if ($setting['depth'] < $this->two_rebate) {
            return;
        }
        //二层返利
        if (!$rec_user['recommendID']) {
            return;
        }
        $rec_rec_user = $this->user_obj->get_user_by_id($rec_user['recommendID']);
        //不是平级直接终止
        if ($rec_rec_user['level'] != $level) {
            return;
        }
        if (!isset($payer_id)) {
            //上级支付返利
            $payer_id = $rec_rec_user['pid'];
        }
        $data = [
            'uid' => $rec_rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param2'],
            'ratio_info' => $setting['param2'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];
        $this->rebate_other_model->add($data);

        //三层返利
        if (!$rec_rec_user['recommendID']) {
            return;
        }
        $rec_rec_rec_user = $this->user_obj->get_user_by_id($rec_rec_user['recommendID']);
        //不是平级直接终止
        if ($rec_rec_rec_user['level'] != $level) {
            return;
        }
        if (!isset($payer_id)) {
            //上级支付返利
            $payer_id = $rec_rec_rec_user['pid'];
        }
        $data = [
            'uid' => $rec_rec_rec_user['id'],
            'rec_id' => $uid,
            'payer_id' => $payer_id,
            'type' => $type,
            'money' => $total * $setting['param3'],
            'ratio_info' => $setting['param3'].$ratio_info,
            'other_info' => $other_info,
            'month' => $this->month,
            'time' => time(),
        ];
        $this->rebate_other_model->add($data);
    }
    /**
     * 低推高/高发展低/平级一次性返利
     * type=2为低推高 type=3为高发展低 type=4为平推
     * @param type $user
     */
    public function once_rebate($user, $type = 2) {
        //$user为被推荐人的信息
        $level = $user['level'];
        $where = [
            'level' => $level,
            'type' => $type,
            'status' => $this->open,
        ];
        $setting = $this->rebate_other_setting_model->where($where)->find();
        if (!$setting || !$user['recommendID']) {
            return;
        }
        $rec_user = $this->user_obj->get_user_by_id($user['recommendID']);

        //支付者
        if ($setting['pay_way'] == $this->head_pay) {
            $payer_id = 0;
        } else {
            //上级支付返利
            $payer_id = $rec_user['pid'];
        }

        if ($type == $this->once_rebate) {
            if ($rec_user['level'] <= $user['level']) {
                return;
            }
        } elseif ($type == $this->development_rebate) {
            if ($rec_user['level'] >= $user['level']) {
                return;
            }
        } elseif($type == $this->same_development_rebate){
            if ($rec_user['level'] != $user['level']) {
                return;
            }
        }

        if(empty($setting['param1'])){
            return;
        }

            //一层返利
            $data = [
                'uid' => $rec_user['id'],
                'rec_id' => $user['id'],
                'payer_id' => $payer_id,
                'type' => $type,
                'money' => $setting['param1'],
                'ratio_info' => $setting['param1'],
                'month' => $this->month,
                'time' => time(),
            ];
            $this->rebate_other_model->add($data);

        //二层返利
        if (!$rec_user['recommendID']) {
            return;
        }
        $rec_rec_user = $this->user_obj->get_user_by_id($rec_user['recommendID']);
        //判断是否符合要求的级别
        //两层以上的低推高，高发展低，第三、四级的代理要和第二级平级
        //B->A->A->A A->B->B->B
        if ($type == $this->once_rebate) {
            if ($rec_rec_user['level'] != $rec_user['level']) {
                return;
            }
        } elseif ($type == $this->development_rebate) {
            if ($rec_rec_user['level'] != $rec_user['level']) {
                return;
            }
        } elseif($type == $this->same_development_rebate) {
            if ($rec_rec_user['level'] != $rec_user['level']) {
                return;
            }
        }
        if(empty($setting['param2'])){
            return;
        }
        $data = [
            'uid' => $rec_rec_user['id'],
            'rec_id' => $user['id'],
            'payer_id' => $payer_id,
            'type' => $type,
            'money' =>  $setting['param2'],
            'ratio_info' => $setting['param2'],
            'month' => $this->month,
            'time' => time(),
        ];
        $this->rebate_other_model->add($data);

        //三层返利
        if (!$rec_rec_user['recommendID']) {
            return;
        }
        $rec_rec_rec_user = $this->user_obj->get_user_by_id($rec_rec_user['recommendID']);
        //判断是否符合要求的级别
        if ($type == $this->once_rebate) {
            if ($rec_rec_rec_user['level'] != $rec_user['level']) {
                return;
            }
        } elseif ($type == $this->development_rebate) {
            if ($rec_rec_rec_user['level'] != $rec_user['level']) {
                return;
            }

        } elseif($type == $this->same_development_rebate) {
            if ($rec_rec_rec_user['level'] != $rec_user['level']) {
                return;
            }
        }
        if(empty($setting['param3'])){
            return;
        }
        $data = [
            'uid' => $rec_rec_rec_user['id'],
            'rec_id' => $user['id'],
            'payer_id' => $payer_id,
            'type' => $type,
            'money' =>$setting['param3'],
            'ratio_info' => $setting['param3'],
            'month' => $this->month,
            'time' => time(),
        ];
        $this->rebate_other_model->add($data);
    }

    /**
     * 获取其它返利记录
     */
    public function get_other_rebate($page_info=array(),$condition=array()) {
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $count = $this->rebate_other_model->where($condition)->count();
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->rebate_other_model->where($condition)->page($page_con)->select();
            }
            else{
                $list = $this->rebate_other_model->where($condition)->select();
            }
            //获取获利人、被推荐人、支付人信息
            $list = $this->get_related_data($list, 'distributor', ['uid','rec_id','payer_id']);
            foreach ($list as $k =>$v) {
                if (!$v['payer_id_info']) {
                    $list[$k]['payer_id_info']['name'] = '总部';
                }
                $list[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
                $list[$k]['status_name'] = $this->status_name[$v['status']];
                $list[$k]['rebate_name'] = $this->rebate_name[$v['type']];
            }
        }
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page_list_num,
            'count' => $count,
            'status_name' => $this->status_name,
        );

        return $return_result;
    }//end func get_rerebate

    //审核其它返利
    public function audit_other_rebate($ids, $status) {
        import('Lib.Action.Funds','App');
        $funds = new Funds();
        foreach ($ids as $id) {
            $rebate_info = $this->rebate_other_model->find($id);

            if( empty($rebate_info) ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '查无此返利数据！',
                ];
                return $return_result;
            }

            $user_id = $rebate_info['uid'];
            $money = $rebate_info['money'];
            $payer_id = $rebate_info['payer_id'];

            $return_result = $funds->rebate_aduit_recharge($user_id,$money,$payer_id);

            if( $return_result['code'] == 1 ){
                $data = array(
                    'status' => $status,
                    'time' => time()
                );
                $row = $this->rebate_other_model->where(['id' => $id, 'status' => $this->not_pay])->save($data);

                if( !$row ){
                    $return_result = [
                        'code'  =>  3,
                        'msg'   =>  '返利审核失败，请重试！',
                    ];
                }else{
                    $return_result = [
                        'code'  =>  1,
                        'msg'   =>  '返利审核成功！',
                    ];
                }
            }
        }
        return $return_result;
    }
    
    //审核团队返利
    public function audit_team_rebate($ids, $status) {
        import('Lib.Action.Funds','App');
        $funds = new Funds();
        $curr_month = date('Ym');
        foreach ($ids as $id) {
            $rebate_month = $this->rebate_team_model->where(['id' => $id])->getField('month');
            if ($rebate_month == $curr_month) {
                $return_result = [
                    'code' => 3,
                    'msg' => '不能结算当前月份返利',
                ];
                return $return_result;
            }
        }
        foreach ($ids as $id) {
            $rebate_info = $this->rebate_team_model->find($id);

            if(empty($rebate_info) ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '查无此返利数据！',
                ];
                return $return_result;
            }

            $uid = $rebate_info['uid'];
            $money = $rebate_info['rebate_money'];
            $payer_id = empty($rebate_info['payer_id']) ? 0 : $rebate_info['payer_id'];

            $return_result = $funds->rebate_aduit_recharge($uid,$money,$payer_id);

            if( $return_result['code'] == 1 ){
                $data = array(
                    'status' => $status,
                    'time' => time()
                );
                $row = $this->rebate_team_model->where(['id' => $id, 'status' => $this->not_pay])->save($data);

                if( !$row ){
                    $return_result = [
                        'code'  =>  3,
                        'msg'   =>  '返利审核失败!',
                    ];
                }else{
                    $return_result = [
                        'code'  =>  1,
                        'msg'   =>  '返利审核成功！',
                    ];
                }
            }
        }
        return $return_result;
    }

    //总后台显示团队返利
    public function get_team_rebate($page_info=array(),$condition=array()){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?50:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        $count = $this->rebate_team_model->where($condition)->count('id');
        if ( $count > 0 ) {
            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;

                $list = $this->rebate_team_model->where($condition)->order('payer_id asc,time desc')->page($page_con)->select();
            }
            else{
                $list = $this->rebate_team_model->where($condition)->order('payer_id asc,time desc')->select();
            }

            if (isset($condition['month'])) {
                $month = $condition['month'];
            } else {
                $month = date('Ym');
            }
            $list = $this->get_related_data($list, 'distributor', ['uid','payer_id']);
            foreach ($list as $k => $v) {
                $list[$k]['status_name'] = $this->status_name[$v['status']];
                //个人业绩
                $list[$k]['person_money'] = $this->team_obj->get_team_money($v['uid'], $month);
                $list[$k]['type_name']=$this->type[$v['type']];
                $list[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
            }
        }

        $return_result = array(
            'list'  =>  $list,
            'count' => $count,
            'limit' => $page_list_num,
            'status_name' => $this->status_name,
        );

        return $return_result;
    }//end func get_other_rebate_info
}