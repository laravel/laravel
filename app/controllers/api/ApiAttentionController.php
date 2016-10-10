<?php
	/**
	* 关注控制器
	**/
	class ApiAttentionController extends ApiCommonController {
		private $apiAttention = null;
		public  function  __construct() {
			$this->apiAttention = new ApiAttention();
		}
		//获取关注列表
		public function attentionList() {
			$info = $this->apiAttention->attentionList();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} else {
				$hasmore = $info['hasmore'];
				unset($info['hasmore']);
				if(empty($info)) {
					$this->setReturn(1,'success');
				} else {
					$this->setReturn(1,'success',$info,$hasmore);
				}
			}
		}
		/**
		 * 获取关注列表，修复删除，获取分页bug
		 * @author:wang.hongli
		 * @since:2016/04/12
		 */
		public function attentionListV2() {
			$info = $this->apiAttention->attentionListV2();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} else {
				$hasmore = $info['hasmore'];
				unset($info['hasmore']);
				if(empty($info)) {
					$this->setReturn(1,'success');
				} else {
					$this->setReturn(1,'success',$info,$hasmore);
				}
			}
		}
		//添加关注
		public function addAttention() {
			$info = $this->apiAttention->addAttention();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $info) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$info);
			}
		}
		//取消关注
		public function undoAttention() {
			$info = $this->apiAttention->undoAttention();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $info) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$info);
			}
		}
		//移除粉丝
		public function undoFans() {
			$info = $this->apiAttention->undoFans();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $info) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$info);
			}
		}
	}