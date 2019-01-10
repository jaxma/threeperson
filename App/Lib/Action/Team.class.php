<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of team
 *团队相关函数
 * @author ilyzbs
 */
class Team {
    private $distributor_model;
    private $count_model;
    private $order_count_model;
//    private $funds_obj;
//    private $order_obj;
    
    private $default_team;
    
    public $money_count_way = 0;//按虚拟币统计
    public $order_count_way = 1;//按订单金额统计
    public $order_num_count_way = 2;//按订单数量统计
    
//    private $is_parent_audit;//虚拟币是否总部充值
//    private $is_top_supply;//是否向上级拿货
    
    public function __construct() {
        $this->distributor_model = M('distributor');
        $this->order_count_model = M('order_count');
        $this->count_model = M('money_month_count');
        $this->default_team = C('DEFAULT_TEAM');
        
//        import('Lib.Action.Funds','App');
//        import('Lib.Action.Order','App');
//        $this->funds_obj = new Funds();
//        $this->order_obj = new Order();

        //判断虚拟币是否由上级审核
//        $this->is_parent_audit = $this->funds_obj->is_parent_audit;
//        //判断订单是否由上级审核
//        $this->is_top_supply = $this->order_obj->is_top_supply;
    }

        /**
     * 获取团队id集合
     * @param int $uid
     * @return type
     */
//     public function get_team_ids($uid,$default_team='') {
//         if( empty($default_team) ){
//             $default_team = $this->default_team;
//         }
        
//         $path = $this->distributor_model->where(['id' => $uid])->getField($default_team);
//         $path .= '-'.$uid;
//         $where['is_lowest'] = 1;
//         $where[$default_team] = $path;
        
//         $map['is_lowest'] = 1;
//         $map[$default_team] = ['like', "$path-%"];
//         $a['_complex'] = $where;
//         $condition[] = $a;
//         $condition['_logic'] = 'OR';
//         $condition['_complex'] = $map;
//         $users = $this->distributor_model->where($condition)->field("id,$default_team")->select();
// //        return $this->distributor_model->getLastSql();
//         if(empty($users)) {
//             return [$uid];
//         }
//         $ids = [];
//         foreach ($users as $user) {
//             $arr = explode('-', $user[$default_team]);
//             $arr[] = $user['id'];
//             $ids = array_merge($ids,$arr);
//         }
//         $ids = array_unique($ids);
        
//         //排除上级id
//         $rec_str = $this->distributor_model->where(['id' => $uid])->getField($default_team);
//         $rec_arr = explode('-', $rec_str);
//         foreach ($ids as $k => $id) {
//             if(in_array($id, $rec_arr)) {
//                 unset($ids[$k]);
//             }
//         }
//         return $ids;
//     }

