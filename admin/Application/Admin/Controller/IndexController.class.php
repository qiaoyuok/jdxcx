<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-02 17:43:35									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-05 09:22:12						*****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
class IndexController extends PublicController {
    public function index(){

		
	    $this->display();
    }

    public function home(){
    	$this->display();
    }
}