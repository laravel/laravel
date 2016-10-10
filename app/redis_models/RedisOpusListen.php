<?php
/**
 * 作品收听相关操作
 * @author:wang.hongli
 * @since:2016/04/08
 */
class RedisOpusListen extends RedisCommon {
	
	/**
	 * 收听作品，每天没人每个作品只能收听配置中的listenNum次
	 * @author:wang.hongli
	 * @param 用户 $uid        	
	 * @param 作品id $opusId        	
	 */
	public function opusListen($uid, $opusId) {
		if (empty ( $uid ) && empty ( $opusId )) {
			return true;
		}
		try {
			$default = $this->getDefaultConnect ();
			$key = 'user:' . $uid . ':opusid:' . $opusId;
			if ($default->exists ( $key )) {
				$listen_limit = Config::get ( 'app.listenNum' );
				$lnum = $default->get ( $key );
				if ($lnum < $listen_limit) {
					$default->incr ( $key );
					return false;
				} else {
					return true;
				}
			} else {
				$tomorrow = strtotime ( date ( 'Y-m-d', strtotime ( '+1 day' ) ) );
				$expire_time = $tomorrow - time ();
				$default->setex ( $key, $expire_time, 1 );
				return false;
			}
		} catch (Exception $e) {
			return true;
		}
	}
}