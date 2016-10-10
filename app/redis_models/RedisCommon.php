<?php
/**
 * redis 公共类
 * @since:2016/04/08
 * @author:wang.hongli
 */
class RedisCommon {
	
	public function getDefaultConnect(){
		try {
			$default = MyRedis::connection('default');
		} catch (Exception $e) {
			$json = json_encode(array('status'=>0,'message'=>'redis链接错误，请及时联系管理员'));
			die($json);
		}
		return $default;
	}
	/**
	 * 建立redis客户端链接
	 * @author:wang.hongli
	 * @since:2016/06/05
	 */
	public function getDefaultClient(){
		try{
			$default_server = Config::get('database.redis.default');
			$client = new Predis\Client($default_server);
		}catch(Exception $e){
			return false;
		}
		return $client;
	}
}