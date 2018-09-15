<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-04 17:41:36									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-11 10:14:12                      *****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
class UserController extends PublicController {
	/**
	 * 用户列表
	 * @Author   孙乔雨
	 * @DateTime 2018-07-04
	 * @param    string     $page  [页码]
	 * @param    string     $limit [每页记录数]
     * @param    string     $nickname   [用户昵称]
	 * @return   [type]            [description]
	 */
    public function index($page='',$limit='',$nickname='',$status=''){

		$user = M("user");
    	if(IS_POST){

    		if (!empty($nickname)) {
                $where['nickname'] = array("like","%$nickname%");
			}

            if (!empty($status)) {
                $where['status'] = array("eq",$status);
            }

            $list = $user->where($where)->select();
    		$count = $user->where($where)->count();
	    	die('{"code":0,"data":'.json_encode($list).',"count":'.$count.'}');
    	}

	    $this->display();
    }

     /**
     * 删除用户
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $uid [用户ID号]
     * @return   [type]          [description]
     */
    public function del($uid=''){
    	!empty($uid) or die($this->error("请先选择数据"));
    	$arr = explode(",", $uid);
    	$map['uid'] = array("in",$uid);
    	$user = M("user");
    	if ($user->where($map)->delete()) {
    		
    		die($this->success("删除成功"));
    	}else{
    		die($this->error("删除失败"));
    	}
    }

    /**
     * 店铺列表
     * @param  string $page     [页码]
     * @param  string $limit    [每页记录数]
     * @param  string $shopname [店铺名称]
     * @param  string $status   [店铺状态]
     * @return [type]           [description]
     */
    public function shop($page='',$limit='',$shopname='',$status='',$realname='',$tel='',$wx=''){
        
        $shop = M("shop");
        if(IS_POST){

            if (!empty($shopname)) {
                $where['shopname'] = array("like","%$shopname%");
            }

            if (!empty($realname)) {
                $where['realname'] = array("like","%$realname%");
            }

            if (!empty($status)) {
                $where['status'] = array("eq",$status);
            }

            if (!empty($tel)) {
                $where['tel'] = array("like","%$tel%");
            }

            if (!empty($wx)) {
                $where['wx'] = array("like","%$wx%");
            }

            $list = $shop->where($where)->select();
            $count = $shop->where($where)->count();
            die('{"code":0,"data":'.json_encode($list).',"count":'.$count.'}');
        }

        $this->display();
    }

    /**
     * 店铺详情
     * @param  string $id [店铺ID号]
     * @return [type]     [description]
     */
    public function shopDetail($id=''){
        
        !empty($id) or die($this->error("缺少参数"));

        $shop = M("shop");
        $shopinfo = $shop->where("id = $id")->find();

        if($shopinfo){
            $this->assign("shopinfo",$shopinfo);
            $this->assign("url",$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]);
        }

        $this->display();
    }

    /**
     * 删除店铺
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $id [店铺ID号]
     * @return   [type]          [description]
     */
    public function delshop($id=''){
        !empty($id) or die($this->error("请先选择数据"));
        $arr = explode(",", $id);
        $map['id'] = array("in",$id);
        $shop = M("shop");
        if ($shop->where($map)->delete()) {
            
            die($this->success("删除成功"));
        }else{
            die($this->error("删除失败"));
        }
    }

    /**
     * 单元格编辑数据
     * @Author   孙乔雨
     * @DateTime 2018-07-03
     * @param    string     $id     [管理员ID号]
     * @param    string     $field  [操作名称]
     * @param    string     $value  [改变后的内容]
     * @return   [type]             [description]
     */
    public function changeOne($id='',$field='',$value=''){
        !empty($value) or die($this->error("值不能为空"));
        
        $shop = M("shop");
        if($shop->where("id = $id")->save(array("$field"=>$value))){
            die($this->success("编辑成功"));
        }else{
            die($this->error("编辑失败"));
        }
    }
}