<?php 
/**
 * 天籁商城model
 * @author :wang.hongli
 * @since :2016/08/03
 */
class ApiTianLaiGoods extends ApiCommon{

	/**
	 * 获取天籁商城导航
	 * @author:wang.hongli
	 * @since :2016/08/14
	 */
	public function getTianLaiNav(){
		$arr = [
			['flag'=>3,'status'=>1,'name'=>'会员增值'],
			['flag'=>4,'status'=>1,'name'=>'实体商品'],
			['flag'=>5,'status'=>1,'name'=>'出版物'],
		];
		return $arr;
	}

	/**
	 * 获取天籁商城商品列表
	 * @author :wang.hongli
	 * @since :2016/08/14
	*/
	public function getTianLaiGoodsList($flag,$pageIndex=1,$count=20){
		$offset = ($pageIndex-1)*$count;
		$count++;
		$list = DB::table('goods')->where('flag',$flag)->where('isdel',0)->orderBy('id','desc')->skip($offset)->take($count)->get();
		if(empty($list)){
			return ['hasmore'=>0];
		}
		$good_ids = [];
		foreach($list as $k=>$v){
			$good_ids[] = $v['id'];
		}
		//获取参数信息
		$goods_param_tmp = DB::table('good_param')->whereIn('goodid',$good_ids)->get();
		$goods_param=[];
		if(!empty($goods_param_tmp)){
			foreach($goods_param_tmp as $k=>$v){
				$goods_param[$v['goodid']] = $v;
			}
		}
		foreach($list as $k=>&$v){
			$tmp = isset($goods_param[$v['id']]) ? $goods_param[$v['id']] : [];
			$v['price'] = isset($tmp['price']) ? $tmp['price'] : $v['price'];
			$v['discount_price'] = isset($tmp['discount_price']) ? $tmp['discount_price'] : 0;
			$v['member_price'] = isset($tmp['member_price'] ) ? $tmp['member_price'] : 0;
			$v['postage_price'] = isset($tmp['postage_price']) ? $tmp['postage_price'] : 0;
			$v['flower_price'] = isset($tmp['flower_price']) ? $tmp['flower_price'] : 0;
			$v['discount_flower_price'] = isset($tmp['discount_flower_price']) ? $tmp['discount_flower_price'] : 0;
			$v['member_flower_price'] = isset($tmp['member_flower_price']) ? $tmp['member_flower_price'] : 0;
			$v['flower_postage_price'] = isset($tmp['flower_postage_price']) ? $tmp['flower_postage_price'] : 0;
			$v['promptgoods'] = isset($tmp['promptgoods']) ? $tmp['promptgoods'] : 0;
			$v['crowdfunding'] = isset($tmp['crowdfunding']) ? $tmp['crowdfunding'] : 0;
			$v['crowdfundinged'] = isset($tmp['crowdfundinged']) ? $tmp['crowdfundinged'] : 0;
			$v['diamond'] = isset($tmp['diamond']) ? $tmp['diamond'] : 0;
			$v['member_diamond'] = isset($tmp['member_diamond']) ? $tmp['member_diamond'] : 0;
			$v['normal_section'] = isset($tmp['normal_section']) ? $tmp['normal_section'] : 0;
			$v['member_section'] = isset($tmp['member_section']) ? $tmp['member_section'] : 0;
			$v['icon'] = isset($tmp['icon']) ? $this->poem_url.$tmp['icon'] : '';
			//标识是否有详情页 1有详情页 0 没有详情页
			if(!empty($tmp)){
				$v['has_detail'] = 1;
			}else{
				$v['has_detail'] = 0;
			}
		}
		$hasmore = count($list) > $count ? 1:0;
		if($hasmore){
			array_pop($list);
		}
		$list['hasmore'] = $hasmore;
		return $list;
	}

