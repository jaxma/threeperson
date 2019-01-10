<?php

/**
 *  topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class WebsetAction extends CommonAction {
    private $logo_upload_dir;
    //

    public function _initialize()
    {
        parent::_initialize();
        $this->logo_upload_dir = $_SERVER['DOCUMENT_ROOT'].__ROOT__;
    }

    public function index() {
        //获取前台logo图片
        $logo_img_path = '/upload/system_logo/index_logo.png';
        if(!file_exists($this->logo_upload_dir.$logo_img_path))$logo_img_path = '/upload/system_logo/index_logo.jpg';
        if(!file_exists($this->logo_upload_dir.$logo_img_path))$logo_img_path = '/upload/system_logo/index_logo.gif';
        if(!file_exists($this->logo_upload_dir.$logo_img_path))$logo_img_path = '/upload/system_logo/index_logo.jpeg';
        $logo_img_path = $logo_img_path.'?'.rand(5,99999);
        //获取系统头像
        $img_path = '/upload/system_logo/system_logo.png?'.rand(5,99999);
        //检查自定义的是否存在
//      if(!file_exists($img_path)){
//          $img_path = '/Public/Radmin_v3/images/logo/system_logo.png?'.rand(5,99999);
//      }
        $kdn_code = kdn_code();
        $aid = $_SESSION['aid'];
        $this->kdn_code = kdn_code();
        $this->webconfig = $this->get_config();
        $this->img_path=$img_path;
        $this->logo_img_path=$logo_img_path;
        $this->display();
    }

    //获取配置
    public function get_webconfig() {
        
        if (!IS_AJAX) {
            return FALSE;
        }

        $webconfig = $this->get_config();
        
        $result = [
            'code' => 1,
            'msg' => '',
            'config' => $webconfig,
        ];
        echo $this->ajaxReturn($result);
    }
    
    
    private function get_config(){
        import('Lib.Action.User', 'App');
        $User = new User();

        import('Lib.Action.Funds', 'App');
        $Funds = new Funds();

        import('Lib.Action.Order', 'App');
        $Order = new Order();

        import('Lib.Action.Integral', 'App');
        $Integral = new Integral();

        $ORDER_MB = C('ORDER_MB');
        
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        $REBATE = C('REBATE');

        $MALL_REBATE=C('MALL_REBATE');
        $MALL_REFUND=C('MALL_REFUND');
        
        $kdnapi = C('kdnapi');
        $send_order = C('SEND_ORDER');
        $kdnian_order = C('KDNIAO_ORDER');
        $KDORDER_PRICE = C('KDORDER_PRICE');
        $MESSAGE_MODULE = C('MESSAGE_MODULE');

        $WX_PAY_CONFIG = C('WX_PAY_CONFIG');

        $webconfig = [
            //基础配置
            'YM_DOMAIN' => C('YM_DOMAIN'), //域名
            'SYSTEM_NAME' => C('SYSTEM_NAME'), //系统名称
            'LOGO_URL' => C('LOGO_URL'), //系统名称
            //级别配置
            'LEVEL_NUM' => C('LEVEL_NUM'), //经销商级别数
            'LEVEL_NAME' => C('LEVEL_NAME'), //经销商级别名
            //系统配置
            'GROW_MODEL' => C('GROW_MODEL'), //发展模式，1为高发展低，2为高发展低及平级推，3为任何级别都可以发展，4为特定级别发展模式，5为特定级别特定发展（5-25新增）
            'GROW_MODEL_LEVEL' => C('GROW_MODEL_LEVEL'), //不同级别的发展模式，级别对应1,2,3，含义与GROW_MODEL相同
            'GROW_MODEL_SPCAIL_LEVEL'   =>  C('GROW_MODEL_SPCAIL_LEVEL'),//特定级别发展特定级别，如[1=>[2,3]]，含义为级别1可以发展级别2，级别3
            'IS_SUBMIT_ID_CARD_IMG' => C('IS_SUBMIT_ID_CARD_IMG'), //0为不提交图片，1为都要提交，2为只提交身份证，3为只提交身份证截图
            'AUDIT_WAY' => C('AUDIT_WAY'), //审核方式1为直接上级审核2为总部审核
            'IS_AUDITED' => C('IS_AUDITED'), //当审核方式为2时，可设置是否在总后台可以审核上级的代理
            'AUDIT_WAY_LEVEL' => C('AUDIT_WAY_LEVEL'), //根据级别进行审核方式
            //系统更新
            'SYSTEM_UPDATE' => C('SYSTEM_UPDATE'), //系统更新指令
            //公众号配置
            'APP_ID' => C('APP_ID'), //公众号AppID
            'APP_SECRET' => C('APP_SECRET'), //公众号AppID
            'APP_TEST' => C('APP_TEST'),
            //微信支付配置
            'WX_PAY_CONFIG' => [
                'MCHID' =>  $WX_PAY_CONFIG['MCHID'], //微信支付商户号
                'KEY' =>  $WX_PAY_CONFIG['KEY'],   //微信支付密钥
            ],
            //消息配置
            'SH_MB' => C('SH_MB'), //审核模板
            'SQ_MB' => C('SQ_MB'), //申请模板
            'NEW' => $ORDER_MB['NEW'], //新订单
            'CANCLE' => $ORDER_MB['CANCLE'], //取消订单
            'AUDIT' => $ORDER_MB['AUDIT'], //审核订单
            'MONEY_MB' => C('MONEY_MB'), //虚拟币申请/审核
            'UPGRADE_APPLY_MB' => C('UPGRADE_APPLY_MB'), //代理升级
            'UPGRADE_PASS_MB' => C('UPGRADE_PASS_MB'),//代理升级申请
            'MONEY_COUNT_WAY' => C('MONEY_COUNT_WAY'),
            
            //功能模块
            'FUNCTION_MODULE'=> [
                'MONEY' =>  (int)$FUNCTION_MODULE['MONEY'],//虚拟币
                'MONEY_APPLY_PAY_TYPE' =>  (int)$FUNCTION_MODULE['MONEY_APPLY_PAY_TYPE'],//虚拟币充值的支付方式
                'INTEGRAL_SHOP' =>  (int)$FUNCTION_MODULE['INTEGRAL_SHOP'],//积分商城
                'MALL_SHOP'=>(int)$FUNCTION_MODULE['MALL_SHOP'],//优惠商城
                'STOCK'=>(int)$FUNCTION_MODULE['STOCK'],//出库模块
                'MARKET'=>(int)$FUNCTION_MODULE['MARKET'],//营销模块
                'GW'=>(int)$FUNCTION_MODULE['GW'],//微官网
                'TEAM'=>(int)$FUNCTION_MODULE['TEAM'],//团队
                'BOSS_ORDER' => (int)$FUNCTION_MODULE['BOSS_ORDER'],//总部下单
                'STOCK_ORDER' => (int)$FUNCTION_MODULE['STOCK_ORDER'],//库存订单 
                //'STOCK_ORDER' => (int)$FUNCTION_MODULE['STOCK_ORDER'],//库存订单 
                'ORDER_FORMAT'  =>  (int)$FUNCTION_MODULE['ORDER_FORMAT'],//代理商城产品规格模块（只禁止了总部后台）
                'SHOP_IN_SHOP' => (int)$FUNCTION_MODULE['SHOP_IN_SHOP'],//店中店
                'DEPOT' =>  (int)$FUNCTION_MODULE['DEPOT'],//仓库功能
            ],
            'ORDER_SHIPPING'=>(int)C('ORDER_SHIPPING'),//运费模板模块
            'SHIPPING_REDUCE_WAY'=>(int)C('SHIPPING_REDUCE_WAY'),//运费模板的满减免运费方式
            
            //返利配置
            'REBATE' => [
                'OPEN' =>  (int)$REBATE['OPEN'],
                'ORDER' =>  (int)$REBATE['ORDER'],
                'MONEY' =>  (int)$REBATE['MONEY'],
                'ONCE' =>  (int)$REBATE['ONCE'],
                'SAME_DEVELOPMENT' =>  (int)$REBATE['SAME_DEVELOPMENT'],
                'DEVELOPMENT' =>  (int)$REBATE['DEVELOPMENT'],
                'PERSONAL' => (int)$REBATE['PERSONAL'],
                'ORDINARY_TEAM' => (int)$REBATE['ORDINARY_TEAM'],
                'CLICK_TEAM_REBATE'=>(int)$REBATE['CLICK_TEAM_REBATE'],//团队返利实时生成还是点击生成，0为实时，1为点击
//                ordinary_team
            ],


            //品牌商城返利配置
            'MALL_REBATE' => [
                'OPEN' =>  (int)$MALL_REBATE['OPEN'],
                'ORDER' =>  (int)$MALL_REBATE['ORDER'],
            ],


            //品牌商城的提现开启以及提现方式
            'MALL_REFUND' =>[
                'IS_OPEN'=>(int)$MALL_REFUND['IS_OPEN'],
                'MALL_REFUND_PAY_TYPE'=>(int)$MALL_REFUND['MALL_REFUND_PAY_TYPE'],
            ],
            
            // 消息模块配置
            'MESSAGE_MODULE' => [
                'SYSTEM' => $MESSAGE_MODULE['SYSTEM'],
                'DISTRIBUTOR' => $MESSAGE_MODULE['DISTRIBUTOR'],
            ],

            'APP_DEBUG' => APP_DEBUG,
            
            //额外的配置文件
            'LOAD_EXT_CONFIG'   =>  C('LOAD_EXT_CONFIG'),
            
            //基础配置
            'IS_TEST' => (int)C('IS_TEST'), //是否测试模式
            'MONEY_COUNT_WAY' => C('MONEY_COUNT_WAY'), //统计业绩方式0用虚拟币1订单金额2订单数量（注明一般情况是这样）
            'DEFAULT_TEAM' => C('DEFAULT_TEAM'), //团队是根据上下级关系(path)还是推荐人关系(rec_path)定义
            
            //快递鸟配置
            'kdnapi'    =>  [
                'EBusinessID'   =>  $kdnapi['EBusinessID'],
                'AppKey'   =>  $kdnapi['AppKey'],
            ],
            'SEND_ORDER' => $send_order,
            'KDNIAO_ORDER' => $kdnian_order,
            'SHIPPER_PAYTYPE' => C('SHIPPER_PAYTYPE'),
            'KDORDER_PRICE' => C('KDORDER_PRICE'),
            //--------------------------start 以下为特殊配置（在extra配置）----------------------------------------
            //用户配置
            'user' => [
                'is_multilayer' => $User->is_multilayer, //是否多层级，选择TRUE，该系统必须有“代理关系表”
                'has_user_bind' => $User->has_user_bind, //是否生成用户关系
                'is_cycle_multilayer' => $User->is_cycle_multilayer, //是否使用有限制次数的循环获得多层代理关系
                'open_upgrade_apply' => $User->open_upgrade_apply, //是否开启代理申请升级
                'upgrade_apply_aduit'=> $User->upgrade_apply_aduit,//1为上级审核，0为总部审核
            ],
            //订单配置
            'order' => [
                'status_name' => $Order->status_name, //订单状态
                'all_pay_type' => $Order->all_pay_type, //支付方式
                'is_generate_order_count' => $Order->is_generate_order_count, //是否生成订单统计表
                'is_top_supply' => $Order->is_top_supply, //是否总部供货
                'is_top_supply_level' => $Order->is_top_supply_level, //如果有值则根据级别判断是否根据
                'opent_order_limit' => $Order->opent_order_limit, //是否启用下单限制
            ],
            //资金配置
            'funds' => [
                'is_charge_money' => $Funds->is_charge_money, //是否进行虚拟币系统的逻辑
                'is_charge_money_level' => $Funds->is_charge_money_level, //如果有值则根据该值进行级别判断是否进行虚拟币功能逻辑
                'is_all_can_refund' => $Funds->is_all_can_refund, //是否充值金额都可以提现，即可提现金额等于充值金额
                'is_get_min_apply_money' => $Funds->is_get_min_apply_money, //是否使用获取最低申请金额
                'is_get_min_refund_money' => $Funds->is_get_min_refund_money, //是否使用获取最低提现金额
                'is_parent_order' => $Funds->is_parent_order, //订单扣费时，扣费金额充回订单供货商时为TRUE，直接扣费不做充值操作为FALSE
                'is_parent_audit' => $Funds->is_parent_audit, //是否由直属上级审核虚拟币，是则由上级审核下级充值申请，并上级相应余额转到下级。否则总部审核充值
                'is_order_return' => $Funds->is_order_return, //是否启用订单返还（需要开启扣虚拟币系统is_charge_money）,注意，开启后经销商及总部审核时都会触发
                'order_return_rank' => $Funds->order_return_rank, //订单返还循环的次数
                'is_rebate_recharge' => $Funds->is_rebate_recharge, //返利是否充入账户
            ],
            //积分配置
            'integral' => [
                'integral_open' => $Integral->integral_open, //是否开启积分功能
                'integral_status' => $Integral->integral_status, //日志类型
                'integral_rule_typ' => $Integral->integral_rule_type, //积分规则类型，排序应与日志类型的行为是一致的（描述可以略有不同，但是进行的系统业务是一致）
            ],
                 
            //--------------------------end 以下为特殊配置（在extra配置）----------------------------------------
        ];
        
        //额外的配置
        $extra = C('extra');
        $extra_arr = [
            'user','order','funds',
        ];//需要更改的额外配置
        
        foreach( $webconfig as $k=>$v ){
            
            if( in_array($k, $extra_arr) ){
                foreach( $v as $k2=>$v2 ){
                    if( isset($extra[$k][$k2]) ){
                        $webconfig[$k][$k2] = $extra[$k][$k2];
                    }
                }
            }
            
        }
        
        
        return $webconfig;
    }



    //修改网站配置
    public function update_webset() {

        import('Lib.Action.User', 'App');
        $User = new User();

        $new_config = [];

        $YM_DOMAIN = trim(I('YM_DOMAIN'));
        $SYSTEM_NAME = trim(I('SYSTEM_NAME'));
        $LOGO_URL = trim(I('LOGO_URL'));
        
        //$LEVEL_NUM = trim(I('LEVEL_NUM'));
        $LEVEL_NAME = I('LEVEL_NAME');
        
        
        // 发展模式
        $GROW_MODEL_LEVEL_KEY = I('GROW_MODEL_LEVEL_KEY');
        $GROW_MODEL_LEVEL_VAL = I('GROW_MODEL_LEVEL_VAL');
        if( $GROW_MODEL_LEVEL_KEY != NULL ){
            foreach($GROW_MODEL_LEVEL_KEY as $k => $v){
                $GROW_MODEL_LEVEL[$v] = $GROW_MODEL_LEVEL_VAL[$k];
            }
        }
        $GROW_MODEL = trim(I('GROW_MODEL'));
        if( $GROW_MODEL != NULL ){
            $new_config['GROW_MODEL'] = $GROW_MODEL;
            $new_config['GROW_MODEL_LEVEL'] = $GROW_MODEL_LEVEL;
        }
        
        
        // 审核方式
        $AUDIT_WAY_LEVEL_KEY = I('AUDIT_WAY_LEVEL_KEY');
        $AUDIT_WAY_LEVEL_VAL = I('AUDIT_WAY_LEVEL_VAL');
        
        if( $AUDIT_WAY_LEVEL_KEY != NULL ){
            foreach($AUDIT_WAY_LEVEL_KEY as $k => $v){
                $AUDIT_WAY_LEVEL[$v] = $AUDIT_WAY_LEVEL_VAL[$k];
            }
        }
        $AUDIT_WAY = trim(I('AUDIT_WAY'));
        if( $AUDIT_WAY != NULL ){
            $new_config['AUDIT_WAY'] = $AUDIT_WAY;
            $new_config['AUDIT_WAY_LEVEL'] = $AUDIT_WAY_LEVEL;
        }
        
//        $GROW_MODEL_LEVEL = trim(I('GROW_MODEL_LEVEL'));
        $IS_SUBMIT_ID_CARD_IMG = trim(I('IS_SUBMIT_ID_CARD_IMG'));
        $MONEY_COUNT_WAY = trim(I('MONEY_COUNT_WAY'));
        
        $IS_AUDITED = trim(I('IS_AUDITED'));
//        $AUDIT_WAY_LEVEL = trim(I('AUDIT_WAY_LEVEL'));

        $SYSTEM_UPDATE = trim(I('SYSTEM_UPDATE'));

        $APP_ID = trim(I('APP_ID'));
        $APP_SECRET = trim(I('APP_SECRET'));
        // 微信功能测试
        $APP_TEST = trim(I('APP_TEST'));
        if($APP_TEST!=NULL){
            $new_config['APP_TEST'] = $APP_TEST;
        }
        
        // 配送单和电子面单开关
        $SEND_ORDER = trim(I('SEND_ORDER'));
        $KDNIAO_ORDER = trim(I('KDNIAO_ORDER'));
        if($SEND_ORDER!=NULL){
            $new_config['SEND_ORDER'] = $SEND_ORDER;
        }
        if($KDNIAO_ORDER!=NULL){
            $new_config['KDNIAO_ORDER'] = $KDNIAO_ORDER;
        }
        
        //消息配置
        $MESSAGE_MODULE_SYSTEM = trim(I('MESSAGE_MODULE_SYSTEM'));
        $MESSAGE_MODULE_DISTRIBUTOR = trim(I('MESSAGE_MODULE_DISTRIBUTOR'));
		    if($MESSAGE_MODULE_SYSTEM!=NULL && $MESSAGE_MODULE_DISTRIBUTOR!=NULL){
			      if(empty($MESSAGE_MODULE_SYSTEM) && empty($MESSAGE_MODULE_DISTRIBUTOR)){
            	  $MESSAGE_MODULE_OPEN = 0;
       		  }else{
           		  $MESSAGE_MODULE_OPEN = 1;
        	  }
			      $new_config['MESSAGE_MODULE']['OPEN'] = $MESSAGE_MODULE_OPEN;
		    }
        
        if($MESSAGE_MODULE_SYSTEM!=NULL){
            $new_config['MESSAGE_MODULE']['SYSTEM'] = $MESSAGE_MODULE_SYSTEM;
        }
        if($MESSAGE_MODULE_DISTRIBUTOR!=NULL){
            $new_config['MESSAGE_MODULE']['DISTRIBUTOR'] = $MESSAGE_MODULE_DISTRIBUTOR;
        }
        
        $SH_MB = trim(I('SH_MB'));
        $SQ_MB = trim(I('SQ_MB'));
        $NEW = trim(I('NEW'));
        $CANCLE = trim(I('CANCLE'));
        $AUDIT = trim(I('AUDIT'));
        $MONEY_MB = trim(I('MONEY_MB'));
        $UPGRADE_APPLY_MB = trim(I('UPGRADE_APPLY_MB'));
        $UPGRADE_PASS_MB = trim(I('UPGRADE_PASS_MB'));
       
        $FUNCTION_MODULE = I('FUNCTION_MODULE');
        
        $EBusinessID = I('EBusinessID');
        $AppKey = I('AppKey');

        //微信支付配置
        $MCHID = trim(I('MCHID'));
        $KEY = trim(I('KEY'));

        // 厂家发货信息
        $BOSS_NAME = trim(I('BOSS_NAME'));
        $BOSS_PROVINCE = trim(I('BOSS_PROVINCE'))=='请选择'?NULL:trim(I('BOSS_PROVINCE'));
        $BOSS_CITY = trim(I('BOSS_CITY'))=='请选择'? NULL:trim(I('BOSS_CITY'));
        $BOSS_COUNTY = trim(I('BOSS_COUNTY'))=='请选择'?NULL:trim(I('BOSS_COUNTY'));
        $BOSS_DETAIL = trim(I('BOSS_DETAIL'));
        $BOSS_PHONE = trim(I('BOSS_PHONE'));
        $SHIPPER_PAYTYPE = trim(I('SHIPPER_PAYTYPE'));
        if($BOSS_NAME!=NULL){
            $new_config['BOSS']['NAME'] = $BOSS_NAME;
        }
        if($BOSS_PROVINCE!=NULL){
            $new_config['BOSS']['PROVINCE'] = $BOSS_PROVINCE;
        }
        if($BOSS_CITY!=NULL){
            $new_config['BOSS']['CITY'] = $BOSS_CITY;
        }
        if($BOSS_COUNTY!=NULL){
            $new_config['BOSS']['COUNTY'] = $BOSS_COUNTY;
        }
        if($BOSS_DETAIL!=NULL){
            $new_config['BOSS']['DETAIL'] = $BOSS_DETAIL;
        }
        if($BOSS_PHONE!=NULL){
            $new_config['BOSS']['PHONE'] = $BOSS_PHONE;
        }
        
        // 电子面单显示价格
        $KDORDER_PRICE = trim(I('KDORDER_PRICE'));
        
        if ($KDORDER_PRICE != NULL) {
            $new_config['KDORDER_PRICE'] = $KDORDER_PRICE;
        }
        
        // 邮费支付方式
        if ($SHIPPER_PAYTYPE != NULL) {
            $new_config['SHIPPER_PAYTYPE'] = $SHIPPER_PAYTYPE;
        }
        // 月结码
        $SHIPPER_CODE = I('SHIPPER_CODE');
        
        foreach($SHIPPER_CODE as $k => $v){
            if(!empty($v)){
                $new_config['SHIPPER_CODE'][$k] = $v;
            }
        }
        
        $IS_TEST=trim(I('IS_TEST'));

        //基础配置
        if ($YM_DOMAIN != NULL) {
            $new_config['YM_DOMAIN'] = $YM_DOMAIN;
        }
        if ($SYSTEM_NAME != NULL) {
            $new_config['SYSTEM_NAME'] = $SYSTEM_NAME;
        }
        if ($LOGO_URL != NULL) {
            $new_config['LOGO_URL'] = $LOGO_URL;
        }

        //级别配置
        $LEVEL_NUM = count($LEVEL_NAME); //级别数直接计算得来
        if ($LEVEL_NAME != NULL) {
            $NEW_LEVEL_NAME = [];
            foreach( $LEVEL_NAME as $k => $v ){
                $NEW_LEVEL_NAME[$k+1] = $v;
            }
            
            $LEVEL_NAME = $NEW_LEVEL_NAME;
            $new_config['LEVEL_NAME'] = $NEW_LEVEL_NAME;
            $new_config['LEVEL_NUM'] = $LEVEL_NUM;
        }
        //系统更新
        if ( $SYSTEM_UPDATE != NULL ) {
            $new_config['SYSTEM_UPDATE'] = $SYSTEM_UPDATE;
        }

        //系统配置
        if ($IS_SUBMIT_ID_CARD_IMG != NULL) {
            $new_config['IS_SUBMIT_ID_CARD_IMG'] = $IS_SUBMIT_ID_CARD_IMG;
        }
        if ($MONEY_COUNT_WAY != NULL) {
            $new_config['MONEY_COUNT_WAY'] = $MONEY_COUNT_WAY;
        }
        
        $new_config['IS_AUDITED'] = isset($IS_AUDITED)?1:0;


        //公众号配置
        if ($APP_ID != NULL) {
            $new_config['APP_ID'] = $APP_ID;
        }
        if ($APP_SECRET != NULL) {
            $new_config['APP_SECRET'] = $APP_SECRET;
        }

        //消息配置
        if ($SH_MB != NULL) {
            $new_config['SH_MB'] = $SH_MB;
        }
        if ($SQ_MB != NULL) {
            $new_config['SQ_MB'] = $SQ_MB;
        }
        if ($NEW != NULL) {
            $new_config['ORDER_MB']['NEW'] = $NEW;
        }
        if ($CANCLE != NULL) {
            $new_config['ORDER_MB']['CANCLE'] = $CANCLE;
        }
        if ($AUDIT != NULL) {
            $new_config['ORDER_MB']['AUDIT'] = $AUDIT;
        }
        if ($MONEY_MB != NULL) {
            $new_config['MONEY_MB'] = $MONEY_MB;
        }
        if ($UPGRADE_APPLY_MB != NULL) {
            $new_config['UPGRADE_APPLY_MB'] = $UPGRADE_APPLY_MB;
        }
        if( $UPGRADE_PASS_MB != NULL ) {
            $new_config['UPGRADE_PASS_MB'] = $UPGRADE_PASS_MB;
        }
        if( $EBusinessID != NULL ) {
            $new_config['kdnapi']['EBusinessID'] = $EBusinessID;
        }
        if( $AppKey != NULL ) {
            $new_config['kdnapi']['AppKey'] = $AppKey;
        }
//      if( isset($EBusinessID) || isset($AppKey) ){
//          $new_config['kdnapi']['EBusinessID'] = $EBusinessID;
//          $new_config['kdnapi']['AppKey'] = $AppKey;
//      }

        //微信支付配置
        if ($MCHID != NULL) {
            $new_config['WX_PAY_CONFIG']['MCHID'] = $MCHID;
        }
        if ($KEY != NULL) {
            $new_config['WX_PAY_CONFIG']['KEY'] = $KEY;
        }

//        //功能模块配置有bug，已被移到单独方法提交 edit by qjq 2018-1-30
//        $FUNCTION_MODULE['MONEY'] = $FUNCTION_MODULE['MONEY']==1?1:0;
//        $FUNCTION_MODULE['INTEGRAL_SHOP'] = $FUNCTION_MODULE['INTEGRAL_SHOP']==1?1:0;
//        $FUNCTION_MODULE['MALL_SHOP'] = $FUNCTION_MODULE['MALL_SHOP']==1?1:0;
//        $FUNCTION_MODULE['MARKET'] = $FUNCTION_MODULE['MARKET']==1?1:0;
//        $FUNCTION_MODULE['GW'] = $FUNCTION_MODULE['GW']==1?1:0;
//        $FUNCTION_MODULE['TEAM'] = $FUNCTION_MODULE['TEAM']==1?1:0;
//        $FUNCTION_MODULE['STOCK'] = $FUNCTION_MODULE['STOCK']==1?1:0;
//
//        $new_config['FUNCTION_MODULE'] = $FUNCTION_MODULE;
        //额外的配置
//        $FUNCTION_MODULE['LOAD_EXT_CONFIG'] = 'extra';

        //过滤不适合的指令
        if (!empty($SYSTEM_UPDATE)) {
            $SYSTEM_UPDATE_STR = substr($SYSTEM_UPDATE, 0, 8); //一般为git pull
            //只能以这个为开头
            if ($SYSTEM_UPDATE_STR != 'git pull') {
                $result = [
                    'code' => 3,
                    'msg' => '更新指令只能以git pull开头',
                ];
                $this->success($result['msg']);
                return;
            }
        }

        $OLD_LEVEL_NAME = C('LEVEL_NAME');
        $new_level_name_arr = array_diff($LEVEL_NAME, $OLD_LEVEL_NAME);

        //如果修改了级别配置，现有代理的级别也需要更改
        if (!empty($LEVEL_NAME) && ($LEVEL_NUM != C('LEVEL_NUM') || !empty($new_level_name_arr))) {
            $result = $User->update_level_name($LEVEL_NAME);

            if ($result['code'] != 1) {
                $this->success($result['msg']);
                return;
            }
        }

        //是否测试模式
        if($IS_TEST != NULL){
            $new_config['IS_TEST'] = $IS_TEST==1?1:0;
        }

        
//
//        //返利配置
//        $OPEN = I('OPEN');
//        $ORDER = I('ORDER');
//        $MONEY = I('MONEY');
//        $ONCE = I('ONCE');
//
//        $REBATE = array(
//            'OPEN' => (int)$OPEN,
//            'ORDER' => (int)$ORDER, //平级推荐订单返利开启/关闭
//            'MONEY' => (int)$MONEY, //平级推荐充值返利开启/关闭
//            'ONCE' => (int)$ONCE, //低推高一次性返利开启/关闭
//        );
//        $new_config['REBATE'] = $REBATE;
        

        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }
    
    //获取某级别是否存在代理
    public function get_level_exist() {
        if (!IS_AJAX) {
            return FALSE;
        }

        $level = I('level');

        if (empty($level) || !is_numeric($level)) {
            $result = [
                'code' => 2,
                'msg' => '提交级别必须为数字',
            ];
            echo $this->ajaxReturn($result);
        }

        $condition = [
            'level' => ['EGT', $level],
        ];

        $dis_info = M('distributor')->field('id')->where($condition)->find();

        if (!empty($dis_info)) {
            $result = [
                'code' => 3,
                'msg' => '该级别及其以下级别还有代理，无法删除！',
            ];
            echo $this->ajaxReturn($result);
        }

        $result = [
            'code' => 1,
            'msg' => '可删除！',
        ];
        echo $this->ajaxReturn($result);
    }

    //修改config配置
    private function update_config($new_config = [],$filename='') {

        if (empty($new_config)) {
            $return_result = [
                'code' => 2,
                'msg' => '没有新的提交'
            ];
            return $return_result;
        }
        
        if( empty($filename) ){
            $filename = 'config.php';
        }
        
        //文件路径地址
//        $path =  'App/Conf/text.php';//测试文本
        $path = 'App/Conf/'.$filename; //正式
        
        if (file_exists($path)) {
            $return_result['file_exists'] = '存在';
        }
        if (is_writable($path)) {
            $return_result['is_writable'] = '可写';
        }

        //读取配置文件,
        $file = include $path;

//        print_r($file);return;
        //合并数组，相同键名，后面的值会覆盖原来的值
        $res = array_merge($file, $new_config);

        //print_r($res);return;
        //数组循环，拼接成php文件
        $str = '<?php' . "\n" . ' return array(' . "\n";

        //config配置数组目前最多三维
        foreach ($res as $key => $value) {
            // '\'' 单引号转义
            if (is_array($value)) {
                $new_str = '   \'' . $key . '\'' . '=> array(' . "\n";

                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $new_str2 = '       \'' . $k . '\'' . '=> array(' . "\n";
                        foreach ($v as $kk => $vv) {
                            $new_str2 .= '          \'' . $kk . '\'' . '=>' . '\'' . $vv . '\'' . ',' . "\n";
                        }
                        $new_str2 .= '              ),' . "\n";
                        $new_str .= $new_str2;
                    } else {
                        $new_str .= '           \'' . $k . '\'' . '=>' . '\'' . $v . '\'' . ',' . "\n";
                    }
                }
                $new_str .= '   ),' . "\n";
                $str .= $new_str;
            } else {
                $str .= '   \'' . $key . '\'' . '=>' . '\'' . $value . '\'' . ',' . "\n";
            }
//            print_r($str);
        };
        $str .= "\n" . '); ?>';

        //print_r($str);
        //return;
        //写入文件中,更新配置文件
        if (file_put_contents($path, $str)) {
            $return_result['code'] = 1;
            $return_result['msg'] = '保存成功！';
        } else {
            $return_result['code'] = 3;
            $return_result['msg'] = '保存失败！';
        }
        //print_r($return_result);
        return $return_result;
    }
    
    //修改额外配置
    public function update_extra(){
        
        $all_info = I();
        
        
        $return_result = $this->update_config($all_info,'extra.php');
        
        
        if( $return_result['code'] == 1 ){
            $this->add_active_log('修改网站配置成功');
            $this->success('修改网站配置成功');
        }
        else{
            $this->error($return_result['msg']);
        }
    }
    
    

    //发布更新
    public function replace() {
        $SYSTEM_UPDATE = C('SYSTEM_UPDATE');
        
        if( empty($SYSTEM_UPDATE) ){
            $new_config['SYSTEM_UPDATE'] = 'git pull origin '.C('DB_NAME');
            $this->update_config($new_config);
        }
        
        $this->display();
    }

    //更新操作
    public function run_replace_ajax() {

//        if (!IS_AJAX) {
//            return FALSE;
//        }


        $SYSTEM_UPDATE = C('SYSTEM_UPDATE');

        if (empty($SYSTEM_UPDATE)) {
            $SYSTEM_UPDATE = 'git pull';
        }


        $res = exec($SYSTEM_UPDATE);
        $result = [
            'code' => 1,
            'msg' => '返回结果：  '.$res,
            'excu' => $res,
        ];
        $this->add_active_log('操作系统更新');
        $this->ajaxReturn($result);
    }

    //清空缓存
    public function clear_cache() {
        $this->display();
    }

    //提交清空缓存
    public function clear_cache_ajax() {

        if (!IS_AJAX) {
            return FALSE;
        }

        $cache = I('cache');
        $data = I('data');
        $logs = I('logs');
        $temp = I('temp');

        $runtime_path = 'App/Runtime'; //缓存位置

        $cache_path = $runtime_path . '/Cache';
        $data_path = $runtime_path . '/Data';
        $logs_path = $runtime_path . '/Logs';
        $temp_path = $runtime_path . '/Temp';

        $exec_res = [];
        if ($cache == 1 && file_exists($cache_path)) {
            $exec_res['cache'] = $this->del_dir($cache_path);
            $this->add_active_log('清除Cache(缓存)');
        }

        if ($data == 1 && file_exists($data_path)) {
            $exec_res['data'] = $this->del_dir($data_path);
            $this->add_active_log('清除Date(数据)');
        }

        if ($logs == 1 && file_exists($logs_path)) {
            $exec_res['logs'] = $this->del_dir($logs_path);
            $this->add_active_log('清除Logs(日志)');
        }

        if ($temp == 1 && file_exists($temp_path)) {
            $exec_res['temp'] = $this->del_dir($temp_path);
            $this->add_active_log('清除Temp(模板)');
        }
        

        $result = [
            'code' => 1,
            'msg' => '更新成功！',
            'exec' => $exec_res,
        ];
        $this->ajaxReturn($result);
    }

    //删除文件夹
    private function del_dir($dir) {
//        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
//               $str = "rmdir /s/q " . $dir;
//        } else {
//               $str = "rm -Rf " . $dir;
//        }
//        $exec_res = 'succ';
//        $exec_res = exec($str);
//        return $exec_res;
        //先删除目录下的文件： 
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->del_dir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹： 
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    //获取缓存信息
    public function get_runtime_info() {

        if (!IS_AJAX) {
            return FALSE;
        }


        $runtime_path = 'App/Runtime'; //缓存位置

        $cache = $runtime_path . '/Cache';
        $data = $runtime_path . '/Data';
        $logs = $runtime_path . '/Logs';
        $temp = $runtime_path . '/Temp';


        $cache_size = $data_size = $logs_size = $temp_size = 0;

        if (file_exists($cache)) {
            $cache_size = $this->getDirSize($cache);
            $cache_size = $this->getRealSize($cache_size);
        }

        if (file_exists($data)) {
            $data_size = $this->getDirSize($data);
            $data_size = $this->getRealSize($data_size);
        }

        if (file_exists($logs)) {
            $logs_size = $this->getDirSize($logs);
            $logs_size = $this->getRealSize($logs_size);
        }

        if (file_exists($temp)) {
            $temp_size = $this->getDirSize($temp);
            $temp_size = $this->getRealSize($temp_size);
        }
        
        
        $result = [
            'code' => 1,
            'msg' => '',
            'info' => [
                'cache' => [
                    'file_exists' => file_exists($cache),
                    'is_writable' => is_writable($cache),
                    'size' => $cache_size,
                ],
                'data' => [
                    'file_exists' => file_exists($data),
                    'is_writable' => is_writable($data),
                    'size' => $data_size,
                ],
                'logs' => [
                    'file_exists' => file_exists($logs),
                    'is_writable' => is_writable($logs),
                    'size' => $logs_size,
                ],
                'temp' => [
                    'file_exists' => file_exists($temp),
                    'is_writable' => is_writable($temp),
                    'size' => $temp_size,
                ],
            ],
        ];

        $this->ajaxReturn($result);
    }

    // 获取文件夹大小  
    public function getDirSize($dir) {
        $handle = opendir($dir);
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += $this->getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }

    // 单位自动转换函数  
    private function getRealSize($size) {
        if( empty($size) ){
            return '0 B';
        }
        
        $kb = 1024;         // Kilobyte  
        $mb = 1024 * $kb;   // Megabyte  
        $gb = 1024 * $mb;   // Gigabyte  
        $tb = 1024 * $gb;   // Terabyte  

        if ($size < $kb) {
            return $size . " B";
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }
    }

    //设置后台样式页面
    public function system_style() {
        $webset = M('website_set');
        
        //获取总后台样式
        $radmin_style = $webset->where(array('name'=>'radmin_style'))->field('value')->find();
        
        //获取代理后台样式
        $admin_style = $webset->where(array('name'=>'admin_style'))->field('value')->find();
        
        //获取总后台登录样式
        $radmin_login = $webset->where(array('name'=>'radmin_login'))->field('value')->find();
        
//      var_dump($radmin_style['value']);
//      var_dump($admin_style['value']);
//      var_dump($radmin_login['value']);die;
        
        
        
        $this->radmin_style=$radmin_style['value'];
        $this->radmin_login=$radmin_login['value'];
        $this->admin_style=$admin_style['value'];
        $this->display();
    }
    
    //上传logo
    public function upload(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array( 'png'); // 设置附件上传类型

        $upload->savePath = './upload/system_logo/';// 设置附件上传目录
        $upload->saveRule = 'system_logo';

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
//      $upload->subDir = 'system_logo/';
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result_msg = "";
        if(empty($info)){
            $result_msg = "上传格式不正确,上传失败";
        }else{
            $result_msg = "上传成功";
        }
        $result = [
          'code' => 0,
          'msg' => $result_msg,
          'src' => $image,
        ];
        $this->ajaxReturn($result);
    }
    
    //上传前台logo
    public function index_logo_upload(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array( 'png','jpg','jpeg','gif'); // 设置附件上传类型

        $upload->savePath = './upload/system_logo/';// 设置附件上传目录
        $upload->saveRule = 'index_logo';

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
//      $upload->subDir = 'system_logo/';
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        if(empty($info)){
            $result_msg = "上传格式不正确,上传失败";
        }else{
	        //删除旧logo
	        $logo_img = '/upload/system_logo/index_logo';
	        foreach ($upload->allowExts as $k => $v) {
	        	if($info[0]['extension'] != $v){
	        		unlink($this->logo_upload_dir.$logo_img.'.'.$v);
	        	}
	        }
            $result_msg = "上传成功";
        }
        $result = [
          'code' => 0,
          'msg' => $result_msg,
          'src' => $image,
        ];
        $this->ajaxReturn($result);
    }
    
    //上传mp文本
    public function upload_mp(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('txt'); // 设置附件上传类型

        $upload->savePath = './';// 设置附件上传目录
        $upload->saveRule = I('mp_file');

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result = [
          'code' => 0,
          'msg' => '上传成功',
          'src' => $image
        ];
        $this->ajaxReturn($result);
    }
    
    //数据库版自定义授权书
    public function certificate_set(){
      $certificate = M('distributor_certificate');

      $cavconfig = F('certificate_config');
      if(empty($cavconfig)){
          $cavconfig = $certificate->field('name,value')->select();
          F('certificate_config',$cavconfig);
      }else{
          $cavconfig = F('certificate_config');
      }

      $bgImg = $certificate->where(['name' => 'bgImg'])->find();
      $bgImg = $bgImg['value'];
      $bgImg = stripslashes($bgImg);
      
      $data = array();
      foreach($cavconfig as $v){
        $data[$v['name']][] = $v['value'];
        
      }
      $cavconfig = json_encode($data);
      $img_show = 1;
      
      if(empty($bgImg)){
        $img_show = 0;
      }
      
      $this->bgImg = $bgImg;
      $this->img_show = $img_show;
      $this->cavconfig =$cavconfig;
      $this->add_active_log;
      $this->display();
    }
    
    //Config版自定义授权书
//  public function certificate_set(){
//    $cavconfig = C('CAVCONFIG');
//    $bgImg = $cavconfig['bgImg'];
//    
//    $this->bgImg = $bgImg;
//    $this->cavconfig = json_encode($cavconfig);
//    $this->display();
//  }
    
    //保存授权书的参数
    public function certificate_save(){
      if(!IS_AJAX){
        return FALSE;
      }
      $certificate = M('distributor_certificate');

      $cavconfig = F('certificate_config');
      if(!empty($cavconfig)){
          F('certificate_config',null);
      }
      //接收处理转义字符
      $cavconfig = I('cavconfig');
      $cavconfig = stripslashes($cavconfig);
      $cavconfig = preg_replace("/&quot;/", "\"", $cavconfig);
      
      $cavconfig = json_decode($cavconfig,true);
      
      $result = null;
      $data = array();
      
      foreach($cavconfig as $k => $v){
          $data['name'] = $k;
          $data['value'] = json_encode($v);
          
          $data['value'] = preg_replace("/^\"/", "", $data['value']);
          $data['value'] = preg_replace("/\"$/", "", $data['value']);
          
          //查询是否存在
          $exsit = $certificate -> where(array('name' => $k))->field('id')->find();
          //存在就更新，不存在就添加
          if(empty($exsit)){
            $result = $certificate->add($data);
          }else{
            $result = $certificate->where(array('name' => $data['name']))->save($data);
          }
      }
      
      $result = [
        'code'=>1,
        'msg'=>"修改成功"
      ];
      $this->add_active_log;
      $this->ajaxReturn($result);
    }
    
    //隐藏文本
    public function hide_text(){
      if(!IS_AJAX){
        return FALSE;
      }
      $str = I('txt');
      $this->add_active_log;
      $this->ajaxReturn(asterisk($str));
    }
    
    //上传授权书
    public function upload_certificate(){
      
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小 3M
        $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型

        $upload->savePath = './upload/';// 设置附件上传目录
//      $upload->saveRule = 'certificate';

        $upload->uploadReplace = true; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
        $upload->autoSub = true;    //是否以子目录方式保存
        $upload->subType = 'custom';  //可以设置为hash或date
        $upload->subDir = "certificate/";
        $upload->dateFormat = 'Ymd';
        $upload->upload();
        $info = $upload->getUploadFileInfo();
        $image = substr($info[0]['savepath'], 1) . $info[0]['savename'];
        $result = [
          'code' => 0,
          'msg' => '上传成功',
          'src' => $image
        ];
        $this->ajaxReturn($result);
    }
    
    //选择后台页面样式
    public function change_system_style(){
      $style_url = I('style_url');
      $style_name = I('style_name');
      
      if(empty($style_url)||empty($style_name)){
        return $this->error('获取参数失败！');
      }
      
      $webset = M('website_set');
      
      $exsit = $webset-> where(['name'=> $style_name])-> field('id')-> find();
      
      $data['name'] = $style_name;
      $data['value'] = $style_url;
      $result = null;
      
      if(empty($exsit)){
        $result = $webset->add($data);
      }else{
        $result = $webset->where(['name'=>$style_name])-> save($data);
      }
      
      $this->success('修改成功！');
}
    //网站配置的返利配置
    public function set_rebate_config(){
        //返利配置
        $OPEN = I('OPEN');
        $ORDER = I('ORDER');
        $MONEY = I('MONEY');
        $ONCE = I('ONCE');
        $SAME_DEVELOPMENT=I('SAME_DEVELOPMENT');
        $DEVELOPMENT=I('DEVELOPMENT');
        $PERSONAL=I('PERSONAL');
        $ORDINARY_TEAM = I('ORDINARY_TEAM');
//        $CLICK_TEAM_REBATE=I('CLICK_TEAM_REBATE');
        $CLICK_TEAM_REBATE=1;

        $REBATE['OPEN']=isset($OPEN)?$OPEN:'0';
        $REBATE['ORDER']=isset($ORDER)?$ORDER:'0';
        $REBATE['MONEY']=isset($MONEY)?$MONEY:'0';
        $REBATE['ONCE']=isset($ONCE)?$ONCE:'0';
        $REBATE['SAME_DEVELOPMENT']=isset($SAME_DEVELOPMENT)?$SAME_DEVELOPMENT:'0';
        $REBATE['DEVELOPMENT']=isset($DEVELOPMENT)?$DEVELOPMENT:'0';
        $REBATE['PERSONAL']=isset($PERSONAL)?$PERSONAL:'0';
        $REBATE['ORDINARY_TEAM']=isset($ORDINARY_TEAM)?$ORDINARY_TEAM:'0';
        $REBATE['CLICK_TEAM_REBATE']=isset($CLICK_TEAM_REBATE)?$CLICK_TEAM_REBATE:'0';
//        $REBATE = array(
//            'OPEN' => (int)$OPEN,
//            'ORDER' => (int)$ORDER, //平级推荐订单返利开启/关闭
//            'MONEY' => (int)$MONEY, //平级推荐充值返利开启/关闭
//            'ONCE' => (int)$ONCE, //低推高一次性返利开启/关闭
//        );
        $new_config['REBATE'] = $REBATE;

        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }

    //网站配置--功能模块配置
    public function set_function_module_config(){
        //功能模块
        $FUNCTION_MODULE = I('FUNCTION_MODULE');
        $FUNCTION_MODULE['MONEY'] = $FUNCTION_MODULE['MONEY']==1?1:0;
        $FUNCTION_MODULE['INTEGRAL_SHOP'] = $FUNCTION_MODULE['INTEGRAL_SHOP']==1?1:0;
        $FUNCTION_MODULE['MALL_SHOP'] = $FUNCTION_MODULE['MALL_SHOP']==1?1:0;
        $FUNCTION_MODULE['MARKET'] = $FUNCTION_MODULE['MARKET']==1?1:0;
        $FUNCTION_MODULE['GW'] = $FUNCTION_MODULE['GW']==1?1:0;
        $FUNCTION_MODULE['TEAM'] = $FUNCTION_MODULE['TEAM']==1?1:0;
        $FUNCTION_MODULE['STOCK'] = $FUNCTION_MODULE['STOCK']==1?1:0;
        $FUNCTION_MODULE['BOSS_ORDER'] = $FUNCTION_MODULE['BOSS_ORDER']==1?1:0;
        $FUNCTION_MODULE['STOCK_ORDER'] = $FUNCTION_MODULE['STOCK_ORDER']==1?1:0;
        $FUNCTION_MODULE['ORDER_FORMAT'] = $FUNCTION_MODULE['ORDER_FORMAT']==1?1:0;
        $FUNCTION_MODULE['SHOP_IN_SHOP'] = $FUNCTION_MODULE['SHOP_IN_SHOP']==1?1:0;
        $FUNCTION_MODULE['DEPOT'] = $FUNCTION_MODULE['DEPOT']==1?1:0;

        $ORDER_SHIPPING=I('ORDER_SHIPPING');
        $SHIPPING_REDUCE_WAY=I('SHIPPING_REDUCE_WAY');
        $new_config['ORDER_SHIPPING']=isset($ORDER_SHIPPING)?$ORDER_SHIPPING:'0';
        $new_config['SHIPPING_REDUCE_WAY']=isset($SHIPPING_REDUCE_WAY)?$SHIPPING_REDUCE_WAY:'0';

        //额外的配置
        $FUNCTION_MODULE['LOAD_EXT_CONFIG'] = 'extra';

        $new_config['FUNCTION_MODULE'] = $FUNCTION_MODULE;
        $result = $this->update_config($new_config);

        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
//        print_r($result);
        $this->success('保存成功');
    }

    //网站配置的品牌商城的返利配置
    public function set_mall_rebate_config(){
        //返利配置
        $OPEN = I('OPEN');
        $ORDER = I('ORDER');
        $IS_OPEN = I('IS_OPEN');
        $MALL_REFUND_PAY_TYPE = I('MALL_REFUND_PAY_TYPE');

        $MALL_REBATE['OPEN']=isset($OPEN)?$OPEN:'0';
        $MALL_REBATE['ORDER']=isset($ORDER)?$ORDER:'0';
        $MALL_REFUND['IS_OPEN']=isset($IS_OPEN)?$IS_OPEN:'0';
        $MALL_REFUND['MALL_REFUND_PAY_TYPE']=isset($MALL_REFUND_PAY_TYPE)?$MALL_REFUND_PAY_TYPE:'0';
        $new_config['MALL_REBATE'] = $MALL_REBATE;
        $new_config['MALL_REFUND'] = $MALL_REFUND;
        $result = $this->update_config($new_config);
        $result['submit'] = $new_config;
        $this->add_active_log('编辑网站配置信息');
        $this->success('保存成功');
    }

    //日志管理
    public function log_view(){
        //读取文件
        $log_path = 'log';
        $list= $this->read_dir_queue( $log_path );
        $count=count($list);
        $page_num=20;
        $p=I('p');
        if($p == 1 || empty($p)){
            $start=0;
            $end=$page_num;
        }else{
            $start=($p-1)*$page_num;
            $end = $p*$page_num;
        }
        $row=array_slice($list,$start,$end);
        $this->count = $count;
        $this->p = $p;
        $this->limit = $page_num;
        $this->list=$row;
        $this->display();
    }

    //队列方式获取文件名称
    function read_dir_queue($dir){
        $files=array();
        $queue=array($dir);
        while($data=each($queue)){
            $path=$data['value'];
            if(is_dir($path) && $handle=opendir($path)){
                while($file=readdir($handle)){
                    if($file=='.'||$file=='..')
                    {
                        continue;
                    }
                   $real_path=$path.'/'.$file;
                    if (!is_dir($real_path)){
                        $name=$real_path;
                        $is_read=is_readable($real_path);
                        $is_write=is_writeable($real_path);
                        $size=filesize($real_path);
                        $arr=[
                            'name'=>$name,
                            'is_read'=>$is_read,
                            'is_write'=>$is_write,
                            'size'=> round($size/1024,2) ,
                        ];
                        $files[] = $arr;
                    }else{
                        $queue[] = $real_path;

                    }
                }
            }
            closedir($handle);
        }
        krsort($files);
        foreach ($files as $key=>$value){
            $files[$key]['name']=substr($value['name'],4);
        }
        return $files;
    }

    //检查文件的大小
    public function get_file_size(){
        $file_path=trim(I('file_path'));
        $files='log/'.$file_path;
        $file_size=filesize($files);
        $size= round($file_size/1024,2);
        //最大的限制(单位:kb)
        $size_limit=1024;
        if($size > $size_limit){
            $res=[
                'code'=>'2',
                'msg'=>'文件太大，请下载查看',
            ];
        }else{
            $res=[
                'code'=>'1',
                'msg'=>'文件太大，请下载查看',
                'file_path'=>$file_path,
            ];
        }
        $this->ajaxReturn($res);
    }

    //查看日志
    public function open_content(){
        $file_path=trim(I('file_path'));
        $files='log/'.$file_path;
        $file=$this->see_content($files);
        $this->file=$file;
        $this->display();
    }
    //获取日志文件内容
    public function see_content($file_name){
        $file = fopen($file_name, "r");
        $user=array();
        $i=0;
        //输出文本中所有的行，直到文件结束为止。
        while(! feof($file))
        {
            $user[$i]= fgets($file);//fgets()函数从文件指针中读取一行
            $i++;
        }
        fclose($file);
        $user=array_filter($user);
        return $user;

    }

}

?>