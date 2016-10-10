<?php 
/**
*	账单明细
*	@author :sunzhen
*   @since :2016/08/21
**/
class BillDetailsController extends BaseController{
	public function iamondWater(){
		header("content-type:text/html;charset=utf8");
		/*****************接收表单****************/
		$fromid = Input::get('fromid','');
		$toid = Input::get('toid','');
		$fromidnick = trim(Input::get('fromidnick',''));
		$toidnick = trim(Input::get('toidnick',''));
		$opusname = trim(Input::get('opusname',''));
		$poemname = trim(Input::get('poemname',''));
		$readername = trim(Input::get('readername',''));
		$goodname = trim(Input::get('goodname',''));
		$orderid = trim(Input::get('orderid',''));
		$input_startime = Input::get('starttime');
		$input_endtime = Input::get('endtime');
		$starttime = strtotime($input_startime);
		$endtime = strtotime($input_endtime);
		$flag = intval(Input::get('flag',-1));
		$return = array('fromid'=>$fromid,'toid'=>$toid,'fromidnick'=>$fromidnick,'toidnick'=>$toidnick,'opusname'=>$opusname,'goodname'=>$goodname,'orderid'=>$orderid,'starttime'=>$input_startime,'endtime'=>$input_endtime,'flag'=>$flag,'poemname'=>$poemname,'readername'=>$readername);
		
		$iamond = DB::table('user_diamond_list')->orderBy('id','desc');
		$max_page = $iamond->max('id');

		if(!empty($fromid)){	
			$iamond->where('fromid','=',$fromid);
			$max_page = $iamond->count();
		}
		if(!empty($toid)){	
			$iamond->where('toid','=',$toid);
			$max_page = $iamond->count();
		}
		//判断搜索送花人
		if(!empty($fromidnick)){
			$user_rs = DB::table('user')->where('nick','=',$fromidnick)->select('id','nick')->get();
			$tmp_fromid = array();
			if(!empty($user_rs)){
				foreach($user_rs as $k=>$v){
					$tmp_fromid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_fromid)){
				return Redirect::to('/admin/defaultError')->with('message',"用户名不存在");
			}
		}
		if(!empty($tmp_fromid)){
			$iamond->whereIn('fromid',$tmp_fromid);
			$max_page = $iamond->count();
		}
		//判断搜索收花人
		if(!empty($toidnick)){
			$usertoname = DB::table('user')->where('nick','=',$toidnick)->select('id','nick')->get();
			$tmp_toid = array();
			if(!empty($usertoname)){
				foreach ($usertoname as $k => $v) {
					$tmp_toid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_toid)){
				return Redirect::to('/admin/defaultError')->with('message','用户名不存在');
			}
		}
		if(!empty($tmp_toid)){
			$iamond->whereIn('toid',$tmp_toid);
			$max_page = $iamond->count();
		}
		//作品
		if(!empty($opusname)){
			$opusname = DB::table('opus')->where('name','=',$opusname)->select('id','name')->get();
			$tmp_pusid = array();
			if(!empty($opusname)){
				foreach ($opusname as $k => $v) {
					$tmp_pusid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_pusid)){
				return Redirect::to('/admin/defaultError')->with('message','作品名为空');
			}
		}
		if(!empty($tmp_pusid)){
			$iamond->whereIn('opusid',$tmp_pusid);
			$max_page = $iamond->count();
		}
		//伴奏歌名
		if(!empty($poemname)){
			$poem = DB::table('poem')->where('name','=',$poemname)->select('id','name')->get();
			$tmp_poemid = array();
			if (!empty($poem)) {
				foreach ($poem as $k => $v) {
					$tmp_poemid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_poemid)){
				return Redirect::to('/admin/defaultError')->with('message','伴奏歌名为空');
			}
		}
		if(!empty($tmp_poemid)) {
			$iamond->whereIn('poemid',$tmp_poemid);
			$max_page = $iamond->count();
		}
		//伴奏人名
		if(!empty($readername)){
			$reader = DB::table('reader')->where('name','=',$readername)->select('id','name')->get();
			$tmp_readerid = array();
			if(!empty($reader)){
				foreach ($reader as $k => $v) {
					$tmp_readerid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_readerid)){
				return Redirect::to('/admin/defaultError')->with('message','伴奏人名为空');
			}
		}
		if(!empty($tmp_readerid)){
			$iamond->whereIn('reader_id',$tmp_readerid);
			$max_page = $iamond->count();
		}
		//商品名称
		if(!empty($goodname)){
			$goods = DB::table('goods')->where('name','=',$goodname)->select('id','name')->get();
			$tmp_goodsid = array();
			if(!empty($goodname)){
				foreach($goods as $k=>$v){
					$tmp_goodsid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_goodsid)){
				return Redirect::to('/admin/defaultError')->with('message','商品不存在');
			}
		}
		if(!empty($tmp_goodsid)){
			$iamond->whereIn('good_id',$tmp_goodsid);
			$max_page = $iamond->count();
		}
		//订单号
		if(!empty($orderid)){
			$iamond->where('orderid',$orderid);
			$max_page = $iamond->count();
		}
		//时间
		if($starttime && $endtime){
			$iamond = $iamond->where('time','>=',$starttime);
			$iamond = $iamond->where('time','<=',$endtime+86399);
			$max_page = $iamond->count();
		}elseif($starttime) {
			$iamond->where('time','>=',$starttime);
			$max_page = $iamond->count();
		}elseif($endtime){
			$iamond->where('time','<=',$endtime+86399);
			$max_page = $iamond->count();
		}
		//类型
		if($flag>-1){
			$iamond->where('flag','=',$flag);
			$max_page = $iamond->count();
		}
		/****************************列表展示*************************/
		$currentPage = Input::get('page',1);
		$pagesize = 20;
		$offSet = ($currentPage-1)*$pagesize;
		$iamond = $iamond->skip($offSet)->take($pagesize)->get();
		//->paginate(20);
		$paginator = Paginator::make($iamond,$max_page, $pagesize);
		//送钻石
		$fromid = array();
		foreach ($iamond as $k => $v) {
			$fromid[] = $v['fromid'];
		}
		if(empty($fromid)){
			return Redirect::to('/admin/defaultError')->with('message',"用户名不存在");
		}
		$userinfo = DB::table('user')->whereIn('id',$fromid)->select('id','nick')->get();
		$userinfos = array();
		foreach ($userinfo as $k => $v) {
			$userinfos[$v['id']] = $v;
		}

		//收钻石
		$toid = array();
		foreach ($iamond as $k => $v) {
			$toid[] = $v['toid'];
		}
		$usertoid = DB::table('user')->whereIn('id',$toid)->select('id','nick')->get();
		$usertoids = array();
		foreach ($usertoid as $k => $v) {
			$usertoids[$v['id']] = $v;
		}


		$opusid = array();
		foreach($iamond as $k => $v){
			$opusid[] = $v['opusid'];
		}
		$opuslist = DB::table('opus')->whereIn('id',$opusid)->get();
		$opusids = array();
		foreach ($opuslist as $k => $v) {
			$opusids[$v['id']] = $v;
		}

		$goodid = array();
		foreach ($iamond as $k => $v) {
			$goodid[] = $v['good_id'];
		}
		$goodlist = DB::table('goods')->whereIn('id',$goodid)->get();
		$goodids = array();
		foreach ($goodlist as $k => $v) {
			$goodids[$v['id']] = $v;
		}

		//伴奏歌名
		$poemid = array();
		foreach ($iamond as $k => $v) {
			$poemid[] = $v['poemid'];
		}
		$poemlist = DB::table('poem')->whereIn('id',$poemid)->get();
		$poemids = array();
		foreach ($poemlist as $k => $v) {
			$poemids[$v['id']] = $v;
		}
		//伴奏姓名
		$reader_id = array();
		foreach ($iamond as $k => $v) {
			$reader_id[] = $v['reader_id'];
		}
		$readlist = DB::table('reader')->whereIn('id',$reader_id)->get();
		$readerids = array();
		foreach ($readlist as $k => $v) {
			$readerids[$v['id']] = $v;
		}
		//展示模板数据
		$iamonds = array();
		foreach ($iamond as $k => $v) {	
			$iamonds[$k]['id'] = $v['id'];
			$iamonds[$k]['num'] = $v['num'];
			$iamonds[$k]['time'] = $v['time'];
			$iamonds[$k]['fromid'] = $userinfos[$v['fromid']]['id'];
			$iamonds[$k]['toid'] = $usertoids[$v['toid']]['id'];
			$iamonds[$k]['fromidnick'] = $userinfos[$v['fromid']]['nick'];
			$iamonds[$k]['toidnick'] = $usertoids[$v['toid']]['nick'];
			if($v['flag'] == 0){
				if(!empty($v['poemid'])){
					$iamonds[$k]['flag'] = "送花消费-作品和人";
				}else{
					$iamonds[$k]['flag'] = "送花消费-伴奏";
				}
				// $iamonds[$k]['flag'] = "送花消费";
			}elseif ($v['flag'] == 1) {
				$iamonds[$k]['flag'] = "下载作品消费";
			}elseif ($v['flag'] == 2) {
				$iamonds[$k]['flag'] = "鲜花兑换的钻石";
			}elseif ($v['flag'] == 3) {
				$iamonds[$k]['flag'] = "自己现金购买的钻石";
			}elseif ($v['flag'] == 4) {
				$iamonds[$k]['flag'] = "兑换商品赠送钻石";
			}elseif ($v['flag'] == 5) {
				$iamonds[$k]['flag'] = "购买商品赠送钻石";	
			}

			if(!empty($v['opusid'])){
				$iamonds[$k]['opusname'] = $opusids[$v['opusid']]['name'];
			}else{
				$iamonds[$k]['opusname'] = "本人";
			}
			if(!empty($v['good_id'])){
				$iamonds[$k]['goodname'] = $goodids[$v['good_id']]['name'];
			}else{
				$iamonds[$k]['goodname'] = "";
			}
			if(!empty($v['orderid'])){
				$iamonds[$k]['orderid'] = $v['orderid'];
			}else{
				$iamonds[$k]['orderid'] = "";
			}
			if(!empty($v['poemid'])){
				$iamonds[$k]['poemname'] = $poemids[$v['poemid']]['name'];
			}else{
				$iamonds[$k]['poemname'] = "";
			}
			if(!empty($v['reader_id'])){
				$iamonds[$k]['readername'] = $readerids[$v['reader_id']]['name'];
			}else{
				$iamonds[$k]['readername'] = "";
			}
		}
		// var_dump($iamonds);die;
		return View::make('billdetails.iamondwater')->with('iamonds',$iamonds)->with('iamond',$paginator)->with('return',$return);
	}
	public function flowersList(){
		header("content-type:text/html;charset=utf8");
		/*****************接收表单信息************************/
		$fromid = Input::get('fromid','');
		$toid = Input::get('toid','');
		$fromidnick = trim(Input::get('fromidnick',''));
		$toidnick = trim(Input::get('toidnick',''));
		$opusname = trim(Input::get('opusname',''));
		$poemname = trim(Input::get('poemname',''));
		$readername = trim(Input::get('readername',''));
		$goodname = trim(Input::get('goodname',''));
		$orderid = trim(Input::get('orderid',''));
		$input_startime = Input::get('starttime');
		$input_endtime = Input::get('endtime');
		$starttime = strtotime($input_startime);
		$endtime = strtotime($input_endtime);
		$flag = intval(Input::get('flag',-1));
		$return = array('fromid'=>$fromid,'toid'=>$toid,'fromidnick'=>$fromidnick,'toidnick'=>$toidnick,'opusname'=>$opusname,'goodname'=>$goodname,'orderid'=>$orderid,'starttime'=>$input_startime,'endtime'=>$input_endtime,'flag'=>$flag,'poemname'=>$poemname,'readername'=>$readername);

		$flower = DB::table('user_flowers_list')->orderBy('id','desc');
		$max_page = $flower->max('id');

		/****************************搜索展示*************************/
		if(!empty($fromid)){	
			$flower->where('fromid','=',$fromid);
			$max_page = $flower->count();
		}
		if(!empty($toid)){		
			$flower->where('toid','=',$toid);
			$max_page = $flower->count();
		}
		//判断搜索送花人
		if(!empty($fromidnick)){
			$user_rs = DB::table('user')->where('nick','=',$fromidnick)->select('id','nick')->get();
			$tmp_fromid = array();
			if(!empty($user_rs)){
				foreach($user_rs as $k=>$v){
					$tmp_fromid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_fromid)){
				return Redirect::to('/admin/defaultError')->with('message',"用户名不存在");
			}
		}
		if(!empty($tmp_fromid)){
			$flower->whereIn('fromid',$tmp_fromid);
			$max_page = $flower->count();
		}
		
		//判断搜索收花人
		if(!empty($toidnick)){
			$usertoname = DB::table('user')->where('nick','=',$toidnick)->select('id','nick')->get();
			$tmp_toid = array();
			if(!empty($usertoname)){
				foreach ($usertoname as $k => $v) {
					$tmp_toid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_toid)){
				return Redirect::to('/admin/defaultError')->with('message','用户名不存在');
			}
		}
		if(!empty($tmp_toid)){
			$flower->whereIn('toid',$tmp_toid);
			$max_page = $flower->count();
		}

		if(!empty($opusname)){
			$opusname = DB::table('opus')->where('name','=',$opusname)->select('id','name')->get();
			$tmp_pusid = array();
			if(!empty($opusname)){
				foreach ($opusname as $k => $v) {
					$tmp_pusid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_pusid)){
				return Redirect::to('/admin/defaultError')->with('message','作品名为空');
			}
		}
		if(!empty($tmp_pusid)){
			$flower->whereIn('opusid',$tmp_pusid);
			$max_page = $flower->count();
		}
		//伴奏歌名
		if(!empty($poemname)){
			$poem = DB::table('poem')->where('name','=',$poemname)->select('id','name')->get();
			$tmp_poemid = array();
			if (!empty($poem)) {
				foreach ($poem as $k => $v) {
					$tmp_poemid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_poemid)){
				return Redirect::to('/admin/defaultError')->with('message','伴奏歌名为空');
			}
		}
		if(!empty($tmp_poemid)) {
			$flower->whereIn('poemid',$tmp_poemid);
			$max_page = $flower->count();
		}
		//伴奏人名
		if(!empty($readername)){
			$reader = DB::table('reader')->where('name','=',$readername)->select('id','name')->get();
			$tmp_readerid = array();
			if(!empty($reader)){
				foreach ($reader as $k => $v) {
					$tmp_readerid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_readerid)){
				return Redirect::to('/admin/defaultError')->with('message','伴奏人名为空');
			}
		}
		if(!empty($tmp_readerid)){
			$flower->whereIn('reader_id',$tmp_readerid);
			$max_page = $flower->count();
		}
		//商品名称
		if(!empty($goodname)){
			$goods = DB::table('goods')->where('name','=',$goodname)->select('id','name')->get();
			$tmp_goodsid = array();
			if(!empty($goodname)){
				foreach($goods as $k=>$v){
					$tmp_goodsid[$v['id']] = $v['id'];
				}
			}
			if(empty($tmp_goodsid)){
				return Redirect::to('/admin/defaultError')->with('message','商品不存在');
			}
		}
		if(!empty($tmp_goodsid)){
			$flower->whereIn('good_id',$tmp_goodsid);
			$max_page = $flower->count();
		}

		//订单号
		if(!empty($orderid)){
			$flower->where('orderid',$orderid);
			$max_page = $flower->count();
		}
		//时间
		if($starttime && $endtime){
			$flower = $flower->where('time','>=',$starttime);
			$flower = $flower->where('time','<=',$endtime+86399);
			$max_page = $flower->count();
		}elseif($starttime) {
			$flower->where('time','>=',$starttime);
			$max_page = $flower->count();
		}elseif($endtime){
			$flower->where('time','<=',$endtime+86399);
			$max_page = $flower->count();
		}
		//类型
		if($flag>-1){
			$flower->where('flag','=',$flag);
			$max_page = $flower->count();
		}
		/****************************列表展示*************************/
		$currentPage = Input::get('page',1);
		$pagesize = 20;
		$offSet = ($currentPage-1)*$pagesize;
		$flower = $flower->skip($offSet)->take($pagesize)->get();
		//->paginate(20);
		$paginator = Paginator::make($flower,$max_page, $pagesize);
		//$flower = DB::table('user_flowers_list')->paginate(20);
		
		//送花人
		$fromid = array();
		foreach ($flower as $k => $v) {
			$fromid[] = $v['fromid'];
		}
		
		if(empty($fromid)){
			return Redirect::to('/admin/defaultError')->with('message',"用户名不存在");
			//return Redirect::to('/admin/flowersList?page=1');
		}

		$userinfo = DB::table('user')->whereIn('id',$fromid)->select('id','nick')->get();
		$userinfos = array();
		foreach ($userinfo as $k => $v) {
			$userinfos[$v['id']] = $v;
		}

		//收花人
		$toid = array();
		foreach ($flower as $k => $v) {
			$toid[] = $v['toid'];
		}
		$usertoid = DB::table('user')->whereIn('id',$toid)->select('id','nick')->get();
		$usertoids = array();
		foreach ($usertoid as $k => $v) {
			$usertoids[$v['id']] = $v;
		}

		//作品名
		$opusid = array();
		foreach($flower as $k => $v){
			$opusid[] = $v['opusid'];
		}
		$opuslist = DB::table('opus')->whereIn('id',$opusid)->get();
		$opusids = array();
		foreach ($opuslist as $k => $v) {
			$opusids[$v['id']] = $v;
		}
		
		//商品名
		$goodid = array();
		foreach ($flower as $k => $v) {
			$goodid[] = $v['good_id'];
		}

		$goodlist = DB::table('goods')->whereIn('id',$goodid)->get();
		$goodids = array();
		foreach ($goodlist as $k => $v) {
			$goodids[$v['id']] = $v;
		}
		//伴奏歌名
		$poemid = array();
		foreach ($flower as $k => $v) {
			$poemid[] = $v['poemid'];
		}
		$poemlist = DB::table('poem')->whereIn('id',$poemid)->get();
		$poemids = array();
		foreach ($poemlist as $k => $v) {
			$poemids[$v['id']] = $v;
		}
		//伴奏姓名
		$reader_id = array();
		foreach ($flower as $k => $v) {
			$reader_id[] = $v['reader_id'];
		}
		$readlist = DB::table('reader')->whereIn('id',$reader_id)->get();
		$readerids = array();
		foreach ($readlist as $k => $v) {
			$readerids[$v['id']] = $v;
		}
		//展示模板数据
		$flowers = array();
		// var_dump($flower);die;
		foreach ($flower as $k => $v) {
			$flowers[$k]['id'] = $v['id'];
			$flowers[$k]['num'] = $v['num'];
			$flowers[$k]['time'] = $v['time'];
			$flowers[$k]['fromid'] = $userinfos[$v['fromid']]['id'];
			$flowers[$k]['toid'] = $usertoids[$v['toid']]['id'];
			$flowers[$k]['fromidnick'] = $userinfos[$v['fromid']]['nick'];
			$flowers[$k]['toidnick'] = $usertoids[$v['toid']]['nick'];
			if($v['flag'] == 0){
				$flowers[$k]['flag'] = "别人赠送";
			}elseif ($v['flag'] == 1) {
				$flowers[$k]['flag'] = "系统赠送";
			}elseif ($v['flag'] == 2) {
				$flowers[$k]['flag'] = "兑换消耗";
			}elseif ($v['flag'] == 3) {
				$flowers[$k]['flag'] = "提现消耗";
			}
			if(!empty($v['opusid'])){
			 	$flowers[$k]['opusname'] = $opusids[$v['opusid']]['name'];
			}else {
				$flowers[$k]['opusname'] = "本人";
			}
			if(!empty($v['orderid'])){
				$flowers[$k]['orderid'] = $v['orderid'];
			}else{
				$flowers[$k]['orderid'] = "";
			}
			if(!empty($v['good_id'])){
				$flowers[$k]['goodname'] = $goodids[$v['good_id']]['name'];
			}else{
				$flowers[$k]['goodname'] = "";
			}
			if(!empty($v['poemid'])){
				$flowers[$k]['poemname'] = $poemids[$v['poemid']]['name'];
			}else{
				$flowers[$k]['poemname'] = "";
			}
			if(!empty($v['reader_id'])){
				$flowers[$k]['readername'] = $readerids[$v['reader_id']]['name'];
			}else{
				$flowers[$k]['readername'] = "";
			}
		}
		// var_dump($flowers);die;
		// echo "<pre>";
		// print_r(DB::getQueryLog());
		// echo "</pre>";
		return View::make('billdetails.flowerslist')->with('flowers',$flowers)->with('flower',$paginator)->with('return',$return);
	}

