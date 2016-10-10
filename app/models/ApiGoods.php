<?php
/**
 * 商品model
 * @author:wang.hongli
 * @since:2016/05/30
 */
class ApiGoods extends ApiCommon {
	
	/**
	 * 获取比赛商品列表
	 * @author:wang.hongli
	 * @since:2016/05/30
	 */
	public function getCompGoodsList($flag=0){
		$list = DB::table('goods')->where('flag',$flag)->where('competition_id','<>',0)->get();
		if(empty($list)){
			return array();
		}
		$rs = array();
		foreach($list as $k=>$v){
			$rs[$v['competition_id']] = $v;
		}
		return $rs;
	}
	/**
	 * 根据比赛id获取商品信息
	 * @author:wang.hongli
	 * @since:2016/05/31
	 */
	public function accorCompIdGetGoodsInfo($flag=0,$competitionId){
		if(empty($competitionId)){
			return false;
		}
		$flag = intval($flag);
		$goodInfo = DB::table('goods')->where('flag',$flag)->where('competition_id',$competitionId)->first();
		if(empty($goodInfo)){
			return false;
		}
		return $goodInfo;
	}
}