<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-03 09:29:40									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-04 11:32:17						*****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use \Think\Controller;

/**
 * 后端用户登录操作类
 */
class LoginController extends Controller{
	
	public function index($username='',$password=''){
		
		if(is_login()){
			$this->redirect("Index/index");
		}

		if (IS_POST) {
			// var_dump($_SESSION);
			!empty($username) or die($this->error("用户名不能为空"));
			!empty($password) or die($this->error("密码不能为空"));

			$admin = M("admin");
			$map['username'] = array("eq",$username);
			$map['status'] = array("eq",1);
			$admininfo = $admin->where($map)->find();
			
			if ($admininfo) {
				$password = md5($password.$admininfo['key']);
				$admininfo['password'] == $password or die($this->error("密码不正确"));

				$_SESSION['user'] = $admininfo;
				$admin->where("uid = ".$admininfo['uid'])->save(array("count"=>$admininfo['count']+1,"ip"=>$_SERVER['REMOTE_ADDR']));
				die($this->success("登陆成功"));
			}else{
				die($this->error("用户被禁用或不存在"));
			}
		}

		$this->display();

	}
}