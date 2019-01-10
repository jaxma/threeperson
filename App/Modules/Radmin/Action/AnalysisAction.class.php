<?php

/**
 *    topos经销商管理系统主页
 */
class AnalysisAction extends CommonAction
{
    private $distributor_model;
    private $order_model;
    private $order_count_model;
    private $money_recharge_log_model;
    private $templet_model;
    public function _initialize()
    {
        parent::_initialize();
        $this->distributor_model = M('distributor');
        $this->order_model = M('order');
        $this->order_count_model = M('order_count');
        $this->money_recharge_log_model = M('money_recharge_log');
        $this->templet_model=M('templet');
    }

    public function index()
    {
        $level_name = C('LEVEL_NAME');
        foreach ($level_name as $k => $v) {
            $agent[$k] = 0;
        }
        $no_agent_audited_users = 0;
        $no_head_audited_users = 0;
        $yes_audited_users = 0;
        $day_users = 0;
        //当天结束时间戳

        $start_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_time = $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        
        $distributor = $this->distributor_model;
        $users = $distributor->field(['id,audited,time,level'])->select();

        foreach ($users as $user) {
            if ($user['audited'] == 1) {
                $yes_audited_users++;
            } else if ($user['audited'] == 2) {
                $no_head_audited_users++;
            } else {
                $no_agent_audited_users++;
            }
            $agent[$user['level']]++;
            if ($start_time <= $user['time'] && $user['time'] <= $end_time) {
                $day_users++;
            }
        }

        //订单统计
        $no_audited_orders = 0;
        $yes_audited_orders = 0;
        $finished_orders = 0;
        $day_orders = 0;
        $total_order_money = 0;
        $orders = $this->order_model->field(['id,status,time,total_price'])->group('order_num')->select();
        foreach ($orders as $order) {
            if ($order['status'] == 1) {
                $no_audited_orders++;
            } else if ($order['status'] == 2) {
                $yes_audited_orders++;
            } else if ($order['status'] == 3) {
                $finished_orders++;
            }
            if ($start_time <= $order['time'] && $order['time'] <= $end_time) {
                $day_orders++;
            }
            if ($order['status'] > 0) {
                $total_order_money += $order['total_price'];
            }
        }

        $this->total_users = count($users);
        $this->yes_audited_users = $yes_audited_users;
        $this->no_agent_audited_users = $no_agent_audited_users;
        $this->no_head_audited_users = $no_head_audited_users;
        $this->day_users = $day_users;

        $this->level_name = $level_name;
        $this->agent = $agent;
        $this->level_count = count($level_name);
        

        $this->total_orders = count($orders);
        $this->yes_audited_orders = $yes_audited_orders;
        $this->no_audited_orders = $no_audited_orders;
        $this->finished_orders = $finished_orders;
        $this->day_orders = $day_orders;
        $this->total_order_money = $total_order_money;

        $this->display();
    }//end func index

    //数据分析
    public function detail()
    {
        //获取今日业绩金额，
        $condition = [
            'pid' => 0,
            'day' => date('Ymd'),
        ];
        $day_total = $this->order_count_model->where($condition)->sum('buy_money');

        //获取前一天的业绩金额
        $condition_before = [
            'pid' => 0,
            'day' => date("Ymd", strtotime("-1 day")),
        ];
        $day_before_total = $this->order_count_model->where($condition_before)->sum('buy_money');

        //统计本月业绩金额
        $condition_month = [
            'month' => date('Ym'),
            'pid' => '0',
            'uid' => '0',
            'day' => '0',
        ];
        $month_total = $this->order_count_model->where($condition_month)->sum('cost_money');
        //将数值变为货币形式
        $day_totals=number_format($day_total,2);
        $day_before_totals=number_format($day_before_total,2);
        $month_totals=number_format($month_total,2);
        $this->day_total = $day_totals;
        $this->day_before_total = $day_before_totals;
        $this->month_total = $month_totals;
        $this->display();
    }

    /**
     * 获取实时订单
     * add by qjq
     */
    public function get_new_order_ajax()
    {

        if (!IS_AJAX) {
            //    return FALSE;
        }

        $page_list_num = I('page_list_num');

        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        $page_info = [
            'page_num' => 1,
            'page_list_num' => $page_list_num,
        ];
        $condition = [
            'status' =>['gt',0],
        ];

        $info = $Order->get_order($page_info, $condition);

//        print_r($info);return;

        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info['list'],
        ];

