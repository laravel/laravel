<?php

/**
 *	后台用户模型
 *	@author:wang.hongli
 *	@since:2016/04/02
 **/
class AdminUser extends AdminCommon {
	/**
	 * 后台恢复用户自动关注
	 *
	 * @author :wang.hongli
	 * @since :2016/04/02
	 * @param
	 *        	:$id 用户id
	 *        	
	 */
	public function autoAttention($id) {
		if (empty ( $id ))
			return;
		$id = intval ( $id );
		$tmpRs = DB::table ( 'user' )->where ( 'recommenduser', '<', 9999 )->orderBy ( 'recommenduser', 'asc' )->get ();
		if (empty ( $tmpRs )) {
			return;
		}
		$data = array ();
		$attentionNum = count ( $tmpRs );
		$time = time ();
		$recommendUserId = array ();
		foreach ( $tmpRs as $key => $value ) {
			$time = $time - 1;
			$data [] = array (
					'uid' => $id,
					'fid' => $value ['id'],
					'relation' => 1,
					'dateline' => $time 
			);
			$recommendUserId [] = $value ['id'];
		}
		try {
			DB::table ( 'follow' )->insert ( $data );
			// 推荐用户粉丝数+1
			DB::table ( 'user' )->whereIn ( 'id', $recommendUserId )->increment ( 'fans', 1 );
			// 用户的关注数
			DB::table ( 'user' )->where ( 'id', $id )->update ( array (
					'attention' => $attentionNum
			) );
		} catch (Exception $e) {
			return;
		}
		
	}
}
