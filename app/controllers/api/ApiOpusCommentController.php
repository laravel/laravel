<?php 
/**
* 作品评论控制器
**/
class ApiOpusCommentController extends ApiCommonController  {
	private $apiOpusComment = null;
	public function __construct() {
		$this->apiOpusComment = new ApiOpusComment();
	}
	//作品评论
	public function commentOpus() {
		$rs = $this->apiOpusComment->commentOpus();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} elseif(is_numeric($rs)) {
			$this->setReturn(1,'success',$rs);
		} else {
			$this->setReturn(0,$rs);
		}
	}

	//作品评论列表
	public function getCommentList() {
		$rs = $this->apiOpusComment->getCommentList();
		if(is_array($rs)) {
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

	//删除作品评论
	public function delOpusComment() {
		$rs = $this->apiOpusComment->delOpusComment();
		if('nolog' === $rs) {
			$this->setReturn(-101,'请登录');
		} elseif(true === $rs) {
			$this->setReturn(1);
		} else {
			$this->setReturn(0,$rs);
		}
	}
}
 ?>