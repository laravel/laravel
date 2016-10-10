<?php 

	/**
	* 	榜单管理
	**/
	class BillboardController extends BaseController {

		//获取最火作品榜
		public function getHotesOpusBoard() {
			$hostesOpusList = DB::table('opus')->where('opus.isdel','=',0)
												->select('isdel','id','name','praisenum','uid','lnum','repostnum','addtime')
		 										->orderBy('opus.lnum','desc')
		 										->paginate(20);
			$item=array();
			foreach ($hostesOpusList as  $k=>$value) {
					$hostesOpusList1 = DB::table('user')->select('id as uid','nick','gender')
														->where("id","=",$value['uid'])
														->first();
														
				$item[$k]['id']=$value['id'];
				$item[$k]['isdel']=$value['isdel'];
				$item[$k]['name']=$value['name'];
				$item[$k]['praisenum']=$value['praisenum'];
				$item[$k]['lnum']=$value['lnum'];
				$item[$k]['repostnum']=$value['repostnum'];
				$item[$k]['addtime']=$value['addtime'];
				$item[$k]['uid']=$hostesOpusList1['uid'];
				$item[$k]['nick']=$hostesOpusList1['nick'];
				$item[$k]['gender']=$hostesOpusList1['gender'];
			}
			 return View::make('billboard.hotesopusboard')->with('hostesopuslist',$hostesOpusList)->with('hostesopuslist1',$item);
		 }
		//获取推荐作品榜
		public function recommendOpusBoard() {
			$recommendOpusList = DB::table('opus')->leftjoin('user','opus.uid','=','user.id')->where('opus.recommendOpus','<',9999)->where('opus.isdel','=',0)
												->select('user.id as uid','user.nick','user.gender','opus.isdel',
													'opus.id','opus.name','opus.praisenum','opus.lnum','opus.repostnum','opus.addtime','opus.recommendopus'
													)
												->orderBy('opus.recommendopus','asc')
												->paginate(20);
			return View::make('billboard.recommendopusboard')->with('recommendopusboard',$recommendOpusList);						
		}

		//修改作品推荐排序
		public function modifyRecommendOpus() {
			$opusid = intval(Input::get('opusid'));
			$recommendopus = intval(Input::get('recommendopus'));
			if(empty($opusid) || empty($recommendopus)) {
				echo 'error';
				return;
			}
			try {
				$maxNum = DB::table('opus')->where('recommendopus','<',9998)->max('recommendopus');
			} catch (Exception $e) {
				echo 'error';
				return;
			}
			if(!empty($maxNum)) {
				if($recommendopus > $maxNum) {
					echo 'error';
					return;
				}
			}
			//获取作品原来的排序
			$recommendopusoldarr = DB::table('opus')->where('id','=',$opusid)->first(array('recommendopus'));
			$recommendopusold = DB::table('opus')->where('id','=',$opusid)->pluck('recommendopus');
			if(empty($recommendopusold)) {
				echo 'error';
				return;
			}
			//新排序 = 原来的排序
			if($recommendopusold == $recommendopus) {
				echo "same";
				return;
			}
			//新排序 < 原来的排序
			if($recommendopus < $recommendopusold) {
				$oldArr = DB::table('opus')->where('recommendopus','>=',$recommendopus)->where('recommendopus','<',$recommendopusold)->select('id','recommendopus')->get();
				if(!empty($oldArr)) {
					foreach($oldArr as $key=>$value) {
						DB::table('opus')->where('id',$value['id'])->increment('recommendopus');
					}
					DB::table('opus')->where('id',$opusid)->update(array('recommendopus'=>$recommendopus));
					echo "success";
					return;
				} else {
					echo 'error';
					return;
				}
			//新排序大于原来的排序
			} else {
				$oldArr = DB::table('opus')->where('recommendopus','>',$recommendopusold)->where('recommendopus','<=',$recommendopus)->select('id','recommendopus')->get();
				if(!empty($oldArr)) {
					foreach($oldArr as $key=>$value) {
						DB::table('opus')->where('id',$value['id'])->decrement('recommendopus');
					}
					DB::table('opus')->where('id',$opusid)->update(array('recommendopus'=>$recommendopus));
					echo 'success';
					return;
				} else {
					echo "error";
					return;
				}
			}
		}

		//删除作品排序
		public function delRecommendOpus() {
			$opusid = intval(Input::get('opusid'));
			$recommendopus = intval(Input::get('recommendopus'));
			if(empty($opusid) || empty($recommendopus)) {
				echo 'error';
				return;
			}
			try {
				DB::table('opus')->where('id',$opusid)->update(array('recommendopus'=>9999));
				DB::table('opus')->where('recommendopus',$recommendopus)->where('recommendopus','<',9998)->decrement('recommendopus');
			} catch (Exception $e) {
				echo 'error';
			}
		}

		//根据用户昵称 和 作品名称搜索作品
		public function searchOpus() {
			$opusname = Input::get('opusname');
			$nick = Input::get('nick');
			if(empty($opusname) || empty($nick)) {
				echo 'error';
				return;
			}
			$sql = "select opus.id as opusid,opus.name,user.id,user.nick from user left join opus on user.id = opus.uid where user.nick like '%{$nick}%' and opus.name like '%{$opusname}%' and opus.isdel != 1 and user.isdel != 1";
			$rs = DB::select($sql);
			if(empty($rs)) {
				echo "error";
				return;
			} else {
				$tmpStr = null;
				foreach($rs as $key=>$value) {
					$tmpStr .= "<option value='{$value['opusid']}'>
									作品id--{$value['opusid']}--作品名称--{$value['name']}--作者id--{$value['id']}--作者昵称--{$value['nick']}
								</option>";
				}
				echo $tmpStr;
				return;
			}
		}

		//添加推荐作品
		public function addRecommendOpus() {
			$opussort = Input::get('opussort');
			$opusid = Input::get('lastdata');
			if(empty($opusid)) {
				return Redirect::to('/admin/recommendOpusBoard');
			} else {
				//选出最大排序
				$maxNum = DB::table('opus')->where('recommendopus','<',9998)->max('recommendopus');
				if(!empty($maxNum)) {
					$maxNum = $maxNum + 1;
				} else {
					$maxNum = 1;
				}
				if($opussort > $maxNum) {
					return Redirect::to('/admin/recommendOpusBoard');
				} else {
					if(empty($opussort)) {
						return Redirect::to('/admin/recommendOpusBoard');
					}
					try {
						//将大排序全部+1
						DB::table('opus')->where('recommendopus','>=',$opussort)->where('recommendopus','<',9998)->increment('recommendopus');
						DB::table('opus')->where('id',$opusid)->update(array('recommendopus'=>$opussort));
					} catch (Exception $e) {
					}
					return Redirect::to('/admin/recommendOpusBoard');
				}
			}
		}
		//获取最火博主
		public function getHotesUserBoard() {
			$hostesUserList = DB::table('user')
										->select('user.id','user.nick','user.email','user.phone','user.gender',
											'user.praisenum','user.lnum','user.repostnum',
											'user.grade','user.addtime','user.thpartType','user.isdel')->where('user.isdel','=',0)
										->orderBy('user.praisenum','desc')
										->orderBy('user.lnum','desc')
										->orderBy('user.repostnum','desc')
										->paginate(20);
			return View::make('billboard.hotesuserboard')->with('hotesuserboard',$hostesUserList);
		}

		//获取推荐博主
		public function recommendUserBoard() {
			$recommendUserList = DB::table('user')
										->select('user.id','user.nick','user.gender','user.email','user.phone',
											'user.thpartType','user.isdel','user.recommenduser')->where('user.isdel','=',0)->where('user.recommenduser','<',9999)
										->orderBy('user.recommenduser','asc')
										->paginate(20);
			return View::make('billboard.recommenduserboard')->with('recommenduserboard',$recommendUserList);
		}

		//删除推荐博主
		public function delRecommendUser() {
			$uid = Input::get('uid');
			$recommenduser = Input::get('recommenduser'); //排序
			if(empty($uid) || empty($recommenduser)) {
				echo 'error';
				return;
			} else {
				try {
					DB::table('user')->where('recommenduser','>',$recommenduser)->where('recommenduser','<',9998)->decrement('recommenduser');
					DB::table('user')->where('id',$uid)->update(array('recommenduser'=>9999));
					//从关系表中删除
					DB::table('navanchorrel')->where('uid',$uid)->where('navid',16)->delete();
				} catch (Exception $e) {
					echo 'error';
				}
			}
		}

		//修改推荐博主顺序
		public function modifyRecommendUserSort() {
			$uid = intval(Input::get('uid'));
			$recommenduser = intval(Input::get('recommenduser'));

			if(empty($uid) || empty($recommenduser)) {
				echo 'error';
				return;
			} else {
				//<=0 退出
				if($recommenduser<=0) {
					echo "error";
					return;
				}
				//判断顺序是否连续
				$sql = "select recommenduser as maxNum from user where recommenduser<9998 order by recommenduser desc limit 1";
				$tmpMax = DB::select($sql);
				if(empty($tmpMax)) {
					$maxNum = 1;
				} else {
					$maxNum = $tmpMax[0]['maxNum'];
				}
				if($recommenduser > $maxNum) {
					echo "error";
					return;
				}
				//获取用户原来的顺序
				$tmpSort = DB::table('user')->where('id','=',$uid)->first(array('recommenduser'));
				if(empty($tmpSort)) {
					echo 'error';
					return;
				} else {
					$oldSort = $tmpSort['recommenduser'];
					//原来的顺序==排列后的顺序
					if($oldSort == $recommenduser) {
						echo 'success';
						return;
					}
					////新排序 < 原来的排序
					if($recommenduser < $oldSort) {
						$tmpArr = DB::table('user')->where('recommenduser','>=',$recommenduser)->where('recommenduser','<',$oldSort)->select('id','recommenduser')->get();
						if(empty($tmpArr)) {
							echo 'error';
							return;
						} else {
							foreach($tmpArr as $key=>$value) {
								DB::table('user')->where('id',$value['id'])->increment('recommenduser');
							}
							DB::table('user')->where('id',$uid)->update(array('recommenduser'=>$recommenduser));
							echo "success";
							return;
						}
					} else {
						$tmpArr = DB::table('user')->where('recommenduser','<=',$recommenduser)->where('recommenduser','>',$oldSort)->select('id','recommenduser')->get();
						if(empty($tmpArr)) {
							echo 'error';
							return;
						} else {
							foreach($tmpArr as $key=>$value) {
								DB::table('user')->where('id',$value['id'])->decrement('recommenduser');
							}
							DB::table('user')->where('id',$uid)->update(array('recommenduser'=>$recommenduser));
							echo "success";
							return;
						}
					}
				}
			}
		}

		//查询用户id,recommenduser
		public function searchUser() {
			$nick = Input::get('nick');
			$uid = Input::get('uid');
			//过滤搜索条件
			if(empty($nick) && empty($uid)) {
				echo 'error';
				return;
			} else {
				$conn = DB::table('user')->select('id','nick');
				if(!empty($uid)){
					$conn->where('id',$uid);
				}
				if(!empty($nick)){
					// $nick = '%'.$nick.'%';
					// $conn->where('nick','like',$nick);
					$conn->where('nick','=',$nick);
				}
				$lastRs = $conn->get();
				if(empty($lastRs)) {
					echo 'error';
					return;
				} else {
					$tmpStr = '';
					foreach($lastRs as $key=>$value) {
						$tmpStr .="<option value='{$value['id']}'>
									用户id--{$value['id']}--用户昵称--{$value['nick']}
								</option>";
					}
					echo $tmpStr;
					return;
				}
			}
		}

		//添加推荐用户
		public function addRecommendUser() {
			//推荐用户排序
			$usersort = Input::get('usersort');
			//获取用户id
			$userid = Input::get('lastdata');
			if(empty($usersort) || empty($userid)) {
				return Redirect::to('/admin/recommendUserBoard');
			} else {
				//检测用户最大排序
				$sql = "select max(recommenduser) as maxNum  from user where recommenduser<9998";
				$tmpRs = DB::select($sql);
				$maxNum = 1;
				if(!empty($tmpRs)) {
					$maxNum = $tmpRs[0]['maxNum']+1;
				}
				if($usersort > $maxNum) {
					return Redirect::to('/admin/recommendUserBoard');
				} else {
					if(empty($usersort)) {
						return Redirect::to('/admin/recommendUserBoard');
					} else {
						//将大排序全部+1
						DB::table('user')->where('recommenduser','>=',$usersort)->where('recommenduser','<',9998)->increment('recommenduser');
						DB::table('user')->where('id',$userid)->update(array('recommenduser'=>$usersort));
						//将数据放到navanchorrel表中
						$time = time();
						DB::table('navanchorrel')->insert(array('navid'=>16,'uid'=>$userid,'addtime'=>$time));
						return Redirect::to('/admin/recommendUserBoard');
					}
				}
			}
		}

	}