<?php
class FeedbackAction extends CommonAction {
  public function _initialize() {
    parent::_initialize();  
  }
  /**
      +----------------------------------------------------------
     * 招聘列表
      +----------------------------------------------------------
     */
    public function index() {
        $M_Guestbook  = M('Guestbook');
        $recruitmentList = $M_Guestbook->order("guestbook_id desc")->select();
        foreach ($recruitmentList as $k => $v) {
          $recruitmentList[$k]['add_time'] = date("Y-m-d",$v['add_time']);
        }
        $this->assign("recruitmentList",$recruitmentList);
        $this->display();
    }

  /**
      +----------------------------------------------------------
     * 修改招聘页面
      +----------------------------------------------------------
     */
  public function edit(){
    /* 权限判断 */
    $recruitment_id=empty($_GET['id'])?0:intval($_GET['id']);
    $M_Guestbook  = M('Guestbook');
    $info = $M_Guestbook->where("guestbook_id=".$recruitment_id)->find(); 
    $info['add_time'] = date("Y-m-d",$info['add_time']);
    $this->assign("info", $info);
    $this->display();
  }
  
  /**
      +----------------------------------------------------------
     * 删除招聘
      +----------------------------------------------------------
     */
  public function del() {
    /* 权限判断 */
    $M_Guestbook  = M('Guestbook');
    $guestbook_id= intval($_GET['guestbook_id']);
    $oldRow = $M_Guestbook->where("guestbook_id=" . $guestbook_id)->find();
    if ($M_Guestbook->where("guestbook_id=" . $guestbook_id)->delete()) {
      parent::admin_log(addslashes($oldRow['description']),'remove','recruitment');
      /* 删除旧图片 */
      $oldRow=$M_Guestbook->where(array('guestbook_id'=>$guestbook_id))->find();
      if ($oldRow['thumb_img'] != ''){
        @unlink("./".$oldRow['thumb_img']);
        @unlink("./".$oldRow['original_img']);
      }
      $this->success("成功删除",U("Feedback/index"));
    } else {
      $this->error("删除失败，可能是不存在该ID的记录",U("Feedback/index"));
    } 
    }

    public function join_online(){
      import("ORG.Util.Page");       //载入分页类
        $page = new Page($count, 20);
        $showPage = $page->show();
    
        $this->assign("page", $showPage);
        $this->assign("join_list", M('join_online')->order('add_time desc')->limit($page->firstRow, $page->listRows)->select());

      $this->display('join_index');
    }

    public function join_info($id){
      $this->detail = M('join_online')->where(array('id'=>$id))->find();
      $this->display();
    }

    //报名管理
    public function baoming($type=1){
      $this->list = M('baoming')->where("type=$type")->order('add_time desc')->page(1,20)->select();
      $this->display('baoming_'.$type);
    }

    //删除报名
    public function del_baoming($id){
      if(M('baoming')->where("id=$id")->delete()){
        $this->success('删除成功！');
      }else{
        $this->success('未知错误！');
      }
    }

    //报名详细
    public function baoming_detail($id){
      $this->detail = M('baoming')->find($id);
      $this->display();
    }

    //批量删除报名
    public function batch(){
      $ids = $_POST['ids'];
      if(is_array($ids)) $ids = implode(',', $ids);
      $where = "id in ($ids)";

      $batch_type = $_POST['batch_type'];

      if(!$batch_type) $this->error('请选择操作类型！');

      if($batch_type=='batch_del_baoming') M('baoming')->where($where)->delete();
      if($batch_type=='batch_del_formdata'){
        $rows = M('formdata')->where($where)->select();
        foreach($rows as $value){
          if($value['type']==6){//人才招聘
            @unlink($value['value9']);
          }
        }
        M('formdata')->where($where)->delete();
      }

      $this->success('操作成功！');
    }

    public function exactA($type=1){
      $rows = M('baoming')->field("company_name,role,name,phone,weixin,add_time")->where('type='.$type)->select();
      $fields = array('机构名称','职务','姓名','联系电话','微信妮称','添加时间');
      foreach($rows as $key=>$value){
        $value['add_time'] = date('Y-m-d H:i',$value['add_time']);
        $rows[$key] = array_values($value);
      }
      array_unshift($rows, $fields);
      $this->exactCsv($rows,'Cooperation.csv');
    }

    public function exactB($type=2){
      $rows = M('baoming')->field("child_num1,child_num2,name,phone,weixin,role,company_name,referee,add_time")->where('type='.$type)->select();
      $fields = array('家庭中0-3岁的孩子有','家庭中4-6岁的孩子有','姓名','电话','微信昵称','是孩子的','家庭企业名称','推荐人或机构','添加时间');
      foreach($rows as $key=>$value){
        $value['add_time'] = date('Y-m-d H:i',$value['add_time']);
        $rows[$key] = array_values($value);
      }
      array_unshift($rows, $fields);
      $this->exactCsv($rows,'Parent.csv');
    }

    //导出CSV
    public function exactCsv($arr,$filename='abcd.csv'){
      $fh = fopen($filename,'w+');
      foreach($arr as $value){
        fputcsv($fh, $value);
      }
      fclose($fh);

      $fh = fopen($filename,'r');
      Header("Content-type: application/octet-stream");
      Header("Accept-Ranges: bytes");
      Header("Accept-Length: ".filesize($filename));
      Header("Content-Disposition: attachment; filename=" . $filename);
      // 输出文件内容
      echo fread($fh,filesize($filename));
      fclose($fh);

      unlink($filename);
      exit;
    }

    //通用表单数据
    public function formdata($type=1){
      //type 1：公开日报名  2：报读狮子公学  3：活动合作
      $this->list = M('formdata')->where("type=$type")->select();
      $this->type = $type;
      $this->display();
    }


    //表单详细
    public function formdata_detail($id){
      $this->detail = M('formdata')->find($id);
      $this->type = $this->detail['type'];
      $this->display();
    }

    //删除表单
    public function del_formdata($id){
      $oldrow = M('formdata')->where("id=$id")->delete();
      if($oldrow['type']==6){//人才招聘
        @unlink($oldrow['value9']);
      }
      M('formdata')->where("id=$id")->delete();
      $this->success('操作成功！');
    }
}
?>