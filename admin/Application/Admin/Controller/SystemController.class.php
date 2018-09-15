<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-03 11:45:25									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-05 17:46:40                      *****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
class SystemController extends PublicController {

    /**
     * 管理员管理列表
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $page  [页码]
     * @param    string     $limit [每页记录数]
     * @param    string     $pid   [父级ID号]
     * @return   [type]            [description]
    */
    public function index($page='',$limit='',$pid=0){
    	$menu = M("menu");
    	if(IS_POST){
    		$where = "pid = $pid";
    		$count = $menu->where($where)->count();
	    	$list = $menu->where($where)->page($page)->limit($limit)->order("sort desc")->select();
	    	die('{"code":0,"data":'.json_encode($list).',"count":'.$count.'}');
    	}

		$this->display();
    }

    /**
     * 菜单添加
     * @Author   孙乔雨
     * @DateTime 2018-07-04
     * @param    string     $id [父级ID号]
     */
    public function add($id=''){
    	$menu = M("menu");
    	if (IS_POST) {
    		$data = $menu->create($_POST);
    		if($menu->add($data)){
    			die($this->success("添加成功"));
    		}else{
    			die($this->error("添加失败"));
    		}
    	}
        
    	$list = $menu->where($where)->select();
        
        if (!empty($id)) {
            
            foreach ($list as $k => $v) {
                
                if ($v['id'] == $id) {
                    $list[$k]['checked'] = 1;
                    break;
                }
            }
        }

        $list = listGetTree($list);
    	$this->assign("list",$list);
    	$this->display();
    }

    /**
     * 单元格编辑数据
     * @Author   孙乔雨
     * @DateTime 2018-07-03
     * @param    string     $id     [菜单ID号]
     * @param    string     $field	[操作名称]
     * @param    string     $value  [改变后的内容]
     * @return   [type]             [description]
     */
    public function changeOne($id='',$field='',$value=''){
        // !empty($value)||$value=="0" or die($this->error("值不能为空"));
    	$menu = M("menu");
		if($menu->where("id = $id")->save(array("$field"=>$value))){
			die($this->success("编辑成功"));
		}else{
			die($this->error("编辑失败"));
		}
    }

    /**
     * 删除菜单
     * @Author   孙乔雨
     * @DateTime 2018-07-03
     * @param    string     $id [菜单ID号]
     * @return   [type]         [description]
     */
    public function del($id=''){
    	
    	$menu = M("menu");
    	$map['id'] = array("in",$id);
    	if($menu->where($map)->delete()){
    		die($this->success("删除成功"));
    	}else{
    		die($this->error("删除失败"));
    	}
    }
}