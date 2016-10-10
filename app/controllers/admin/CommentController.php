<?php 
	//评论控制器
	class CommentController extends BaseController {

		//获取评论列表
		public function getCommentList()
		{
			//根据评论人的昵称和评论人的id搜索
			$uid = Input::has('uid') ? intval(Input::get('uid')) : '';
			$nick = Input::has('nick') ? Input::get('nick') : '';
			
			//被评论人的id和被评论人的昵称
			$toid = Input::has('toid')?intval(Input::get('toid')):'';
			$tonick = Input::has('tonick') ? Input::get('tonick') : '';
			$search_arr = array('uid'=>$uid,'nick'=>$nick,'toid'=>$toid,'tonick'=>$tonick);
			try {
				$conn = DB::table('opuscomment')->select('id','uid','opusid','fromid','toid','content','isdel','addtime');
				if(!empty($uid)){
					$conn->where('fromid',$uid);
				}
				if(!empty($toid)){
					$conn->where('toid',$toid);
				}
				$user_ids = array();
				if(!empty($nick)){
					$like = '%'.$nick.'%';
					$user_ids = DB::table('user')->where('nick','like',$like)->where('isdel','<>',1)->lists('id');
					if(!empty($user_ids)){
						$conn->whereIn('fromid',$user_ids);
					}
				}
				$touser_ids = array();
				$user_ids = array();
				if(!empty($tonick)){
					$like = '%'.$tonick.'%';
					$user_ids = DB::table('user')->where('nick','like',$like)->where('isdel','<>',1)->lists('id');
					if(!empty($user_ids)){
						$conn->whereIn('toid',$user_ids);
					}
				}
				$conn->orderBy('id','desc');
				$rs = $conn->paginate(20);
			} catch (Exception $e) {
				return Redirect::to ( '/admin/defaultError' )->with ( 'message', '没有符合条件记录，请重试' );
			}
			$from_id = array();
			$to_id = array();
			$user_id = array();
			$opus_id = array();
			if(!empty($rs))
			{
				foreach($rs as $key=>$value)
				{
					$from_id[] = $value['fromid'];
					$to_id[] = $value['toid'];
					$opus_id[] = $value['opusid'];
					$user_id[] = $value['uid'];
				}
				//两个数组取并集
				$uid = array_merge($from_id,$to_id,$user_id);
				//去除值为0的元素
				if(!empty($uid))
				{
					foreach($uid as  $val)
					{
						if(empty($val)) continue;
						$user_id[] = $val;
					}
				}
				if(!empty($user_id))
				{
					$tmp_user = DB::table('user')->select('id','nick')->whereIn('id',$user_id)->get();
					if(!empty($tmp_user))
					{
						foreach($tmp_user as $val)
						{
							$user[$val['id']] = $val['nick'];
						}
					}
				}
				$opus = array();
				//查出作品
				if(!empty($opus_id)){
					$opus_tmp = DB::table('opus')->select('id','name','addtime')->whereIn('id',$opus_id)->get();
					if(!empty($opus_tmp))
					{
						foreach($opus_tmp as $k=>$v)
						{
							$opus[$v['id']] = array('name'=>$v['name'],'addtime'=>$v['addtime']);
						}
					}
				}
				$commentlist = array();
				//评论为主线，整合数组
				foreach($rs as $k=>$v)
				{
					$commentlist[$k]['commentid'] = $v['id'];
					$commentlist[$k]['id'] = $v['opusid'];
					$commentlist[$k]['uid'] = $v['uid'];
					$commentlist[$k]['name'] = isset($opus[$v['opusid']]['name']) ? $opus[$v['opusid']]['name'] : '未知';
					$commentlist[$k]['opusaddtime'] = isset($opus[$v['opusid']]['addtime']) ? $opus[$v['opusid']]['addtime'] : '未知';
					$commentlist[$k]['commentid'] = $v['id'];
					$commentlist[$k]['content'] = $v['content'];
					$commentlist[$k]['addtime'] = $v['addtime'];
					$commentlist[$k]['isdel'] = $v['isdel'];
					$commentlist[$k]['fromid'] =$v['fromid'];
					$commentlist[$k]['toid'] = $v['toid'];
					$commentlist[$k]['nick'] = isset($user[$v['uid']]) ? $user[$v['uid']] : '未知';
					$commentlist[$k]['fromnick'] = isset($user[$v['fromid']]) ? $user[$v['fromid']]: '未知';
					$commentlist[$k]['tonick'] = isset($user[$v['toid']]) ? $user[$v['toid']] : '未知';
				}
			}
			return View::make('comment.commentlist')->with('commentlist',$commentlist)->with('rs',$rs)->with('searcharr',$search_arr);
		}

		//删除or恢复评论
		public function delOrDelComment() {
			$commentId = intval(Input::get('commentId'));
			$opusId = intval(Input::get('opusId'));
			if(empty($commentId) || empty($opusId)) {
				echo 'error';
				return;
			}
			$sign = intval(Input::get('sign'));
			//解禁 isdel -> 0
			$isdel=1;
			if($sign) {
				$isdel = 0;
			}
			try {
				$update_flag = DB::table('opuscomment')->where('id',$commentId)->update(array('isdel'=>$isdel));
				if($update_flag){
					DB::table('opus')->where('id',$opusId)->decrement('commentnum');
				}
				echo 'success';
			} catch (Exception $e) {
				echo "error";
			}
		}
	}