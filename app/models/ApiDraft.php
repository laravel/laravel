<?php
/**
 ** 作品模型
 */
class ApiDraft extends ApiCommon {
	
	// 作品上传
	public function uploadDraft() {
		$info = $this->viaCookieLogin ();
		if ($info) {
			$uid = $info ['id'];
			if (! Input::has ( 'poemId' ))
				return '原诗不存在';
			$poemId = intval(Input::get ( 'poemId' ));// 原诗id
			$opusName = trim ( Input::get ( 'opusName' ) ); // 作品名称
			if(my_sens_word($opusName))
			{
				return '题目中含有禁用词';
			}
			//针对自由诵读特殊处理
			//$nav_arr = array(21,86,87,88,89);
			//$nav_id = DB::table('navpoemrel')->where('poemid',$poemId)->lists('navid');
			//	//交集
			//$intersect = array_intersect($nav_arr, $nav_id);
			//if(!empty($intersect)){
			//	return '按照国家有关部门要求，“自由诵读”功能暂停使用！';
			//}
			if (empty ( $opusName )) {
				return '作品名称不能为空';
			}
			// 参赛 '1,2,3',如果是夏青杯，检测权限等
			$competitionId = intval(Input::get ( 'competitionId' ));
			if (! empty ( $competitionId )) {
				$apiCompetition = new ApiCompetition ();
				$flag = $apiCompetition->dif_check_competition ( $competitionId );
				if ($flag ['status'] != 1) {
					return $flag ['message'];
				}
			}
			// 对自由朗诵或者改编朗诵中的'版'字特殊处理
			$replace_str = '版';
			if (mb_strpos ( $opusName, $replace_str ) !== false) {
				$opusName = str_replace ( $replace_str, '', $opusName );
			}
			$str = "伴奏";
			if (mb_strpos ( $opusName, $str ) !== false) {
				if (mb_strpos ( $opusName, '·' )) {
					$tmpName = explode ( '·', $opusName );
					$opusName = '自由诵读·' . array_pop ( $tmpName );
				}
			}
			//根据伴奏id，特殊处理诗文比赛作品名
			if($poemId >=100000001 ){
				$opusName = '自由诵读·'.$opusName;
			}
			
			$pinyinName = '';
			$pattern = '/([\x{4e00}-\x{9fa5}a-zA-Z0-9])/u';
			preg_match_all ( $pattern, $opusName, $res );
			if (! empty ( $res )) {
				$pinyinName = implode ( "", $res [0] );
			}
			
			if (! Input::has ( 'opusTime' ))
				return '作品时长不符合规则';
			$opusTime = Input::get ( 'opusTime' );
			// 暂留禁用语功能
			if (! Input::has ( 'lyricurl' ))
				return '原始作品不存在';
			$lyricPath = Input::get ( 'lyricurl' ); // 原歌词地址,绝对地址
			$tmpArr = explode ( '/', $lyricPath );
			$dataLyricPath = '/' . implode ( array_slice ( $tmpArr, 3 ), '/' );
			$type = 0; // 标识是否是改词
			if (Input::get ( 'lyric' )) { // 修改后的歌词
				//进行敏感词过滤
				$lyric_content = Input::get('lyric');
				if(my_sens_word($lyric_content))
				{
					return '由于作品中涉嫌包含敏感词导致无法上传！敬请谅解！';
				}
				$lyric = htmlspecialchars ( $lyric_content );
				$lyricDir = $this->isExistDir ( 'lyric' );
				$lyricName = time () . uniqid () . '.lrc';
				$lyricPath = $lyricDir . $lyricName;
				$dataLyricPath = ltrim ( $lyricPath, '.' );
				if (! file_put_contents ( $lyricPath, $lyric )) {
					return '作品上传失败，请重试';
				}
				$type = 1;
			}
			// 作品上传
			$arr = Input::file ( 'formName' );
			if (! empty ( $arr )) {
				// 判断作品类型，只能是MP3格式
				$my_file_type = my_file_type ( $arr->getRealPath () );
				if (empty ( $my_file_type ) || strtolower ( $my_file_type ) != 'mp3') {
					return '请上传正确文件类型';
				}
				$ext = 'mp3';
				$oName = time () . uniqid ();
				$name = $oName . '.' . $ext;
				$opusDir = $this->isExistDir ( 'poem' );
				$lastOpusPath = $opusDir . $name;
				$dataOpusPath = ltrim ( $lastOpusPath, '.' );
				$arr->move ( $opusDir, $name );
			} else {
				return '作品上传失败，请重试';
			}
			include './../app/commands/PinYin.php';
			$pinYin = new Pinyin ();
			$firstchar = @$pinYin->getFirstChar ( $pinyinName );
			$allchar = @$pinYin->getPinyin ( $pinyinName );
			$tmpPyArr = explode ( ' ', $allchar );
			$str = null;
			foreach ( $tmpPyArr as $k => $v ) {
				if (empty ( $v )) {
					continue;
				}
				$str .= substr ( $v, 0, 1 );
			}
			//选出伴奏读者，写者
			if($poemId >= 100000001){
				try {
					$writer_uid  = DB::table('opus_poetry')->where('id',$poemId)->pluck('uid');
					$writer = DB::table('user')->where('id',$writer_uid)->pluck('nick');
				} catch (Exception $e) {
				}
			}else{
				$writer = DB::table('poem')->where('id',$poemId)->pluck('writername');
			}
			$writer = !empty($writer) ? $writer : '佚名';
			// 作品入库
			$time = time ();
			$data = array (
					'uid' => $uid,
					'name' => $opusName,
					'firstchar' => $firstchar,
					'pinyin' => $str,
					'lyricurl' => $dataLyricPath,
					'poemId' => $poemId,
					'url' => $dataOpusPath,
					'opustime' => $opusTime,
					'type' => $type,
					'addtime' => $time,
					'reader'=>$info['nick'],
					'writer'=>$writer,
			);
			//写入草稿箱  建表
			$opusId = DB::table ( 'draft' )->insertGetId ( $data );
			// if ($opusId) {
			// 	// 作品数+1,最新作品修改名称
			// 	DB::table('user')->where('id',$uid)->increment('opusnum',1,array('opusname'=>$opusName));
			// 	// 作品分类操作,来源为普通伴奏poemId<100000001，插入分类，否则poemId>=100000001插入自由诵读
			// 	$this->insertCat ( $poemId, $opusId,$uid );
			// 	//增加作品收听数
			// 	$this->incLisNum($opusId);
			// } else {
			// 	return '作品上传失败，请重试';
			// }
			if ($opusId=="") {
				return false;
			}else{
				return true;
			}
			// // 定制听中增加作品
			// $arr = array (
			// 		'uid' => $uid,
			// 		'opusid' => $opusId,
			// 		'opustype' => 0,
			// 		'repuid' => $uid,
			// 		'isdel' => 0,
			// 		// 'isself'=>1,
			// 		'addtime' => time () 
			// );
			// DB::table ( 'personalcustom' )->insert ( $arr );
			// if (! empty ( $competitionId )) {
			// 	$tmpStr = trim ( $competitionId );
			// 	$tmpRs = explode ( ',', $tmpStr );
			// 	$sql = "insert into competition_opus_rel (competitionid,opusid,uid) values ";
			// 	$tmpStr = ' ';
			// 	foreach ( $tmpRs as $k => $v ) {
			// 		$tmpStr .= '(' . $v . ',' . $opusId . ',' . $uid . '),';
			// 	}
			// 	$sql .= rtrim ( $tmpStr, ',' );
			// 	DB::insert ( $sql );
			// }
			// return $data;
		} else {
			return 'nolog';
		}
	}
	// 草稿列表
	//就看自己作品
	public function getDraftList() {
		$count = ! empty ( Input::get ( 'count' ) ) ? intval(Input::get ( 'count' )) : 20 ;
		$pageIndex = ! empty ( Input::get ( 'pageIndex' ) ) ? intval(Input::get ( 'pageIndex' )) : 1 ;
		$offSet = ($pageIndex - 1) * $count;
		++ $count;
		$userid = 0;
		$info = $this->viaCookieLogin ();
		if (! empty ( $info )){
			$uid = $info ['id'];
		}
		$rs = DB::table('draft')->where('uid',$uid)->orderBy('id','desc')->skip($offSet)->take($count)->get();
		foreach($rs as $k=>&$v){

			$v['lyricurl']= 'http://'.$this->url .$v['lyricurl'];
			$v['url']= 'http://'. $this->url .$v['url'];
		}
		if (! empty ( $rs )) {
			$num = count ( $rs );
			if ($num >= $count) {
				array_pop ( $rs );
				$rs['hasmore'] = 1;
			} else {	
				$rs['hasmore'] = 0;
			}
			 return $rs;
		}else{
			return 'nodata';
		}
	}
	// 删除作品 
	public function delDraft() {
		$info = $this->viaCookieLogin ();
		$url=Config::get('app.url');
		if (! $info)
			return 'nolog';
		$uid = $info ['id'];
		if (!$_POST['opusId']){
				return 'noID';
			}
		$opusId = intval($_POST['opusId']);
		$opus=DB::table('draft')->where('id','=',$opusId)->where("uid",'=',$uid)->first();
		if($opus){
			if(file_exists(public_path(trim($opus['url'],'/')))){
				if(!unlink(public_path(trim($opus['url'],'/')))) return '删除失败';
			}

			$a=DB::table('draft')->where('id','=',$opusId)->where("uid",'=',$uid)->delete();
			if($a){
				return true;
			}else{
				return '删除记录失败';
			}
		}
 

	}
  
