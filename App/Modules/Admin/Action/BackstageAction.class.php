<?php

/**
 * 	topos经销商后台
 */
class BackstageAction extends CommonAction {

	public function backstage_cat()
    {
    	$this->display();
    }
    
    //花絮的图片
    public function index() {
      $id = I('id');
      $backstage_model = M('backstage');
      $backstage_img_model = M('backstage_img');
     
      $where = "isopen = 1 AND id = $id";  
      $list = $backstage_model->where($where)->order('sequence desc')->find();
      $list['time'] = date('Y-h-m h:i',$list['time']);
      $content = $backstage_img_model->where('status = 1 and pid ='.$list['id'])->order('sequence desc')->field('many_image')->select();
      foreach ($content as $a => $b) {
        $images[] = explode(',', $b['many_image']);
      }
      $list['content'] = $images;
    
      $this->list = $list;
      $this->display();

    }

    

}
?>