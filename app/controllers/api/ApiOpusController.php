<?php 
	
/**
* 作品控制器
*/
class ApiOpusController extends ApiCommonController {
	private $apiOpus = null;
	public function __construct() {
		$this->apiOpus = new ApiOpus();
	}
	//作品上传
	public function uploadOpus() {
		$rs = $this->apiOpus->uploadOpus();
		if('nolog'===$rs) {
			$this->setReturn(-101,'请登录');
		} elseif(is_array($rs)) {
			$this->setReturn(1,'success',$rs);
		} else {
			$this->setReturn(0,$rs);
		}
	}

	//我 or 他的作品列表
	public function getOpusList() {
		$rs = $this->apiOpus->getOpusList();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(empty($rs)) {
			$this->setReturn(1,'success',array());
		} elseif(is_array($rs)) {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		}
	}

	//删除作品
	public function delOpus() {
		$rs = $this->apiOpus->delOpus();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}

	//收听作品
	public function opusListen() {
		$rs = $this->apiOpus->opusListen();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		}else if(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
				
	}

	//根据作品id获取作品信息
	public function accorOpusIdGetInfo() {
		$rs = $this->apiOpus->accorOpusIdGetInfo();
		if(is_array($rs)) {
			$this->setReturn(1,'success',$rs);
		} else {
			$this->setReturn(0,$rs);
		}
	}

	//第三方转发成功，人，作品转发数+1
	public function successShareInNum() {
		$rs = $this->apiOpus->successShareInNum();
		if('nolog'=== $rs) {
			$this->setReturn(-101,'请登录');
		} else if(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
	//举报作品
	public function report() {
		if($this->apiOpus->report()) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0);
		}
	}
	public function totop(){
		$rs = $this->apiOpus->totop();
		if('nolog'=== $rs) {
			$this->setReturn(-101,'请登录');
		} else if(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0);
		}
	}

}
 ?>