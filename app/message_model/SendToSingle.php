<?php
/**
 * 给指定用户发送消息
* @author:wang.hongli
* @since:2016/06/06
*/
class SendToSingle extends MessageCommon{

	/**
	 * 给指定用户发送消息
	 * @author:wang.hongli
	 * @since:2016/06/06
	 */
	public function sendMessage($data){
		if (empty ( $data )||empty($data['toid'])) {
			return false;
		}
		$notificationid = 0;
		if($data['type'] != 6){
			//mysql中插入消息
			$notificationid = $this->addNotice($data);
			if(!$notificationid){
				return false;
			}
		}
		//初始化redisNotification对象
		$redisNotification = new RedisNotification();
		$redisNotification->addSingleNotification($data['fromid'],$data['toid'],$notificationid,$data['type']);
		return true;
	}
}
