<?php 
/**
 * 诗文比赛model
 * @author:wang.hongli
 * @since:2016/04/27
 */
class ApiPoetry extends ApiCommon{
	
	private $poetry_lyric_url = '';
	public function __construct(){
		parent::__construct();
		$this->poetry_lyric_url = 'upload/opusperylyric/';
	}
	
	/**
	 * 添加诗文作品
	 * @param unknown $data -- 表单提交的数据
	 * @since:2016/04/27
	 * @author:wang.hongli
	 */
	public function addOpusPoetry($data){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = intval($info['id']);
		$rules = array(
				'title'=>'required',
				'author'=>'required',
				'nationality'=>'required|alpha',
				'content'=>'required',
				'competitionid'=>'required|integer',
		);
		$message = array(
				'title.required'=>'请填写标题',
				'author.required'=>'请填写作者名称',
				'nationality.required'=>'国籍不能为空',
				'nationality.alpha'=>'国籍必须为字符串',
				'content.required'=>'诗文内容不能为空',
				'competitionid.required'=>'比赛id不能为空',
				'competitionid.integer'=>'比赛id必须为数字'
		);
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			$m  = $validator->messages();
			$msg = $m->all();
			return $msg[0];
		}
		$check_str = $data['content'].$data['title'].$data['author'].$data['nationality'];
		if(my_sens_word($check_str))
		{
			return '内容含有禁用词';
		}
		$competitionid = $data['competitionid'];
		$apiCheckPermission = new ApiCheckPermission();
		$tmp_rs = $apiCheckPermission->check_permission($competitionid);
		$status = $tmp_rs['status'];
		$message = $tmp_rs['message'];
		if($status != 1){
			return $message;
		}
		$time = time();
		$data['uid'] = $uid;
		$data['status'] = 2;
		$data['add_time'] = $data['update_time'] = $time;
		$data['click_num'] = 1;
		if(!empty($data['_token'])){
			unset($data['_token']);
		}
		try {
			$content = htmlspecialchars($data['content']);
			unset($data['content']);
			//对内容进行替换,\r\n -> \n,\r->\n
			$search = array("\r\n","\r");
			$replace = "\n";
			$content = str_replace($search, $replace, $content);
			$data['lyric'] = $content;
			$filename = uniqid().'.lrc';
			$lyricDir = $this->isExistDir ( 'opusperylyric' );
			$data['lyric'] = $this->poetry_lyric_url.$filename;
			file_put_contents($data['lyric'], $content);
			$id = DB::table('opus_poetry')->insertGetId($data);
			$data['lyric'] = $this->poem_url.'/'.$data['lyric'];
			$data['id'] = $id;
		} catch (Exception $e) {
			return '添加失败，请重试';
		}
		return $data;
	}
	
	/**
	 * 删除诗文比赛作品
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function delOpusPoetry($id){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		if(empty($id)){
			return '删除作品失败';
		}
		$uid = $info['id'];
		try {
			$db_uid = DB::table('opus_poetry')->where('id',$id)->pluck('uid');
			if($uid == $db_uid){
				DB::table('opus_poetry')->where('id',$id)->update(array('status'=>0));
				return true;
			}else{
				return '没有权限删除此作品';
			}
		} catch (Exception $e) {
			return '删除作品失败';
		}
	}
	/**
	 * 获取诗文比赛作品列表
	 * @author:wang.hongli
	 * @since:2016/04/27
	 * @param:$competitionId 比赛id $offSet 偏移量  $count 获取总数
	 */
	public function getOpusPoetryList($competitionId,$offSet,$count){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$competitionId = intval($competitionId);
		$uid = $info['id'];
		
		$rs = DB::table('opus_poetry')->where('status',2)->where('competitionid',$competitionId)->orderBy('click_num','desc')->orderBy('praise_num','desc')->orderBy('repost_num','desc')->skip($offSet)->take($count)->get();
		$uids = array();
		if(!empty($rs)){
			//获取个人赞过的作品
			$redisOpusPoetry = new RedisOpusPoetry();
			$opus_poetry_ids = $redisOpusPoetry->getUserPraisePoetry($uid);
			foreach($rs as $k=>&$v){
				$uids[] = $v['uid'];
				$v['ispraise'] = 0;
				if(!empty($opus_poetry_ids) && in_array($v['id'], $opus_poetry_ids)){
					$v['ispraise'] = 1;
				}
				$v['lyric'] = $this->poem_url.'/'.$v['lyric'];
			}
			//获取用户信息
			$tmp_user_rs = DB::table('user')->select('id','nick','gender','grade','sportrait','portrait','authtype','teenager','isleague')->whereIn('id',$uids)->get();
			$user_rs = array();
			if(!empty($tmp_user_rs)){
				foreach($tmp_user_rs as $key=>$value){
					$value['portrait'] = $this->poem_url.ltrim($value['portrait'],'.');
					$value['sportrait'] = $this->poem_url.ltrim($value['sportrait'],'.');
					$user_rs[$value['id']] = $value;
				}
			}
			//组合数组
			if(!empty($rs)){
				foreach($rs as $k=>&$v){
					$tmp_user = $user_rs[$v['uid']];
					$v['nick'] = $tmp_user['nick'];
					$v['gender'] = $tmp_user['gender'];
					$v['grade'] = $tmp_user['grade'];
					$v['sportrait'] = $tmp_user['sportrait'];
					$v['portrait'] = $tmp_user['portrait'];
					$v['authtype'] = $tmp_user['authtype'];
					$v['teenager'] = $tmp_user['teenager'];
					$v['isleague'] = $tmp_user['isleague'];
				}
			}
		}
		//判断是否还有下一页
		if($this->hasMore($rs,$count)) {
			array_pop($rs);
			$rs['hasmore'] = 1;
		} else {
			$rs['hasmore'] = 0;
		}
		return $rs;
	}
	
	/**
	 * 诗文比赛--查看作品
	 * @author:wang.hongli
	 * @since:2016/04/27
	 * @param:$id  作品id
	 */
	public function viewOpusPoetry($id){
		if(empty($id)){
			return;
		}
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = $info['id'];
		$redisOpusPoetry = new RedisOpusPoetry();
		if($redisOpusPoetry->viewOpusPoetry($uid, $id)){
			try {
				DB::table('opus_poetry')->where('id',$id)->increment('click_num');
			} catch (Exception $e) {
			}
		}
		//界面详情
		$rs = DB::table('opus_poetry')->where('id',$id)->first(array('id','competitionid','title','uid','author','nationality','lyric','click_num','comment_num','add_time','repost_num','praise_num'));
		if(empty($rs)){
			return;
		}
		$ownid = $rs['uid'];
		$user = DB::table('user')->where('id',$ownid)->first(array('id','gender','sportrait','portrait','authtype','grade','teenager','isleague'));
		if(empty($user)){
			return;
		}
		$rs['lyric'] = $lyric_file = $this->poem_url.'/'.$rs['lyric'];
		$flag = @file_get_contents($lyric_file);
		$rs['content'] = '';
		if(!empty($flag)){
			$rs['content'] = $flag;
		}
		$rs['uid'] = $user['id'];
		$rs['gender'] = $user['gender'];
		$rs['sportrait'] = $this->poem_url.ltrim($user['sportrait'],'.');
		$rs['portrait'] = $this->poem_url.ltrim($user['portrait'],'.');
		$rs['authtype'] = $user['authtype'];
		$rs['grade'] = $user['grade'];
		$rs['teenager'] = $user['teenager'];
		$rs['isleague'] = $user['isleague'];
		return $rs;
	}
	
	/**
	 * 诗文比赛作品转发数
	 * @author:wang.hongli
	 * @since:2016/04/27
	 * 
	 */
	public function repostOpusPoetry($id){
		if(empty($id)){
			return;
		}
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return;
		}
		$uid = $info['id'];
		try {
			DB::table('opus_poetry')->where('id',$id)->increment('repost_num');
		} catch (Exception $e) {
		}
		return;
	}
	
	/**
	 * 诗文比赛作品赞数增加
	 * @author:wang.hongli
	 * @since:2016/04/27
	 * @param:flag 1 赞 2取消赞
	 */
	public function praiseOpusPoetry($id,$flag = 1){
		if(empty($id)) return;
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return;
		$uid = $info['id'];
		$redisOpusPoetry = new RedisOpusPoetry();
		if($redisOpusPoetry->praiseOpusPoetry($uid, $id,$flag)){
			if($flag ==1){
				$incr_num = 1;
			}elseif($flag==2){
				$incr_num = -1;
			}
			try {
				DB::table('opus_poetry')->where('id',$id)->increment('praise_num',$incr_num);
			} catch (ception $e) {
			}
		}
		return;
	}
	
	/**
	 * 诗文比赛评论功能
	 * @author:wang.hongli
	 * @since:2016/04/28
	 */
	public function opusPoetryComment($data){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return -101;
		}
		//评论人的id
		$fromid = $info['id'];
		$rules = array(
				'opusid'=>'required|integer',
				'toid'=>'required|integer',
				'content'=>'required',
		);
		$message = array(
				'opusid.required'=>'作品id不能为空',
				'opusid.integer'=>'作品id比必须为整数',
				'toid.required'=>'被评论用户不能为空',
				'toid.integer'=>'评论用户id必须为整数'
		);
		$data['fromid'] = $fromid;
		//作品主人id
		$uid = DB::table('opus_poetry')->where('id',$data['opusid'])->pluck('uid');
		$data['uid'] = $uid;
		$data['isdel'] = 0;
		$data['addtime'] = time();
		if(my_sens_word($data['content']))
		{
			return '内容含有禁用词';
		}
		$data['content'] = serialize($data['content']);
		if(!empty($data['_token'])){
			unset($data['_token']);
		}
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			$m  = $validator->messages();
			$msg = $m->all();
			return $msg[0];
		}
		try {
			$commentId = DB::table('opus_poetry_comment')->insertGetId($data);
			DB::table('opus_poetry')->where('id',$data['opusid'])->increment('comment_num');
			return $commentId;
		} catch (Exception $e) {
			return '发表评论错误，请重试';
		}
	}
	
	/**
	 * 诗文比赛获取作品评论列表
	 * @author:wang.hongli
	 * @since:2016/04/28
	 * @param:$opusid 作品id count 每页显示的数量,$offSet 偏移量
	 */
	public function getOpusPoetryCommentList($opusid,$count,$offSet){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = $info['id'];
		$rs = DB::table('opus_poetry_comment')->where('isdel',0)->where('opusid',$opusid)->orderBy('id','desc')->skip($offSet)->take($count)->get();
		$uid_array = array();
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$rs[$k]['content'] = unserialize($v['content']);
				$uid_arr[$v['uid']] = $v['uid'];
				$uid_arr[$v['fromid']] = $v['fromid'];
				if(!empty($v['toid'])){
					$uid_arr[$v['toid']] = $v['toid'];
				}
			}
			//选出用户信息
			$tmp_users = DB::table('user')->select('id','nick','gender','sportrait','portrait','teenager','isleague','grade','authtype')->whereIn('id',$uid_arr)->get();
			$users = array();
			if(!empty($tmp_users)){
				foreach($tmp_users as $k=>$v){
					$v['sportrait'] = $this->poem_url.ltrim($v['sportrait'],'.');
					$v['portrait'] = $this->poem_url.ltrim($v['portrait'],'.');
					$users[$v['id']] = $v;
				}
			}
			foreach($rs as $k=>&$v){
				$tmp_from_user = $users[$v['fromid']];
				$v['nick'] = $tmp_from_user['nick'];
				$v['gender'] = $tmp_from_user['gender'];
				$v['sportrait'] =$tmp_from_user['sportrait'];
				$v['portrait'] = $tmp_from_user['portrait'];
				$v['teenager'] = $tmp_from_user['teenager'];
				$v['isleague'] = $tmp_from_user['isleague'];
				$v['grade'] = $tmp_from_user['grade'];
				$v['authtype'] = $tmp_from_user['authtype'];
				if(!empty($v['toid']) && ($v['fromid'] != $v['toid'])){
					$tmpUserInfo = array();
					$v['toUserInfo'] = $users[$v['toid']];
				}else{
						$v['toUserInfo'] = null;
				}
			}
			//判断是否还有下一页
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
		}
		return $rs;
	}
	
	/**
	 * 诗文比赛--删除诗文比赛作品评论
	 * @author:wang.hongli
	 * @since:2016/04/28
	 * @param:$id 评论id
	 */
	public function delOpusPoetryComment($id){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = $info['id'];
		try {
			$uids = DB::table('opus_poetry_comment')->where('id',$id)->first(array('uid','fromid','toid','opusid'));
			if(empty($uids)){
				return '删除评论失败';
			}
			$opusid = $uids['opusid'];
			unset($uids['opusid']);
			if(!in_array($uid,$uids)){
				return '没有权限删除此评论';
			}
			DB::table('opus_poetry_comment')->where('id',$id)->update(array('isdel'=>1));
			DB::table('opus_poetry')->where('id',$opusid)->decrement('comment_num');
			return true;
		} catch (Exception $e) {
			return '删除评论失败，请重试';
		}
	}
	
	/**
	 * 诗文比赛--获取自己诗文列表
	 * @author:wang.hongli
	 * @since:2016/05/11
	 */
	public function getSelfOpusPoetry($competitionid = 0){
		if(empty($competitionid)){
			return array();
		}
		$info = $this->viaCookieLogin();
		if(empty($info))
			return 'nolog';
		$uid = $info['id'];
		$rs = DB::table('opus_poetry')->where('status',2)->where('uid',$uid)->where('competitionid',$competitionid)->get();
		if(!empty($rs)){
			//判断是否点赞
			$redisOpusPoetry = new RedisOpusPoetry();
			$opus_poetry_ids = $redisOpusPoetry->getUserPraisePoetry($uid);
			
			foreach($rs as $k=>&$v){
				$v['ispraise'] = 0;
				if(!empty($opus_poetry_ids) && in_array($v['uid'],$opus_poetry_ids)){
					$v['ispraise'] = 1;
				}
				$v['lyric'] = $this->poem_url.'/'.$v['lyric'];
			}
		}else{
			$rs = array();
		}
		return $rs;
	}
}