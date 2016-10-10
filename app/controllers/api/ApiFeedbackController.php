<?php 
/**
*	意见反接口
*	@author:wang.hongli
*	@since:2015/01/11
**/
class ApiFeedbackController extends ApiCommonController
{
	/**
	*	添加反馈
	*	@author:wang.hongli
	*	@since:2015/01/11
	*/
	public function feedBack()
	{
		$apiFeedback = new ApiFeedBack;
		$status = $apiFeedback->feedBack();
		switch ($status) {
			case -1:
				$this->setReturn(-101,'请登录');
				break;
			case -2:
				$this->setReturn(0,'请填写所有信息');
				break;
			case -3:
				$this->setReturn(0,'反馈失败，请重试');
				break;
			case -4:
				$this->setReturn(0,'反馈内容含有禁用词，请重试');
				break;
			case 1:
				$this->setReturn(1);
		}
	}
}