    /**
     * 简单对称加密算法之加密

     * @param int $uid 要查找团队的代理id
     * @param array $users 保存在缓存文件的最底层的is_lowest=1的代理$default_team字段的数据
     * @param int $default_team path/rec_path字段
     * @return array 代理id集合(一维数组)
     */
    public function get_team_ids($uid, $users, $default_team='') {
        if( empty($default_team) ){
            $default_team = $this->default_team;
        }

        if(empty($users)) {
            return [$uid];
        }
        $my = $this->distributor_model->where(['id' => $uid])->field("id,$default_team,is_lowest")->find();
        //判断到是最低级并且没有下级直接返回
//        $count = $this->distributor_model->where(['recommendID' => $my['id']])->count('id');
//        if ($my['is_lowest'] == 1 && $count == 0) {
//            return [$uid];
//        }
        if ($my['is_lowest']) {
            return [$uid];
        }
        $rec_path = $my[$default_team];
        $rec_str = $rec_path;
        $rec_path .= '-'.$uid.'-';//用于模糊匹配
        $ids = [];//结果团队id集合
        $low_ids = [];//最底层代理id集合
        
        //第一次模糊匹配
        $result = ""; //正则匹配出的id字符串集合
        $match_path = [];//正则搜索结果
        $rule  = "/($rec_path.*)/";
        foreach ($users as $user) {
            preg_match($rule,$user[$default_team],$result);
            if ($result) {
                $match_path[] = $result[0];
                $low_ids[] = $user['id'];
            }
        }
        $match_path = array_unique($match_path);
        foreach ($match_path as $path_str) {
            $arr = explode('-', $path_str);
            foreach ($arr as $v) {
                $ids[$v] = $v;
            }
        }
        //第二次精确匹配(其实就是找出直接推荐的并且is_lowest=1的代理)
        $real_path = $rec_str.'-'.$uid;//用于精确匹配
        foreach ($users as $user) {
            if ($user[$default_team] == $real_path) {
                $low_ids[] = $user['id'];
            }
        }
        
        $ids = array_merge($ids,$low_ids);
        $ids = array_unique($ids);
        //排除上级id
        $rec_arr = explode('-', $rec_str);
        foreach ($ids as $k => $id) {
            if(in_array($id, $rec_arr)) {
                unset($ids[$k]);
            }
        }
        //自己的id也要在团队里面
        if (!in_array($uid, $ids)) {
            $ids[] = $uid;
        }
//        var_dump($ids);die;
        return $ids;
    }
    
