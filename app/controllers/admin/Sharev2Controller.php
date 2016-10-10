<?php 
	/**
	* 作品，软件分享页面
	**/
	class Sharev2Controller extends BaseController {
		//作品id --分享作品
		public function shareOpusv2($id) {
			if(!empty($id)) {
				$id = intval($id);
				$rs = DB::table('opus')->leftJoin('user','user.id','=','opus.uid')->where('opus.id','=',$id)->where('opus.isdel','=',0)
								 ->first(array('user.nick','user.sportrait','user.gender','user.authtype','user.grade','user.isleague','user.teenager',
								 	'opus.id','opus.name','opus.url','opus.lnum','opus.repostnum','opus.praisenum','opus.addtime','opus.opustime','opus.lyricurl'));
				if(empty($rs))
				{
					header('Location:/admin/share404');
					die;
				}
				if(!empty($rs)) 
				{
					$url = Config::get('app.poem_url');
					$rs['sportrait'] = !empty($rs['sportrait']) ? $url.ltrim($rs['sportrait'],'.'): null;
					$rs['url'] = !empty($rs['url']) ? $url.$rs['url'] : null;
					$lyric_url = $url.ltrim($rs['lyricurl'],'.');
					$rs['content'] = @file_get_contents($lyric_url);
					$rs['name'] = str_replace('·', ' · ', $rs['name']);
					//增加收听数量
					$apiOpus = new ApiOpus();
					$apiOpus->incLisNum($id);
				}
				$data = $this->getUrl();
				$rs['downurl'] = $data['downurl'];
				return View::make('sharev2.shareOpusv2')->with('list',$rs);
			}
		}

		/**
		*	分享伴奏
		*	@author:wang.hongli
		*	@since:2015/05/31
		*	@param:伴奏id
		**/
		public function sharePoemv2($id)
		{
			$id = intval($id);
			if(!empty($id))
			{
				$id = intval($id);
				$rs = DB::table('poem')->where('id','=',$id)->where('isdel','=',0)
								 ->first(array('name','downnum','readername','writername','burl','yurl','lyricurl','duration','aliasname','addtime'));
				if(empty($rs))
				{
					header('Location:/admin/share404');
					die;
				}
				if(!empty($rs))
				{
					//伴奏下载数增加
					try {
						DB::table('poem')->where('id',$id)->increment('downnum');
					} catch (Exception $e) {
					}
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
				$url = Config::get('app.poem_url');
				$data = $this->getUrl();
				$rs['downurl'] = $data['downurl'];
				$rs['content'] = @file_get_contents('.'.$rs['lyricurl']);
				$rs['sex'] = $sex;
				$rs['yurl'] = $url.$rs['yurl'];
				$rs['burl'] = $url.$rs['burl'];
				$rs['duration'] = str_replace('.', ':', $rs['duration']);
				return View::make('sharev2.sharePoemv2')->with('list',$rs);
			}
		}
		
		//分享产品
		public function shareSoftWarev2() {
			$data = $this->getUrl();
			$url = $data['downurl'];
			return View::make('sharev2.shareSoftv2')->with('downurl',$url);
		}
		
		/**
		 * 分享诗文比赛--诗文比赛诗词作品，不是音频作品
		 * @author:wang.hongli
		 * @since:2016/05/11
		 */
		public function sharePoetry($id=0){
			$id = intval($id);
			if(empty($id)){
				header('Location:/admin/share404');
				die;
			}
			$data = $this->getUrl();
			$rs = DB::table('opus_poetry')->where('id',$id)->first();
			if(empty($rs)){
				header('Location:/admin/share404');
				die;
			}
			$url = Config::get('app.poem_url');
			$rs['content'] = @file_get_contents($rs['lyric']);
			if(!empty($rs['content'])){
				$rs['content'] = str_replace("\n", "<br/>", $rs['content']);
			}
			//获取用户信息
			$user_info = DB::table('user')->where('id',$rs['uid'])->first(array('sportrait'));
			$rs['sportrait'] = $url.ltrim($user_info['sportrait'],'.');
			
			$rs['downurl'] = $data['downurl'];
			return View::make('sharev2.sharePoetry')->with('list',$rs);
		}
		// 404
		public function share404() {
			$data = $this->getUrl();
			$url = $data['downurl'];
			return View::make('sharev2.share404')->with('downurl',$url);
		}

		//判断是ios，还是android
		protected function getUrl() {
			$url = "http://a.app.qq.com/o/simple.jsp?pkgname=com.ss.readpoem&g_f=991653";
			$data['downurl'] = $url;
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

