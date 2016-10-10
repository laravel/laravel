<?php
/**
** 城市地区
*/
class ApiCity{
	
	public static $cache_path="../upload/cache_";
	
	//获取省份
	public static function getProvince(){
		$data=array();
		$cacheid="province";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name from sx_province order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[]=$v;
			}
			self::_setCache($cacheid,$data);
		}
		return $data;
	}
	//获取城市
	public static function getCity($province_id=0){
		$cacheid="city";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name,province_id from sx_city order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[$v['province_id']][]=$v;
			}
			self::_setCache($cacheid,$data);
		}
		if($province_id==0){
			return $data;
		}else{
			return isset($data[$province_id])?$data[$province_id]:array();
		}
	}
	//获取县区
	public static function getArea($city_id=0){
		$cacheid="area";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name,city_id from sx_area order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[$v['city_id']][]=$v;
			}
			self::_setCache($cacheid,$data);
		}
		if($city_id==0){
			return $data;
		}else{
			return isset($data[$city_id])?$data[$city_id]:array();
		}
	}
	
	/*
	* 缓存算法-获取
	*/
	public static function _getCache($cacheid){
		$data=array();
		if(!empty($cacheid)){
			$file_path=self::$cache_path.$cacheid;
			if(file_exists($file_path)){
				$file=file_get_contents($file_path);
				$data=unserialize($file);
			}
		}
		return $data;
	}
	/*
	* 缓存算法-设置
	*/
	public static function _setCache($cacheid,$data){
		if(!empty($cacheid)){
			$_data=serialize($data);
			file_put_contents(self::$cache_path.$cacheid,$_data);
			return true;
		}else{
			return false;
		}
	}
	
	//=======后台使用方法id=>name=========
	//所有省份
	public static function getAllProvince(){
		$data=array();
		$cacheid="all_province";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name from sx_province order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[$v["id"]]=$v["name"];
			}
			self::_setCache($cacheid,$data);
		}
		return $data;
	}
	//所有城市
	public static function getAllCity(){
		$cacheid="all_city";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name,province_id from sx_city order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[$v['province_id']][$v['id']]=$v['name'];
			}
			self::_setCache($cacheid,$data);
		}
		return $data;
	}
	//所有地区
	public static function getAllArea(){
		$cacheid="all_area";
		$data=self::_getCache($cacheid);
		if(empty($data)){
			$data=array();
			$sql="select id,name,city_id from sx_area order by id asc";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$data[$v['city_id']][$v['id']]=$v['name'];
			}
			self::_setCache($cacheid,$data);
		}
		return $data;
	}

}
