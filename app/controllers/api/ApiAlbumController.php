<?php

	/**
	* 照片控制器
	**/
	class ApiAlbumController extends  ApiCommonController {
		private $apiAlbum = null;
		public function __construct() {
			$this->apiAlbum = new ApiAlbum();
		}
		//上传图片
		public function uploadAlbum() {
			$info = $this->apiAlbum->uplodAlbum();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $info) {
				$this->setReturn(1,'success');
			} elseif(false === $info) {
				$this->setReturn(0,'上传失败,请重试');
			} elseif('overflow' === $info) {
				$this->setReturn(0,'图片已满');
			}
		}

		//删除图片
		public function delAlbum() {
			$info = $this->apiAlbum->delAlbum();
			if('nolog' === $info) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $info) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$info);
			}
		}

		//相片列表
		public function albumList() {
			$info = $this->apiAlbum->albumList();
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
		
	}