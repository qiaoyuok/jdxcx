<?php
/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-05 09:21:26									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-05 16:34:25                      *****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
class AnnounceController extends PublicController {
    
    /**
     * 公告列表
     * @Author   孙乔雨
     * @DateTime 2018-07-05
     * @param    string     $page     [页码]
     * @param    string     $limit    [每页记录数]
     * @param    string     $author   [发布人]
     * @param    string     $title    [公告标题]
     * @param    string     $start    [开始时间]
     * @param    string     $end      [结束时间]
     * @return   [type]               [description]
     */
    public function index($page='',$limit='',$author='',$title='',$start='',$end=''){

		$announce = M("announce");
    	if(IS_POST){
    		if (!empty($author)) {
				$where['author'] = array('like',"%$author%");
			}

            if (!empty($title)) {
                $where['title'] = array('like',"%$title%");
            }

            if (!empty($start)) {
                $where['create_time'] = array('egt',$start);
            }

            if (!empty($end)) {
                $where['create_time'] = array('let',$end);
            }

			$count = $announce->where($where)->count();

	    	$list = $announce->where($where)->page($page)->limit($limit)->order("sort desc,create_time desc")->select();
	    	die('{"code":0,"data":'.json_encode($list).',"count":'.$count.'}');
    	}

	    $this->display();
    }

    /**
     * 添加公告
     * @Author   孙乔雨
     * @DateTime 2018-07-05
     * @param    string     $title      [公告标题]
     * @param    string     $author     [发布人]
     * @param    string     $content    [内容]
     */
    public function add($title='',$author='',$content=''){

        if(IS_POST){
            !empty($title) or die($this->error("标题不能为空"));
            !empty($content) or die($this->error("内容不能为空"));

            $announce = M("announce");
            $_POST['create_time'] = date("Y-m-d H:i:s",time());
            $data = $announce->create(array_filter($_POST));
            if ($announce->add($data)) {
                
                die($this->success("添加成功"));
            }else{
                die($this->error("添加失败"));
            }
        }

    	$this->display();
    }

    /**
     * 编辑公告
     * @Author   孙乔雨
     * @DateTime 2018-07-05
     * @param    string     $id          [公告ID号]
     * @param    string     $title       [公告标题]
     * @param    string     $author      [发布者]
     * @param    string     $content     [公告标题]
     * @return   [type]                  [description]
     */
    public function edit($id='',$title='',$author='',$content=''){
        
        !empty($id) or die($this->error("缺少参数"));

        $announce = M("announce");

        if (IS_POST) {
            !empty($title) or die($this->error("标题不能为空"));
            !empty($content) or die($this->error("内容不能为空"));
            $data = $announce->create(array_filter($_POST));
            if($announce->where("id = $id")->save($data)){
                die($this->success("编辑成功"));
            }else{
                die($this->error("编辑失败"));
            }
        }

        $announceinfo = $announce->where("id = $id")->find();
        $this->assign("announceinfo",$announceinfo);
        $this->display();
    }

    /**
     * 单元格编辑数据
     * @Author   孙乔雨
     * @DateTime 2018-07-03
     * @param    string     $id     [ID号]
     * @param    string     $field  [操作名称]
     * @param    string     $value  [改变后的内容]
     * @return   [type]             [description]
     */
    public function changeOne($id='',$field='',$value=''){
        // !empty($value)||$value=="0" or die($this->error("值不能为空"));
        $announce = M("announce");
        if($announce->where("id = $id")->save(array("$field"=>$value))){
            die($this->success("编辑成功"));
        }else{
            die($this->error("编辑失败"));
        }
    }

    /**
     * 预览公告
     * @Author   孙乔雨
     * @DateTime 2018-07-05
     * @param    string     $id [description]
     * @return   [type]         [description]
     */
    public function preview($id=''){
        
        $announce = M("announce");

        $announceinfo = $announce->where("id = $id")->find();

        $this->assign("content",$announceinfo['content']);
        $this->display();
    }

    /**
     * 删除公告
     * @Author   孙乔雨
     * @DateTime 2018-07-05
     * @param    string     $id [公告ID号]
     * @return   [type]         [description]
     */
    public function del($id=''){

        !empty($id) or die($this->error("请先选择数据"));
        $arr = explode(",", $id);
        $map['id'] = array("in",$id);
        $announce = M("announce");
        if ($announce->where($map)->delete()) {
            
            die($this->success("删除成功"));
        }else{
            die($this->error("删除失败"));
        }
    }
}