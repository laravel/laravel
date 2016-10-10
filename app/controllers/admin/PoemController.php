<?php
use Illuminate\Http\Request;

// 伴奏模块
class PoemController extends BaseController {
	
	// 获取伴奏列表,伴奏，所属分类
	public function poemList() {
		$id = Input::get ( 'id' );
		$poemname = Input::get ( 'poemname' );
		
		$conn = DB::table ( 'poem' );
		if (! empty ( $id )) {
			$conn = $conn->where ( 'id', '=', $id );
		}
		if (! empty ( $poemname )) {
			$conn = $conn->where ( 'name', 'like', '%' . $poemname . '%' );
		}
		
		$poemList = $conn->where ( 'isdel', '<>', 1 )->orderBy ( 'id', 'desc' )->paginate ( 20 );
		return View::make ( 'poem.poemList' )->with ( 'poemList', $poemList )->with ( 'id', $id )->with ( 'poemname', $poemname );
	}
	// 修改伴奏
	public function updatePoem() {
		$id = ( int ) Input::get ( 'id' );
		$poem = DB::table ( 'poem' )->where ( 'id', $id )->first ();
		// 所有分类
		$sql = "select id,category from navigation order by sort desc";
		$alltype = DB::select ( $sql );
		// 查询伴奏的分类
		$type = array ();
		$sql = "select navid from navpoemrel where poemid='" . $id . "'";
		$rs = DB::select ( $sql );
		foreach ( $rs as $k => $v ) {
			$type [] = $v ['navid'];
		}
		return View::make ( 'poem.updatePoem' )->with ( 'alltype', $alltype )->with ( 'type', $type )->with ( 'poem', $poem );
	}
	public function updatePoemDo() {
		// 读者老的名字
		$old_readername = trim ( Input::get ( 'old_readername' ) );
		// 写者老的名字
		$old_writername = trim ( Input::get ( 'old_writername' ) );
		
		$id = intval ( Input::get ( 'id' ) );
		$category = Input::get ( 'category' );
		$data = array ();
		$data ['name'] = trim(Input::get ( 'name' ));
		$data ['allchar'] = Input::get ( 'allchar' );
		$data ['spelling'] = Input::get ( 'spelling' );
		$data ['aliasname'] = Input::get ( 'aliasname' );
		$data ['readername'] = trim ( Input::get ( 'readername' ) );
		$data ['readerallchar'] = Input::get ( 'readerallchar' );
		$data ['writername'] = trim ( Input::get ( 'writername' ) );
		$data ['writerallchar'] = Input::get ( 'writerallchar' );
		$data ['sex'] = Input::get ( 'sex' );
		$data ['duration'] = Input::get ( 'duration' );
		$data ['burl'] = Input::get ( 'burl' );
		$data ['yurl'] = Input::get ( 'yurl' );
		$data ['lyricurl'] = Input::get ( 'lyricurl' );
		try {
			DB::table ( 'poem' )->where ( 'id', $id )->update ( $data );
		} catch (Exception $e) {
			return Redirect::to ( '/admin/defaultError' )->with ( 'message', '伴奏重复读写人重复，请重试' );
		}
		// 删除旧分类
		DB::table ( 'navpoemrel' )->where ( 'poemid', '=', $id )->delete ();
		// 添加新分类
		if (! empty ( $category )) {
			foreach ( $category as $v ) {
				DB::table ( 'navpoemrel' )->insert ( array (
						'navid' => ( int ) $v,
						'poemid' => $id,
						'addtime' => time () 
				) );
			}
		}
		
		// 修改读者-伴奏关系
		if ($old_readername != $data ['readername']) {
			// 判断新的读者是否存在
			$new_reader_id = DB::table ( 'reader' )->where ( 'name', $data ['readername'] )->pluck ( 'id' );
			if (empty ( $new_reader_id )) {
				return Redirect::to ( '/admin/defaultError' )->with ( 'message', '填写的读者不存在，请返回从新填写' );
			}
			// 选出读者id，删除关系表reader中的记录
			$old_reader_id = DB::table ( 'reader' )->where ( 'name', $old_readername )->pluck ( 'id' );
			if (! empty ( $old_reader_id ) && ! empty ( $id )) {
				DB::table ( 'readpoemrel' )->where ( 'readerid', $old_reader_id )->where ( 'poemid', $id )->delete ();
			}
			if (! empty ( $new_reader_id ) && ! empty ( $id )) {
				// 插入reader关系表中
				DB::table ( 'readpoemrel' )->insert ( array (
						'readerid' => $new_reader_id,
						'poemid' => $id,
						'addtime' => time () 
				) );
			}
		}
		// 修改写者-伴奏关系
		if ($old_writername != $data ['writername']) {
			// 判断新的写者是否存在
			$new_writer_id = DB::table ( 'writer' )->where ( 'name', $data ['writername'] )->pluck ( 'id' );
			if (empty ( $new_writer_id )) {
				return Redirect::to ( '/admin/defaultError' )->with ( 'message', '填写的写者不存在，请返回从新填写' );
			}
			// 选出写者id，删除关系表writer表中的记录
			$old_writer_id = DB::table ( 'writer' )->where ( 'name', $old_writername )->pluck ( 'id' );
			if (! empty ( $old_writer_id ) && ! empty ( $id )) {
				DB::table ( 'writepoemrel' )->where ( 'writerid', $old_writer_id )->where ( 'poemid', $id )->delete ();
			}
			// 插入writepoemrel关系表中
			if (! empty ( $new_writer_id ) && ! empty ( $id )) {
				DB::table ( 'writepoemrel' )->insert ( array (
						'writerid' => $new_writer_id,
						'poemid' => $id,
						'addtime' => time () 
				) );
			}
		}
		return Redirect::to ( '/admin/updatePoem?id=' . $id );
	}
	public function addPoemDownNum() {
		$num = ( int ) Input::get ( 'num' );
		$ids = Input::get ( 'ids' );
		if ($num > 0) {
			$where = '';
			if (! empty ( $ids )) {
				$_ids = explode ( ",", $ids );
				$where = " where id in ('" . implode ( "','", $_ids ) . "')";
			}
			$sql = "update poem set downnum=downnum+" . $num . "" . $where;
			DB::update ( $sql );
			
			echo 1;
		} else {
			echo 0;
		}
	}
	
