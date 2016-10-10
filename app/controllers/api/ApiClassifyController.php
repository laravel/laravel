<?php 

/**
* 分类控制器
**/
class ApiClassifyController extends ApiCommonController {
	private $apiClassify;
	private $nolog;
	function __construct(){
		$this->apiClassify = new ApiClassify();
		$this->nolog=Lang::get('messages.nolog');//请登录
	}
	//得到个人作品分类
	public function getClassify() {
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info)) {
			$this->setReturn(-101,$this->nolog);
			return ;
		}
		$rs = $this->apiClassify->getClassify($user_info['id']);
		if($rs){
			$this->setReturn(1,'success',$rs);
			return ;
		}
	}
	//根据分类得到作品
	public function getOpus() {
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info['id'])) {
			$this->setReturn(-101,$this->nolog);return ;
		}
		$hasmore = 0;
		$pid=intval(Input::get('id',0));
		if(empty($pid)){
			$this->setReturn(0,'success',[],$hasmore);
			return;
		}
		$other_id=Input::get('other_id',0);
		$uid = $user_info['id'];
		if($other_id){
			$uid=$other_id;
		}
		//分页
		$count = intval(Input::get('count',20));
		$pageIndex = intval(Input::get('pageIndex',1));
		$offSet = ($pageIndex-1)*$count;
		++$count;
		$rs = $this->apiClassify->getOpus($uid,$pid,$offSet,$count);
		if(empty($rs)){
			$this->setReturn(1,'success',[],$hasmore);
			return;
		}
		if(isset($rs['hasmore'])){
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			if($hasmore == 1){
				array_pop($rs);
			}
		}
		$this->setReturn(1,'success',$rs,$hasmore);
	}
}