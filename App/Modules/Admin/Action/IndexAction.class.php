<?php

/**
 * 	topos经销商后台
 */
class IndexAction extends CommonAction {

    private $company_model;

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {
        $this->display();
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
    }
    public function project() {
        
        $this->display();
    }
    
     public function projectlist() {
        
        $this->display();
    }
}
?>