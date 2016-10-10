<?php

/**
* 第三方广告控制器
* @author :wang.hongli
* @since :2016/08/09
**/
class ApiThirdAdvertisingController extends  ApiCommonController {
	
	private $apiThirdAdvertising;
	function  __construct(){
		parent::__construct();
		$this->apiThirdAdvertising = new ApiThirdAdvertising();
	}
	/**
	 * 获取广告列表
	 * @author:wang.hongli
	 * @since :2016/08/09
	 * @param : column_id:所属栏目id
	 */
	public function getThirdAdvList(){
		$column_id = intval(Input::get('column_id',1));
		$platform = intval(Input::get('platform',0));
		$size = 5;
		$time = time();
		$list = $this->apiThirdAdvertising->getThirdAdvList($column_id,$platform,$size,$time);
		$this->setReturn(1,'success',$list);
	}	
}