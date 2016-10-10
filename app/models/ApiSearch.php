<?php 
/**
* 全局搜索模型
**/
class ApiSearch extends ApiCommon {

	
	public function search(){
		$info = $this->viaCookieLogin();
		if($info){
			$uid = $info['id'];
		}else{
			$uid = 0;
		}
		$flag = Input::get('flag');
		$keywords = trim(Input::get('keywords'));
		if(empty($keywords)) return '请输入搜索关键';
		$count =20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		if(empty($keywords)) return '请输入搜索关键';
		$apiEsSearch = new ApiEsSearch();
		// $apiEsSearch = 1;
		$apiEsSearch = App::make('apiEsSearch');
		switch ($flag) {
			case 1:
				$poem_ids = $apiEsSearch->searchPoem(['keywords'=>$keywords,'pageIndex'=>$pageIndex,'count'=>$count]);
				if(empty($poem_ids)){
					return '结果为空';
				}
				$poem_ids_str = implode(',', $poem_ids);
				$rs = DB::table('poem')->whereIn('id',$poem_ids)->orderBy(DB::raw("FIELD(id,$poem_ids_str)"))->get();
				$data = $this->convUrlHasNext($rs,$count);
				return $data;
				break;
			case 2:
				$opus_ids = $apiEsSearch->searchOpus(['keywords'=>$keywords,'pageIndex'=>$pageIndex,'count'=>$count]);
				if(empty($opus_ids)){
					return '结果为空';
				}
				$opus_ids_str = implode(',', $opus_ids);
				$rs = DB::table('opus')->whereIn('id',$opus_ids)->orderBy(DB::raw("FIELD(id,$opus_ids_str)"))->get();
				if(!empty($rs))
				{
					$uid_array = array();
					foreach($rs as $k=>$v)
					{
						$uid_array[$v['uid']] = $v['uid'];
					}
					// 查询用户结果
					$user_rs_tmp = DB::table('user')
								->select('id','nick','gender','grade','sportrait','authtype','teenager','isleague')
								->whereIn('id',$uid_array)
								->get();
					$user_rs = array();
					if(!empty($user_rs_tmp))
					{
						foreach($user_rs_tmp as $key=>$value)
						{
							$user_rs[$value['id']] = $value;
						}
					}
					foreach($rs as $key=>$value)
					{
						$rs[$key]['nick'] = isset($user_rs[$value['uid']]['nick']) ? $user_rs[$value['uid']]['nick'] :'';
						$rs[$key]['gender'] = isset($user_rs[$value['uid']]['gender']) ? $user_rs[$value['uid']]['gender'] : 1;
						$rs[$key]['grade'] = isset($user_rs[$value['uid']]['grade'])? $user_rs[$value['uid']]['grade'] : 1;
						$rs[$key]['sportrait'] = isset($user_rs[$value['uid']]['sportrait']) ? $user_rs[$value['uid']]['sportrait'] : null;
						$rs[$key]['authtype'] = isset($user_rs[$value['uid']]['authtype']) ? $user_rs[$value['uid']]['authtype'] : 0;
						$rs[$key]['teenager'] = isset($user_rs[$value['uid']]['teenager']) ? $user_rs[$value['uid']]['teenager'] : 1;
						$rs[$key]['isleague'] = isset($user_rs[$value['uid']]['isleague']) ? $user_rs[$value['uid']]['isleague'] : 0;
					}
					//根据伴奏id获取伴奏信息
					//$writeArr = ApiPoem::getWriterInfoById($rs);
					foreach($rs as $key=>&$value) {
						$tmpRs = array();
						$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.trim($value['sportrait'],'.') : null;
						$value['lyricurl'] = $this->poem_url.$value['lyricurl'];
						$value['url'] = $this->poem_url.$value['url'];
						$value['colStatus'] = $this->isCollection($uid,$value['id']); //是否收藏0没有1有
						$value['praStatus'] = $this->isPraise($uid,$value['id']);//是否赞0没有1有
						$value['writername'] = isset($value['writername']) ? $value['writername'] : null;
					}
					unset($value);
					$num = count($rs);
					if($num>=$count) {
						array_pop($rs);
						$rs['hasmore'] = 1;
					} else {   
						$rs['hasmore'] = 0;
					}
					return $rs;
				}else{
					return '结果为空';
				}
				break;
			case 3:
				$user_ids = $apiEsSearch->searchUser(['keywords'=>$keywords,'pageIndex'=>$pageIndex,'count'=>$count]);
				if(empty($user_ids)){
					return '结果为空';
				}
				$user_id_string = implode(',', $user_ids);
				$rs = DB::table('user')->whereIn('id',$user_ids)->orderBy(DB::raw("FIELD(id,$user_id_string)"))->get(['id','nick','gender','lnum','repostnum','attention','praisenum','fans','opusnum','grade','sportrait','portrait','signature','authtype','addtime','teenager','isleague']);
				if(!empty($rs)) {
					foreach($rs as $key=>&$value) {
						$value['portrait'] = !empty($value['portrait']) ?  $this->poem_url.ltrim($value['portrait'],'.') : '';
						$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
						$value['bgpic'] = !empty($value['bgpic']) ? $this->poem_url.ltrim($value['bgpic']) : '' ;
						//判断关注状态
						if(!empty($uid)) {
							$value['relation'] = $this->attentionStatus($uid,$value['id']); //关注状态0陌生人，1我->他 2，他->我 3->相互
						} else {
							$value['relation'] = 0;
						}
					}
					unset($value);
					$num = count($rs);
					if($num>=$count) {
						array_pop($rs);
						$rs['hasmore'] = 1;
					} else {
						$rs['hasmore'] = 0;
					}
					return $rs;
				} else {
					return '结果为空';
				}
				break;
			case 4:
				$data = dealPostData();
				$data['pageIndex'] = !empty($data['pageIndex']) ? intval($data['pageIndex']) : 1;
				$data['count'] =  20;
				$rs = $this->searchCompetiton($data);
				return $rs;
				# code...
				break;
			case 5:
				if(empty($uid)){
					return "结果为空";
				}				
				$like = '%'.$keywords.'%';//乡愁select name from opus where uid = 12 and id=27;
				$rs = DB::table('opus')->where('uid',$uid)->where('name','like',$like)->where('isdel','=','0')->orderBy('id','desc')
				// ->select('id','uid','lyricurl','url','opustime','lnum','commentnum','repostnum','praisenum','name','addtime')
				->get();
				if(!empty($rs)){
					//获取用户收藏的所有作品
					$collection_opus = ApiCommonStatic::isCollection_v2($uid);
					// 获取登录用户赞过的所有作品
					$praise_opus = ApiCommonStatic::isPraise_v2($uid);
					$opusid = array();
					// $opus = $rs;
					foreach ($rs as $key => $value) {
						$opusid[] = $value['id'];
					}
					$opus_param = DB::table('opus_param')->whereIn('opusid',$opusid)->select('opusid','flower','istop')->get();
					$opus_paramids =  array();
					foreach ($opus_param as $key => $value) {
						$opus_paramids[$value['opusid']] = $value;
					}
					foreach ($rs as $key => &$value){
						$value ['lyricurl'] = $this->poem_url . $value ['lyricurl'];
						$value ['url'] = $this->poem_url.$value ['url'];
						$value ['colStatus'] = in_array($value['id'],$collection_opus) ? 1 : 0;
						$value ['praStatus'] = in_array($value['id'],$praise_opus) ? 1 : 0;
						if (empty($opus_paramids[$value['id']]['flower'])) {
							$value ['flower'] = 0;
						}else{
							$value ['flower'] = $opus_paramids[$value['id']]['flower'];
						}
						if (empty($opus_paramids[$value['id']]['istop'])) {
							$value ['istop'] = 0;
						}else{
							$value ['istop'] = $opus_paramids[$value['id']]['istop'];
						}

						// $value ['istop'] = $opus_paramids[$value['id']]['istop'];
						// $opusid[] = $value['id'];
					}
					unset($value);
					$num = count($rs);
					if($num>=$count){
						$rs['hasmore'] = 1;
					}else{
						$rs['hasmore'] = 0;
					}
					return $rs;
				}else{
					return "结果为空";
				}
			break;
		}

	}
	//1,搜伴奏2，搜作品 3搜用户 4,根据比赛pid搜比赛
	public function searchv2() {
		$info = $this->viaCookieLogin();
		if($info) {
			$uid = $info['id'];
		} else {
			$uid = 0;
		}
		$flag = Input::get('flag');
		$keywords = mb_strtoupper(trim(Input::get('keywords')),'utf-8');
		if(empty($keywords)) return '请输入搜索关键';
		$count =20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$offSet = ($pageIndex-1)*$count;
		++$count;
		switch ($flag) {
			case 1:
				$sql = "select * from poem where (aliasname like '%{$keywords}%' or allchar like '%{$keywords}%' or writername like '%{$keywords}%' or spelling like '%{$keywords}%' or readerallchar like '%{$keywords}%' or writerallchar like '%{$keywords}%') and isdel !=1 order by id desc  limit $offSet,$count";
				$rs = DB::select($sql);
				$data = $this->convUrlHasNext($rs,$count);
				if($data) {
					return $data;
				} else {
					return '结果为空';
				}
				break;
			case 2:
				// 判断关键词是拼音还是汉字
				$pattern = '/[a-z]+/i';
				$where = " and opus.name like '%{$keywords}%' ";
				if(preg_match($pattern,$keywords))
				{
					$where = " and opus.pinyin like '%{$keywords}%' ";
				}
				$sql = "select opus.id,opus.uid,opus.name,opus.firstchar,opus.pinyin,opus.lnum,opus.praisenum,opus.repostnum,opus.lyricurl,opus.poemid,opus.type,opus.url,opus.opustime,opus.addtime,opus.commentnum,opus.writer as writername from opus where opus.isdel != 1 $where order by id desc limit $offSet,$count";
				$rs = DB::select($sql);
				if(!empty($rs))
				{
					$uid_array = array();
					foreach($rs as $k=>$v)
					{
						$uid_array[$v['uid']] = $v['uid'];
					}
					// 查询用户结果
					$user_rs_tmp = DB::table('user')
								->select('id','nick','gender','grade','sportrait','authtype','teenager','isleague')
								->whereIn('id',$uid_array)
								->get();
					$user_rs = array();
					if(!empty($user_rs_tmp))
					{
						foreach($user_rs_tmp as $key=>$value)
						{
							$user_rs[$value['id']] = $value;
						}
					}
					foreach($rs as $key=>$value)
					{
						$rs[$key]['nick'] = isset($user_rs[$value['uid']]['nick']) ? $user_rs[$value['uid']]['nick'] :'';
						$rs[$key]['gender'] = isset($user_rs[$value['uid']]['gender']) ? $user_rs[$value['uid']]['gender'] : 1;
						$rs[$key]['grade'] = isset($user_rs[$value['uid']]['grade'])? $user_rs[$value['uid']]['grade'] : 1;
						$rs[$key]['sportrait'] = isset($user_rs[$value['uid']]['sportrait']) ? $user_rs[$value['uid']]['sportrait'] : null;
						$rs[$key]['authtype'] = isset($user_rs[$value['uid']]['authtype']) ? $user_rs[$value['uid']]['authtype'] : 0;
						$rs[$key]['teenager'] = isset($user_rs[$value['uid']]['teenager']) ? $user_rs[$value['uid']]['teenager'] : 1;
						$rs[$key]['isleague'] = isset($user_rs[$value['uid']]['isleague']) ? $user_rs[$value['uid']]['isleague'] : 0;
					}
				}
				if(!empty($rs)) {
					//根据伴奏id获取伴奏信息
// 					$writeArr = ApiPoem::getWriterInfoById($rs);
					foreach($rs as $key=>&$value) {
						$tmpRs = array();
						$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.trim($value['sportrait'],'.') : null;
						$value['lyricurl'] = $this->poem_url.$value['lyricurl'];
						$value['url'] = $this->poem_url.$value['url'];
						$value['colStatus'] = $this->isCollection($uid,$value['id']); //是否收藏0没有1有
						$value['praStatus'] = $this->isPraise($uid,$value['id']);//是否赞0没有1有
						$value['writername'] = isset($value['writername']) ? $value['writername'] : null;
					}
					unset($value);
					$num = count($rs);
					if($num>=$count) {
						array_pop($rs);
						$rs['hasmore'] = 1;
					} else {
						$rs['hasmore'] = 0;
					}
					return $rs;
				} else {
					return '结果为空';
				}
				break;
			case 3:
				$pattern = '/^[a-z]+$/i';
				$where = " and nick like '%{$keywords}%' ";
				if(preg_match($pattern,$keywords))
				{
					$where = " and pinyin like '%{$keywords}%' ";
				}
				$sql = "select id,nick,gender,lnum,repostnum,attention,praisenum,fans,opusnum,grade,sportrait,portrait,signature,authtype,addtime,teenager,isleague from user where isdel != 1 $where order by id desc limit $offSet,$count";
				$rs = DB::select($sql);
				if(!empty($rs)) {
					foreach($rs as $key=>&$value) {
						$value['portrait'] = !empty($value['portrait']) ?  $this->poem_url.ltrim($value['portrait'],'.') : '';
						$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
						$value['bgpic'] = !empty($value['bgpic']) ? $this->poem_url.ltrim($value['bgpic']) : '' ;
						//判断关注状态
						if(!empty($uid)) {
							$value['relation'] = $this->attentionStatus($uid,$value['id']); //关注状态0陌生人，1我->他 2，他->我 3->相互
						} else {
							$value['relation'] = 0;
						}
					}
					unset($value);
					$num = count($rs);
					if($num>=$count) {
						array_pop($rs);
						$rs['hasmore'] = 1;
					} else {
						$rs['hasmore'] = 0;
					}
					return $rs;
				} else {
					return '结果为空';
				}
				break;
			case 4:
				$data = dealPostData();
				$data['pageIndex'] = !empty($data['pageIndex']) ? intval($data['pageIndex']) : 1;
				$data['count'] =  20;
				$rs = $this->searchCompetiton($data);
				return $rs;
		}
	}
	
