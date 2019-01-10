<?php

/**
 * 	topos经销商管理系统
 */

class ShippingAction extends CommonAction {


    //---------**************-------------

    public function index()
    {
        $template=M('shipping_goods_shipping_template');
        $way=M("shipping_way");

        $temp['status']=1;
        //查找总数
        $count = $template_list=$template->where($temp)->count();
        $page_num=20;
        
        if($count>0){
          //分页
          import('ORG.Util.Page');
          $p = new Page($count, $page_num);
          $limit = $p->firstRow . "," . $p->listRows;
          
          $template_list=$template->where($temp)->limit($limit)->order("id desc")->select();
          
          $page = $p->show(true);
          
          $i=1;
          
          foreach ($template_list as  &$template){

            $template['country']=$this->get_region_name($template['country']);
            $template['province']=$this->get_region_name($template['province']);
            $template['city']=$this->get_region_name($template['city']);
            $template['district']=$this->get_region_name($template['district']);
            $way_list=$way->where("template_id=$template[id] and is_default=1")->find();
            $template['default']=$way_list;
            $i++;
          }
          
          $this->assign("template_list",$template_list);
          $this->assign("page",$page);
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();
    }


    /* 获得地区名称 */
    public function get_region_name($region_id){
        return M("shipping_region")->where("region_id=$region_id")->getField("region_name");
    }

    /**
     * 添加模板信息
     */
    public function add(){
            $region=M("shipping_region");
            //国家列表
            $countrylist=$region->where(array('region_type'=>0))->select();
            if(C('SHIPPING_REDUCE_WAY')){
                $shipping_reduce=M('shipping_reduce')->where(['shipping_reduce_way'=>C('SHIPPING_REDUCE_WAY')])->select();
            }else{
                $shipping_reduce=M('shipping_reduce')->where(['shipping_reduce_way'=>0])->select();
            }
            $this->assign('shipping_reduce',$shipping_reduce);
            $this->assign('countrylist',$countrylist);
            //自动创建地区逻辑
            $count = M('shipping_large_area')->count('id');
            if($count == 0){
                $area_info=[
                    [
                        'large_area_id'        =>  1,
                        'large_area_name'  =>  '华东',
                        'arr_child'  =>  '25,16,31,3,17',
                        'arr_child_name'      => '上海,江苏省,浙江省,安徽省,江西省',
                    ],
                    [
                        'large_area_id'        =>  2,
                        'large_area_name'  =>  '华北',
                        'arr_child'  =>  '2,27,23,22,10,19',
                        'arr_child_name'      => '北京,天津,山西省,山东省,河北省,内蒙古自治区',
                    ],
                    [
                        'large_area_id'        =>  3,
                        'large_area_name'  =>  '华中',
                        'arr_child'  =>  '14,13,11',
                        'arr_child_name'      => '湖南省,湖北省,河南省',
                    ],
                    [
                        'large_area_id'        =>  4,
                        'large_area_name'  =>  '华南',
                        'arr_child'  =>  '6,7,4,9',
                        'arr_child_name'      => '广东省,广西壮族自治区,福建省,海南省',
                    ],
                    [
                        'large_area_id'        =>  5,
                        'large_area_name'  =>  '东北',
                        'arr_child'  =>  '18,15,12',
                        'arr_child_name'      => '辽宁省,吉林省,黑龙江省',
                    ],
                    [
                        'large_area_id'        =>  6,
                        'large_area_name'  =>  '西北',
                        'arr_child'  =>  '24,29,5,20,21',
                        'arr_child_name'      => '陕西省,新疆维吾尔自治区,甘肃省,宁夏回族自治区,青海省',
                    ],
                    [
                        'large_area_id'        =>  7,
                        'large_area_name'  =>  '西南',
                        'arr_child'  =>  '32,30,8,28,26',
                        'arr_child_name'      => '重庆,云南省,贵州省,西藏自治区,四川省',
                    ],
                    [
                        'large_area_id'        =>  8,
                        'large_area_name'  =>  '港澳台',
                        'arr_child'  =>  '33,34,35',
                        'arr_child_name'      => '香港特别行政区,澳门特别行政区,台湾',
                    ],
                    [
                        'large_area_id'        => 9,
                        'large_area_name'  =>  '海外',
                        'arr_child'  =>  '2000',
                        'arr_child_name'      => '海外',
                    ],

                ];
                M('shipping_large_area')->addAll($area_info);
            }
            $this->display();
    }

    public function insert()
    {
        if ($_POST) {

            $shipping_way = M("shipping_way");
            $template = M('shipping_goods_shipping_template');
            $template_name = trim(I('post.template_name'));
            $country = trim(I('post.country'));
            $province = trim(I('post.province'));
            $city = trim(I('post.city'));
            $district = trim(I('post.area'));
            $reduce_id=trim(I('post.reduce_id'));
            if ($template_name == '') {
                $this->error('请填写模板名称！');
                die;
            }
//            if ($country == 0 || $province == 0 || $city == 0 || $district == 0) {
//                $this->error('请选择商品所在地区！');
//                die;
//            }

            foreach ($_POST['default'] as &$default) {
                $default_first_num = trim($default['first_num']);
                $default_first_fee=trim($default['first_fee']);
                $default_continue_num = trim($default['continue_num']);
                $default_continue_fee = trim($default['continue_fee']);
                if ($default_first_num == 0 || $default_first_num == '' || !is_numeric($default_first_num)) {
                    $this->error('商品件数必须为大于0的数字');
                    die;
                }
                if($default_continue_fee == ''|| !is_numeric($default_first_fee)){
                    $this->error('商品首费价格必须为数字');die;
                }
                if ($default_continue_num == 0 || $default_continue_num == '' || !is_numeric($default_continue_num)) {
                    $this->error('商品续件数必须为大于0的数字');
                    die;
                }
                if ( $default_continue_fee == '' || !is_numeric($default_continue_fee)) {
                    $this->error('商品续费价格必须为数字');
                    die;
                }
                $default['area_name'] = "全国";
                $default['area_id'] = 1;
                $default['is_default'] = 1;
                $default['create_date'] = time();

            }
            foreach ($_POST['other'] as &$others) {
                if ($others['area_id'] == 0 || $others['area_id'] == '') {
                    $this->error('请选择商品配送区域');
                    die;
                }
                if ($others['first_num'] == 0 || $others['first_num'] == '' || !is_numeric($others['first_num'])) {
                    $this->error('商品首件价格必须为大于0的数字');
                    die;
                }
                if ( $others['first_fee'] == '' || !is_numeric($others['first_fee'])) {
                    $this->error('商品首费价格必须为数字');
                    die;
                }
                if ($others['continue_num'] == 0 || $others['continue_num'] == '' || !is_numeric($others['continue_num'])) {
                    $this->error('商品续件数必须为大于0的数字');
                    die;
                }
                if ( $others['continue_fee'] == '' || !is_numeric($others['continue_fee'])) {
                    $this->error('商品续费价格必须为数字');
                    die;
                }
                $others['is_default'] = 0;
                $others['create_date'] = time();
            }

            $shipping = I('post.shipping_way');
            $shipping_wa = implode(',', $shipping);

            $data = [
                'template_name' => $template_name,
                'country' => $country,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'is_free' => trim(I('post.is_free')),
                'price_way' => trim(I('post.price_way')),
//                'shipping_way' => $shipping_wa,
                'shipping_way' => 0,//因为只有快递一种，所以默认变为0
                'create_date' => time(),
                'reduce_id'=>$reduce_id,
            ];

            $way_id = $template->add($data);

            if ($way_id) {

                foreach ($_POST['default'] as &$default) {
                    $default['template_id'] = $way_id;
                    $default_res = $shipping_way->add($default);
                }
                foreach ($_POST['other'] as &$other) {
                    $other['template_id'] = $way_id;
                    $other_res = $shipping_way->add($other);
                }


//                if(!$default_res && !$other_res){
////                  var_dump($default_res);
////                  var_dump($other_res);
////                  die;
//                    $this->error(' 运送方式添加失败');
//                }else{
                $this->success('操作成功', __URL__ . '/' . 'index');

            } else {
                $this->error('添加失败');
            }
        }
    }

    
//查看模板信息
    public function info(){
        if($_GET['template_id']){
            $shipping_info = $this->get_shipping_info();
            $this->assign('template_row',$shipping_info['info']['template_row']);
            $this->assign('way_list',$shipping_info['info']['way_list']);
            $this->assign('reduce_info',$shipping_info['info']['reduce_info']);
            $this->display();
        }
    }
    
    public function get_shipping_info(){
        $template_id = I('template_id');
        $result = null;
        
        if(!empty($template_id)){
            $shipping_way=M("shipping_way");
            $template = M('shipping_goods_shipping_template');
            $shipping_reduce = M('shipping_reduce');
            $condition_one=[
                'template_id' => $template_id,
            ];
            $condition_two=[
                'id' => $template_id,
            ];
            $template_row=$template->where($condition_two)->find();
            $reduce_info=$shipping_reduce->where(['id'=>$template_row['reduce_id'],'shipping_reduce_way'=>C('SHIPPING_REDUCE_WAY')])->find();
            $way_list=$shipping_way->where($condition_one)->order("shipping_way")->select();
            $result = [
                'code' => 1,    
                'info' => [
                    'template_row' => $template_row,
                    'way_list' => $way_list,
                    'reduce_info' =>$reduce_info,
                ],
                'msg' => '获取成功！'
            ];
        }else{
            $result = [
                'code' => 2,
                'info' => null,
                'msg' => '获取失败！'
            ];
        }
        if(IS_AJAX){
            $this->ajaxReturn($result);
        }else{
            return $result;
        }
    }


    /**
     * ajax获取区域列表
     */
    public function getArea()
    {
        $data = M('shipping_large_area')->select();
        foreach ($data as $k => $v) {
            $data[$k]['arr_child']      = explode(',', $v['arr_child']);
            $data[$k]['arr_child_name'] = explode(',', $v['arr_child_name']);
        }
        echo json_encode($data);
    }

    public function get_city(){
        $region=M("shipping_region");
        $id=$_GET["parent"];
        $condition=[
            'parent_id' => $id,
        ];
        $city_list=$region->where($condition)->select();
        $this->ajaxReturn($city_list);
    }


    //修改模板地区
    public function template_edit(){
        $region=M("shipping_region");
        $countrylist=$region->where(array('region_type'=>0))->select();
        $template_id=I('get.template_id');
        $shipping_template_info = M('shipping_goods_shipping_template')->find($template_id);
        //运费减免
        if(C('SHIPPING_REDUCE_WAY')){
            $shipping_reduce=M('shipping_reduce')->where(['shipping_reduce_way'=>C('SHIPPING_REDUCE_WAY')])->select();
        }else{
            $shipping_reduce=M('shipping_reduce')->where(['shipping_reduce_way'=>0])->select();
        }

        $this->assign('shipping_reduce',$shipping_reduce);
        $this->assign('countrylist',$countrylist);
        $this->assign('template_id',$template_id);
        $this->assign('shipping_template_info',$shipping_template_info);
        $this->display();
    }

    public function edit(){
        $shipping_way=M("shipping_way");
        $shipping_goods_shipping_template = M('shipping_goods_shipping_template');
        $template_name=trim(I('post.template_name'));
        $country=trim(I('post.country'));
        $province=trim(I('post.province'));
        $city=trim(I('post.city'));
        $district=trim(I('post.area'));
        $template_id=I('template_id');
        $reduce_id=trim(I('reduce_id'));
        if($template_name==''){
            $this->error('请填写模板名称！');die;
        }
//        if($country == 0 || $province == 0 ||$city == 0 || $district == 0){
//            $this->error('请选择商品所在地区！');die;
//        }

        foreach ($_POST['default'] as &$default){
            $default_first_num=trim($default['first_num']);
             $default_first_fee=trim($default['first_fee']);
            $default_continue_num=trim($default['continue_num']);
            $default_continue_fee=trim($default['continue_fee']);
            if($default_first_num==0||$default_first_num==''||!is_numeric($default_first_num)){
                $this->error('商品件数必须为大于0的数字');die;
            }
            if($default_first_fee==''||!is_numeric($default_first_fee)){
                $this->error('商品首费价格必须为数字');die;
            }
            if($default_continue_num==0||$default_continue_num==''||!is_numeric($default_continue_num)){
                $this->error('商品续件数必须为大于0的数字');die;
            }
            if($default_continue_fee==''||!is_numeric($default_continue_fee)){
                $this->error('商品续费价格必须为数字');die;
            }
            $default['area_name']="全国";
            $default['area_id']=1;
            $default['is_default']=1;
            $default['create_date']=time();

        }
        foreach ($_POST['other'] as &$others){

            if($others['area_id']==0||$others['area_id']==''){
                $this->error('请选择商品配送区域');die;
            }
            if($others['first_num']==0||$others['first_num']==''||!is_numeric($others['first_num'])){
                $this->error('商品首件价格必须为大于0的数字');die;
            }
            if($others['first_fee']==''||!is_numeric($others['first_fee'])){
                $this->error('商品首费价格必须为数字');die;
            }
            if($others['continue_num']==0||$others['continue_num']==''||!is_numeric($others['continue_num'])){
                $this->error('商品续件数必须为大于0的数字');die;
            }
            if($others['continue_fee']==''||!is_numeric($others['continue_fee'])){
                $this->error('商品续费价格必须为数字');die;
            }
            $others['is_default']=0;
            $others['create_date']=time();
        }

        $shipping=I('post.shipping_way');
        $shipping_wa=implode(',',$shipping);

        $data=[
            'template_name' => $template_name,
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'is_free' => trim(I('post.is_free')),
            'price_way' => trim(I('post.price_way')),
//            'shipping_way' => $shipping_wa,
            'shipping_way' => 0,//因为只有快递一种，所以默认变为0
            'create_date' => time(),
            'reduce_id'=>$reduce_id,
        ];

        $way_id=$shipping_goods_shipping_template->where(array('id'=>$template_id))->save($data);
        if($way_id){
            $shipping_way->where(array('template_id'=>$template_id))->delete();
            foreach ($_POST['default'] as &$default){
                $default['template_id']=$template_id;


                $default_res=$shipping_way->add($default);
            }
            foreach ($_POST['other'] as &$other){

                $other['template_id']=$template_id;


               $other_res=$shipping_way->add($other);
            }


            if(!$default_res && !$other_res){
                $this->error(' 运送方式添加失败');
            }else{
                $this->success('操作成功',__URL__.'/'.'index');
            }
        }else{

            $this->error('添加失败');
        }



    }


    //编辑页面ajax显示数据
    public function get_template_edit(){
        if(!IS_AJAX){
           return FALSE;
        }

        $shipping_goods_shipping_template=M('shipping_goods_shipping_template');
        $region=M("shipping_region");

        $template_id=I('template_id');

//         $template_id = 140;
           
        $template_info=$shipping_goods_shipping_template->where(array('id'=>$template_id))->find();

        //获取货物地址信息
        $country=$template_info['country'];
        $province=$template_info['province'];
        $city=$template_info['city'];
        $district=$template_info['district'];
        $arr=array();
        array_push($arr,$country,$province,$city,$district);

        $condition_search=[
            'region_id' => array('in',$arr),
        ];
        $files='region_id,region_name';
        $condition_search_info=$region->where($condition_search)->field($files)->select();

        //运费模版---获取快递运费信息
        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $other['is_group'] = 1;

        $condition=[
            'template_id'=>$template_id,
        ];
        $info = $Order->get_shipping($condition, $other);
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '获取成功',
            'info'  =>  $template_info,
            'shop_address' => $condition_search_info,
            'shipping_way' => $info,
        ];
        $this->ajaxReturn($return_result);
    }

