<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-04 11:11:31									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-04 16:01:08						*****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
class AdminController extends PublicController {

	/**
	 * 管理员管理列表
	 * @Author   孙乔雨
	 * @DateTime 2018-07-04
	 * @param    string     $page  		[页码]
	 * @param    string     $limit 		[每页记录数]
	 * @param    string     $username 	[用户名]
	 * @return   [type]            [description]
	 */
    public function index($page='',$limit='',$username=''){

		$admin = M("admin");
		if (IS_POST) {
			if (!empty($username)) {
				$where = "username like '%".$username."%'";
			}
			// echo $where;
    		$count = $admin->where($where)->count();
	    	$list = $admin->where($where)->page($page)->limit($limit)->select();
	    	die('{"code":0,"data":'.json_encode($list).',"count":'.$count.'}');
		}

	    $this->display();
    }

    /**
     * 添加管理员
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $password   [密码]
     * @param    string     $repassword [确认密码]
     */
    public function add($password='',$repassword=''){
    	if (IS_POST) {
    		!empty($password) or die($this->error("密码不能为空"));
    		!empty($repassword) or die($this->error("密码不能为空"));
    		$password == $repassword or die($this->error("两次输入密码不同"));
    		$admin = M("admin");
    		$key = getKey(18);
    		$_POST['password'] = md5($password.$key);
    		$_POST['key'] = $key;
    		$_POST['create_time'] = date("Y-m-d H:i:s",time());
    		$data = $admin->create(array_filter($_POST));
    		if ($admin->add($data)) {
    			die($this->success("添加成功"));
    		}else{
    			die($this->error("添加失败"));
    		}
    	}
    	$this->display();
    }

    /**
     * 删除管理员
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $uid [description]
     * @return   [type]          [description]
     */
    public function del($uid=''){
    	!empty($uid) or die($this->error("请先选择数据"));
    	$arr = explode(",", $uid);

    	!in_array(C("ADMINISTRATOR"),$arr) or die($this->error("不能删除超级管理员")); 
    	$map['uid'] = array("in",$uid);
    	$admin = M("admin");
    	if ($admin->where($map)->delete()) {
    		
    		die($this->success("删除成功"));
    	}else{
    		die($this->error("删除失败"));
    	}
    }

    /**
     * 修改密码
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $uid        [用户ID号]
     * @param    string     $password   [密码]
     * @param    string     $repassword [确认密码]
     * @return   [type]                 [description]
     */
    public function editPassword($uid='',$password='',$repassword=''){
    	if(IS_POST){
    		!empty($password) or die($this->error("密码不能为空"));
    		!empty($repassword) or die($this->error("密码不能为空"));
    		$password == $repassword or die($this->error("两次输入密码不同"));
    		$admin = M("admin");
    		$key = getKey(18);
    		$_POST['password'] = md5($password.$key);
    		$_POST['key'] = $key;
    		$_POST['update_time'] = date("Y-m-d H:i:s",time());
    		$data = $admin->create(array_filter($_POST));
    		if ($admin->where("uid = $uid")->save($data)) {
    			die($this->success("修改成功"));
    		}else{
    			die($this->error("修改失败"));
    		}
    	}
    	$this->display();
    }

     /**
     * 单元格编辑数据
     * @Author   孙乔雨
     * @DateTime 2018-07-03
     * @param    string     $id     [管理员ID号]
     * @param    string     $field	[操作名称]
     * @param    string     $value  [改变后的内容]
     * @return   [type]             [description]
     */
    public function changeOne($uid='',$field='',$value=''){
        !empty($value)||$value=="0" or die($this->error("值不能为空"));
        if ($uid == C("ADMINISTRATOR") && $field == "status") {
    		die($this->error("不能对超级管理员操作")); 
        }
    	$admin = M("admin");
		if($admin->where("uid = $uid")->save(array("$field"=>$value))){
			die($this->success("编辑成功"));
		}else{
			die($this->error("编辑失败"));
		}
    }
}