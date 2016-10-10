<?php 
	/**
	*接口api用户相关的类
	*/
	class ApiNavigationController extends ApiCommonController {

		//获取首页导航 --两个地方用到首页导航type不传，录音type=0
		public function navigationList() {
			if(Input::has('type')) {
				$rs = DB::table('navigation')->select('id','category','pic','type','sort','pid')->where('type',0)->where('isdel',0)->orderBy('sort','asc')->get();
			} else {
				$rs = DB::table('navigation')->select('id','category','pic','type','sort','pid')->where('isdel',0)->orderBy('sort','asc')->get();
			}
			$tmpCat = array();
			if(empty($rs)) {
				$this->setReturn(1,'success');
				return;
			}
			foreach($rs as $key=>$value) {
				$tmpCat[$value['id']] = $value;
				if(!empty($tmpCat[$value['id']]['pic'])) {
					$tmpCat[$value['id']]['pic'] = $this->poem_url.$value['pic'];
				}
			}
			$tmpCat2 = array();
			if(!empty($tmpCat)) {
				foreach($tmpCat as $key=>$value) {
					if(!empty($value['pid'])) {
						@$tmpCat[$value['pid']]['subCat'][] = array('id'=>$value['id'],'category'=>$value['category'],'sort'=>$value['sort']);
						unset($tmpCat[$key]);
					}
				}
				foreach($tmpCat as $key=>$value) {
					$tmpCat2[] = $value;
				}
			}
			$this->setReturn(1,'success',$tmpCat2);
		}

		//版本更新提示
		public function version() {
			$platForm = empty(Input::get('platForm')) ? 0 : 1;
			$rs = DB::table('version')->where('platform',$platForm)->orderBy('uptime','desc')->first();
			if(empty($rs)) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(1,'success',$rs);
			}
		}

		//根据分类查找人的列表---主播类型16:推荐17:男18:女19:明星20:草根主播
		public function getAnchor() {
			$apiNavigation = new ApiNavigation();
			$rs = $apiNavigation->getAnchor();
			if(is_array($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				$this->setReturn(1,'success',$rs,$hasmore);
			} else {
				$this->setReturn(1);
			}
		}

		//根据分类获取作品列表
		public function accordNavGetOpusList() {
			
			if(!Input::has('navId')) {
				$this->setReturn(0,'获取作品列表失败');
				return;
			}
			$navId = intval(Input::get('navId'));
			// $count = !empty(Input::has('count')) ? intval(Input::get('count')) : 20;
			$count = 20;
			$pageIndex = !empty(Input::has('pageIndex')) ? intval(Input::get('pageIndex')) : 1;
			$offSet = $count*($pageIndex - 1);
			$count++;
			$apiNavigation = new ApiNavigation();
			$rs = $apiNavigation->accordNavGetOpusList($navId,$count,$offSet);
			if(is_array($rs)) {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				if(!empty($rs)) {
					$this->setReturn(1,'success',$rs,$hasmore);
				} else {
					$this->setReturn(1);
				}
			} else {
				$this->setReturn(0,$rs);
			}
		}
		//获取广告列表
		public function getAdversing() {
			$platform = Input::has('platform') ? intval(Input::get('platform')) : 0;
			$rs = DB::table('advertising')->where('platform',$platform)->where('status',0)->where('isnew',0)->orderBy('orderby','asc')->orderBy('addtime','desc')->get();
			if(empty($rs)){
				$this->setReturn(1);
				return;
			}else{
				foreach($rs as $key=>&$value) {
					$value['url'] = !empty($value['url']) ? trim($value['url'],'.') : null;
					$value['pic'] = !empty($value['pic']) ? $this->poem_url.trim($value['pic'],'.') : null;
				}
				unset($value);
				$this->setReturn(1,'sucess',$rs);
				return;
			}
		}

		/**
		*	新获取广告列表
		*	@author:wang.hongli
		*	@since:2014/12/07
		*/
		public function getAdInfo()
		{
			$platform = Input::has('platform') ? intval(Input::get('platform')) : 0;
			$rs = DB::table('advertising')->where('platform',$platform)->where('status',0)->where('isnew',1)->orderBy('orderby','asc')->orderBy('addtime','desc')->get();
			if(empty($rs)){
				$this->setReturn(1);
			}else{
				foreach($rs as $key=>&$value)
				{
					$value['url'] = !empty($value['url']) ? trim($value['url'],'.') : null;
					$value['pic'] = !empty($value['pic']) ? $this->poem_url.trim($value['pic'],'.') : null;
				}
				$this->setReturn(1,'success',$rs);
			}
		}

		//获取精品推荐列表
		public function recommendation() {
			$apiNavigation = new ApiNavigation();
			$rs = $apiNavigation->recommendation();
			if('nodata' === $rs) {
				$this->setReturn(1);
			} else {
				$hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
				if(empty($rs)) {
					$this->setReturn(1);
				} else {
					$this->setReturn(1,'success',$rs,$hasmore);
				}
			}
		}

		//启动时候，显示一张图片
	    public function showBootPic() {
	    	$version = Input::get('version');//新版本 
	    	$conn = DB::table('showbootpic');
	    	if(empty($version))
	    	{
	    		$conn->whereNotIn('flag',array(0,1,2));
	    	}
	    	$rs = $conn->orderBy('id','desc')->skip(0)->take(5)->get();
	    	if(empty($rs)){
	    		$this->setReturn(1);
	    	}else{
	    		foreach($rs as $k=>&$v){
	    			$v['url'] = $this->url.$v['url'];
	    		}
	    		$this->setReturn(1,'success',$rs);
	    	}
	    }
		//商城广告
		//参数     platform 平台类型  0 ios 1安卓 
		//hgz   商城广告分类 20 
		//2016-07-17
		public function listAbShop(){
			
			 	$platform = Input::has('platform') ? intval(Input::get('platform')) : 0;
			$rs = DB::table('advertising')->where('platform',$platform)->where("type",'=','20')->where('status',0)->where('isnew',1)->orderBy('orderby','asc')->orderBy('addtime','desc')->get();
			if(empty($rs)){
				$this->setReturn(0);
			}else{
				foreach($rs as $key=>&$value)
				{
					$value['url'] = !empty($value['url']) ? trim($value['url'],'.') : null;
					$value['pic'] = !empty($value['pic']) ? $this->poem_url.trim($value['pic'],'.') : null;
				}
				$this->setReturn(1,'success',$rs);
			}
		}
	}