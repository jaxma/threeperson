<?php 
class RotationmapAction extends CommonAction{
	
    
    //轮播图名字
    public function get_names(){
        $name_arr = array(
            'banner.jpg','banner2.jpg','banner3.jpg'
        );
        
        return $name_arr;
    }//end func get_names
    
    //rotation_map
    public function index(){
        
        $this->name_arr = $this->get_names();
        $this->display();
    }//end func index
    
    
    //更新图片
    public function edit(){
        
        $name_arr = $this->get_names();
        $image = $this->upload();
        
        $result = true;
        
        foreach ( $image as $k => $v ){
            $savepath = $v['savepath'];
            $savename = $v['savename'];
            
            $new_img = $savepath.$savename;
            $old_img = $savepath.$name_arr[$k];
            
            
            if( file_exists($old_img) ){
                if( !unlink($old_img) ){
                    unlink($new_img);
                    $result = false;
                    break;
                }
            }
            
            
            if( !rename($new_img,$old_img) ){
                $result = false;
                break;
            }
        }
        
        if( $result ){
//            echo 'succ';
            $this->add_active_log('更新轮播图成功');
            $this->success('更新轮播图成功！','index');
        }
        else{
//            echo 'error';
            $this->error('更新失败！');
        }
        
    }//end func edit
    
    
    function upload() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize = 1048576;
        $upload->allowExts = array('jpg'); // 设置附件上传类型
        $upload->savePath = './Public/images/home/';
        $upload->uploadReplace = false; //存在同名文件是否是覆盖
        $upload->thumbRemoveOrigin = "true";
        $upload->autoSub = false;    //是否以子目录方式保存
        $upload->subType = 'date';  //可以设置为hash或date
        $upload->dateFormat = 'Ym';
        $upload->subType = 'date';  //可以设置为hash或date
        
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {

            $info = $upload->getUploadFileInfo();
            return $info;
            
//            $image = $info[0]['savepath'] . $info[0]['savename'];
//            return __ROOT__ . substr($image, 1);
        }
    }
    
}

?>