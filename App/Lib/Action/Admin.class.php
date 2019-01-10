<?php
//后台的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Admin {
    
    public $is_add_log = TRUE;//是否添加日志
    
    public $admin_auth = [
        '1' =>  '权限',
//         '2' =>  '代理查看',
//         '15' =>  '代理操作',
//         '13' =>  '代理审核',
//         '25'    =>  '发展代理',
        
//         '5' =>  '虚拟币',
//         '16' =>  '虚拟币审核',
        
//         '3' =>  '代理商城',
//         '17' =>  '代理商城产品属性',
//         '18' =>  '代理商城产品属性',
        
//         '4' =>  '积分规则',
//         '19' =>  '积分订单',
//         '20' =>  '积分订单',
        
//         '6' =>  '出库',
//         '7' =>  '市场营销',
//         '8' =>  '微官网',
//         '9' =>  '返利',
//         '10' =>  '品牌商城',
// //        '11' =>  '营销活动',
//         '12'=>  '数据分析',
//         '14'  => '云仓下单',
        
        '21'  => '系统配置',
        '98'  => '公司内容管理',
        '97'  => '摄影棚',
        '99'  => '摄影分类管理',
        '100'  => '摄影封面管理',
        '101'  => '视频分享管理',
        
        // '22'    =>  '运费模板',
        // '23'    =>  '防伪管理',
        // '24'    =>  '消息管理',
        
        // '26'    =>  '仓库管理',
    ];
    
    //权限相关对应的module
    public $admin_auth_module = [
        '1' =>  'admin',
        '2' =>  'manager,upgrade',
        '15' =>  'manager,regulations,info',
        '13' =>  'manager',
        
        '5' =>  'funds',
        '16' =>  'funds',
        
        '3' =>  'order,inform',
        '17' =>  'sku',
        '18' =>  'shipping',
        
        '4' =>  'integral',
        '19' =>  'integralorder',
        '20' =>  'integraltemplet',
        
        '6' =>  'stock',
        '7' =>  'market',
        '8' =>  'publicity,aptitude,goods,info',
        '9' =>  'rebate,newrebate',
        '10' =>  'malltemplet,mallorder',
//        '11' =>  'sale',
        '12'=>  'analysis',
        '14'  => 'stockorder',
        
        '21'  => 'webset',
        
        '22'    =>  'shipping',
        '23'    =>  'security',
        '24'    =>  'inform',
        '25'    =>  'templet',
        '26'    =>  'depot',

        '98'  => 'photo',
        '97'  => 'photo',
        '99'  => 'photo',
        '100'  => 'photo',
        '101'  => 'video',
    ];
    
    //例外的权限
    //首页、用户手册、发展链接
    public $admin_auth_extra = [
        'index','user','info','admin'
    ];
    
    
    /**
     * 架构函数
     */
    public function __construct() {
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        
        if( $FUNCTION_MODULE['MONEY'] != 1 ){
            unset($this->admin_auth[5]);
        }
        elseif( $FUNCTION_MODULE['INTEGRAL_SHOP'] != 1 ){
            unset($this->admin_auth[4]);
        }
        elseif( $FUNCTION_MODULE['MALL_SHOP'] != 1 ){
            unset($this->admin_auth[10]);
        }
        elseif( $FUNCTION_MODULE['STOCK'] != 1 ){
            unset($this->admin_auth[6]);
        }
        elseif( $FUNCTION_MODULE['MARKET'] != 1 ){
            unset($this->admin_auth[7]);
        }
        elseif( $FUNCTION_MODULE['GW'] != 1 ){
            unset($this->admin_auth[8]);
        }
        elseif( $FUNCTION_MODULE['STOCK_ORDER'] != 1 ){
            unset($this->admin_auth[13]);
        }
        
    }
    
    
    
    //添加后台操作日志
    public function add_active_log($aid,$log){


        if( !$this->is_add_log ){
            return TRUE;
        }
        
        
        $cur_url = __SELF__;//当前的URL地址

        if( $aid == NULL || empty($log) ){
            return FALSE;
        }

        $active_log_obj = M('admin_active_log');
        
        
        $add_info = array(
            'aid'           =>  $aid,
            'log'           =>  $log,
            'active_url'    =>  $cur_url,
            'created'       =>  time(),
        );

        $result = $active_log_obj->add($add_info);
        
//        if( $result ){
//            return 1;
//        }
//        else{
//            return $active_log_obj->getDbError();
//        }
        
        
        return $result;
    }//end func add_active_log
    
    //新项目登陆toposxitong账号初始化一些数据
    public function init_data() {
        //产品属性
        $data = [
            [
                'name'  =>  '款式',
            ],
            [
                'name'  =>  '功效',
            ],
        ];
        M('templet_property')->addAll($data);
        
        //升级说明
        $level_name = C('LEVEL_NAME');
        foreach ($level_name as $k => $v) {
           $option[] = [
               'level' => $k,
               'money' => 0,
               'desc' => '请设置升级支付金额',
               'created' => time(),
           ]; 
        }
        M('distributor_upgrade_desc')->addAll($option);
    }
    
    
    
}