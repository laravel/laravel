<?php 
/**
*	公共静态方法
*	@author:wang.hongli
*	@since:2014/11/8
**/
class ApiCommonStatic   {
	
	public static $url = '';
	public static $poem_url = '';
	
	public static function init(){
		self::$url = Config::get('app.url');
		self::$poem_url = Config::get('app.poem_url');
	}
	
	/**
	*	获取作品列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	*	params['count'] 每页显示的条数
	*	params['pageIndex'] 显示到第几页
	*	flag 0作品 1主播
	*	sid 榜单列表id
	*	type 动态榜单 1周 2月 3年
	*	uid 登陆用户id
	*/
	public static function getOpusList($uid,$params=array('count'=>20,'pageIndex'=>1),$flag=0,$type=1,$sid=0)
	{	
		self::init();
		$data = array();
		$offSet = ($params['pageIndex']-1)*$params['count'];
		$type = (int)$type;
		$flag = (int)$flag;
		$cache_minit = 30;
		if(empty($sid))
		{
			switch ($type) {
				case 1:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,lnum-preweeknum as totalNum,praisenum,repostnum,addtime,opustime,writer as writername from opus where isdel=0 order by totalNum desc,repostnum desc,praisenum desc limit {$offSet},{$params['count']}";
					break;
				case 2:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,lnum-premonthnum as totalNum,praisenum,repostnum,addtime,opustime,writer as writername  from opus where isdel = 0 order by totalNum desc,repostnum desc, praisenum desc limit {$offSet},{$params['count']}";
					break;
				case 3:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,lnum-preyearnum as totalNum,praisenum,repostnum,addtime,opustime,writer as writername from opus where isdel = 0 order by totalNum desc,repostnum desc, praisenum desc limit {$offSet},{$params['count']}";
					break;
			}

			$cache_key = 'api_billbord_opus_'.$flag.'_'.$type.'_'.$params['pageIndex'];
			if(Cache::has($cache_key))
			{
				$data = Cache::get($cache_key);
			}
			else
			{
				$data = DB::select($sql);
				Cache::put($cache_key,$data,$cache_minit);
			}
			if(empty($data))
			{
				return $data;
			}
			$tmp_uid = '';
			foreach($data as $k=>$v)
			{
				$tmp_uid .= $v['uid'].',';
			}
			$sql = "select id,nick,gender,grade,sportrait,authtype,teenager,isleague from user where id in (".trim($tmp_uid,',').")";
			$tmp_user_rs = DB::select($sql);
			if(empty($tmp_user_rs))
			{
				return $data;
			}
			//根据用户id，处理数组
			$last_user_rs = array();
			foreach($tmp_user_rs as $key=>$value)
			{
				$last_user_rs[$value['id']] = $value;
			}
			foreach($data as $k=>&$v)
			{
				if(isset($last_user_rs[$v['uid']]))
				{
					$v['nick'] = $last_user_rs[$v['uid']]['nick'];
					$v['gender'] = $last_user_rs[$v['uid']]['gender'];
					$v['grade'] = $last_user_rs[$v['uid']]['grade'];
					$v['sportrait'] = $last_user_rs[$v['uid']]['sportrait'];
					$v['authtype'] = $last_user_rs[$v['uid']]['authtype'];
					$v['teenager'] = $last_user_rs[$v['uid']]['teenager'];
					$v['isleague'] = $last_user_rs[$v['uid']]['isleague'];
				}
			}
		}
		else
		{
			$filePath = self::getBillBordUrl($sid,'billbord');
			if(file_exists($filePath))
			{
				$content = unserialize(file_get_contents($filePath));
				$data = array_slice($content, $offSet,$params['count']);
				if(!empty($data)){
					$tmp_uid = [];
					$user_info = [];
					foreach($data as $key=>$value){
						$tmp_uid[] = $value['uid'];
					}
					$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uid)->get(['id','nick','sportrait','authtype','isleague','teenager']);
					foreach($tmp_user_info as $k=>$v){
						$user_info[$v['id']] = $v;
					}
					foreach($data as $item=>&$items){
						$items['nick'] = isset($user_info[$items['uid']]['nick']) ? $user_info[$items['uid']]['nick'] : $items['nick'];
						$items['sportrait'] = isset($user_info[$items['uid']]['sportrait']) ? $user_info[$items['uid']]['sportrait'] : $items['sportrait'];
						$items['authtype'] = !empty($user_info[$items['uid']]['authtype']) ? intval($user_info[$items['uid']]['authtype']) : 0;
						$items['isleague'] = !empty($user_info[$items['uid']]['isleague']) ? intval($user_info[$items['uid']]['isleague']) : 0;
						$items['teenager'] = !empty($user_info[$items['uid']]['teenager']) ? intval($user_info[$items['uid']]['teenager']) : 0;
					}
				}
			}
		}
		//获取登录用户收藏的所有作品
		$collection_opus = ApiCommonStatic::isCollection_v2($uid);
		//获取登录用户赞过的所有作品
		$praise_opus = ApiCommonStatic::isPraise_v2($uid);
		//根据伴奏id获取伴奏信息
// 		$writeArr = ApiPoem::getWriterInfoById($data);
		foreach($data as $k=>$v)
		{
			$data[$k]['praStatus'] = !empty($praiseData[$uid]) ? 1 : 0;
			$data[$k]['colStatus'] = !empty($collData[$uid]) ? 1 : 0;
			//获取绝对地址
			$data[$k]['sportrait'] = !empty($v['sportrait']) ? self::$poem_url.ltrim($v['sportrait'],'.') : self::$poem_url.'/upload/portrait/male.png';

			$data[$k]['url'] = self::$poem_url.$v['url'];
			$data[$k]['lyricurl'] = self::$poem_url.$v['lyricurl'];
			$data[$k]['colStatus'] = isset($collection_opus[$v['id']]) ? 1  : 0; //1 收藏 0 没有收藏
			$data[$k]['praStatus'] = isset($praise_opus[$v['id']]) ? 1 : 0; // 1 赞过 0 没有赞过
			//收听数
			$data[$k]['lnum'] = isset($v['totalNum']) ? $v['totalNum'] : $v['praisenum'];
			//写者writername
// 			$data[$k]['writername'] = isset($writeArr[$v['poemid']]) ? $writeArr[$v['poemid']] : null;
		}
		//判断是否有下一页,特殊处理，每页100个
		$num = count($data);
		if($params['pageIndex']>=5){
			$data['hasmore'] = 0;
		}elseif($num>=$params['count'])
		{
			array_pop($data);
			$data['hasmore'] = 1;
		}
		else
		{
			$data['hasmore'] = 0;
		}
		return $data;
	}

	/**
	*	获取人的列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	*	params['count'] 每页显示的条数
	*	params['pageIndex'] 显示到第几页
	*	flag 0作品 1主播
	*	sid 榜单列表id
	*	uid 登陆用户id
	*	type 动态榜单 1周 2月 3年
	*/
	public static function getUserList($uid,$params=array('count'=>20,'pageIndex'=>1),$flag=1,$type=1,$sid=0)
	{	
		self::init();
		$data = array();
		$offSet = ($params['pageIndex']-1)*$params['count'];
		$type = (int)$type;
		$flag = (int)$flag;
		$cache_minit = 30;
		if(empty($sid))
		{	
			switch ($type) {
				case 1:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preweeknum as totalNum ,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic,isleague from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit {$offSet},{$params['count']}";
					break;
				case 2:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-premonthnum as totalNum,  fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic,isleague from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit {$offSet},{$params['count']}";
					break;
				case 3:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preyearnum as totalNum,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic,isleague from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit {$offSet},{$params['count']}";
					break;
			}
			$cache_key = 'api_billbord_user_'.$flag.'_'.$type.'_'.$params['pageIndex'];
			if(Cache::has($cache_key))
			{
				$data = Cache::get($cache_key);
			}
			else
			{
				$data = DB::select($sql);
				Cache::put($cache_key,$data,$cache_minit);
			}
		}
		else
		{	
			$filePath = self::getBillBordUrl($sid,'billbord');
			if(file_exists($filePath))
			{
				$content = unserialize(file_get_contents($filePath));
				$data = array_slice($content, $offSet,$params['count']);
				$tmp_id = [];
				$user_info = [];
				if(!empty($data)){
					foreach($data as $k=>$v){
						$tmp_id[] = $v['id'];
					}
					if(!empty($tmp_id)){
						$tmp_user_info = DB::table('user')->whereIn('id',$tmp_id)->get(['id','nick','sportrait','authtype','isleague','teenager']);
						foreach($tmp_user_info as $key=>$value){
							$user_info[$value['id']] = $value;
						}
						foreach ($data as $key => &$value) {
							$value['authtype'] = isset($user_info[$value['id']]['authtype']) ? $user_info[$value['id']]['authtype']:0;
							$value['isleague'] = isset($user_info[$value['id']]['isleague']) ? $user_info[$value['id']]['isleague']:0;
							$value['teenager'] = isset($user_info[$value['id']]['teenager']) ? $user_info[$value['id']]['teenager']:0;
						}
					}
				}
			}
		}
		if(!empty($data))
		{
			foreach($data as $key=>$value)
			{
				//判断是否关注
				$data[$key]['relation'] = self::attentionStatus($uid,$value['id']);
				//头像
				$data[$key]['sportrait'] = !empty($value['sportrait']) ? self::$poem_url.ltrim($value['sportrait'],'.') : null;
				$data[$key]['portrait'] = !empty($value['portrait']) ? self::$poem_url.ltrim($value['portrait'],'.') : null;
				//赞数
				$data[$key]['praisenum'] = isset($value['totalNum']) ? $value['totalNum'] : $value['praisenum'];
			}
		}
		//判断是否有下一页
		$num = count($data);
		//限制客户端只显示10页
		if($params['pageIndex']>=5){
			$data['hasmore'] = 0;
		}elseif($num>=$params['count'])
		{
			$data['hasmore'] = 1;
		}
		else
		{
			$data['hasmore'] = 0;
		}
		return $data;
		
	}

	/**
	*	根据sid获取静态榜单txt目录
	*	@author:wang.hongli
	*	@since:2014/11/08
	*	sid 静态列表id
	**/
	public static function getBillBordUrl($sid,$filepath='billbord')
	{
		$url = './upload/'.$filepath.'/'.$sid.'.txt';
		return $url;
	}
	/**
	*	优化后查看作品是否收藏
	*	@author:wang.hongli
	*	@since:2015/03/15
	**/
	public static function isCollection_v2($uid)
	{
		$data = array();
		if($uid)
		{
			$sql = "select opusid from collection where uid = {$uid}";
			if($rs = DB::select($sql))
			{
				foreach($rs as $k=>$v)
				{
					$data[] = $v['opusid'];
				}
			}
		}
		return $data;
	}

	/**
	*	优化后查看是否赞过某个作品
	*	@author:wang.hongli
	*	@since:2015/03/15
	**/
	public static function isPraise_v2($uid)
	{
		$redisOpusPraise = new RedisOpusPraise();
		$data = $redisOpusPraise->getUserPraise($uid);
		return $data;
	}

	/**
	*	获取关注状态
	*	@author:wang.hongli
	*	@since:2014/11/08
	*	$uid:登陆人的id
	*	@fid:对方id
	*/
	public static function attentionStatus($uid,$fid) {
		// $sql = "select relation from follow where uid={$uid} and fid = {$fid}";
		$rs = DB::table('follow')
							->where('uid','=',$uid)
							->where('fid','=',$fid)
							->first(array('relation'));
		if(!empty($rs)) {
			if($rs['relation'] == 3) {
				return 3;
			} else {
				return 1;//我关注别人
			}
		}
		$rs = DB::table('follow')
							->where('uid','=',$fid)
							->where('fid','=',$uid)
							->first(array('relation'));
		if(!empty($rs)) {
			if($rs['relation'] == 3) {
				return 3;
			} else {
				return 2;//别人关注我
			}
		}
		return 0; //陌生人
	}

	/**
	*	获取原创作者数组arrar('poemid'=>'writer')--放缓存里面要改
	*	@author:wang.hongli
	*	@since:2014/11/08
	**/
	public static function getPoemWriter()
	{
		$data = array();
		$sql = "select id,writername from poem";
		$rs = DB::select($sql);
		if(!empty($rs))
		{
			foreach($rs as $k=>$v)
			{
				$data[$v['id']] = $v['writername'];
			}
		}
		return $data;
	}

	//判断是否通过cookie登录
	public static function viaCookieLogin() {
		self::init();	
		$id = !empty($_COOKIE['id']) ? $_COOKIE['id'] : false;
		// $id = 35;
		if(!$id) {
			return false;
		} else {
			$tmpArr = explode('|', $id);
			if(empty($tmpArr)) return false;
			$id = $tmpArr[0];
			$token = $tmpArr[1];
			//从redis中取出用户信息
			$key = 'cookie_check:'.$id.':'.$token;
			$redisUserInfo = new RedisUserInfo();
			$info = $redisUserInfo->getCookieFromRedis($key);
			if(!empty($info)){
				return $info;
			}
			$info = DB::table('user')
						->where('id','=',$id)->where('token','=',$token)->where('isdel','=',0)
						->first(array('id','nick','email','phone',
							'gender','lnum','repostnum','attention',
							'praisenum','fans','opusnum','grade',
							'sportrait','portrait','bgpic','albums','signature',
							'authtype','addtime','accessToken','expirationDate','thPartid','pwd','isdel','isleague'));
			if(empty($info)) return false;
			if(!empty($info['pwd'])) {
				if(1 == $info['isdel']) return false; 
				$info['haspwd'] = 1;
			} else {
				$info['haspwd'] = 0;
			}
			unset($info['pwd']);
			
			$info['portrait'] = !empty($info['portrait']) ?  self::$poem_url.ltrim($info['portrait'],'.') : '';
			$info['sportrait'] = !empty($info['sportrait']) ? self::$poem_url.ltrim($info['sportrait'],'.') : '';
			$info['bgpic'] = !empty($info['bgpic']) ? self::$poem_url.ltrim($info['bgpic'],'.') : '' ;
			//标记用户为活跃用户
			// if(!empty($info['id'])){
			// 	$redisNotification = new RedisActiveUser();
			// 	$redisNotification->addActiveUser($info['id']);
			// }
			// 将用户信息存入redis
			$redisUserInfo->addCookieToRedis($id,$token,$info,1800);
			return $info;
		}
	}

	/**
	*	删除作品公共接口
	*	删除作品isdel=1, 作品数-1，最新作品名称更新，删除消息，删除参赛列表 删除文件  记录URL到删除文件表
	*	@author:wanghongli
	*	@since:2015/04/30
	*	@param:$uid 用户ID opusId 作品id
	**/
	public static function delOpus($uid=0,$opusId = 0)
	{
		if(empty($opusId) || empty($uid)) return false;
		try {
			//物理删除（修改）
			//得到url
			$opus=DB::table('opus')->where('id','=',$opusId)->first();			
 			 //判断文件是否存在
			$file= is_file(public_path(trim($opus['url'],'/')));
			if(!$file){
				return false;
			}
			if(!unlink(public_path(trim($opus['url'],'/')))){ return false;}

			//写入删除信息
			DB::table('opus_del_list')->insert(array('user_uid'=>$uid,"url"=>$opus['url'],"opusid"=>$opus['id'],'flag'=>0,'isdel'=>0,'deltime'=>time()));
			//物理删除（修改）
			//删除关系表信息
			$rs=DB::table('opus_param')->where('opusid',$opusId)->first();
			if($rs){
				DB::table('opus_param')->where('opusid',$opusId)->delete();				
			}
			DB::table('personalcustom')->where('opusid','=',$opusId)->delete();
			//查询作品收听数
			$opus_info = DB::table('opus')->where('id',$opusId)->first(array('lnum','praisenum','repostnum'));
			//修改用户总收听数，赞数，转发数
			DB::table('opus')->where('id',$opusId)->delete();
			//用户所有收听数  赞 转发数
			$num=DB::table("user")->where('id','=',$uid)->first(array('lnum','praisenum','repostnum','opusnum'));
			$lnum=($num['lnum']-$opus_info['lnum'])>0?$num['lnum']-$opus_info['lnum']:0;
			$praisenum=($num['praisenum']-$opus_info['praisenum'])>0?$num['praisenum']-$opus_info['praisenum']:0;
			$repostnum=($num['repostnum']-$opus_info['repostnum'])>0?$num['repostnum']-$opus_info['repostnum']:0;
			$update_arr1 = array($lnum,$praisenum,$repostnum,$uid);
				//个人作品数减一
			if($num['opusnum']){
				DB::table('user')->where('id','=',$uid)->decrement('opusnum');
			}
			//修改朗诵会会员中收听数
			$sql = "update league_user set lnum=?,praisenum=?,repostnum=? where uid=?";
			DB::update($sql,$update_arr1);
			//将人最新的作品选出来
			$newOpusName = DB::table('opus')->where('uid','=',$uid)->where('isdel','<>',1)->orderBy('id','desc')->take(1)->pluck('name');
			$opusName = !empty($newOpusName) ? trim($newOpusName) : '';
			DB::table('user')->where('id','=',$uid)->update(array('opusname'=>$opusName,'lnum'=>$lnum ,'praisenum'=>$praisenum ,"repostnum"=>$repostnum));
			DB::table('notice')->where('opusid','=',$opusId)->delete();
			DB::table('competition_opus_rel')->where('opusid','=',$opusId)->delete();
			//选出根据分类分表所在的表
			$table_ids = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists('table_id');
			if(!empty($table_ids)){
				foreach($table_ids as $k=>$v){
					$table_name = 'nav_opus_'.$v;
					DB::table($table_name)->where('opusid',$opusId)->delete();
				}
				DB::table('nav_opus_table_id')->where('opusid',$opusId)->delete();
			}
			//同步ES用户
			$apiEsSearch = App::make('apiEsSearch');
			$apiEsSearch->delEsOpus($opusId);
			return true;

		} catch (Exception $e) {
				return false;
		}
	}

}
 ?>