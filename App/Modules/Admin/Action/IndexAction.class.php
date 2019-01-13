<?php

/**
 * 	topos经销商后台
 */
class IndexAction extends CommonAction {

   
    public function index() {
    	$this->title = C('SYSTEM_NAME');
        
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