  	// 正式发布
	 public function  toOpus(){
			$competitionId = intval(Input::get ( 'competitionId',0 ));		 
			if (!Input::has('opusId')){
				return 'noID';
			}
		 	$Id = intval(Input::get('opusId'));
		 	$info=DB::table("draft")->where('id','=',$Id)->first();
			unset($info['id']);
			$info['addtime']=time();
			$opusId = DB::table ( 'opus' )->insertGetId ($info);
			if ($opusId) {
					// 作品数+1,最新作品修改名称
					DB::table('user')->where('id',$info['uid'])->increment('opusnum',1,array('opusname'=>$info['name']));
					// 作品分类操作,来源为普通伴奏poemId<100000001，插入分类，否则poemId>=100000001插入自由诵读
					$this->insertCat ( $info['poemid'], $opusId,$info['uid'] );
					//增加作品收听数
					$this->incLisNum($opusId);
					DB::table('draft')->where('id','=',$Id)->delete();
					$apiEsSearch = App::make('apiEsSearch');
					$apiEsSearch->addEsOpus(['id'=>$Id,'name'=>$info['name'],'pinyin'=>$info['pinyin']]);
				} else {
					return '作品上传失败，请重试';
				}
				
				// 定制听中增加作品
				$arr = array (
						'uid' => $info['uid'],
						'opusid' => $opusId,
						'opustype' => 0,
						'repuid' => $info['uid'],
						'isdel' => 0,
						// 'isself'=>1,
						'addtime' => time()
				);
				DB::table ( 'personalcustom' )->insert ( $arr );
			// 参赛 '1,2,3',如果是夏青杯，检测权限等	
			if ($competitionId  !=0) {
				$apiCheckPermission = new ApiCheckPermission();
				$flag = $apiCheckPermission->check_permission($competitionId);
				// $apiCompetition = new ApiCompetition ();
				// $flag = $apiCompetition->dif_check_competition ( $competitionId );
				if ($flag ['status'] != 1) {
					return $flag ['message'];
				}
			}
			if ($competitionId  !=0)  {
				$tmpStr = trim ( $competitionId );
				$tmpRs = explode ( ',', $tmpStr );
				$sql = "insert into competition_opus_rel (competitionid,opusid,uid) values ";
				$tmpStr = ' ';
				foreach ( $tmpRs as $k => $v ) {
					$tmpStr .= '(' . $v . ',' . $opusId . ',' . $info['uid'] . '),';
				}
				$sql .= rtrim ( $tmpStr, ',' );
				DB::insert ( $sql );
			}
			
			return $opusId ;

	 }




