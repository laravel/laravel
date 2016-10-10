<?php 
/**
*	私人定制控制器
**/
class ApiPersonalCustomController extends ApiCommonController {
	private $apiPseronalCustom = null;
	public function __construct() {
		$this->apiPseronalCustom = new ApiPersonalCustom();
	}
	//获取私人定制列表
	public function getPCList() {
		$rs = $this->apiPseronalCustom->getPCList();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		}else {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			if(empty($rs)) {
				$this->setReturn(1);
			} else {
				$this->setReturn(1,'success',$rs,$hasmore);
			}
		}
	}

	//私人定制中删除自己转发，或者自己的作品
	public function delPCOpus() {
		$rs = $this->apiPseronalCustom->delPCOpus();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} else if (true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
}