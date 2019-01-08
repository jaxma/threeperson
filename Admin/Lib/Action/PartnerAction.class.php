<?php
class PartnerAction extends CommonAction {
  public function _initialize() {
    parent::_initialize();  
  }

  public function index($cat_id=0, $name='',$weixin=''){
    $this->cat_id   = $cat_id;
    $this->cat_list = M('articlecat')->where('parent_id=12')->select();

    $where = '1';

    if($cat_id>0) $where .= ' and cat_id='.$cat_id;
    if(!empty($name)) $where .= " and name like '%$name%'";
    if(!empty($weixin)) $where .= " and weixin like '%$weixin%'";

    $page = $_REQUEST['p']? $_REQUEST['p']+0 : 1;

    $list  = M('partner')->where($where)->page($page, 20)->select();
    foreach ($list as $key => $value) {
      $list[$key]['cat_name'] = M('articlecat')->where('cat_id='.$value['cat_id'])->getField('cat_name');
    }

    $count = M('partner')->where($where)->count();
    import("ORG.Util.Page");       //载入分页类
    $page = new Page($count, 20);
    $showPage = $page->show();
    
    $this->assign("page", $showPage);

    $this->assign('list', $list);

    $this->display();
  }

  public function add($cat_id=0){
    $this->cat_id   = $cat_id;
    $this->cat_list = M('articlecat')->where('parent_id=12')->select();

    $album_list = M('album')->order('sort_order asc,id desc')->where("id_value=$cat_id")->select();
    $this->assign('album_list',$album_list);
    $this->display();
  }

  public function insert(){
    $cat_id = $_POST['cat_id'];

    //添加相册
    if(is_array($_POST['ori_img'])){
        foreach($_POST['ori_img'] as $key=>$value){
            $album = array();
            $album['original_img']  = $value;
            $album['thumb_img']     = $_POST['thumb_img'][$key];
            $album['sort_order']    = $_POST['img_sort'][$key]+0;
            $album['description']   = $_POST['img_description'][$key];
            $album['id_value']      = $cat_id;

            M('album')->add($album);
        }
    }

    if($_FILES['filedata']['error']===0){
        $mode = $_POST['mode'];
        $ext  = pathinfo($_FILES['filedata']['name'], PATHINFO_EXTENSION);
        if($ext != 'xls') $this->error('上传的文件的格式不正确！');

        move_uploaded_file($_FILES['filedata']['tmp_name'],'partner.xls');

        import("ORG.Util.Spreadsheet_Excel_Reader");

        // ExcelFile($filename, $encoding);
        $data = new Spreadsheet_Excel_Reader();

        // Set output Encoding.
        $data->setOutputEncoding('utf-8');

        $data->read('partner.xls');

        $arr = array();
        $column = array('cat_id','level','name','weixin','mobile','idcard','no','address','remark');
        for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
          $row = array();
          $row['cat_id'] = $cat_id;
          for ($j = 2; $j <= $data->sheets[0]['numCols']; $j++) {
            //echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
            $row[$column[$j-1]] = $data->sheets[0]['cells'][$i][$j];
          }
          //echo "\n";
          if(empty($row['name'])) continue;
          $arr[] = $row;
        }

        if($mode == 'insert'){
            M('partner')->addAll($arr); 
        }else{
            if($cat_id==0){
              M('partner')->query('truncate table '.table('partner'));
            }else{
              M('partner')->where('cat_id='.$cat_id)->delete();
            }
            M('partner')->addAll($arr);
        }

        unlink('partner.xls');
    }

    $this->success('操作成功！');
  }


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
}