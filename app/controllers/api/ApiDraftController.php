<?php 
	
/**
* 草稿箱控制器
*/
class ApiDraftController extends ApiCommonController {
	private $apiDraft = null;
	private $nolog = null;
	public function __construct() {
		$this->apiDraft = new ApiDraft();
		$this->nolog=Lang::get('messages.nolog');//请登录
	}
	//草稿上传
	//同正常上传
	public function uploadDraft() {
		$rs = $this->apiDraft->uploadDraft();
		if('nolog'===$rs) {
			$this->setReturn(-101,$this->nolog);
		} elseif($rs===true) {
			$this->setReturn(1,'success',$rs);
		} elseif($rs===false)  {
			$this->setReturn(0,'error',$rs);
		}
	}
	//草稿箱列表
	//同正常列表
	public function getDraftList() {
		$rs = $this->apiDraft->getDraftList();
		if('nolog' === $rs) {
			$this->setReturn(-101,$this->nolog);
		} elseif($rs=='nodata') {
			$this->setReturn(1,"",array(),0);
		} elseif(is_array($rs)) {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		}
	}
	//删除草稿
	//暂时未删除文件 
	public function delDraft() {
		$rs = $this->apiDraft->delDraft();
		if('nolog' === $rs) {
			$this->setReturn(-101,$this->nolog);
		}else if('noID' === $rs) {
			$this->setReturn(-200,"请传作品id");
		}  elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
 
	 //正式发布作品
	 public function toOpus() {	
		$rs = $this->apiDraft->toOpus();
	 if('noID' === $rs) {
			$this->setReturn(-200,"请传作品id");
		} elseif( is_int($rs)) {
			$this->setReturn(1,'success',$rs,0);
		}else {
			$this->setReturn(0,$rs);
		}
	}
		//根据作品id获取作品信息
	public function DraftIdGetInfo() {
		$rs = $this->apiDraft->DraftIdGetInfo();
		if(is_array($rs)) {
			$this->setReturn(1,'success',$rs);
		} else {
			$this->setReturn(0,$rs);
		}
	}
}
 ?>                                                                                                                                                                                  