	// 修改伴奏名称
	public function modifyPoemName() {
		$name = trim ( Input::get ( 'name' ) );
		$type = Input::get ( 'type' );
		$poemid = Input::get ( 'poemid' );
		if (empty ( $poemid ) || empty ( $type ) || empty ( $name )) {
			echo "error";
			return;
		}
		switch ($type) {
			case 1 :
				$sql = "update poem set name = '{$name}' where id= '{$poemid}'";
				break;
			case 2 :
				$sql = "update poem set aliasname = '{$name}' where id = '{$poemid}'";
				break;
			case 3 :
				$sql = "update poem set spelling = '{$name}' where id = '{$poemid}'";
				break;
			case 4 :
				$sql = "update poem set allchar = '{$name}' where id = '{$poemid}'";
				break;
			case 5 :
				$sql = "update poem set downnum = {$name} where id = '{$poemid}'";
				break;
		}
		if (! DB::update ( $sql )) {
			echo "error";
			return;
		}
	}
	
	// 添加伴奏--渲染视图
	public function adminAddPoem() {
		$sql = "select id,category from navigation order by sort desc";
		$rs = DB::select ( $sql );
		return View::make ( 'poem.adminAddPoem' )->with ( 'list', $rs );
	}
	
	// 添加伴奏--动作
	public function doAdminAddPoem() {
		$name = trim ( Input::get ( 'name' ) );
		$category = Input::get ( 'category' );
		$readername = trim ( Input::get ( 'readername' ) );
		$writername = trim ( Input::get ( 'writername' ) );
		$duration = trim ( Input::get ( 'duration' ) );
		$spelling = trim ( Input::get ( 'spelling' ) );
		$allchar = trim ( Input::get ( 'allchar' ) );
		$aliasname = trim ( Input::get ( 'aliasname' ) );
		$readerfirstchar = trim ( Input::get ( 'readerfirstchar' ) );
		$readerpinyin = trim ( Input::get ( 'readerpinyin' ) );
		$writerfirstchar = trim ( Input::get ( 'writerfirstchar' ) );
		$writerpinyin = trim ( Input::get ( 'writerpinyin' ) );
		$poemercatid = Input::get ( 'poemercatid' );
		$poemercatid = ! empty ( $poemercatid ) ? $poemercatid : 1; // 默认是男分类
		$file1 = Input::file ( 'formName1' ); // 伴奏
		$file2 = Input::file ( 'formName2' ); // 原唱
		$file3 = Input::file ( 'formName3' ); // 歌词
		
		if (empty ( $name ) || empty ( $category ) || empty ( $readername ) || empty ( $writername ) || empty ( $duration ) || empty ( $spelling ) || empty ( $allchar ) || empty ( $aliasname ) || empty ( $file1 ) || empty ( $file2 ) || empty ( $file3 ) || empty ( $writerfirstchar ) || empty ( $readerfirstchar )) {
			return Redirect::to ( '/admin/adminAddPoem' );
		}
		$tmpArr1 = $this->importRWL ( $readername, $readerfirstchar, $readerpinyin, $writername, $writerfirstchar, $writerpinyin, $file1, $file2, $file3 );
		if (! $tmpArr1) {
			return Redirect::to ( '/admin/adminAddPoem' );
		} else {
			$readerId = $tmpArr1 ['readerId'];
			$writerId = $tmpArr1 ['writerId'];
			$lyricId = $tmpArr1 ['lyricid'];
			$lyricurl = $tmpArr1 ['lyricurl'];
			$yurl = $tmpArr1 ['yurl'];
			$burl = $tmpArr1 ['burl'];
		}
		$time = time ();
		// 诗入库poem表
		$poemRs = array (
				'name' => $name,
				'lyricid' => $lyricId,
				'readername' => $readername,
				'writername' => $writername,
				'burl' => $burl,
				'yurl' => $yurl,
				'lyricurl' => $lyricurl,
				'duration' => $duration,
				'spelling' => $spelling,
				'allchar' => $allchar,
				'aliasname' => $aliasname,
				'addtime' => $time 
		);
		
		$poemid = DB::table ( 'poem' )->insertGetId ( $poemRs );
		if (empty ( $poemid )) {
			return Redirect::to ( '/admin/adminAddPoem' );
		}
		
		// 读者-伴奏关系表插入数据
		$sql = "insert into readpoemrel (readerid,poemid,addtime) values ({$readerId},$poemid,$time)";
		if (! DB::insert ( $sql )) {
			return Redirect::to ( '/admin/adminAddPoem' );
		}
		// 写者--伴奏关系表
		$sql = "insert into writepoemrel (writerid,poemid,addtime) values ({$writerId},{$poemid},$time)";
		if (! DB::insert ( $sql )) {
			return Redirect::to ( '/admin/adminAddPoem' );
		}
		
		// 写者--写者-写者性别分类关系表
		$sql = "insert into wprel (writerid,poemercatid,addtime) values ({$writerId},{$poemercatid},$time)";
		if (! DB::insert ( $sql )) {
			return Redirect::to ( '/admin/adminAddPoem' );
		}
		// 导航--伴奏入库
		$sql = "insert into navpoemrel (navid,poemid,addtime) values ";
		$str = null;
		foreach ( $category as $value ) {
			$str .= "($value,$poemid,$time),";
		}
		$str = trim ( $str, ',' );
		$sql = $sql . $str;
		DB::insert ( $sql );
		
		return Redirect::to ( '/admin/adminAddPoem' );
	}
	// 读者，写者，歌词 上传入库操作，--返回读者id，写者id，歌词url
	protected function importRWL($readername, $readerfirstchar, $readerpinyin, $writername, $writerfirstchar, $writerpinyin, $file1, $file2, $file3) {
		$time = time ();
		// 读者
		$lastData = array (); // 最后返回的数组
		$readerRs = array ();
		$writerRs = array ();
		$lastData = array ();
		$readerRs = DB::table ( 'reader' )->where ( 'name', '=', $readername )->first ( array (
				'id' 
		) );
		$writerRs = DB::table ( 'writer' )->where ( 'name', '=', $writername )->first ( array (
				'id' 
		) );
		if (empty ( $readerRs )) {
			$readerId = DB::table ( 'reader' )->insertGetId ( array (
					'name' => $readername,
					'firstchar' => $readerfirstchar,
					'pinyin' => $readerpinyin 
			) );
		} else {
			$readerId = $readerRs ['id'];
		}
		if (empty ( $readerId ))
			return false;
		$lastData ['readerId'] = $readerId;
		if (empty ( $writerRs )) {
			$writerId = DB::table ( 'writer' )->insertGetId ( array (
					'name' => $writername,
					'firstchar' => $writerfirstchar,
					'pinyin' => $writerpinyin 
			) );
		} else {
			$writerId = $writerRs ['id'];
		}
		if (empty ( $writerId ))
			return false;
		$lastData ['writerId'] = $writerId;
		// 歌词
		$filePath = './upload/lyric/default/';
		$lyricName = time () . uniqid () . '.' . 'lrc';
		$lastFilePath = $filePath . $lyricName;
		$file3->move ( $filePath, $lastFilePath );
		$lastFilePath = ltrim ( $lastFilePath, '.' );
		$lastData ['lyricurl'] = $lastFilePath;
		if (empty ( $lastFilePath ))
			return false;
			// 入库
		$data = array (
				'url' => $lastFilePath,
				'addtime' => $time 
		);
		$lyricId = DB::table ( 'lyric' )->insertGetId ( $data );
		if (empty ( $lyricId ))
			return false;
		$lastData ['lyricid'] = $lyricId;
		
		// 原唱上传
		$filePath = './upload/poem/default/';
		$yPoem = time () . uniqid () . '.' . 'mp3';
		$lastFilePath1 = $filePath . $yPoem;
		$file2->move ( $filePath, $lastFilePath1 );
		$lastFilePath1 = ltrim ( $lastFilePath1, '.' );
		if (empty ( $lastFilePath1 ))
			return false;
		$lastData ['yurl'] = $lastFilePath1;
		
		// 伴奏上传
		$bPoem = time () . uniqid () . '.' . 'mp3';
		$lastFilePath2 = $filePath . $bPoem;
		$file1->move ( $filePath, $lastFilePath2 );
		$lastFilePath2 = ltrim ( $lastFilePath2, '.' );
		if (empty ( $lastFilePath2 ))
			return false;
		$lastData ['burl'] = $lastFilePath2;
		return $lastData;
	}
	
