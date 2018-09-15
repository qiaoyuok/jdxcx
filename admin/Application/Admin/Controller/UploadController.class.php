<?php

/**
 * --------------------------------------------------------------------------------------------------
 * *****						@Author: sunqiaoyu												*****
 * *****						@Date:   2018-06-26 11:46:20									*****
 * *****						@Last Modified by:   gl003										*****
 * *****						@Last Modified time: 2018-06-27 17:13:27						*****
 * --------------------------------------------------------------------------------------------------
 */
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\FileDiyModel;
use Admin\Model\ImageDiyModel;
/**
 * 用户登录类
 */
class UploadController extends Controller{

	public function upload(){

		$date = date("Ym",time());
	  	$up = new FileDiyModel(array("filepath"=>"./Uploads/images/".$date."/", "allowtype"=>array("gif", "jpg", "png"),"maxsize"=>1048576));

	  	if($up->uploadFile("file")){

		    $filename=$up->getNewFileName();

		    $img=new ImageDiyModel("./Uploads/images/".$date."/");

		    $th_filename=$img->thumb($filename, 500, 500, "th_");

		    echo '{"status":1,"filename":"'."/Uploads/images/".date("Ym",time())."/".$th_filename.'"}';
	  	}else{
	    	echo '{"status":0,"msg":"'.$up->getErrorMsg().'"}';
	 	}
	}
}