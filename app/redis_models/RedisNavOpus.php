<?php 
/**
 * 作品导航获取，设置缓存
 * @author :wang.hongli
 * @since :2016/07/21
 */
class RedisNavOpus extends RedisCommon{

	/**
	 * 排行榜顶部根据分类获取作品列表放入redis缓存
	 * @author :wang.hongli
	 * @since :2016/07/21 
	 * @param string  $prefix      [缓存前缀]
	 * @param array   $data        [需要缓存的数组]
	 * @param integer $navId       [导航id]
	 * @param integer $offSet      [索引到第几条]
	 * @param integer $expire_time [过期时间，秒]
	 */
	public function addNavOpus($prefix='api_opus_navigation_',$data=[],$navId=0,$offSet=0,$expire_time=3){
		if(empty($navId) || empty($data)){
			return false;
		}
		$client = $this->getDefaultClient();
		if(!$client){
			return false;
		}
		$key = $prefix.$navId.'_'.$offSet;
		$json_data = json_encode($data);
		$client->setex($key,$expire_time,$json_data);
		return true;
	}

	/**
	 * [从redis缓存中获取:排行榜顶部根据分类获取作品列表]
	 * @param  string  $prefix [description]
	 * @param  integer $navId  [description]
	 * @param  integer $offSet [description]
	 * @return [type]          [description]
	 */
	public function getNavOpus($prefix='api_opus_navigation_',$navId=0,$offSet=0){
		if(empty($navId)){
			return false;
		}
		$client = $this->getDefaultClient();
		if(!$client){
			return false;
		}
		$key = $prefix.$navId.'_'.$offSet;
		$json_data = $client->get($key);
		if(empty($json_data)){
			return false;
		}
		$data = json_decode($json_data,true);
		return $data;
	}
}

 ?>