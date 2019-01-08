<?php
class ControlAction extends Action{
    public function __construct() {
        parent::__construct();
    }

    /********************************************************************************************/

    //地区选择控件
    /*
        JS调用：Create_Position($container, $position)     myScript.js

        非JS可以直接在模版用 include标签引用
        <include file='Control:position' province="{$userInfo.province}" city="{$userInfo.city}" district="{$userInfo.district}"/>
        模版文件：Home/Tpl/Control/position
    */
    public function position($province=0, $city=0, $district=0){
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('district',$district);
        $this->display();
    }

    //获取地区列表
    public function getRegion($region_id){
        $region_list = M('region')->where("parent_id=$region_id")->select();
        echo "<option value='0'>请选择</option>";
        foreach ($region_list as $key => $value) {
            echo "<option value='". $value['region_id'] ."'>". $value['region_name'] ."</option>";
        }
    }


    /*******************************************************************************************/


    //相册控件
    /*
        JS调用：Create_Album($container, $id_value, $thumb_width, $thumb_height)     myScript.js

        非JS可以直接在模版用 include标签引用
        <include file="Control:album" id_value="{$cat_id}" thumb_width='120' thumb_height='120'/>
        模版文件：Home/Tpl/Control/album
    */
    public function album($id_value, $thumb_width, $thumb_height){
        $album_list = M('album')->order('sort_order asc,id desc')->where("id_value=$id_value")->select();

        $this->assign('album_list',$album_list);
        $this->assign('thumb_width',$thumb_width);
        $this->assign('thumb_height',$thumb_height);
        $this->display();
    }

    //删除数据库的图片
    public function delimg($id){
        $oldrow = M('album')->where('id='.$id)->find();
        M('album')->where('id='.$id)->delete();
        @unlink($oldrow['original_img']);
        @unlink($oldrow['thumb_img']);
        exit('1');
    }

    //删除文件夹的图片
    public function delimg2(){
        $ori_img = $_GET['ori_img'];
        $thumb_img = $_GET['thumb_img'];
        @unlink($ori_img);
        @unlink($thumb_img);
        exit('1');
    }

    /**********************************************************************************************/

    //标签列表
    public function tag_list($cat_id, $keyword=''){
        $where= "cat_id=$cat_id";
        if($keyword) $where .= " and tag_name like '%$keyword%'";
        $list = M('tags')->where($where)->order('id desc')->select();
        $this->assign('list', $list);
        $this->display();
    }

    //添加标签
    public function addTag($cat_id, $tag_name){
        $data['cat_id']     = $cat_id;
        $data['tag_name']   = $tag_name;

        $info               = M('tags')->where("tag_name='$tag_name'")->find();
        if($info) exit($info['id']);

        $tag_id             = M('tags')->add($data);
        exit($tag_id);
    }
}