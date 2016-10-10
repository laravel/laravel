<?php
/**
 * 给所有认证用户发送消息
 * @author:wang.hongli
 * @since:2016/06/06
 */
class SendToAuthtype extends MessageCommon{
	
	/**
	 * 给所有认证用户发送消息
	 * @author:wang.hongli
	 * @since:2016/06/06
	 */
	public function sendMessage($data){
		if (empty ( $data )) {
			return false;
		}
		//mysql中插入消息
		$notificationid = $this->addNotice($data);
		if(!$notificationid){
			return false;
		}
		//初始化redisNotification对象
		$redisNotification = new RedisNotification();
		$count = DB::table ( 'user' )->max ( 'id' );
		$size = 1000;
		
		// 选出用户分批发送
		for($i = 0; $i <= $count; $i = $i + $size) {
			$tmp_start = $i+1;
			$tmp_end = $i+$size;
			$tmp_user = DB::table('user')->whereBetween('id',array($tmp_start,$tmp_end))->where('isdel','=',0)->where('authtype',1)->lists('id');
			if(empty($tmp_user)) {
				continue;
			}
			$redisNotification->addNotification($tmp_user,$notificationid);
		}
	}
}