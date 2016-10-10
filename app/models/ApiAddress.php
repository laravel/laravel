<?php 
/**
 * 获取用户地址
 * @author :wang.hongli
 * @since :2016/08/17
 */
class ApiAddress extends ApiCommon{

	/**
	 * 根据id获取省市区
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public static function getAddress($province=0,$city=0,$area=0){
		$return = [];
		if(!empty($province)){
			$province_arr = DB::table('sx_province')->where('id',$province)->first(['id','name','ename']);
			if(!empty($province_arr)){
				$return['province'] = ['province_id'=>$province_arr['id'],'name'=>$province_arr['name']];
			}
		}
		if(!empty($city)){
			$city_arr = DB::table('sx_city')->where('id',$city)->first(['id','name']);
			if(!empty($city_arr)){
				$return['city'] = ['city_id'=>$city_arr['id'],'name'=>$city_arr['name']];
			}
		}
		if(!empty($area)){
			$area_arr = DB::table('sx_area')->where('id',$area)->first(['id','name']);
			if(!empty($area_arr)){
				$return['area'] = ['area_id'=>$area_arr['id'],'name'=>$area_arr['name']];
			}
		}
		return $return;

	}
}

 ?>