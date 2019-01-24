<?php
//后台的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Admin {
    public $is_add_log = TRUE;//是否添加日志
    public $admin_auth = [
        '1' =>  '权限',
        '2'  => '系统配置',
        '3'  => '网站分类管理',
        '4'  => '项目',
        '5'  => '新闻',
        '6'  => '关于TOPOS',
        '7'  => '关于TOPOS',
    ];
    //权限相关对应的module
    public $admin_auth_module = [
        '1' =>  'admin',
        '2'  => 'webset',
        '3'  => 'photo',
        '4'  => 'item',
        '5'  => 'news',
        '6'  => 'aboutus',
        '7'  => 'aboutus',
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