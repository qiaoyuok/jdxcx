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
use \Think\Controller;
/**
 * 用户登录类
 */
class LoginapiController extends Controller{
	
	private $app = array(
		array("appid"=>"wx7fe7f621f73e5c28","appsecret"=>"26479436c120ecf1af0eb89fb3a2f293"),
		array("appid"=>"wx5c66d34269e41b02","appsecret"=>"00aff3832cd93f458dcc6704e370186e"),
		array("appid"=>"wx5c6ed82f77379fbc","appsecret"=>"2e4a46871591b8007059afd3d73e8e50"),
		array("appid"=>"wx7ad7b161d1cf29a7","appsecret"=>"0f1b50a97a9d766a0f4f63fdb19162a4")
	);
	/**
	 * 获取用户的openid
	 * @Author   孙乔雨
	 * @DateTime 2018-03-23
	 * @param    string     $code [code用户换取openid]
	 * @return   [type]           [description]
	 */
	public function getopenid($code='',$encryptedData='',$iv='',$flag=0){
		
		if (empty($code)) {
			echo '{"status":0,"msg":"没有传来code"}';
			exit;
		}

		// 获取用户openid或者unionid(此接口和微信公众号接口不同)
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$this->app[$flag]['appid']."&secret=".$this->app[$flag]['appsecret']."&js_code=".$code."&grant_type=authorization_code";

		// 采用curl抓取内容
		$ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_HEADER,0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
        $res = curl_exec($ch); 
        curl_close($ch);
        $sessionKey = json_decode($res,true);
        sleep(1);
        if($sessionKey){
        	$data = $this->getTel($encryptedData, $iv,$sessionKey['session_key']);

			if (!empty($data)) {
				$user = D("user");
				$map['rtel'] = array("eq",$data['purephonenumber']);
				$userinfo = $user->where($map)->find();
				if($userinfo){
					die($this->loginStatus($userinfo['uid']));
				}else{
					$uid = $user->add(array("rtel"=>$data['purephonenumber'],'create_time' =>date("Y-m-d H:i:i",time()),'nickname'=>$data['purephonenumber'],"tel"=>$data['purephonenumber']));
					if ($uid>0) {
						die($this->loginStatus($uid));
					}else{
						die('{"status":0,"msg":"登陆失败3"}');
					}
				}
			}else{
				die('{"status":0,"msg":"登陆失败,请重试"}');
			}
        }else{

        	die('{"status":0,"msg":"登陆失败1"}');
        }
	}


	public function login($code='',$iv='',$encryptedData='',$flag=0){
		
		!empty($code) or die('{"status":0,"msg":"code为空"}');
		!empty($iv) or die('{"status":0,"msg":"iv为空"}');
		!empty($encryptedData) or die('{"status":0,"msg":"encryptedData为空"}');

		$this->getopenid($code,$encryptedData, $iv,$flag);
	}

	// 返回用户登录态信息
	public function loginStatus($uid=''){

		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$data = md5(str_shuffle($str));
		$token = M("token");
		$tokeninfo = $token->where("uid = $uid")->find();
		if ($tokeninfo) {
			return '{"status":1,"uid":'.$uid.',"token":"'.$tokeninfo['token'].'"}';
		}else{
			if ($token->add(array("uid"=>$uid,"token"=>$data))) {
				return '{"status":1,"uid":'.$uid.',"token":"'.$data.'"}';
			}else{
				return '{"status":0,"msg":"登陆失败"}';
			}
		}
		
	}

	/**
	 * 获取用户手机号
	 * @Author   孙乔雨
	 * @DateTime 2018-03-23
	 * @param    string     $encryptedData [加密的用户数据]
	 * @param    string     $iv            [向量]
	 * @param    string     $sessionKey    [钥匙]
	 * @return   [type]                    [description]
	 */
	public function getTel($encryptedData='', $iv='',$sessionKey=''){
		
		if (strlen($sessionKey) != 24) {
			return false;
		}

		$aesKey=base64_decode($sessionKey);

        
		if (strlen($iv) != 24) {
			return false;
		}

		$aesIV=base64_decode($iv);

		$aesCipher=base64_decode($encryptedData);

		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

		return array_change_key_case(json_decode($result,true));

	}
}