        $this->ajaxReturn($result);
    }//end func get_new_order_ajax


    /**
     * 订单统计
     * add by qjq
     */
    public function get_order_count()
    {

        if (!IS_AJAX) {

            return FALSE;

        }

        $month = trim(I('month'));
        $day = trim(I('day'));
//        $month=201711;
//        $day=20171128;
        if (strlen($month) > 6) {
            $month = substr($month, 0, 6);
        }
        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $uid = array('gt', 0);
        $condition = [
            'pid' => 0,
            'uid' => $uid,
            'day' => $day,
        ];


        if (!empty($month)) {
            if (empty($day)) {
                $condition['month'] = $month;
                $day = 0;
                $condition['day'] = $day;
            } else {
                $condition['month'] = $month;
                $condition['day'] = $day;
            }

        } else {
            $result = [
                'code' => 2,
                'msg' => '月份参数错误！',
            ];
            $this->ajaxReturn($result);
        }

        $order_by = 'buy_money desc';
        $info = $Order->get_order_count([], $condition, $order_by);

        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];

        $this->ajaxReturn($result);
    }//end func get_order_count

    /**
     * 经销商等级分布
     * add by qjq
     */
    public function get_distributor_level()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $distributor = $this->distributor_model;

        $level_info = $distributor->group('level')->select();
        foreach ($level_info as $v) {
            if (!isset($ids[$v['level']])) {
                $ids[$v['level']] = $v['level'];
                $ids[$v['levname']] = $v['levname'];
                $resultname[] = $ids[$v['levname']];
                $result[] = $distributor->where('level=' . $ids[$v['level']])->count();
            }
        }
        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'count' => $result,
            'name' => $resultname,
        ];
        $this->ajaxReturn($result);
    }

    /**
     * 商品类别占比
     * add by qjq
     */
    public function get_order_type()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $month = trim(I('month'));
        $day = trim(I('day'));
        $page_list_num = I('page_list_num');

        import('Lib.Action.Order', 'App');

        $Order = new Order();
        if (strlen($month) > 6) {
            $month = substr($month, 0, 6);
        }
        $pid = array('gt', 0);
        $condition = [
            'pid' => $pid,
            'uid' => 0,
        ];
        if (!empty($month)) {
            if (empty($day)) {
                $condition['month'] = $month;
                $day = 0;
                $condition['day'] = $day;
            } else {
                $condition['month'] = $month;
                $condition['day'] = $day;
            }

        } else {
            $result = [
                'code' => 2,
                'msg' => '参数错误！',
            ];
            $this->ajaxReturn($result);
        }
