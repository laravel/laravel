<?php
/**
 * 所有回调调用的方法model
 * @author:wang.hongli
 * @since:2016/05/25
 */
class ApiPayCallBack extends ApiCommon{
	
	private $orderid;
	private $order_info;
	private $good_info;
	private $time;
	private $apiTianLaiGoods;
	
	/**
	 * 构造方法
	 * @param number $orderid 订单id
	 * @param array $order_info 订单信息
	 * @param array $good_info 商品信息
	 */
	function __construct($orderid=0,$order_info=array(),$good_info=array()){
		$this->orderid = $orderid;
		$this->order_info = $order_info;
		$this->good_info = $good_info;
		$this->time = time();
		$this->apiTianLaiGoods = new ApiTianLaiGoods();
	}
	/**
	 * 打赏回调逻辑
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function callReward(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$time = time();
			$arr = array('uid'=>$this->order_info['uid'],'sort'=>1,'flag'=>2,'type'=>3,'addtime'=>$time,'ext'=>$this->order_info['total_price']);
			DB::table('diploma')->insert($arr);
			//更新订单
			$this->updateOrderStatus();
		} catch (Exception $e) {
		}
		return true;
	}
	
	/**
	 * 夏青杯回调逻辑
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function callSummerCup(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try{
			//夏青杯 - 判断是否有权限了
			$over_time=strtotime("2015-08-01");
			$rlt = DB::table('user_permission')->where('type','=',2)->where('uid','=',$this->order_info['uid'])->first();
			if(!empty($rlt)){
				DB::table('user_permission')->where('uid',$this->order_info['uid'])->where('type',2)->update(array('over_time'=>$over_time,'update_time'=>$this->time));
			}else{
				DB::table('user_permission')->insert(array('uid'=>$this->order_info['uid'],'type'=>2,'update_time'=>$this->time,'over_time'=>$over_time));
			}
			//更新订单状态
			$this->updateOrderStatus();
		} catch (Exception $e){
		}
		return true;
	}
	
	/**
	 * 各种比赛回调逻辑
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function callCompetition(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$match = DB::table('competitionlist')->where('id','=',$this->good_info['competition_id'])->first(array('endtime','has_invitation'));
			//添加权限
			$over_time=$match['endtime'];
			$rlt = DB::table('user_permission')->where('type','=',$this->good_info['competition_id'])->where('uid','=',$this->order_info['uid'])->first();
			if(!empty($rlt)){
				DB::table('user_permission')->where('uid',$this->order_info['uid'])->where('type',$this->good_info['competition_id'])->update(array('over_time'=>$over_time,'update_time'=>$this->time));
			}else{
				DB::table('user_permission')->insert(array('uid'=>$this->order_info['uid'],'type'=>$this->good_info['competition_id'],'update_time'=>$this->time,'over_time'=>$over_time,'good_id'=>$this->good_info['id']));
			}
			//记录邀请码
			if($match['has_invitation']==1){
				$code_info = DB::table('user_match')->where('uid','=',$this->order_info['uid'])->first();
				if(!empty($code_info) && $code_info['invitationcode']){
					DB::table('order_list')->where('id',$this->orderid)->update(array('invitationcode'=>$code_info['invitationcode']));
				}
			}
			//更新订单状态
			$this->updateOrderStatus();
		} catch (Exception $e) {
		}
		return true;
	}
	
	/**
	 * 班级活动回调逻辑
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function callClassActive(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}

		try {
			//更新class_active中用户添加的最新一条记录
			$class_active_info = DB::table('class_active_form')->where('uid',$this->order_info['uid'])->where('status',0)->orderBy('id','desc')->take(1)->first(array('id','invitationcode'));
			if(empty($class_active_info)){
				return true;
			}
			//记录邀请码
			if(!empty($class_active_info['invitationcode'])){
				DB::table('order_list')->where('id',$this->orderid)->update(array('invitationcode'=>$class_active_info['invitationcode']));
			}
			//更新提交表单状态
			DB::table('class_active_form')->where('id',$class_active_info['id'])->update(array('status'=>1,'orderid'=>$this->orderid,'goods_id'=>$this->good_info['id']));
			//更新订单状态
			$this->updateOrderStatus();
		} catch (Exception $e) {
		}
		return true;
	}

	/**
	 * 结束比赛逻辑处理
	 * @author :wang.hongli
	 * @since :2016/07/11
	 */
	public function callFinishCompetition(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		//判断是否为单独购买光盘
		if($this->good_info['flag'] == 2 && !empty($this->good_info['good_pid'])){
			try {
				DB::table('order_list')->where('goods_id',$this->good_info['good_pid'])->where('uid',$this->order_info['uid'])->where('status',2)->update(['attach_id'=>$this->good_info['id'],'attach_price'=>$this->good_info['price']]);
			} catch (Exception $e) {
			}
		}
		$this->updateOrderStatus();
		return true;
	}

