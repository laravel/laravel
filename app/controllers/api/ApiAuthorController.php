<?php 
/**
*	用户认证
*	@author:wang.hongli
*	@since:2015/01/11
**/
class ApiAuthorController extends ApiCommonController
{

	/**
	*	添加认证
	*	@author:wang.hongli
	*	@since:2015/01/11
	**/
	public function author()
	{
		$apiAuthor = new ApiAuthor;
		$status = $apiAuthor->author();
		switch ($status) {
			case -1:
				$this->setReturn(-101,'请登录');
				break;
			case -2:
				$this->setReturn(0,'请填写所有信息');
				break;
			case -3:
				$this->setReturn(0,'认证失败，请重试');
				break;
			case 1:
				$this->setReturn(1);
				break;
		}
	}
}