<?php

namespace Admin\Controller;
use Think\Controller;
/**
 * 接口类文件
 */
class ApiController extends ApibaseController{
	
	const KEY = "c241a5adfce82243988443ebf79eeab7";

	/**
	 * 添加店铺认证
	 * @param string $uid               [用户ID号]
	 * @param string $yyzz              [营业执照]
	 * @param string $idz               [身份证正面]
	 * @param string $idf               [身份证反面]
	 * @param string $realname          [真实姓名]
	 * @param string $tel               [手机号]
	 * @param string $wx                [微信号]
	 * @param string $shopname          [店铺名]
	 * @param string $address 			[格式化地址]
	 * @param string $name           	[地址]
	 * @param string $latitude          [纬度]
	 * @param string $longitude         [经度]
	 * @param string $detail            [详细地址]
	 */
	public function addShop($uid='',$yyzz='',$idz='',$idf='',$realname='',$tel='',$wx='',$shopname='',$address='',$name='',$latitude='',$longitude='',$detail=''){
		
		!empty($yyzz) or error("请先上传营业执照");
		!empty($idf) or error("请先上传门头照片");
		!empty($realname) or error("请填写真实姓名");
		!empty($tel) or error("请填写手机号");
		$preg = "/^1[345678]\d{9}$/";
		if (!preg_match($preg,$tel)) {
			error("手机号格式不正确");
		}
		!empty($shopname) or error("请填写店铺名称");
		!empty($address) or error("请选择地址");
		!empty($detail) or error("请填写详细地址");

		$user = D("user");
		$userdata = $user->create($_POST);
		$user->where("uid = $uid")->save($userdata);

		$shop = D("shop");

		$_POST['create_time'] = date("Y-m-d H:i:s",time());
		$shopdata = $shop->create($_POST);
		if ($shop->add($shopdata)) {
			success("提交成功");
		}else{
			error("提交失败");
		}
	}

	/**
	 * 获取该用户下的店铺
	 * @param  string $uid [用户ID号]
	 * @return [type]      [description]
	 */
	public function getshopinfo($uid=''){

		$shop = M("shop");

		$shopinfo = $shop->where("uid = $uid")->find();

		if ($shopinfo) {
			if ($shopinfo['status'] == 1) {
				die('{"status":1,"msg":"恭喜您：已通过认证","color":"green"}');
			}else if($shopinfo['status'] == 0){
				die('{"status":0,"msg":"等待审核中...","color":"gray"}');
			}else{
				die('{"status":0,"msg":"抱歉：审核未通过","color":"red"}');
			}
		}else{
			die('{"status":2,"msg":"待提交"}');
		}
	}

