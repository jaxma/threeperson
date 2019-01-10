<?php
//后台图形统计代码
header("Content-Type: text/html; charset=utf-8");

class Analysis
{

    private $distributor_model;
    private $order_model;
    private $order_count_model;
    private $templet_model;

    public function __construct()
    {
        $this->distributor_model = M('distributor');
        $this->order_model = M('order');
        $this->order_count_model = M('order_count');
        $this->templet_model = M('templet');

    }

    //获取经销商每天增长的人数
    public function get_add_distributor($start_time, $end_time, $type, $is_show_level)
    {

        $level = C('LEVEL_NUM');
        $level_name = C('LEVEL_NAME');
        $k = 1;
        //如果不显示每个等级的人数
        if ($is_show_level == '') {
            //type为小时
            if ($type == 'hours') {
                for ($i = $start_time; $i < $end_time; $i += 3600) {
                    $condition['time'] = ['between', [$i, $i + 3600]];
                    $day[] = date('G', $i);
                    $count[] = $this->distributor_model->where($condition)->count('id');
                }
            } //type为天数
            elseif ($type == 'day') {
                for ($i = $start_time; $i < $end_time; $i += 86400) {
                    $condition['time'] = ['between', [$i, $i + 86400]];
                    $day[] = date('d', $i);
                    $count[] = $this->distributor_model->where($condition)->count('id');
                }
            } //type为周
            elseif ($type == 'week') {
                for ($i = $start_time; $i < $end_time; $i += (86400 * 7)) {
                    $condition['time'] = ['between', [$i, $i + (86400 * 7)]];
                    $day[] = '第' . $k++ . '周';
                    $count[] = $this->distributor_model->where($condition)->count('id');
                }
            }
        } //如果显示每个等级的人数
        elseif ($is_show_level == 1) {
            //type为小时
            if ($type == 'hours') {
                for ($j = 1; $j <= $level; $j++) {
                    for ($i = $start_time; $i < $end_time; $i += 3600) {
                        $condition['time'] = ['between', [$i, $i + 3600]];
                        $condition['level'] = $j;
                        $day[] = date('G', $i);
                        $count[] = $this->distributor_model->where($condition)->count('id');
                        foreach ($level_name as $kk => $vv) {
                            $list[$level_name[$j]] = array_combine($day, $count);
                        }
                    }

                }
            } //type为天数
            elseif ($type == 'day') {
                for ($j = 1; $j <= $level; $j++) {
                    for ($i = $start_time; $i < $end_time; $i += 86400) {
                        $condition['time'] = ['between', [$i, $i + 86400]];
                        $condition['level'] = $j;
                        $day[] = date('m/d', $i);
                        $count[] = $this->distributor_model->where($condition)->count('id');
                    }
                    $list[$level_name[$j]] = array_combine($day, $count);

                }
            } //type为周
            elseif ($type == 'week') {
                for ($j = 1; $j <= $level; $j++) {
                    for ($i = $start_time; $i < $end_time; $i += (86400 * 7)) {
                        $condition['time'] = ['between', [$i, $i + (86400 * 7)]];
                        $condition['level'] = $j;
                        $day[] = '第' . $k++ . '周';
                        $count[] = $this->distributor_model->where($condition)->count('id');
                    }
                    $list[$level_name[$j]] = array_combine($day, $count);
                }
            } //type为月份(暂按30天计算，最好暂时不要使用，以免有数据统计错误)
            elseif ($type == 'month') {
                for ($j = 1; $j <= $level; $j++) {
                    for ($i = $start_time; $i < $end_time; $i += (86400 * 31)) {
                        $start_time_one = date('Ym', $i);
                        $start_time_two = ($start_time_one . '01');
                        $statr = strtotime($start_time_two);
                        $end = strtotime('+1 month -1 sec', $statr);
                        $condition['time'] = ['between', [$statr, $end]];
                        $condition['level']=$j;
                        $day[] = date('Y年n月', $i);
                        $count[] = $this->distributor_model->where($condition)->count('id');
                    }
                    $list[$level_name[$j]] = array_combine($day, $count);

                }
            }
        }


        if ($is_show_level == 1) {
            $return_result = array(
                'list' => $list,
            );
        } else {
            $return_result = array(
                'day' => $day,
                'count' => $count,
            );
        }
        return $return_result;
    }


    //获取每天订单的金额
    public function get_order_money($month)
    {
        $month = ($month . '01');
        $start_time = strtotime($month);
        $end_time = strtotime('+1 month -1 sec', $start_time);
        for ($i = $start_time; $i < $end_time; $i += 86400) {
            $condition['time'] = ['between', [$i, $i + 86400]];
            $condition['status'] =['gt',0];
            $day[] = date('d', $i);
            $total = $this->order_model->where($condition)->count('id');
            $totals = 0;
            if ($total > 0) {
                $sum = $this->order_model->where($condition)->group('order_num')->select();
                foreach ($sum as $v) {
                    $totals += $v['total_price'];
                }
            }
            if (empty($total)) {
                $totals = '0.00';
            }
            $count[] = $totals;
        }
        $return_result = array(
            'day' => $day,
            'count' => $count,
        );
        return $return_result;
    }


