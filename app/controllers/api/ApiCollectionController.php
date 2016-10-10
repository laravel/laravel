<?php 

/**
*	收藏控制器
**/
class ApiCollectionController extends ApiCommonController {
	private $apiCollection = null;
	public function __construct() {
		$this->apiCollection = new ApiCollection();
	}
	//增加 or 删除收藏
	public function colEdit() {
		$rs = $this->apiCollection->colEdit();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
	
	//获取收藏列表
	public function colList() {
		$rs = $this->apiCollection->colList();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(empty($rs)) {
			$this->setReturn(1);
		} elseif(is_array($rs)) {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		}
	}
}
