<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Tbag <897498621@qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// Uploadify.class.php 2013年3月22日8:09:05

class UploadifyAction extends Action{
	public function index(){
		$this->display();
	}
	
	public function upload(){
        if (!empty($_FILES)) {
            import("ORG.UploadFile");
            import("ORG.Image");
            $upload = new UploadFile(); // 实例化上传类
            $upload->maxSize = 500000; // 设置附件上传大小
            $upload->saveRule = 'uniqid';
			$upload->uploadReplace = true;
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->savePath = './Upload/goodsImg'; // 设置附件上传目录
            if (!$upload->upload()) { // 上传错误提示错误信息
				$error['message'] = $upload->getErrorMsg();
                $error['status'] = 0;
				/*echo '<script type="text/javascript">alert("'.$error['message'].'");</script>';*/
			    echo json_encode($error);
                exit;
            } else {
                // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                $info[0]['file'] = trim($info[0]['savepath'].$info[0]['savename'],'.');
                echo json_encode($info[0]);
				//echo '<script>parent.set('.json_encode($info[0]).')</script>';
                exit;
            }
        }
	}

}