	/**
	 * 获取天籁商城商品详细信息
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function getTianlaiGoodsInfo($user_info=[],$goodid=0,$goods_num=1){
		try {
			$goods_info = DB::table('goods')->where('id',$goodid)->where('isdel',0)->first();
			if(empty($goods_info)){
				// 获取商品信息失败
				return false;
			}
			//获取参数信息
			$goods_param = DB::table('good_param')->where('goodid',$goodid)->first();
			if(!empty($goods_param)){
				$goods_info['price'] = $goods_param['price'];
				$goods_info['discount_price'] = $goods_param['discount_price'];
				$goods_info['member_price'] = $goods_param['member_price'];
				$goods_info['postage_price'] = $goods_param['postage_price'];
				$goods_info['flower_price'] = $goods_param['flower_price'];
				$goods_info['discount_flower_price'] = $goods_param['discount_flower_price'];
				$goods_info['member_flower_price'] = $goods_param['member_flower_price'];
				$goods_info['flower_postage_price'] = $goods_param['flower_postage_price'];
				$goods_info['promptgoods'] = $goods_param['promptgoods'];
				$goods_info['crowdfunding'] = $goods_param['crowdfunding'];
				$goods_info['diamond'] = $goods_param['diamond'];
				$goods_info['member_diamond'] = $goods_param['member_diamond'];
				$goods_info['normal_section'] = $goods_param['normal_section'];
				$goods_info['member_section'] = $goods_param['member_section'];
				$goods_info['icon'] = $this->poem_url.$goods_param['icon'];
				$goods_info['des_detail'] = $goods_param['des_detail'];
				$goods_info['normal_price_section']=trim($goods_param['normal_price_section']);
				$goods_info['member_price_section']=trim($goods_param['member_price_section']);
				$goods_info['normal_flower_price_section']=trim($goods_param['normal_flower_price_section']);
				$goods_info['member_flower_price_section']=trim($goods_param['member_flower_price_section']);
			}
			//获取图片信息
			$goods_pic = DB::table('goodspic')->where('goodid',$goodid)->lists('url');
			if(!empty($goods_pic)){
				foreach($goods_pic as $k=>&$v){
					$v= $this->poem_url.$v;
				}
				$goods_info['goodspic'] = $goods_pic;
			}
			return $goods_info;
		} catch (Exception $e) {
			// 获取商品详细信息失败
			return false;
		}
	}
	/**
	 * 鲜花兑换商品
	 * @author :wang.hongli
	 * @since :2016/08/15
	 */
	public function exchangeGood($user_info=[],$data=[]){
		$msg = ['code'=>-4,'msg'=>'兑换失败'];
		if(empty($user_info) || empty($data)){
			return $msg;
		}
		//获取商品信息
		$goods_info = $this->getTianlaiGoodsInfo($user_info,$data['goods_id'],$data['num']);
		if(empty($goods_info)){
			return ['code'=>-1,'msg'=>'此商品已下架'];
		}
		$prices = $this->realExpendFolow($user_info,$goods_info,$data);
		if(empty($prices['totalPrice'])){
			return ['code'=>-2,'msg'=>'本商品不支持鲜花兑换'];
		}
		//检测鲜花数是否够
		$apiCheckPermission = new ApiCheckPermission();
		$flag = $apiCheckPermission->isEnough($user_info['id'],1,$prices['totalPrice']);
		if(!$flag){
			return ['code'=>-3,'msg'=>'鲜花数不足'];
		}
		$time = time();
		$send_out = 0;
		//如果是虚拟商品，状态直接为已发货
		if($goods_info['flag'] ==3){
			$send_out = 1;
		}else{
			if(empty($data['address_id'])){
				return ['code'=>-100,'msg'=>'请选择地址'];
			}
		}
		//插入订单表
		$insert_data = [
			'orderid'=>$data['orderid'],
			'uid'=>$user_info['id'],
			'goods_id'=>$data['goods_id'],
			'price'=>$prices['price'],
			'old_price'=>$prices['old_price'],
			'num'=>$data['num'],
			'total_price'=>$prices['totalPrice'],
			'pay_type'=>$data['pay_type'],
			'description'=>$goods_info['description'],
			'status'=>2,
			'addtime'=>$time,
			'updatetime'=>$time,
			'plat_from'=>$data['plat_from'],
			'audit_time'=>$time,
			'attach_id'=>$data['goods_id'],
			'attach_price'=>$goods_info['flower_postage_price'],
			'comments'=>$data['comments'],
			'send_out'=>$send_out,
			'isexchange'=>1,
			'address_id'=>$data['address_id'],
		];
		try {
			$id = DB::table('flower_order_list')->insertGetId($insert_data);
			if($id){
				//购买钻石
				$data['flag'] = 2;
				$this->buyDiamond($user_info,$goods_info,$data);
				//已经筹到的数量
				$this->incrementCrowdfundinged($goods_info,$insert_data['num']);
				//开通相关权限
				$this->passPermission($user_info,$goods_info,$data);
				//赠送钻石等操作
				$data['flag'] = 4;
				$this->giveDiamond($user_info,$goods_info,$data);
				//开通会员赠送下载数
				$this->giveDownNum($user_info,$goods_info,$data);
				//用户鲜花数操作
				DB::table('user_asset_num')->where('uid',$user_info['id'])->decrement('flower',$prices['totalPrice']);
			}
			return ['code'=>1,'msg'=>'success','data'=>['orderid'=>$insert_data['orderid'],'data'=>$insert_data]];
		} catch (Exception $e) {
			return $msg;
		}
	}
	/**
	 * 插入天籁商城订单 -- 现金支付
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function insertTianLaiOrder($user_info=[],$data = []){
		$msg = ['code'=>-4,'msg'=>'兑换失败'];
		if(empty($user_info) || empty($data)){
			return $msg;
		}
		//获取商品详细信息
		$goods_info = $this->getTianlaiGoodsInfo($user_info,$data['goods_id'],$data['num']);
		if(empty($goods_info)){
			return ['code'=>-1,'msg'=>'此商品已下架'];
		}
		//现金支付	
		$prices = $this->realExpendMoney($user_info,$goods_info,$data);
		if(empty($prices['totalPrice'])){
			return ['code'=>-2,'msg'=>'本商品不支持现金支付'];
		}
		$send_out = 0;
		//如果是虚拟商品，状态直接为已发货
		if($goods_info['flag'] ==3){
			$send_out = 1;
		}else{
			if(empty($data['address_id'])){
				return ['code'=>-100,'msg'=>'请选择地址'];
			}
		}
		$time = time();
		//插入订单表
		$insert_data = [
			'orderid'=>$data['orderid'],
			'uid'=>$user_info['id'],
			'goods_id'=>$data['goods_id'],
			'price'=>$prices['price'],
			'old_price'=>$prices['old_price'],
			'num'=>$data['num'],
			'total_price'=>$prices['totalPrice'],
			'pay_type'=>$data['pay_type'],
			'description'=>$goods_info['description'],
			'status'=>0,
			'addtime'=>$time,
			'updatetime'=>$time,
			'plat_from'=>$data['plat_from'],
			'audit_time'=>$time,
			'attach_id'=>$data['goods_id'],
			'attach_price'=>$goods_info['flower_postage_price'],
			'comments'=>$data['comments'],
			'send_out'=>$send_out,
			'isexchange'=>0,
			'address_id'=>$data['address_id'],
		];
		//插入订单
		if(!DB::table('order_list')->insert($insert_data)){
			return ['code'=>-5,'msg'=>'插入订单失败'];
		}
		$apiPay = new ApiPay();
		$return = $apiPay->opOrder($insert_data);
		$return['orderid'] = $insert_data['orderid'];
		return $return;
	} 
	/**
	 * 购买钻石特殊处理
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function buyDiamond($user_info=[],$goods_info=[],$data=[]){
		if($goods_info['id'] == 61){
			//购买钻石
			$flag = DB::table('user_asset_num')->where('uid',$user_info['id'])->pluck('id');
			if($flag){
				DB::table('user_asset_num')->where('uid',$user_info['id'])->increment('jewel',$data['num']);
			}else{
				DB::table('user_asset_num')->insert(['uid'=>$user_info['id'],'flower'=>0,'get_flower'=>0,'jewel'=>$data['num']]);
			}
			$diamond_arr = [
				'fromid'=>0,
				'toid'=>$user_info['id'],
				'num'=>$giveDiamond,
				'time'=>time(),
				'flag'=>$data['flag'],
				'good_id'=>$goods_info['id'],
				'orderid'=>$data['orderid'],
			];
			//赠送钻石记录
			DB::table('user_diamond_list')->insert($diamond_arr);
		}
	}
	/**
	 * 计算用户实际消耗的鲜花
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function realExpendFolow($user_info=[],$goods_info=[],$data=[]){
		$return = [];
		$ismember = $user_info['ismember'];
		$totalPrice = 0;
		$pay_price = 0;
		$old_price = 0;
		//没有任何额外的价格
		$pay_price = $old_price = $goods_info['flower_price'];
		$totalPrice = $goods_info['flower_price']*$data['num'];
		//如果有折扣价格
		if(!empty($goods_info['discount_flower_price'])){
			$pay_price = $goods_info['discount_flower_price'];
			$totalPrice = $goods_info['discount_flower_price']*$data['num'];
		}
		//如果为会员
		if(!empty($ismember)){
			//如果为分段计费
			if(!empty($goods_info['member_flower_price_section'])){
				// 1-20|2-39|3-40
				$tmp_arr = explode('|', $goods_info['member_flower_price_section']);
				foreach($tmp_arr as $k=>$v){
					$tmp_flower_price_arr = explode('-', $v);
					$tmp_num = $tmp_flower_price_arr[0];
					$tmp_price = $tmp_flower_price_arr[1];
					if($data['num']>=$tmp_num){
						$pay_price = $tmp_price;
						$totalPrice = $pay_price*$data['num'];
					}
				}
			}elseif(!empty($goods_info['member_flower_price'])){
				$pay_price = $goods_info['member_flower_price'];
				$totalPrice = $pay_price*$data['num'];
			}
		}else{
			//普通人分段计费
			if(!empty($goods_info['normal_flower_price_section'])){
				// 1-20|2-19|3-10
				$tmp_arr = explode('|', $goods_info['normal_flower_price_section']);
				foreach($tmp_arr as $k=>$v){
					$tmp_flower_price_arr = explode('-', $v);
					$tmp_num = $tmp_flower_price_arr[0];
					$tmp_price = $tmp_flower_price_arr[1];
					if($data['num']>=$tmp_num){
						$pay_price = $tmp_price;
						$totalPrice = $pay_price*$data['num'];
					}
				}
			}
		}
		$totalPrice += $goods_info['flower_postage_price'];
		$return = ['totalPrice'=>$totalPrice,'price'=>$pay_price,'old_price'=>$old_price];
		return $return;
	}

	/* * 计算用户实际消耗的现金
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function realExpendMoney($user_info=[],$goods_info=[],$data=[]){
		$return = [];
		$ismember = $user_info['ismember'];
		$totalPrice = 0;
		$pay_price = 0;
		$old_price = 0;
		//没有任何额外的价格
		$pay_price = $old_price = $goods_info['price'];
		$totalPrice = $goods_info['price']*$data['num'];
		//如果为会员
		if(!empty($ismember)){
			//如果为分段计费
			if(!empty($goods_info['member_price_section'])){
				// 1-20|2-39|3-40
				$tmp_arr = explode('|', $goods_info['member_price_section']);
				foreach($tmp_arr as $k=>$v){
					$tmp_flower_price_arr = explode('-', $v);
					$tmp_num = $tmp_flower_price_arr[0];
					$tmp_price = $tmp_flower_price_arr[1];
					if($data['num']>=$tmp_num){
						$pay_price = $tmp_price;
						$totalPrice = $pay_price*$data['num'];
					}
				}
			}elseif(!empty($goods_info['member_price'])){
				$pay_price = $goods_info['member_price'];
				$totalPrice = $pay_price*$data['num'];
			}
		}else{
			//普通人分段计费
			if(!empty($goods_info['normal_price_section'])){
				// 1-20|2-19|3-10
				$tmp_arr = explode('|', $goods_info['normal_price_section']);
				foreach($tmp_arr as $k=>$v){
					$tmp_flower_price_arr = explode('-', $v);
					$tmp_num = $tmp_flower_price_arr[0];
					$tmp_price = $tmp_flower_price_arr[1];
					if($data['num']>=$tmp_num){
						$pay_price = $tmp_price;
						$totalPrice = $pay_price*$data['num'];
					}
				}
			}
		}
		$totalPrice += $goods_info['postage_price'];
		$return = ['totalPrice'=>$totalPrice,'price'=>$pay_price,'old_price'=>$old_price];
		return $return;
	}

	/**
	 * 开通相关权限
	 * @author :wang.hongli
	 * @since :2016/08/15
	 * @param : goodid 权限和商品id绑定
	 */
	public function passPermission($user_info=[],$goods_info=[],$data=[]){
		if(empty($user_info) || empty($goods_info) || empty($data)){
			return false;
		}
		$goodid = $goods_info['id'];
		$starttime = strtotime(date('Y-m-d'));
		$addtime = $data['num']*30*86400;
		$time = time();
		switch ($goodid) {
			case 59:
				//开通会员user_member
				$rs = DB::table('user_members')->where('uid',$user_info['id'])->first(['id','uid','starttime','endtime']);
				if($rs){
					$endtime = $rs['endtime']+$addtime;
					if(!DB::table('user_members')->where('uid',$user_info['id'])->update(['endtime'=>$endtime,'updatetime'=>$time])){
						return false;
					}
				}else{
					$endtime = $starttime+$addtime;
					$user_members = ['uid'=>$user_info['id'],'starttime'=>$starttime,'endtime'=>$endtime,'updatetime'=>$time];
					if(!DB::table('user_members')->insert($user_members)){
						return false;
					}
				}
				break;
			case 60:
				//开通私信通
				$rs = DB::table('user_letter_permission')->where('uid',$user_info['id'])->first(['id','uid','starttime','endtime']);
				if($rs){
					$endtime = $rs['endtime']+$addtime;
					if(!DB::table('user_letter_permission')->where('uid',$user_info['id'])->update(['endtime'=>$endtime,'updatetime'=>$time])){
						return false;
					}
				}else{
					$endtime = $starttime+$addtime;
					$user_letter_permission = ['uid'=>$user_info['id'],'starttime'=>$starttime,'endtime'=>$endtime,'updatetime'=>$time];
					if(!DB::table('user_letter_permission')->insert($user_letter_permission)){
						return false;
					}
				}
				break;
		}
		return true;
	}
	/**
	 * 开通会员赠送下载数
	 * @author :wang.hongli
	 * @since :2016/08/18
	 */
	public function giveDownNum($user_info=[],$goods_info=[],$data=[]){
		$giveDownNum = 0;
		if(empty($user_info) || empty($goods_info) || empty($data)){
			return $giveDownNum;
		}
		if($goods_info['id'] == 59){
			$down_opus_num = Config::get('app.down_opus_num');
			$num = $data['num']*$down_opus_num;
			$flag = DB::table('user_asset_num')->where('uid',$user_info['id'])->pluck('id');
			if($flag){
				DB::table('user_asset_num')->where('uid',$user_info['id'])->increment('down_total_num',$num);
			}else{
				$data = ['uid'=>$user_info['id'],'flower'=>0,'get_flower'=>0,'jewel'=>0,'cost_jewel'=>0,'down_total_num'=>$num];
				DB::table('user_asset_num')->insert($data);
			}
			//30天内下载数限制
			$starttime = strtotime(date('Y-m-d'));
			for($i=0;$i<$data['num'];$i++){
				$starttime = strtotime(date('Y-m-d'))+($i*30*86400);
				$endtime = $starttime+(30*86400);
				$data = ['starttime'=>$starttime,'endtime'=>$endtime,'uid'=>$user_info['id'],'down_num'=>$down_opus_num];
				DB::table('down_opus_limit')->insert($data);			
			}
		}
		return $giveDownNum;
	}
	/**
	 * 赠送钻石
	 * @author :wang.hongli
	 * @since :2016/08/15
	 */
	public function giveDiamond($user_info=[],$goods_info=[],$data=[]){
		$giveDiamond = 0;
		if(empty($user_info) || empty($goods_info) || empty($data)){
			return $giveDiamond;
		}
		$ismember = 0;
		if(isset($user_info['ismember'])){
			$ismember = $user_info['ismember'];
		}
		//赠送钻石数
		switch ($ismember) {
			case 0:
				if(!empty($goods_info['normal_section'])){
					$tmp_arr =explode('|', $goods_info['normal_section']);
					foreach($tmp_arr as $k=>$v){
						$tmp = explode('-', $v);
						$section = $tmp[0];
						$give = $tmp[1];
						if($data['num']>=$section){
							$giveDiamond = $give;
						}
					}
				}elseif(!empty($goods_info['diamond'])){
					$giveDiamond = $goods_info['diamond']*$data['num'];
				}
				break;
			case 1:
				if(!empty($goods_info['member_section'])){
					$tmp_arr =explode('|', $goods_info['member_section']);
					foreach($tmp_arr as $k=>$v){
						$tmp = explode('-', $v);
						$section = $tmp[0];
						$give = $tmp[1];
						if($data['num'] >= $section){
							$giveDiamond = $give;
						}
					}
				}elseif(!empty($goods_info['member_diamond'])){
					$giveDiamond = $goods_info['member_diamond']*$data['num'];
				}
				break;
		}
		if(!empty($giveDiamond)){
			$diamond_arr = [
				'fromid'=>0,
				'toid'=>$user_info['id'],
				'num'=>$giveDiamond,
				'time'=>time(),
				'flag'=>$data['flag'],
				'good_id'=>$goods_info['id'],
				'orderid'=>$data['orderid'],
			];
			//赠送钻石记录
			DB::table('user_diamond_list')->insert($diamond_arr);
			//赠送钻石
			$flag = DB::table('user_asset_num')->where('uid',$user_info['id'])->pluck('id');
			if($flag){
				DB::table('user_asset_num')->where('uid',$user_info['id'])->increment('jewel',$giveDiamond);
			}else{
				DB::table('user_asset_num')->insert(['uid'=>$user_info['id'],'flower'=>0,'get_flower'=>0,'jewel'=>$giveDiamond]);
			}
		}
	}

	/**
	 * 众筹商品增加众筹数
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function incrementCrowdfundinged($goods_info=[],$num=1){
		if(isset($goods_info['promptgoods']) && $goods_info['promptgoods'] == 2){
			//已经筹到的数量
			try {
				DB::table('good_param')->where('goodid',$goods_info['id'])->increment('crowdfundinged',$num);
			} catch (Exception $e) {
				
			}
		}
		return;
	}


}

 ?>