<?php

/**
 * 	topos经销商管理系统
 */
class VideoAction extends CommonAction {

    
    //获取表名
    private function get_model(){
        
        return 'Video';
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

    //添加产品信息
    public function add() {
        $this->display();
    }

    public function insert() {
        $model_name = $this->get_model();

        $count = D($model_name)->count();
        if($count >= 5){
            $this->error('你已经添加了五个视频！');
        }
        
        $news = I('post.disc');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $video=I('post.video');
        $isopen=I('post.isopen');
        $sequence = I('post.sequence');
        $data = array(
            'video' => $video,
            'name' => trim(I('post.name')),
            'presents' => I('post.presents'),
            'news' => $news,
            'isopen' => $isopen,
            'sequence' => $sequence,
            'time' => time()
        );
        $res = D($model_name)->add($data);
        if ($res) {
            $new_config['ADD_VIDEO_TEMP'] = '';
            $this->update_config($new_config);
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
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function update() {
        $model_name = $this->get_model();
        
        $id = I('post.id');
        $id_info=M('Video')->where(array('id' => $id))->find();
        $old_video=$id_info['video'];
        $video=I('video');
        if(strcmp($old_video,$video)==0){
            $video=$video;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['video'];
            @unlink($url);
            $video = $video;
        }

        $news = I('post.disc');
        $news = stripslashes($news);
        $news = preg_replace("/&amp;/", "&", $news);
        $news = preg_replace("/&quot;/", "\"", $news);
        $news = preg_replace("/&lt;/", "<", $news);
        $news = preg_replace("/&gt;/", ">", $news);
        $isopen = I('post.isopen');
        $sequence = I('post.sequence');
        $data = array(
            'video' => $video,
            'name' => trim(I('post.name')),
            'presents' => I('post.presents'),
            'news' => $news,
            'isopen' => $isopen,
            'oldvideo' => '',
            'sequence' => $sequence,
            'time' => time()
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $new_config['EDIT_VIDEO_TEMP'] = '';
            $this->update_config($new_config);
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            $this->success("操作成功",__URL__.'/'.'index');
        }
    }

    //删除产品信息
    public function delete() {
        $model_name = $this->get_model();
        
        $id = I('id');
        $url = D($model_name)->where(array('id' => $id))->getField('video');
        $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $url;
        @unlink($url);
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除'.$name.'信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    public function scan_video(){
        $model_name = $this->get_model();
        $dir = $_SERVER['DOCUMENT_ROOT'].__ROOT__.'/upload/video';
        $allpath = $this->getVideoPath($dir);
        foreach ($allpath as $k => $v) {
            $rv = strrchr($v['path'],'upload');
            $rv = '/'.$rv;
            $allpath[$k]['real_path'] = $rv;
            $des = D($model_name)->where('video = "'.$rv.'"')->find();
            if($des){
                $allpath[$k]['des'] = $des;
            }
        }
        // p($allpath);
        $this->assign('allpath',$allpath);
        $this->display();
    }

    public function del_video_file(){
        $model_name = $this->get_model();
        $path = I('path');
        $res = D($model_name)->where(array('video' => $path))->find();
        if($res){
            $this->error('视频正在使用，请到视频上传页面删除');
        }
        $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $path;
        @unlink($url);
        $name = $this->get_name();
        $this->add_active_log('删除'.$name.'信息');
        $this->success('删除成功');
    }


    public function img(){
        $model_name = $this->get_model();
        $id = I('id');
        $row = D($model_name)->where('id='.$id)->find();
        $this->assign('row',$row);
        $this->display();
    }
    public function image_update(){
        $model_name = $this->get_model();
        
        $id = I('post.id');
        $id_info=M('Video')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;
        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }
        $data = array(
            'image' => $image
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $new_config['EDIT_VIDEO_TEMP'] = '';
            $this->update_config($new_config);
            $name = $this->get_name();
            $this->add_active_log('编辑'.$name.'信息');
            echo "<script>window.location.reload();</script>";
        }
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

    private function getVideoPath($dir){
        global $arr;
        if(is_dir($dir)){
            $p = scandir($dir);
            foreach($p as $val){
                if($val !="." && $val !=".."){
                    if(is_dir($dir.'/'.$val)){
                        $this->getVideoPath($dir.'/'.$val);
                    }else{
                        $arr[]['path'] = $dir.'/'.$val;
                    }
                }
            }
        }
        return $arr;
    }

}

?>