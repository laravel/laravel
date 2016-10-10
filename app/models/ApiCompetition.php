<?php 
/**
*诗友会模型
*@author:wang.hongli
*@since:2014/10/26
**/
class ApiCompetition extends ApiCommon
{
	/**
	*	获取诗友会分类
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getCompCategory()
	{
		$info = $this->viaCookieLogin();
		if(empty($info)) 
		{
			return 'nolog';
		}
		$type_id=!empty(Input::get('type'))?Input::get('type') : 1;//默认朗诵会
		$data = array();
		$sql = "select * from competition where type_id='{$type_id}' order by sort";
		$rs = DB::select($sql);
		if(!empty($rs))
		{
			$data = $rs;
		}
		return $data;
	}
	
	/**
	* 	读诗诗友会子分类
	*	@author:wang.hongli
	*	@author:2014/10/26
	**/
	public function getSubComCategory($pid=0,$params = array('offSet'=>0,'count'=>20),$keywords='',$id=0)
	{	
		$info = $this->viaCookieLogin();
		if(empty($info)) 
		{
			return 'nolog';
		}
		$data = array('hasmore'=>0);
		$where = '';
		if(!empty(Input::get('flag'))) //1 过滤掉结束的
		{
			$where = ' and isfinish != 1';
		}

		if(!empty($pid) || !empty($keywords))
		{	
			$sql = "select * from competitionlist  where pid = {$pid} {$where} order by isfinish asc,sort desc,starttime desc limit {$params['offSet']},{$params['count']}";
			if(!empty($keywords))
			{
				$sql = "select * from competitionlist  where pid in(1,2,3) and name like '%{$keywords}%' {$where} order by isfinish asc,starttime,starttime desc limit {$params['offSet']},{$params['count']}";
			}
		}
		elseif(!empty($id))
		{
			$sql = "select * from competitionlist  where id = '".$id."' {$where} limit {$params['offSet']},{$params['count']}";
		}
		$rs = DB::select($sql);
		if(!empty($rs))
		{	
			$num = count($rs);
			foreach($rs as $key=>&$value)
			{
				//主图
				$value['mainpic'] = !empty($value['mainpic']) ? $this->poem_url.ltrim($value['mainpic'],'.') : '';
				//轮播图
				if(!empty($value['piclist']))
				{
					$tmpPicList = array();
					$tmpPicList = explode(';', $value['piclist']);
					unset($value['piclist']);
					foreach($tmpPicList as $k=>$v)
					{
						$value['piclist'][] = !empty($v) ? $this->poem_url.ltrim($v,'.') : '';
					}
				}
			}
			unset($value);
			if($num >= $params['count'])
			{
				array_pop($rs);
				$rs['hasmore'] = 1;
			}
			else
			{
				$rs['hasmore'] = 0;
			}
			$data = $rs;
		}
		return $data;
	}

