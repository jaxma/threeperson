<?php

/**
 *    topos经销商管理系统--市场营销模块
 */
class MarketAction extends CommonAction
{

    //获取表名
    private function get_model()
    {

        return 'MarketBusiness';
    }

    //获取该栏目中文名字
    private function get_name()
    {

        return '市场营销商学院';
    }

//商学院列表显示
    public function business_index()
    {
        $model_name = $this->get_model();

        $count = M($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('id desc')->limit($limit)->select();

            //联表查询
            $category_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['category_id']])) {
                    $ids[$v['category_id']] = $v['category_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $cats = M('market_business_category')->where(['id' => ['in', $ids]])->select();

            //取出数据
            foreach ($cats as $v) {
                $category_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['category_name'] = $category_info[$v['category_id']]['name'];
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

    //添加商学院信息
    public function business_add()
    {

        $market_business_category = M('MarketBusinessCategory');
        $dis_market_business_category = $market_business_category->field('id,name')->select();
        $this->assign('dis_market_business_category', $dis_market_business_category);

        $this->display();
    }

    public function business_insert()
    {

        $model_name = $this->get_model();

        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
//        $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'image' => $image,
            'title' => trim(I('post.title')),
            'content' => $content,
            'category_id' => I('post.category_id'),
            'time' => time()

        );
        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('添加' . $name . '信息');
            $this->success('添加成功',__URL__.'/business_index');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑商学院信息
    public function business_edit()
    {
        $model_name = $this->get_model();
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $this->row = $row;
        $this->id = $id;

        $market_business_category = M('MarketBusinessCategory');
        $dis_market_business_category = $market_business_category->select();
        $this->assign('dis_market_business_category', $dis_market_business_category);

        $this->display();
    }

    public function business_update()
    {
        $model_name = $this->get_model();

        $id = I('post.id');
        $id_info=  M('market_business')->where(array('id' => $id))->find();
        $old_image=$id_info['image'];
        $image=I('image');
        if(strcmp($old_image,$image)==0){
            $image=$image;

        }else{
            $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $id_info['image'];
            @unlink($url);
            $image = $image;
        }

        $content = I('post.content');
        $content = stripslashes($content);
        $content = preg_replace("/&amp;/", "&", $content);
        $content = preg_replace("/&quot;/", "\"", $content);
        $content = preg_replace("/&lt;/", "<", $content);
        $content = preg_replace("/&gt;/", ">", $content);
        $data = array(
            'image' => $image,
            'title' => trim(I('post.title')),
            'content' => $content,
            'category_id' => I('post.category_id'),
            'time' => time()
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑' . $name . '信息');
            $this->success("操作成功",__URL__.'/business_index');
        }
    }


    //删除商学院信息
    public function business_delete()
    {
        $id = I('id');

        $businessl_info = M('market_business')->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'] .__ROOT__. $businessl_info[0]['image'];
        @unlink($url);
        $res = M('market_business')->delete($id);
        if ($res) {
            $name = $this->get_name();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    //获取表名
    private function get_business_category()
    {

        return 'MarketBusinessCategory';
    }

    //获取该栏目中文名字
    private function get_pname()
    {

        return '市场营销商学院分类';
    }

    //商学院分类列表显示
    public function business_category_index()
    {
        $model_name = $this->get_business_category();

        $count = D($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('id desc')->limit($limit)->select();
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

    //添加商学院分类信息
    public function business_category_add()
    {

        $this->display();
    }

    public function business_category_insert()
    {
        $model_name = $this->get_business_category();

        $data = array(
            'name' => trim(I('post.name')),
            'time' => time()
        );
        $res = D($model_name)->add($data);
        if ($res) {
            $name = $this->get_pname();
            $this->add_active_log('添加' . $name . '信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }

    }

    //编辑商学院分类信息
    public function business_category_edit()
    {
        $model_name = $this->get_business_category();
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function business_category_update()
    {
        $model_name = $this->get_business_category();

        $id = I('post.id');
        $data = array(
            'name' => trim(I('post.name')),
            'time' => time()
        );
        $res = M($model_name)->where(['id' => $id])->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_pname();
            $this->add_active_log('编辑' . $name . '信息');
            $this->success("操作成功");
        }
    }

    //删除商学院分类信息
    public function business_category_delete()
    {
        $model_name = $this->get_business_category();
        $id = I('id');
        $res = D($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_pname();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

//获取视频中心表名
    private function get_movie()
    {

        return 'MarketMovie';
    }

    //获取表名字
    private function get_movie_pname()
    {

        return '市场营销视频中心';
    }

    //视频中心显示
    public function movie_index()
    {
        $model_name = $this->get_movie();

        $count = M($model_name)->count('id');
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 20);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = M($model_name)->order('time desc')->limit($limit)->select();
            //分页显示
            $page = $p->show();
            //模板赋值显示
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }

    //iframe打开弹出框
    public function movie_iframe(){
        $question_id=I('get.id');
        $this->question_id=$question_id;
        $this->display();
    }

    //添加视频信息
    public function movie_add()
    {

        $this->display();
    }

    public function movie_insert()
    {
        $model_name = $this->get_movie();
        
        $link = trim(I('link'));
        $link = stripslashes($link);
        $link = preg_replace("/&amp;/", "&", $link);
        $link = preg_replace("/&quot;/", "\"", $link);
        $link = preg_replace("/&lt;/", "<", $link);
        $link = preg_replace("/&gt;/", ">", $link);
        preg_match('/<iframe.+src=\"(.*?)\".*?>/i',$link,$match);
        

        $data = array(
            'title' => I('post.title'),
            'link' => $match[1],
            'time' => time()
        );
        $res = M($model_name)->add($data);
        if ($res) {
            $name = $this->get_movie_pname();
            $this->add_active_log('添加' . $name . '信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }

    }

    //编辑视频信息
    public function movie_edit()
    {
        $model_name = $this->get_movie();
        $id = $_GET['id'];
        $row = M($model_name)->find($id);
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function movie_update()
    {
        $model_name = $this->get_movie();

        $id = I('post.id');
        
        $link = trim(I('link'));
        $link = stripslashes($link);
        $link = preg_replace("/&amp;/", "&", $link);
        $link = preg_replace("/&quot;/", "\"", $link);
        $link = preg_replace("/&lt;/", "<", $link);
        $link = preg_replace("/&gt;/", ">", $link);
        preg_match('/<iframe.+src=\"(.*?)\".*?>/i',$link,$match);
        

        $data = array(
            'title' => I('post.title'),
            'link' => $match[1],
            'time' => time()
        );
        $res = M($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_name();
            $this->add_active_log('编辑' . $name . '信息');
            $this->success("操作成功");
        }
    }

    //删除视频信息
    public function movie_delete()
    {
        $model_name = $this->get_movie();
        $id = I('id');
        $res = M($model_name)->where(array('id' => $id))->delete();
        if ($res) {
            $name = $this->get_movie_pname();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    //获取素材库表
    private function get_material()
    {

        return 'MarketMaterial';
    }

    //获取中文名字
    private function get_material_name()
    {

        return '市场营销素材库';
    }

//素材库列表显示
    public function material_index()
    {
        $model_name = $this->get_material();

        $count = M($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = M($model_name)->order('id desc')->limit($limit)->select();
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

    //添加素材
    public function material_add()
    {
        $this->display();
    }

    public function material_insert()
    {

        $model_name = $this->get_material();
//        $image = $this->upload();
        $image=I('post.image');
        $data = array(
            'title' => trim(I('post.title')),
            'image' => $image,
            'time' => time()
        );
        $res = M($model_name)->add($data);
        if ($res) {
            $name = $this->get_material_name();
            $this->add_active_log('添加' . $name . '信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑素材
    public function material_edit()
    {
        $model_name = $this->get_material();
        $id = $_GET['id'];
        $row = D($model_name)->find($id);
        $this->row = $row;
        $this->id = $id;
        $this->display();
    }

    public function material_update()
    {
        $model_name = $this->get_material();

        $id = I('post.id');
        $id_info= M('market_material')->where(array('id' => $id))->find();
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
            'title' => trim(I('post.title')),
            'image' => $image,
            'time' => time()
        );
        $res = M($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_material_name();
            $this->add_active_log('编辑' . $name . '信息');
            $this->success("操作成功");
        }
    }


    //删除素材
    public function material_delete()
    {

        $id = I('id');
        $material_info = M('market_material')->where('id=' . $id)->select();
        $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__ . $material_info[0]['image'];
        @unlink($url);
        $res = M('market_material')->delete($id);

        if ($res) {
            $name = $this->get_material_name();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    //获取素材详情表名
    private function get_material_detail()
    {

        return 'MarketMaterialDetail';
    }

    //获取中文名字
    private function get_material_detail_name()
    {

        return '市场营销素材详情';
    }

//素材详情列表显示
    public function material_detail_index()
    {
        $model_name = $this->get_material_detail();

        $count = M($model_name)->count('id');
        $page_num=20;
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, $page_num);
            $limit = $p->firstRow . "," . $p->listRows;
            $list = D($model_name)->order('time desc')->limit($limit)->select();
            foreach ($list as $k => $v) {
                $arr = explode(',', $v['image']);
                $list[$k]['image'] = $arr;
            }

            //联表查询
            $material_info = [];
            //将id取出来
            foreach ($list as $v) {
                if (!isset($ids[$v['material_id']])) {
                    $ids[$v['material_id']] = $v['material_id'];
                }
            }
            //将取出来的id在另外的表根据id查询
            $mats = M('market_material')->where(['id' => ['in', $ids]])->select();
            //取出数据
            foreach ($mats as $v) {
                $material_info[$v['id']] = $v;
            }

            foreach ($list as $k => $v) {
                $list[$k]['material_detail_title'] = $material_info[$v['material_id']]['title'];
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

    //添加素材详情信息
    public function material_detail_add()
    {
        $market_material = M('MarketMaterial');
        $dis_market_material = $market_material->field('id,title')->select();
        $this->assign('dis_market_material', $dis_market_material);
        $this->display();
    }

    public function material_detail_insert()
    {

        $model_name = $this->get_material_detail();

        $descirbe = I('post.descirbe');
        $descirbe = preg_replace("/&amp;/", "&", $descirbe);
        $descirbe = preg_replace("/&quot;/", "\"", $descirbe);
        $descirbe = preg_replace("/&lt;/", "<", $descirbe);
        $descirbe = preg_replace("/&gt;/", ">", $descirbe);
//       $descirbe = str_replace("<br/>","\r\n",$descirbe);
//       $descirbe = str_replace(" ","&nbsp;&nbsp;",$descirbe);
//        $image = $this->uploadimage();
        $image=I('post.image');
        $images = implode(',', $image);

        $data = array(
            'title' => trim(I('post.title')),
            'image' => $images,
            'material_id' => I('post.material_id'),
            'descirbe' => $descirbe,
            'time' => time()
        );

        $res = D($model_name)->add($data);

        if ($res) {
            $name = $this->get_material_detail_name();
            $this->add_active_log('添加' . $name . '信息');
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    //编辑素材详情信息
    public function material_detail_edit()
    {
        $model_name = $this->get_material_detail();
        $id = $_GET['id'];
        $row = M($model_name)->find($id);

        $row_image = $row['image'];
        $arr = explode(',', $row_image);
        //在每个前面加上__ROOT__,用在编辑时本机显示图片
        array_walk(
            $arr,
            function (&$s, $k, $prefix = '__ROOT__') {
                $s = str_pad($s, strlen($prefix) + strlen($s), $prefix, STR_PAD_LEFT);
            }
        );
        $row_arr=implode(',',$arr);

        $this->arr = $row_arr;
        $this->row = $row;
        $this->id = $id;

        $market_material = M('MarketMaterial');
        $dis_market_material = $market_material->select();
        $this->assign('dis_market_material', $dis_market_material);

        $this->display();
    }

    public function material_detail_update()
    {
        $model_name = $this->get_material_detail();

        $id = I('post.id');

        $image=I('post.image');
        $images = implode(',', $image);

        $descirbe = I('post.descirbe');
        $descirbe = stripslashes($descirbe);
        $descirbe = preg_replace("/&amp;/", "&", $descirbe);
        $descirbe = preg_replace("/&quot;/", "\"", $descirbe);
        $descirbe = preg_replace("/&lt;/", "<", $descirbe);
        $descirbe = preg_replace("/&gt;/", ">", $descirbe);
//        $descirbe = str_replace("<br/>","\n",$descirbe);
//        $descirbe = str_replace(" ","&nbsp;&nbsp;",$descirbe);
        $data = array(
            'image' => $images,
            'title' => trim(I('post.title')),
            'material_id' => I('post.material_id'),
            'descirbe' => $descirbe,
            'time' => time()
        );
        $res = D($model_name)->where(array('id' => $id))->save($data);
        if ($res === false) {
            $this->error("操作失败");
        } else {
            $name = $this->get_material_detail_name();
            $this->add_active_log('编辑' . $name . '信息');
            $this->success("操作成功");
        }
    }


    //删除素材详情信息
    public function material_detail_delete()
    {
        $id = I('id');

        $businessl_info = M('market_material_detail')->where('id=' . $id)->select();

        foreach ($businessl_info as $k => $v) {
            $arr = explode(',', $v['image']);
            $businessl_info[$k]['image'] = $arr;
            for ($i = 0; $i < count($arr); $i++) {
                $url = $_SERVER['DOCUMENT_ROOT'].__ROOT__. '/' . $businessl_info[$k]['image'][$i];


               @unlink($url);
            }

        }
        $res = M('market_material_detail')->delete($id);
        if ($res) {
            $name = $this->get_material_detail_name();
            $this->add_active_log('删除' . $name . '信息');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }





//
//    * +-------------------------------------------------
//    * 商学院上传图片
//    * +-------------------------------------------------
//    * @param string $name
//    * +-------------------------------------------------
//    * @return string $info(中文提示)
//    * +-------------------------------------------------
//
//  function upload()
//  {
//      import('ORG.Net.UploadFile');
//      $upload = new UploadFile();// 实例化上传类
//      $upload->maxSize = 3145728; // 设置附件上传大小 3M
//      $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'pic'); // 设置附件上传类型
//
//      $upload->savePath = './upload/market/';// 设置附件上传目录
//
//      $upload->uploadReplace = false; //存在同名文件是否是覆盖
//      $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
//      $upload->autoSub = true;    //是否以子目录方式保存
//      $upload->subType = 'date';  //可以设置为hash或date
//      $upload->dateFormat = 'Ymd';
//      if (!$upload->upload()) {
//          $this->error($upload->getErrorMsg());
//      } else {
//          $info = $upload->getUploadFileInfo();
//          $image = $info[0]['savepath'] . $info[0]['savename'];
//          return __ROOT__ . substr($image, 1);
//      }
//  }
//
//
//  //素材库详情图片上传
//  public function uploadimage()
//  {
//      if ($_FILES['image'] != '') {
//          import('ORG.Net.UploadFile');
//          $upload = new UploadFile();// 实例化上传类
//          $upload->maxSize = 3145728;// 设置附件上传大小
//          $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//          $upload->savePath = 'upload/market/';// 设置附件上传目录
//          $upload->uploadReplace = false; //存在同名文件是否是覆盖
//          $upload->thumbRemoveOrigin = "true";//生成缩略图后是否删除原图
//          $upload->autoSub = true;    //是否以子目录方式保存
//
//          $upload->dateFormat = 'Ymd';
//          $upload->subType = 'date';  //可以设置为hash或date
//          $upload->saveName = array('uniqid', '');
//          if (!$upload->upload()) { //捕获上传异常
//
//              $this->error($upload->getErrorMsg());
//
//          } else {
//              for ($i = 0; $i < count($_FILES["image"]["name"]); $i++) {
//
//                  $uploadList = $upload->getUploadFileInfo();
//                  $arr[] = $uploadList[$i]['savepath'] . $uploadList[$i]['savename'];
//              }
//              $image['image'] = $arr[$i];
//              $image = $image['image'];
//              $images[] = __ROOT__ . substr($image, 1);
//
//          }
//
//      }
//      return $arr;
//  }

}

?>