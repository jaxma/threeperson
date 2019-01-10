<?php

/**
 *  经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class UpgradecountAction extends Action {
    private $upgrade_obj;
    
    public function _initialize() {
        import('Lib.Action.Upgrade','App');
        $this->upgrade_obj = new Upgrade();
    }

    //生成升级申请
    public function run() {
        $start_time = time();
        echo 'start time:'.date('Y-m-d H:i:s', $start_time).'-';
        
        $this->upgrade_obj->run();
        
        $end_time = time();
        echo 'end time:'.date('Y-m-d H:i:s', $end_time).'-';
        $total_time = $end_time - $start_time;
        echo 'total time:'.$total_time;
    }

}

?>