	 	// 将作品导航分类
	protected function insertCat($poemId, $opusId,$uid) {
		if (empty ( $poemId ) || empty ( $opusId ) || empty($uid))
			return;
		$poemId = intval($poemId);
		$opusId = intval($opusId);
		//如果poemId>=100000001 来源为诗文比赛，插入自由诵读
		$time = time ();
// 		if($poemId>=100000001){
// 			DB::table('navopusrel')->insert(array('categoryid'=>21,'opusid'=>$opusId,'poemid'=>$poemId,'addtime'=>$time));
// 			return;
// 		}
		// 根据原诗id查找导航分类
		$rs = DB::table('navpoemrel')->where('poemid',$poemId)->lists('navid');
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$tmp_table_id = $v%10;
				DB::table('nav_opus_table_id')->insert(array('opusid'=>$opusId,'table_id'=>$tmp_table_id));
				$table_name = 'nav_opus_'.$tmp_table_id;
				DB::table($table_name)->insert(array('uid'=>$uid,'opusid'=>$opusId,'categoryid'=>$v,'commentnum'=>0,'lnum'=>1,'repostnum'=>0,'praisenum'=>0,'addtime'=>$time,'poemid'=>$poemId));
			}
		}
		return;
	}
	
	// 收听作品，作品收听次数+1 人总收听次数+1
	public function incLisNum($opusId = 0) {
		if (empty ( $opusId ))
			return false;
		$opusId = intval ( $opusId );
		$uid = DB::table('opus')->where('id',$opusId)->pluck('uid');
		if (empty ( $uid ))
			return false;
		try {
			DB::table('user')->where('id',$uid)->increment('lnum',1);
			DB::table('opus')->where('id',$opusId)->increment('lnum',1);
			//根据分类分表中作品收听数增加
			$this->lnumNavOpus($opusId);
			//user_league 朗诵会会员表中收听数增加
			DB::table('league_user')->where('uid',$uid)->increment('lnum',1);
			// 判断等级
			$this->setUserGrade ( $uid );
		} catch (Exception $e) {
		}
		
	}
	/**
	 * 根据分类分表中作品收听数增加
	 * @author:wang.hongli
	 * @since:2016/04/16
	 */
	protected  function lnumNavOpus($opusId){
		$table_ids = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists('table_id');
		if(empty($table_ids)) return;
		foreach($table_ids as $k=>$v){
			$table_name = 'nav_opus_'.$v;
			try {
				DB::table($table_name)->where('opusid',$opusId)->increment('lnum');
			} catch (Exception $e) {
			}
		}
	}
	