//
//        if (!empty($month)) {
//            $condition['month'] = $month;
//            $day = 0;
//            if ($day == null) {
//                $day = date('Ymd');
//                $condition['day'] = $day;
//            } else {
//                $condition['day'] = $day;
//            }
//        } else {
//            $result = [
//                'code' => 2,
//                'msg' => '月份获取失败！',
//            ];
//            $this->ajaxReturn($result);
//        }

        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        $page_info = [
            'page_num' => 1,
            'page_list_num' => $page_list_num,
        ];

        $order_by = 'cost_num';

        $info = $Order->get_order_count($page_info, $condition, $order_by);
        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($result);
    }


    /**
     * 获取用户区域信息
     * add by feng
     * edit by qjq
     */
    public function get_distributor_area_info($type, $area, $start_time, $end_time)
    {

        if (empty($type)) {
            $return_result = [
                'code' => 2,
                'msg' => '参数不可为空',
            ];
            return $return_result;
        }


        if (!in_array($type, ['province', 'city', 'all', 'allCity'])) {
            $return_result = [
                'code' => 2,
                'msg' => '非可用的类型',
            ];
            return $return_result;
        }


        $distributor_model = $this->distributor_model;


        if ($start_time == '' && $end_time == '') {
            $condition = [];
        } else {
            $condition = [
                'time' => ['between', [$start_time, $end_time]]
            ];
        }
        if (!empty($area) && $type != 'all') {
            $condition[$type] = $area;
        }

//        if( !empty($city) ){
//            $condition['city'] = $city;
//        }

        $IS_TEST = C('IS_TEST');

        if ($IS_TEST) {
            $info = $distributor_model->where($condition)->select();
        } else {
            $info = $distributor_model->cache(true, 1800)->where($condition)->select();
        }


        if (empty($info)) {
            $return_result = [
                'code' => 3,
                'msg' => '没有用户信息',
            ];
            return $return_result;
        }


        $count = [

        ];

        foreach ($info as $v) {
            $v_province = $v['province'];
            $v_city = $v['city'];
            $v_county = $v['county'];

            if ($type == 'city') {
                $count[$v_county]++;
            } elseif ($type == 'province') {
                $count[$v_city]++;
            } elseif ($type == 'all') {
                $count[$v_province]++;
            } elseif ($type == 'allCity') {
                $count[$v_city]++;
            }

        }


        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'count' => $count,
            'type' => $type,
            'info' => $info,
        ];
        return $return_result;


    }//end func get_distributor_area_info


    /**
     * 获取代理区域信息
     * add by feng
     * edit by qjq
     */
    public function get_distributor_area_ajax()
    {

        if (!IS_AJAX) {
         return FALSE;
        }

        $type = trim(I('type'));//province,county,allCity
        $area = trim(I('area'));
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));
//
//           $type = 'all';
//        $area = 'cn';
//        $start_time=20171202;
//        $end_time=20171202;
        if($start_time != ''|| $end_time != ''){
            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_time);
        }


        $result = $this->get_distributor_area_info($type, $area, $start_time, $end_time);

//        header("Content-Type:text/html; charset=utf-8");
//        print_r($result);return;

        $this->ajaxReturn($result);
    }//end func get_distributor_area_info_ajax

    /**
     * 代理省级排行
     * add by feng
     * edit by qjq
     */
    public function get_province_ranking()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $level_num = C('LEVEL_NUM');
        $level_name = C('LEVEL_NAME');
        $start_time = trim(I('start_time'));
        $end_time = trim(I('end_time'));

