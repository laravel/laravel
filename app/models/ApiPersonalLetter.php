<?php 
/**
*	私信模型
**/
class ApiPersonalLetter extends ApiCommon {

	//发送私信
	public function sendPersonLetter() {
		$errorMessage = "你的内容中涉及敏感信息，不能公开发布！";
		$info = $this->viaCookieLogin();
		// $info['id'] = 28;
		// $info['nick'] = 'chunt';
		if($info) {
			$type = Input::get('type'); //0普通消息 1语音消息
			if(empty($type)) {
				$type = 0;
				$duration = 0;
				$voiceurl = null;
			} else {
				//上传语音
				$filePath = $this->isExistDir('voice');
				$arr = Input::file('formName');
				// print_r($arr);die;
				if(!empty($arr)) {
					// $ext = $arr->guessExtension();
					$ext = 'amr';
					$name = time().uniqid();
					$name = $name.'.'.$ext;
					$lastFilePath = $filePath.$name;
					$arr->move($filePath,$name);
					$lastFilePath = ltrim($lastFilePath,'.');
					// $sql = "update user set bgpic='{$lastFilePath}' where id = $uid";
				} else {
					return '发送语音失败';
				}
				$type = 1;
				$voiceurl = $lastFilePath;
				$duration = Input::get('duration');
				if(empty($duration)) {
					$duration = 0;
				}
			}
			$msgFromId = $msgFrom = $info['id'];
			$msgFnick = $info['nick'];
			$msgToId = $msgTo =  Input::get('msgToId');//对方id
			// $msgToId = $msgTo = 27;
			$msgToNick = Input::get('msgToNick');//对方昵称
			// $msgToNick = 'waka';
			if(empty($msgToNick) || empty($msgToId)) return '接收人不存在';
			$message = Input::get('message'); //私信内容
			if(my_sens_word($message))
			{
				return $errorMessage;
			}
			// $message = 'hahah';
			// if(empty($message))  return '私信内容不能为空';
			if(empty($message)) {
				$message = null;
			}
			$message = serialize($message);
			$new = 0;
			$dateLine = time();
			$delStatus = 0;//是否删除0没删除1发送的人删除2接收的人删除
			$plid = $this->getPlid($msgFromId,$msgToId);
			if(empty($plid)) return '发送失败,请重试';

			$pmid = DB::table('pms')->insertGetId(
					array(
						'msgfrom'	=>	$msgFrom,
						'msgfnick'	=>	$msgFnick,
						'msgfromid'	=>	$msgFromId,
						'msgto'		=>	$msgTo,
						'msgtnick'	=>	$msgToNick,
						'msgtoid' 	=>	$msgToId,
						'new'		=>	$new,
						'dateline'	=>	$dateLine,
						'message' 	=> 	$message,
						'delstatus' =>	$delStatus,
						'type'		=> 	$type,
						'voiceurl' 	=> 	$voiceurl,
						'plid' 		=> 	$plid,
						'duration' 	=> 	$duration
					));
			if(empty($pmid)) return '发送失败,请重试';
			//最新消息列表pms_list -- 同pms_index中uids一样，有更新，无插入
			$message = unserialize($message);
			$lastMessage = serialize(array(
				'msgFrom'	=>	$msgFrom,
				'msgFnick'	=> 	$msgFnick,
				'msgFromId'	=> 	$msgFromId,
				'msgTo'		=>	$msgTo,
				'msgToNick'	=>	$msgToNick,
				'msgToId'	=> 	$msgToId,
				'message' 	=> 	$message,
				'new' 		=> 	0,
				'dateline' 	=> 	$dateLine,
				'voiceurl' 	=>	$voiceurl,
				'duration'	=> 	$duration
			));
			$sql1 = "insert into pms_list (plid,uid,pmnum,dateline,lastmessage,voiceurl,duration,is_new) values ($plid,$msgFromId,1,$dateLine,'{$lastMessage}','{$voiceurl}',$duration,0)";
			$sql2 = "insert into pms_list (plid,uid,pmnum,dateline,lastmessage,voiceurl,duration,is_new) values ($plid,$msgToId,1,$dateLine,'{$lastMessage}','{$voiceurl}',$duration,0);";
			try {
					$flag1 = DB::insert($sql1);
					// if(!$flag1) return '发送失败';
				} catch (Exception $e) {
					$sql1 = "update pms_list set lastmessage='{$lastMessage}',voiceurl='{$voiceurl}',duration={$duration},pmnum=pmnum+1,is_new=0 where uid = {$msgFromId} and plid={$plid}";
					DB::update($sql1);
				}
				
			try {
				$flag2 = DB::insert($sql2);
				} catch(Exception $e) {
					$sql2 = "update pms_list set lastmessage='{$lastMessage}',voiceurl='{$voiceurl}',duration={$duration},is_new=0 where uid = {$msgToId} and plid={$plid}";
					DB::update($sql2);
				}
			//发送私信推送消息
			$content = $msgFnick.'给您发了一条私信';
			// $this->pushMsg($msgToId,$content,5);
			$this->addNotification($msgToId,$msgFromId,'','',$plid,$msgToId,$content,5,$msgToId);
			return $pmid;
 		} else {
			return 'nolog';
		}
	}

