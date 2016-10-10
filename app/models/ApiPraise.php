<?php 

/**
*	用户对作品赞模型
**/
class ApiPraise extends ApiCommon 
{
	public function praiseEdit(){
		$info = $this->viaCookieLogin();
		if(!empty($info)){
			$uid = $info['id'];
			$nick = $info['nick'];
			if(!Input::has('opusId')){
				return false;
			}
			$opusId = intval(Input::get('opusId'));
			//flag 1 赞默认 2 取消赞
			$flag = !empty(Input::has('flag')) ? intval(Input::get('flag')) : 1;
			//操作redis praise:user:5:opus
			$redisOpusPraise = new RedisOpusPraise();
			$operator_flag = $redisOpusPraise->praiseEdit($uid, $opusId, $flag);
			//操作成功后，执行后续操作
			if($operator_flag){
				switch($flag){
					case 1:
						$this->praiseNum($opusId,0);
						//获取作品主人id，作品名称,推送消息
						$tmpRs = $this->getOpusAndUserInfo($opusId);
						if(!empty($tmpRs)) {
							$content = $nick.'赞了你的作品'.$tmpRs['name'];
							$toid = $tmpRs['id']; //给toid发送消息
// 							$this->addNotification($toid,$uid,$opusId,'','',$toid,$content,3,$toid,'');
							$data = array(
									'action'=>0,
									'type'=>3,
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
							
							$this->pushMsg($toid,$content,3);
						}
						break;
					case 2:
						$this->praiseNum($opusId,1);
						break;
				}
			}
			return true;
		}else{
			return 'nolog';
		}
	}
	//用户表praisenum,作品表赞数+1 0+ 1- 总赞的次数+1 0+ 1-
	protected function praiseNum($opusId,$flag) {
		$opusId = intval($opusId);
		$flag = intval($flag);
		$opusOwnerId = DB::table('opus')->where('id',$opusId)->pluck('uid');
		if(!empty($opusOwnerId)) {
			try {
				if(empty($flag)){
					DB::table('user')->where('id',$opusOwnerId)->increment('praisenum');
					DB::table('opus')->where('id',$opusId)->increment('praisenum');
					//朗诵会会员赞数增加
					DB::table('league_user')->where('uid',$opusOwnerId)->increment('praisenum');
				}else{
					DB::table('user')->where('id',$opusOwnerId)->decrement('praisenum');
					DB::table('opus')->where('id',$opusId)->decrement('praisenum');
					//朗诵会会员赞数增加
					DB::table('league_user')->where('uid',$opusOwnerId)->decrement('praisenum');
				}
				//作品按照导航分表中作品赞
				$this->praiseNumNavOpus($opusId, $flag);
			} catch (Exception $e) {
			}
		}
	}
	
	/**
	 * 作品赞--分表冗余数据
	 * @author:wang.hongli
	 * @since:2016/05/16
	 */
	protected function praiseNumNavOpus($opusId,$flag){
		//选出作品所在的表
		$tables = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists("table_id");
		if(empty($tables)) return;
		foreach($tables as $k=>$v){
			$table_name = 'nav_opus_'.$v;
			try {
				if(empty($flag)){
					DB::table($table_name)->where('opusid',$opusId)->increment('praisenum');
				}else{
					DB::table($table_name)->where('opusid',$opusId)->decrement('praisenum');
				}
			} catch (Exception $e) {
			}
		}
	}


}