<?php
/**
 * redis记录用户操作model
 * @author:wang.hongli
 * @since:2016/06/05
 */
class RedisActiveUser extends RedisCommon {
	/**
	 * 标记用户为活跃用户
	 * @author:wang.hongli
	 * @since:2016/06/08
	 */
	public function addActiveUser($uid){
		if(empty($uid)){
			return false;
		}
		$uid = intval($uid);
		try {
			$client = $this->getDefaultClient();
			$score = time();
			$key = 'active_user';
			$client->zadd($key,$score,$uid);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 获取活跃用户
	 * ＠author:wang.hongli
	 * @since:2016/06/08
	 */
	public function getActiveUserList($start=0,$end=0){
		$client = $this->getDefaultClient();
		$key = 'active_user';
		$rs = $client->zrange($key,$start,$end);
		if(empty($rs)){
			return array();
		}
		return $rs;
	}
	
	/**
	 * 获取活跃用户数量
	 * @author:wang.hongli
	 * @since:2016/06/08
	 */
	public function getActiveUserCount(){
		$client = $this->getDefaultClient();
		$key = 'active_user';
		$count = $client->zcard($key);
		return !empty($count) ? intval($count) : 0;
	}

	/**
	 * 删除月非活跃用户--计划任务每月执行一次
	 * @author :wang.hongli
	 * @since :2016/09/05
	 */
	public function delAllNoActiveUser(){
		$client = $this->getDefaultClient();
		$key = 'active_user';
		//start_time
		$start_time = strtotime("-3 month");
		//end_time
		$end_time = strtotime("-1 month");
		$client->zremrangebyscore($key,$start_time,$end_time);
	}
}