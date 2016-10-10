<?php 

	/**
	* 和诗相关的模型
	**/
	class ApiPoem extends ApiCommon {

		//诗人性别分类列表
		public function getPoemerCat () {
			$sql = "select * from poemercat order by `sort`";
			$rs = DB::select($sql);
			if(!empty($rs)) {
				foreach($rs as $key=>&$value) {
					$value['icon'] = $this->poem_url.$value['icon'];
				}
			}
			return $rs;
		}

		//根据性别分类查找写者
		public function getWriterList() {
			$gender = Input::get('gender'); //1男2女 空全部
			$count = !empty(Input::get('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			if(3 != $gender) {
				$sql = "select writer.id,writer.name,writer.firstchar,writer.pinyin from wprel left join writer on wprel.writerid = writer.id where wprel.poemercatid = {$gender} order by writer.pinyin limit $offSet,$count";
			} else {
				$sql = "select writer.id,writer.name,writer.firstchar,writer.pinyin from wprel left join writer on wprel.writerid = writer.id order by writer.pinyin limit $offSet,$count";
			}
			$rs = DB::select($sql);
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
			return $rs;	
		}

		//根据性别分类查找读者
		public function getReaderList() {
			$gender = Input::get('gender');
			$count = !empty(Input::get('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			if(3 != $gender) {
				$sql = "select reader.id,reader.name,reader.firstchar,reader.pinyin from rprel left join reader on rprel.readerid = reader.id where rprel.poemercatid = {$gender} order by reader.pinyin limit $offSet,$count";
			} else {
				$sql = "select reader.id,reader.name,reader.firstchar,reader.pinyin from rprel left join reader on rprel.readerid = reader.id order by reader.pinyin limit $offSet,$count";
			}
			$rs = DB::select($sql);
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
			return $rs;
		}

		//根据写者id查找原诗
		public function getPoemByWriterId() {
			$writerId = Input::get('writerId');
			// $writerId = 1;
			if(empty($writerId)) return;
			$count = !empty(Input::get('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			$sql = "select poem.* from writepoemrel left join poem on writepoemrel.poemid = poem.id where writepoemrel.writerid = {$writerId} order by poem.addtime desc limit $offSet,$count";
			$rs = DB::select($sql);
			$data = $this->convUrlHasNext($rs,$count);
			if($data) {
				return $data;
			} else {
				return false;
			}
		}

		//根据读者id查找原诗
		public function getPoemByReaderId() {
			$readerId = Input::get('readerId');
			// $readerId = 1;
			if(empty($readerId)) return;
			$count = !empty(Input::get('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			$sql = "select poem.* from readpoemrel left join poem on readpoemrel.poemid = poem.id where readpoemrel.readerid = {$readerId} order by poem.addtime desc limit $offSet,$count";
			$rs = DB::select($sql);
			$data = $this->convUrlHasNext($rs,$count);
			if($data) {
				return $data;
			} else {
				return false;
			}
			
		}
		//原始诗下载次数统计
		public function poemDownNum() {
			if(!Input::has('poemId')) return '诗不存在';
			$poemId = intval(Input::get('poemId'));
			try {
				DB::table('poem')->where('id',$poemId)->increment('downnum');
			} catch (Exception $e) {
			}
			return true;
		}

		//根据原始诗分类获取诗列表
		public function getPoemListByNavId() {
			//按照时间排序分类
			$nav_arr = array(21,86,87,88,89);
			$count = !empty(Input::get('count')) ? intval(Input::get('count')) : 20;
			$pageIndex = !empty(Input::get('pageIndex')) ? intval(Input::get('pageIndex')) : 1;
			$offSet = ($pageIndex-1)*$count;
			++$count;
			if(!Input::has('navigationId')) {
				$rs = DB::table('poem')->orderBy('id','desc')->skip($offSet)->take($count)->get();
			} else {
				$navigationId = intval(Input::get("navigationId"));
				if(in_array($navigationId,$nav_arr)){
					$poem_ids = DB::table('navpoemrel')->where('navid',$navigationId)->lists('poemid');
					if(!empty($poem_ids)){
						$rs = DB::table('poem')->whereIn('id',$poem_ids)->orderBy('free_sort','asc')->skip($offSet)->take($count)->get();
					}
				}else{
					$poem_ids = DB::table('navpoemrel')->where('navid',$navigationId)->orderBy('poemid','desc')->skip($offSet)->take($count)->lists('poemid');
					$rs = DB::table('poem')->whereIn('id',$poem_ids)->orderBy('id','desc')->get();
				}
			}
			$data = $this->convUrlHasNext($rs,$count);
			if($data) {
				return $data;
			} else {
				return false;
			}
		}

		//根据原诗id获取原诗信息
		public function accorPoemGetInfo() {
			$poemId = Input::get('poemId');
			$flag = Input::get('flag');
			if(empty($poemId)) return '此诗不存在';
			//针对自由诵读特殊处理
// 			$nav_arr = array(21,86,87,88,89);
// 			$nav_id = DB::table('navpoemrel')->where('poemid',$poemId)->lists('navid');
// 			//交集
// 			$intersect = array_intersect($nav_arr, $nav_id);
// 			if(!empty($intersect)){
// 				return '按照国家有关部门要求，“自由诵读”功能暂停使用！';
// 			}
			//针对安卓特殊处理
			if(empty($flag) && $poemId>=100000001){
				return '获取信息失败';
			}
			//如果poemId>=100000001,从诗文比赛获取伴奏
			if($poemId>=100000001){
				$rs = DB::table('opus_poetry')->where('id',$poemId)->where('status',2)->first(array('id','competitionid','uid','title','author','nationality','lyric','click_num','comment_num','add_time','repost_num','praise_num'));
				if(empty($rs)){
					return '获取信息失败';
				}
				$rs['lyric'] = $this->poem_url.'/'.$rs['lyric'];
				$info = $this->viaCookieLogin();
				if(empty($info)){
					return '请登陆';
				}
				$uid = $info['id'];
				//判断是否点赞
				$redisOpusPoetry = new RedisOpusPoetry();
				$opus_poetry_ids = $redisOpusPoetry->getUserPraisePoetry($uid);
				$rs['ispraise'] = 0;
				if(!empty($opus_poetry_ids) && in_array($poemId,$opus_poetry_ids)){
					$rs['ispraise'] = 1;
				}
				//获取用户信息
				$user_info = DB::table('user')->where('id',$rs['uid'])->first(array('nick','gender','grade','portrait','sportrait','authtype','teenager','isleague'));
				$user_info['portrait'] = !empty($user_info['portrait']) ? $this->poem_url.ltrim($user_info['portrait'],'.') : '';
				$user_info['sportrait'] = !empty($user_info['sportrait']) ? $this->poem_url.ltrim($user_info['sportrait'],'.') : '';
				//数组合并
				return array_merge($rs,$user_info);
			}else{
				$rs = DB::table('poem')->where('id',$poemId)->where('isdel','<>',1)->first();
				if(empty($rs)) return '获取信息失败';
				$rs['burl'] = $this->poem_url.trim($rs['burl'],'.');
				$rs['yurl'] = $this->poem_url.trim($rs['yurl'],'.');
				$rs['lyricurl'] = $this->poem_url.trim($rs['lyricurl'],'.');
				return $rs;
			}
		}

		//补充新词
		public function supplementLyric() {
			$info = $this->viaCookieLogin();
			$type = Input::get('type'); //0，美文推荐 1佳作投稿 
			if(!empty($info)) {
				$uid = $info['id'];
			} else {
				$uid = 0;
			}
			$lyric = trim(Input::get('lyric'));
			if(empty($lyric)) return '补充的新词为空';
			$lyric = serialize($lyric);
			$time = time();
			$sql = "insert into addlyric (uid,lyric,addtime,type) values ({$uid},'{$lyric}',{$time},{$type})";
			if(DB::insert($sql)) {
				return 1;
			} else {
				return '添加失败，请重试';
			}
		}
		
		//诗经板块查询
		public function getShiJingList() {
			//按照时间排序分类
			$data = array();
			//诗经表
			$ids=array();
			$sql="select * from race_shijing";
			$rs=DB::select($sql);
			if(!empty($rs)){
				foreach($rs as $v){
					$ids[]=$v["poem_id"];
				}
				//查询
				$sql = "select * from poem where id in ('".implode("','",$ids)."') order by downnum desc";
				$rs = DB::select($sql);
				foreach($rs as $v){
					$v['burl'] = $this->poem_url.$v['burl'];
					$v['yurl'] = $this->poem_url.$v['yurl'];
					$v['lyricurl'] = $this->poem_url.$v['lyricurl'];
					$data[] = $v;
				}
			}
			if($data) {
				return $data;
			} else {
				return false;
			}
		}
		/**
		 * [getWriterInfoById description]
		 * @param  array  $params [作品相关数组]
		 * @return [type]         [获取伴奏id为主键的数组]
		 */
		public static function getWriterInfoById($params=array())
		{
			$data = array();
			if(!empty($params))
			{
				$tmp_poem_id = array();
				foreach($params as $key=>$value)
				{
					if(!in_array($value['poemid'],$tmp_poem_id) && $value['type'] == 0)
					{
						$tmp_poem_id[] = $value['poemid'];
					}
				}
				if(!empty($tmp_poem_id))
				{
					$tmp_data = DB::table('poem')->whereIn('id',$tmp_poem_id)->select('id','writername')->get();
				}
				if(!empty($tmp_data))
				{
					foreach($tmp_data as $k=>$v)
					{
						$data[$v['id']] = $v['writername'];
					}
				}
			}
			return $data;
		}

		//得到伴奏读者信息
    public function getPoemUserInfo($poemid){
        $poem_info=DB::table('readpoemrel')->where('poemid',$poemid)->first();
        $user_info=DB::table('user_reader_rel')->where('reader_id',$poem_info['readerid'])->first();
        if($user_info){
            $user=DB::table('user')->where('id',$user_info['reader_id'])->first(['id','nick','gender','grade','sportrait','authtype','isleague']);
            $rs=array('id'=>$user['id'],'nick'=>$user['nick'],'gender'=>$user['gender'],'grade'=>$user['grade'],'sportrait'=>$user['sportrait'],'authtype'=>$user['authtype'],'isleague'=>$user['isleague']);
            return $rs;
        }else{
            return NULL;
        }
        

    }
	}