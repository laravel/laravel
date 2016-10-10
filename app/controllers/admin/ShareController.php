<?php 
	/**
	* 作品，软件分享页面
	**/
	class ShareController extends BaseController {
		//作品id --分享作品
		public function shareOpus($id) {
			if(!empty($id)) {
				$rs = DB::table('opus')->leftJoin('user','user.id','=','opus.uid')->where('opus.id','=',$id)
								 ->first(array('user.nick','user.sportrait','user.gender','user.authtype','user.grade','user.isleague','user.teenager',
								 	'opus.id','opus.name','opus.url','opus.lnum','opus.repostnum','opus.praisenum','opus.addtime','opus.opustime'));
				if(!empty($rs)) {
					$url = Config::get('app.url');
					$rs['sportrait'] = !empty($rs['sportrait']) ? $url.ltrim($rs['sportrait'],'.'): null;
					$rs['url'] = !empty($rs['url']) ? $url.$rs['url'] : null;
					$rs['addtime2'] = date('Y-m-d',$rs['addtime']);
					$rs['addtime1'] = date('H:i',$rs['addtime']);
					$rs['addtime'] = $rs['addtime2'];
					if(!empty($rs['opustime'])){
						$str = $rs['opustime'];
						$min = round($str/60);
						$len1 = strlen($min);
						if($len1 == 0){
							$min = '00';
						} else if($len1<2){
							$min = '0'.$min;
						} 
						$sec = $str%60;
						$len2 = strlen($sec);
						if($len2==0){
							$sec = '00';
						} else if($len2<2){
							$sec = '0'.$sec;
						}
						$rs['realtime'] = $min.':'.$sec;
					} else{
						$rs['realtime'] = '00:00';
					}
					
				}

				$data = $this->getUrl();
				$rs['downurl'] = $data['downurl'];
				$rs['flag'] = $data['flag'];
				return View::make('share.shareOpus')->with('list',$rs);
			}
		}

		/**
		*	分享伴奏
		*	@author:wang.hongli
		*	@since:2015/05/31
		*	@param:伴奏id
		**/
		public function sharePoem($id)
		{
			$id = intval($id);
			if(!empty($id))
			{
				$id = intval($id);
				$rs = DB::table('poem')->where('id','=',$id)->where('isdel','=',0)
								 ->first(array('name','downnum','readername','writername','burl','yurl','lyricurl','duration','aliasname','addtime'));
				if(!empty($rs))
				{
					//查找人的id
					$readerRs = DB::table('readpoemrel')->where('poemid','=',$id)->first(array('readerid'));

					$readerid = !empty($readerRs['readerid']) ? $readerRs['readerid'] : 0;
					$sex = 1;//1男2女
					if(!empty($readerid))
					{
						$sexRs = DB::table('rprel')->where('readerid','=',$readerid)->first(array('poemercatid'));
						if(!empty($sexRs['poemercatid']) && in_array($sexRs['poemercatid'],array(1,2))){
							$sex = $sexRs['poemercatid'];//1男 2女
						}
					}
				}
				$url = Config::get('app.url');
				$data = $this->getUrl();
				$rs['downurl'] = $data['downurl'];
				$rs['flag'] = $data['flag'];
				$rs['sex'] = $sex;
				$rs['yurl'] = $url.$rs['yurl'];
				$rs['burl'] = $url.$rs['burl'];
				$rs['duration'] = str_replace('.', ':', $rs['duration']);
				return View::make('share.sharePoem')->with('list',$rs);
			}
		}

		//分享产品
		public function shareSoftWare() {
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
			if (stripos($userAgent,'iPhone') || strpos($userAgent,'iPad') || strpos($userAgent,'iPad') || strpos($userAgent,'iOS')) {
				// $url = 'itms-services://?action=download-manifest&url=https://geyouquan.com/ios/KGWB.plist';
				$url = 'http://itunes.apple.com/cn/app/id887320939?mt=8';
				$flag = 1;
			} elseif (strpos($userAgent,"Android")) {
				$url = 'http://openbox.mobilem.360.cn/d.php?p=com.ss.readpoem';
				$flag = 2;
			} else {
				// echo "<script>location.href='http://www.geyouquan.com'</script>";
				$url = 'http://itunes.apple.com/cn/app/id887320939?mt=8';
				$flag = 3;
			}
			return View::make('share.shareSoftWare')->with('downurl',$url)->with('flag',$flag);
		}

		//判断是ios，还是android
		protected function getUrl() {
			$userAgent = $_SERVER['HTTP_USER_AGENT']; 
			if(strpos($userAgent,"iPhone") || strpos($userAgent,"iPad") || strpos($userAgent,"iPod") || strpos($userAgent,"iOS")){
				$url = 'http://itunes.apple.com/cn/app/id887320939?mt=8';
				$flag = 1;
			}else if(strpos($userAgent,"Android")){
				$url='http://openbox.mobilem.360.cn/d.php?p=com.ss.readpoem'; //android下载地址，待添加
				$flag = 2;
			}else{ 
				$url='http://www.geyouquan.com';
				$flag = 3;
			}
			$data['downurl'] = $url;
			$data['flag'] = $flag;
			return $data;
		}


		/**
		*	获取比赛分赛区实施方案
		*	@author:wang.hongli
		*	@since:2015/07/19
		**/	
		public function getMatchClause($competitionid=0)
		{
			$apiCompetition = new ApiCompetition();
			if(empty($competitionid))
			{
				$this->setReturn(-1,'没有服务条款');
			}
			$rs = $apiCompetition->getMatchClause($competitionid);
			return View::make('competition.matchclause')->with('data',$rs);
		}
	}