	/**
	 * 获取用户信息
	 * @param  string $uid [用户ID号]
	 * @return [type]      [description]
	 */
	public function getuserinfo($uid=''){
		
		$userinfo = D("user u")->field("s.status,u.avatarurl,u.nickname,u.sex,u.tel,.u.wx,u.realname")->join("left join jd_shop s on s.uid = u.uid")->where("u.uid = $uid")->find();
		if (!empty($userinfo['avatarurl'])) {
			$userinfo['avatarurl'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$userinfo['avatarurl'];
		}
		die('{"status":1,"data":'.json_encode($userinfo).'}');
	}

	/**
	 * 添加维修服务
	 * @param string $uid     [用户ID号]
	 * @param string $des     [服务描述]
	 * @param string $ability [技能标签]
	 * @param string $pics    [图片列表]
	 */
	public function addFixed($uid='',$des='',$ability='',$pics=''){
		
		!empty(trim($des)) or error("请填写服务描述");
		!empty(trim($ability)) or error("请填写技能标签");

		$fixed = D("fixed");

		$_POST['create_time'] = date("Y-m-d H:i:s",time());
		$data = $fixed->create($_POST);
		if($fixed->add($data)){

			success("提交成功");
		}else{
			error("提交失败");
		}
	}

	/**
	 * 添加回收服务
	 * @param string $uid      [用户ID号]
	 * @param string $realname [真实姓名]
	 * @param string $tel      [手机号]
	 * @param string $wx       [微信号]
	 * @param string $address  [格式化地址]
	 * @param string $name     [地点名]
	 * @param string $detail   [详细地址]
	 * @param string $des      [服务描述]
	 * @param string $pics     [图片]
	 */
	public function addRecovery($uid='',$realname='',$tel='',$wx='',$address='',$name='',$detail='',$des='',$pics='',$latitude='',$longitude=''){
		
		!empty(trim($realname)) or error("姓名不能为空");
		!empty($tel) or error("手机号不能为空");
		$preg = "/^1[345678]\d{9}$/";
		if (!preg_match($preg,$tel)) {
			error("手机号格式不正确");
		}
		!empty($address)&&!empty($name)&&!empty($latitude)&&!empty($longitude) or error("请先选择地址");
		!empty($detail) or error("请填写详细地址");
		!empty($des) or error("请填写服务介绍");

		$recovery = D("recovery");

		$_POST['create_time'] = date("Y-m-d H:i:s",time());

		$user = D("user");
		$userdata = $user->create($_POST);


		$recoverydata = $recovery->create($_POST);
		if($recovery->add($recoverydata)||$user->where("uid = $uid")->save($userdata)){
			success("提交成功");
		}else{
			error("提交失败");
		}
	}

	/**
	 * 获取我的发布列表
	 * @param  string $uid 			[用户ID号]
	 * @param  string $latitude 	[维度]
	 * @param  string $longitude 	[经度]
	 * @return [type]      [description]
	 */
	public function getMyPublishList($uid='',$latitude='',$longitude='',$mark=0){

		switch ((int)$mark) {
			/**
			 * 维修服务列表
			 */
			case 0:
				$list = D("fixed f,jd_shop s,jd_user u")->field("u.realname,u.avatarurl,s.shopname,s.address,s.detail,s.latitude,s.idf,s.longitude,f.status,f.view,f.id,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance,f.id,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ,(SELECT count(*) FROM jd_fixed_order fo WHERE fo.fid = f.id AND (fo.status = 3 OR fo.status = 4)) count")->where("f.uid = $uid and s.uid = f.uid and s.uid = u.uid")->select();
				if (!empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['distance']<1) {
							$list[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
						}else{
							$list[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
						}

						$list[$k]['svgscore'] = sprintf("%.1f",$list[$k]['svgscore']);
						$list[$k]['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$list[$k]['idf'];
						$list[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
					}
				}
				break;

			/**
			 * 回收服务列表
			 */
			case 1:
				$list = D("recovery r,jd_user u")->field("r.*,u.realname,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - r.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(r.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - r.longitude * PI() / 180 ) / 2 ), 2 )))) distance")->where("r.uid = u.uid and r.uid = $uid")->select();
				if (!empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['distance']<1) {
							$list[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
						}else{
							$list[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
						}

						$list[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
						$list[$k]['des'] = cutaddress($v['des'],12);
					}
				}
				break;

			
			default:
				$list = D("fixed f,jd_shop s,jd_user u")->field("s.realname,u.avatarurl,s.shopname,s.address,s.detail,s.latitude,s.idf,s.longitude,f.status,f.view,f.id,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance,f.id,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ,(SELECT count(*) FROM jd_fixed_order fo WHERE fo.fid = f.id AND (fo.status = 3 OR fo.status = 4)) count")->where("f.uid = $uid and s.uid = f.uid and s.uid = u.uid")->select();
				if (!empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['distance']<1) {
							$list[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
						}else{
							$list[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
						}

						$list[$k]['svgscore'] = sprintf("%.1f",$list[$k]['svgscore']);
						$list[$k]['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$list[$k]['idf'];
						$list[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
					}
				}
				break;
		}


		if (!empty($list)) {
			die('{"status":1,"data":'.json_encode($list).'}');
		}else{
			die('{"status":0,"msg":"发布列表空空如也"}');
		}
		
	}

	/**
	 * 计算两点之间的直线距离
	 * @param  string $latitude  [当前用户纬度]
	 * @param  string $longitude [当前用户经度]
	 * @param  string $slatitude [商家纬度]
	 * @param  string $slongitude [商家经度]
	 * @return [type]            [description]
	 */
	public function dist($latitude='',$longitude='',$slatitude='',$slongitude=''){
		
		$url = "https://restapi.amap.com/v3/distance?key=".self::KEY."&origins=".sprintf("%.6f", $longitude).",".sprintf("%.6f", $latitude)."&destination=".sprintf("%.6f", $slongitude).",".sprintf("%.6f", $slatitude)."&type=0&output=JSON";

		// 采用curl抓取内容
		$ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_HEADER,0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
        $res = curl_exec($ch); 
        curl_close($ch);
        $res = json_decode($res,true);
        if ($res['status'] == 1) {
        	if ($res['results'][0]['distance']>1000) {
        		return (sprintf("%.2f",$res['results'][0]['distance']/1000))."km";
        	}else{
        		return $res['results'][0]['distance']."m";
        	}
        }else{
        	return "null";
        }
	}

	/**
	 * 获取维修服务详情
	 * @param  string $uid       [用户ID号]
	 * @param  string $id        [维修服务ID号]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]            [description]
	 */
	public function getFixedDetail($uid='',$id='',$latitude='',$longitude=''){
		
		$fixeddetail = D("fixed f,jd_shop s,jd_user u")->field("f.des,f.ability,s.*,u.avatarurl")->where("f.uid = s.uid and s.uid = u.uid and s.uid = $uid")->find();

		$fixeddetail['distance'] = $this->dist($latitude,$longitude,$fixeddetail['latitude'],$fixeddetail['longitude']);
		$fixeddetail['latitude'] = (float)$latitude;
		$fixeddetail['longitude'] = (float)$longitude;
		die('{"status":1,"data":'.json_encode($fixeddetail).'}');
	}

	/**
	 * 获取维修服务列表  规则：获取距离在50km内的服务商
	 * @param  string $latitude  	[纬度]
	 * @param  string $longitude 	[(经度)]
	 * @param  string $page 		[页码]
	 * @param  string $limit 		[每页记录数]
	 * @return [type]            [description]
	 */
	public function getFixedList($latitude='',$longitude='',$page='',$limit=''){
		
		$fixedlist = D("shop s,jd_user u,jd_fixed f")
					->field("s.*,u.avatarurl,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance")
					->where("s.uid = u.uid and s.uid = f.uid")
					->page($page)
					->limit($limit)
					->select();

		if (!empty($fixedlist)) {
			foreach ($fixedlist as $k => $v) {
				if ($v['distance']<1) {
					$fixedlist[$k]['distance'] = sprintf("%.2f",$v['distance']*1000)."m";
				}else{
					$fixedlist[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
				}
			}
		}
		if (!empty($fixedlist)) {
			die('{"status":1,"data":'.json_encode($fixedlist).'}');
		}else{
			die('{"status":0,"msg":"数据为空"}');
		}
	}

	/**
	 * 获取该用户对该服务商户商的收藏状态
	 * @param  string $uid  [description]
	 * @param  string $flag [收藏的类型；1：维修服务；2：废品回收站；3：附近商圈；4：品质货源]
	 * @param  string $s_id [description]
	 * @return [flag]       [description]
	 */
	public function getCollectStatus($uid='',$flag='',$s_id=''){
		
		$collect = M("collect");

		$collectinfo = $collect->where("uid = $uid and flag = $flag and s_id = $s_id")->find();

		if (!empty($collectinfo)) {
			die('{"status":1,"msg":"已收藏"}');
		}else{
			die('{"status":0,"msg":"未收藏"}');
		}
	}

	/**
	 * 该用户对商户下该服务的收藏操作
	 * @param  string $uid  [description]
	 * @param  string $flag [收藏的类型；1：维修服务；2：废品回收站；3：附近商圈；4：品质货源]
	 * @param  string $s_id [description]
	 * @return [flag]       [description]
	 */
	public function collectOption($uid='',$flag='',$s_id=''){
		
		$collect = D("collect");
		$collectinfo = $collect->where("uid = $uid and flag = $flag and s_id = $s_id")->find();

		if (!empty($collectinfo)) {
			
			if ($collect->where("id = ".$collectinfo['id'])->delete()) {
				die('{"status":1,"msg":"取消收藏成功"}');
			}else{
				die('{"status":0,"msg":"取消收藏失败"}');
			}

		}else{
			$data = $collect->create($_POST);
			if ($collect->add($data)) {
				die('{"status":1,"msg":"收藏成功"}');
			}else{
				die('{"status":0,"msg":"收藏失败"}');
			}
		}
	}

	/**
	 * 添加地址
	 * @param  string $uid       [用户ID号]
	 * @param  string $realname  [真实姓名]
	 * @param  string $tel       [手机号]
	 * @param  string $address   [地址]
	 * @param  string $name      [地点名]
	 * @param  string $detail    [详细描述]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]            [description]
	 */
	public function addAddress($uid='',$realname='',$tel='',$address='',$name='',$detail='',$latitude='',$longitude=''){
		
		!empty($realname) or error("姓名不能为空");
		!empty($tel) or error("手机号不能为空");
		$preg = "/^1[345678]\d{9}$/";
		if (!preg_match($preg,$tel)) {
			error("手机号格式不正确");
		}
		!empty($name) or error("请先选择地址");
		!empty($detail) or error("请填写详细地址");

		$address_obj = D("address");
		$data = $address_obj->create($_POST);
		if ($address_obj->add($data)) {
			success("添加成功");
		}else{
			error("添加失败");
		}
	}

	/**
	 * 获取用户地址列表
	 * @param  string $uid [用户ID号]
	 * @return [type]      [description]
	 */
	public function getAddressList($uid=''){
		$address = M("address");

		$addresslist = $address->where("uid = $uid")->select();

		if (!empty($addresslist)) {
			die('{"status":1,"data":'.json_encode($addresslist).'}');
		}else{
			die('{"status":0,"msg":"您的地址列表空空如也"}');
		}
	}

	/**
	 * 删除地址
	 * @param  string $id [地址ID号]
	 * @return [type]     [description]
	 */
	public function deladdress($id=''){
		!empty($id) or error("请选择数据");

		$address = M("address");
		if($address->where("id = $id")->delete()){
			die('{"status":1,"data":"删除成功"}');
		}else{
			die('{"status":0,"data":"删除失败"}');
		}
	}

	/**
	 * 获取地址详情
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function getAddressDetail($id=''){
		
		!empty($id) or error("请选择数据");

		$address = M("address");
		$addressinfo = $address->where("id = $id")->find();
		if($addressinfo){
			die('{"status":1,"data":'.json_encode($addressinfo).'}');
		}else{
			die('{"status":0,"msg":"获取失败"}');
		}
	}

	/**
	 * 获取默认地址详情
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function getDefaultAddressDetail($uid=''){
		
		!empty($uid) or error("请选择数据");

		$address = M("address");
		$addressinfo = $address->where("uid = $uid and status = 1")->find();
		if($addressinfo){
			die('{"status":1,"data":'.json_encode($addressinfo).'}');
		}else{
			die('{"status":0,"msg":"获取失败"}');
		}
	}

	/**
	 * 获取地址详情
	 * @param  string $id [description]
	 * @param  string $realname  [真实姓名]
	 * @param  string $tel       [手机号]
	 * @param  string $address   [地址]
	 * @param  string $name      [地点名]
	 * @param  string $detail    [详细描述]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]     [description]
	 */
	public function editAddress($id='',$uid='',$realname='',$tel='',$address='',$name='',$detail='',$latitude='',$longitude=''){
		
		!empty($id) or error("请选择数据");		
		!empty($realname) or error("姓名不能为空");
		!empty($tel) or error("手机号不能为空");
		$preg = "/^1[345678]\d{9}$/";
		if (!preg_match($preg,$tel)) {
			error("手机号格式不正确");
		}
		!empty($name) or error("请先选择地址");
		!empty($detail) or error("请填写详细地址");

		$address_obj = D("address");
		$data = $address_obj->create($_POST);
		if ($address_obj->where("id = $id and uid = $uid")->save($data)) {
			success("编辑成功");
		}else{
			error("编辑失败");
		}
	}

	/**
	 * 获取地址详情
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function chooseAddress($id='',$uid=''){
		
		!empty($id) or error("请选择数据");

		$address = M("address");
		$address->where("uid = $uid")->save(array("status"=>0));
		
		if($address->where("id = $id")->save(array("status"=>1))){
			die('{"status":1,"msg":"选择成功"}');
		}else{
			die('{"status":0,"msg":"选择失败"}');
		}
	}

	/**
	 * 添加维修服务订单
	 * @param string $addressdetail [下单用户详情]
	 * @param string $remark		[用户留言]
	 * @param string $fid 			[维修服务的ID号]
	 */
	public function addFixedOrder($addressdetail='',$remark='',$fid='',$uid=''){
		  !empty($addressdetail) or error("请先选择地址");
		  !empty($remark) or error("请填写留言信息");
		  !empty($fid) or error("缺少参数");

		  $fixed_order = D("fixed_order");
		  $_POST['create_time'] =date("Y-m-d H:i:s",time());
		  $data = $fixed_order->create($_POST);
		  if($fixed_order->add($date)){
		  	die('{"status":1,"msg":"预约成功"}');
		  }else{

		  	die('{"status":0,"msg":"预约失败"}');
		  }
	}

	/**
	 * 获取我得预约列表
	 * @param  string $uid  [用户ID号]
	 * @param  string $mark [标志；1：待确认；2：待服务；3：待评价；4：已完成；5：已取消；0：全部]
	 * @return [type]       [description]
	 */
	public function getMyOrderList($uid='',$mark='',$page=1,$limit=10){
		
		$where[] = "fo.uid = $uid and fo.fid = f.id and f.uid = s.uid";
		if(!empty($mark)){
			$where['fo.status'] = array("eq",$mark);
		}
		$fixedorderlist = D("fixed_order fo,jd_fixed f,jd_shop s")->field("s.shopname,fo.*")->where($where)->page($page)->limit($limit)->select();

		if (!empty($fixedorderlist)) {
			foreach ($fixedorderlist as $k => $v) {
				$fixedorderlist[$k]['addressdetail'] = json_decode($v['addressdetail'],true);
				$fixedorderlist[$k]['statustext'] = getorderstatus((int)$v['status']);
				$fixedorderlist[$k]['color'] = getordercolor((int)$v['status']);
			}
		}

		if (!empty($fixedorderlist)) {
			die('{"status":1,"data":'.json_encode($fixedorderlist).'}');
		}else{
			die('{"status":0,"data":"空空如也"}');
		}
	}

	/**
	 * 获取商家预约列表
	 * @param  string $uid  [商家用户ID号]
	 * @param  string $mark [标志；1：待确认；2：待服务；3,4：已完成；5：已取消；0：全部]
	 * @return [type]       [description]
	 */
	public function getShopOrderList($uid='',$mark='',$page=1,$limit=10){
		
		$where[] = "f.uid = $uid and f.id = fo.fid";
		if(!empty($mark)){
			if ($mark == 3 || $mark == 4) {
				$map['fo.status'] = array("eq",3);
				$map['fo.status'] = array("eq",4);
	            $map['_logic'] = 'OR';
	            $where[] = $map;
			}else{
				$where['fo.status'] = array("eq",$mark);
			}
		}
		$fixedorderlist = D("fixed_order fo,jd_fixed f")->field("fo.*")->where($where)->page($page)->limit($limit)->select();

		if (!empty($fixedorderlist)) {
			foreach ($fixedorderlist as $k => $v) {
				$fixedorderlist[$k]['addressdetail'] = json_decode($v['addressdetail'],true);
				$fixedorderlist[$k]['statustext'] = getorshopderstatus((int)$v['status']);
				$fixedorderlist[$k]['color'] = getordercolor((int)$v['status']);
				$fixedorderlist[$k]['remark'] = cutaddress($fixedorderlist[$k]['remark'],12);
			}
		}

		if (!empty($fixedorderlist)) {
			die('{"status":1,"data":'.json_encode($fixedorderlist).'}');
		}else{
			die('{"status":0,"data":"空空如也"}');
		}
	}

	/**
	 * 获取预约详情
	 * @param  string $id [预约ID号]
	 * @return [type]     [description]
	 */
	public function getOrderDetail($id='',$latitude='',$longitude=''){
		!empty($id) or error("缺少参数");

		/**
		 * 获取预约订单详详情
		 * @var [type]
		 */
		$fixedorderdetail = D("fixed_order")
		->where("id = $id")
		->find();
		$fixedorderdetail['addressdetail'] = json_decode($fixedorderdetail['addressdetail'],true);
		$fixedorderdetail['addressdetail']['latitude'] = (float)$fixedorderdetail['addressdetail']['latitude'];
		$fixedorderdetail['addressdetail']['longitude'] = (float)$fixedorderdetail['addressdetail']['longitude'];
		/**
		 * 获取服务商信息
		 */
		
		$fixeddetail = D("fixed f,jd_shop s,jd_user u")
		->field("f.id,s.shopname,u.realname,u.tel,u.wx,s.address,s.detail,s.idf,s.name,f.view,s.status,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ,(SELECT count(*) FROM jd_fixed_order fo WHERE fo.fid = f.id AND (fo.status = 3 OR fo.status = 4)) count")
		->where("f.id = ".$fixedorderdetail['fid']." and f.uid = s.uid and f.uid = u.uid")
		->find();

		$fixedorderdetail['statustext'] = getorderstatus((int)$fixedorderdetail['status']);
		$fixeddetail['svgscore'] = sprintf("%.1f",$fixeddetail['svgscore']);
		$fixedorderdetail['color'] = getordercolor((int)$fixedorderdetail['status']);
		$fixeddetail['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$fixeddetail['idf'];
		$fixeddetail['tmp_address'] = cutaddress($fixeddetail['address'].$fixeddetail['name'].$fixeddetail['detail']);
		if ($fixeddetail['distance']<1) {
			$fixeddetail['distance'] = sprintf("%.2f",$fixeddetail['distance']*1000)."m";
		}else{
			$fixeddetail['distance'] = sprintf("%.2f",$fixeddetail['distance'])."km";
		}

		$fixedorderdetail['fixedinfo'][] = $fixeddetail;
		die('{"status":1,"data":'.json_encode($fixedorderdetail).'}');
	}

	/**
	 * 改变预约状态
	 * @param  string $fuid    [客户ID号]
	 * @param  string $status [description]
	 * @return [type]         [description]
	 */
	public function changeOrderStatus($id='',$status=''){
		!empty($status) or error("缺少参数");

		$fixed_order = M("fixed_order");

		if($fixed_order->where("id = $id")->save(array("status"=>$status))){
			die('{"status":1,"msg":"操作成功"}');
		}else{

			die('{"status":0,"msg":"操作失败"}');
		}
	}

	/**
	 * 维修服务评价提交
	 * @param  string $fid     [维修服务列表ID号]
	 * @param  string $uid     [评价者ID号]
	 * @param  string $content [评价内容]
	 * @param  string $score   [评价分数]
	 * @param  string $oid   [预约订单ID号]
	 * @return [type]          [description]
	 */
	public function fixedAssess($fid='',$uid='',$content='',$score='',$oid=''){
		
		!empty(trim($content)) or error("评价内容不能为空");

		$assess = D("assess");
		$data = $assess->create($_POST);
		$data['create_time'] = date("Y.m.d",time());

		if ($assess->add($data)) {
			// 更改预约订单状态为完成
			$fixed_order = M("fixed_order");

			if($fixed_order->where("id = $oid")->save(array("status"=>4))){
				die('{"status":1,"msg":"操作成功"}');
			}else{

				die('{"status":0,"msg":"操作失败"}');
			}
		}else{
			die('{"status":0,"msg":"操作失败1"}');
		}
	}

	/**
	 * 获取收藏列表
	 * @param  string $latitude  	[纬度]
	 * @param  string $longitude 	[(经度)]
	 * @param  string $page 		[页码]
	 * @param  string $limit 		[每页记录数]
	 * @param  string $flag 		[收藏的类型；1：维修服务；2：废品回收站；3：附近商圈；4：品质货源]
	 * @return [type]            [description]
	 */
	public function getCollectList($latitude='',$longitude='',$page=1,$limit=10,$flag='',$uid=''){

		$where = "c.uid = $uid and c.flag = $flag";

		switch ((int)$flag) {
			case 1:
				$where .= " and c.s_id = f.id and f.uid = s.uid and u.uid = s.uid";
				$order = "distance asc";

				$list = D("shop s,jd_user u,jd_fixed f,jd_collect c")
					->field("s.shopname,u.realname,s.address,s.name,s.detail,s.detail,s.idf,f.view,s.status,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance,f.id,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ,(SELECT count(*) FROM jd_fixed_order fo WHERE fo.fid = f.id AND (fo.status = 3 OR fo.status = 4)) count")
					->where($where)
					->page($page)
					->limit($limit)
					->order($order)
					->select();

				if (!empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['distance']<1) {
							$list[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
						}else{
							$list[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
						}

						$list[$k]['svgscore'] = sprintf("%.1f",$list[$k]['svgscore']);
						$list[$k]['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$list[$k]['idf'];
						$list[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
					}
				}

				break;
			case 2:
				$where .= " and c.s_id = r.id and r.uid = u.uid";

				$list = D("recovery r,jd_collect c,jd_user u")
					->field("u.realname,r.*,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - r.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(r.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - r.longitude * PI() / 180 ) / 2 ), 2 )))) distance")
					->where($where)
					->page($page)
					->limit($limit)
					->order($order)
					->select();

				if (!empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['distance']<1) {
							$list[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
						}else{
							$list[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
						}
						$list[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
					}
				}
				break;
			default:
				# code...
				break;
		}

		if (!empty($list)) {
			die('{"status":1,"data":'.json_encode($list).'}');
		}else{
			die('{"status":0,"msg":"数据为空"}');
		}
	}

	/**
	 * 获取维修服务的详情
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function getEditFixedDetail($id='',$uid=''){
		
		$fixedinfo = D("fixed")->where("id = $id and uid = $uid")->find();
		$fixedinfo['pics'] = json_decode($fixedinfo['pics'],true);
		foreach ($fixedinfo['pics'] as $k => $v) {
			$fixedinfo['pics'][$k] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$v['url'];
			$fixedinfo['serv'][]['url'] = $v['url'];
		}
		die('{"status":1,"data":'.json_encode($fixedinfo).'}');
	}

	/**
	 * 编辑维修服务信息
	 * @param  string $id      [维修服务ID号]
	 * @param  string $des     [描述]
	 * @param  string $ability [技能]
	 * @param  string $pics    [图片]
	 * @return [type]          [description]
	 */
	public function editFixed($id='',$des='',$ability='',$pics='',$uid=''){
		
		!empty(trim($des)) or error("请填写服务描述");
		!empty(trim($ability)) or error("请填写技能标签");

		$pics = json_decode($pics,true);
		foreach ($pics as $k => $v) {
			if (empty($v['url'])) {
				unset($pics[$k]);
			}
		}
		$_POST['pics'] = json_encode($pics);
		$fixed = D("fixed");

		$data = $fixed->create($_POST);
		if($fixed->where("id = $id and uid = $uid")->save($data)){

			success("编辑成功");
		}else{
			error("编辑失败");
		}
	}

	/**
	 * 编辑废品回收服务信息
	 * @param  string $id        [服务ID号]
	 * @param  string $realname  [真实姓名]
	 * @param  string $tel       [手机号]
	 * @param  string $wx        [微信号]
	 * @param  string $address   [格式化地址]
	 * @param  string $name      [地点名]
	 * @param  string $detail    [地址详情]
	 * @param  string $des       [描述]
	 * @param  string $pics      [图片]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]            [description]
	 */
	public function editRecovery($id='',$uid='',$realname='',$tel='',$wx='',$address='',$name='',$detail='',$des='',$pics='',$latitude='',$longitude=''){
		!empty($id) or error("缺少参数");

		if(IS_POST){

			!empty(trim($realname)) or error("姓名不能为空");
			!empty($tel) or error("手机号不能为空");
			$preg = "/^1[345678]\d{9}$/";
			if (!preg_match($preg,$tel)) {
				error("手机号格式不正确");
			}
			!empty($address)&&!empty($name)&&!empty($latitude)&&!empty($longitude) or error("请先选择地址");
			!empty($detail) or error("请填写详细地址");
			!empty($des) or error("请填写服务介绍");

			$recovery = D("recovery");

			$user = D("user");
			$userdata = $user->create($_POST);

			$recoverydata = $recovery->create($_POST);
			if($recovery->where("id = $id and uid = $uid")->save($recoverydata)||$user->where("uid = $uid")->save($userdata)){
				success("编辑成功");
			}else{
				error("编辑失败");
			}
		}

		$recoveryinfo = D("recovery r,jd_user u")->where("r.id = $id and r.uid = u.uid")->field("r.*,u.realname,u.tel,u.wx")->find();
		$recoveryinfo['pics'] = json_decode($recoveryinfo['pics'],true);
		foreach ($recoveryinfo['pics'] as $k => $v) {
			$recoveryinfo['pics'][$k] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$v['url'];
			$recoveryinfo['serv'][]['url'] = $v['url'];
		}
		// unset($recoveryinfo['pics']);
		die('{"status":1,"data":'.json_encode($recoveryinfo).'}');
	}

	/**
	 * 删除发布得服务
	 * @param  string $id [维修服务ID号A]
	 * @return [type]    [description]
	 */
	public function delPublish($id='',$mark=0,$uid=''){
		!empty(trim($id)) or error("缺少参数");

		switch ((int)$mark) {
			case 0:
				$fixed = M("fixed");
				$delres = $fixed->where("id = $id and uid = $uid")->delete();
				break;
			case 1:
				$recovery = M("recovery");
				$delres = $recovery->where("id = $id and uid = $uid")->delete();
				break;
		}

		if($delres){
			success("删除成功");
		}else{
			error("删除失败");
		}
	}

	/**
	 * 下架发布得服务
	 * @param  string $id [维修服务ID号A]
	 * @return [type]    [description]
	 */
	public function downPublish($id='',$mark=0,$uid=''){
		!empty(trim($id)) or error("缺少参数");
		$data = array("status"=>-1);
		switch ((int)$mark) {
			case 0:
				$fixed = M("fixed");
				$delres = $fixed->where("id = $id and uid = $uid")->save($data);
				break;
			case 1:
				$recovery = M("recovery");
				$delres = $recovery->where("id = $id and uid = $uid")->save($data);
				break;
		}

		if($delres){
			success("下架成功");
		}else{
			error("下架失败");
		}
	}

	/**
	 * 上架发布得服务
	 * @param  string $id [维修服务ID号A]
	 * @return [type]    [description]
	 */
	public function upPublish($id='',$mark=0,$uid=''){
		!empty(trim($id)) or error("缺少参数");
		$data = array("status"=>1);
		switch ((int)$mark) {
			case 0:
				$fixed = M("fixed");
				$delres = $fixed->where("id = $id and uid = $uid")->save($data);
				break;
			case 1:
				$recovery = M("recovery");
				$delres = $recovery->where("id = $id and uid = $uid")->save($data);
				break;
		}

		if($delres){
			success("上架成功");
		}else{
			error("上架失败");
		}
	}

	/**
	 * 编辑用户信息
	 * @param  string $uid [用户ID号]
	 * @return [type]      [description]
	 */
	public function editUserinfo($uid='',$avatarurl='',$sex='',$nickname='',$tel='',$wx='',$realname=''){
		
		$user = D("user");

		if (IS_POST) {
			!empty(trim($nickname)) or error("昵称不能为空");
			!empty(trim($tel)) or error("手机号不能为空");
			$preg = "/^1[345678]\d{9}$/";
			if (!preg_match($preg,$tel)) {
				error("手机号格式不正确");
			}
			!empty(trim($realname)) or error("姓名不能为空");
			$data = $user->create($_POST);
			if(empty($data['avatarurl'])){
				unset($data['avatarurl']);
			}
			if($user->where("uid = $uid")->save($data)){
				success("保存成功");
			}else{
				error("保存失败");
			}
		}

		$userinfo = $user->where("uid = $uid")->find();
		if ($userinfo['avatarurl']) {
			$userinfo['avatarurl'] =  $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$userinfo['avatarurl'];
		}
		if ($userinfo) {
			die('{"status":1,"data":'.json_encode($userinfo).'}');
		}else{
			die('{"status":0,"msg":"获取失败"}');
		}
	}

	/**
	 * 编辑用户申请信息
	 * @param  string $uid [用户ID号]
	 * @return [type]      [description]
	 */
	public function editShop($uid='',$yyzz='',$idz='',$idf='',$realname='',$tel='',$wx='',$shopname='',$formatted_address='',$address='',$latitude='',$longitude='',$detail=''){
		

		if(IS_POST){
			$shop = D("shop");
			!empty($yyzz) or error("请先上传营业执照");
			!empty($idf) or error("请先上传门头照片");
			!empty($realname) or error("请填写真实姓名");
			!empty($tel) or error("请填写手机号");
			$preg = "/^1[345678]\d{9}$/";
			if (!preg_match($preg,$tel)) {
				error("手机号格式不正确");
			}
			!empty($shopname) or error("请填写店铺名称");
			!empty($address) or error("请选择地址");
			!empty($detail) or error("请填写详细地址");

			$user = D("user");
			// 修改用户信息
			$userdata = $user->create($_POST);

			/**
			 * 编辑店铺认证信息
			 * @var [type]
			 */
			$shopdata = $shop->create($_POST);
			$shopdata['status'] = 0;
			if ($shop->where("uid = $uid")->save($shopdata)||$user->where("uid = $uid")->save($userdata)) {
				success("保存成功");
			}else{
				error("保存失败fdsf111");
			}
		}

		$shopinfo = D("shop s,jd_user u")->field("s.*,u.realname,u.tel,u.wx")->where("s.uid = $uid and u.uid = s.uid")->find();

		if ($shopinfo['yyzz']) {
			$shopinfo['yyzzr'] =  $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$shopinfo['yyzz'];
		}
		if ($shopinfo['idz']) {
			$shopinfo['idzr'] =  $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$shopinfo['idz'];
		}
		if ($shopinfo['idf']) {
			$shopinfo['idfr'] =  $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$shopinfo['idf'];
		}
		die('{"status":1,"data":'.json_encode($shopinfo).'}');

	}
}