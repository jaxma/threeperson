<?php

/**
 * 	topos经销商管理系统
 */
class PhotoAction extends CommonAction {

    private $cat_model;
    private $company_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->cat_model = M('cat');
        $this->company_model = M('company');
    }
    
    //获取表名
    private function get_model(){
        
        return 'Photo';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '摄影';
    }
    
    //产品信息列表
    public function index() {
        $model_name = $this->get_model();
        
        $count = D($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('time desc')->limit($limit)->select();
            foreach ($list as $k => $v) {
                $this_cat2 = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->field('name')->find();
                if($this_cat2){
                    $list[$k]['cat2'] = $this_cat2['name'];
                    $this_cat1 = $this->cat_model->where('status = 1 and id = '.$this_cat2['pid'])->field('name')->find();
                    $list[$k]['cat1'] = $this_cat1['name'];
                }else{
                    $this_cat1 = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->field('name')->find();
                    $list[$k]['cat1'] = $this_cat1['name'];
                }
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

    //摄影分类
    public function cat(){
        import('Lib.Action.Team', 'App');
        $Team = new Team();

        $count =  $this->cat_model->count('id');
        if ($count > 0) {
            $this->assign('list', 1);
        }else{
            $this->assign('list', '');
        }
        $this->display();
    }
    
    
    //公司详情主页
    public function company_con1() {
        $type = array('1');
        $where['type'] = ['in', $type];
        $count = $this->company_model->where($where)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->company_model->where($where)->order('type asc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->type = 1;
        $this->display('company_con');
    }
    //公司详情主页
    public function company_con2() {
        $type = array('2');
        $where['type'] = ['in', $type];
        $count = $this->company_model->where($where)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = $this->company_model->where($where)->order('type asc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
            $this->count=$count;
        }
        $this->p=I('p');
        $this->limit=$page_num;
        $this->type = 2;
        $this->display('company_con');
    }
    //编辑页面
    public function company() {
        $id = I('id');
        $type = I('type');
        $this->company = $this->company_model->where('id = '.$id)->find();
        $this->id = $id;
        $this->type = $type;
        $this->display();
    }

    public function company_update() {
        
        $id = I('id');
        $type = I('type');

        $content = I('post.content');
        $name = I('post.name');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
        $status = I('post.status');
        $data = array(
            'content' => $content,
            'name' => $name,
            'status' => $status,
            'time' => time()
        );
        $res = $this->company_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            if($type == 1){
                $this->success("操作成功",__URL__.'/'.'company_con1');
            }elseif($type == 2){
                $this->success("操作成功",__URL__.'/'.'company_con2');
            }else{
                $this->success("操作成功",U("Photo/company",array("id"=>$id)));
            }
        }
    }

    public function company_add() {
        
        $type = I('type');
        $this->assign('type',$type);
        $this->display();
    }

    public function company_insert() {
        
        $content = I('post.content');
        $name = I('post.name');
        $type = I('post.type');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
//        $image = $this->upload();
        $status=I('post.status');
        $data = array(
            'content' => $content,
            'name' => $name,
            'status' => $status,
            'type' => $type,
            'time' => time()
        );
        $res = $this->company_model->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            if($type == 1){
                $this->success("操作成功",__URL__.'/'.'company_con1');
            }elseif($type == 2){
                $this->success("操作成功",__URL__.'/'.'company_con2');
            }else{
                $this->success("操作成功",__URL__.'/'.'company');
            }
        } else {
            $this->error("操作失败");
        }
    }

    //删除公司信息
    public function delete_con() {
        
        $id = I('id');
        $res = $this->company_model->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //产品分类
    public function templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $cats = $this->cat_model->order('id asc')->select();
        //一级分类
        foreach ($cats as $cat) {
            if ($cat['pid'] == 0) {
                $one[] = $cat;
                $one_id[] = $cat['id'];
            }
        }
        //二级分类关联一级分类
        foreach ($cats as $cat) {
            if (in_array($cat['pid'], $one_id)) {
                $two[$cat['pid']] = $cat;
                $two_id[] = $cat['id'];
            }
        }
        //三级分类关联二级分类
        // foreach ($cats as $cat) {
        //     if (in_array($cat['pid'], $two_id)) {
        //         $three[$cat['pid']] = $cat;
        //     }
        // }

        //判断是否有子分类
        foreach ($one as $k => $v) {
            $one[$k]['has_child'] = 0;
            if (isset($two[$v['id']])) {
                $one[$k]['has_child'] = 1;
            }
        }
        // foreach ($two as $k => $v) {
        //     $two[$k]['has_child'] = 0;
        //     if (isset($three[$v['id']])) {
        //         $two[$k]['has_child'] = 1;
        //     }
        // }
        $result = [
            'one' => $one,
            'two' => $two,
            // 'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);

    }
    //获取产品子分类
    public function get_son_templet_category()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id=I('id');
        $pid = I('pid');
        //全部
        if ($pid == -1) {
            $one_cats = $this->cat_model->order('id asc')->where(['pid' => 0])->select();
            foreach ($one_cats as $cat) {
                $one_ids[] = $cat['id'];
            }
            $where['pid'] = ['in', $one_ids];
        } else {
            $where['pid'] = $pid;
        }

        //二级分类
        $two = $this->cat_model->order('id asc')->where($where)->select();
        foreach ($two as $v) {
            $two_ids[] = $v['id'];
        }
        //三级分类关联二级分类
        $cats = $this->cat_model->order('id asc')->where(['pid' => ['in', $two_ids]])->select();
        foreach ($cats as $cat) {
            $three[$cat['pid']][] = $cat;
        }
        //判断是否有子分类
        foreach ($two as $k => $v) {
            $two[$k]['has_child'] = 0;
            if (isset($three[$v['id']])) {
                $two[$k]['has_child'] = 1;
            }
        }
        $result = [
            'two' => $two,
            'three' => $three
        ];
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }

    //改变状态---开启或者关闭
    public function set_status()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id = I('id');
        $status = I('status');

        //  $id=6;
        //   $status=1;
        if(empty($id)){
            $return_result = [
                'code' => 2,
                'msg' => 'id获取失败',

            ];
            $this->ajaxReturn($return_result);
        }
        if(empty($status)){
            $return_result = [
                'code' => 3,
                'msg' => '状态获取失败',

            ];
            $this->ajaxReturn($return_result);
        }
        elseif ($status == '1') {
            $status = 0;
        } elseif($status == '2'){
            $status = 1;
        }
        $this->cat_model->where(array('id' => $id))->save(['status' => $status]);
        // $this->model->where(array('category_id'=>$id))->save(['active'=>$status]);
        $pid_info_one = $this->cat_model->where(array('pid' => $id))->select();
        if ($pid_info_one) {
            foreach ($pid_info_one as $v) {
                $ids = $v['id'];
                $this->cat_model->where(array('id' => $ids))->save(['status' => $status]);
                // $this->model->where(array('category_id'=>$ids))->save(['active'=>$status]);
                $pid_info_two[] = $this->cat_model->where(array('pid' => $ids))->select();
            }

            if ($pid_info_two) {
                foreach ($pid_info_two as $table => $row) {
                    foreach ($row as $col) {
                        $idss[] = $col['id'];
                    }
                }
                $condition = [
                    'id' => array('in', $idss)
                ];
                $condition_two=[
                    'category_id' => array('in', $idss)
                ];
                $this->cat_model->where($condition)->save(['status' => $status]);
                // $this->model->where($condition_two)->save(['active'=>$status]);
            }

        }
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',

        ];
        $this->ajaxReturn($return_result);
    }

    //获取中文名字
    private function get_category_name()
    {

        return '摄影分类';
    }

    public function category_insert()
    {

//        $image = $this->upload();
        $image=I('post.image');

        $pid1 = trim(I('post.pid1'));
        $pid2 = trim(I('post.pid2'));
        if ($pid1 == "a" && $pid2 == "a") {
            $pid = 0;
        } elseif ($pid2 == "a" || $pid2 == '') {
            $pid = $pid1;
        } elseif ($pid1 != "a" && $pid2 != "a") {
            $pid = $pid2;
        }
        $name=trim(I('post.name'));
        if(empty($name)){
            $this->error('名称不能为空！');die;
        }
        $name_en=trim(I('post.name_en'));
        if(empty($name_en)){
            $this->error('英文名称不能为空！');die;
        }
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }
        $data = array(
            'name' => $name,
            'name_en' => $name_en,
            'image' => $image,
            'pid' => $pid,
            'sequence' => $sequence,
            'add_time' => time()
        );
        $res = $this->cat_model->add($data);
        if ($res) {
            $name = $this->get_category_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //添加分类
    public function category_add()
    {
        $p_id=I('get.p_id');
        $c_id=I('get.c_id');
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $reduction_category = M('templet_category');
        $cate = $reduction_category->select();
        $cateres = $Team->sortt($cate);
        $this->assign('cateres', $cateres);
        $this->p_id=$p_id;
        $this->c_id=$c_id;
        $this->display();
    }

    //编辑
    public function category_edit()
    {
        import('Lib.Action.Team', 'App');
        $Team = new Team();

        $id = $_GET['id'];
        $pid = $_GET['pid'];
        $row = $this->cat_model->find($id);
        $listres = $this->cat_model->select();
        $list = $Team->sortt($listres);


        $this->list = $list;
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    //获取3级列表的名称
    public function get_category_ajax()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $id = I('post.id');
//      $id=10;
        $condition = [
            'id' => $id,
        ];
        $is_two_cat = false;
        $info_one = $this->cat_model->where($condition)->find();
        $info_two_pid = $info_one['pid'];
        if ($info_two_pid != 0) {

            $info_two = $this->cat_model->where(array('id' => $info_two_pid))->find();
            $info_three_pid = $info_two['pid'];
            if ($info_three_pid != 0) {
                $info_three = $this->cat_model->where(array('id' => $info_three_pid))->find();
            } else {
                $is_two_cat = true;
            }
        }

        if ($info_two == "" && $info_three == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_two,
                'info_two' => $info_three,
                'is_two_cat' => $is_two_cat
            ];
        } elseif ($info_three == "") {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_two,
                'info_two' => $info_three,
                'is_two_cat' => $is_two_cat
            ];
        } else {
            $return_result = [
                'code' => 1,
                'msg' => '获取成功',
                'info_one' => $info_three,
                'info_two' => $info_two,
                'is_two_cat' => $is_two_cat
            ];
        }

        $this->ajaxReturn($return_result);
    }
    public function category_update()
    {
        $id = I('post.id');

        $id_info= $this->cat_model->where(array('id' => $id))->find();
        $old_image=$id_info['image'];

        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }


        $condition_one=[
            'id' => $id,
        ];
        $condition_two=[
            'pid' => $id,
        ];
        $info= $this->cat_model->where($condition_one)->find();
        $info_count=$this->cat_model->where($condition_two)->count();

        $info_pid=$info['pid'];
        $pid1 = trim(I('post.pid1'));
        $pid2 = trim(I('post.pid2'));

        if($info_pid == 0){
            if($info_count){
                if($pid1 != "a"){
                    $this->error('原分类下面还有子分类，禁止进行此操作');die;
                }
            }
        }
        if ($pid1 == "a" && $pid2 == "a"  ) {
            $pid = 0;
        } elseif ($pid2 == "a") {
            $pid = $pid1;
        } elseif ($pid1 != "a" && $pid2 != "a") {
            $pid = $pid2;
        }
        $sequence=trim(I('sequence'));
        if($sequence<0 || $sequence >9999){
            $this->error('优先级已超出指定范围');
        }
        $name=trim(I('post.name'));
        if(empty($name)){
            $this->error('名称不能为空！');die;
        }
        $name_en=trim(I('post.name_en'));
        if(empty($name_en)){
            $this->error('英文名称不能为空！');die;
        }
        $data = array(
            'name' => $name,
            'name_en' => $name_en,
            'image' => $image,
            'pid' => $pid,
            'sequence' => $sequence,
            'add_time' => time()
        );

        $res = $this->cat_model->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_category_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功");
        }

    }


    //删除
    public function category_delete()
    {

        $id = I('id');
        $model_info = $this->cat_model->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__ .  $model_info[0]['image'];
        @unlink($url);
        $res = $this->cat_model->delete($id);

        if ($res) {
            $name = $this->get_category_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //摄影图片
    //添加产品信息
    public function add() {
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();
        
        $news = I('post.disc');
        $category_id2 = I('post.category_id2');
        $many_image=I('many_image');
        $many_images=implode(',',$many_image);
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $sequence = I('post.sequence');
//        $image = $this->upload();
        $image=I('post.image');
        $isopen=I('post.isopen');
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'cat1' => trim(I('post.category_id1')),
            'cat2' => trim(I('post.category_id2')),
            'presents' => I('post.presents'),
            'news' => $news,
            'isopen' => $isopen,
            'time' => time(),
            'many_image' => $many_images,
            'sequence' => $sequence,
        );

        if(!$image || empty($image)){
             $this->error('请添加摄影封面图片后,再提交!');
        }
        
        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功',__URL__.'/'.'index');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑产品信息
    public function edit() {
        $model_name = $this->get_model();
        
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $row_image = $row['many_image'];
        $arr = explode(',', $row_image);
        //在每个前面加上__ROOT__,用在编辑时本机显示图片
        array_walk(
            $arr,
            function (&$s, $k, $prefix = '__ROOT__') {
                $s = str_pad($s, strlen($prefix) + strlen($s), $prefix, STR_PAD_LEFT);
            }
        );
        $row_arr=implode(',',$arr);
        $cat1 = $this->cat_model->where('id='.$row['cat1'])->find();
        $cat2 = $this->cat_model->where('id='.$row['cat2'])->find();
        $this->row = $row;
        $this->arr = $row_arr;
        $this->id = $id;
        $this->display();
    }

    public function update() {
        $model_name = $this->get_model();
        
        $id = I('post.id');
        $id_info=M('Photo')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }
        if(!$image || empty($image)){
             $this->error('请添加摄影封面图片后,再提交!');
        }
        
        $many_image=I('many_image');
        $many_images=implode(',',$many_image);

        $news = I('post.disc');
        $category_id2 = I('post.category_id2');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $isopen = I('post.isopen');
        $sequence = I('post.sequence');
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'cat1' => trim(I('post.category_id1')),
            'cat2' => trim(I('post.category_id2')),
            'presents' => I('post.presents'),
            'news' => $news,
            'isopen' => $isopen,
            'sequence' => $sequence,
            'many_image' => $many_images,
            'time' => time()
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'index');
        }
    }
    //删除产品信息
    public function delete() {
        $model_name = $this->get_model();
        
        $id = I('id');
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
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

}

?>