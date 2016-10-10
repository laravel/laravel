<?php 
	/**
	* 后台版本更新
	**/
	class VersionController extends BaseController {
		
		//添加新版本---渲染视图
		public function versionList() {
			return View::make('version.versionList');
		}	

		//添加新版本--动作
		public function doAddVersion() {
			$url = Input::get('url');
			$platform = Input::get('platform');
			$des = Input::get('des');
			$force_update = !empty(Input::get('force_update')) ? intval(Input::get('force_update')) : 0;
			$version = !empty(Input::get('version')) ? Input::get('version') : 0;
			$version_code = !empty(Input::get('version_code')) ? Input::get('version_code') : 0;
			$time = time();

			$data = array(
				'url'=>$url,
				'platform'=>$platform,
				'des' =>$des,
				'force_update'=>$force_update,
				'version'=>$version,
				'version_code'=>$version_code,
				'uptime' => $time
				);
			DB::table('version')->insert($data);
			return View::make('version.versionList');
		}

		//查看版本列表
		public function checkVersionList() {
			$platform = Input::get('platform');
			if(empty($platform)) {
				$rs = DB::table('version')->where('platform','=',0)
										  ->orderby('uptime','desc')
										  ->get();

			} else {
				$rs = DB::table('version')->where('platform','=',1)
										  ->orderby('uptime','desc')
										  ->get();
			}
			return View::make('version.checkVersionList')->with('list',$rs);
		}	

		//佳作投稿
		public function addLyric()
		{	
			$addLyric = DB::table('addlyric')->select('addlyric.id', 'addlyric.uid', 'addlyric.addtime', 'addlyric.ischecked', 'addlyric.lyric', 'user.nick', 'user.gender')
											->leftJoin('user','user.id','=','addlyric.uid')	
				                            ->where('type','=',1)
				                            ->orderBy('addlyric.id','desc')->paginate(20);
			return View::make('version.addlyric')->with(array('addlyric'=>$addLyric,'title'=>'佳作投稿'));
		}
		
		//美文推荐
		public function addLyric2()
		{	
			$addLyric = DB::table('addlyric')->select('addlyric.id', 'addlyric.uid', 'addlyric.addtime', 'addlyric.ischecked', 'addlyric.lyric', 'user.nick', 'user.gender')
											->leftJoin('user','user.id','=','addlyric.uid')	
				                            ->where('type','=',0)
				                            ->orderBy('addlyric.id','desc')->paginate(20);
			return View::make('version.addlyric')->with(array('addlyric'=>$addLyric,'title'=>'美文推荐'));
		}
		
		//意见反馈
		public function feedBackList(){
			$conn = DB::table('feedback')->select('id','uid','nick','realname','telphone','content','addtime','status','plat_form','notice_msg','dev');
			$status = Input::has('status') ? Input::get('status') : -1;
			if($status>-1){
				$conn->where('status','=',$status);
			}
			$list = $conn->orderBy('id','desc')->paginate(20);
			
			//用户性别
			$users=array();
			if(!empty($list)){
				$uids=array();
				foreach($list as $v){
					$uids[$v['uid']]=$v['uid'];
				}
				$sql="select id,nick,gender from user where id in(".implode(",",$uids).")";
				$rlt=DB::select($sql);
				foreach($rlt as $v){
					$users[$v['id']]=$v;
				}
			}
			
			$all_status = array(-1=>'所有状态',0=>'未处理',1=>'问题不存在',2=>'已处理');
			return View::make('version.feedbacklist')->with(array('list'=>$list,'title'=>'意见反馈','status'=>$status,'all_status'=>$all_status,'users'=>$users));
		}
		public function setfeedBackStatus(){
			$id = (int)Input::get('id');
			$status = (int)Input::get('status');
			$content = htmlspecialchars(Input::get('push_content'));
			$uid = !empty(Input::get('uid')) ? intval(Input::get('uid')) : 0;
			//如果为以解决，需要内容不能为空
			if($status == 2 && empty($content)){
				echo -1;
				return;
			}
			if($status == 2){
				if(empty($uid)){
					echo -1;
					return;
				}
				$send_msg  = array(
					'action'=>0,
					'type'=>7,
					'fromid'=>1,
					'toid'=>$uid,
					'opusid'=>0,
					'name'=>'',
					'addtime'=>time(),
					'content'=>serialize($content),
					'commentid'=>0,
					'competitionid'=>0,
				);
				$distributeMessage = new DistributeMessage();
				$distributeMessage->distriMessage($send_msg);
			}
			$update_data = ['status'=>$status];
			if(!empty($content)){
				$s_content = serialize($content);
				$update_data['notice_msg'] = $s_content;
			}
			DB::table('feedback')->where('id',$id)->update($update_data);
			echo "操作成功";
		}

	}

