<?php

/**
 * 	topos经销商管理系统
 */
class AboutusAction extends CommonAction {

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
        
        return 'News';
    }
    
    //获取该栏目中文名字
    private function get_name(){
        
        return '新闻';
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

    public function books(){

    }
    public function designer(){

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

  



    //摄影图片
    //添加产品信息
    public function add() {
        $c_id = 2;
        $p_id = 5;
        $this->assign('c_id',$c_id);
        $this->assign('p_id',$p_id);
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