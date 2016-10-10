<?php 
/**
 * 账单model
 * @author :wang.hongli
 * @since :2016/08/17
 */
class ApiAccountStatement extends ApiCommon {

	/**
	 * 获取我的账单头部导航
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function getAccountStatementNav(){
		$arr = [
			['flag'=>1,'status'=>1,'name'=>'充值'],
			['flag'=>2,'status'=>1,'name'=>'兑换'],
			['flag'=>3,'status'=>1,'name'=>'提现'],
		];
		return $arr;
	}
	/**
	 * 钻石明细
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function diamondDetailList($uid=0,$offSet=0,$count=21){

		$list = DB::table('user_diamond_list')->where('toid',$uid)->skip($offSet)->take($count)->orderBy('id','desc')->get(['id','fromid','toid','opusid','num','time','good_id','orderid','flag','poemid','reader_id']);
		if(empty($list)){
			return [];
		}

		$tmp_uids = $tmp_opusid = $tmp_goods = $tmp_poemid = [];
		foreach($list as $k=>$v){
			if(empty($v['fromid'])){
				$tmp_uids[] = $v['fromid'];
			}
			if(!empty($v['toid'])){
				$tmp_uids[] = $v['toid'];
			}
			if(!empty($v['opusid'])){
				$tmp_opusid[] = $v['opusid'];
			}
			if(!empty($v['good_id'])){
				$tmp_goods[] = $v['good_id'];
			}
			if(!empty($v['poemid'])){
				$tmp_poemid[] = $v['poemid'];
			}
		}

		$user_info = $opus_info = $good_info = $poem_info = $reader_info = [];
		if(!empty($tmp_uids)){
			$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uids)->get(['id','nick']);
			foreach($tmp_user_info as $k=>$v){
				$user_info[$v['id']] = $v['nick'];
			}
		}
		if(!empty($tmp_opusid)){
			$tmp_opus_info = DB::table('opus')->whereIn('id',$tmp_opusid)->get(['id','name']);
			if(!empty($tmp_opus_info)){
				foreach($tmp_opus_info as $k=>$v){
					$opus_info[$v['id']] = $v['name'];
				}
			}
		}
		if(!empty($tmp_goods)){
			$tmp_good_info = DB::table('goods')->whereIn('id',$tmp_goods)->get(['id','name']);
			if(!empty($tmp_good_info)){
				foreach($tmp_good_info as $k=>$v){
					$good_info[$v['id']] = $v['name'];
				}
			}
		}
		if(!empty($tmp_poemid)){
			$tmp_poem_info = DB::table('poem')->whereIn('id',$tmp_poemid)->get(['id','name']);
			if(!empty($tmp_poem_info)){
				foreach($tmp_poem_info as $k=>$v){
					$poem_info[$v['id']] = $v;
				}
			}
			//获取导师id
			$reader_ids = DB::table('readpoemrel')->whereIn('poemid',$tmp_poemid)->lists('readerid');
			if(!empty($reader_ids)){
				$tmp_reader_info = DB::table('reader')->whereIn('id',$reader_ids)->get(['id','name']);
				if(!empty($tmp_reader_info)){
					foreach($tmp_reader_info as $k=>$v){
						$reader_info[$v['id']] = $v;
					}
				}
			}
		}
		foreach($list as $k=>&$v){
			$v['from_nick'] = isset($user_info[$v['fromid']]) ? $user_info[$v['fromid']] : '系统';
			$v['to_nick'] = isset($user_info[$v['toid']]) ? $user_info[$v['toid']] : '';
			$v['good_name'] = isset($good_info[$v['good_id']]) ? $good_info[$v['good_id']] : '';
			$v['opus_name'] = isset($opus_info[$v['opusid']]) ? $opus_info[$v['opusid']] : '';
			$v['poem_name'] = isset($poem_info[$v['poemid']]['name']) ? $poem_info[$v['poemid']]['name'] : '';
			$v['reader_name'] = isset($reader_info[$v['reader_id']]['name']) ? $reader_info[$v['reader_id']]['name'] : '';
		}
		return $list;
	}
	/**
	 * 鲜花明细
	 * @author :wang.hognli
	 * @since :2016/08/17
	 */
	public function flowerDetailList($uid=0,$offSet=0,$count=21){

		$list = DB::table('user_flowers_list')->where('toid',$uid)->skip($offSet)->take($count)->orderBy('id','desc')->get(['id','fromid','toid','opusid','num','time','good_id','orderid','flag','poemid','reader_id']);
		if(empty($list)){
			return [];
		}

		$tmp_uids = $tmp_opusid = $tmp_goods = $tmp_poemid  = [];
		foreach($list as $k=>$v){
			if(empty($v['fromid'])){
				$tmp_uids[] = $v['fromid'];
			}
			if(!empty($v['toid'])){
				$tmp_uids[] = $v['toid'];
			}
			if(!empty($v['opusid'])){
				$tmp_opusid[] = $v['opusid'];
			}
			if(!empty($v['good_id'])){
				$tmp_goods[] = $v['good_id'];
			}
			if(!empty($v['poemid'])){
				$tmp_poemid[] = $v['poemid'];
			}
		}

		$user_info = $opus_info = $good_info = $poem_info = $reader_info = [];
		if(!empty($tmp_uids)){
			$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uids)->get(['id','nick']);
			foreach($tmp_user_info as $k=>$v){
				$user_info[$v['id']] = $v['nick'];
			}
		}
		if(!empty($tmp_opusid)){
			$tmp_opus_info = DB::table('opus')->whereIn('id',$tmp_opusid)->get(['id','name']);
			if(!empty($tmp_opus_info)){
				foreach($tmp_opus_info as $k=>$v){
					$opus_info[$v['id']] = $v['name'];
				}
			}
		}
		if(!empty($tmp_goods)){
			$tmp_good_info = DB::table('goods')->whereIn('id',$tmp_goods)->get(['id','name']);
			if(!empty($tmp_good_info)){
				foreach($tmp_good_info as $k=>$v){
					$good_info[$v['id']] = $v['name'];
				}
			}
		}
		if(!empty($tmp_poemid)){
			$tmp_poem_info = DB::table('poem')->whereIn('id',$tmp_poemid)->get(['id','name']);
			if(!empty($tmp_poem_info)){
				foreach($tmp_poem_info as $k=>$v){
					$poem_info[$v['id']] = $v;
				}
			}
			//获取导师id
			$reader_ids = DB::table('readpoemrel')->whereIn('poemid',$tmp_poemid)->lists('readerid');
			if(!empty($reader_ids)){
				$tmp_reader_info = DB::table('reader')->whereIn('id',$reader_ids)->get(['id','name']);
				if(!empty($tmp_reader_info)){
					foreach($tmp_reader_info as $k=>$v){
						$reader_info[$v['id']] = $v;
					}
				}
			}
		}
		foreach($list as $k=>&$v){
			$v['from_nick'] = isset($user_info[$v['fromid']]) ? $user_info[$v['fromid']] : '系统';
			$v['to_nick'] = isset($user_info[$v['toid']]) ? $user_info[$v['toid']] : '';
			$v['good_name'] = isset($good_info[$v['good_id']]) ? $good_info[$v['good_id']] : '';
			$v['opus_name'] = isset($opus_info[$v['opusid']]) ? $opus_info[$v['opusid']] : '';
			$v['poem_name'] = isset($poem_info[$v['poemid']]['name']) ? $poem_info[$v['poemid']]['name'] : '';
			$v['reader_name'] = isset($reader_info[$v['reader_id']]['name']) ? $reader_info[$v['reader_id']]['name'] : '';
		}
		return $list;
	}
}

 ?>