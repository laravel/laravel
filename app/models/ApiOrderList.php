<?php 
/**
 * 用户获取订单相关信息
 * @author :wang.hongli
 * @since :2016/08/16
 */
class ApiOrderList extends ApiCommon{

	/**
	 * 获取自己用户订单列表
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function myOrderList($uid=0,$offSet=0,$page_count=21){
		$return = [];
		try {
			$gids = DB::table('goods')->whereIn('flag',[3,4,5])->lists('id');
			if(empty($gids)){
				return $return;
			}
			$gids = implode(',', $gids);
			$sql = "select * from order_list where uid = ? and goods_id in ( ? ) and status=2 and isdel=0 
				union all select * from flower_order_list where uid = ? and goods_id in ( ? ) and status=2 and isdel=0 order by addtime desc limit {$offSet},{$page_count}";
			$data = [$uid,$gids,$uid,$gids];
			$return = DB::select($sql,$data);
			if(empty($return)){
				return $return;
			}
			$goods_id = [];
			$orders_id = [];
			foreach($return as $k=>$v){
				$goods_id[] = $v['goods_id'];
				$orders_id[] = $v['orderid'];
			}
			//查找商品信息
			$tmp_goods_info = DB::table('good_param')->whereIn('goodid',$goods_id)->get(['goodid','icon']);
			if(empty($tmp_goods_info)){
				return $return;
			}
			$goods_info = [];
			if(!empty($tmp_goods_info)){
				foreach($tmp_goods_info as $k=>$v){
					$v['icon'] = $this->poem_url.$v['icon'];
					$goods_info[$v['goodid']] = $v;
				}
			}
			$goods_main_info = [];
			$tmp_goods_main_info = DB::table('goods')->whereIn('id',$goods_id)->get(['id','name']);
			if(!empty($tmp_goods_main_info)){
				foreach($tmp_goods_main_info as $k=>$v){
					$goods_main_info[$v['id']] = $v;
				}
			}

			$diamonds = [];
			$tmp_diamond = DB::table('user_diamond_list')->where('toid',$uid)->whereIn('orderid',$orders_id)->get(['id','fromid','toid','num','time','good_id','orderid']);
			if(!empty($tmp_diamond)){
				foreach($tmp_diamond as $k=>$v){
					$diamonds[$v['orderid']] = $v;
				}
			}
			foreach($return as $k=>&$v){
				//商品图片
				$v['icon'] = isset($goods_info[$v['goods_id']]['icon']) ? $goods_info[$v['goods_id']]['icon'] : '';
				$v['name'] = isset($goods_main_info[$v['goods_id']]['name']) ? $goods_main_info[$v['goods_id']]['name']:'';
				//赠送钻石数
				$v['diamond'] = isset($diamonds[$v['orderid']]['num']) ? $diamonds[$v['orderid']]['num'] : 0;
			}
		} catch (Exception $e) {
			return [];
		}
		return $return;
	}

	/**
	 * 获取订单详情
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function orderInfo($uid=0,$orderid=0,$isexchange=0){
		$return = [];
		$conn = '';
		if($isexchange == 0){
			$conn = DB::table('order_list');
		}else{
			$conn = DB::table('flower_order_list');
		}
		$orderInfo = $conn->where('uid',$uid)->where('orderid',$orderid)->first();
		if(empty($orderInfo)){
			return $return;
		}
		$goods_info = DB::table('goods')->where('id',$orderInfo['goods_id'])->first(['id','name','description']);
		$goods_param = DB::table('good_param')->where('goodid',$orderInfo['goods_id'])->first(['id','icon']);
		$diamond = DB::table('user_diamond_list')->where('toid',$uid)->where('orderid',$orderid)->pluck('num');
		//获取地址和收货人信息
		$user_address= DB::table('user_address')->where('id',$orderInfo['address_id'])->first(['id','province_id','city_id','area_id','address','name','tel','istop']);
		if(!empty($user_address)){
			$city = ApiAddress::getAddress($user_address['province_id'],$user_address['city_id'],$user_address['area_id']);
			$user_address['province_name'] = isset($city['province']['name']) ? $city['province']['name'] : '';
			$user_address['city_name'] = isset($city['city']['name']) ? $city['city']['name'] : '';
			$user_address['area_name'] = isset($city['area']['name']) ? $city['area']['name'] : '';
		}else{
			$user_address = null;
		}
		$orderInfo['diamond'] = !empty($diamond) ? $diamond : 0;
		$orderInfo['name'] = $goods_info['name'];
		$orderInfo['description'] = $goods_info['description'];
		$orderInfo['icon'] = $this->poem_url.$goods_param['icon'];
		$orderInfo['address'] = $user_address;
		return $orderInfo;
	}

	/**
	 * 删除订单
	 * @author :wang.hongli
	 * @since :2016/08/18
	 * @param : isexchange 0 现金购买 1 鲜花兑换
	 */
	public function delMyOrder($id=0,$uid=0,$isexchange=0){
		if($isexchange ==0){
			$conn = DB::table('order_list');
		}else{
			$conn = DB::table('flower_order_list');
		}
		$flag = $conn->where('id',$id)->where('uid',$uid)->update(['isdel'=>1]);
		if(!$flag){
			return false;
		}
		return true;
	}
}


 ?>