	/**
	 * 中华诵读联合会交过费的自动通过审核
	 * @author :wang.hongli
	 * @since :2016/07/14
	 */
	public function callLeague(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$rs = DB::table('user_permission')->where('uid','=',$this->order_info['uid'])->where('type','=',1)->first();
			if(!empty($rs)){
				DB::table('league')->where('uid','=',$this->order_info['uid'])->update(array('status'=>2));
				DB::table('order_list')->where('orderid','=',$this->orderid)->update(['audit_status'=>2,'audit_time'=>$this->time]);
				//更新user_permission过期时间
				$num = !empty($this->order_info['num']) ? intval($this->order_info['num']) : 1;
				$add_time = $num*365*86400;
				$sql = "update user_permission set over_time=over_time+?,update_time=? where uid = ? and type = 1";
				DB::update($sql,[$add_time,$this->time,$this->order_info['uid']]);
				DB::table('user')->where('id',$this->order_info['uid'])->update(['isleague'=>1]);
				//插入到自动审核表
				DB::table('league_auto_pass')->insert(['uid'=>$this->order_info['uid'],'type'=>0,'order_id'=>$this->orderid]);
				$id = DB::table('league_user')->where('uid',$this->order_info['uid'])->pluck('id');
				if(empty($id)){
					//将数据插入到league_user联合会冗余表中
					$user_info = DB::table('user')->where('id',$this->order_info['uid'])->first(array('praisenum','lnum','repostnum'));
					DB::table('league_user')->insert(array('id'=>0,'uid'=>$this->order_info['uid'],'praisenum'=>$user_info['praisenum'],'lnum'=>$user_info['lnum'],'repostnum'=>$user_info['repostnum'],'addtime'=>time()));
					//将用户认证信息保留
					$auth_content = DB::table('user_auth_content')->where('uid',$this->order_info['uid'])->first(['id','uid','auth_content']);
					if(!empty($auth_content['auth_content'])){
						DB::table('user')->where('id',$this->order_info['uid'])->update(['authconent'=>$auth_content['auth_content'],'authtype'=>1]);
					}
				}
			}
		} catch (Exception $e) {
		}
		$this->updateOrderStatus();
		return true;
	}
	
	/**
	 * 开通会员
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function callPassMember(){

		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$user_info['id'] = $this->order_info['uid'];
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

			$data['num'] = $this->order_info['num'];
			$data['orderid'] = $this->order_info['orderid'];
			//众筹商品增加众筹数
			// $this->apiTianLaiGoods->incrementCrowdfundinged($this->good_info,$this->order_info['num']);
			//开通相关权限
			$this->apiTianLaiGoods->passPermission($user_info,$this->good_info,$data);
			//赠送钻石等操作
			$this->apiTianLaiGoods->giveDiamond($user_info,$this->good_info,$data);
			//开通会员赠送下载数
			$this->apiTianLaiGoods->giveDownNum($user_info,$this->goods_info,$data);
		} catch (Exception $e) {
			
		}
		//更新订单状态
		$this->updateOrderStatus();
		return true;
	}
	/**
	 * 开通私信通
	 * @author:wang.hongli
	 * @since :2016/08/16
	 */
	public function callPassPersonLetter(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$user_info['id'] = $this->order_info['uid'];
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

			$data['num'] = $this->order_info['num'];
			$data['orderid'] = $this->order_info['orderid'];

			//众筹商品增加众筹数
			// $this->apiTianLaiGoods->incrementCrowdfundinged($this->good_info,$this->order_info['num']);
			//开通相关权限
			$this->apiTianLaiGoods->passPermission($user_info,$this->good_info,$data);
			//赠送钻石等操作
			$this->apiTianLaiGoods->giveDiamond($user_info,$this->good_info,$data);

		} catch (Exception $e) {
			
		}
		//更新订单状态
		$this->updateOrderStatus();
		return true;
	}
	/**
	 * 实体商品
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function callEntityGood(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$user_info['id'] = $this->order_info['uid'];
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

			$data['num'] = $this->order_info['num'];
			$data['orderid'] = $this->order_info['orderid'];

			//众筹商品增加众筹数
			$this->apiTianLaiGoods->incrementCrowdfundinged($this->good_info,$this->order_info['num']);
			//开通相关权限
			// $this->apiTianLaiGoods->passPermission($user_info,$this->good_info,$data);
			//赠送钻石等操作
			$this->apiTianLaiGoods->giveDiamond($user_info,$this->good_info,$data);
		} catch (Exception $e) {
		}
		//更新订单状态
		$this->updateOrderStatus();
		return true;
	}

	/**
	 * 出版物
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function callPublicationGood(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$user_info['id'] = $this->order_info['uid'];
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

			$data['num'] = $this->order_info['num'];
			$data['orderid'] = $this->order_info['orderid'];
			$data['flag'] = 3;
			//众筹商品增加众筹数
			$this->apiTianLaiGoods->incrementCrowdfundinged($this->good_info,$this->order_info['num']);
			//开通相关权限
			// $this->apiTianLaiGoods->passPermission($user_info,$this->good_info,$data);
			//赠送钻石等操作
			$this->apiTianLaiGoods->giveDiamond($user_info,$this->good_info,$data);
		} catch (Exception $e) {
		}
		//更新订单状态
		$this->updateOrderStatus();
		return true;
	}

	/**
	 * 购买钻石
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function callBuyDiamond(){
		if(empty($this->order_info) || empty($this->good_info) || empty($this->orderid)){
			return true;
		}
		//避免多次重复调用
		if(!empty($this->order_info['status']) && $this->order_info['status'] == 2){
			return true;
		}
		try {
			$user_info['id'] = $this->order_info['uid'];
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

			$data['num'] = $this->order_info['num'];
			$data['orderid'] = $this->order_info['orderid'];
			$data['flag'] = 3;
			//购买钻石
			$this->apiTianLaiGoods->buyDiamond($user_info,$this->good_info,$data);
			//赠送钻石等操作
			$data['flag'] = 5;
			$this->apiTianLaiGoods->giveDiamond($user_info,$this->good_info,$data);
		} catch (Exception $e) {
		}
		//更新订单状态
		$this->updateOrderStatus();
		return true;
	}
	/**
	 * 操作成功后，更新订单状态
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function updateOrderStatus(){
		//更新订单状态
		try {
			DB::table('order_list')->where('orderid',$this->orderid)->update(array('status'=>2,'updatetime'=>$this->time));
		} catch (Exception $e) {
		}
		return ;
	}
	
}