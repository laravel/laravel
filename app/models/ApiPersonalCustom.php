<?php 
/**
*	私人定制
**/
class ApiPersonalCustom extends ApiCommon {
	//获取私人定制列表
	public function getPCList()
	{
		$info = $this->viaCookieLogin();
		$uid = $info['id'];
		if(empty($uid)) return 'nolog';
		$count = !empty(Input::get('count')) ? Input::get('count') : 20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$offSet = ($pageIndex-1)*$count;
		++$count;
		//获取被我关注的人的id
		$uidRs = DB::table('follow')->where('uid','=',$uid)->lists('fid');
		if(empty($uidRs)){
			$uidRs = array($uid);
		}else{
			array_push($uidRs, $uid);
		}
		//关注表中选出转发人的相关信息
		$sql = "select id,uid as ownid,repuid,opustype,opusid,comment,addtime from personalcustom force index(PRI) where isdel !=1 and repuid in ( ".implode(',', $uidRs)." ) order by id desc limit {$offSet},{$count}";
		$rs = DB::select($sql);
		//转发人信息
		$repRs = array();
		//作品主人id
		$ownRs = array();
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$repRs[$v['repuid']] = $v;
				$ownRs[$v['opusid']] = $v['ownid']; 
			}
		}
		//选出转发用户信息
		$repIds = array_keys($repRs);
		$repUserRs_tmp = DB::table('user')->whereIn('id',$repIds)->select('id as uid','nick','gender','grade','authtype','sportrait','portrait','teenager','isleague')->get();
		$repUserRs = array();
		foreach($repUserRs_tmp as $repuserRs_k=>$repuserRs_v)
		{
			$repuserRs_v['portrait'] = !empty($repuserRs_v['portrait']) ? $this->poem_url.ltrim($repuserRs_v['portrait'],'.') : null;
			$repuserRs_v['sportrait'] = !empty($repuserRs_v['sportrait']) ? $this->poem_url.ltrim($repuserRs_v['sportrait'],'.') : null;
			$repUserRs[$repuserRs_v['uid']] = $repuserRs_v;
		}
		//选出作品主人信息
		$ownIds = array_unique(array_values($ownRs));
		$ownUserRs_tmp = DB::table('user')->whereIn('id',$ownIds)->select('id','nick','gender','grade','portrait','sportrait','authtype','teenager','isleague')->get();
		$ownUserRs = array();
		foreach($ownUserRs_tmp as $ownUserRs_k=>$ownUserRs_v)
		{
			$ownUserRs_v['portrait'] = !empty($ownUserRs_v['portrait']) ? $this->poem_url.ltrim($ownUserRs_v['portrait'],'.') : null;
			$ownUserRs_v['sportrait'] = !empty($ownUserRs_v['sportrait']) ? $this->poem_url.ltrim($ownUserRs_v['sportrait'],'.') : null;
			$ownUserRs[$ownUserRs_v['id']] = $ownUserRs_v;
		}
		//选出作品信息
		$opusIds = array_keys($ownRs);
		$opusRs_tmp = DB::table('opus')->whereIn('id',$opusIds)->select('id','name','lnum','praisenum','repostnum','commentnum','opustime','lyricurl','url','addtime')->get();
		$opusRs = array();
		foreach($opusRs_tmp as $opusRs_k=>$opusRs_v)
		{
			$opusRs_v['lyricurl'] = $this->poem_url.$opusRs_v['lyricurl'];
			$opusRs_v['url'] = $this->poem_url.$opusRs_v['url'];
			$opusRs[$opusRs_v['id']] = $opusRs_v;
		}
		//获取登录用户收藏的所有作品
		$collection_opus = ApiCommonStatic::isCollection_v2($uid);
		//获取登录用户赞过的所有作品
		$praise_opus = ApiCommonStatic::isPraise_v2($uid);

		$data = array();
		foreach($rs as $repRs_tmp_k=>&$repRs_tmp_v)
		{
			//转发用户头像
			$rep_portrait = isset($repUserRs[$repRs_tmp_v['repuid']]['portrait']) ? $repUserRs[$repRs_tmp_v['repuid']]['portrait'] : null;
			$rep_sportrait = isset($repUserRs[$repRs_tmp_v['repuid']]['sportrait']) ? $repUserRs[$repRs_tmp_v['repuid']]['sportrait'] : null;
			$comment = unserialize($repRs_tmp_v['comment']);
			//获取作品主人信息
			$ownUserInfo = isset($ownUserRs[$repRs_tmp_v['ownid']]) ? $ownUserRs[$repRs_tmp_v['ownid']] : null;
			//获取作品信息
			$opusInfo = isset($opusRs[$repRs_tmp_v['opusid']]) ? $opusRs[$repRs_tmp_v['opusid']] : null;
			$repRs_tmp_v['uid'] = $repUserRs[$repRs_tmp_v['repuid']]['uid'];
			$repRs_tmp_v['nick'] = $repUserRs[$repRs_tmp_v['repuid']]['nick'];
			$repRs_tmp_v['gender'] = $repUserRs[$repRs_tmp_v['repuid']]['gender'];
			$repRs_tmp_v['grade'] = $repUserRs[$repRs_tmp_v['repuid']]['grade'];
			$repRs_tmp_v['authtype'] = $repUserRs[$repRs_tmp_v['repuid']]['authtype'];
			$repRs_tmp_v['sportrait'] = $rep_sportrait;
			$repRs_tmp_v['portrait'] = $rep_portrait;
			$repRs_tmp_v['teenager'] = $repUserRs[$repRs_tmp_v['repuid']]['teenager'];
			$repRs_tmp_v['isleague'] = $repUserRs[$repRs_tmp_v['repuid']]['isleague'];
			$repRs_tmp_v['comment'] = $comment;
			$repRs_tmp_v['ownUserINfo'] = $ownUserInfo;
			$repRs_tmp_v['opusInfo'] = $opusInfo;
			//两个状态
			$repRs_tmp_v['colStatus'] = in_array($repRs_tmp_v['opusid'],$collection_opus) ? 1 : 0;//是否收藏0没有1有
			$repRs_tmp_v['praStatus'] = in_array($repRs_tmp_v['opusid'],$praise_opus) ? 1 : 0;//是否赞0没有1有
		}	
		unset($repRs_tmp_v);
		//判断是否有下一页
		if($this->hasMore($rs,$count)) {
			array_pop($rs);
			if($pageIndex>=10){
				$rs['hasmore'] = 0;
			}else{
				$rs['hasmore'] = 1;
			}
		} else {
			$rs['hasmore'] = 0;
		}
		return $rs;
	}
	//私人定制中删除自己转发，或者自己的作品
	public function delPCOpus() {
		$info = $this->viaCookieLogin();
		if($info) {
			$uid = $info['id'];
			$customId = Input::get('customId');
			if(empty($customId)) return '找不到此定制项';
			$rs = DB::table('personalcustom')->where('id','=',$customId)->first(array('uid','repuid','opustype','opusid'));
			if(empty($rs)) return '此数据已删除';
			if(empty($rs['opusid'])) return '无法删除';
			//判断是自己的作品
			if($uid== $rs['uid'] && 0==$rs['opustype']) {
				$sql = "update personalcustom set isdel=1 where id = {$customId}";
				//自己的作品数-1
				$sql2 = "update user set opusnum = opusnum-1 where id = {$uid}";
				//删除作品
				$sql3 = "update opus set isdel = 1 where id = {$rs['opusid']}";
				
			} else if ($rs['repuid'] == $uid && 1 == $rs['opustype']) {
				$sql = "update personalcustom set isdel=1 where id = {$customId}";
				//作品转发数-1
				$sql2 = "update opus set repostnum = repostnum-1 where id = {$rs['opusid']}";
			} else {
				return '没有权限删除';
			}
			if(DB::update($sql)) {
				try {
					if(!empty($sql2)) {
						DB::update($sql2);
					}
					if(!empty($sql3)) {
						DB::update($sql3);
					}
				} catch (Exception $e) {
					return true;
				}
				return true;
			} else {
				return '删除失败';
			}
		} else {
			return 'nolog';
		}
	}
}