	// 佳作投稿
	public function addLyric() {
		$sql = "select user.id as uid,user.nick,addlyric.id,addlyric.ischecked,addlyric.lyric,addlyric.addtime,addlyric.type from addlyric left join user on user.id = addlyric.uid order by addlyric.addtime desc";
		$rs = DB::select ( $sql );
		return View::make ( 'poem.addLyric' )->with ( 'list', $rs );
	}
	
	// 修改投稿状态
	public function modifyAddLyric() {
		$return = 0;
		$checkstatus = $_POST ['checkstatus'];
		$addlyricid = $_POST ['addlyricid'];
		if (empty ( $addlyricid )) {
			echo $return;
			return;
		}
		$status = 0;
		if (0 == $checkstatus) {
			$status = 1;
		}
		$sql = "update addlyric set ischecked = {$status} where id = {$addlyricid}";
		if (DB::update ( $sql )) {
			$return = 1;
		}
		echo $return;
		return;
	}
	
	// 举报作品列表
	public function reportOpus() {
		$pagesize = 20;
		$page = (Input::has('page')) ? intval(Input::get('page')) : 1;
		$offset = ($page-1)*$pagesize;
		$total = DB::table('reportOpus')->count();
		$rs = DB::table('reportOpus')->select('id','opusid','reason','status','fromid','from_nick','addtime')->orderBy('id','desc')->skip($offset)->take($pagesize)->get();
		//单独获取分页
		$paginator = Paginator::make($rs, $total, $pagesize);
		$opus_ids = array();
		$uids = array();
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$opus_ids[] = $v['opusid'];
			}
			//查出所有作品
			$opus_tmp_rs = DB::table('opus')->select('id','name','uid')->whereIn('id',$opus_ids)->get();
			$opus_rs = array();
			if(!empty($opus_tmp_rs)){
				foreach($opus_tmp_rs as $k=>$v){
					$uids[] = $v['uid'];
					$opus_rs[$v['id']] = $v;
				}
			}
			//查询用户昵称和性别
			$user_tmp_rs = DB::table('user')->select('id','nick','gender')->whereIn('id',$uids)->get();
			$user_rs = array();
			if(!empty($user_tmp_rs)){
				foreach($user_tmp_rs as  $k=>$v){
					$user_rs[$v['id']] = $v;
				}
			}
			//数组整合
			foreach($rs as $k=>&$v){
				$v['name'] = isset($opus_rs[$v['opusid']]['name']) ? $opus_rs[$v['opusid']]['name'] : '未知';
				$uid = $opus_rs[$v['opusid']]['uid'];
				$v['nick'] = $user_rs[$uid]['nick'];
				$v['gender'] = $user_rs[$uid]['gender'];
			}
		}
		return View::make ( 'poem.reportOpus' )->with ( 'list', $rs )->with('pages',$paginator);
	}
	// 处理举报作品的状态
	public function modifyReportOpus() {
		$return = 0;
		$status = intval($_POST ['status']);
		$id = intval($_POST ['id']);
		if (empty ( $id )) {
			echo $return;
			return;
		}
		$rStatus = 0;
		if (0 == $status) {
			$rStatus = 1;
		}
		try {
			DB::table('reportOpus')->where('id',$id)->update(array('status'=>$rStatus));
			$return = 1;
		} catch (Exception $e) {
		}
		echo $return;
		return;
	}
	// xls列表
	public function poemXlsList() {
		$poemxlslist = DB::table ( 'poem_xls' )->where('status','<>',1)->orderBy ( 'id', 'desc' )->paginate ( 20 );
		return View::make ( 'poem.poemXlsList' )->with ( 'poemxlslist', $poemxlslist );
	}
	/**
	 * 导入伴奏范读删除功能
	 * @author:wang.hongli
	 * @since:2016/03/31
	 * @param:id 执行导入表poem_xls中记录id，flag:1 删除 2导入
	 */
	public function delOrExecXls(){
		$id = intval($_GET['id']);
		if(empty($id) ){
			echo '操作失败，请重试';
		}
		if(DB::table('poem_xls')->where('id',$id)->update(array('status'=>1))){
			echo '删除成功';
		}else{
			echo '删除失败';
		}
	}	
	// 计划任务-用户列表(自动增加用户的作品三个数量)
	public function planUserList() {
		$pagesize = 10;
		$conn = DB::table ( 'plan_user' )->select ( 'plan_user.id', 'plan_user.uid', 'user.nick' )->leftJoin ( 'user', 'plan_user.uid', '=', 'user.id' );
		
		$uid = ( int ) Input::get ( 'uid' ) ? ( int ) Input::get ( 'uid' ) : '';
		if (! empty ( $uid )) {
			$conn = $conn->where ( 'plan_user.uid', '=', $uid );
		}
		$nick = Input::get ( 'nick' ) ? Input::get ( 'nick' ) : '';
		if (! empty ( $nick )) {
			$conn = $conn->where ( 'user.nick', 'like', '%' . $nick . '%' );
		}
		
		$total = $conn->count ();
		$list = $conn->orderBy ( 'plan_user.id', 'desc' )->paginate ( $pagesize );
		return View::make ( 'poem.planUserList' )->with ( 'list', $list )->with ( 'total', $total )->with ( 'uid', $uid )->with ( 'nick', $nick );
	}
	public function planUserDel() {
		$id = ( int ) $_GET ["id"];
		$sql = "delete from plan_user where id=" . $id;
		DB::delete ( $sql );
		echo 1;
	}
	public function planUserAdd() {
		$uid = ( int ) $_GET ["uid"];
		$sql = "select * from plan_user where uid=" . $uid;
		$rlt = DB::select ( $sql );
		if (empty ( $rlt )) {
			$sql = "insert into plan_user(uid) values (" . $uid . ")";
			DB::insert ( $sql );
			echo 1;
		} else {
			echo 0;
		}
	}
	
	// 计划任务配置
	public function planConfig() {
		$pagesize = 10;
		$conn = DB::table ( 'plan_config' )->select ( '*' );
		$total = $conn->count ();
		$list = $conn->orderBy ( 'id', 'asc' )->paginate ( $pagesize );
		return View::make ( 'poem.planConfig' )->with ( 'list', $list )->with ( 'total', $total );
	}
	public function updatePlanConfig() {
		$id = ( int ) $_GET ['id'];
		$info = DB::table ( 'plan_config' )->where ( 'id', $id )->first ();
		return View::make ( 'poem.updatePlanConfig' )->with ( 'info', $info );
	}
	public function updatePlanConfigDo() {
		$id = ( int ) $_POST ['id'];
		$status = ( int ) $_POST ['status'];
		/*
		 * $tmp['min_shou']=(int)$_POST['min_shou'];
		 * $tmp['max_shou']=(int)$_POST['max_shou'];
		 * $tmp['min_agree']=(int)$_POST['min_agree'];
		 * $tmp['max_agree']=(int)$_POST['max_agree'];
		 * $tmp['min_zhuan']=(int)$_POST['min_zhuan'];
		 * $tmp['max_zhuan']=(int)$_POST['max_zhuan'];
		 * $contents=serialize($tmp);
		 */
		$contents = serialize ( $_POST );
		$sql = "update plan_config set contents='" . $contents . "',status='" . $status . "' where id='" . $id . "'";
		$rlt = DB::update ( $sql );
		if ($rlt) {
			return Redirect::to ( 'admin/updatePlanConfig?id=' . $id );
		} else {
			return Redirect::to ( 'admin/updatePlanConfig?id=' . $id );
		}
	}
	public function upPlanConfigStatus() {
		$id = ( int ) $_GET ["id"];
		$status = ( int ) $_GET ["status"] == 2 ? 2 : 0;
		$sql = "update plan_config set status = '" . $status . "' where id=" . $id;
		$rlt = DB::update ( $sql );
		if ($rlt) {
			echo 1;
		} else {
			echo 0;
		}
	}
	public function planExec() {
		$time = microtime ( true );
		$name = $_GET ['name'];
		if ($name == 'plan_all_num') {
			$apiPlan = new ApiPlan ();
			$apiPlan->planAll ();
		} elseif ($name == 'plan_user_num') {
			$apiPlan = new ApiPlan ();
			$apiPlan->planUserNum ();
		}
		echo "使用：" . (microtime ( true ) - $time) . "<br>";
		echo "OK";
	}
	/**
	 * 后台修改伴奏拼音首字母
	 * @author:wang.hongli
	 * @since:2016/03/29
	 * @param:无
	 */
	public function modifyPoemChar()
	{
		$spelling = $_POST['spelling'];
		$poemid = intval($_POST['poemid']);
		$flag = intval($_POST['flag']);
		$adminPoem = new AdminPoem();
		$validator = $adminPoem->modifyPoemChar($spelling,$poemid,$flag);
		if($validator){
			echo 1;
		}
	}
	/**
	 * 后台上传伴奏，范读 -- 视图
	 * @author:wang.hongli
	 * @since:2016/03/29
	 */
	public function adminViewUpPoem(){
		if(!empty($_FILES))
		{
				//检测上传文件类型
				$file =  Input::file ( 'file' );
				$my_file_type = strtolower(my_file_type ( $file->getRealPath () ));
				$file_name = $file->getClientOriginalName();
				if(empty($my_file_type) || $my_file_type != 'mp3')
				{
					die($file_name.'不是mp3类型文件');
				}
				try {
					//移动文件到指定目录
					$file->move('upload/poem/default/',$file_name);
					die('');
				} catch (Exception $e) {
					die($file_name.'上传失败，请重试');
				}
		}
		return View::make ( 'poem.viewuppoem' );
	}
	
	
	/**
	 * 后台上传诗词
	 * @author:wang.hongli
	 * @since:2016/03/29
	 */
	public function adminViewUpLyric(){
		if(!empty($_FILES)){
			$file =  Input::file ( 'file' );
			$my_file_ext = strtolower($file->getClientOriginalExtension());
			$file_name = $file->getClientOriginalName();
			$error_msg = $file_name.'上传失败，请重新上传';
			if(empty($my_file_ext) || $my_file_ext != 'lrc')
			{
				die($file_name.'不是lrc类型文件');
			}
			//文件名重新匹配一次
			if(stripos($file_name, '.') !== false){
				$tmp_name = explode('.', $file_name);
				$file_name = trim($tmp_name[0]).'.lrc';
			}
			else{
				$file_name .= '.lrc';
			}
			try {
				//移动文件到指定目录
				$file->move('upload/lyric/default/',$file_name);
				die('');
			} catch (Exception $e) {
				die($error_msg);
			}
		}
		return View::make('poem.viewuplyrice');
	}
	
	/**
	 * 后台上传excel表格
	 * @author:wang.hongli
	 * @since:2016/03/29
	 */
	public function adminViewUpExcel(){
		if(!empty($_FILES)){
			$file =  Input::file ( 'file' );
			$my_file_type = strtolower(my_file_type ( $file->getRealPath () ));
			$file_name = $file->getClientOriginalName();
			$error_msg = $file_name.'上传失败，请重新上传';
			if(empty($my_file_type) || $my_file_type != 'msoffice')
			{
				die($file_name.'不是xls类型文件');
			}
			//移动文件到指定目录
			$new_file_path = public_path('importexcel/');
			$new_file_full_path = $new_file_path.$file_name;
			if(file_exists($new_file_full_path)){
				die($file_name.'已经存在，请修改excel表格名称');
			}
			try {
				$file->move($new_file_path,$file_name);
				$plan_time = time()+7200;
				DB::table('poem_xls')->insert(array('name'=>$file_name,'plan_time'=>$plan_time,'add_time'=>$plan_time,'status'=>0,'update_time'=>0));
				die('');
			} catch (Exception $e) {
				die($error_msg);
			}
		}
		return View::make('poem.viewupexcel');
	}
	
	/**
	 * 修改伴奏导入时间
	 * @author:wang.hongli
	 * @since:2016/05/15
	 */
	public function updatePoemPlanTime(){
		$id= intval(Input::get('id'));
		$plan_time = Input::get('plan_time');
		if(empty($id) || empty($plan_time)){
			echo 'error';
		}
		$plan_time = strtotime($plan_time);
		try {
			DB::table('poem_xls')->where('id',$id)->update(array('plan_time'=>$plan_time));
		} catch (Exception $e) {
			print_r($e->getMessage());die;
		}
	}
	
	
}

