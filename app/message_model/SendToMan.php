<?php
/**
 * 给所有男用户发送消息
 * @author:wang.hongli
 * @since:2016/06/06
 */
class SendToMan extends MessageCommon{
	
	/**
	 * 给男用户发送消息
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
		//获取活跃用户数量,false 则不作为
		$count = $this->getActiveUserCount();
		if(empty($count)) return false;
		
		$redisNotification = new RedisNotification();
		$redisActiveUser = new RedisActiveUser();
		for($i=0;$i<=$count;$i+=100){
			$end = $i+99;
			$tmp_uids = $redisActiveUser->getActiveUserList($i,$end);
			//选出数据库中的用户
			if(empty($tmp_uids)) continue;
			$uids = DB::table('user')->whereIn('id',$tmp_uids)->where('gender',1)->where('isdel','=',0)->lists('id');
			if(empty($uids)) continue;
			//增加消息,设置消息过期时间
			$redisNotification->addNotification($uids,$notificationid);
		}
	}
}