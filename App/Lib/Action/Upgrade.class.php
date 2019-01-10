<?php
//代理任务升级类
header("Content-Type: text/html; charset=utf-8");
require_once "Common.class.php";

class Upgrade extends Common{
    
    private $distributor_model;
    private $upgrade_apply_model;
    private $upgrade_setting_model;
    
    private $team_obj;
    
    public $open = 1; //升级开启
    public $close = 0; //升级关闭
    
    //任务类型
    public $upgrade_people = 0;//人数
    public $upgrade_personal_money = 1;//个人业绩
    public $upgrade_team_money = 2;//团队业绩
    
    //按人数是根据哪种关系算
    public $number_recommend = 0;//按推荐
    public $number_development = 0;//按发展
    
    public $upgrade_manual = 0;//手动升级
    public $upgrade_auto = 1;//任务升级

    public $yes_upgrade = 1; //已升级
    public $not_upgrade = 0; //未升级

    public $status_name = [];//审核状态
    public $type_name = [];//升级类型
    
    public $status_boss_audit = 0;//待总部审核
    public $status_yes_audit = 1;//审核通过
    public $status_no_audit = 2;//审核不通过
    public $status_head_audit = 3;//待上级审核
    public $status_agent_audit = 4;//待代理确认升级(中间状态)
    /**
     * 架构函数
     */
    public function __construct() {
        import('Lib.Action.Team', 'App');
        $this->team_obj = new Team();
        $this->upgrade_apply_model = M('distributor_upgrade_apply');
        $this->upgrade_setting_model = M('distributor_upgrade_setting');
        $this->distributor_model = M('distributor');
        
        $this->status_name[$this->status_boss_audit] = '待总部审核';
        $this->status_name[$this->status_yes_audit] = '审核通过';
        $this->status_name[$this->status_no_audit] = '审核不通过';
        
        $this->type_name[$this->upgrade_manual] = '手动升级';
        $this->type_name[$this->upgrade_auto] = '任务升级';
    }
    
    public function upgrade($user) {
        //最高级不用升级
        if (!$user || $user['level'] == 1) {
            return;
        }
        //判断人数是否达标
        $people_num = $this->check_people($user);
        //判断个人业绩是否达标
        $personal_money = $this->check_personal_money($user);
        //判断团队业绩是否达标
        $team_money = $this->check_team_money($user);
        //定义几种任务是or或and关系(自由定义)
        if ($people_num || $personal_money || $team_money) {
            //升级任务都没有开启则直接返回
            if ($people_num === true && $personal_money === true && $team_money === true) {
                return;
            }
            $where = [
                 'uid' => $user['id'],
                 'type' => $this->upgrade_auto,
                 'status' => $this->status_boss_audit,//待代理确认升级
            ];
            $data  = [
                'cur_level' => $user['level'],
                'apply_level' => $user['level'] - 1,
                'people_num' => is_numeric($people_num) ? $people_num : 0,
                'personal_money' => is_numeric($personal_money) ? $personal_money : 0,
                'team_money' => is_numeric($team_money) ? $team_money : 0,
                'created' => time(),
            ];
            $apply = $this->upgrade_apply_model->where($where)->find();
            if (!$apply) {
                //写进升级申请表
                $this->upgrade_apply_model->add(array_merge($data, $where));
            } else {
                $this->upgrade_apply_model->where($where)->save($data);
            }
        }
    }
    
    //检查任务是否达标(人数)
    private function check_people($user) {
        $map = [
            'status'=> $this->open,
            'type' => $this->upgrade_people,
            'level' => $user['level'],
        ];
        $upgrade_setting = $this->upgrade_setting_model->where($map)->find();
        $people_num = $upgrade_setting['parameter'];//任务人数
        if (!$upgrade_setting || $people_num <= 0) {
            return true;
        }
        //计算人数是否达标
        if ($upgrade_setting['number_type'] == $this->number_recommend) {
            //按推荐同级、比自己高级人数计算
            $where = [
                'recommendID' => $user['id'],
                'level' => ['elt', $user['level']],
                'audited' => 1
            ];
            $count = $this->distributor_model->where($where)->count('id');
            if ($count < $people_num) {
                return false;
            }
            return $count;
        } else {
           //按发展下级人数计算
        }
    }
    //检查任务是否达标(个人业绩)
    private function check_personal_money($user) {
        $map = [
            'status'=> $this->open,
            'type' => $this->upgrade_personal_money,
            'level' => $user['level']-1,
        ];
        $upgrade_setting = $this->upgrade_setting_model->where($map)->find();
        if (!$upgrade_setting) {
            return true;
        }
        //计算个人业绩是否达标
        $person_money = $this->team_obj->get_team_money($user['id']);
        if ($person_money < $upgrade_setting['parameter']) {
            return false;
        }
        return $person_money;
    }
    //检查任务是否达标(团队业绩)
    private function check_team_money($user) {
        $count_way = C('MONEY_COUNT_WAY');//统计方式0虚拟币1订单金额2订单数量
        $map = [
            'status'=> $this->open,
            'type' => $this->upgrade_team_money,
            'level' => $user['level']-1,
        ];
        $upgrade_setting = $this->upgrade_setting_model->where($map)->find();
        if (!$upgrade_setting) {
            return true;
        }
        //计算团队业绩是否达标
        $team_path = get_team_path_by_cache();
//        $uids = $this->team_obj->get_team_ids($user['id'], $team_path);
        //获取实际参与团队业绩计算的团队id
        $uids = $this->team_obj->get_team_count_ids($count_way, $user, $team_path);
        $team_money = $this->team_obj->get_team_money($uids);
        if ($team_money < $upgrade_setting['parameter']) {
            return false;
        }
        return $team_money;
    }
    
