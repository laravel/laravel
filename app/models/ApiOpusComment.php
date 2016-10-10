<?php
/**
* 作品评论
**/
class ApiOpusComment extends ApiCommon {

	//作品评论/转发到定制听
	public function commentOpus() {
		$errorMessage = "你的内容中涉及敏感信息，不能公开发布！";
		$info = $this->viaCookieLogin();
		if(!empty($info)) {
			$fromId = $info['id'];
			$nick = $info['nick'];
			$opusId = Input::get('opusId');
			if(empty($opusId)) return '作品不存在';
			$type = Input::get('type');//0评论调用接口 1转发调用接口
			$flag = Input::get('flag');//0只是评论or只是转发  1 评论的时候转发，转发的时候评论

			$rs = DB::table('opus')->where('id','=',$opusId)->first(array('uid'));
			//初始化commentid
			$commentId = 0;
			$customId = 0;
			if(empty($rs)) return '评论失败';
			$uid = $rs['uid']; //作品主人id
			$toid = Input::has('toid') ? intval(Input::get('toid')) : 0; 
			$tmpRs = $this->getOpusAndUserInfo($opusId);
			//评论调用接口
			if(empty($type)) {
				$content = Input::get('content');
				if(my_sens_word($content)){
					return $errorMessage;
				}
				if(empty($content)) return '评论不能为空';
				$content = serialize($content);
				$arr = array(
					'uid'	 => $uid,
					'opusid' => $opusId,
					'fromid' => $fromId,
					'toid'	 => $toid,
					'content'=> $content,
					'addtime'=> time(),
				);
				if(!empty($flag)) { //同时转发到微博
					$arr2 = array(
						'uid'=>$uid,
						'opusid'=>$opusId,
						'repuid'=>$fromId,
						'opustype'=>1,
						'isdel'=>0,
						'addtime'=>time(),
						'comment'=>$content
					);
					$customId = DB::table('personalcustom')->insertGetId($arr2);
					if(!empty($customId)) {
						$this->repostNum($opusId,$uid);//转发数+1
						//获取作品主人id，作品名称,推送消息
						if(!empty($tmpRs)) {
							$content = $nick.'转发了你的作品'.$tmpRs['name'];
							$pushId = $tmpRs['id']; //给toid发送消息
							// $this->addNotification($uid,$fromId,$opusId,'','',$toid,$content,2,$toid,$customId);
							$data = array(
								'action'=>0,
								'type'=>2,
								'uid'=>$uid,
								'fromid'=>$fromId,
								'toid'=>$pushId,
								'opusid'=>$opusId,
								'name'=>$tmpRs['name'],
								'addtime'=>time(),
								'commentid'=>0
							);
							$distributeMessage = new DistributeMessage();
							$distributeMessage->distriMessage($data);
							$this->pushMsg($pushId,$content,2);
						}
					}
				} 
				$commentId = DB::table('opuscomment')->insertGetId($arr);
				if(empty($commentId)) return '评论失败';
				//作品评论数+1
				$this->commentNum($opusId,1);
				if(!empty($tmpRs) && $fromId != $tmpRs['id']) {
					$content = $nick.'评论了你的作品'.$tmpRs['name'];
					$pushId = $tmpRs['id']; //给toid发送消息
					// $this->addNotification($uid,$fromId,$opusId,$commentId,'',$toid,$content,1,$toid,'');
					$data = array(
						'action'=>0,
						'type'=>1,
						'uid'=>$uid,
						'fromid'=>$fromId,
						'toid'=>$pushId,
						'opusid'=>$opusId,
						'name'=>$tmpRs['name'],
						'addtime'=>time(),
						'commentid'=>$commentId
					);
					$distributeMessage = new DistributeMessage();
					$distributeMessage->distriMessage($data);

					$this->pushMsg($pushId,$content,1);
				}
				//推送到受评论的人
				if(!empty($arr['toid']) && $arr['toid']!=$arr['uid']){
					$content = $nick.'回复了你的评论';
					$pushId = $arr['toid']; //给toid发送消息
					// $this->addNotification($pushId,$fromId,$opusId,$commentId,'',$toid,$content,1,$toid,'');
					$data = array(
						'action'=>0,
						'type'=>1,
						'uid'=>$uid,
						'fromid'=>$fromId,
						'toid'=>$pushId,
						'opusid'=>$opusId,
						'name'=>'',
						'addtime'=>time(),
						'commentid'=>$commentId,
					);
					$distributeMessage = new DistributeMessage();
					$distributeMessage->distriMessage($data);

					$this->pushMsg($pushId,$content,1);
				}
				return $commentId;
			} else {
				//转发调用接口
				$content = Input::get('content');
				if(my_sens_word($content))
				{
					return $errorMessage;
				}
				if(!empty($flag)) { //评论并转发
					if(empty($content)) return '内容不能为空';
					$content = serialize($content);
					$arr = array(
						'uid'	 => $uid,
						'opusid' => $opusId,
						'fromid' => $fromId,
						'toid'	 => $toid,
						'content'=> $content,
						'addtime'=> time(),
					);
					$arr2 = array(
						'uid'=>$uid,
						'opusid'=>$opusId,
						'repuid'=>$fromId,
						'opustype'=>1,
						'isdel'=>0,
						'addtime'=>time(),
						// 'isself'=>$isself,
						'comment'=>$content
					);
					$commentId = DB::table('opuscomment')->insertGetId($arr);
					if(empty($commentId)) return '评论失败';
					//作品评论数+1
					$this->commentNum($opusId,1);
					$customId = DB::table('personalcustom')->insertGetId($arr2);
					if(!empty($tmpRs) && $fromId != $tmpRs['id']) {
						$content = $nick.'评论了你的作品'.$tmpRs['name'];
						$pushId = $tmpRs['id']; //给toid发送消息
						// $this->addNotification($uid,$fromId,$opusId,$commentId,'',$toid,$content,1,$toid,'');
						$data = array(
							'action'=>0,
							'type'=>1,
							'uid'=>$uid,
							'fromid'=>$fromId,
							'toid'=>$pushId,
							'opusid'=>$opusId,
							'name'=>'',
							'addtime'=>time(),
							'commentid'=>$commentId,
						);
						$distributeMessage = new DistributeMessage();
						$distributeMessage->distriMessage($data);
						$this->pushMsg($pushId,$content,1);
					}
					//推送到受评论的人
					if(!empty($arr['toid']) && $arr['toid']!=$arr['uid']){
						$content = $nick.'回复了你的评论';
						$pushId = $arr['toid']; //给toid发送消息
						// $this->addNotification($pushId,$fromId,$opusId,$commentId,'',$toid,$content,1,$toid,'');
						$data = array(
							'action'=>0,
							'type'=>1,
							'uid'=>$uid,
							'fromid'=>$fromId,
							'toid'=>$pushId,
							'opusid'=>$opusId,
							'name'=>'',
							'addtime'=>time(),
							'commentid'=>$commentId,
						);
						$distributeMessage = new DistributeMessage();
						$distributeMessage->distriMessage($data);
						$this->pushMsg($pushId,$content,1);
					}
					if(!empty($customId))
						$this->repostNum($opusId,$uid);//转发数+1
					if(!empty($tmpRs) && $fromId != $tmpRs['id']) {
						$content = $nick.'转发了你的作品'.$tmpRs['name'];
						$pushId = $tmpRs['id']; //给toid发送消息
						// $this->addNotification($uid,$fromId,$opusId,'','',$toid,$content,2,$toid,$customId);
						$data = array(
							'action'=>0,
							'type'=>2,
							'uid'=>$uid,
							'fromid'=>$fromId,
							'toid'=>$pushId,
							'opusid'=>$opusId,
							'name'=>'',
							'addtime'=>time(),
							'commentid'=>$commentId,
						);
						$distributeMessage = new DistributeMessage();
						$distributeMessage->distriMessage($data);
						$this->pushMsg($pushId,$content,2);
					}
					return $commentId;
				} else { //只转发
					$content = serialize($content);
					$arr2 = array(
						'uid'=>$uid,
						'opusid'=>$opusId,
						'repuid'=>$fromId,
						'opustype'=>1,
						'isdel'=>0,
						'addtime'=>time(),
						// 'isself'=>$isself,
						'comment'=>$content
					);
					$customId = DB::table('personalcustom')->insertGetId($arr2);
					if(!empty($customId))
						$this->repostNum($opusId,$uid);//转发数+1
					if(!empty($tmpRs)) {
						$content = $nick.'转发了你的作品'.$tmpRs['name'];
						$pushId = $tmpRs['id']; //给toid发送消息
						// $this->addNotification($uid,$fromId,$opusId,$commentId,'',$toid,$content,2,$toid,$customId);
						$data = array(
							'action'=>0,
							'type'=>2,
							'uid'=>$uid,
							'fromid'=>$fromId,
							'toid'=>$pushId,
							'opusid'=>$opusId,
							'name'=>'',
							'addtime'=>time(),
							'commentid'=>$commentId,
						);
						$distributeMessage = new DistributeMessage();
						$distributeMessage->distriMessage($data);
						$this->pushMsg($pushId,$content,2);
					}
					return true;
				}
			}
		} else {
			return 'nolog';
		}
	}
	//评论列表
	public function getCommentList() {
		$opusId = Input::get('opusId');
		if(empty($opusId)) return '获取评论失败';
		$count = !empty(Input::get('count')) ? Input::get('count') : 20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$offSet = ($pageIndex-1)*$count;
		++$count;
		// $opusId = 5;
		$sql = "select opuscomment.id,opuscomment.uid,opuscomment.opusid,opuscomment.fromid,opuscomment.toid,opuscomment.content,opuscomment.addtime as caddtime,user.nick,user.portrait,user.sportrait,user.gender,user.grade,user.authtype,user.teenager,user.isleague from opuscomment left join user on user.id = opuscomment.fromid where opuscomment.opusid = {$opusId} and user.isdel != 1 and opuscomment.isdel != 1 order by opuscomment.addtime desc limit $offSet,$count";
		$rs = DB::select($sql);
		if(!empty($rs)) {
			foreach($rs as $key=>&$value) {
				$tmpRs = array();
				$value['content'] = unserialize($value['content']);
				$value['portrait'] = !empty($value['portrait']) ? $this->poem_url.ltrim($value['portrait'],'.') : null;
				$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : null;
				if($value['fromid'] != $value['toid']) {
					$tmpRs = DB::table('user')->where('id','=',$value['toid'])->first(array('id','nick','grade','gender','portrait','sportrait'));
					if(!empty($tmpRs)) {
						$tmpRs['portrait'] = $this->poem_url.ltrim($tmpRs['portrait'],'.');
						$tmpRs['sportrait'] = $this->poem_url.ltrim($tmpRs['sportrait'],'.');
						$value['toUserInfo'] = $tmpRs;
					} else {
						$value['toUserInfo'] = null;
					}
				} else {
					// $value['toUserInfo']['id'] = $value['uid'];
					// $value['toUserInfo']['nick'] = $value['nick'];
					// $value['toUserInfo']['gender'] = $value['gender'];
					// $value['toUserInfo']['grade'] = $value['grade'];
					// $value['toUserInfo']['portrait'] = $url.ltrim($value['portrait'],'.');
					// $value['toUserInfo']['sportrait'] = $url.ltrim($value['sportrait'],'.');
					$value['toUserInfo'] = null;
				}
			}
			unset($value);
		}
		//判断是否有下一页
		if($this->hasMore($rs,$count)) {
			array_pop($rs);
			$rs['hasmore'] = 1;
		} else {
			$rs['hasmore'] = 0;
		}
		return $rs;
	}

