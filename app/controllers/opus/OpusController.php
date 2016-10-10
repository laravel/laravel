<?php 
	/**
	*	后台--作品控制器
	*	@author:wang.hongli
	*	@since:2016/02/28
	*/
	class OpusController extends BaseController {

		/**
		*	后台-获取作品列表
		*	@author:wang.hongli
		*	@since:2016/02/28
		**/
		public function opusList() {
			header("content-type:text/html;charset=utf8");
			$pagesize = 20;
			$uid = Input::get('uid','');
			$nick = trim(Input::get('nick',''));
			$opusname = trim(Input::get('opusname',''));
			$isdel = intval(Input::get('isdel',0));
			$type = intval(Input::get('type',-1));
			$isread = intval(Input::get('isread',-1));
			$return = array('nick'=>$nick,'opusname'=>$opusname,'uid'=>$uid,'isdel'=>$isdel,'type'=>$type,'isread'=>$isread);
		
			$time = strtotime(date('Y-m-d',strtotime('-1 day')));
			$base_dir = public_path('upload/poem/'.$time.'/');
			$poem_num = get_dir_num($base_dir);
			$conn = DB::table('opus');
			$max_page = $conn->max('id');
			if($isdel>-1){
				$conn->where('opus.isdel','=',$isdel);
			}
			if($type>-1){
				$conn->where('opus.type','=',$type);
			}
			if($isread > -1){
				$conn->where('opus.isread','=',$isread);
			}
			if(!empty($uid))
			{	
				$conn->where('opus.uid','=',$uid);
				$max_page = DB::table('user')->where('id',$uid)->pluck('opusnum');
			}
			if(!empty($nick))
			{
				$max_page = 0;
				$user_rs = DB::table('user')->where('nick','=',$nick)->select('nick','gender','id','opusnum')->get();
				$tmp_uid = array();
				$users = array();
				if(!empty($user_rs)){
					foreach($user_rs as $k=>$v){
						$tmp_uid[$v['id']] = $v['id'];
						$max_page += $v['opusnum'];
					}
				}
				if(empty($tmp_uid)){
					return Redirect::to('/admin/defaultError')->with('message',"用户名不存在");
				}
			}
			if(!empty($opusname))
			{
				$opusname = '%'.$opusname.'%';
				$conn->where('opus.name','like',$opusname);
				$max_page = $conn->count();
			}
			if(!empty($tmp_uid)){
				$conn->whereIn('uid',$tmp_uid);
			}
			$currentPage = Input::get('page',1);
			$offSet = ($currentPage-1)*$pagesize;
			$conn = $conn->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.isdel','opus.commentnum','opus.sharenum','opus.downnum','opus.opustime','opus.addtime','opus.url','opus.isread')->orderBy('id','desc')->skip($offSet)->take($pagesize)->get();
			if($max_page>2000000){
				$max_page -= 500000;
			}
			$paginator = Paginator::make($conn,$max_page, $pagesize);
			$userids=array();
			foreach($conn as $k=>$value){
					$userids[] = $value['uid'];//取得是opus里的uid
			}
			if(empty($userids)){
				return Redirect::to('/admin/defaultError')->with('message',"查询的用户名或者用户ID不存在");
			}
			$userinfo = DB::table('user')->whereIn('id',$userids)->select('id','nick','gender')->get();
			$userinfos = [];
			foreach ($userinfo as $k => $value) {
				$userinfos[$value['id']] = $value;//取的是user里的id
			}
			$opuslists = array();
			foreach ($conn as $k => $value) {
				$opuslists[$k]['id'] = $value['id'];//是opus里面的id
				$opuslists[$k]['uid'] = $userinfos[$value['uid']]['id'];
				$opuslists[$k]['nick'] = $userinfos[$value['uid']]['nick'];
				$opuslists[$k]['gender'] = $userinfos[$value['uid']]['gender'];
				$opuslists[$k]['name'] = $value['name'];
				$opuslists[$k]['lnum'] = $value['lnum'];
				$opuslists[$k]['praisenum'] = $value['praisenum'];
				$opuslists[$k]['repostnum'] = $value['repostnum'];
				$opuslists[$k]['isdel'] = $value['isdel'];
				$opuslists[$k]['commentnum'] = $value['commentnum'];
				$opuslists[$k]['sharenum'] = $value['sharenum'];
				$opuslists[$k]['downnum'] = $value['downnum'];
				$opuslists[$k]['opustime'] = $value['opustime'];
				$opuslists[$k]['addtime'] = $value['addtime'];
				$opuslists[$k]['url'] = $value['url'];
				$opuslists[$k]['isread'] = $value['isread'];
			}
			return View::make('opus.opuslist')->with('opuslist',$opuslists)->with('return',$return)->with('poem_num',$poem_num)->with('opusitem',$paginator);
		}
		
		//试听
		public function readOpus(){
			$id = (int)Input::get('id');
			try {
				DB::table('opus')->where('id',$id)->update(array('isread'=>1));
			} catch (Exception $e) {
			}
		}
		
		//添加各种数量
		public function addOpusNum(){
			$type = Input::get('type');
			$min_num = (int)Input::get('min_num');
			$max_num = (int)Input::get('max_num');
			$uid = Input::get('uid');
			$nick = Input::get('nick');
			$opusname = Input::get('opusname');
			
			$op_type = Input::get('op_type');
			$ids = Input::get('ids');
			if($op_type==1){
				if(empty($ids)){
					echo 0;
					exit;
				}
				$arr_ids = array_filter(explode(",",$ids));
				$conn = DB::table('opus')->select('opus.id','opus.uid')->leftJoin('user','user.id','=','opus.uid');
				$opuslist = $conn->whereIn('opus.id',$arr_ids)->where('user.isdel','=',0)->where('opus.isdel','=',0)->orderBy('opus.id','desc')->get();
			}else{
				if(empty($min_num) || empty($max_num) ){
					echo 0;
					exit;
				}
				if(!in_array($type,array('lnum','praisenum','repostnum'))){
					echo 0;
					exit;
				}
				if(empty($uid) && empty($nick) && empty($opusname)){
					echo 0;
					exit;
				}
				$conn = DB::table('opus');
				$conn = $conn->select('opus.id','opus.uid')->leftJoin('user','user.id','=','opus.uid');
				if(!empty($uid))
				{
					$conn = $conn->where('opus.uid','=',$uid); 
				}
				if(!empty($nick))
				{
					$nick = '%'.$nick.'%';
					$conn = $conn->where('user.nick','like',$nick); 
				}
				if(!empty($opusname))
				{
					$opusname = '%'.$opusname.'%';
					$conn = $conn->where('opus.name','like',$opusname);
				}
				$opuslist = $conn->where('user.isdel','=',0)->where('opus.isdel','=',0)->orderBy('opus.id','desc')->get();
			}
			$uids = array();
			foreach($opuslist as $v){
				$rand=rand($min_num,$max_num);
				//作品统计数量增加
				DB::table('opus')->where('id',$v['id'])->increment($type,$rand);
				//根据导航分表中的相关数量增加
				$table_ids = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$v['id'])->lists('table_id');
				if(!empty($table_ids)){
					foreach($table_ids as $key=>$value){
						$table_name = 'nav_opus_'.$value;
						DB::table($table_name)->where('opusid',$v['id'])->increment($type,$rand);
					}
				}
				//用户统计数量增加
				DB::table('user')->where('id',$v['uid'])->increment($type,$rand);
				//朗诵会会员冗余表league_user数量增加
				DB::table('league_user')->where('uid',$v['uid'])->increment($type,$rand);
				if(isset($uids[$v['uid']])){
					$uids[$v['uid']]+=$rand;
				}else{
					$uids[$v['uid']]=$rand;
				}
			}
			foreach($uids as $uid=>$num){
				//用户收听数
				$info = DB::table('user')->where('id','=',$uid)->first(array('lnum'));
				//查询收听数量
				$gradeInfo = DB::table('grade')->where('lnum','>',$info['lnum'])->first();
				if(empty($gradeInfo)){
					$gradeInfo['grade'] = 10;
				}
				//更新等级
				DB::table('user')->where('id',$uid)->update(array('grade'=>$gradeInfo['grade']));
			}
			echo 1;
			
		}

		//删除或恢复作品
		public function delOrDelOpus() {
			$opusId = Input::get('opusid');
			$sign = Input::get('sign');
			$uid = Input::get('uid');
			if(empty($opusId) || empty($uid)) {
				echo 'error';
				exit;
			}
			//恢复 isdel -> 0
			// if($sign) {
			// 	$isdel = 0;
			// 	//作品数+1
			// 	$sql = "update user set opusnum=opusnum+1 where id = {$uid}";
			// 	//定制听中opusid的记录恢复回来
			// 	$sql2 = "update personalcustom set isdel = 0 where opusid = $opusId";
			// } else {
			// 	$isdel = 1;
			// 	//作品数-1
			// 	$sql = "update user set opusnum=opusnum-1 where id = {$uid}";
			// 	//定制听中opusid的记录删除
			// 	$sql2 = "update personalcustom set isdel = 1 where opusid = $opusId";

				$aa=ApiCommonStatic::delOpus ( $uid, $opusId );
				echo $aa;
			
			// }
			// if(!DB::update($sql)) {
			// 	echo 'error';
			// 	exit;
			// }
			// $sql = "update opus set isdel = {$isdel} where id = {$opusId}";
			// if(!DB::update($sql)) {

			// 	$sql = "delete from competition_opus_rel where opusid = {$opusId} and uid = {$uid}";
			// 	if(!DB::update($sql))
			// 	{
			// 		echo 'error';
			// 		exit;
			// 	}
			// 	echo 'error';
			// 	exit;
			// } else {
			// 	DB::update($sql2);
			// 	echo 'success';
			// 	exit;
			// }
		}

		//添加作品--渲染视图
		public function addOpus() {
			return View::make('opus.addopus');
		}

		//ajax根据用户昵称获取相关用户
		public function accordNickFind() {
			$nick = trim(Input::get('nick'));
			$flag = Input::get('flag');
			$detailpoem = Input::get('detailpoem');
			if(empty($nick) && empty($detailpoem)) {
				echo "error";
				return;
			} else {
				if(1 == $flag) {
					$sql = "select id,nick from user where isdel=0 and nick like '%{$nick}%'";
				} else {
					$sql = "select id,name as nick from poem where name like '%{$detailpoem}%'";
				}
				$rs = DB::select($sql);
				if(empty($rs)) {
					echo "error";
					return;
				} else {
					$option = null;
					foreach($rs as $key=>$value) {
						$option .= "<option value='{$value['id']}'>{$value['nick']}</option>";
					}
					echo $option;
					return;
				}
			}
		}

		//添加作品--动作
		public function doAddOpus() {
			$userid = Input::get('userid');
			$poemid = Input::get('poemid');
			$opusname = trim(Input::get('opusname'));
			$firstchar = trim(Input::get('firstchar'));
			$pinyin = trim(Input::get('pinyin'));
			$opustime = trim(Input::get('opustime'));
			if(empty($opusname) || empty($firstchar) || empty($pinyin) || empty($opustime)) {
				return View::make('opus.addopus');
			}
			//上传歌词
			$fileLyric = Input::file('lyricName');
			//上传作品
			$fileOpus = Input::file('opus');
			if(empty($fileLyric) || empty($fileOpus)) {
				return View::make('opus.addopus');
			}
			$filePath = './upload/lyric/default/';
			$lyricName = time().uniqid().'.'.'lrc';
			$lastFilePath = $filePath.$lyricName;
			$fileLyric->move($filePath,$lastFilePath);
			$lastFilePath = ltrim($lastFilePath,'.');
			if(empty($lastFilePath)) {

			}
			$time = time();
			//将歌词插入lyric表
			$lyricdata = array(
				'url'=>$lastFilePath,
				'addtime'=>$time
			);
			//获取歌词id
			$lyricid = DB::table('lyric')->insertGetId($lyricdata);
			if(empty($lyricid)) {
				return View::make('opus.addopus');
			}
			$timestamp = strtotime(date('Y-m-d',time()));
			$filePath2 = './upload/poem/'.$timestamp.'/';
			if(!file_exists($filePath)) {
				mkdir($filePath,0755);
			}
			$poemName = $time.uniqid().'.'.'mp3';
			$lastFilePath2 = $filePath2.$poemName;
			$fileOpus->move($filePath2,$lastFilePath2);
			$lastFilePath2 = ltrim($lastFilePath2,'.');
			if(empty($lastFilePath2)) {
				return View::make('opus.addopus');
			}
			//将作品插入opus表
			$opusdata = array(
				'uid'=>$userid,
				'name'=>$opusname,
				'firstchar'=>$firstchar,
				'pinyin'=>$pinyin,
				'lyricid'=>$lyricid,
				'lyricurl'=>$lastFilePath,
				'poemid'=>$poemid,
				'url'=>$lastFilePath2,
				'opustime'=>$opustime,
				'addtime'=>$time,
				'type'=>1
			);
			$lastpoemid = DB::table('opus')->insertGetId($opusdata);
			if(empty($lastpoemid)) {
				return View::make('opus.addopus');
			}
			//根据原诗找出分类
			$tmpRs = DB::table('navpoemrel')->where('poemid',$poemid)->lists('navid');
			if(empty($tmpRs)) {
				return View::make('opus.addopus');
			} else {
				foreach($tmpRs as $key=>$value){
					$table_id = $value%10;
					$table_name = 'nav_opus_'.$table_id;
					DB::table('nav_opus_table_id')->insert(array('opusid'=>$lastpoemid,'table_id'=>$table_id));
					DB::table($table_name)->insert(array('uid'=>$userid,'opusid'=>$lastpoemid,'categoryid'=>$value,'addtime'=>time(),'poemid'=>$poemid));
				}
				//人的作品数+1
				DB::table('user')->where('id',$userid)->increment('opusnum',1,array('opusname'=>$opusname));
				return View::make('opus.addopus');
			}
		}

		//根据作品修改作品和人的相关收听数，赞数，转发数
		//type 1 : 收听 2：赞 3 转发 
		public function modify_opus_args()
		{
			$num = abs(intval($_POST['num']));
			//作品id
			$id = intval($_POST['id']);
			$old_val = abs(intval($_POST['old_val']));
			$type = intval($_POST['type']);
			$uid = intval($_POST['uid']);
			if(empty($id))
			{
				echo 'error';
				return;
			}
			switch($type)
			{
				case 1:
					$column = 'lnum';
					break;
				case 2:
					$column = 'praisenum';
					break;
				case 3:
					$column = 'repostnum';
					break;
			}
			$modify_num = $num-$old_val;
			try {
				DB::table('opus')->where('id',$id)->increment($column,$modify_num,array($column=>$num));
				DB::table('user')->where('id','=',$uid)->increment($column,$modify_num);
				//选出根据导航分表中的数据
				$table_id = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$id)->lists('table_id');
				if(!empty($table_id)){
					foreach($table_id as $k=>$v){
						$table_name = 'nav_opus_'.$v;
						DB::table($table_name)->where('opusid',$id)->update(array($column=>$num));
					}
				}
				//朗诵会会员冗余表 lnum,praisenum,repostnum数增加
				DB::table('league_user')->where('uid',$uid)->increment($column,$modify_num);
			} catch (Exception $e) {
				return;
			}
			//修改用户等级,如果是修改收听数
			if($type == 1){
				$adminOpus = new AdminOpus();
				$adminOpus->setUserGrade($uid);
			}
		}
		/**
		*	将作品从原来所属分类中移除
		*	@author:wang.hongli
		*	@since:2015/08/23
		**/
		public function catremove()
		{
			$opusid = intval(Input::get('opusid'));
			$competitionid = intval(Input::get('competitionid'));
			$return = 0;
			if(empty($opusid))
			{
				echo $return;
				return;
			}
			$return = AdminOpus::catremove($opusid,$competitionid);
			echo $return;
			return;
		}
		
		//测试
		public function test(){
			$time= microtime(true);
			$apiPlan = new ApiPlan;
			$apiPlan->planAll();
			echo "OK";
			echo "使用：".(microtime(true)-$time)."<br>";
		}
	}