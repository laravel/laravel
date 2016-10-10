<?php
/**
 * 班级活动控制器
 * @author:wang.hongli
 * @since:2016/05/25
 */
class ApiClassActiveController extends ApiCommonController{
	
	private $apiClassActive;
 
	function __construct(){
		$this->apiClassActive = new ApiClassActive();
	}
	
	/**
	 * 用户参加活动
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function joinClassActive(){
		$data = dealPostData();
		$rs = $this->apiClassActive->joinClassActive($data);
		if($rs === 'nolog'){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		if(is_numeric($rs)){
			$this->setReturn(1,'success',$rs);
		}else{
			$this->setReturn(0,$rs);
		}
	}
	
	/**
	 * 用户获取最近添加过的表单信息
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function getClassActiveUserInfo(){
		$rs = $this->apiClassActive->getClassActiveUserInfo();
		if($rs === 'nolog'){
			$this->setReturn(-101,Lang::get('messages.nolog'));
		}else{
			$this->setReturn(1,$rs);
		}
	}
	
	/**
	 * 根据活动id获取活动详情
	 * @auhtor:wang.hongli
	 * @since:2016/05/31
	 */
	public function getClassActiveInfo(){
		$data = dealPostData();
		$rs = $this->apiClassActive->getClassActiveInfo($data);
		if(is_array($rs)){
			$this->setReturn(1,'success',$rs);
			return;
		}
		$this->setReturn(0,$rs);
	}
}