	//删除评论
	public function delOpusComment() {
		$info = $this->viaCookieLogin();
		if(!empty($info)) {
			$uid = $info['id'];
			$commentId = Input::get('commentId');
			if(empty($commentId)) return '该评论不存在';
			$sql = "update opuscomment set isdel = 1 where id = {$commentId} and (uid = {$uid} or fromid = {$uid} or toid = {$uid})";
			try {
				if(DB::update($sql)) {
					$tmpRs = DB::table('opuscomment')->where('id','=',$commentId)->first(array('opusid'));
					//作品评论数-1
					if(!empty($tmpRs)) {
						$opusId = $tmpRs['opusid'];
						$this->commentNum($opusId,2);
					}
					return true;
				} else {
					return '没有权限删除此评论';
				}
			} catch (Exception $e) {
				return '删除失败';
			}
		} else {
			return 'nolog';
		}
	}
	//作品评论数操作flag 1+ 2-
	protected function commentNum($opusId,$flag) {
		if(empty($opusId)) return;
		switch ($flag) {
			case 1:
				DB::table('opus')->where('id',$opusId)->increment('commentnum');
				break;
			case 2:
				DB::table('opus')->where('id',$opusId)->decrement('commentnum');
				break;
		}
		$this->commentNumNavOpus($opusId, $flag);
	}
	
	/**
	 * 根据导航分表中的评论数修改
	 * @author:wang.hongli
	 * @since:2016/05/16
	 */
	protected  function commentNumNavOpus($opusId,$flag){
		if(empty($opusId)) return false;
		$table_id = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists('table_id');
		if(empty($table_id)) return false;
		foreach($table_id as $k=>$v){
			$table_name = 'nav_opus_'.$v;
			switch($flag){
				case 1:
					DB::table($table_name)->where('id',$opusId)->increment('commentnum');
					break;
				case 2:
					DB::table($table_name)->where('id',$opusId)->decrement('commentnum');
					break;
			}
		}
	}

	//作品转发数+1
	protected function repostNum($opusId,$uid) {
		if(empty($opusId) || empty($uid)) return false;
		try {
			DB::table('opus')->where('id',$opusId)->increment('repostnum');
			DB::table('user')->where('id',$uid)->increment('repostnum');
			//根据导航分类分表中转发数增加
			$api = new ApiOpus();
			$api->shareNumNavOpus($opusId);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}