	/**
	* 	读诗诗友会子分类作品列表
	*	@author:wang.hongli
	*	@author:2014/10/26
	**/
	public function getSubCatOpusList($id=0,$params = array('pageIndex'=>1,'offSet'=>0,'count'=>20),$isfinish=0)
	{
		$info = $this->viaCookieLogin();
		$uid = $info['id'];
		$flag = !empty(Input::get('flag')) ? Input::get('flag') : 0;
		if(empty($info)) 
		{
			return 'nolog';
		}
		$data = array();
		if(empty($id))
		{
			return $data;
		}
		$cache_minit = 30;
		//没有结束,动态获取
		if(empty($isfinish))
		{	
			if(!empty($flag))
			{
				$cache_key = 'api_competition_'.$id.'_'.$flag.'_'.$params['pageIndex'];
				if(Cache::has($cache_key))
				{
					$rs = Cache::get($cache_key);
				}
				else
				{
					$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.lnum,opus.premonthnum,opus.repostnum,opus.praisenum,opus.addtime,opus.opustime,opus.writer as writername, user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.teenager,user.isleague from competition_opus_rel left join opus on competition_opus_rel.opusid = opus.id left join user on user.id = opus.uid where competition_opus_rel.competitionid = {$id} and opus.isdel = 0 and user.isdel != 1 order by lnum desc,opus.repostnum desc, opus.praisenum desc limit {$params['offSet']},{$params['count']}";
					$rs = DB::select($sql);
					Cache::put($cache_key,$rs,$cache_minit);
					
				}
			}
			else
			{
				$cache_key = 'api_competition_'.$id.'_'.$params['pageIndex'];
				if(Cache::has($cache_key))
				{
					$rs = Cache::get($cache_key);
				}
				$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.lnum,opus.premonthnum,opus.lnum-opus.premonthnum as lnum,opus.repostnum,opus.praisenum,opus.addtime,opus.opustime,opus.writer as writername,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.teenager,user.isleague from competition_opus_rel left join opus on competition_opus_rel.opusid = opus.id left join user on user.id = opus.uid where competition_opus_rel.competitionid = {$id} and opus.isdel = 0 and user.isdel != 1 order by lnum desc,opus.repostnum desc, opus.praisenum desc limit {$params['offSet']},{$params['count']}";
				$rs = DB::select($sql);
				Cache::put($cache_key,$rs,$cache_minit);
			}
		}
		else
		{
			$tmpRs = DB::table('staticcomplog')->where('complistid','=',$id)->orderBy('addtime','desc')->first();
			if(!empty($tmpRs))
			{
				$rs = array_slice(unserialize($tmpRs['content']),$params['offSet'],$params['count']);
				$tmp_uids = [];
				$user_info = [];
				if(!empty($rs)){
					foreach($rs as $k=>$v){
						$tmp_uids[] = $v['uid'];
					}
					if(!empty($tmp_uids)){
						$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uids)->get(['id','nick','sportrait','authtype','isleague','teenager']);
						foreach($tmp_user_info as $key=>$value){
							$user_info[$value['id']] = $value;
						}
					}
					foreach($rs as $k=>&$v){
						$v['nick'] = isset($user_info[$v['uid']]['nick']) ? $user_info[$v['uid']]['nick'] : $v['nick'];
						$v['sportrait'] = isset($user_info[$v['uid']]['sportrait']) ? $user_info[$v['uid']]['sportrait'] : $v['sportrait'];
						$v['authtype'] = !empty($user_info[$v['uid']]['authtype']) ? intval($user_info[$v['uid']]['authtype']) : 0;
						$v['isleague'] = !empty($user_info[$v['uid']]['isleague']) ? intval($user_info[$v['uid']]['isleague']) : 0;
						$v['teenager'] = !empty($user_info[$v['uid']]['teenager']) ? intval($user_info[$v['uid']]['teenager']) : 0;
					}
				}
			}
			else
			{
				return $data['hasmore'] = 0;
			}
		}
		if(!empty($rs))
		{
			//获取登录用户收藏的所有作品
			$collection_opus = ApiCommonStatic::isCollection_v2($uid);
			//获取登录用户赞过的所有作品
			$praise_opus = ApiCommonStatic::isPraise_v2($uid);
			//根据伴奏id获取伴奏信息
// 			$writeArr = ApiPoem::getWriterInfoById($rs);
			foreach($rs as $key=>&$value)
			{
				$tmp_arr = array();
				$value['url'] = $this->poem_url.$value['url'];
				$value['lyricurl'] = $this->poem_url.$value['lyricurl'];
				$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
				$value['authtype'] = !empty($value['authtype']) ? intval($value['authtype']) : 0;
				$value['isleague'] = !empty($value['isleague']) ? intval($value['isleague']) : 0;
				$value['teenager'] = !empty($value['teenager']) ? intval($value['teenager']) : 0;
				$value['colStatus'] = in_array($value['id'],$collection_opus) ? 1 : 0;//是否收藏0没有1有
				$value['praStatus'] = in_array($value['id'],$praise_opus) ? 1 : 0;//是否赞0没有1有
				$value['writername'] = isset($value['writername']) ? $value['writername'] : null;
			}
			unset($value);
			if(count($rs) >= $params['count'])
			{
				array_pop($rs);
				$rs['hasmore'] = 1;
			}
			else
			{
				$rs['hasmore'] = 0;
			}
			$data = $rs;
		}
		else{
			$data['hasmore'] = 0;
		}
		return $data;
	}

	/**
	*	诗友会获取某时间榜单列表
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getStaticCompLog($complistid)
	{
		$info = $this->viaCookieLogin();
		if(empty($info)) 
		{
			return 'nolog';
		}
		$data = array();
		if(empty($complistid))
		{
			return $data;
		}
		$sql = "select id,complistid,addtime from staticcomplog where complistid = {$complistid} order by addtime desc";
		$rs = DB::select($sql);
		if(!empty($rs))
		{
			$data = $rs;
		}
		return $data;
	}

	/**
	*	诗友会获取特定静态列表
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getStaticSubCatOpusList($id=0,$params = array('offSet'=>0,'count'=>20))
	{
		$info = $this->viaCookieLogin();
		if(empty($info)) 
		{
			return 'nolog';
		}
		$data = array();
		if(empty($id))
		{
			$data['hasmore'] = 0;
			return $data;
		}
		$rs = DB::table('staticcomplog')->where('id','=',$id)->first();
		if(!empty($rs))
		{	
			$rs = array_slice(unserialize($rs['content']),$params['offSet'],$params['count']);
			if(!empty($rs))
			{
				foreach($rs as $key=>&$value)
				{
					$value['url'] = $this->poem_url.$value['url'];
					$value['lyricurl'] = $this->poem_url.$value['lyricurl'];
					$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
					$value['authtype'] = !empty($value['authtype']) ? intval($value['authtype']) : 0;
					$value['isleague'] = !empty($value['isleague']) ? intval($value['isleague']) : 0;
					$value['teenager'] = !empty($value['teenager']) ? intval($value['teenager']) : 0;

				}

				if(count($rs) >= $params['count'])
				{
					array_pop($rs);
					$rs['hasmore'] = 1;
				}
				else
				{
					$rs['hasmore'] = 0;
				}
				$data = $rs;
			}
			else
			{
				$data['hasmore'] = 0;
			}	
			return $data;
		}
		else
		{
			return $data['hasmore'] = 0;
		}
	}
	/**
	 * 根据比赛id获取比赛详情 version2
	 * @author :wang.hongli <[<email address>]>
	 * @since :2016/07/12 [<description>]
	 */
	public function getCompDetailV2($id=0){
		if(empty($id)){
			return 'error';
		}
		$rs = DB::table('competitionlist')
				->where('id','=',$id)
				->get(array('id','name','name_short','mainpic','piclist','pid','sort','starttime','endtime','haslist','isfinish','monthflag','clause_title','description','has_invitation'));
		if(empty($rs)){
			return [];
		}
		$tmp_rs = $rs[0];
		$attach_info = ['id'=>0,'price'=>0,'name'=>''];
		if($tmp_rs['isfinish'] == 1 && in_array($tmp_rs['pid'], [4,5,6,7])){
			$goods_id = DB::table('goods')->where('competition_id','=',$rs[0]['id'])->where('flag',2)->pluck('id');
			if(!empty($goods_id)){
				//get attach_info
				$attach_info = DB::table('goods')->where('good_pid',$goods_id)->where('flag',2)->first();
			}
		}else{
			$goods_id = DB::table('goods')->where('competition_id','=',$rs[0]['id'])->where('flag','<>',2)->pluck('id');
		}
		foreach($rs as $k=>$v)
		{
			$tmp_arr = explode(';', $v['piclist']);
			$str = array();
			if(!empty($tmp_arr))
			{
				foreach($tmp_arr as $key=>$value)
				{
					$str[] = $this->poem_url.$value;
				}
			}
			$rs[$k]['piclist'] = $str;
			$rs[$k]['mainpic'] = $this->poem_url.$v['mainpic'];
		}
		$rs[0]['attach_id'] = !empty($attach_info['id']) ? $attach_info['id'] : 0;
		$rs[0]['attach_price'] = !empty($attach_info['price']) ? $attach_info['price']:0;
		$rs[0]['attach_name'] = !empty($attach_info['name']) ? $attach_info['name']: '';

		if(!empty($goods_id))
		{
			$rs[0]['goods_id'] = $goods_id;
		}
		if(!empty($rs[0]['clause_title']))
		{
			$rs[0]['clause_url'] = $this->url.'/admin/getMatchClause/'.$rs[0]['id'];
		}
		$rs[0]['type_id']  = 0;
		//获取比赛类型
		if(!empty($rs[0]['pid'])){
			$rs[0]['type_id'] = DB::table('competition')->where('id',$rs[0]['pid'])->pluck('type_id');
		}
		return $rs;
	}
	/**
	*	根据比赛id获取比赛详情
	*	@author:wang.hongli
	*	@since:2015/02/01
	*/
	public function getCompDetail($id)
	{
		if(empty($id)) return 'error';
		$rs = DB::table('competitionlist')
				->where('id','=',$id)
				->get(array('id','name','name_short','mainpic','piclist','pid','sort','starttime','endtime','haslist','isfinish','monthflag','clause_title','description','has_invitation'));

		$rs = !empty($rs) ? $rs : array();
		if(!empty($rs))
		{
			
			//根据比赛id获取商品id
			$goods_id = DB::table('goods')->where('competition_id','=',$rs[0]['id'])->first(array('id'));
			foreach($rs as $k=>$v)
			{
				$tmp_arr = explode(';', $v['piclist']);
				$str = array();
				if(!empty($tmp_arr))
				{
					foreach($tmp_arr as $key=>$value)
					{
						$str[] = $this->poem_url.$value;
					}
				}
				$rs[$k]['piclist'] = $str;
				$rs[$k]['mainpic'] = $this->poem_url.$v['mainpic'];
			}
			if(!empty($goods_id['id']))
			{
				$rs[0]['goods_id'] = $goods_id['id'];
			}
			if(!empty($rs[0]['clause_title']))
			{
				$rs[0]['clause_url'] = $this->url.'/admin/getMatchClause/'.$rs[0]['id'];
			}
			$rs[0]['type_id']  = 0;
			//获取比赛类型
			if(!empty($rs[0]['pid'])){
				$rs[0]['type_id'] = DB::table('competition')->where('id',$rs[0]['pid'])->pluck('type_id');
			}
		}
		return $rs;
	}
	/*
	* 获取赛事列表
	*/
	public function getMatchList($where,$page=1,$page_size=20,$flag=0){
		$data = array();
		$where_str = "";
		if(isset($where['pid'])){
			$where_str.=" and pid='".$where['pid']."'";
		}
		if(isset($where['type_id'])){
			switch($where['type_id']){
				case 2:
					$where_str.=" and pid in (4,5)";
					break;
				case 3:
					$where_str.=" and pid in (6,7)";
					break;
			}
		}
		if(isset($where['keywords'])){
			$where_str.=" and name like '%".$where['keywords']."%'";
		}
		$sql = "select count(*) as num from competitionlist where 1 ".$where_str;
		$rlt = DB::select($sql);
		$total = $rlt[0]['num'];
		$total_page=ceil($total/$page_size);
		if($total>0){
			//查询对应的商品id
			$goods = array();
			$rlt = DB::table('goods')->where('competition_id','>',0)->get();
			foreach($rlt as $v){
				$goods[$v['competition_id']]=$v['id'];
			}
			//选出比赛所属类型 type_id:1,2,3
			$tmp_type_ids = DB::table('competition')->select('id','type_id')->get();
			$type_ids = array();
			foreach($tmp_type_ids as $k=>$v){
				$type_ids[$v['id']] = $v['type_id'];
			}
			//查询列表
			$sql = "(select id,name,name_short,mainpic,piclist,pid,sort,starttime,endtime,haslist,isfinish,monthflag,clause_title,description,has_invitation,pid from competitionlist 
					where endtime>='".time()."' ".$where_str." )
					UNION All
					(select id,name,name_short,mainpic,piclist,pid,sort,starttime,endtime,haslist,isfinish,monthflag,clause_title,description,has_invitation,pid from competitionlist where endtime<'".time()."' ".$where_str." ) order by isfinish asc,sort desc,starttime desc";
			$data = DB::select($sql);
			
			foreach($data as $key=>$value){
				//主图
				$data[$key]['mainpic'] = !empty($value['mainpic']) ? $this->poem_url.ltrim($value['mainpic'],'.') : '';
				//轮播图
				if(!empty($value['piclist'])){
					$tmp = explode(';', $value['piclist']);
					unset($data[$key]['piclist']);
					foreach($tmp as $k=>$v){
						$data[$key]['piclist'][] = !empty($v) ? $this->poem_url.ltrim($v,'.') : '';
					}
				}
				//商品id
				$data[$key]['goods_id'] = isset($goods[$value['id']]) ? $goods[$value['id']] : 0; 
				if(!empty($value['clause_title']))
				{
					$data[$key]['clause_url'] = $this->url."/admin/getMatchClause/".$value['id']; 
				}
				//比赛类型
				$data[$key]['type_id'] = isset($type_ids[$value['pid']]) ? $type_ids[$value['pid']] : 0;
			}
		}
		$data['hasmore'] = $total_page > $page ? 1 : 0;
		//补充主播大赛和作品大赛
		if(!empty($flag))
		{
			$tmp = array();
		}
		else
		{
			$tmp = array(
				array(
					"id"=>100000,
					"name"=>"主播大赛",
					"mainpic"=>"http://weinidushi.com.cn/img/zhubodasai.png",
					"pid"=>5,
					"sort"=>1,
					"starttime"=>1427939999,
					"endtime"=>1437407999,
					"haslist"=>1,
					"isfinish"=>0,
					"monthflag"=>0,
					"piclist"=>array(),
					"goods_id"=>0,
					"clause_url"=>''
				),
				array(
					"id"=>100001,
					"name"=>"作品大赛",
					"mainpic"=>"http://weinidushi.com.cn/img/zuopindasai.png",
					"pid"=>5,
					"sort"=>1,
					"starttime"=>1427939999,
					"endtime"=>1437407999,
					"haslist"=>1,
					"isfinish"=>0,
					"monthflag"=>0,
					"piclist"=>array(),
					"goods_id"=>0,
					"clause_url"=>''
				),
			);
		}
		
		if(isset($where['pid']) && $where['pid']==5 && $page==1){
			$data = array_merge($tmp, $data);
		}
		
		return $data;
	}
	
	/*
	* 赛事报名表单提交
	*/
	public function updateMatchUser($arr){
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'msg'=>'请登录');
		$uid = $info['id'];
		$data = DB::table('user_match')->where('uid','=',$uid)->first();
		$id=0;
		if(!empty($data)){
			//存在就更新
			unset($arr['addtime']);
			unset($arr['status']);
			$id = $data["id"];
			$this->upMatchInfo($data['uid'],$arr);
		}else{
			//不存在就添加
			$arr['uid'] = $info['id'];
			$arr['nick_name'] = $info['nick'];
			$arr['gender'] = $info['gender'];
			$id = $this->addMatchInfo($arr);
		}
		
		if($id>0){
			return array('code'=>1,'id'=>$id,'msg'=>'提交成功');
		}else{
			return array('code'=>0,'msg'=>'操作失败');
		}
		
	}
	/*
	* 赛事报名表添加数据
	*/
	public function addMatchInfo($arr){
		$id = DB::table('user_match')->insertGetId($arr);
		return (int)$id;
	}
	
	/*
	* 赛事报名表更新数据
	*/
	public function upMatchInfo($uid,$arr){
		if(!is_array($arr) || empty($arr) || empty($uid)){
			return 0;
		}
		
		$tmp=array();
		foreach($arr as $k=>$v){
			$tmp[]=$k."='".$v."'";
		}
		
		$sql = "update user_match set ".implode(",",$tmp)." where uid = '".$uid."'";
		if(DB::update($sql)) {
			return $uid;
		}else{
			return 0;
		}
	}
	/*
	* 按用户uid获取赛事报名
	*/
	public function getMatchUser()
	{
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'请登录');
		
		$uid = $info['id'];
		$info = DB::table('user_match')
				->where('uid','=',$uid)
				->first(array('id','uid','card','name','nick_name','company','address','zip','mobile','email','cause','province_id','city_id','area_id'));
		if($info) 
		{
			return array('code'=>1,'data'=>$info);
		}
		return array('code'=>-1,'msg'=>'操作失败');
	}

	/**
	*	根据比赛id获取比赛分赛区实施方案
	*	@author:wang.hongli
	*	@since:2015/07/19
	*/
	public function getMatchClause($competitionid = 0)
	{
		if(empty($competitionid))
		{
			return false;
		}
		$rs = DB::table('competitionlist')
				->where('id','=',$competitionid)
				->first(array('id','clause_title','clause'));
		if(empty($rs))
			return false;
		return $rs;

	}
	
}
 ?>