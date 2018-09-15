<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-07-03 09:34:48									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-07-06 10:13:31                      *****
 * --------------------------------------------------------------------------------------------------
 */
// 判断管理员是否登录
function is_login(){
	
	$user = $_SESSION['user'];
	if(empty($user)){
		return 0;
	}else{
		return $user['uid'];
	}
}

function error($msg=''){
    die('{"status":0,"msg":"'.$msg.'"}');
}

function success($msg=''){
    die('{"status":1,"msg":"'.$msg.'"}');
}

function dealtel($tel=''){
    if (!empty($tel)) {
        return substr($tel,0,3)."*****".substr($tel,8);
    }
}
function cutaddress($str='',$len=16){
    
    if (mb_strlen($str)>$len) {
        return mb_substr($str,0,$len)."...";
    }else{
        return $str;
    }
}
function dealwx($wx=''){
    if (!empty($wx)) {
        return substr($wx,0,2)."******";
    }else{
        return '';
    }
}

function nicknamedel($str=''){
    
    $len = mb_strlen($str);
    $star = "*";
    for ($i=0; $i < $len-1; $i++) { 
       if($i>1){
        $star.="*";
       }
    }
    if ($len>2) {
        return mb_substr($str,0,1).$star.mb_substr($str,-1);
    }else{
        return mb_substr($str,0,1).$star;
    }
}

// [标志；1：待确认；2：待服务；3：待评价；4：已完成；5：已取消；0：全部]
function getorderstatus($status=''){
    
    switch ($status) {
        case 1:
            $statustext = "待确认";
            break;
        case 2:
            $statustext = "待服务";
            break;
        case 3:
            $statustext = "待评价";
            break;
        case 4:
            $statustext = "已完成";
            break;
        case 5:
            $statustext = "已取消";
            break;
    }
    return $statustext;
}

// [标志；1：待确认；2：待服务；3，4：已完成；5：已取消；0：全部]
function getorshopderstatus($status=''){
    
    switch ($status) {
        case 1:
            $statustext = "待接单";
            break;
        case 2:
            $statustext = "待服务";
            break;
        case 3:
            $statustext = "已完成";
            break;
        case 4:
            $statustext = "已完成";
            break;
        case 5:
            $statustext = "已取消";
            break;
    }
    return $statustext;
}

function getordercolor($status=''){
    
    switch ($status) {
        case 1:
            $color = "red";
            break;
        case 2:
            $color = "red";
            break;
        case 3:
            $color = "red";
            break;
        case 4:
            $color = "green";
            break;
        case 5:
            $color = "#515151";
            break;
    }
    return $color;
}

// 无限极分类
function getTree($array){

    //第一步 构造数据
    $items = array();
    foreach($array as $value){
        $items[$value['id']] = $value;
    }
    //第二部 遍历数据 生成树状结构
    $tree = array();
    foreach($items as $key => $value){
    //如果pid这个节点存在
        if(isset($items[$value['pid']])){
            //把当前的$value放到pid节点的son中 注意 这里传递的是引用 为什么呢？
            $items[$value['pid']]['son'][] = &$items[$key];
        }else{
            $tree[] = &$items[$key];
        }
    }
    return $tree;
}

/**
 * 面包屑导航
 * @Author   孙乔雨
 * @DateTime 2018-07-04
 * @param    [type]     $arr [菜单列表]
 * @param    [type]     $id  [当前菜单ID号]
 * @return   [type]          [description]
 */
function familytree($arr,$id) {
    $tree = array();
     
    foreach($arr as $v) {
        if($v['id'] == $id) {// 判断要不要找父栏目
            if($v['pid'] > 0) { // parnet>0,说明有父栏目
                $tree = array_merge($tree,familytree($arr,$v['pid']));
            }
 
            $tree[] = $v; // 以找到上地为例
        }
    }
 
    return $tree;
}

/**
 * 获取密钥key
 * @Author   孙乔雨
 * @DateTime 2018-07-04
 * @param    string     $length [密钥长度]
 * @return   [type]             [description]
 */
function getKey($length=18){
    $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($str),0,$length);
}

/**
 * 模拟获取验证码
 * @Author   孙乔雨
 * @DateTime 2018-07-04
 * @param    integer    $length [长度]
 * @return   [type]             [description]
 */
function code($length=6){
    $number = "0123456789";
    // return substr(str_shuffle($str),0,$length);
    return "666666";
}

 /**
 * 递归实现无限极分类
 * @param $array 分类数据
 * @param $pid 父ID
 * @param $level 分类级别
 * @return $list 分好类的数组 直接遍历即可 $level可以用来遍历缩进
 */
function listGetTree($array, $pid =0, $level = 0){
    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($array as $key => $value){
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value['pid'] == $pid){
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            $value['html']  = str_repeat("──", $level);
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($array[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            listGetTree($array, $value['id'], $level+1);
        }
    }
    return $list;
}

 /**
 * 递归实现无限极分类
 * @param $array 分类数据
 * @param $pid 父ID
 * @param $level 分类级别
 * @return $list 分好类的数组 直接遍历即可 $level可以用来遍历缩进
 */
function listGetTree1($array, $pid =0, $level = 0){
    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($array as $key => $value){
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value['pid'] == $pid){
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            $value['html']  = str_repeat("──", $level);
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($array[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            listGetTree1($array, $value['id'], $level+1);
        }
    }
    return $list;
}