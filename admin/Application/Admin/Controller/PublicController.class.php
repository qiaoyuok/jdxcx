<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-02 17:48:49									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-10 11:53:32						*****
 * --------------------------------------------------------------------------------------------------
 */
/**
 * 后台入口操作公共类文件
 */
namespace Admin\Controller;
use \Think\Controller;

class PublicController extends Controller{
	
	public function _initialize(){
		if (!is_login()) {
			$this->redirect("Login/index");
		}
		$menu = M("menu");
		// 获取菜单列表
		$menus = $menu->where("status = 1")->order("sort desc")->select();
		
		// 获取面包屑导航
		$action = substr(__ACTION__,1);

		$menuinfo = $menu->where("url = '".$action."' and pid >0")->find();
		if ($menuinfo) {
			$nav = familytree($menus,$menuinfo['id']);
		}
		$menus = getTree($menus);

		$this->assign("nav",$nav);
		$this->assign("menus",$menus);
	}


	/**
	 * 用户退出操作
	 * @Author   孙乔雨
	 * @DateTime 2018-07-03
	 * @return   [type]     [description]
	 */
	public function loginOut(){
		var_dump("nihao");
		$_SESSION['user'] = "";
		$this->redirect("Login/index");
	}
}