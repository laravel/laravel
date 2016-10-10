<?php 
	/**
	* 导航控制器
	**/
	class NavigationController extends BaseController {

		//获取导航列表 0 获取作品分类 否则都是首页显示
		public function navigationList() {
			$sql = "select * from navigation order by sort";
			$rs = DB::select($sql);
			if(!empty($rs)) {
				$url = Config::get('app.url');
				foreach($rs as $key=>&$value) {
					if(empty($value['pic'])) {
						$value['pic'] = '';
					} else {
						$value['pic'] = $url.$value['pic'];
					}
				}
				return View::make('navigation.navigationList')->with('navigationList',$rs);
			}
		}

		//修改导航列表图片,导航名称
		public function modifyNavigation() {
			$type = Input::get('type');
			if(1==$type) {
				$categoryid = Input::get('categoryid');
				$cateName = Input::get('cateName');
				$sql = "update navigation set category = '{$cateName}' where id = $categoryid";
				if(DB::update($sql)) {
					echo $cateName;
				} else {
					echo 'error';
				}
			} else {
				$id = Input::get('id');
				$filePath = './upload/navigation/';
				$arr = Input::file('formName');
				$lastFilePath = null;
				if(empty($arr)) {
					return Redirect::to('/admin/navigationList');
				} else {
					$ext = $arr->guessExtension();
					$imgName = time().uniqid();
					$imgName = $imgName.'.'.$ext;
					$lastFilePath = $filePath.$imgName;
					$arr->move($filePath,$imgName);
					$lastFilePath = ltrim($lastFilePath,'.');

					$sql = "update navigation set pic = '{$lastFilePath}' where id = $id";
					DB::update($sql);
					return Redirect::to('/admin/navigationList');
				}
			}
			
		}

		//检测某个父分类下是否存在某个子分类
		public function checkSubHeadExists() {
			$subhead = Input::get('subhead');
			$parentid = Input::get('parentid');
			if(empty($subhead) || empty($parentid)) {
				echo 'error';
				return;
			}
			$sql = "select id from navigation where category like '%{$subhead}%'";
			$tmpRs = DB::select($sql);
			if(!empty($tmpRs)) {
				echo 'error';
				return;
			}
		}
		//添加子分类
		public function addSubNavigation() {
			$subhead = Input::get('subhead');
			$parentid = Input::get('parentid');
			$subsort = Input::get('subsort');
			$suborparcat = Input::get('suborparcat'); //0 添加父类 1 添加的是子类
			if(empty($subhead) || empty($parentid) || empty($subsort)) {
				echo 'error';
				return;
			}
			//添加子类
			if(1 == $suborparcat) {
				$sql1 = "update navigation set sort=sort+1 where pid = $parentid and sort>={$subsort}";
				$sql2 = "insert into navigation (category,sort,pid) values ('{$subhead}',$subsort,$parentid)";
			} else {
			//添加父类
				 $sql1 = "update navigation set sort=sort+1 where sort>={$subsort}";
				 $sql2 = "insert into navigation (category,sort) values ('{$subhead}',$subsort)";
			}
			try {
				DB::update($sql1);
			} catch (Exception $e) {
			}
			if(!DB::insert($sql2)) {
				echo 'error';
				return;
			} else {
				echo "success";
				return;
			}
		}

		//修改分类顺序
		public function modifyNavSort() {
			$oldSort = Input::get('oldSort');
			$navid = Input::get('navid');
			if(empty($oldSort) || empty($navid)) {
				echo "error";
				return;
			}
			$sql = "update navigation set sort={$oldSort} where id = {$navid}";
			try {
				if(DB::update($sql)) {
					echo "success";
					return;
				} else {
					echo "error";
					return;
				}
			} catch (Exception $e) {
				echo "success";
				return;
			}
		}

		//删除 or 恢复导航
		public function navDelOrReplay() {
			$navid = Input::get('navid');
			$status = Input::get('status');
			if(empty($navid)) {
				echo "error";
				return;
			}
			if($status) {
				$isdel = 0;
			} else {
				$isdel = 1;
			}
			$sql = "update navigation set isdel = {$isdel} where id = $navid";
			try {
				if(DB::update($sql)) {
					echo "success";
					return;
				} else {
					echo "error";
					return;
				}
			} catch (Exception $e) {
				echo "success";
				return;
			}
		}

		//获取闪图列表
		public function getShowRootList() {
			$sql = "select * from showbootpic order by id desc";
			$rs = DB::select($sql);
			$url = Config::get('app.url');
			if(!empty($rs)) {
				foreach($rs as $key=>&$value) {
					$value['url'] = $url.$value['url'];
				}
			}
			return View::make('navigation.getShowRootList')->with('showrootlist',$rs);
		}
		//添加闪图
		public function addShowRoot() {
			$showbootpic = Input::file('showbootpic');
			//判断文件类型
			if(empty($showbootpic)) {
				return View::make('navigation.getShowRootList')->with('showrootlist',$rs);
			} else {
				$ext = $showbootpic->guessExtension();
				$allow_img = array('png','jpeg','gif','jpg');
				if(!in_array($ext, $allow_img))
				{
					return View::make('navigation.getShowRootList')->with('showrootlist',$rs);
				}
				$filePath = './upload/showrootlist/';
				$showPicName = time().uniqid().'.'.'jpeg';

				$lastFilePath = $filePath.$showPicName;
				$showbootpic->move($filePath,$lastFilePath);
				$lastFilePath = ltrim($lastFilePath,'.');
				$flag = !empty($_POST['flag']) ? $_POST['flag'] : 0;
				$com_flag = !empty($_POST['com_flag']) ? $_POST['com_flag'] : 0;
				$type = !empty($_POST['type']) ? $_POST['type'] : 0;
				$html_link = !empty($_POST['html_link']) ? $_POST['html_link'] : "";
				$sql = "insert into showbootpic (url,flag,com_flag,type,html_link) values ('{$lastFilePath}',$flag,$com_flag,$type,'{$html_link}')";
				DB::insert($sql);
				$sql = "select * from showbootpic order by id desc";
				$rs = DB::select($sql);
				$url = Config::get('app.url');
				if(!empty($rs)) {
					foreach($rs as $key=>&$value) {
						$value['url'] = $url.$value['url'];
					}
				}
				return View::make('navigation.getShowRootList')->with('showrootlist',$rs);
			}
		}
		//删除闪图
		public function delShowList() {
			$myimageid = Input::get("myimageid");
			if(empty($myimageid)) {
				echo "error";
				return;
			} else {
				$sql = "delete from showbootpic where id = $myimageid";
				if(!DB::delete($sql)) {
					echo "error";
				} else {
					echo "success";
				}
			}
		}
	}
 ?>