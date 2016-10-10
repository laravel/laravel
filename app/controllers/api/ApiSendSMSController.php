<?php
/**
 * 发送验证码
 * @author:wang.hongli
 * @since:2016/05/27
 */
class ApiSendSMSController extends ApiCommonController{

	/**
	 * 发送短信验证码
	 * @author:wang.hongli
	 * @since:2016/05/27
	 * @param:phone手机号 ;flag 1 注册 2 绑定手机号
	 */
	public function sendSMS(){
		$phone = Input::get('phone');
		$flag = intval(Input::get('flag'));
		$code = mt_rand(100000,999999);
		$apiSendSMS = new ApiSendSMS();
		$rs = $apiSendSMS->sendSMS($phone, $code,$flag);
		if(is_array($rs)){
			$this->setReturn(1,'succes',$rs);
		}else{
			$this->setReturn(0,$rs);
		}
	}
}