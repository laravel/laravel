<?php
/**
 * 打赏model
 * @author:wang.hongli
 * @since:2016/04/29
 */
class ApiRewardController extends ApiCommonController {
	
	private $apiReward = '';
	public function __construct(){
		$this->apiReward =  new ApiReward();
	}
	/**
	 * 获取打赏的随机金额
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function getRandomMoney(){
		if(!Input::has('goods_id')){
			$this->setReturn(0);
			return;
		}
		$goods_id = intval(Input::get('goods_id'));
		$random = $this->apiReward->getRandomMoney($goods_id);
		if(empty($random)){
			$this->setReturn(0);
			return;
		}
		$this->setReturn(1,'success',$random);
	}
}