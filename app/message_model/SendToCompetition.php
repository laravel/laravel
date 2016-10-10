<?php

/**
 * 给参加诵读比赛的人发送消息
 * @author:wang.hongli
 * @sine:2016/06/0７
 */
class SendToCompetition extends MessageCommon {
	
	/**
	 * 给参加诵读比赛的人发送消息
	 * 
	 * @author :wang.hongli
	 * @since :2016/06/05
	 * @param:competitionid 注意要有比赛id
	 */
	public function sendMessage( $data) {
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
		//选出参加比赛的用户
		$competitionid = $data['competitionid'];
		if(empty($competitionid)) return false;
		$tmp_user = DB::table('user_permission')->distinct('uid')->where('type',$competitionid)->lists('uid');
		if(empty($tmp_user)) return false;
		$redisNotification->addNotification($tmp_user,$notificationid);
	}
}