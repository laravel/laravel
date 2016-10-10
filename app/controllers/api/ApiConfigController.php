<?php 
/**
*api配置文件
*@author:wang.hongli
*@since:2016/06/28
*/
class ApiConfigController extends ApiCommonController {

	/**
	* 
	* @author:wang.hongli
	* @since:2016/06/28
	* @param:captcha 1 open 0 close 验证码
	**/
	public function getConfig(){
		$config = [
			'captcha'=>1,
		];
		$this->setReturn(1,'success',$config);
	}
}
?>