	/**
	 * 搜索比赛列表
	 * @author:wang.hongli
	 * @since:2016/05/30
	 */
	protected function searchCompetiton($data){
		if(empty($data)){
			return '搜索错误';
		}
		$rules = array(
				'pageIndex'=>'integer',
				'count'=>'integer',
				'pid'=>'required|integer',
				'keywords'=>'required'
		);
		$message = array(
				'pageIndex.integer'=>'分页格式错误',
				'count'=>'每页显示条数错误',
				'pid.required'=>'比赛id错误',
				'pid.integer'=>'比赛id格式错误',
				'keywords.required'=>'请输入关键字',
		);
		//验证
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			$msg =  $validator->messages()->first();
			return $msg;
		}
		$pageIndex = $data['pageIndex'];
		$count = $data['count'];
		$count++;
		$pid = $data['pid'];
		$keywords = $data['keywords'];
	
		$conn = DB::table('competitionlist')->where('pid',$pid);
		if(!empty($data['keywords'])){
			$keywords = '%'.$data['keywords'].'%';
			$conn->where('name','like',$keywords);
		}
		$pageIndex = !empty($pageIndex) ? intval($pageIndex) : 1;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		//结束的在最下面,按时间倒序排序
		$conn->orderBy('isfinish','asc')->orderBy('sort','desc')->orderBy('starttime','desc');
		$rs = $conn->skip($offSet)->take($count)->get();
		if(!empty($rs)){
			//获取商品列表
			$apiGoods = new ApiGoods();
			$goods = $apiGoods->getCompGoodsList(0);
			//选出比赛所属类型 type_id:1,2,3
			$tmp_type_ids = DB::table('competition')->select('id','type_id')->get();
			$type_ids = array();
			foreach($tmp_type_ids as $k=>$v){
				$type_ids[$v['id']] = $v['type_id'];
			}
				
			foreach($rs as $key=>$value){
				//主图
				$rs[$key]['mainpic'] = !empty($value['mainpic']) ? $this->poem_url.ltrim($value['mainpic'],'.') : '';
				//轮播图
				if(!empty($value['piclist'])){
					$tmp = explode(';', $value['piclist']);
					unset($rs[$key]['piclist']);
					foreach($tmp as $k=>$v){
						$rs[$key]['piclist'][] = !empty($v) ? $this->poem_url.ltrim($v,'.') : '';
					}
				}
				//商品id
				$rs[$key]['goods_id'] = isset($goods[$value['id']]['id']) ? $goods[$value['id']]['id'] : 0;
				if(!empty($value['clause_title']))
				{
					$rs[$key]['clause_url'] = $this->url."/admin/getMatchClause/".$value['id'];
				}
				//比赛类型
				$rs[$key]['type_id'] = isset($type_ids[$value['pid']]) ? $type_ids[$value['pid']] : 0;
			}
		}else{
			$rs = array();
		}
		if(count($rs) >= $count){
			array_pop($rs);
			$rs['hasmore'] = 1;
		}else{
			$rs['hasmore'] = 0;
		}
		//判断是否含有下一页
		return $rs;
	}


	//诗学院颂学院搜索
	public function collegeSearch($id,$name,$count,$offSet){
			$college=DB::table('college_list')->where('id','=',$id)->first();
			if($college["pid"]==0){
					$class=DB::table('college_list')->where('pid',$id)->get();
					$class_id=array();
					foreach($class as $k=>$v){
						$class_id[]=$v['id'];
					}
				 	
					$list=DB::table('class_active')->whereIn('pid',$class_id)->where('name','like','%'.$name.'%')->skip($offSet)->take($count)->get();
					foreach ($list as $key => $value) {
						$list[$key]['piclist']=unserialize($value['piclist']);
					}
					return $list;

			}else{
					$list= DB::table('class_active') ->where('pid',$id)->where('name','like','%'.$name.'%')->skip($offSet)->take($count)->get();
						foreach ($list as $key => $value) {
						$list[$key]['piclist']=unserialize($value['piclist']);
					}
					return $list;
			}
			
	}
}