    //删除
    public function delete(){
        if(!empty($_GET['template_id'])&&is_numeric($_GET['template_id'])){
            $template=M('shipping_goods_shipping_template');
            $shipping_way=M('shipping_way');
            $template_id=I('template_id');

           $res=$template->where(array('id'=>$template_id))->delete();
           $del=$shipping_way->where(array('template_id'=>$template_id))->delete();
        }
        if($res){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    private function get_name()
    {

        return '运费减免';
    }
    //满减包邮优惠
    public function reduce_index(){
        $shipping_reduce=M('shipping_reduce');
        $count=$shipping_reduce->count('id');
        $page_num=20;
        if($count>0){
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            if(C('SHIPPING_REDUCE_WAY')){
                $list =$shipping_reduce->where(['shipping_reduce_way'=>1])->order('id desc')->limit($limit)->select();
            }else{
                $list =$shipping_reduce->where(['shipping_reduce_way'=>0])->order('id desc')->limit($limit)->select();
            }

            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->display();
    }

    public function reduce_add(){
        $id=trim(I('id'));
        $shipping_reduce=M('shipping_reduce');
        $info=$shipping_reduce->where(array('id'=>$id))->find();
        if($info['reduce_money'] == '0'){
            $info['reduce_money']='减免运费';
        }
        $this->info=$info;
        $this->display();
    }

    public function reduce_insert(){
        $id=trim(I('id'));
        $name=trim(I('name'));
        $type=trim(I('type'));
        $need_money=trim(I('need_money'));
        $need_num=trim(I('need_num'));
        $shipping_reduce_way=C('shipping_reduce_way');
        if(empty($id)){
            $data=[
                'name'=>$name,
                'type'=>$type,
                'need_money'=>$need_money,
                'need_num'=>$need_num,
                'shipping_reduce_way'=>$shipping_reduce_way,
                'time'=>time()
            ];
            if($type == 1){
                if($need_num == 0 ){
                    $this->error('数量为0');
                    die;
                }
            }
            if($type == 2){
                if($need_money == 0 ){
                    $this->error('金额不能为0');
                    die;
                }
            }
            if($type == 3){
                if($need_num == 0 || $need_money == 0){
                    $this->error('数量或者金额不能为0');
                    die;
                }
            }
            if(empty($shipping_reduce_way)){
                $list =M('shipping_reduce')->where(['shipping_reduce_way'=>0])->find();
                if($list){
                    $this->error('运费减免为全场使用，最多只能添加一条信息');
                    die;
                }
            }
            $res=M('shipping_reduce')->add($data);
            if($res){
                $name = $this->get_name();
                $this->add_active_log('添加' . $name . '信息');
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            //如果是数量
            if($type == '1'){
                $money=0;
                $num=$need_num;
            }
            //如果是金额
            elseif ($type == '2'){
                $money=$need_money;
                $num=0;
            }
            //如果是数量和金额
            elseif ($type == '3'){
                $money=$need_money;
                $num=$need_num;
            }
            if($type == 1){
                if($need_num == 0 ){
                    $this->error('数量为0');
                    die;
                }
            }
            if($type == 2){
                if($need_money == 0 ){
                    $this->error('金额不能为0');
                    die;
                }
            }
            if($type == 3){
                if($need_num == 0 || $need_money == 0){
                    $this->error('数量或者金额不能为0');
                    die;
                }
            }
            $data=[
                'id'=>$id,
                'name'=>$name,
                'need_money'=>$money,
                'need_num'=>$num,
                'type'=>$type,
                'shipping_reduce_way'=>C('shipping_reduce_way'),
                'time'=>time()
            ];

            $res=M('shipping_reduce')->where(array('id'=>$id))->save($data);
            if($res){
                $name = $this->get_name();
                $this->add_active_log('修改' . $name . '信息');
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
    }

    public function reduce_delete(){
        $id=trim(I('id'));
        $res=M('shipping_reduce')->where(array('id'=>$id))->delete();
        if($res){
            $name = $this->get_name();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

}

?>