    /**
     * 获取团队/个人业绩
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money($uids, $month = "", $day = "") {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        if ($month) {
            $where['month'] = $month;
        }
        if ($day) {
            $where['day'] = $day;
        }

        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            if($count_way == $this->order_count_way){
                //统计订单金额
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                $money = $this->order_count_model->where($where)->sum('buy_money');
            } else if($count_way == $this->order_num_count_way){
                //统计订单数量
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                $money = $this->order_count_model->where($where)->sum('buy_num');
            } else {
                //虚拟币
                $money = $this->count_model->where($where)->sum('money');
            }
            return empty($money) ? 0.00 : $money;
        } else {
            $where['uid'] = $uids;

            //个人业绩
            if($count_way == $this->order_count_way){
                //统计订单金额
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                $money = $this->order_count_model->where($where)->sum('buy_money');

            } else if($count_way == $this->order_num_count_way){
                //统计订单数量
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                $money = $this->order_count_model->where($where)->sum('buy_num');
            }
            else{
                //虚拟币
                $money = $this->count_model->where($where)->sum('money');
            }
            
            return empty($money) ? 0.00 : $money;
        }
        return 0.00;
    }
    
    
    /**
     * 2017-11-3废弃
     * 获取团队/个人业绩
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money_old($uids, $month = "", $day = "") {
        if ($month) {
            $where['month'] = $month;
        }
         if ($day) {
            $where['day'] = $day;
        }
        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            $money = $this->count_model->where($where)->sum('money');
            return empty($money) ? 0.00 : $money;
        } else {
            //个人业绩
            $where['uid'] = $uids;
            $money = $this->count_model->where($where)->sum('money');
            return empty($money) ? 0.00 : $money;
        }
        return 0.00;
    }
    
    
     /**
     * 获取团队/个人业绩详细数据
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money_detail($uids, $month = "", $day = "") {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        if ($month) {
            $where['month'] = $month;
        }
         if ($day) {
            $where['day'] = $day;
        }
        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            if(in_array($count_way, [$this->order_count_way, $this->order_num_count_way])){
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                return $this->order_count_model->where($where)->select();
            }
            else{
                return $this->count_model->where($where)->select();
            }
        } else {
            //个人业绩
            $where['uid'] = $uids;
            if(in_array($count_way, [$this->order_count_way, $this->order_num_count_way])){
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                return $this->order_count_model->where($where)->select();
            }
            else{
                return $this->count_model->where($where)->select();
            }
        }
        return null;
    }
    
    //本月业绩(用于图形统计数据)
    public function get_graph_money($uid, $month = "") {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        $money = [];
        if (!$month) {
            $month = date('Ym');
        }
        
        $where['uid'] = $uid;
        $where['month'] = $month;
        
        
        if($count_way == $this->order_count_way){
            $money_key = 'buy_money';
            
            $where['pid']   =   0;
            $count = $this->order_count_model->where($where)->select();
        } else if($count_way == $this->order_num_count_way){
            $money_key = 'buy_num';
            
            $where['pid']   =   0;
            $count = $this->order_count_model->where($where)->select();
        }
        else{
            $money_key = 'money';
            $count = $this->count_model->where($where)->select();
        }
        
        foreach ($count as $v) {
            if (isset($money[$v['day']])) {
                $money[$v['day']] += $v[$money_key];
            } else {
                $money[$v['day']] = $v[$money_key];
            }
        }
        for ($i=1;$i<32;$i++) {
           if ($i<10) {
               $day = $month.'0'.$i;
           } else {
               $day = $month.$i;
           }
           if (!isset($money[$day])) {
               $money[$day] = '0.00';
           }
        }
        ksort($money);
        $data = array_values($money);
        return $data;
    }

    //银行卡
    public function get_distributor_bank($page_info=array(),$condition=array()){


        $distributor_obj = M('distributor_bank');
        import('ORG.Util.Page');


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $distributor_obj->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $distributor_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $distributor_obj->where($condition)->order('id desc')->select();
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
    }

    /**
     * 数据根据pid排序
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     * add by qjq
     */
    public function sortt($data,$pid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sortt($data,$v['id'],$level+1);
            }
        }
        return $arr;

    }


    /**
     * 获取代理等级中文名字和人数信息
     * @param array $condition
     * @param array $other
     * @return array
     * add by qjq
     */

    public function get_dis($condition=array(),$other=array()){
        $is_group = isset($other['is_group'])?$other['is_group']:0;
        $list = $this->distributor_model->where($condition)->group('level')->order('level')->select();

        foreach ($list as $v) {
            if (!isset($ids[$v['level']])) {
                $ids[$v['level']] = $v['level'];
                $ids[$v['levname']] = $v['levname'];
                $resultname[]=$ids[$v['levname']];
                $condition['level']=$ids[$v['level']];
                $level_num[] =  $this->distributor_model->where($condition)->count();
            }
        }


        foreach( $list as $k => $v ){
            $lev_num = array_combine($resultname, $level_num);
            $levname =  $v['levname'];
            $level[]=    $v['level'];
            $list_group[$levname][$v['level']] = $level_num[$k];

        }

        if( $is_group){
            $list = $list_group;
        }
        //-----end 整理添加相应其它表的信息-----

        $return_result = array(
            'list'  =>  $list,
        );

        return $return_result;
    }//end func get_dis

    /**
     * 获取等级下面各代理信息
     * @param array $page_info
     * @param array $condition
     * @return array
     * add by qjq
     */
    public function get_distributor_info($page_info=array(),$condition=array()){

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        //$level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        $level_num_max = C('LEVEL_NUM_MAX');

        $count = $this->distributor_model->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->distributor_model->where($condition)->order('convert(name using gb2312) asc')->page($page_con)->select();

            }
            else{
                $list = $this->distributor_model->where($condition)->order('convert(name using gb2312) asc')->select();

            }
            $ids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['id'];
                if( !isset($ids[$v_uid]) ){
                    $ids[$v_uid] = $v_uid;
                }
            }
            array_values($ids);
            array_unique($ids);
            $condition_dis = array(
                'id'    =>  array('in',$ids),
            );
            $field = 'id,recommendID,level,levname,pid';

            $dis_info = $this->distributor_model->field($field)->where($condition_dis)->select();

            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_id = $v_dis['id'];

                $condition1=[
                    'recommendID' => $v_dis_id,
//                    'audited'=>1
                ];
                $condition2=[
                    'pid' => $v_dis_id,
//                    'audited'=>1
                ];

                $dis_recommendID_info=$this->distributor_model->where($condition1)->count();
                $dis_pid_info=$this->distributor_model->where($condition2)->count();

                $dis_key1_info[$v_dis_id] = $dis_recommendID_info;
                $dis_key2_info[$v_dis_id] =$dis_pid_info;

            }

            foreach( $list as $k => $v ){
                $v_uid = $v['id'];
                $list[$k]['dis_info'] = $dis_key1_info[$v_uid];
                $list[$k]['dis_recommendID_count'] = $dis_key1_info[$v_uid];
                $list[$k]['dis_pid_count'] = $dis_key2_info[$v_uid];
                $v_time = $v['time'];
                $list[$k]['time_format'] = date('Y-m-d H:i',$v_time);
            }

            //-----end 整理添加相应其它表的信息-----
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
    }//end func get_distributor


    /**
     * 经销商树状图
     * @param $page_info
     * @param array $condition
     * @return array
     * add by qjq
     */
    public function dis_tree($page_info,$condition=array(),$type){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $YM_DOMAIN = C('YM_DOMAIN');
        $level_num = C('LEVEL_NUM');
        $level_name = C('LEVEL_NAME');

        $count = $this->distributor_model->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list =$this->distributor_model->where($condition)->order('time desc')->page($page_con)->select();
            }
            else{
                $list =$this->distributor_model->where($condition)->order('time desc')->select();
            }
            if($type == 'pid'){
                foreach ($list as $k => $v) {
                    $list[$k]['count'] =$this->distributor_model->where(array('pid' => $list[$k]['id'],'audited'=>1))->count();
                }
            }elseif ( $type == 'recommendID'){
                foreach ($list as $k => $v) {
                    $list[$k]['count'] =$this->distributor_model->where(array('recommendID' => $list[$k]['id'],'audited'=>1))->count();
                }
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
            'YM_DOMAIN' => $YM_DOMAIN,
            'level_num' => $level_num,
            'level_name' =>$level_name,
        );

        return $return_result;
    }



    /**
     * 获取树状图的下属
     * @param $page_info
     * @param array $condition
     * @return array
     * add by qjq
     */
    public function dis_treedirect($page_info,$condition=array(),$type){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $count = $this->distributor_model->where($condition)->count();

        if ($count > 0) {

            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list =$this->distributor_model->where($condition)->order('time desc')->page($page_con)->select();
            }
            else{
                $list =$this->distributor_model->where($condition)->order('time desc')->select();
            }
            if($type == 'pid'){
                foreach ($list as $k => $v) {
                    $list[$k]['count'] =$this->distributor_model->where(array('pid' => $list[$k]['id'],'audited'=>1))->count();
                }
            }elseif ($type == 'recommendID'){
                foreach ($list as $k => $v) {
                    $list[$k]['count'] =$this->distributor_model->where(array('recommendID' => $list[$k]['id'],'audited'=>1))->count();
                }
            }

        }else{
            $return_result = array(
                'code'  =>  2,
                'msg'  =>  '没下级经销商',
            );
            return $return_result;
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
    }
    

    /**
     * 获取id的父类信息
     * @param $id
     * @param $type
     * @return array
     * add by qjq
     */
    public function findParent($id,$type)
    {
        $field='id,pid,name,path,rec_path,level';
        $info = $this->distributor_model->where(array('id'=>$id))->field($field)->select();
        //判断传过来的type,然后查询相应的链条
        if($type == 'pid'){
            foreach ($info as $k => $v) {
                $info[$k]['count'] =$this->distributor_model->where(array('pid' => $info[$k]['id'],'audited'=>1))->count();
            }
            $id_path=$info[0]['path'];
            $pid=explode('-',$id_path);
            $condition['id']=array('in',$pid);
            $parent_info=$this->distributor_model->where($condition)->field($field)->select();
            foreach ($parent_info as $k => $v) {
                $parent_info[$k]['count'] =$this->distributor_model->where(array('pid' => $parent_info[$k]['id'],'audited'=>1))->count();
            }
        }elseif ($type == 'recommendID'){
            foreach ($info as $k => $v) {
                $info[$k]['count'] =$this->distributor_model->where(array('recommendID' => $info[$k]['id'],'audited'=>1))->count();
            }
            $id_path=$info[0]['rec_path'];
            $pid=explode('-',$id_path);
            $condition['id']=array('in',$pid);
            $parent_info=$this->distributor_model->where($condition)->field($field)->select();
            foreach ($parent_info as $k => $v) {

                $parent_info[$k]['count'] =$this->distributor_model->where(array('recommendID' => $parent_info[$k]['id'],'audited'=>1))->count();
            }
        }
        if(empty($parent_info)){
            return $info;
        }else{
            $parent_all=array_merge($parent_info,$info);
            return $parent_all;
        }


//        //若path,recpath链条错误,则可以启用
//        $field='id,pid,recommendID,name,path,rec_path,level';
//        $info = $this->distributor_model->where(array('id'=>$id))->field($field)->select();
//        if($type == 'pid'){
//            $pid=$info[0]['pid'];
//            foreach ($info as $k => $v) {
//                $info[$k]['count'] =$this->distributor_model->where(array('pid' => $info[$k]['id'],'audited'=>1))->count();
//            }
//            while ($pid !=0){
//                $condition=[
//                    'id'=>$pid,
//                ];
//                $parent=$this->distributor_model->where($condition)->field($field)->find();
//                $pid=$parent['pid'];
//                $parent_info[]=$parent;
//                foreach ($parent_info as $k => $v) {
//                $parent_info[$k]['count'] =$this->distributor_model->where(array('pid' => $parent_info[$k]['id'],'audited'=>1))->count();
//                }
//            }
//        }elseif ($type == 'recommendID'){
//            $recommendID=$info[0]['recommendID'];
//            foreach ($info as $k => $v) {
//                $info[$k]['count'] =$this->distributor_model->where(array('recommendID' => $info[$k]['id'],'audited'=>1))->count();
//            }
//            while ($recommendID !=0){
//                $condition=[
//                    'id'=>$recommendID,
//                ];
//                $parent=$this->distributor_model->where($condition)->field($field)->find();
//                $recommendID=$parent['recommendID'];
//                $parent_info[]=$parent;
//                foreach ($parent_info as $k => $v) {
//                $parent_info[$k]['count'] =$this->distributor_model->where(array('recommendID' => $parent_info[$k]['id'],'audited'=>1))->count();
//                }
//            }
//        }
//        if(empty($parent_info)){
//            return $info;
//        }else{
//            $parent_arr=  array_reverse($parent_info);
//            $parent_all=array_merge($parent_arr,$info);
//            return $parent_all;
//        }

    }
 
    //查找哪些代理有下级但is_lowest没有置0的
    public function is_yes_lowest() {
        //清除团队缓存
        clean_team_path_cache();
        if (C('DEFAULT_TEAM') == 'path') {
            $field = 'pid';
        } else {
            $field = 'recommendID';
        }
        $users = $this->distributor_model->select();
        foreach ($users as $user) {
            $rec = $this->distributor_model->where(["$field" => $user['id'], 'audited' => 1])->find();
            if ($rec && $user['is_lowest'] == 1) {
                $no[] = $user;
            }
        }
//        echo '<pre>';var_dump($no);die;
        foreach ($no as $v) {
            $this->distributor_model->where(['id' => $v['id']])->save(['is_lowest' => 0]);
        }
    }
    
    //根据条件过滤团队id
    public function filter_team_ids($uids, $where) {
        $new_uids = [];
        foreach ($uids as $uid) {
            $where['id'] = $uid;
            $user = $this->distributor_model->where($where)->find();
            if (!$user) {
                continue;
            }
            $new_uids[] = $uid;
        }
        return $new_uids;
    }
    
    //计算团队里各个等级的人数
    public function get_team_level_number($uids) {
        $level_num = C('LEVEL_NUM');
        for($i = 1;$i <= $level_num;$i++) {
            $result[$i] = 0;
        }
        foreach ($uids as $uid) {
            $user = $this->distributor_model->find($uid);
            if (isset($result[$user['level']])) {
                $result[$user['level']]++;
            }
        }
        return $result;
    }
    
    //获取全国业绩
    public function get_total_money($month) {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        if (!$month) {
            $month = get_month();
        }
        if($count_way == $this->order_count_way){
            $where = [
                'pid' => 0,
                'uid' => 0,
                'day' => 0,
                'month' => $month
            ];
            return $this->order_count_model->where($where)->getField('cost_money');
        } else if($count_way == $this->order_num_count_way){
            $where = [
                'pid' => 0,
                'uid' => 0,
                'day' => 0,
                'month' => $month
            ];
            return $this->order_count_model->where($where)->getField('cost_num');
        } else {
            return $this->count_model->where(['month' => $month])->sum('money');
        }
    }
    
    /**
     * 获取实际参与团队业绩计算的团队id
     * @param type $count_way 统计方式0虚拟币1订单金额2订单数量
     * @param type $user 要获取团队的代理信息
     * @param type $team_path //查找团队的参数
     */
     public function get_team_count_ids($count_way, $user, $team_path) {
        import('Lib.Action.Funds','App');
        import('Lib.Action.Order','App');
        $funds_obj = new Funds();
        $order_obj = new Order();
        $uids = [];
        if ($count_way == $this->money_count_way) {
            if ($funds_obj->is_parent_audit) {
                //如果按虚拟币统计，并且是上级审核，则只统计和自己级别相同的代理业绩总和
                //比如 A1->B2->B3->A4->B5
                //A1的团队业绩=A1+A4  B2的团队业绩=B2+B3+B5
                $uids = $this->get_team_ids($user['id'], $team_path);
                $uids = $this->filter_team_ids($uids, ['level'=>$user['level'], 'audited'=>1]);
                
            } else {
                //获取团队信息
                $uids = $this->get_team_ids($user['id'], $team_path);

            }
        } else if (in_array($count_way,[$this->order_count_way,  $this->order_num_count_way])) {
            if (!$order_obj->is_top_supply) {
                //如果按订单统计，并且是上级审核，则只统计和自己级别相同的代理业绩总和
                //比如 A1->B2->B3->A4->B5
                //A1的团队业绩=A1+A4  B2的团队业绩=B2+B3+B5
                $uids = $this->get_team_ids($user['id'], $team_path);
                $uids = $this->filter_team_ids($uids, ['level'=>$user['level'], 'audited'=>1]);
            } else {
                //获取团队信息
                $uids = $this->get_team_ids($user['id'], $team_path);

            }
        }
        return $uids;
     }
     
     //虚拟币扣费，同时扣除个人业绩
     public function charge_person_money($uid, $money, $month) {
         if (!$month) {
             $month = get_month();
         }
         $where = [
             'uid' => $uid,
             'month' => $month,
         ];
         $money_count = $this->count_model->where($where)->order('day desc')->select();
         foreach ($money_count as $v) {
             $diff_money = bcsub($v['money'],$money);
             if ($diff_money >= 0) {
                 //一次够扣
                 $data = [
                     'money' => $diff_money,
                 ];
                 $this->count_model->where(['id' => $v['id']])->save($data);
                 break;
             } else {
                 //扣除多条记录的金额
                 $this->count_model->where(['id' => $v['id']])->save(['money'=>0]);
                 $money = abs($diff_money);
                 continue;
             }
         }
     }
}
