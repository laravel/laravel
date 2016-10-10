<?php
/**
 * 关注类
 **/
class ApiAttention extends ApiCommon {
	public function attentionListV2() {
		$info = $this->viaCookieLogin ();
		if (empty ( $info ))
			return 'nolog';
		$count = ! empty ( Input::has ( 'count' ) ) ? intval ( Input::get ( 'count' ) ) : 20;
		$return_page_flag = $page_flag = ! empty ( Input::has ( 'pageIndex' ) ) ? intval ( Input::get ( 'pageIndex' ) ) : 0;
		$flag = ! empty ( Input::has ( 'flag' ) ) ? intval ( Input::get ( 'flag' ) ) : 0; // 非空 关注列表 空 歌迷列表
		$otherId = ! empty ( Input::has ( 'otherId' ) ) ? intval ( Input::get ( 'otherId' ) ) : 0;
		$count ++;
		if (! empty ( $otherId )) {
			$uid = intval ( $otherId );
		} elseif (! empty ( $info )) {
			$uid = intval ( $info ['id'] );
		} else {
			return 'nolog';
		}
		$uid_array = array ();
		$rs = array ();
		if (empty ( $page_flag )) {
			$return_page_flag = $page_flag = DB::table ( 'follow' )->max ( 'id' );
		}
		if (! empty ( $flag )) {
			$uid_array = DB::table ( 'follow' )->select('id','fid as uid')->where ( 'uid', $uid )->where ( 'id', '<=', $page_flag )->orderBy ( 'id', 'desc' )->take ( $count )->get ();
		} else {
			$uid_array = DB::table ( 'follow' )->select('id','uid')->where ( 'fid', $uid )->where ( 'id', '<=', $page_flag )->orderBy ( 'id', 'desc' )->take ( $count )->get ();
		}
		if (! empty ( $uid_array )) {
			$tmp_arr = array ();
			$order = array ();
			foreach ( $uid_array as $key => $value ) {
				$order [] = $value ['uid'];
				$tmp_arr [] = $value ['uid'];
				$return_page_flag = $value['id'];
			}
			$orderStr = implode ( ',', $order );
			$rs = DB::table ( 'user' )->select (
						'id', 'nick', 'email', 'opusname',
						'phone', 'gender', 'lnum', 'repostnum',
						'attention', 'praisenum', 'fans', 'opusname',
						'grade', 'sportrait', 'portrait', 'albums', 'signature',
						'authtype', 'teenager', 'isleague' )->whereIn ( 'id', $tmp_arr )->orderByRaw ( DB::raw ( "FIELD (id,$orderStr)" ) )->get();
			if (! empty ( $rs )) {
				// 歌迷消息数组
				if (empty ( $otherId )){
// 					$attenNotify = $this->getAttenNotify ( $uid, 6 );
					$redisNotification  = new RedisNotification();
					$attenNotify = $redisNotification->getAttenNotify($uid);
				}
				foreach ( $rs as $key => &$value ) {
					$value ['sportrait'] = $this->poem_url . ltrim ( $value ['sportrait'], '.' );
					$value ['portrait'] = $this->poem_url . ltrim ( $value ['portrait'], '.' );
					// 获取到关注状态
					$value ['relation'] = $this->attentionStatus ( $info ['id'], $value ['id'] );
					// 判断被关注消息 empty flag 标识获取歌迷列表 $ohter empty 获取自己的
					if (empty ( $flag ) && empty ( $otherId )) {
						//获取关注自己的人的id
// 						$value ['msgStatus'] = !empty ( $attenNotify [$value ['id']] ) ? $attenNotify [$value ['id']] : 0;
						$value['msgStatus'] = in_array($value['id'],$attenNotify) ? 1 : 0;
					}
				}
				unset ( $value );
			}
			if ($this->hasMore ( $uid_array, $count )) {
				array_pop ( $rs );
				$rs ['hasmore'] = $return_page_flag;
			} else {
				$rs ['hasmore'] = 0;
			}
			return $rs;
		}
	}
	// flag 非空获取关注列表 flag空 歌迷列表
	public function attentionList() {
		$info = $this->viaCookieLogin ();
		if (empty ( $info ))
			return 'nolog';
		$count = ! empty ( Input::has ( 'count' ) ) ? intval ( Input::get ( 'count' ) ) : 20;
		$pageIndex = ! empty ( Input::has ( 'pageIndex' ) ) ? intval ( Input::get ( 'pageIndex' ) ) : 1;
		$flag = ! empty ( Input::has ( 'flag' ) ) ? intval ( Input::get ( 'flag' ) ) : 0; // 非空 关注列表 空 歌迷列表
		                                                                                  // $flag = 1;
		$offSet = $count * ($pageIndex - 1);
		$count ++;
		$otherId = ! empty ( Input::has ( 'otherId' ) ) ? intval ( Input::get ( 'otherId' ) ) : 0;
		if (! empty ( $otherId )) {
			$uid = intval ( $otherId );
		} elseif (! empty ( $info )) {
			$uid = intval ( $info ['id'] );
		} else {
			return 'nolog';
		}
		$uid_array = array ();
		$rs = array ();
		if (! empty ( $flag )) {
			$sql = 'select fid as uid from follow where uid = ' . $uid . ' order by dateline desc limit ?,?';
		} else {
			$sql = 'select uid from follow where fid = ' . $uid . ' order by dateline desc limit ?,?';
		}
		$uid_array = DB::select ( $sql, array (
				$offSet,
				$count 
		) );
		if (! empty ( $uid_array )) {
			$tmp_str = '';
			$order = 'order by field(id';
			foreach ( $uid_array as $key => $value ) {
				$order .= ',' . $value ['uid'];
				$tmp_str .= $value ['uid'] . ',';
			}
			$tmp_str = trim ( $tmp_str, ',' );
			$order .= ' )';
			
			$sql = "select id,nick,email,opusname,phone,gender,lnum,repostnum,attention,praisenum,fans,opusname,grade,sportrait,portrait,albums,signature,authtype,teenager,isleague from user where id in (" . $tmp_str . ') ' . $order;
			$rs = DB::select ( $sql );
		}
		if (! empty ( $rs )) {
			// 歌迷消息数组
			if (empty ( $otherId )){
				$redisNotification  = new RedisNotification();
				$attenNotify = $redisNotification->getAttenNotify($uid);
			}
			foreach ( $rs as $key => &$value ) {
				$value ['sportrait'] = $this->poem_url . ltrim ( $value ['sportrait'], '.' );
				$value ['portrait'] = $this->poem_url . ltrim ( $value ['portrait'], '.' );
				// 获取到关注状态
				$value ['relation'] = $this->attentionStatus ( $info ['id'], $value ['id'] );
				// 判断被关注消息 empty flag 标识获取歌迷列表 $ohter empty 获取自己的
				if (empty ( $flag ) && empty ( $otherId )) {
					$value['msgStatus'] = in_array($value['id'],$attenNotify) ? 1 : 0;	
				}
			}
			unset ( $value );
		}
		if ($this->hasMore ( $uid_array, $count )) {
			array_pop ( $rs );
			$rs ['hasmore'] = 1;
		} else {
			$rs ['hasmore'] = 0;
		}
		return $rs;
	}
	// 添加关注
	public function addAttention() {
		$info = $this->viaCookieLogin ();
		// $info['id'] = 27;
		if (! Input::has ( 'fid' ))
			return '关注失败,请重试';
		$relation = 1;
		$time = time ();
		if ($info) {
			$uid = $info ['id'];
			$fid = Input::get ( 'fid' );
			if ($uid == $fid)
				return '自己不能关注自己';
				// $fid = 25;
				// 查看fid是否关注uid
			$tmpArr = DB::table ( 'follow' )->where ( 'uid', '=', $fid )->where ( 'fid', '=', $uid )->first ( array (
					'id' 
			) );
			if (! empty ( $tmpArr )) {
				$relation = 3;
				$followId = $tmpArr ['id'];
			}
			$sql = "insert into follow (uid,fid,relation,dateline) values (?,?,?,?);";
			try {
				DB::insert ( $sql, array (
						$uid,
						$fid,
						$relation,
						$time 
				) );
				if (3 == $relation) {
					$sql = "update follow set relation = ? where id=?";
					if (! DB::update ( $sql, array (
							$relation,
							$followId 
					) )) {
						return '关注失败,请重试';
					}
				}
				// uid 关注数+1 fid歌迷数+1
				if (! $this->followNum ( $uid, 1 ))
					return '关注失败,请重试';
				if (! $this->fansNum ( $fid, 1 ))
					return '关注失败,请重试';
					// 发送消息
// 				$this->addNotification ( $fid, $uid, '', '', '', $fid, '', 6, $fid, '' );
				$data = array(
						'action'=>0,
						'type'=>6,
						'uid'=>$uid,
						'fromid'=>$uid,
						'toid'=>$fid,
						'opusid'=>0,
						'name'=>'',
						'addtime'=>time(),
						'content'=>'',
						'commentid'=>0
				);
				$distributeMessage = new DistributeMessage();
				$distributeMessage->distriMessage($data);
				return true;
			} catch ( Exception $e ) {
				return '已关注此人';
			}
		} else {
			return 'nolog';
		}
	}
	// 取消关注
	public function undoAttention() {
		$info = $this->viaCookieLogin ();
		if (! Input::has ( 'fid' ))
			return '取消关注失败,请重试';
		$time = time ();
		if ($info) {
			$uid = $info ['id'];
			$fid = Input::get ( 'fid' );
			$tmpArr = DB::table ( 'follow' )->where ( 'uid', '=', $uid )->where ( 'fid', '=', $fid )->first ( array (
					'relation' 
			) );
			if (empty ( $tmpArr ))
				return '没关注过,怎么取消关注 ?';
			if (3 == $tmpArr ['relation']) {
				$sql = "update follow set relation = 1 where uid = ? and fid = ? ";
				if (! DB::update ( $sql, array (
						$fid,
						$uid 
				) )) {
					return '取消关注失败';
				}
			}
			$sql = "delete from follow where uid=? and fid=?";
			if (! DB::delete ( $sql, array (
					$uid,
					$fid 
			) )) {
				return '取消关注失败';
			}
			// uid 关注数-1 fid歌迷数-1
			if (! $this->followNum ( $uid, 2 ))
				return '取消关注失败';
			if (! $this->fansNum ( $fid, 2 ))
				return '取消关注失败';
			return true;
		} else {
			return 'nolog';
		}
	}
	