// 根据作品id获取作品信息
	public function  DraftIdGetInfo() {
		$info = $this->viaCookieLogin ();
		$errorMsg = '获取信息失败';
		$info['id']=2;
		if(empty($info)) return $errorMsg;
		$uid = $info['id'];
		if(!Input::has('opusId')) return $errorMsg;
		$opusId = intval(Input::get('opusId'));
		$opus_info = DB::table('draft')->where('id',$opusId)->first(array('id','uid','name','lyricurl','poemid','url','opustime','addtime'));
		if(empty($opus_info)) return $errorMsg;
		$user_info = DB::table('user')->where('id',$opus_info['uid'])->where('isdel','<>',1)->first(array('nick','portrait','sportrait','gender','grade','authtype','teenager','isleague'));
		if(empty($user_info)) return $errorMsg;
		$opus_info['lnum'] =0;
		$opus_info['praisenum'] = 0;
		$opus_info['repostnum'] = 0;
		$opus_info['commentnum'] = 0;
		$opus_info['sharenum'] = 0;
		$user_info['sportrait'] = $this->poem_url.ltrim($user_info['sportrait'],'.');
		$user_info['portrait'] = $this->poem_url.ltrim($user_info['portrait'],'.');
		$opus_info['lyricurl'] = $this->poem_url.ltrim($opus_info['lyricurl'],'.');
		$opus_info['url'] = $this->poem_url.ltrim($opus_info['url'],'.');
		$opus_info['colStatus'] = $this->isCollection($uid, $opusId);
		$opus_info['praStatus'] = $this->isPraise($uid, $opusId);
		$rs = array_merge($opus_info,$user_info);
		return $rs;
		
	}
}