<?php


namespace Admin\Controller;
use Think\Controller;

/**
 * 接口入口类
 */
class ApibaseController extends Controller{
	
	/**
	 * 入口执行的校验操作
	 * @return [type] [description]
	 */
	public function _initialize(){
		
		$uid = I('uid');
		$token = I("token");

		!empty($uid)&&!empty($token) or die('{"status":-1,"msg":"请重新登陆"}');

		$obj_token = M("token");
		$tokeninfo = $obj_token->where("uid = $uid")->find();

		if (empty($tokeninfo)||$tokeninfo['token'] != $token) {
			
			die('{"status":-1,"msg":"请重新登陆"}');
		}

	}

}

