<?php
/**
 *	作品收藏模型
 **/
class ApiCollection extends ApiCommon {
	// 收藏 or 删除收藏
	public function colEdit() {
		$info = $this->viaCookieLogin ();
		if ($info) {
			$uid = $info ['id'];
			$nick = $info ['nick'];
			$time = time ();
			$flag = Input::get ( 'flag' ); // 1添加2删除
			if (! $flag)
				return '参数错误';
			if (! Input::has ( 'opusId' ))
				return '此作品不存在';
			$opusId = intval ( Input::get ( 'opusId' ) );
			// 选出作品主人id
			$ownId = DB::table ( 'opus' )->where ( 'id', '=', $opusId )->where ( 'isdel', 0 )->pluck ( 'uid' );
			if (empty ( $ownId ))
				return '作品收藏失败，请重试';
				// $opusId = 2;
			switch ($flag) {
				case 1 :
					try {
						$insert_flag = DB::table ( 'collection' )->insert ( array (
								'opusid' => $opusId,
								'uid' => $uid,
								'ownid' => $ownId,
								'addtime' => $time 
						) );
						if ($insert_flag) {
							$this->collectionNum ( 0, $opusId );
							// 获取作品主人id，作品名称,推送消息
							$tmpRs = $this->getOpusAndUserInfo ( $opusId );
							if (! empty ( $tmpRs )) {
								$content = $nick . '收藏了你的作品' . $tmpRs ['name'];
								$toid = $tmpRs ['id']; // 给toid发送消息
								$data = array(
										'action'=>0,
										'type'=>4,
										'uid'=>$uid,
										'fromid'=>$uid,
										'toid'=>$toid,
										'opusid'=>$opusId,
										'name'=>$tmpRs['name'],
										'addtime'=>time(),
										'content'=>'',
										'commentid'=>0
								);
								$distributeMessage = new DistributeMessage();
								$distributeMessage->distriMessage($data);
								$this->pushMsg($toid,$content,4);
							}
						} else {
							return '收藏失败，请重试';
						}
					} catch ( Exception $e ) {
						return '此作品已收藏';
					}
					break;
				case 2 :
					try {
						$delete_flag = DB::table ( 'collection' )->where ( 'uid', $uid )->where ( 'opusid', $opusId )->delete ();
						if ($delete_flag) {
							$this->collectionNum ( 1, $opusId );
						}
					} catch ( Exception $e ) {
						return '删除失败,请重试';
					}
					break;
			}
			return true;
		} else {
			return 'nolog';
		}
	}
	
	// 我的收藏列表
	public function colList() {
		$info = $this->viaCookieLogin ();
		// $info['id'] = 28;
		$count = Input::has ( 'count' ) ? Input::get ( 'count' ) : 20;
		$pageIndex = Input::has ( 'pageIndex' ) ? Input::get ( 'pageIndex' ) : 1;
		$offSet = ($pageIndex - 1) * $count;
		++ $count;
		$sql = "select user.id as uid,user.portrait,user.sportrait,user.gender,user.grade,user.nick,user.authtype,user.teenager,opus.id,opus.name,opus.lnum,opus.praisenum,opus.repostnum,opus.lyricurl,opus.url,opus.opustime,opus.`type`,opus.poemid,opus.commentnum,opus.opustime,opus.writer,opus.reader,collection.addtime as caddtime,user.isleague from collection left join opus on opus.id = collection.opusid left join user on user.id = collection.ownid where collection.uid = ? and opus.isdel != 1 order by collection.addtime desc limit $offSet,$count;";
		if (Input::has ( 'otherId' )) {
			$otherId = intval ( Input::get ( 'otherId' ) );
			$rs = DB::select ( $sql, array (
					$otherId 
			) );
		} elseif (! empty ( $info )) {
			$uid = $info ['id'];
			$rs = DB::select ( $sql, array (
					$uid 
			) );
		} else {
			return 'nolog';
		}
		if (! empty ( $rs )) {
			foreach ( $rs as $key => &$value ) {
				$readerArr = array ();
				$value ['portrait'] = $this->poem_url . ltrim ( $value ['portrait'], '.' );
				$value ['sportrait'] = $this->poem_url . ltrim ( $value ['sportrait'], '.' );
				$value ['lyricurl'] = $this->poem_url . $value ['lyricurl'];
				$value ['url'] = $this->poem_url . $value ['url'];
				$value['writer'] = !empty($value['writer']) ? $value['writer']:'佚名';
				$value['reader'] = !empty($value['reader']) ? $value['reader'] : '佚名';
			}
			unset ( $value );
			$num = count ( $rs );
			if ($num >= $count) {
				array_pop ( $rs );
				$rs ['hasmore'] = 1;
			} else {
				$rs ['hasmore'] = 0;
			}
		}
		return $rs;
	}
	
	// 增加/减少收藏数0+ 1-
	protected function collectionNum($type = 0, $opusId = '') {
		if (empty ( $opusId ))
			return false;
		$opusId = intval ( $opusId );
		try {
			if ($type) {
				DB::table ( 'opus' )->where ( 'id', $opusId )->decrement ( 'collectnum' );
			} else {
				DB::table ( 'opus' )->where ( 'id', $opusId )->increment ( 'collectnum' );
			}
		} catch ( Exception $e ) {
			return false;
		}
	}
}