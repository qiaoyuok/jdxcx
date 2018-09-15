<?php


namespace Admin\Controller;
use Think\Controller;

/**
 * 接口入口类
 */
class ApicomController extends Controller{

	const KEY = "c241a5adfce82243988443ebf79eeab7";

	/**
	 * 获取维修服务列表  规则：获取距离在50km内的服务商
	 * @param  string $latitude  	[纬度]
	 * @param  string $longitude 	[(经度)]
	 * @param  string $page 		[页码]
	 * @param  string $limit 		[每页记录数]
	 * @param  string $keyword 		[关键字搜索]
	 * @return [type]            [description]
	 */
	public function getFixedList($latitude='',$longitude='',$page=1,$limit=10,$keyword='',$sort="",$area='',$serv=''){
			
		
		if (!empty($keyword)) {
			$map1['u.realname'] = array("like","%$keyword%");
            $map1['s.shopname'] = array("like","%$keyword%");
            $map1['f.ability'] = array("like","%$keyword%");
            $map1['_logic'] = 'OR';
            $where[] = $map1;
		}
		$where['f.status'] = array("eq",1);
		$order = "distance asc";

		//排序，默认是按距离排序，access评价由高到低排序，all综合排序，先按好评排序再按距离排序
		if($sort == "assess"){
			$order = "svgscore desc";
		}elseif ($sort == "all") {
			$order = "svgscore desc,distance asc";
		}

		if (!empty($area)) {
			$map2['s.name'] = array("like","%$area%");
            $map2['s.address'] = array("like","%$area%");
            $map2['s.detail'] = array("like","%$area%");
            $map2['_logic'] = 'OR';
            $where[] = $map2;
		}

		if (!empty($serv)) {
			$map3['f.ability'] = array("like","%$serv%");
            $map3['s.shopname'] = array("like","%$serv%");
            $map3['_logic'] = 'OR';
            $where[] = $map3;
		}

		$where[] = "s.uid = u.uid and s.uid = f.uid";

		$fixedlist = D("shop s,jd_user u,jd_fixed f")
					->field("s.shopname,u.realname,s.address,s.detail,s.idf,s.name,s.status,f.view,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - s.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(s.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - s.longitude * PI() / 180 ) / 2 ), 2 )))) distance,f.id,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ,(SELECT count(*) FROM jd_fixed_order fo WHERE fo.fid = f.id AND (fo.status = 3 OR fo.status = 4)) count")
					->where($where)
					->page($page)
					->limit($limit)
					->order($order)
					->select();

		if (!empty($fixedlist)) {
			foreach ($fixedlist as $k => $v) {
				if ($v['distance']<1) {
					$fixedlist[$k]['distance'] = sprintf("%.0f",$v['distance']*1000)."m";
				}else{
					$fixedlist[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
				}

				$fixedlist[$k]['svgscore'] = sprintf("%.1f",$fixedlist[$k]['svgscore']);
				$fixedlist[$k]['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$fixedlist[$k]['idf'];
				$fixedlist[$k]['tmp_address'] = cutaddress($v['address'].$v['name'].$v['detail']);
			}
		}
		if (!empty($fixedlist)) {
			die('{"status":1,"data":'.json_encode($fixedlist).'}');
		}else{
			die('{"status":0,"msg":"数据为空"}');
		}
	}

	/**
	 * 获取热门服务
	 * @return [type] [description]
	 */
	public function getServ(){
		$serv = M("serv");
		$servlist = $serv->select();
		die('{"status":1,"data":'.json_encode($servlist).'}');
	}

	/**
	 * 获取维修服务详情
	 * @param  string $uid       [用户ID号]
	 * @param  string $id        [维修服务ID号]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]            [description]
	 */
	public function getFixedDetail($id='',$latitude='',$longitude=''){
		
		!empty($id) or error("缺少參數");

		$fixeddetail = D("fixed f,jd_shop s,jd_user u")->field("f.des,f.ability,f.id fid,s.*,f.view,f.pics,u.realname,u.tel,u.wx,(select count(*) from jd_fixed_order fo where fo.fid = f.id and (fo.status = 3 or fo.status = 4)) count,(SELECT AVG(score) FROM jd_assess a WHERE a.fid = f.id) svgscore ")->where("f.uid = s.uid and f.uid = u.uid and f.id = $id")->find();

		$fixeddetail['distance'] = $this->dist($latitude,$longitude,$fixeddetail['latitude'],$fixeddetail['longitude']);
		$fixeddetail['latitude'] = (float)$fixeddetail['latitude'];
		$fixeddetail['longitude'] = (float)$fixeddetail['longitude'];
		$fixeddetail['pics'] = json_decode($fixeddetail['pics'],true);
		$fixeddetail['idf'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$fixeddetail['idf'];
		$fixeddetail['svgscore'] = sprintf("%.1f",$fixeddetail['svgscore'] );
		// 格式图片列表
		if (!empty($fixeddetail['pics'])) {
			foreach ($fixeddetail['pics'] as $k => $v) {
				$fixeddetail['pics'][$k] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$v['url']; 
			}
		}

		D("fixed")->where("id = ".$fixeddetail['fid'])->save(array("view"=>$fixeddetail['view']+1));

		// 格式技能标签
		if (!empty($fixeddetail['ability'])) {
			$fixeddetail['ability'] = explode(" ",$fixeddetail['ability']);
		}

		die('{"status":1,"data":'.json_encode($fixeddetail).'}');
	}

	/**
	 * 获取回收服务详情
	 * @param  string $uid       [用户ID号]
	 * @param  string $id        [服务ID号]
	 * @param  string $latitude  [纬度]
	 * @param  string $longitude [经度]
	 * @return [type]            [description]
	 */
	public function getRecoveryDetail($id='',$latitude='',$longitude=''){
		
		!empty($id) or error("缺少參數");

		$recoverydetail = D("recovery r,jd_user u")->field("r.*,u.realname,u.tel,u.wx,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - r.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(r.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - r.longitude * PI() / 180 ) / 2 ), 2 )))) distance")->where("r.uid = u.uid and r.id = $id")->find();

		if ($recoverydetail['distance']<1) {
			$recoverydetail['distance'] = sprintf("%.2f",$recoverydetail['distance']*1000)."m";
		}else{
			$recoverydetail['distance'] = sprintf("%.2f",$recoverydetail['distance'])."km";
		}

		$recoverydetail['latitude'] = (float)$recoverydetail['latitude'];
		$recoverydetail['longitude'] = (float)$recoverydetail['longitude'];
		$recoverydetail['pics'] = json_decode($recoverydetail['pics'],true);
		// 格式图片列表
		if (!empty($recoverydetail['pics'])) {
			foreach ($recoverydetail['pics'] as $k => $v) {
				$recoverydetail['pics'][$k] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$v['url']; 
			}
		}

		$recoverydetail['texttel'] = dealtel($recoverydetail['tel']);
		$recoverydetail['textwx'] = dealwx($recoverydetail['wx']);

		D("recovery")->where("id = ".$id)->save(array("view"=>$recoverydetail['view']+1));

		die('{"status":1,"data":'.json_encode($recoverydetail).'}');
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
	 * 获取评论列表
	 * @param  string $fid [维修服务ID号]
	 * @return [type]      [description]
	 */
	public function getAssessList($fid='',$page=1,$limit=10){
		
		!empty($fid) or error("缺少参数");

		$assesslist = D("assess a,jd_user u")
		->where("a.uid = u.uid and fid = $fid")
		->field("a.*,u.avatarurl,u.nickname")
		->page($page)
		->limit($limit)
		->select();

		$assessinfo =  D("assess a,jd_user u")
		->where("a.uid = u.uid and fid = $fid")
		->field("avg(a.score) svgscore,count(*) count")
		->find();

		$assessinfo['svgscore'] = sprintf("%.1f",$assessinfo['svgscore']);

		if (!empty($assesslist)) {
			$scores = 0;
			foreach ($assesslist as $k => $v) {
				$assesslist[$k]['avatarurl'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$v['avatarurl'];
				$assesslist[$k]['nickname'] = nicknamedel($v['nickname']);
				$arr = [];
				for ($i=0; $i < 5; $i++) { 
					if($i<$v['score']){
						$arr[] = 1;
					}else{
						$arr[] = 0;
					}
				}

				$assesslist[$k]['assessstar'] = $arr;
			}
			$data['assesslist'] = $assesslist;
			$data['assessinfo'] = $assessinfo;
			die('{"status":1,"data":'.json_encode($data).'}');

		}else{
			die('{"status":0,"msg":"没有更多了"}');
		}
	}

	/**
	 * 获取回收服务列表
	 * @param  string  $latitude  [纬度]
	 * @param  string  $longitude [经度]
	 * @param  integer $page      [页码]
	 * @param  integer $limit     [每页记录数]
	 * @param  string  $keyword   [搜索关键字]
	 * @param  string  $mark	  [mark 1:附近回收站点；2：附近商圈]
	 * @return [type]             [description]
	 */
	public function getRecoveryList($latitude='',$longitude='',$page=1,$limit=10,$keyword='',$mark=1){
		
		switch ((int)$mark) {
			case 1:
				if (!empty($keyword)) {
					$map['r.des'] = array("like","%$keyword%");
					$map['r.address'] = array("like","%$keyword%");
					$map['r.name'] = array("like","%$keyword%");
					$map['r.detail'] = array("like","%$keyword%");
					$map['u.realname'] = array("like","%$keyword%");
					$map['_logic'] = 'OR';
           			$where[] = $map;
				}
				$where['r.status'] = array("eq",1);
				$where[] = "u.uid = r. uid";
				$list = D("recovery r,jd_user u")
				->field("r.id,r.des,r.address,r.name,r.detail,r.view,u.realname,( 6378.138 * 2 * ASIN( SQRT( POW( SIN(( ".$latitude." * PI() / 180 - r.latitude * PI() / 180 ) / 2 ), 2 ) + COS(".$latitude." * PI() / 180) * COS(r.latitude * PI() / 180) * POW( SIN(( ".$longitude." * PI() / 180 - r.longitude * PI() / 180 ) / 2 ), 2 )))) distance")
				->where($where)
				->page($page)
				->limit($limit)
				->order("distance asc")
				->select();
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
				# code...
				break;
		}

		if(!empty($list)){
			die('{"status":1,"data":'.json_encode($list).'}');
		}else{
			die('{"status":0,"msg":"没有了"}');
		}
	}
}