	//私信列表,1，从消息列表过来，2，好友主页过来
	public function persinalLetterList() {
		$info = $this->viaCookieLogin();
		// $info['id'] = 28;
		if($info) {
			$count = !empty(Input::get('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			$msgFromId = $info['id'];
			$plid = Input::get('plid');
			if(empty($plid)) {
				$msgToId = Input::get('msgToId');
				// $msgToId = 27;
				$str = null;
				if($msgFromId > $msgToId) {
					$str = $msgToId.','.$msgFromId;
				} else {
					$str = $msgFromId.','.$msgToId;
				}
				$plidArr = DB::table('pms_index')->where('uids','=',"{$str}")->first(array('plid'));
				if(empty($plidArr)) return '获取消息列表失败';
				$plid = $plidArr['plid'];
			}
			$sql = "select msgfromid,msgfnick,duration,msgtoid,msgtnick,new,dateline,message,voiceurl,type,plid,pmid from pms where plid = $plid and `msgfromid` = $msgFromId and `delstatus` != 1 union all select msgfromid,msgfnick,duration,msgtoid,msgtnick,new,dateline,message,voiceurl,type,plid,pmid from pms where plid = $plid and `msgtoid` = $msgFromId and `delstatus` != 2";
			$rs = DB::select($sql);
			//对message反序列化
			if(!empty($rs)) {
				foreach($rs as $key=>&$value) {
					$value['message'] = unserialize($value['message']);
					$value['plid'] = $plid;
					if(!empty($value['voiceurl'])) $value['voiceurl'] = $this->poem_url.$value['voiceurl'];
				}
			}
			//判断是否有下一页
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
			return $rs;
		} else {
			return 'nolog';
		}
	}

	//删除私信
	public function delPersinalLetter() {
		$info = $this->viaCookieLogin();
		// $info['id'] = 27;
		if($info) {
			$uid = $info['id'];
			$plid = Input::get('plid');
			// $plid = 1;
			if(empty($plid)) return '删除私信失败';
			$sql = "delete from pms_list where plid = $plid and uid = $uid";
			try {
				if(DB::delete($sql)) {
					//删除列表中数据
					$this->delPms($uid,$plid);
					return true;
				}
			} catch (Exception $e) {
				return '删除失败，请重试';
			}
		} else {
			return 'nolog';
		}
	}
	//pms_index表操作 返回plid值
	protected function getPlid($msgFromId='',$msgToId='') {
		//id 升序排序
		$str = null;
		if($msgFromId > $msgToId) {
			$str = $msgToId.','.$msgFromId;
		} else {
			$str = $msgFromId.','.$msgToId;
		}
		//查看uid,toid 是否存在
		$rs = DB::table('pms_index')->where('uids','=',"{$str}")->first(array('plid'));
		if(!empty($rs)) {
			$plid = $rs['plid'];
		} else {
			$plid = DB::table('pms_index')->insertGetId(
									array(
										'uids'=>"{$str}"
									));
		}
		return $plid;
	}

	//删除私信
	protected function delPms($uid,$plid) {
		//msgfromid 1 msgtoid 2 删除
		$sql = "select msgfromid,msgtoid,pmid,delstatus from pms where plid = $plid and msgfromid = $uid and delstatus !=1 union select  msgfromid,msgtoid,pmid,delstatus from pms where plid = $plid and msgtoid = $uid and delstatus != 2";
		$rs = DB::select($sql);
		if(!empty($rs)) {
			foreach($rs as  $key=>$value) {
				if(0 == $value['delstatus']) {
					if($uid = $value['msgfromid']) {
						$delStatus = 1;
					} else {
						$delStatus = 2;
					}
					$sql = "update pms set delstatus = {$delStatus} where pmid = {$value['pmid']}";
					try {
						DB::update($sql);
					} catch (Exception $e) {
						continue;
					}
				} else {
					$sql = "delete from pms where pmid = {$value['pmid']}";
					try {
						DB::delete($sql);
					} catch (Exception $e) {
						continue;						
					}
				}
			}
		}
	}
}