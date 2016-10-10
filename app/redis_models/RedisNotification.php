<?php
/**
 * redis发送消息操作model
 * @author:wang.hongli
 * @since:2016/06/05
 */
class RedisNotification extends RedisCommon {
	
	//redis键配置数组
	private $config;
	function __construct(){
		$this->config = array(
				'notificationfansnum'=>'notificationfansnum:user:',//粉丝消息数
				'notificationnum'=>'notificationnum:user:',//普通消息数
				'notification'=>'notification:user:',//用户消息列表
				'active_user'=>'active_user',//活跃用户
		);
	}
	/**
	 * 给单个用户发送消息
	 * @param number $fromid 发送人id
	 * @param number $toid      接收人id
	 * @param number $notificationid 消息id
	 * @param number $type=>消息类型1评论 2转发 3赞 4收藏5收到私信6,被关注7系统消息
	 */
	public function addSingleNotification($fromid=0,$toid=0,$notificationid=0,$type=0){
		if(empty($fromid) || empty($toid)){
			return false;
		}
		if($type != 6 && empty($notificationid)){
			return false;
		}
		try {
			$client = $this->getDefaultClient();
			$score = time();
			if($type == 6 && !empty($fromid) && !empty($toid)){
				$key = $this->config['notificationfansnum'].$toid;
				$client->sadd($key,intval($fromid));
			}else{
				//记录消息和消息数量
				$key = $this->config['notification'].$toid;
				// $client->zadd($key,$score,$notificationid);
				$client->lpush($key,$notificationid);
				$num = $client->llen($key);
				if($num>200){
					$client->ltrim($key,0,199);
				}
				$key =$this->config['notificationnum'].$toid;
				$client->sadd($key,$notificationid);
			}
			//将toid用户设置为活跃用户
			$client->zadd($this->config['active_user'],$score,$toid);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * 获取用户未读消息id
	 * @author :wang.hongli
	 * @since :2016/08/07
	 */
	public function getNotificationId($uid){
		if(empty($uid)){
			return [];
		}
		try {
			$client = $this->getDefaultClient();
			$key = $this->config['notificationnum'].$uid;
			$uids = $client->smembers($key);
			return $uids;
		} catch (Exception $e) {
			return [];	
		}
	}
	/**
	 * 用户消息集合添加消息id,可批量添加
	 * @author:wang.hongli
	 * @since:2016/06/05
	 * @param:用户id,$notificationid 消息id
	 * @key规则：notification:user:uid->notification:user:1
	 */
	public function addNotification($uids=array(),$notificationid=0,$data=array()){
		if(empty($uids) || empty($notificationid)){
			return false;
		}
		try {
			$client = $this->getDefaultClient();
			//批量插入
			$client->pipeline(function($pipe) use($uids,$notificationid,$data)
			{
				$score = time();
				foreach($uids as $v){
					//记录消息和消息数量
					$key = $this->config['notification'].$v;
					// $pipe->zadd($key,$score,$notificationid);
					$pipe->lpush($key,$notificationid);
					$key = $this->config['notificationnum'].$v;
					$pipe->sadd($key,$notificationid);
				}
			});
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 获取用户自己的消息列表
	 * @author:wang.hongli
	 * @since:2016/06/05
	 */
	public function getNoticeList($info=array(),$start=0,$end=20){
		$return = array();
		if(empty($info)){
			return $return;
		}
		try {
			$uid = $info['id'];
			$client = $this->getDefaultClient();
			$key = $this->config['notification'].$uid;
			$rs = $client->lrange($key,$start,$end);
			$size = abs($end-$start)+1;
			$rs = !empty($rs) ? $rs :array();
			$sql_rs = array();
			$num = count($rs);
			//如果redis中的数据为空，则从mysql中获取,但是不补充系统消息
			if(empty($rs) || $num<$size){
				$start += $num;
				$size -= $num;
				//获取用户系统消息类型
				// $apiNotification = new ApiNotification();
				// $notice_type_ids = $apiNotification->getUserNoticeType($info);
				// $notice_type_str = implode(',', $notice_type_ids);
				$sql = "select id from notice where toid=?  and type != 7 order by id desc limit ?,?";
				// $tmp_rs = DB::select($sql,array($uid,$notice_type_str,$start,$size));
				$tmp_rs = DB::select($sql,array($uid,$start,$size));
				if(empty($tmp_rs)){
					return $rs;
				}
				foreach($tmp_rs as $k=>$v){
					$sql_rs[]=$v['id'];
				}
				//将用户放入活跃用户有序集合
				$client->zadd($this->config['active_user'],time(),$uid);
				//将消息id放入'notification:user:uid' list
				$key = $this->config['notification'].$uid;
				$client->rpush($key,$sql_rs);
				//redis结果集和mysql结果集合并
				$rs = array_merge($rs,$sql_rs);
			}
		} catch (Exception $e) {
			$rs = $return;
		}
		return $rs;
	}
	/**
	 * 删除消息
	 * @author:wang.hongli
	 * @since:2016/06/11
	 * @param:uid用户id,id消息id
	 */
	public function delNotification($uid,$id){
		if(empty($uid) || empty($id)){
			return false;
		}
		try {
			$client = $this->getDefaultClient();
			$key = $this->config['notification'].$uid;
			// $flag = $client->zrem($key,$id);
			$flag = $client->lrem($key,0,$id);
			return !empty($flag) ? intval($flag) : false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 获取消息数量 普通消息notificationNum　粉丝消息　fansNotiNum
	 * @author:wang.hongli
	 * @since:2016/06/11
	 */
	public function getNotificationNum($uid){
		if(empty($uid)) return false;
		try {
			$client = $this->getDefaultClient();
			$key = $this->config['notificationnum'].$uid;
			$notificationNum = $client->scard($key);
			$data['notificationNum'] = !empty($notificationNum) ? intval($notificationNum) : 0;
			$key = $this->config['notificationfansnum'].$uid;
			$fansNotiNum = $client->scard($key);
			$data['fansNotiNum'] = !empty($fansNotiNum) ? intval($fansNotiNum) : 0;
			return $data;
		} catch (Exception $e) {
			return array('notificationNum'=>0,'fansNotiNum'=>0);
		}
	}
	
	/**
	 * 将某个用户notificationfansnum:user:uid中的消息清空
	 * @author:wang.hongli
	 * @since:2016/06/11
	 * @param:uid 用户id
	 */
	public function clearReadedFansStatus($uid=0){
		if(empty($uid)){
			return 0;
		}
		try {
			$client = $this->getDefaultClient();
			$key =$this->config['notificationfansnum'].$uid;
			$num = $client->del($key);
			return !empty($num) ? intval($num) : 0;
		} catch (Exception $e) {
			return 0;
		}
	}
	
	/**
	 * 清除用户消息--普通消息--调取完消息列表之后，清除
	 * @author:wang.hongli
	 * ＠since:2016/06/11
	 */
	public function clearNoticeNum($uid=0){
		if(empty($uid)){
			return 0;
		}
		try {
			$client = $this->getDefaultClient();
			$key = $this->config['notificationnum'].$uid;
			$num = $client->del($key);
			return !empty($num) ? intval($num) : 0;
		} catch (Exception $e) {
			return 0;
		}
	}
	
	/**
	 * 获取关注自己的用户列表
	 * @author:wang.hongli
	 * @since:2016/06/11
	 */
	public function getAttenNotify($uid){
		if(empty($uid)){
			return array();
		}
		try {
			$key = $this->config['notificationfansnum'].$uid;
			$client = $this->getDefaultClient();
			$uids = $client->smembers($key);
			return !empty($uids) ? $uids : array();
		} catch (Exception $e) {
			return array();
		}
	}
}