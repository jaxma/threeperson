<?php

/**
 * 	topos经销商后台
 */
class NewsAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
        $this->display();
    }

    public function detail() {
        $this->display('newsdetail');
    }
}
?>