<?php
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class PhotoAction extends Action {
    private $cat_model;
    private $photo_model;
    private $company_model;
    private $studio_model;
    private $backstage_model;
    private $backstage_img_model;

    public function __construct() {
        $this->cat_model = M('cat');
        $this->photo_model = M('photo');
        $this->company_model = M('company');
        $this->studio_model = M('studio');
        $this->backstage_model = M('backstage');
        $this->backstage_img_model = M('backstage_img');
        $key = I('key');
        if(md5($key) != 'b0d5ecaf75116cf41c5c7f416303c5b7' || !$key || empty($key)){
          $this->ajaxReturn(array('state'=>101,'msg'=>'非法请求'));
          return false;
        }
    }

    //
    public function index() {
        
    }
    
    public function photopicFind() {
      $app = I('appr');//获取app
      $id = I('id');
      $where = "isopen = 1 AND id = $id";
      $page_num = I('page_num');
      $page_list_num = 20;
      $count = $this->photo_model->where($where)->count();
      
      if( $count > 0 ){
            if(!empty($page_num)){
                $page_con = $page_num.','.$page_list_num;
                $list = $this->photo_model->where($where)->order('sequence desc')->page($page_con)->find();
                  $list['time'] = date('Y-h-m h:i',$list['time']);
                  $many_image = explode(',', $list['many_image']);
                  $list['many_image'] = $many_image;
                  $arr = array();
                    foreach ($list['many_image'] as $keyi => $vali) {
                          $v = $app.$vali;
                          $arr[] = $v;
                          $list['new_many_image'] = $arr;
                  }
            }else{
                $list = $this->photo_model->where($where)->order('sequence desc')->find();
                $list['time'] = date('Y-h-m h:i',$list['time']);
                $many_image = explode(',', $list['many_image']);
                $list['many_image'] = $many_image;

                $arr = array();
                foreach ($list['many_image'] as $keyi => $vali) {
                      $v = $app.$vali;
                      $arr[] = $v;
                      $list['new_many_image'] = $arr;
                }
            }
      }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'limit'=>$page_num,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }








    public function photopic() {
        $where = 'isopen = 1';
        $page_num = I('page_num');
        $page_list_num = 20;
        $list = array();
        $count = $this->photo_model->where($where)->count();
        if( $count > 0 ){
            if( !empty($page_num) ){
                $page_con = $page_num.','.$page_list_num;
                $list = $this->photo_model->where($where)->order('sequence desc')->page($page_con)->select();
                foreach ($list as $k => $v) {
                  if($v['cat2'] == 0 || !$v['cat2']){
                    $cat = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->find();
                  }else{
                    $cat = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->find();
                  }
                  $list[$k]['cat'] = $cat;
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                  $many_image = $v['many_image'];
                  $many_image = explode(',', $many_image);
                  $list[$k]['many_image'] = $many_image;
                }
            }else{
                $list = $this->photo_model->where($where)->order('sequence desc')->select();
                foreach ($list as $k => $v) {
                  if($v['cat2'] == 0 || !$v['cat2']){
                    $cat = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->find();
                  }else{
                    $cat = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->find();
                  }
                  $list[$k]['cat'] = $cat;
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                  $many_image = $v['many_image'];
                  $many_image = explode(',', $many_image);
                  $list[$k]['many_image'] = $many_image;
                }
            }
        }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'limit'=>$page_num,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }
    
    public function cat() {
        $where = 'status = 1 and pid = 0';
        $list = array();
        $count = $this->cat_model->where($where)->count();
        $list = $this->cat_model->where($where)->order('sequence asc')->select();
        foreach ($list as $k => $v) {
          $child = $this->cat_model->where('pid ='.$v['id'])->order('sequence asc')->select();
          foreach ($child as $kk => $vv) {
              $child[$kk]['add_time'] = date('Y-h-m h:i',$vv['add_time']);
          }
          $list[$k]['child'] = $child;
          $list[$k]['add_time'] = date('Y-h-m h:i',$v['add_time']);
        }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }
    
    public function company() {
        $type = I('type');
        if(!$type || empty($type)){
          $this->ajaxReturn(array('state'=>100,'msg'=>'缺少参数type'));
        }
        $where = 'status = 1 and type = '.$type;
        $res = $this->company_model->where($where)->order('id desc')->find();
        $res['time'] = date('Y-h-m h:i',$res['time']);
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'list'=>$res,
        ];
        $this->ajaxReturn($res);
    }

    public function catfindphoto(){
        $cat = I('cat');
        //如果分类级别被改变，分类可能存在一级分类中，也可能存在二级分类中
        $level = I('level');
        if(!$cat || empty($cat) || !$level || empty($level)){
          $this->ajaxReturn(array('state'=>100,'msg'=>'缺少参数cat或level'));
        }
        if($level == 1){
          $where = 'isopen = 1 and cat1 = '.$cat;
        }else{
          $where = 'isopen = 1 and cat2 = '.$cat;
        }
        $page_num = I('page_num');
        $page_list_num = 20;
        $list = array();
        $count = $this->photo_model->where($where)->count();
        if( $count > 0 ){
            if( !empty($page_num) ){
                $page_con = $page_num.','.$page_list_num;
                $list = $this->photo_model->where($where)->order('sequence desc')->page($page_con)->select();
                foreach ($list as $k => $v) {
                  // if($v['cat2'] == 0 || !$v['cat2']){
                  //   $cat = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->find();
                  // }else{
                  //   $cat = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->find();
                  // }
                  // $list[$k]['cat'] = $cat;
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                }
            }else{
                $list = $this->photo_model->where($where)->order('sequence desc')->select();
                foreach ($list as $k => $v) {
                  // if($v['cat2'] == 0 || !$v['cat2']){
                  //   $cat = $this->cat_model->where('status = 1 and id = '.$v['cat1'])->find();
                  // }else{
                  //   $cat = $this->cat_model->where('status = 1 and id = '.$v['cat2'])->find();
                  // }
                  // $list[$k]['cat'] = $cat;
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                }
            }
        }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'limit'=>$page_num,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }
    
    public function logo() {
        $logo = '/upload/system_logo/index_logo.png';
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].__ROOT__.$logo))$logo = '/upload/system_logo/index_logo.jpg';
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].__ROOT__.$logo))$logo = '/upload/system_logo/index_logo.gif';
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].__ROOT__.$logo))$logo = '/upload/system_logo/index_logo.jpeg';
        $logo = $logo.'?'.rand(5,99999);
        $res = [
          'logo_img' => $logo,
          'logo_url' => C('LOGO_URL'),
        ];
        $this->ajaxReturn($res);
    }

    public function catfindchild(){
        $cat_id = I('cat_id');
        if(!$cat_id || empty($cat_id)){
          $res = [
            'state'=>101,
            'msg'=>'缺少参数cat_id',
          ];
          $this->ajaxReturn($res);
        }
        $where = 'status = 1 and pid = '.$cat_id;
        $list = array();
        $count = $this->cat_model->where($where)->count();
        $list = $this->cat_model->where($where)->order('sequence asc')->select();
        foreach ($list as $k => $v) {
          $list[$k]['add_time'] = date('Y-h-m h:i',$v['add_time']);
        }
        if(!$list){
          $res = [
            'state'=>102,
            'msg'=>'没有数据',
          ];
        }else{
          $res = [
            'state'=>1,
            'msg'=>'数据获取成功',
            'count'=>$count,
            'list'=>$list,
          ];
        }
        $this->ajaxReturn($res);
    }

    //单项查询id 后台花絮 图片
    public function backstageFind() {
      $app = I('appr');
      $id = I('id');
      $thismodel = $this->backstage_model;
      $where = "isopen = 1 AND id=$id";  
      $list = $thismodel->where($where)->order('sequence desc')->find();
      $list['time'] = date('Y-h-m h:i',$list['time']);
      $content = $this->backstage_img_model->where('status = 1 and pid ='.$list['id'])->order('sequence desc')->field('many_image')->select();
      foreach ($content as $a => $b) {
        $imagesList = explode(',', $b['many_image']);
        foreach ($imagesList as $key => $val) {
          $img[] = $app.$val;
        }
      }
      $list['content'] = $img;
          
      $res = [
        'state'=>1,
        'msg'=>'数据获取成功',
        'list'=>$list['content'],
      ];
      $this->ajaxReturn($res);
    }


    public function studio() {
        $bs = I('bs');
        $bs=='bs'?$thismodel = $this->backstage_model:$thismodel = $this->studio_model;
        $merge = I('merge');
        $where = 'isopen = 1';
        $page_num = I('page_num');
        $page_list_num = 20;
        $list = array();
        $count = $thismodel->where($where)->count();
        if( $count > 0 ){
            if( !empty($page_num) ){
                $page_con = $page_num.','.$page_list_num;
                $list = $thismodel->where($where)->order('sequence desc')->page($page_con)->select();
                foreach ($list as $k => $v) {
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                  if($bs){
                    $content = $this->backstage_img_model->where('status = 1 and pid ='.$v['id'])->order('sequence desc')->field('many_image')->select();
                    $images2 = array();
                    foreach ($content as $a => $b) {
                      $bb = explode(',', $b['many_image']);
                      if(count($bb)>9){
                        foreach ($bb as $c => $d) {
                          if($c>8){
                            unset($bb[$c]);
                          }
                        }
                      }
                      $images[] = $bb;
                      $images2 = array_merge($bb,$images2);
                    }
                    if($merge)$images = $images2;
                    $list[$k]['content'] = $images;
                  }
                }
            }else{
                $list = $thismodel->where($where)->order('sequence desc')->select();
                foreach ($list as $k => $v) {
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                  if($bs){
                    $content = $this->backstage_img_model->where('status = 1 and pid ='.$v['id'])->order('sequence desc')->field('many_image')->select();
                    $images2 = array();
                    foreach ($content as $a => $b) {
                      $bb = explode(',', $b['many_image']);
                      if(count($bb)>9){
                        foreach ($bb as $c => $d) {
                          if($c>8){
                            unset($bb[$c]);
                          }
                        }
                      }
                      $images[] = $bb;
                      $images2 = array_merge($bb,$images2);
                    }
                    if($merge)$images = $images2;
                    $list[$k]['content'] = $images;
                  }
                }
            }
        }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'limit'=>$page_num,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }










    public function studio2() {
        $id = I('id');
        if(!$id){
          $res = [
            'state'=>100,
            'msg'=>'缺少参数id',
          ];
          $this->ajaxReturn($res);
        }
        $where = 'isopen = 1 and id ='.$id;
        $res = $this->studio_model->where($where)->find();
        $res['time'] = date('Y-h-m h:i',$res['time']);
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'list'=>$res,
        ];
        $this->ajaxReturn($res);
    }
    
}

?>