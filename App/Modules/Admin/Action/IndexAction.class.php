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
    }
    public function project() {
        
        $this->display();
    }
    
     public function projectlist() {
        
        $this->display();
    }
}
?>