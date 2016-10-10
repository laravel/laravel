<?php 
/**
 * 用户获取订单相关信息
 * @author :wang.hongli
 * @since :2016/08/16
 */
class ApiOrderListController extends ApiCommonController {

	private $apiOrderList;
	function __construct(){
		parent::__construct();
		$this->apiOrderList = new ApiOrderList();
	}

	/**
	 * 获取自己用户订单列表
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function myOrderList(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$pageIndex = intval(Input::get('pageIndex',1));
		$count = intval(Input::get('count',20));
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$uid = $user_info['id'];
		$rs = $this->apiOrderList->myOrderList($uid,$offSet,$count);
		$hasmore = 0;
		if(count($rs) >= $count){
			$hasmore = 1;
			array_pop($rs);
		}
		$this->setReturn(1,'success',$rs,$hasmore);
	}

	/**
	 * 获取订单详情
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function orderInfo(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$orderId = Input::get('orderid',0);
		//支付方式0现金1兑换
		$isexchange = intval(Input::get('isexchange',0));
		$pattern = '/\d{28}/';
		if(!preg_match($pattern, $orderId)){
			$this->setReturn(-5,'订单号错误');
			return;
		}
		$orderInfo = $this->apiOrderList->orderInfo($user_info['id'],$orderId,$isexchange);
		$this->setReturn(1,'success',$orderInfo);
	}

	/**
	 * 删除订单
	 * @author :wang.hongli
	 * @since :2016/08/18
	 * @param : isexchange 0 现金购买 1 鲜花兑换
	 */
	public function delMyOrder(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$id = intval(Input::get('id',0));
		if(empty($id)){
			$this->setReturn(0,'删除订单失败');
			return;
		}
		$isexchange = intval(Input::get('isexchange',0));
		$flag = $this->apiOrderList->delMyOrder($id,$user_info['id'],$isexchange);
		if(!$flag){
			$this->setReturn(0,'删除订单失败');
			return;
		}
		$this->setReturn(1,'success',$id);
	}
}


 ?>