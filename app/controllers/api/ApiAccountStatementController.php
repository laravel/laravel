<?php 
/**
 * 账单controller
 * @author :wang.hongli
 * @since :2016/08/17
 */

class ApiAccountStatementController extends ApiCommonController {
	private $apiAccountStatement;
	function __construct(){
		parent::__construct();
		$this->apiAccountStatement = new ApiAccountStatement();
	}
	/**
	 * 获取我的账单头部导航
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function getAccountStatementNav(){
		$return = $this->apiAccountStatement->getAccountStatementNav();
		$this->setReturn(1,'success',$return);
	}
	/**
	 * 钻石明细
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function diamondDetailList(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$pageIndex = intval(Input::get('pageIndex',1));
		$count = intval(Input::get('count',20));
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$diamondDetailList = $this->apiAccountStatement->diamondDetailList($user_info['id'],$offSet,$count);
		$hasmore = 0;
		if(count($diamondDetailList) >= $count){
			$hasmore = 1;
			array_pop($diamondDetailList);
		}
		$this->setReturn(1,'success',$diamondDetailList,$hasmore);
	}

	/**
	 * 鲜花明细
	 * @author :wang.hongli
	 * @since :2016/08/17
	 */
	public function flowerDetailList(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$pageIndex = intval(Input::get('pageIndex',1));
		$count = intval(Input::get('count',20));
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$list = $this->apiAccountStatement->flowerDetailList($user_info['id'],$offSet,$count);
		$hasmore = 0;
		if(count($list) >= $count){
			$hasmore = 1;
			array_pop($list);
		}
		$this->setReturn(1,'success',$list,$hasmore);
	}
}


 ?>