	// 移除粉丝
	public function undoFans() {
		$info = $this->viaCookieLogin ();
		if (! Input::has ( 'fid' ))
			return '没人,你叫我怎么移除粉丝?';
		if ($info) {
			$uid = $info ['id'];
			$fid = Input::get ( 'fid' );
			$tmpArr = DB::table ( 'follow' )->where ( 'uid', '=', $fid )->where ( 'fid', '=', $uid )->first ( array (
					'relation' 
			) );
			if (empty ( $tmpArr ))
				return '移除失败';
			$relation = $tmpArr ['relation'];
			if (3 == $relation) {
				$sql = "delete from follow where uid = ? and fid = ?";
				if (DB::delete ( $sql, array (
						$fid,
						$uid 
				) )) {
					// uid粉丝数-1 fid 关注数-1
					if (! $this->fansNum ( $uid, 2 ) || ! $this->followNum ( $fid, 2 )) {
						return '移除失败';
					}
					$sql = "delete from follow where uid = ? and fid = ?";
					if (! DB::delete ( $sql, array (
							$uid,
							$fid 
					) )) {
						return '移除失败';
					}
					// uid 关注数-1 fid粉丝数-1
					if (! $this->followNum ( $uid, 2 ) || ! $this->fansNum ( $fid, 2 )) {
						return '移除失败';
					}
					return true;
				}
			} else {
				$sql = "delete from follow where uid = ? and fid = ?";
				if (! DB::delete ( $sql, array (
						$fid,
						$uid 
				) ))
					return '移除失败';
					// uid 粉丝-1 fid关注-1
				if (! $this->fansNum ( $uid, 2 ) || ! $this->followNum ( $fid, 2 )) {
					return '移除失败';
				}
				return true;
			}
		} else {
			return 'nolog';
		}
	}
	