    //代理订单分析趋势图
    public function dis_order_analysis($start_time, $end_time, $type, $condition = array())
    {

          if ($type == 'day') {
            for ($i = $start_time; $i < $end_time; $i += 86400) {
                $con_s = date('Ymd', $i);
                $condition['day'] = $con_s;
                $day[] = date('m/d', $i);
                $totals = $this->order_count_model->where($condition)->sum('buy_money');
                if (empty($totals)) {
                    $totals = '0.00';
                }
                $count[] = $totals;
            }
        } //type为月份
        elseif ($type == 'month') {
            for ($i = $start_time; $i < $end_time; $i += (86400 * 31)) {
                $start_time_one = date('Ym', $i);
                $start= strtotime($start_time_one . '01');
                $condition['month'] = $start_time_one;
                $condition['day'] = array('gt', 0);
                $day[] = date('Y年n月', $start);
                $totals = $this->order_count_model->where($condition)->sum('buy_money');
                if (empty($totals)) {
                    $totals = '0.00';
                }
                $count[] = $totals;
            }
        }


        $return_result = array(
            'day' => $day,
            'count' => $count,
        );
        return $return_result;
    }

    //获取订单统计记录
    public function get_order_count_analysis($start_time, $end_time, $type)
    {
        $list = array();
        $temp_info = $this->templet_model->field('id,name')->limit(30)->select();
        $arr_temp = array_reduce($temp_info, function (&$arr_temp, $v) {
            $arr_temp[$v['id']] = $v['name'];
            return $arr_temp;
        });
        $key = array_keys($arr_temp);
        $value = array_values($arr_temp);

        if(empty($list)){
         if($type == 'day'){
            for ($j = 0; $j < count($key); $j++) {
                for ($i = $start_time; $i < $end_time; $i += 86400) {
                    $con_s = date('Ymd', $i);
                    $condition=[
                        'uid'=>array('gt',0),
                        'day'=>$con_s,
                        'pid'=>$key[$j],
                    ];
                    $day[] = date('m/d', $i);
                    $totals = $this->order_count_model->where($condition)->sum('buy_money');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    $counts[] = $totals;
                }

                $list[$value[$j]] = array_combine($day, $counts);
                S('list', $list, 600);
            }
        }
        //type为month，即横坐标为月份
        elseif ($type == 'month'){
            for ($j = 0; $j < count($key); $j++) {
                for ($i = $start_time; $i < $end_time; $i += (86400 * 31)) {
                    $con_s = date('Ym', $i);
                    $condition=[
                        'uid'=>array('gt',0),
                        'day'=>0,
                        'month'=>$con_s,
                        'pid'=>$key[$j],
                    ];
                    $day[] = date('Y年n月', $i);
                    $totals = $this->order_count_model->where($condition)->sum('buy_money');
                    if (empty($totals)) {
                        $totals = '0.00';
                    }
                    $counts[] = $totals;
                }

                $list[$value[$j]] = array_combine($day, $counts);
                S('list', $list, 600);
            }
        }
      }
        $list=S('list');
        $return_result = array(
            'list' => $list,
        );

        return $return_result;
    }//end func get_order_count_analysis
    //获取订单记录
    public function get_order_analysis($page_info=array(),$condition=array(),$other=array(),$type){


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $level_num = C('LEVEL_NAME');
       // $status_name = $this->status_name;

        $is_group = isset($other['is_group'])?$other['is_group']:0;

        $count = $this->order_model->where($condition)->count('distinct order_num');

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;
                $list = $this->order_model->where($condition)->order('time desc')->page($page_con)->select();

            }
            else{
                $list = $this->order_model->where($condition)->order('time desc')->select();
            }


            //-----整理添加相应其它表的信息-----
            $uids = array();
            $pids = [];

            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];

                if( !isset($uids[$v_uid]) ){
                    $uids[$v_uid] = $v_uid;
                }
                if( !isset($pids[$v_pid]) ){
                    $pids[$v_pid] = $v_pid;
                }

            }

            array_values($uids);
            array_unique($uids);
            $condition_dis = array(
                'id'    =>  array('in',$uids),
            );

            $field = 'id,name,levname,headimgurl';

            $dis_info = $this->distributor_model->field($field)->where($condition_dis)->select();

            $dis_key_info[0]['name'] = '总部';
            $dis_key_info['0']['levname'] = '总部';
            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_uid = $v_dis['id'];

                $dis_key_info[$v_dis_uid] = $v_dis;

            }


            array_values($pids);
            array_unique($pids);
            $condition_temp = [
                'id'    =>  ['in',$pids],
            ];

            $templet_info = $this->templet_model->where($condition_temp)->select();

            $templet_key_info = [];
            foreach( $templet_info as $v_temp ){
                $v_temp_id = $v_temp['id'];
                $templet_key_info[$v_temp_id] = $v_temp;

            }

            $list_group = [];

            foreach( $list as $k => $v ){
                $v_uid = $v['user_id'];
                $v_pid = $v['p_id'];
                $v_type = $v[$type];
                $v_u_level = $v['u_level'];
                $v_p_level = $v['p_level'];
                $v_updated = $v['updated'];
                $v_status = $v['status'];


//                $list[$k]['u_info'] = $dis_key_info[$v_uid];
//                $list[$k]['p_info'] = $dis_key_info[$v_pid];
                $list[$k]['u_name'] = $dis_key_info[$v_uid]['name'];
//                $list[$k]['p_name'] = $dis_key_info[$v_pid]['name'];
                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];

               // $list[$k]['status_name'] = $status_name[$v_status];
                $list[$k]['u_levname'] = $dis_key_info[$v_uid]['levname'];
                $list[$k]['p_levname'] = $dis_key_info[$v_pid]['levname'];
                $list[$k]['templet'] = $templet_key_info[$v_pid];
                $list[$k]['dis_info'] = $dis_key_info[$v_uid];
                $list[$k]['updated_format'] = date('Y-m-d H:i',$v_updated);

                $list_group[$v_type][] = $list[$k];
            }
            //-----end 整理添加相应其它表的信息-----


            if( $is_group ){
                $list = $list_group;
            }
        }



        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }


        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_order

}