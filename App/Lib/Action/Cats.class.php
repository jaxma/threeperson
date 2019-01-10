<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of team
 * 分类相关函数
 * @author tongjiong
 */
class Cats {
    private $cats_model;
    
    public function __construct() {
        $this->cats_model = M('cats');
    }

    /**
     * 分类树状图
     * @param $page_info
     * @param array $condition
     * @return array
     * add by qjq
     */
    public function getTree($page_info,$condition=array(),$type){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $count = $this->cats_model->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list =$this->cats_model->where($condition)->order('sequence desc')->page($page_con)->select();
            }
            else{
                $list =$this->cats_model->where($condition)->order('sequence desc')->select();
            }
            if($type == 'pid'){
                foreach ($list as $k => $v) {
                    $list[$k]['count'] =$this->cats_model->where(array('pid' => $list[$k]['id']))->count();
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
    public function catsFindParent($id)
    {
        $field='id,pid,name,status,image,path';
        $info = $this->cats_model->where(array('id'=>$id))->field($field)->select();
        foreach ($info as $k => $v) {
            $info[$k]['count'] =$this->cats_model->where(array('pid' => $info[$k]['id']))->count();
        }

        $id_path=$info[0]['path'];
        $pid=explode('-',$id_path);
        $condition['id']=array('in',$pid);
        $parent_info=$this->cats_model->where($condition)->field($field)->select();
        foreach ($parent_info as $k => $v) {
            $parent_info[$k]['count'] =$this->cats_model->where(array('pid' => $parent_info[$k]['id']))->count();
        }
        if(empty($parent_info)){
            return $info;
        }else{
            $parent_all=array_merge($parent_info,$info);
            return $parent_all;
        }
    }
    
    public function getIdPath($id){
        $arr = $this->subCat($id,$arr);
        $arr = array_reverse($arr);
        array_pop($arr);
        $path = implode('-' , $arr);
        return '0-'.$path;
    }

    public function subCat($id){
        $arr = array();
        $parent = $this->cats_model->where('id = '.$id)->find();
        $arr[] = $parent['id'];
        if($parent['pid'] != 0){
            $arr2 = $this->subCat($parent['pid'],$arr);
            $arr = array_merge($arr,$arr2);
        }
        return $arr;
    }

}
