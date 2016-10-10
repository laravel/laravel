<?php
/**
 * 消息分发接口
 */
class DistributeMessage extends ApiCommon{
	
	/**
	 * @消息分发model
	 * @param action=>操作类型(0单个人发送,1全部 2男 3女 4青少年用户 5认证用户 6联合会会员 7诵读比赛,诗文比赛,8培训班-线下 ),
	 * @param type=>消息类型1评论 2转发 3赞 4收藏5收到私信6,被关注7系统消息
	 * @param competitionid=>比赛id
	 * @param uid=>用户id
	 * @param fromid 发送者id
	 * @param toid 接收人的id -- 如果接收人的id不为空，则发给单个人
	 * @param opusid 作品id
	 * @param name 暂时定位作品名称
	 * @param addtime 添加时间
	 * @param content 消息内容或者评论内容
	 * @param commentid 评论id
	 */
	public function distriMessage($data){
		set_time_limit(0);
		ini_set('memory_limit',-1);
		//根据不同参数将消息分发到不同的方法中
		if(empty($data)){
			return;
		}
		switch($data['action']){
			case 0:
				$sendToSingle = new SendToSingle();
				$sendToSingle->sendMessage($data);
				break;
			case 1:
				$sendToAllMessage = new SendToAllMessage();
				$sendToAllMessage->sendMessage($data);
				break;
			case 2:
				$sendToMan = new SendToMan();
				$sendToMan->sendMessage( $data);
				break;
			case 3:
				$sendToWoman = new SendToWoman();
				$sendToWoman->sendMessage($data);
				break;
			case 4:
				$sendToTeenager = new SendToTeenager();
				$sendToTeenager->sendMessage($data);
				break;
			case 5:
				$sendToAuthtype = new SendToAuthtype();
				$sendToAuthtype->sendMessage($data);
				break;
			case 6:
				$sendToLeague = new SendToLeague();
				$sendToLeague->sendMessage($data);
				break;
			case 7:
				$sendToCompetition = new SendToCompetition();
				$sendToCompetition->sendMessage($data);
				break;
		}
	}
}