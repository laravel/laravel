<?php
/**
 * 诗文比赛作品redis model
 * @author:wang.hongli
 * @since:2016/04/27
 */
class RedisOpusPoetry extends RedisCommon{
	
	//默认redis链接
	private $default;
	function __construct(){
		$this->default = $this->getDefaultConnect();
	}
	
	/**
	 * 查看作品
	 * @author:wang.hongli
	 * @since:2016/04/27
	 * @param:uid 用户id $id 查看诗文作品id
	 * @return false 已经超过限制，不在增加查看数
	 */
	public function viewOpusPoetry($uid,$id){
		if(empty($uid) || empty($id)){
			return false;
		}
		try {
			$key = 'views:user:'.$uid.':poetryid:'.$id;
			if($this->default->exists($key)){
				$view_num_limit= Config::get('app.opusPoetryNum');
				$view_num = $this->default->get($key);
				if($view_num < $view_num_limit){
					$this->default->incr($key);
					return true;
				}else{
					return false;
				}
			}else{
				$tomorrow = strtotime ( date ( 'Y-m-d', strtotime ( '+1 day' ) ) );
				$expire_time = $tomorrow - time ();
				$this->default->setex ( $key, $expire_time, 1 );
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * @tutorial:诗文比赛赞 或者取消赞 放入redis无序集合,用来判断赞的状态 praise:user:2:opuspoetry
	 * @author:wang.hongli
	 * @since:2016/04/26
	 * @param:flag 1 赞 2取消赞
	 * @param:uid 用户id
	 * @param:id 诗文作品id
	 */
	public function praiseOpusPoetry($uid,$id,$flag=1){
		$return = 0;
		if(empty($uid) || empty($id)) 
			return $return;
		$uid = intval($uid);
		$id = intval($id);
		$key = 'praise:user:'.$uid.':opuspoetry';
		$return = 0;
		try {
			switch($flag){
				case 1:
					$return = $this->default->sadd($key,$id);
					break;
				case 2:
					$return = $this->default->srem($key,$id);
					break;
			}
		} catch (Exception $e) {
		}
		return $return;
	}
	/**
	 * 诗文比赛获取用户赞过的作品id
	 * @author:wang.hongli
	 * @since:2016/04/28
	 * @param:uid 用户id
	 * @param:opus_poetry_id  作品id 
	 */
	public function getUserPraisePoetry($uid){
		$return = array();
		try {
			if(!empty($uid)){
				$key = 'praise:user:'.$uid.':opuspoetry';
				$rs = $this->default->smembers($key);
				if($rs){
					$return = $rs;
				}
			}
		} catch (Exception $e) {
		}
		return $return;
	}
	
}