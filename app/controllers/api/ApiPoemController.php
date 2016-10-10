<?php 
	/**
	* 和诗相关的接口
	**/
	class ApiPoemController extends ApiCommonController {
		private $apiPoem = null;
		public function __construct() {
			$this->apiPoem = new ApiPoem();
		}
		//诗人性别分类列表
		public function getPoemerCat() {
			$rs = $this->apiPoem->getPoemerCat();
			if(!empty($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(1);
			}
		}	
		//根据性别分类查找写者	
		public function getWriterList() {
			$rs = $this->apiPoem->getWriterList();
			if(!empty($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}
		//根据性别分类查找读者
		public function getReaderList() {
			$rs = $this->apiPoem->getReaderList();
			if(!empty($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}

		//根据写者id查找原诗
		public function getPoemByWriterId() {
			$rs = $this->apiPoem->getPoemByWriterId();
			if(!empty($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}

		//根据读者id查找原诗
		public function getPoemByReaderId() {
			$rs = $this->apiPoem->getPoemByReaderId();
			if(!empty($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}
		//原始诗下载次数统计
		public function poemDownNum() {
			$rs = $this->apiPoem->poemDownNum();
			if(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//根据原始诗分类获取诗列表 --什么都不传，默认获取最新诗列表
		public function getPoemListByNavId() {
			$rs = $this->apiPoem->getPoemListByNavId();
			if(!empty($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}

		//根据原诗id获取原诗信息
		public function accorPoemGetInfo() {
			$rs = $this->apiPoem->accorPoemGetInfo();
			if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//补充新词
		public function supplementLyric() {
			$rs = $this->apiPoem->supplementLyric();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(1 === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		
		//诗经板块-伴奏列表
		public function getShiJingList(){
			$rs = $this->apiPoem->getShiJingList();
			if(!empty($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(1);
			}
		}
	 public function getPoemUserInfo(){
		  $user_info = ApiCommonStatic::viaCookieLogin();
	
		  if(!$user_info){
            $this->setReturn(-101,'nolog');return;
        }
		if(!Input::has('poemid')){
			$this->setReturn(-1,'no_poemid');return;
		}
		$poemid=Input::get('poemid');
		$rs = $this->apiPoem->getPoemUserInfo($poemid);
		$this->setReturn(1,'success',$rs);return;
	 	

	 }
	}