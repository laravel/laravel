<?php
/**
 * 后台订单管理模型
 * @author wang.hongli
 * @since:2016/06/01
 *
 */
class AdminOrder extends AdminCommon{
	
	/**
	 * 联合会会费获取
	 * @param unknown $data
	 * @param 1 联合会会费 2,诵读比赛费 3,诗文比赛费 4,培训班活动费,5,打赏团队费
	 */
	public function orderList($id,$conn){
		switch($id){
			case 1:
				$goods_id = array(1);
				$conn->where('goods_id',1);
				break;
			case 2:
				$goods_id = DB::table('goods')
								->leftJoin('competitionlist','goods.competition_id','=','competitionlist.id')
								->select('goods.id')
								->where('goods.competition_id','<>',0)
								->where('goods.flag','=',0)
								->whereIn('competitionlist.pid',array(4,5))->lists('goods.id');
				$conn->whereIn('goods_id',$goods_id);
				break;
			case 3:
				$goods_id = DB::table('goods')
				->leftJoin('competitionlist','goods.competition_id','=','competitionlist.id')
				->select('goods.id')
				->where('goods.competition_id','<>',0)
				->where('goods.flag','=',0)
				->whereIn('competitionlist.pid',array(6,7))->lists('goods.id');
				$conn->whereIn('goods_id',$goods_id);
				break;
			case 4:
				$goods_id = DB::table('goods')
				->leftJoin('competitionlist','goods.competition_id','=','competitionlist.id')
				->select('goods.id')
				->where('goods.competition_id','<>',0)
				->where('goods.flag','=',1)
				->lists('goods.id');
				$conn->whereIn('goods_id',$goods_id);
				break;
			case 5:
				$goods_id = array(16);
				$conn->where('goods_id',16);
				break;
		}
		$total_money = $conn->sum('total_price');
		return array('conn'=>$conn,'goods_id'=>$goods_id,'total_money'=>$total_money);
		
	}
}