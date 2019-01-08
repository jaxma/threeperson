<?php
class SurveyAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();	
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index(){
		$limit 		= 20;
		import("ORG.Util.Page");       //载入分页类
		$count 		= M('survey')->count();
        $page 		= new Page($count, 20);
        $showPage 	= $page->show();
		$this->assign("filter", $filter);
        $this->assign("page", $showPage);
		$this->list = M('survey')->order('add_time desc')->limit($page->firstRow, $page->listRows,$filter)->select();

		$this->display();
	}

	/**
	 * [del description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function del($id){
		$file_url = M('survey')->where("id=$id")->getField('file_url');
		
		if(M('survey')->where("id=$id")->delete()){
			@unlink($file_url);
			$this->success('操作成功！');
		}else{
			$this->error('网络错误，请稍候再试！');
		}
	}
}
?>