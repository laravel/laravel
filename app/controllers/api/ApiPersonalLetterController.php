<?php 
/**
*	发送私信控制器
**/
class ApiPersonalLetterController extends ApiCommonController {

	private $apiPersonalLetter = null;
	public function __construct() {
		$this->apiPersonalLetter = new ApiPersonalLetter();
	}
	//发送私信
	public function sendPersonLetter() {
		$rs = $this->apiPersonalLetter->sendPersonLetter();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		}  elseif(is_numeric($rs)) {
			$this->setReturn(1,'success',$rs);
		} else {
			$this->setReturn(0,$rs);
		}
	}

	//获取私信列表
	public function persinalLetterList() {
		$rs = $this->apiPersonalLetter->persinalLetterList();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(is_array($rs)) {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			if(!empty($rs)) {
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		} else {
			$this->setReturn(1);
		}
	}

	//删除私信
	public function delPersinalLetter() {
		$rs = $this->apiPersonalLetter->delPersinalLetter();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
}