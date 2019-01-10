<?php

/**
 *  topos经销商管理系统
 */
header("Content-Type: text/html; charset=utf-8");

class CatsAction extends CommonAction
{
    private $cats_model;
    public function _initialize() {
        $this->cats_model = M('cats');
    }
    
    //获取该栏目中文名字
    private function get_name(){
        return '分类';
    }
    //获取表名
    private function get_model(){
        
        return 'Cats';
    }
    
    //分类列表
    public function index()
    {
        $condition = array(
            "pid" => 0
        );
        $row = $this->cats_model->where($condition)->select();
        foreach ($row as $k => $v) {
            $row[$k]['count'] = $this->cats_model->where(array('pid' => $row[$k]['id']))->count();
        }

        $this->row = $row;
        $this->display();
    }
    //获取树形图
    public function getPtree()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $page_num = trim(I('page_num'));
        $type = trim(I('type'));
        $page_list_num = trim(I('page_list_num'));

        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        if ($type == 'pid') {
            $condition = array(
                "pid" => '0'
            );
        }

        import('Lib.Action.Cats', 'App');
        $Cats = new Cats();
        $result = $Cats->getTree($page_info, $condition, $type);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }
    //查找子类
    public function getCtree()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $id = I('post.mid');
        $page_num = trim(I('page_num'));
        $type = trim(I('type'));

        $page_list_num = trim(I('page_list_num'));
        if (empty($page_num)) {
            $return_result = [
                'code' => 2,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($type)) {
            $return_result = [
                'code' => 3,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        if ($type == 'pid') {
            $condition = array(
                "pid" => $id
            );
        }

        import('Lib.Action.Cats', 'App');
        $Cats = new Cats();
        $result = $Cats->getTree($page_info, $condition, $type);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
        ];
        $this->ajaxReturn($return_result);
    }
    //查看详情
    public function tree_detail()
    {
        $detail = $this->cats_model->find(I('id'));
        $this->detail = $detail;
        $data = $this->cats_model->order('sequence desc')->select();
        $res = $this->sub_all($data,0);
        $res = $this->procHtml($res);
        $res = '<select name = "pid"><option value = "'.$detail['pid'].'">选择上级</option><option value = 0 >顶级</option>'.$res.'</select>';
        $this->assign('procHtml',$res);
        $this->display();
    }
    //查看详情
    public function add_child()
    {
        // $this->detail = $this->cats_model->find(I('id'));
        $this->pid = I('id');
        $this->display();
    }
    public function update() {
        $name = I('post.name');
        if(!$name){
            $this->error('分类名不能为空');
        }
        $image=I('post.image');
        $id = I('id');
        $status = intval(I('status'));
        $pid = I('pid');
        if($id == $pid){
            $this->error('上级不能是自己');
        }
        $oldpid = $this->cats_model->where('id = '.$id)->getField('pid');
        $parent_path = $this->cats_model->where('id = '.$pid)->getField('path');
        if($pid == 0){
            $path = 0;
        }else{
            $path = $parent_path.'-'.$pid;
        }
        //所有子类
        $ids = $this->sub_cat_ids($id);
        $ids_arr = $this->sub_cat_ids($id,1);
        if(in_array($pid, $ids_arr)){
            $this->error('无法移到自己的子类下');
        }
        if($ids && $status != 1){
            //隐藏所有子类
            $data = array(
                'status' => 0,
            );
            $this->cats_model->where('id in ('.$ids.')')->save($data);
        }
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'id' => $id,
            'status' => $status,
            'sequence' => trim(I('post.sequence')),
            'pid' => $pid,
            'path' => trim($path),
            'time' => time()
        );
        $res =  $this->cats_model->where('id='.$id)->save($data);
        //pid更新后再更新子类的path
        if($pid != $oldpid){
            foreach ($ids_arr as $childId) {
                import('Lib.Action.Cats', 'App');
                $Cats = new Cats();
                $childIdPath = $Cats->getIdPath($childId);
                $data = array(
                    'path' => $childIdPath,
                );
                $this->cats_model->where('id = '.$childId)->save($data);
            }
        }
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('修改'.$name.'信息');
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
    public function insert() {
        $name = I('post.name');
        if(!$name){
            $this->error('分类名不能为空');
        }
        $type = I('post.type');
        //$image = $this->upload();
        $image = I('post.image');
        $pid = I('post.pid');
        if(!$pid || $type){
            //一级
            $pid = 0;
            $path = 0;
        }else{
            $parent_path = $this->cats_model->where('id = '.$pid)->getField('path');
            $path = $parent_path.'-'.$pid;
        }
        $data = array(
            'image' => $image,
            'name' => trim(I('post.name')),
            'pid' => $pid,
            'path' => trim($path),
            'status' => I('status'),
            'sequence' => I('sequence'),
            'time' => time()
        );
        $res =  $this->cats_model->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加'.$name.'信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    public function delete(){
        $id = I('id');
        $res = $this->cats_model->where(array('id' => $id))->delete();
        $childrenIds = $this->sub_cat_ids($id);
        $this->cats_model->where('id in ('.$childrenIds.')')->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');

            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 树状图的搜索
     */
    public function search_tree()
    {
        $name = trim(I('name'));

        $page_num = trim(I('page_num'));

        if (empty($name)) {
            $return_result = [
                'code' => 2,
                'name' => 'name不能为空',
            ];
            $this->ajaxReturn($return_result);
        }

        $res = $this->cats_model->where('name like "%'.$name.'%"')->find();
        if(!$res){
            $return_result = [
                'code' => 3,
                'msg' => '名称不存在',
            ];
            $this->ajaxReturn($return_result);
        }
        //获取父类的信息
        import('Lib.Action.Cats', 'App');
        $Cats = new Cats();
        $Parent_info = $Cats->catsFindParent($res['id']);
        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'Parent_info' => $Parent_info,
        ];
        $this->ajaxReturn($return_result);
    }

    //获取子类ID
    public function sub_cat_ids($id,$array=false){
        $sub_cat_tree = is_array($id)? $id : $this->sub_cat($id,$this->cats_model);
        $cat_ids = $this->get_sub_cat_ids($sub_cat_tree);
        return $cat_ids?($array? $cat_ids : implode(',',$cat_ids)) : ($array? array($id) : $id);
    }

    private function get_sub_cat_ids($sub_cat_tree){
        $cat_ids = array();
        foreach($sub_cat_tree as $value){
            $cat_ids[] = $value['id'];
            if($value['sub_cat'])$cat_ids = array_merge($cat_ids,$this->get_sub_cat_ids($value['sub_cat']));
        }
        return $cat_ids;
    }

    //子孙树
    public function sub_cat($id){
        $cat_list = $this->cats_model->where(array('pid'=>$id))->select();
        $tree=array();
        foreach($cat_list as $cat){
            if($cat['pid'] == $id){
                $cat['sub_cat'] = $this->sub_cat($cat['id']);
                $tree[]=$cat;
            }
        }
        return $tree;
    }

    public function sub_all($data,$pid){
        $tree = array();
        foreach($data as $k => $v){
            if($v['pid'] == $pid){
                $v['child'] = $this->sub_all($data, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    public function procHtml($tree){
        $html = '';
        foreach($tree as $t){
            if($t['child'] == ''){
                $html .= "<option value = ".$t['id'].">";
                $html .= $t['name'];
                $html .= "</option>";
            }else{
                $arr = explode('-', $t['path']);
                $html .= "<option value = ".$t['id'].">";
                foreach ($arr as $k => $v) {
                    $html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $html .= $t['name'];
                $html .= $this->procHtml($t['child']);
                $html = $html."</option>";
            }
        }
        return $html ? '<span style = "margin-left:10px">'.$html.'</span>' : $html ;
    }

    //重置所有分类的path
    public function resetPath(){
        $cats = $this->cats_model->select();
        foreach ($cats as $k => $v) {
            if($v['pid'] == 0){
                $data = array(
                    'path' => 0,
                );
                $this->cats_model->where('id = '.$v['id'])->save($data);
            }else{
                $res = $this->cats_model->where('id = '.$v['id'])->find();
                import('Lib.Action.Cats', 'App');
                $Cats = new Cats();
                $arr = $Cats->subCat($res['id'],$arr);
                $arr = array_reverse($arr);
                array_pop($arr);
                $path = implode('-' , $arr);
                $data = array(
                    'path' => '0-'.$path,
                );
                $this->cats_model->where('id = '.$v['id'])->save($data);
            }
        }
    }


}