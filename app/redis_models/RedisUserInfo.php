<?php 
/**
 * 用户相关模型--redis
 * @author :wangongli
 * @since :2016/07/20
 */

class RedisUserInfo extends RedisCommon{
	/**
	 * 删除上一个cookie
	 * @author :wang.hongli
	 * @since :2016/07/20
	 */
	public function delUserCookieFromRedis($id=0,$old_token=''){
		if(empty($id) || empty($old_token)){
			return false;
		}
		$key = 'cookie_check:'.$id.':'.$old_token;
		$client = $this->getDefaultClient();
		if($client){
			$client->del($key);
		}
		return true;
	}
	/**
	 * 将用户信息存放到redis中
	 * @author:wang.hongli
	 * @since:2016/07/20
	 */
	public function addCookieToRedis($id=0,$token='',$user_info=[],$expire_time=1800){
		if(empty($id) || empty($token) || empty($user_info)){
			return false;
		}
		$key = 'cookie_check:'.$id.':'.$token;
		$val = json_encode($user_info);
		$client = $this->getDefaultClient();
		// $expire_time = 1800;
		if(!$client){
			return false;
		}
		$client->setex($key,$expire_time,$val);
		return  true;
	}

	/**
	 * 从redis中取出用户信息
	 * @author :wang.hongli
	 * @since :2016/07/20
	 */
	public function getCookieFromRedis($key=''){
		if(empty($key)){
			return false;
		}
		$client=$this->getDefaultClient();
		if(!$client){
			return false;
		}
		$user_info = $client->get($key);
		if(empty($user_info)){
			return false;
		}	
		return json_decode($user_info,true);
	}
}

 ?>