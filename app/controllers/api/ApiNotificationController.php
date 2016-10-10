<?php 
/**
*	消息列表控制器
**/
class ApiNotificationController extends ApiCommonController {

	/**
	 * 获取消息列表
	 * @author:wang.hongli
	 * @since:2016/06/06
	 */
	public function getNotificationList(){
		$apiNotification = new ApiNotification();
		$rs = $apiNotification->getNotification();
		if($rs === 'nolog'){
			$this->setReturn(-101,'请登录');
		}elseif(is_array($rs)){
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		}else{
			$this->setReturn(1,'success',array());
		}
	}
	/**
	 * 删除消息
	 * @author:wang.hongli
	 * ＠since:2016/06/11
	 */
	public function delNotification(){
		$apiNotification = new ApiNotification();
		$rs = $apiNotification->delNotification();
		if('nolog' === $rs){
			$this->setReturn(-101,'请登录');
		}elseif(true === $rs){
			$this->setReturn(1);
		}else {
			$this->setReturn(0,$rs);
		}
	}

	//标记某条消息是否读过
	public function isReadedStatus() {
		$apiNotification = new ApiNotification();
		$apiNotification->isReadedStatus();
	}
	/**
	 * 获取消息数量
	 * @author:wang.hongli
	 * @since:2016/06/11
	 */
	public function getNotificationNum(){
		$apiNotification = new ApiNotification();
		$num = $apiNotification->getNotificationNum();
		if('nolog' === $num){
			$this->setReturn(-101,'请登陆');
		}else{
			$this->setReturn(1,'success',$num);
		}
	}
}