//        $start_time=20171201;
//        $end_time=20171201;

                //开始时间戳和结束时间戳
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                $end_time = strtotime('+1 days -1 sec', $end_time);



        $province = array('北京', '天津', '上海', '重庆 ', '辽宁', '吉林', '黑龙江', '河北', '山西', '陕西', '山东', '安徽', '江苏', '浙江', '河南', '湖北', '湖南', '江西', '台湾', '福建', '云南', '海南', '四川', '贵州', '广东', '甘肃', '青海', '西藏', '新疆', '广西', '内蒙古', '宁夏');

        for ($i = 1; $i <= $level_num; $i++) {
            $count = [];
            $condition = [
                'time' => ['between', [$start_time, $end_time]],
                'level' => $i,
            ];
            foreach ($province as $v) {
                $condition['province'] = $v;
                $sum = $this->distributor_model->cache(true, 1800)->where($condition)->count();
                if (empty($sum)) {
                    $sum = 0;
                }
                $pro[] = $v;
                $count[] = $sum;
            }
            $list[$level_name[$i]] = $count;
        }


        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $list,
            'province' => $province,
        ];
        $this->ajaxReturn($return_result);

    }
    //-------*****数据分析首页开始*****--------

    /**
     * 经销商人数增长统计
     * add by qjq
     */
    public function count_distributor()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $month = trim(I('month'));
        $type = trim(I('type'));//横坐标的类型 day、week、month
        $is_show_level = trim(I('is_show_level'));
        // $month=201711;
        // $type='day';
        //$is_show_level=='';
        if (empty($month)) {
            $month = date('Ym');
        }

        if (empty($type)) {
            $return_result = [
                'code' => 2,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        $month = ($month . '01');
        $start_time = strtotime($month);
        $end_time = strtotime('+1 month -1 sec', $start_time);

        import('Lib.Action.Analysis', 'App');
        $Analysis = new Analysis();

        $info = $Analysis->get_add_distributor($start_time, $end_time, $type, $is_show_level);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($return_result);
    }

    /**
     * 每天的订单金额统计
     * add by qjq
     */
    public function count_order_money()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $month = trim(I('month'));
        if (empty($month)) {
            $month = date('Ym');
        }
        import('Lib.Action.Analysis', 'App');
        $Analysis = new Analysis();
        $info = $Analysis->get_order_money($month);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];

        $this->ajaxReturn($return_result);
        // $this->ajaxReturn($info);
    }
    //-------*****数据分析首页结束*****--------


    //-------------***代理统计分析开始***-----
    /**
     * 代理增长趋势
     * add by qjq
     */
    public function add_dis_trend()
    {
        if (!IS_AJAX) {
          return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $type = trim(I('type'));//横坐标的类型 day、week、month、hours
        $is_show_level = trim(I('is_show_level'));//是否显示每个级别
//        $start_time=20170905;
//        $end_time=20171230;
//        $type='month';
//        $is_show_level=1;

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 4,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_times = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_times);

        import('Lib.Action.Analysis', 'App');
        $Analysis = new Analysis();
        //查询条件,$start_time，$end_time传过来的直接是一个时间戳
        $info = $Analysis->get_add_distributor($start_time, $end_time, $type, $is_show_level);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];
        $this->ajaxReturn($return_result);
    }

    /**
     *代理订单分析趋势图
     * add by qjq
     */
    public function dis_order_anslysis()
    {
        if (!IS_AJAX) {
         return FALSE;
        }
      $start_time=trim(I('start_time'));//开始时间
      $end_time=trim(I('end_time'));//结束时间
      $dis_id=trim(I('dis_id'));//代理id
      $type=trim(I('type'));//横坐标的类型 day、month

//        $start_time = 20171111;
//        $end_time = 20171230;
//        $type = 'month';
//        $dis_id = 6;

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 4,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($dis_id)) {
            $return_result = [
                'code' => 5,
                'msg' => '用户id不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        //获取团队信息
        $uid = $this->distributor_model->where(array('recommendID' => $dis_id))->field('id,name')->select();
        foreach ($uid as $v) {
            $uids[] = $v['id'];
        }

            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_time);



        import('Lib.Action.Analysis', 'App');
        $Analysis = new Analysis();
        setLog(json_encode($end_time));
        //查询条件
        //个人
        $condition = [
            'uid' => $dis_id,
            'pid' => 0,
        ];
        $self_info = $Analysis->dis_order_analysis($start_time, $end_time, $type, $condition);

        //团队
        $condition_team = [
            'uid' => array('in', $uids),
            'pid' => 0,
        ];
        $team_info = $Analysis->dis_order_analysis($start_time, $end_time, $type, $condition_team);


        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'self_info' => $self_info,
            'team_info' => $team_info,

        ];
        $this->ajaxReturn($return_result);
    }


    /**
     *代理订单金额排行
     * add by qjq
     */
    public function dis_money_ranking()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $type = trim(I('type'));//统计的类型，按天数，按月份

