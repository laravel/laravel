<?php
/**
 * 作品赞的相关操作
 * @author:wang.hongli
 * @since:2016/04/14
 */
class RedisOpusPraise extends RedisCommon{
	
	//默认redis链接
	private $default;
	function __construct(){
		$this->default = $this->getDefaultConnect();
	}
	/**
	 * @tutorial:赞 或者取消赞 放入redis集合,用来判断赞的状态 praise:user:2:opus
	 * @author:wang.hongli
	 * @since:2016/04/14
	 * @param:flag 1 赞 2取消赞
	 * @param:uid 赞 或者 取消赞的用户id
	 * @param:opusId 作品id
	 * 
	 */
	public function praiseEdit($uid,$opusId,$flag){
		if(empty($uid) || empty($opusId)){
			return false;
		}
		$uid = intval($uid);
		$opusId = intval($opusId);
		$key = 'praise:user:'.$uid.':opus';
		$return = 0;
		try {
			switch ($flag){
				case 1:
					$return = $this->default->sadd($key,$opusId);
					break;
				case 2:
					$return = $this->default->srem($key,$opusId);
					break;
			}
		} catch (Exception $e) {
			return $return;
		}
		
		return $return;
	}
	/**
	 * @tutorial:根据用户id获取用户赞过得所有作品
	 * @author:wang.hongli
	 * @since:2016/04/14
	 * @param 用户id $uid
	 */
	public function getUserPraise($uid){
		$return = array();
		if(!empty($uid)){
			$key = 'praise:user:'.$uid.':opus';
			try {
				$rs = $this->default->smembers($key);
				if($rs){
					$return = $rs;
				}
			} catch (Exception $e) {
				return $return;
			}
		}
		return $return;
	}
	/**
	 * 根据用户id，作品id判断是否赞过某个作品
	 * @param 用户 $uid
	 * @param:作品id $opusId
	 */
	public function isPraise($uid,$opusId){
		$praStatus = 0;
		if(!empty($uid) && !empty($opusId)){
			try {
				$key = 'praise:user:'.$uid.':opus';
				$praStatus = $this->default->sismember($key,$opusId);
			} catch (Exception $e) {
			}
		}
		return !empty($praStatus) ? intval($praStatus) : 0;
	}
}