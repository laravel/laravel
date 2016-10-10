<?php
/**
*	所有有关的权限检测
*	@author:wang.hongli
*	@since:2016/07/07
**/
class ApiCheckPermission extends ApiCommon{
	
	/**
	* 权限检测分发
	*@author:wang.hongli
	*@since:2016/07/07
	*@param:action  -- 动作 check_league:诵读会,check_competition:比赛检测,check_finish_competition:结束比赛检测
	*@param:info 用户信息 type 
	*@param:competitionid 比赛id
	*/
	public function check_permission($competitionid=0,$type=0){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			$rs = ['status'=>-101,'message'=>'请登陆'];
		}
		if(!empty($type) && $type == 1){
			$rs = $this->check_league($info,[]);
			return $rs;
		}
		if(!empty($competitionid)){
			$competitioninfo = DB::table('competitionlist')->where('id',$competitionid)->first();
			//其他比赛权限检测
			$rs = $this->dif_check_competition($info,$competitioninfo);
		}else{
			$rs = ['status'=>0,'message'=>'获取比赛信息失败'];
		}
		return $rs;
	}

	/**
	* 权限检测分发
	*@author:wang.hongli
	*@since:2016/07/07
	*@param:action  -- 动作 check_league:诵读会,check_competition:比赛检测,check_finish_competition:结束比赛检测
	*@param:info 用户信息 type 
	*@param:competitionid 比赛id
	*/
	public function check_permissionv2($competitionid=0,$type=0){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			$rs = ['status'=>-101,'message'=>'请登陆'];
		}
		if(!empty($type) && $type == 1){
				$rs = $this->check_leaguev2($info,[]);
				return $rs;
		}elseif(!empty($competitionid)){
			$competitioninfo = DB::table('competitionlist')->where('id',$competitionid)->first();
			//结束比赛参赛
			if($competitioninfo['isfinish'] == 1 && in_array($competitioninfo['pid'],[4,5,6,7])){
				$rs = $this->check_finish_competition($info,$competitioninfo);
				return $rs;
			}else{
				//其他比赛权限检测
				$rs = $this->dif_check_competition($info,$competitioninfo);
				return $rs;
			}
		}else{
			return ['status'=>0,'message'=>'获取比赛信息失败'];
		}
	}
	/**
	*诵读联合会
	*@author:wang.hongli
	*@param:2016/07/07
	**/
	private function check_league($info=[],$competitioninfo=[]){
		if(empty($info)){
			return ['status'=>-101,'message'=>'请登录'];
		}
		$uid = $info['id'];
		$rs = DB::table('user_permission')->where('uid','=',$uid)->where('type','=',1)->first(['id','over_time']);
		if(empty($rs)){
			return ['status'=>-2,'message'=>'没有权限'];
		}
		return ['status'=>1,'message'=>'success'];
	}
	/**
	*诵读联合会
	*@author:wang.hongli
	*@param:2016/07/07
	**/
	private function check_leaguev2($info=[],$competitioninfo=[]){
		if(empty($info)){
			return ['status'=>-101,'message'=>'请登录'];
		}
		$uid = $info['id'];
		$rs = DB::table('user_permission')->where('uid','=',$uid)->where('type','=',1)->first(['id','over_time']);
		if(empty($rs)){
			return ['status'=>-2,'message'=>'没有权限'];
		}
		$time = time();
		if($rs['over_time'] < $time){
			return ['status'=>-1,'message'=>'到期续费'];
		}else{
			return  ['status'=>1,'message'=>'再次续费'];
		}
	}

	/**
	*诵读会，诵读比赛权限检测
	*@author:wang.hongli
	*@since:2016/07/07
	**/
	private function dif_check_competition($user_info=[],$competitioninfo=[]){

		if(empty($user_info)){
			return ['status'=>-101,'message'=>'请登录'];
		}
		if(empty($competitioninfo)){
			return ['status'=>0,'message'=>'没有比赛'];
		}
		$uid = $user_info['id'];
		$competitionid = $competitioninfo['id'];
		//获取数据库中收费比赛和用户选择的比塞
		$competitionlist = DB::table('competitionlist')->whereIn('pid',array(4,6,7))->lists('id');

		if($competitioninfo['isfinish'] == 1){
			return ['status'=>-1,'message'=>'比赛已经结束'];
		}
		if(in_array($competitionid,$competitionlist)){
			//进行资格检测
			$id = DB::table('user_permission')->where('uid','=',$uid)->where('type','=',$competitionid)->first(['id']);
			if(empty($id)){
				return ['status'=>-2,'message'=>'您需先交纳报名费后再上传作品'];
			}
		}
		//检测诗文比赛权限
		$check_com = DB::table('competitionlist')->whereIn('pid',array(6,7))->lists('id');
		if(!empty($check_com) && in_array($competitionid,$check_com)){
			$c = DB::table('opus_poetry')->where('competitionid',$competitionid)->where('uid',$uid)->where('status',2)->first(['id']);
			if(!empty($c)){
				//已经参加过
				return ['status'=>-3,'message'=>'您只能提交一个参赛作品'];
			}else{
				return ['status'=>1,'message'=>'success'];
			}
		}
		//其他比赛数量检测
		$check_type_arr = DB::table('competitionlist')->whereIn('pid',[1,2,3,4,5])->lists('id');
		//不在检测活动范围，返回true
		if(!in_array($competitionid, $check_type_arr))
		{
			return ['status'=>1,'message'=>'success'];
		}
		$opusid = DB::table('competition_opus_rel')
						->where('uid','=',$uid)
						->where('competitionid','=',$competitionid)
						->first(['opusid']);
		if(!empty($opusid))
		{
			return ['status'=>-3,'message'=>'您只能提交一个参赛作品'];
		}
		return ['status'=>1,'message'=>'success'];
	}

	/**
	*结束的诵读比赛，诗文比赛权限检测
	*@author:wang.hongli
	*@since:2016/07/08
	**/
	private function check_finish_competition($info,$competitioninfo=[]){
		if(empty($info)){
			return ['status'=>-101,'message'=>'请登录'];
		}
		if(empty($competitioninfo)){
			return ['status'=>0,'message'=>'没有比赛'];
		}
		$uid = $info['id'];
		$competitionid = $competitioninfo['id'];

		//获取商品信息
		$good_info = DB::table("goods")->where('competition_id',$competitionid)->where('flag',2)->first();
		if(empty($good_info)){
			return ['status'=>-1,'message'=>'商品信息获取失败'];
		}
		//权限检测
		$rs = DB::table('finish_comp_user')->where('uid',$uid)->where('competitionid',$competitionid)->first();
		if(empty($rs)){
			return ['status'=>-4,'message'=>'没有权限参赛'];
		}
		//是否报过名
		$rs = DB::table('order_list')->where('uid',$uid)->where('goods_id',$good_info['id'])->where('status',2)->first();
		if(empty($rs)){
			return ['status'=>-5,'message'=>'success'];
		}
		//查询是否有光盘
		$attach_rs = DB::table('goods')->where('good_pid',$good_info['id'])->where('flag',2)->first();
		if(empty($attach_rs)){
			return ['status'=>-6,'message'=>'无光盘'];
		}
		if(!empty($rs['attach_id'])){
			return ['status'=>-6,'message'=>'已经报名并且购买了光盘'];
		}
		else{
			return ['status'=>-7,'message'=>'已经报名但是没有购买光盘'];
		}
	}

	/**
	 * 判断用户是否为会员
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function isMember($uid=0){
		if(empty($uid)){
			return 0;
		}
		try {
			$rs = DB::table('user_members')->where('uid',$uid)->first(['starttime','endtime']);
			if(empty($rs)){
				return 0;
			}
			$time = time();
			if($time>=$rs['starttime'] && $time<=$rs['endtime']){
				return 1;
			}
		} catch (Exception $e) {
		}
		return 0;
	}

	/**
	 * 判断用户钻石，鲜花数是否充足
	 * @author :wang.hongli
	 * @since:2016/08/14
	 * @param : $type 0 钻石 1鲜花
	 * @param :expend 消费的数量
	 */
	public function isEnough($uid=0,$type=0,$expend=0){
		if(empty($uid) || empty($expend)){
			return false;
		}
		try {
			switch ($type) {
				case 0:
					$clumn = 'jewel';
					break;
				case 1:
					$clumn = 'flower';
					break;
			}
			$num = DB::table('user_asset_num')->where('uid',$uid)->pluck($clumn);
			if($num>=$expend){
				return true;
			}else{
				return false;
			}
		} catch (Exception $e) {
			return false;
		}

	}
}
?>