	/**
	 * 运营人员列表,钻石，鲜花运营人员列表
	 * @author :wang.hongli
	 * @since :2016/09/20
	 */
	public function operationUserList(){
		$size = 20;
		$page = intval(Input::get('page',1));
		$offSet = ($page-1)*$size;
		$rs = DB::table('operationuserlist')->orderBy('id','desc')->skip($offSet)->take($size)->get();
		if(empty($rs)){
			$rs = [];
		}
		$uids =[];
		foreach($rs as $k=>$v){
			$uids[$v['uid']] = $v['uid'];
		}
		$tmp_user_info = DB::table('user')->whereIn('id',$uids)->get(['nick','id']);
		$user_info = [];
		if(!empty($tmp_user_info)){
			foreach($tmp_user_info as $k=>$v){
				$user_info[$v['id']] = $v;
			}
		}
		foreach($rs as $k=>&$v){
			$v['nick'] = isset($user_info[$v['uid']]['nick']) ? $user_info[$v['uid']]['nick'] : '';
		}
		$max_page = DB::table('operationuserlist')->max('id');
		$paginator = Paginator::make($rs,$max_page, $size);
		return View::make('billdetails.operationuserlist')->with('rs',$rs)->with('paginator',$paginator);
	}

	/**
	 * 添加运营人员列表,钻石，鲜花运营人员列表
	 * @author :wang.hongli
	 * @since :2016/09/20
	 */
	public function addOperationUser(){
		$uid = intval(Input::get('uid',0));
		if(empty($uid)){
			return Redirect::to('/admin/defaultError')->with('message','请填写用户id');
		}
		//判断用户是否存在
		if(!DB::table('user')->where('id',$uid)->pluck('id')){
			return Redirect::to('/admin/defaultError')->with('message','该用户不存在');
		}
		//判断用户是否已经添加过
		$user = DB::table('operationuserlist')->where('uid',$uid)->pluck('id');
		if(!empty($user)){
			return Redirect::to('/admin/defaultError')->with('message','此用户已经存在');
		}
		$data = ['uid'=>$uid,'addtime'=>time(),'operator_uid'=>1,'operator_time'=>time(),'isdel'=>0];
		DB::table('operationuserlist')->insert($data);
		return Redirect::to('/admin/operationUserList');
	}

	/**
	 * 删除/恢复运营人员用户
	 * @author :wang.hongli
	 * @since :2016/09/20
	 */
	public function modifyOperationUser(){
		$isdel = intval(Input::get('isdel',0));
		$id = intval(Input::get('id',0));
		if(empty($id)){
			echo -1;
		}
		$isdel = $isdel^1 ? 1:0;
		DB::table('operationuserlist')->where('id',$id)->update(['isdel'=>$isdel]);
		return 1;
	}
}