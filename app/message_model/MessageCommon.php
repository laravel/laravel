<?php
/**
 * 消息模型base model
 * @author:wang.hongli
 * @since:2016/06/05
 */
class MessageCommon extends Eloquent {
	protected $rules;
	function __construct() {
		$this->rules = array (
				'type' => 'required|integer',
				'fromid' => 'required|integer',
				'toid' => 'required|integer',
				'opusid' => 'integer',
				'addtime' => 'integer',
				'commentid' => 'integer' 
		);
	}
	/**
	 * 更新消息表内容，返回消息id
	 * ＠author:wang.hongli
	 * 
	 * @since :2016/06/05
	 */
	public function addNotice($data) {
		if (empty ( $data )) {
			return false;
		}
		try {
			// 向消息表中添加消息内容
			$insert = array (
					'type' => $data ['type'],
					'fromid' => $data ['fromid'],
					'toid' => $data ['toid'],
					'opusid' => $data ['opusid'],
					'name' => $data ['name'],
					'addtime' => $data ['addtime'],
					'content' => !empty($data ['content']) ? $data['content'] : '',
					'commentid' => $data ['commentid']
			);
			// 验证输入信息
			$validator = Validator::make ( $insert, $this->rules );
			if ($validator->fails ()) {
				return false;
			}
			try {
				$id = DB::table ( 'notice' )->insertGetId ( $insert );
			} catch (Exception $e) {
				print_r($e->getMessage());die;
			}
			return $id;
		} catch ( Exception $e ) {
			return false;
		}
	}
	
	/**
	 * 获取活跃用户总数
	 * @author:wang.hongli
	 * @since:2016/06/06
	 */
	public function getActiveUserCount() {
		//初始化RedisActiveUser对象
		$redisActiveUser = new RedisActiveUser();
		$count = $redisActiveUser->getActiveUserCount();
		return !empty($count) ? intval($count) : 0;
	}
}