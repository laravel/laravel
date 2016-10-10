<?php
/**
 * redis记录用户操作model
 * @author:wang.hongli
 * @since:2016/06/05
 */
class RedisUserFlowers extends RedisCommon {
	/**
	 * 个人守护榜加花
	 * @author:hgz
	 * @since:2016/07/04
	 */
	public function addUserFolowers($to_id,$from_id,$num){
		if(empty($to_id)){
			return false;
		}
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:uid:'.$to_id;
			$oldnum=$client->zScore($key,$from_id);
			$client->zadd($key,$num+$oldnum,$from_id);
			return true; 		
		} catch (Exception $e) {		 
			return true; 
		}
	}	
	/**
	 * 个人守护榜列表
	 * @author:hgz
	 * @since:2016/07/04
	 */
	public function listUserFolowers($to_id,$start,$end){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:uid:'.$to_id;
			$list=$client->zRevRange($key,$stat,$end,'WITHSCORES');
			return $list;
		}catch (Exception $e) {
			return false;
		}
	}
	/**
	 * 主播总榜加花
	 * @author:hgz
	 * @since:2016/07/05
	 */
	public function addAllFolowers($from_id,$num){
			 
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:all';
			$oldnum=$client->zScore($key,$from_id);
			$client->zadd($key,$num+$oldnum,$from_id);
			return true; 		
		} catch (Exception $e) {		 
			return true; 
		}
	}
	/**
	 * 主播总榜列表
	 * @author:hgz
	 * @since:2016/07/05
	 */
		public function listAllFolowers($start,$end){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:all';
			$list=$client->zRevRange($key,$start,$end,'WITHSCORES');
			return $list;
		}catch (Exception $e) {
			return false;
		}
		
		}
	/**
	 * 主播年榜单加花
	 * @author:hgz
	 * @since:2016/07/05
	 */
	 public function addYearFolowers($year,$from_id,$num){
			 
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:year：'.$year;
			$oldnum=$client->zScore($key,$from_id);
			$client->zadd($key,$num+$oldnum,$from_id);
			return true; 		
		} catch (Exception $e) {		 
			return true; 
		}
	}
		/**
	 *  主播年榜单列表
	 * @author:hgz
	 * @since:2016/07/05
	 */
		public function  listYearFolowers($year){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:year：'.$year;
			$list=$client->zRevRange($key,0,-1,'WITHSCORES');
			return $list;
		}catch (Exception $e) {
			return false;
		}
		
		}
	/**
	 * 主播月榜单加花
	 * @author:hgz
	 * @since:2016/07/05
	 */
	 public function addMonthFolowers($month,$from_id,$num){
			 
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:month：'.$year;
			$oldnum=$client->zScore($key,$from_id);
			$client->zadd($key,$num+$oldnum,$from_id);
			return true; 		
		} catch (Exception $e) {		 
			return true; 
		}
	 }
	 	/**
	 *  主播月榜单列表
	 * @author:hgz
	 * @since:2016/07/05
	 */
		public function  listMonthFolowers($month){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:month'.$month;
			$list=$client->zRevRange($key,0,-1,'WITHSCORES');
			return $list;
		}catch (Exception $e) {
			return false;
		}
		
		}
		/**
	 * 主播周榜单加花
	 * @author:hgz
	 * @since:2016/07/05
	 */
	 public function addWeekFolowers($week,$from_id,$num){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:week：'.$week;
			$oldnum=$client->zScore($key,$from_id);
			$client->zadd($key,$num+$oldnum,$from_id);
			return true; 		
		} catch (Exception $e) {		 
			return true; 
		}
	}
	  	/**
	 *  主播周榜单列表
	 * @author:hgz
	 * @since:2016/07/05
	 */
		public function  listWeekFolowers($week){
		try {
			$client = $this->getDefaultClient();
			$key = 'flowerslist:week'.$week;
			$list=$client->zRevRange($key,0,-1,'WITHSCORES');
			return $list;
		}catch (Exception $e) {
			return false;
		}
		
		}
}