<?php


namespace Admin\Controller;
use Think\Controller;

/**
 * 接口入口类
 */
class ApiCommonController extends Controller{
	
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
					$fixedlist[$k]['distance'] = sprintf("%.2f",$v['distance']*100)."m";
				}else{
					$fixedlist[$k]['distance'] = sprintf("%.2f",$v['distance'])."km";
				}
			}
		}
		die('{"status":1,"data":'.json_encode($fixedlist).'}');
	}

}

