<?php

/**
 *  topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class RedisridAction extends Action {
    private $newredis_obj;
    private $order_obj;
    private $admin_obj;

    public function _initialize(){
        import('Lib.Action.Newredis','App');
        import('Lib.Action.Order','App');
        import('Lib.Action.Admin','App');

        $this->newredis_obj=new Newredis();
        $this->order_obj=new Order();
        $this->admin_obj=new Admin();

    }

    //释放订单队列
    public function test_order_redis(){


        //连接redis
//        setLog('系统开始自动执行队列订单的写入','redis');
        $key_release_redis='key_release_redis';
        $con=$this->newredis_obj->connect();
//        setLog('系统是否连接成功'.json_encode($con),'redis');
        $res_key_exists=$this->newredis_obj->key_exists($key_release_redis);
//        setLog('判断key是否存在'.json_encode($res_key_exists),'redis');
        if(!$this->newredis_obj->key_exists($key_release_redis)){
//            setLog(json_encode('进入加锁环节'),'redis');
            $this->newredis_obj->set($key_release_redis,'1',0);

            $this->order_obj->release_redis_order();

            //订单释放完成，删除key,让下一批订单继续开始执行
            $this->newredis_obj->delete($key_release_redis);
        }

    }

//    //释放自动审核订单的队列
//    public function release_auto_audit_order(){
//        $key_auto_audit='key_auto_audit';
//        $key_release_order='key_release_order';//（写入队列的key）
//        $linux_release_order = 'linux_release_order'; //(linux执行脚本释放队列的key)
//        $key_use_aid='key_use_aid';    //总后台操作自动审核订单的用户
//        //判断写入队列是否完成
//        if($this->newredis_obj->key_exists($key_release_order)){
//            return false;
//        }
//        //判断队列的key存不存在
//        if(!$this->newredis_obj->key_exists($key_auto_audit)){
//            return false;
//        }
//       //判断key里面是否有值
//        $get_value=$this->newredis_obj->get_lrange($key_auto_audit,0,-1);
//        if(empty($get_value)){
//            return false;
//        }
//        //获取操作者
//        $use_aid=$this->newredis_obj->get($key_use_aid);
//        //获取长度
//        $get_size=$this->newredis_obj->size($key_auto_audit);
//        //循环释放列表的值
//        for($i=$get_size; $i >0 ;$i--){
//            $order_nums=$this->newredis_obj->pop($key_auto_audit);
//            $res=$this->order_obj->radmin_audit([$order_nums]);
//            //添加日志
//            if($res['code'] == 1){
//                $log='订单自动审核:'.$res['msg'];
//                $this->admin_obj->add_active_log($use_aid,$log);
//            }
//            setLog('订单号：'.$order_nums.',自动审核的结果为'.json_encode($res),'release_auto_audit_order');
//        }
//        //释放队列订单完成，解锁
//        if($this->newredis_obj->key_exists($linux_release_order)){
//            $this->newredis_obj->delete($linux_release_order);
//        }
//        if($this->newredis_obj->key_exists($key_use_aid)){
//            $this->newredis_obj->delete($key_use_aid);
//        }
//    }
}

?>