    //获取用户申请升级信息
    public function get_distributor_upgrade_apply($page_info=array(),$condition=array()){

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        
        $count = $this->upgrade_apply_model->where($condition)->count();
        $levename = C('LEVEL_NAME');
        
        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->upgrade_apply_model->where($condition)->order('status asc')->page($page_con)->select();
            }
            else{
                $list = $this->upgrade_apply_model->where($condition)->order('status asc')->select();
            }

            $list = $this->get_related_data($list, 'distributor', ['uid','audit_id']);
            
            foreach( $list as $k => $v ){
                $v_status = $v['status'];
                $v_cur_level = $v['cur_level'];
                $v_apply_level = $v['apply_level'];
                $v_created = $v['created'];
                $v_type = $v['type'];
                
                $list[$k]['cur_levname'] = $levename[$v_cur_level];
                $list[$k]['apply_levname'] = $levename[$v_apply_level];
                $list[$k]['status_name'] = $this->status_name[$v_status];
                $list[$k]['created_fromat'] = date('Y-m-d H',$v_created);
                $list[$k]['type_name'] = $this->type_name[$v_type];
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
            'list'  =>  empty($list)?[]:$list,
            'page'  =>  $page,
            'count' =>  $count,
        );

        return $return_result;
    }
    
     /**
     * 审核升级,注：这个方法只用于审核不通过，审核通过在代理管理的升级方法里触发
     * @param type $id
     * @param type $status
     * @param type $audit_id
     * @return string|int
     */
    public function upgrade_apply_pass($ids,$status,$audit_id=0){
        import('Lib.Action.NewRebate', 'App');
        $rebate_obj = new NewRebate();
        $level_name = C('LEVEL_NAME');
        if( empty($ids) || empty($status) ){
            $result = [
                'code'  =>  2,
                'msg'   =>  '提交错误！',
            ];
            return $result;
        }
        foreach ($ids as $id) {
            $apply = $this->upgrade_apply_model->find($id);
            if( !$apply ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '查无此申请数据!',
                ];
                return $return_result;
            }
            //升级代理
            $user = $this->distributor_model->find($apply['uid']);
            if( !$user ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '查无此代理!',
                ];
                return $return_result;
            }
//            if($user['level'] <= $apply['apply_level']){
//                $return_result = [
//                    'code'  =>  3,
//                    'msg'   =>  '代理当前等级高于申请的等级!',
//                ];
//                return $return_result;
//            }
//            if (in_array($user['level'], $rebate_obj->rebate_agent_level)) {
//                //升级之前要更新团队业绩和返利
//                $rebate_obj->create_team_rebate('', '', $user);
//            }
//            if($status == 1){
//                $data = [
//                    'id' => $apply['uid'],
//                    'level' => $apply['apply_level'],
//                    'levname' => $level_name[$apply['apply_level']],
//                ];
//                if (!$this->distributor_model->save($data)) {
//                    $return_result = [
//                        'code'  =>  3,
//                        'msg'   =>  '代理升级失败!',
//                    ];
//                    return $return_result;
//                }
//            }

            $save['id'] = $id;
            $save['status'] = $status;
            $save['updated'] = time();
            $res = $this->upgrade_apply_model->save($save);
            if( !$res ){
                $return_result = [
                    'code'  =>  3,
                    'msg'   =>  '审核失败!',
                ];
                return $return_result;
            }else{
                $return_result = [
                    'code'  =>  1,
                    'msg'   =>  '审核成功！',
                ];
            }
        }
        return $return_result;
    }
}