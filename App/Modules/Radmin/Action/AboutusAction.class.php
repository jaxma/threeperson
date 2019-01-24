<?php

/**
 * 	topos经销商管理系统
 */
class AboutusAction extends CommonAction {

    private $cat_model;
    private $header_arr = array(
            1=>"头部介绍",
            2=>"FirstArticle",
            3=>"SecondArticle",
            4=>"ThirdArticle",
            5=>"FourthArticle",
        );

    public function _initialize()
    {
        parent::_initialize();
        $this->cat_model = M('cat');
    }
    //获取表名
    private function get_model(){
        return 'News';
    }

    //获取该栏目中文名字
    private function get_name(){
        return '关于TOPOS';
    }
    public function article(){
        $model_name = 'about';
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
        $this->row = $row;
        $this->arr = $row_arr;
        $this->id = $id;
        
        $this->header = $this->header_arr[$id];

        $this->display();
    }
    public function aupdate(){
        $model_name = 'about';
        $id = I('post.id');
        $id_info=M($model_name)->where(array('id' => $id))->find();
        $title = trim(I('post.title',''));
        $title_en = trim(I('post.title_en',''));
        $isopen=I('post.isopen');
        $sequence = I('post.sequence');
        $title_des = trim(I('post.title_des',''));
        $title_des_en = trim(I('post.title_des_en',''));
        $many_image=I('many_image');
        $many_images = implode(',',$many_image);
        $content = trim(I('post.content',''));
        $content = $this->formateStr($content);
        $content_en = trim(I('post.content_en',''));
        $content_en = $this->formateStr($content_en);

        if(empty($title)||empty($title_en)||empty($many_image)){
            $this->error('红色带星项目必须填写，请检查后重新提交');
            exit();
        }
        $data = array(
            'title' => $title,
            'title_en' => $title_en,
            'title_des' => $title_des,
            'title_des_en' => $title_des_en,
            'content' => $content,
            'content_en' => $content_en,
            'isopen' => $isopen,
            'many_image' => $many_images,
            'sequence' => $sequence,
            'time' => time(),
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->header_arr[$id];
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'article?id='.$id);
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
    private function formateStr($content){
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
        return $content;
    }

}

?>