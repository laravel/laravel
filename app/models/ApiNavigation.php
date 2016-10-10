<?php 
/**
* 导航模型
**/
class ApiNavigation extends ApiCommon {

	//首页获取人（主播列表)
	public function getAnchor() {
		$info = $this->viaCookieLogin();
		if($info) $uid = $info['id'];
		$count = !empty(Input::has('count')) ? intval(Input::get('count')) : 20;
		$pageIndex = !empty(Input::has('pageIndex')) ? intval(Input::get('pageIndex')) : 1;
		$offSet = $count*($pageIndex - 1);
		$count++;
		$navigationId = intval(Input::get('id')); //导航id
		$conn = DB::table('user')->select(
						'id','nick','phone','gender','lnum','repostnum','attention','praisenum',
						'fans','opusnum','grade','sportrait','portrait','albums','signature','authtype',
						'opusname','isedit','issex','teenager','addtime','bgpic','isleague');
		
		if(17 == $navigationId || 18 == $navigationId || 22 == $navigationId) {
			$mWhere = null;
			switch ($navigationId) {
				case 17:
					$conn->where('gender',1);
					break;
				case 18:
					$conn->where('gender',0);
					break;
				case 22:
					$conn->where('teenager',1);
			}
			$conn->where('isdel','<>',1)->orderBy('praisenum','desc');
		} elseif(16==$navigationId) {
				//推荐主播
				$tmp_uid = DB::table('navanchorrel')->where('navid',$navigationId)->lists('uid');
				if(!empty($tmp_uid)){
					$conn->whereIn('id',$tmp_uid)->where('isdel','<>','1')->orderBy('recommenduser');
				}
		} elseif(20 == $navigationId) {
			//草根主播:男女主播+没有加V的用户
			$conn->where('teenager','<>',1)->where('authtype','<>',1)->where('isdel','<>',1)->orderBy('praisenum','desc')->orderBy('lnum','desc')->orderBy('repostnum','desc');
		} elseif(19 == $navigationId) {
			$conn->where('authtype',1)->where('isdel','<>',1)->where('authconent','<>','中华诵读联合会会员')->orderBy('praisenum','desc')->orderBy('lnum','desc')->orderBy('repostnum','desc');
		}else {
			$tmp_uid = DB::table('navanchorrel')->where('navid',$navigationId)->lists('uid');
			if(empty($tmp_uid)){
				return array();
			}
			$conn->whereIn('id',$tmp_uid)->where('isdel','<>',1)->orderBy('praisenum','desc')->orderBy('lnum','desc')->orderBy('repostnum','desc');
		}
		$rs = $conn->skip($offSet)->take($count)->get();
		$fids = array();
		if(!empty($rs)) {
			foreach($rs as $key=>&$value) {
				$value['portrait'] = !empty($value['portrait']) ?  $this->poem_url.ltrim($value['portrait'],'.') : '';
				$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
				$value['bgpic'] = !empty($value['bgpic']) ? $this->poem_url.ltrim($value['bgpic']) : '' ;
				$fids[] = $value['id'];
			}
			unset($value);
			//获取关注人的id
			$fids = self::myAttenUser($uid, $fids);
			foreach($rs as $k=>&$v){
				$v['relation'] = in_array($v['id'],$fids) ? 1 : 0;
			}
		}
		if($this->hasMore($rs,$count)) {
			array_pop($rs);
			$rs['hasmore'] = 1;
		} else {
			$rs['hasmore'] = 0;
		}
		return $rs;
	}
	/**
	 * 根据分类获取作品列表
	 * @author:wang.hongli
	 * @since:2016/05/17
	 */
	public function accordNavGetOpusList($navId=0,$count=20,$offSet=0){
		$info = $this->viaCookieLogin();
		$uid = 0;
		if(!empty($info)){
			$uid = $info['id'];
		}
		//是否由下一页标识
		$hasmore = 0;
		//设置缓存时间
		$cache_second = 300;
		$new_opus_second = 3;
		$prefix = 'api_opus_navigation_';
		$redisNavOpus = new RedisNavOpus();
		$rs = $redisNavOpus->getNavOpus($prefix,$navId,$offSet);
		if(!$rs){
			//新作抢先听
			if($navId == 52){
				if(!$rs){
					$rs = DB::table('opus')
						->select('id','uid','commentnum','poemid','name','url','lyricurl','type','firstchar','lnum','repostnum','praisenum','addtime','opustime','writer as writername')
						->where('isdel','<>',1)
						->orderBy('addtime','desc')
						->skip($offSet)->take($count)->get();
					if(!empty($rs)){
						if(count($rs) >= $count){
							$hasmore = 1;
						}
						$redisNavOpus->addNavOpus($prefix,$rs,$navId,$offSet,$new_opus_second);
					}
				}

			//推荐作品
			}elseif($navId==73){
				$rs = DB::table('opus')
				->select('id','uid','commentnum','poemid','name','url','lyricurl','type','firstchar','lnum','repostnum','praisenum','addtime','opustime','writer as writername')
				->where('recommendopus','<',9999)->where('isdel','<>',1)
				->orderBy('recommendopus','desc')
				->skip($offSet)->take($count)->get();
				if(!empty($rs)){
					if(count($rs) >= $count){
						$hasmore = 1;
					}
					$redisNavOpus->addNavOpus($prefix,$rs,$navId,$offSet,$cache_second);
				}
			}else{
				$table = $navId%10;
				$table_name = 'nav_opus_'.$table;
				$opus_id = DB::table($table_name)->where('categoryid',$navId)->orderBy('lnum','desc')->orderBy('repostnum','desc')->orderBy('praisenum','desc')->skip($offSet)->take($count)->lists('opusid');
				if(count($opus_id) >= $count){
					$hasmore = 1;
				}
				$search_opus_ids = implode(',', $opus_id);
				$rs = DB::table('opus')
						->select('id','uid','commentnum','poemid','name','url','lyricurl','type','firstchar','lnum','repostnum','praisenum','addtime','opustime','writer as writername')
						->where('isdel','<>',1)->whereIn('id',$opus_id)->orderByRaw(DB::raw("FIELD(id,$search_opus_ids)"))->get();
				if(!empty($rs)){
					$redisNavOpus->addNavOpus($prefix,$rs,$navId,$offSet,$cache_second);
				}
			}
		}else{
			$hasmore = 1;
		}
		//用户id，获取用户信息
		$tmp_user_id = array();
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$tmp_user_id[] = $v['uid'];
			}
		}
		$user_last = array();
		$users = DB::table('user')->select('id','nick','gender','grade','sportrait','authtype','teenager','isleague')->whereIn('id',$tmp_user_id)->get();
		if(!empty($users)){
			foreach($users as $key=>&$value){
				$user_last[$value['id']] = $value;
			}
		}
		//获取登录用户收藏的所有作品
		$collection_opus = ApiCommonStatic::isCollection_v2($uid);
		//获取登录用户赞过的所有作品
		$praise_opus = ApiCommonStatic::isPraise_v2($uid);
		
		foreach($rs as $k=>&$v){
			if(isset($user_last[$v['uid']]))
			{
				$tmp_user_info = $user_last[$v['uid']];
				$v['nick'] = $tmp_user_info['nick'];
				$v['gender'] = $tmp_user_info['gender'];
				$v['grade'] = $tmp_user_info['grade'];
				$v['sportrait'] = !empty($tmp_user_info['sportrait']) ? $this->poem_url.ltrim($tmp_user_info['sportrait'],'.') : null;
				$v['authtype'] = $tmp_user_info['authtype'];
				$v['teenager'] = $tmp_user_info['teenager'];
				$v['isleague'] = $tmp_user_info['isleague'];
			}
			$v['url'] = $this->poem_url.$v['url'];
			$v['lyricurl'] = $this->poem_url.$v['lyricurl'];
			
			$v['colStatus'] = in_array($v['id'],$collection_opus) ? 1 : 0;//是否收藏0没有1有
			$v['praStatus'] = in_array($v['id'],$praise_opus) ? 1 : 0;//是否赞0没有1有
			$v['writername'] = !empty($v['writername']) ? $v['writername'] : '佚名';
		}
		if($hasmore == 1){
			array_pop($rs);
		}
		$rs['hasmore'] = $hasmore;
		return $rs;
	}
	//精品推荐接口
	public function recommendation() {
		$flag = Input::get('flag'); //0苹果1android
		$count = !empty(Input::has('count')) ? Input::get('count') : 20;
		$pageIndex = !empty(Input::has('pageIndex')) ? Input::get('pageIndex') : 1;
		$offSet = $count*($pageIndex - 1);
		$count++;
		if(empty($flag)) {
			$sql = "select * from recommendation where platform != 1 and isdel != 1 order by sort";
		} else {
			$sql = "select * from recommendation where platform != 0 and isdel != 1 order by sort";
		}
		$tmpRs = DB::select($sql);
		if(empty($tmpRs)) return 'nodata';
		foreach($tmpRs as $key=>&$value) {
			if(!empty($value['url'])) {
				$value['sicon'] = $this->poem_url.$value['sicon'];
			}
		}
		//判断是否有下一页
		if($this->hasMore($tmpRs,$count)) {
			array_pop($tmpRs);
			$tmpRs['hasmore'] = 1;
		} else {
			$tmpRs['hasmore'] = 0;
		}
		return $tmpRs;
	}
}
