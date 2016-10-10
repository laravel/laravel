<?php
/**
 * 给所有联合会用户发送消息
 * @author:wang.hongli
 * @since:2016/06/06
 */
class SendToLeague extends MessageCommon{
	
	/**
	 * 给所有联合会用户发送消息
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
		//选出联合会会员
		$tmp_user = DB::table('user_permission')->distinct('uid')->where('type',1)->lists('uid');
		if(empty($tmp_user)){
			return;
		}
		$redisNotification->addNotification($tmp_user,$notificationid);
	}

	/**
	 * 给部分联合会会员推消息
	 * @author :wang.hongli
	 * @since :2016/07/13
	 * @param : uids 选出部分联合会会员
	 */
	public function sendMessagePart($uids=[],$data){
		//mysql中插入消息
		$notificationid = $this->addNotice($data);
		if(!$notificationid || empty($uids)){
			return false;
		}
		//
		if(empty($uids)){
			return false;
		}
		//初始化redisNotification对象
		$redisNotification = new RedisNotification();
		$redisNotification->addNotification($uids,$notificationid);
	}
}