//        $start_time=201701;
//        $end_time=201710;
//        $type='month';

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 4,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        //按天数统计
        if ($type == 'day') {
            $condition = [
                'pid' => 0,
                'uid' => array('gt', 0),
                'day' => ['between', [$start_time, $end_time]],
            ];

        } //按月份统计
        elseif ($type == 'month') {
            //格式转换
            if (strlen($start_time) > 6 || strlen($end_time) > 6) {
                $start_time = substr($start_time, 0, 6);
                $end_time = substr($end_time, 0, 6);
            }
            $condition = [
                'pid' => 0,
                'uid' => array('gt', 0),
                'day' => 0,
                'month' => ['between', [$start_time, $end_time]],
            ];
        }
        //查出所需的uid,并变为数组
        $uid = $this->order_count_model->where($condition)->field('uid')->group('uid')->select();
        foreach ($uid as $v) {
            $uids[] = $v['uid'];
        }
        $unique_arr = array_unique($uids);
        //查询uid的数据
        foreach ($unique_arr as $k => $v) {
            $condition['uid'] = $v;
            $count_info[] = $this->order_count_model->where($condition)->sum('buy_money');
            $dis_info[] = $this->distributor_model->where(array('id' => $v))->field('name')->find();
        }
        //合并数组
        $list = array_combine($count_info, $dis_info);
        //排序
        krsort($list);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'list' => $list,
        ];
        $this->ajaxReturn($return_result);
    }

    /**
     *代理订单类别占比(获取某个代理及其团队产品的类型的占比)
     * add by qjq
     */
    public function get_order_type_radio()
    {
        if (!IS_AJAX) {
         return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $dis_id = trim(I('dis_id'));//代理id
        $type=trim(I('type'));


//        $start_time=20171202;
//        $end_time=20171202;
//        $dis_id=6;
//        $type='day';

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($dis_id)) {
            $return_result = [
                'code' => 5,
                'msg' => '代理id不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        //获取团队信息
        $uid = $this->distributor_model->where(array('recommendID' => $dis_id))->field('id,name')->select();
        foreach ($uid as $v) {
            $uids[] = $v['id'];
        }

        if($type == 'day'){
            $condition = [
                'uid' => $dis_id,
                'pid' => array('gt', 0),
                'day' => ['between', [$start_time, $end_time]],
            ];

            $condition_team = [
                'uid' => array('in', $uids),
                'pid' => array('gt', 0),
                'day' => ['between', [$start_time, $end_time]],
            ];
        }elseif ($type == 'month'){
            $start=substr($start_time,0,6);
            $end=substr($end_time,0,6);
            $condition = [
                'uid' => $dis_id,
                'pid' => array('gt',0),
                'month' => ['between', [$start, $end]],
                'day'=>0,
            ];
            $condition_team = [
                'uid' => array('in', $uids),
                'pid' => array('gt', 0),
                'month' => ['between', [$start, $end]],
                'day'=>0,
            ];
        }
        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $page_info = [
            'page_num' => 1,
            'page_list_num' => 100000000,
        ];
        $myself_type_info = $Order->get_order_count($page_info, $condition);

        $team_type_info = $Order->get_order_count($page_info, $condition_team);

        //个人
        $myself_type = array();
        foreach ($myself_type_info as $key => $value) {
            foreach ($value as $v) {
                if (isset($myself_type[$v['pid']])) {
                    $myself_type[$v['pid']]['buy_money'] += $v['buy_money'];
                } else {
                    $myself_type[$v['pid']] = $v;
                }
            }
        }
        //团队
        $team_type = array();
        foreach ($team_type_info as $key => $value) {
            foreach ($value as $v) {
                if (isset($team_type[$v['pid']])) {
                    $team_type[$v['pid']]['buy_money'] += $v['buy_money'];
                } else {
                    $team_type[$v['pid']] = $v;
                }
            }
        }
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'myself_info' => $myself_type,
            'team_info' => $team_type
        ];
        $this->ajaxReturn($return_result);

    }

    //-------******充值分析模块****---------

    /**
     *代理充值趋势图
     * add by qjq
     */
    public function get_dis_apply()
    {
        if (!IS_AJAX) {
          return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $type = trim(I('type'));//横坐标的类型,day、month、hours
        $dis_id=trim(I('dis_id'));
//        $start_time = 20171204;
//        $end_time = 20171204;
//        $type = 'hours';
//        $dis_id=6;
        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($type)) {
            $return_result = [
                'code' => 4,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_time);

        $apply_type = [
            '1' => '申请充值',
            '2' => '后台充值',
            '3' => '订单返现',
            '4' =>  '下级下单',
            '5' =>  '返利返现',
        ];
        //横坐标为小时
        if($type == 'hours'){
            for ($j = 1; $j <= count($apply_type); $j++) {
                for ($i = $start_time; $i < $end_time; $i += 3600) {
                    $condition=[
                        'created'=>array('between',[$i, $i + 3600]),
                        'type'=>$j,
                        'uid'=>$dis_id
                    ];
                    $day[] = date('G', $i);
                    $totals = $this->money_recharge_log_model->where($condition)->sum('money');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    $count[] = $totals;
                }
                foreach ($apply_type as $kk => $vv) {
                    $list[$apply_type[$j]] = array_combine($day, $count);
                }
            }
        }
        //type横坐标为天数
        elseif ($type == 'day') {
            for ($j = 1; $j <= count($apply_type); $j++) {
                for ($i = $start_time; $i < $end_time; $i += 86400) {
                    $condition=[
                        'created'=>array('between',[$i, $i + 86400]),
                        'type'=>$j,
                        'uid'=>$dis_id
                    ];
                    $day[] = date('m/d', $i);
                    $totals = $this->money_recharge_log_model->where($condition)->sum('money');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    $count[] = $totals;
                }
                foreach ($apply_type as $kk => $vv) {
                    $list[$apply_type[$j]] = array_combine($day, $count);
                }
            }
        } //type横坐标为月份
        elseif ($type == 'month') {
            $start_time_one = date('Ym', $start_time);
            $end_time_one = date('Ym', $end_time);
            //最精确的开始时间和结束时间
            $start_time = strtotime($start_time_one . '01');
            $ends_time = strtotime($end_time_one . '01');
            $end_time = strtotime('+1 month', $ends_time);

            for ($j = 1; $j <= count($apply_type); $j++) {
                for ($i = $start_time; $i < $end_time; $i += (86400 * 31)) {
                    $start_time_two = date('Ym', $i);
                    $start= strtotime($start_time_two . '01');
                    $end = strtotime('+1 month -1 sec', $start);
                    $condition=[
                        'created'=>array('between',[$start, $end]),
                        'type'=>$j,
                        'uid'=>$dis_id
                    ];
                    $day[] = date('Ym', $start);
                    $totals = $this->money_recharge_log_model->where($condition)->sum('money');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    $count[] = $totals;

                }
                foreach ($apply_type as $kk => $vv) {
                    $list[$apply_type[$j]] = array_combine($day, $count);
                }
            }
        }
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'list' => $list,
        ];
        $this->ajaxReturn($return_result);
    }

    /**
     *代理充值排行
     * add by qjq
     */
    public  function get_apply_ranking(){
        if(!IS_AJAX){
             return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $type=trim(I('type'));//类型day、month
        $apply_type=trim(I('apply_type'));//充值的类型,可为空、1、2、3、4、5

//        $start_time = 20171130;
//        $end_time = 20171204;
//        $type='day';
//        $apply_type=2;

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        //判断类型为day还是month
        if($type == 'day'){
            $start_time = strtotime($start_time);
            $end_times = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_times);
        }elseif($type == 'month'){
            $start_time_one = substr($start_time, 0, 6);
            $end_time_one = substr($end_time, 0, 6);
            $start_time_two = ($start_time_one . '01');
            $end_time_two = ($end_time_one . '01');
            //正式的开始时间
            $start_time = strtotime($start_time_two);
            $end_time_three = strtotime($end_time_two);
            $end_time = strtotime('+1 month -1 sec', $end_time_three);
        }
        //判断是否有充值的类型的值
        if(empty($apply_type)){
            $condition=[
                'created'=>array('between',[$start_time, $end_time]),
            ];
        }else{
            $condition=[
                'type'=>$apply_type,
                'created'=>array('between',[$start_time, $end_time]),
            ];
        }

        import('Lib.Action.Funds', 'App');
        $Funds = new Funds();
        $page_info = [
            'page_num' => 1,
            'page_list_num' => 100000000,
        ];
        $myself_type_info=$Funds->get_money_recharge_log($page_info,$condition);

        $info= array();
        foreach ($myself_type_info as $key => $value) {
            foreach ($value as $v){
                if (isset($info[$v['uid']])) {
                    $info[$v['uid']]['money'] += $v['money'];
                }
                else {
                    $info[$v['uid']] = $v;
                }
            }
        }
        //排序，金额大到小
        $list=array();
        foreach($info as $v){
            $list[] = $v['money'];
        }
        array_multisort($list, SORT_DESC, $info);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'list' =>  $info,
        ];
        $this->ajaxReturn($return_result);
    }
