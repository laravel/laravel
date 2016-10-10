<?php
	/**
	* 照片模型
	**/
	class ApiAlbum extends ApiCommon {
		//上传照片
		public function uplodAlbum() {
			$info = $this->viaCookieLogin();
			if($info) {
				$uid = $info['id'];
				$num = $info['albums'];
				if($num >= 9) return 'overflow';
				$id = DB::table('albumindex')->insertGetId(
						array('id'=>null)
					);
				if($id) {
					$tableName = 'album'.$id%2;
					if(!$this->uploadPic($tableName,$uid,'album',100,1,$id)) {
						return false;
					} else {
						$update_flag = DB::table('user')->where('id',$uid)->increment('albums');
						if(!$update_flag ) {
							return false;
						}
					}
				}
				return true;
			} else {
				return 'nolog';
			}
		}

		//删除图片
		public function delAlbum() {
			$info = $this->viaCookieLogin();
			// $info['id'] = 25;
			if($info) {
				$uid = $info['id'];
				if(!Input::has('picId')) return '没有该图片';
				$picId = Input::get('picId'); //图片的id
				// $picId = 2;
				//删除权限判断
				$tableName = 'album'.$picId%2;
				if(!$this->deletePermission($picId,$tableName,$uid)) 
					return '没有权限';
				try {
					$delete_flag = DB::table('albumindex')->where('id',$picId)->delete();
					if(!$delete_flag) return '删除失败';
					DB::table($tableName)->where('id',$picId)->delete();
					DB::table('user')->where('id',$uid)->decrement('albums');
				} catch (Exception $e) {
					return '删除失败';
				}
					
			} else {
				return 'nolog';
			}
		}

		//获取相册图片列表--1,自己 2,他人
		public function albumList() {
			$info = $this->viaCookieLogin();
			$count = !empty(Input::has('count')) ? Input::get('count') : 20;
			$pageIndex = !empty(Input::has('pageIndex')) ? Input::get('pageIndex') : 1;
			$offSet = ($pageIndex-1)*$count;
			$count++;
			$sql = "select * from album0 where uid = ? union all select * from album1 where uid = ? order by addtime asc limit ?,?";	
			if(Input::has('otherId')) {
				$otherId = Input::get('otherId');
				$rs = DB::select($sql,array($otherId,$otherId,$offSet,$count));
			} elseif(!empty($info))  {
				$uid = $info['id'];
				$rs = DB::select($sql,array($uid,$uid,$offSet,$count));
			} else {
				return 'nolog';
			}
			foreach($rs as $key=>&$value) {
				$value['surl'] = $this->poem_url.$value['surl'];
				$value['url'] = $this->poem_url.$value['url'];
			}
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
			return $rs;
		}
	}