	// 增加|减少关注数 flag 1+ 2-
	protected function followNum($uid = '', $flag = '') {
		if (empty ( $flag ) || empty ( $uid ))
			return false;
		switch ($flag) {
			case 1 :
				$addAttention = 'attention=attention+1';
				break;
			case 2 :
				$addAttention = 'attention=attention-1';
				break;
		}
		$sql = "update user set {$addAttention} where id = {$uid}";
		if (DB::update ( $sql )) {
			return true;
		} else {
			return false;
		}
	}
	
	// 增加|减少歌迷数 flag 1+ 2-
	protected function fansNum($uid = '', $flag = '') {
		if (empty ( $flag ) || empty ( $uid ))
			return false;
		switch ($flag) {
			case 1 :
				$addFans = 'fans=fans+1';
				break;
			case 2 :
				$addFans = 'fans=fans-1';
				break;
		}
		$sql = "update user set {$addFans} where id = {$uid}";
		if (DB::update ( $sql )) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 获取被关注消息
	 *
	 * @author :wang.hongli
	 * @since :2015/01/11
	 * @param
	 *        	:$uid 登陆人的id
	 * @param
	 *        	:$type 消息类型
	 */
	// protected function getAttenNotify($uid, $type = 6) {
	// 	$return = array ();
	// 	if (! empty ( $uid )) {
	// 		$sql = "select id,fromid from notification where ownid = {$uid} and type={$type} and isdel = 0 and isnew = 0";
	// 		$rs = DB::select ( $sql );
	// 		if (! empty ( $rs )) {
	// 			foreach ( $rs as $k => $v ) {
	// 				$return [$v ['fromid']] = $v ['id'];
	// 			}
	// 		}
	// 	}
	// 	return $return;
	// }
}