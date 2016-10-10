<?php 
/**
*	消息列表模型
**/
class ApiNotification extends ApiCommon {
	
	/**
	 * 获取系统消息--redis中获取个人列表
	 * @author:wang.hongli
	 * @since:2016/06/05
	 */
	public function getNotification(){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$count = !empty(Input::get('count')) ? intval(Input::get('count')) : 20;
		$pageIndex = !empty(Input::get('pageIndex')) ? intval(Input::get('pageIndex')) : 1;
		$start = ($pageIndex-1)*$count;
		$end = $start+$count;
		$uid = $info['id'];
		//从redis中获取消息列表
		$redisNotification = new RedisNotification();
		$notice_ids = $redisNotification->getNoticeList($info,$start,$end);
		$return = array();
		$hasmore = 0;
		if(empty($notice_ids)){
			return $return['hasmore'] = $hasmore;
		}
		if(count($notice_ids) >= $count){
			$hasmore = 1;
			array_pop($notice_ids);
		}
		$rs = DB::table('notice')->whereIn('id',$notice_ids)->orderBy('id','desc')->get();
		if(empty($rs)){
			return $return['hasmore'] = $hasmore;
		}
		//获取用户未读消息id
		$newNotificationIds = $redisNotification->getNotificationId($uid);
		// type 消息类型1评论 2转发 3赞 4收藏5收到私信--废弃 6,关注--废弃７系统消息，改到其它接口
		//获取用户id
		$uids_array  = array();
		//评论id
		$comments_id = array();
		//获取
		foreach($rs as $k=>&$v){
			if($v['type'] != 7){
				$uids_array[] = $v['fromid'];
			}
			if($v['type'] == 1){
				$comments_id[] = $v['commentid'];
			}
			if(!empty($v['content'])){
				$v['comment'] = $v['content'] = unserialize($v['content']);
			}
			$v['is_new'] = 0;
			if(in_array($v['id'], $newNotificationIds)){
				$v['is_new'] = 1;
			}
		}
		unset($v);
		$user_info = array();
		if(!empty($uids_array)){
			$tmp_user_info = DB::table('user')->whereIn('id',$uids_array)->select('id','nick','sportrait','gender','grade','portrait','authtype','teenager','isleague')->get();
			if(!empty($tmp_user_info)){
				foreach($tmp_user_info as $k=>$v){
					$v['sportrait'] = $this->poem_url.'/'.ltrim($v['sportrait']);
					$v['portrait'] = $this->poem_url.'/'.ltrim($v['portrait']);
					$user_info[$v['id']] = $v;
				}
			}
		}
		$comment_info = array();
		if(!empty($comments_id)){
			$tmp_comment_info = DB::table('opuscomment')->whereIn('id',$comments_id)->get(array('id','content'));
			if(!empty($tmp_comment_info)){
				foreach($tmp_comment_info as $key=>$value){
					$comment_info[$value['id']] = unserialize($value['content']);
				}
			}
		}
		//获取系统消息用户
		$roles = self::getRoles();
		//根据消息类型，组合消息
		foreach($rs as $key=>&$v){
			$v['ownid'] = $v['toid'];
			$v['uid'] = !empty($user_info[$v['fromid']]['id']) ? $user_info[$v['fromid']]['id'] : 0;
			$v['nick'] = !empty($user_info[$v['fromid']]['nick']) ? $user_info[$v['fromid']]['nick'] : '';
			$v['sportrait'] = !empty($user_info[$v['fromid']]['sportrait']) ? $user_info[$v['fromid']]['sportrait'] : '';
			$v['gender'] = !empty($user_info[$v['fromid']]['gender']) ? $user_info[$v['fromid']]['gender'] : 0;
			$v['grade'] = !empty($user_info[$v['fromid']]['grade']) ? $user_info[$v['fromid']]['grade'] : 1;
			$v['authtype'] = !empty($user_info[$v['fromid']]['authtype']) ? $user_info[$v['fromid']]['authtype'] : 0;
			$v['teenager'] = !empty($user_info[$v['fromid']]['teenager']) ? $user_info[$v['fromid']]['teenager'] : 0;
			$v['isleague'] = !empty($user_info[$v['fromid']]['isleague']) ? $user_info[$v['fromid']]['isleague'] : 0;

			switch($v['type']){	
				case 1:
					$v['comment'] = !empty($comment_info[$v['commentid']]) ? $comment_info[$v['commentid']] : '';
					break;
				case 7:
					$v['nick'] = !empty($roles[$v['fromid']]['role_name']) ? $roles[$v['fromid']]['role_name'] : '诵读小助手';
					$v['sportrait'] = !empty($roles[$v['fromid']]['sportrait']) ? $roles[$v['fromid']]['sportrait'] : $this->poem_url.'/upload/icon/songduxiaozhushou.jpg';
					$v['gender'] = 0;
					$v['grade'] = 1;
					$v['authtype'] =  0;
					$v['teenager'] =  0;
					$v['isleague'] =  0;
					break;
			}
		}
		//清除消息数量
		$redisNotification->clearNoticeNum($uid);
		$rs['hasmore'] = $hasmore;
		return $rs;
	}
	/**
	 * 删除消息
	 * @author:wang.hongli
	 * @since:2016/06/11
	 * @param:id 消息id
	 */
	public function delNotification(){
		$info = $this->viaCookieLogin();
		if(empty($info['id'])){
			return 'nolog';
		}
		$uid = $info['id'];
		$id = Input::has('id') ? intval(Input::get('id')) : 0;
		if(empty($id)){
			return '删除失败,请重试';
		}
		try {
			//redis中删除对应记录
			$redisNotification = new RedisNotification();
			$flag = $redisNotification->delNotification($info['id'], $id);
			//mysql同样删除对应记录
			if($flag){
				DB::table('notice')->where('toid',$uid)->where('id',$id)->where('type','<>',7)->delete();
			}
			return true;
		} catch (Exception $e) {
			return '删除失败,请重试';
		}
	}

