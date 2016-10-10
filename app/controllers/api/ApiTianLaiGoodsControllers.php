<?php 
	
/**
 * 天籁商城
 * @author:wang.hongli
 * @since :2016/08/14
 * */

class ApiTianLaiGoodsControllers extends ApiCommonController  {

	private $apiTianLaiGoods;
	private $apiPay;
	function __construct(){
		parent::__construct();
		$this->apiTianLaiGoods = new ApiTianLaiGoods();
		$this->apiPay = new ApiPay();
	}
	/**
	 * 获取天籁商城导航
	 * @author:wang.hongli
	 * @since :2016/08/14
	 */
	public function getTianLaiNav(){
		$rs = $this->apiTianLaiGoods->getTianLaiNav();
		$this->setReturn(1,'success',$rs);
	}

	/**
	 * 获取天籁商城商品列表
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function getTianLaiGoodsList(){
		$flag = intval(Input::get('flag',3));
		$count = intval(Input::get('count',20));
		$pageIndex =intval(Input::get('pageIndex',1));
		$list = $this->apiTianLaiGoods->getTianLaiGoodsList($flag,$pageIndex,$count);
		$hasmore = $list['hasmore'];
		unset($list['hasmore']);
		$this->setReturn(1,'success',$list,$hasmore);
	}

	/**
	 * 获取天籁商城商品详细信息
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function getTianlaiGoodsInfo(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(!$user_info['id']){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		//商品id
		$goodid = Input::get('goodid',0);
		//商品数量
		$goodsNum = abs(intval(Input::get('goods_num',1)));
		if(empty($goodid) || empty($goodsNum)){
			$this->setReturn(0,'获取商品详情失败');
			return;
		}
		$goodInfo = $this->apiTianLaiGoods->getTianlaiGoodsInfo($user_info,$goodid,$goodsNum);
		if(!$goodInfo){
			$this->setReturn(0,'获取商品详情失败');
		}else{
			$this->setReturn(1,'success',$goodInfo);
		}
	}
	/**
	 * 生成订单号
	 * @author :wang.hongli
	 * @since :2016/08/15
	 */
	// public function genOrderid(){
	// 	$orderid = $this->apiPay->genOrderid();
	// 	$this->setReturn(1,'success',$orderid);
	// }
	/**
	 * 鲜花兑换商品
	 * @author :wang.hongli
	 * @since :2016/08/15
	 */
	public function exchangeGood(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$data['uid'] = $user_info['id'];
		$data['goods_id'] = intval(Input::get('goods_id',0));
		if(empty($data['goods_id'])){
			$this->setReturn(-1,'此商品已下架');
			return;
		}
		$data['num'] = abs(intval(Input::get('num',1)));
		//兑换鲜花
		$data['pay_type'] = intval(Input::get('pay_type',0));
		$data['plat_from'] = intval(Input::get('plat_from',0));
		$data['comments'] = Input::get('comments','');
		$apiPay = new ApiPay();
		$data['orderid'] = $apiPay->genOrderid();
		if($data['orderid'] == -1){
			$this->setReturn(-4,'订单号生成错误');
			return;
		}
		$data['address_id']  = Input::get('address_id',0);
		$pattern = '/^\d{28}$/';
		if(empty($data['orderid']) || !preg_match($pattern, $data['orderid'])){
			$this->setReturn(-4,'订单号错误');
			return;
		}
		//判断是否为会员
		$apicheckPermission = App::make('apicheckPermission');
		$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

		$return = $this->apiTianLaiGoods->exchangeGood($user_info,$data);
		if($return['code'] == 1){
			$this->setReturn(1,$return['data']['orderid'],$data);
		}else{
			$this->setReturn($return['code'],$return['msg']);
		}
	}
	/**
	 * 插入订单
	 * @author :wang.hongli
	 * @since :2016/08/14
	 */
	public function insertTianLaiOrder(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$data['uid'] = $user_info['id'];
		$data['goods_id'] = intval(Input::get('goods_id',0));
		if(empty($data['goods_id'])){
			$this->setReturn(-1,'此商品已下架');
			return;
		}
		$data['num'] = abs(intval(Input::get('num',1)));
		//支付类型 支付类型 1是银联2支付宝3支付宝网页版4财付通
		$data['pay_type'] = intval(Input::get('pay_type',0));
		if(empty($data['pay_type'])){
			$this->setReturn(0,'支付错误');
		}
		// 平台类型 0 ios 1 android
		$data['plat_from'] = intval(Input::get('plat_from',0));
		//备注
		$data['comments'] = Input::get('comments','');

		$apiPay = new ApiPay();
		$data['orderid'] = $apiPay->genOrderid();
		if($data['orderid'] == -1){
			$this->setReturn(-4,'订单号生成错误');
			return;
		}
		$data['address_id']  = Input::get('address_id',0);
		$pattern = '/^\d{28}$/';
		if(empty($data['orderid']) || !preg_match($pattern, $data['orderid'])){
			$this->setReturn(-4,'订单号错误');
			return;
		}
		//判断是否为会员
		$apicheckPermission = App::make('apicheckPermission');
		$user_info['ismember'] = $apicheckPermission->isMember($user_info['id']);

		$return = $this->apiTianLaiGoods->insertTianLaiOrder($user_info,$data);
		if($return['code'] == 1){
			$this->setReturn(1,$return['orderid'],$return['data']);
		}else{
			$this->setReturn($return['code'],$return['msg']);
		}
	}
}
?>