//-----------***代理统计分析结束***---------



    //-----------*****订单统计分析*****---------

    /**
     *订单产品趋势图
     * add by qjq
     */
    public function get_order_sale_analysis()
    {
        if (!IS_AJAX) {
        return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $type = trim(I('type'));//横坐标的类型,day
        
//         $start_time=20171212;
//         $end_time=20171214;
//         $type='day';

        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($type)) {
            $return_result = [
                'code' => 4,
                'msg' => '类型不能为空',
            ];
            $this->ajaxReturn($return_result);
        }



            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_time);

        import('Lib.Action.Analysis', 'App');
        $Analysis = new Analysis();
        //查询条件
        $info_self = $Analysis->get_order_count_analysis($start_time, $end_time, $type);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'list' => $info_self,
        ];
        $this->ajaxReturn($return_result);
    }


    /**
     * 获取全部产品名称
     * add by qjq
     */
    public function get_all_templet(){
        $templet_info=$this->templet_model->field('id,name')->select();
        $this->ajaxReturn($templet_info);
    }

    /**
     * 产品销量和销售额排行榜
     * add by qjq
     */
    public function get_sale_templet(){
        if(!IS_AJAX){
         return FALSE;
        }
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $templet_id = trim(I('templet_id'));//产品id
        $type=trim(I('type')); //横坐标的类型

//         $start_time=20171102;
//         $end_time=20171202;
//         $type='day';
//        $templet_id=18;
        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

            //开始时间戳和结束时间戳
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $end_time = strtotime('+1 days -1 sec', $end_time);



        if(empty($day)&& empty($sale_money) && empty($sale_num)) {

            if ($type == 'day') {
                if (empty($templet_id)) {
                    $condition = [
                        'uid' => array('gt', 0),
                        'pid' => 0,
                    ];
                } else {
                    $condition = [
                        'uid' => array('gt', 0),
                        'pid' => $templet_id,
                    ];
                }

                for ($i = $start_time; $i < $end_time; $i += 86400) {
                    $con_s = date('Ymd', $i);
                    $condition['day'] = $con_s;
                    $day[] = date('m/d', $i);
                    $totals = $this->order_count_model->where($condition)->sum('buy_money');
                    $num = $this->order_count_model->where($condition)->sum('buy_num');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    if (empty($num)) {
                        $num = 0;
                    }
                    $sale_money[] = $totals;
                    $sale_num[] = $num;
                    S('day', $day, 600);
                    S('sale_money', $sale_money, 600);
                    S('sale_num', $sale_num, 600);

                }
            } elseif ($type == 'month') {
                if (empty($templet_id)) {
                    $condition = [
                        'uid' => array('gt', 0),
                        'pid' => 0,
                        'day' => 0,
                    ];
                } else {
                    $condition = [
                        'uid' => array('gt', 0),
                        'pid' => $templet_id,
                        'day' => 0,
                    ];
                }
                for ($i = $start_time; $i < $end_time; $i += (86400 * 31)) {
                    $start_time_one = date('Ym', $i);
                    $condition['month'] = $start_time_one;
                    $day[] = date('Y年n月', $i);
                    $totals = $this->order_count_model->where($condition)->sum('buy_money');
                    $num = $this->order_count_model->where($condition)->sum('buy_num');

                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    if (empty($num)) {
                        $num = 0;
                    }
                    $sale_money[] = $totals;
                    $sale_num[] = $num;
                    S('day', $day, 600);
                    S('sale_money', $sale_money, 600);
                    S('sale_num', $sale_num, 600);
                }
            }

        }
        $day=S('day');
        $sale_money=S('sale_money');
        $sale_num=S('sale_num');

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'day' => $day,
            'sale_money'=>$sale_money,
            'sale_num'=>$sale_num,
        ];
        $this->ajaxReturn($return_result);
    }

    /**
     * 代理发货地区
     * add by qjq
     */
    public function get_sale_area(){
        $start_time = trim(I('start_time'));//开始时间
        $end_time = trim(I('end_time'));//结束时间
        $templet_id = trim(I('templet_id'));//产品id
        $type=trim(I('type'));//省 province、市 city 、区 county
        // $start_time=20171212;
        // $end_time=20171212;
        // $templet_id=20;
        // $type='province';
        if (empty($start_time)) {
            $return_result = [
                'code' => 2,
                'msg' => '开始时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($end_time)) {
            $return_result = [
                'code' => 3,
                'msg' => '结束时间不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        //将传过来的时间变成时间戳
        //开始时间戳和结束时间戳
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        $end_time = strtotime('+1 days -1 sec', $end_time);
        $condition=[
            'time'=>['between', [$start_time, $end_time]],
            'pid'=>$templet_id,
            'status'=>array('gt',1),
        ];
        $other['is_group'] = 1;

        import('Lib.Action.Analysis', 'App');
        $Analysis=new Analysis();
        $page_info = [
            'page_num' => 1,
            'page_list_num' => 100000000,
        ];
        $info=$Analysis->get_order_analysis($page_info,$condition,$other,$type);

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info'=>$info
        ];
        $this->ajaxReturn($return_result);
    }


}

?>