	/**
	*	标记某条消息是否读过
	*	@author:wang.hongli
	*	@since:2015/01/11
	*	@modify:wang.hongli
	*/
	public function isReadedStatus() {
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return;
		}
		$uid = intval($info['id']);
		$redisNotification = new RedisNotification();
		$redisNotification->clearReadedFansStatus($uid);
		return;
	}
	/**
	 * 获取消息数量VERSION3.0 -- redis中获取消息数量
	 * @author:wang.hongli
	 * @since:2016/06/11
	 * @return notificationNum 普通消息 fansNotiNum 粉丝消息数量
	 */
	public function getNotificationNum(){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = intval($info['id']);
		//redis 中获取记录
		$redisNotification  = new RedisNotification();
		$data = $redisNotification->getNotificationNum($uid);
		return $data;
	}
	/**
	 * 获取推送消息的角色
	 * @author:wang.hongli
	 * @since：2016/04/22
	 */
	public function getRoles(){
		$data = array();
		$rs = DB::table('msg_role')->get();
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$v['sportrait'] = $this->poem_url.'/'.$v['sportrait'];
				$data[$v['id']] = $v;
			}
		}
		return $data;
	}

	/**
	* 获取用户系统消息类型
	*@author:wang.hongli
	*@since:2016/06/16
	*@param:info userinfo
	**/
	public function getUserNoticeType($info=array()){
		$return = array(1);
		if(empty($info)){
			return $return;
		}
		//gender
		if(empty($info['gender'])){
			array_push($return,3);
		}else{
			array_push($return,2);
		}
		//teenager
		if(!empty($info['teenager'])){
			array_push($return,4);
		}
		//authtype
		if(!empty($info['authtype'])){
			array_push($return,5);
		}
		//联合会会员
		$is_league = DB::table('league_user')->where('uid',$info['id'])->pluck('id');
		if(!empty($is_league)){
			array_push($return,6);
		}
		return $return;
	}

}