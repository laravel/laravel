<?php 
/**
*后台商品管理列表
*@author:wang.hongli
*@since:2016/07/08
**/
class AdminGoods extends AdminCommon{

	/**
	*	添加商品分类
	*	@author:wang.hongli
	*	@since:2016/07/08
	**/
	public function addGoodCategory($data=[]){
		$rules = [
			'name'=>'required|alpha|unique:goods_category,name',
			'sort'=>'required|integer'
		];
		$message = [
			'name.required'=>'请填写分类名称',
			'name.alpha'=>'分类名称错误',
			'name.unique'=>'该分类存在',
			'sort.required'=>'请填写排序',
			'sort.integer'=>'排序必须为数字',
		];
		$validator = Validator::make ( $data, $rules,$message );
		if ($validator->fails ()) {
			$msg =  $validator->messages()->first();
			return $msg;
		}
		DB::table('goods_category')->insert($data);
		return true;
	}

	/**
	 * 获取商品分类
	 * @author :wang.hongli
	 * @since :2016/07/08
	 */
	public function getGoodsCategory(){
		$category = DB::table('goods_category')->where('status',0)->get();
		if(empty($category)){
			return [];
		}
		$rs = [];
		foreach($category as $k=>$v){
			$rs[$v['id']] = $v;
		}
		return $rs;
	}

	/**
	 * 获取结束比赛
	 * @author :wanghongli <[<email address>]>
	 * @since :2016/07/08 [<description>]
	 */
	public function getFinishCompetition(){
		$tmp = DB::table('competitionlist')->whereIn('pid',[4,5,6,7])->where('isfinish',1)->get();
		$finish_competition_list = [];
		if(!empty($tmp)){
			foreach($tmp as $k=>$v){
				$finish_competition_list[$v['id']] = $v['name'];
			}
		}
		return $finish_competition_list;
	}

	/**
	 * 添加结束比赛商品
	 * @author :wang.hongli
	 * @since : [<2016/07/08>]
	 */
	public function addFinishCompGoods($data=[]){
		if(empty($data)){
			return '添加错误';
		}
		$rules = [
			'name'=>'required|alpha|unique:goods,name',
			'price'=>'required',
			'type'=>'required|integer',
			'competition_id'=>'required|integer',
			'cd_price'=>'numeric',
		];
		$validator = Validator::make ( $data, $rules );
		if ($validator->fails ()) {
			return false;
		}
		if(isset($data['_token'])){
			unset($data['_token']);
		}
		$time = time();
		$good_data = [
			'name'=>$data['name'],
			'price'=>$data['price'],
			'type'=>$data['type'],
			'description'=>$data['description'],
			'competition_id'=>$data['competition_id'],
			'flag'=>2,
			'start_time'=>$time,
			'end_time'=>$time,
			'discount_price'=>0,
			'good_pid'=>0
		];
		try {
			$good_id = DB::table('goods')->insertGetId($good_data);
			$cd_name = !empty($data['cd_name']) ? $data['cd_name'] : '活动现场光盘';
			if($good_id && !empty($data['cd_price'] )){
				$good_attach = [
					'name'=>$cd_name,
					'price'=>$data['cd_price'],
					'type'=>$data['type'],
					'description'=>$cd_name,
					'competition_id'=>$data['competition_id'],
					'flag'=>2,
					'start_time'=>$time,
					'end_time'=>$time,
					'discount_price'=>0,
					'good_pid'=>$good_id,
				];
				DB::table('goods')->insert($good_attach);
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
?>