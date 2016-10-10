<?php 

/**
* 赞控制器
**/
class ApiPraiseController extends ApiCommonController {
	
	//增加/取消赞
	public function praiseEdit() {
		$apiPraise = new ApiPraise();
		$rs = $apiPraise->praiseEdit();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
}