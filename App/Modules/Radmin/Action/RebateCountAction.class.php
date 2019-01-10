<?php

/**
 *  经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class RebateCountAction extends Action {
    private $rebate_obj;
    
    public function _initialize() {
        import('Lib.Action.NewRebate','App');
        $this->rebate_obj = new NewRebate();
    }

    //生成团队奖
    public function run() {
        $start_time = time();
        echo 'start time:'.date('Y-m-d H:i:s', $start_time).'-';
        
        $this->rebate_obj->create_team_rebate();
        
        $end_time = time();
        echo 'end time:'.date('Y-m-d H:i:s', $end_time).'-';
        $total_time = $end_time - $start_time;
        echo 'total time:'.$total_time;
    }
    
    //统计上个月的返利(月初统计上个月)
    public function pre_run() {
        $month = get_month(1);
        $start_time = time();
        echo 'start time:'.date('Y-m-d H:i:s', $start_time).'-';
        
        $this->rebate_obj->create_team_rebate('', $month);
        
        $end_time = time();
        echo 'end time:'.date('Y-m-d H:i:s', $end_time).'-';
        $total_time = $end_time - $start_time;
        echo 'total time:'.$total_time;
    }
}

?>