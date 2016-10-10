<?php 
	/**
	* 精品模块
	**/
	class RecommendationController extends BaseController {

		//添加精品推荐
		public function addRecommendation() {
			return View::make('recommendation.addRecommendation');
		}

		//doAddRecommendation
		public function doAddRecommendation() {
			$title = trim(Input::get('title'));
			$subhead = trim(Input::get('subhead'));
			$url = trim(Input::get('url'));
			$sort = trim(Input::get('sort'));
			$platform = Input::get('platform');
			$arr = Input::file('formName');

			if(empty($title) || empty($url) || empty($sort) || empty($arr)) {
				return Redirect::to('/admin/addRecommendation');
			}
			$filePath = './upload/recommendation/';
			$lastFilePath = null;
			$ext = $arr->guessExtension();
			$imgName = time().uniqid();
			$imgName = $imgName.'.'.$ext;
			$lastFilePath = $filePath.$imgName;
			$arr->move($filePath,$imgName);
			$lastFilePath = ltrim($lastFilePath,'.');
			$time = time();
			//检测排序是否超过最大排序
			$sql = "select max(sort) as maxSort from recommendation where platform={$platform} and isdel != 1 and sort<998";
			$tmpRs = DB::select($sql);
			if(empty($tmpRs)) {
				$lastMaxNum = 1;
			} else {
				$lastMaxNum = $tmpRs[0]['maxSort']+1;
			}
			if($sort > $lastMaxNum) {
				return View::make('recommendation.addRecommendation');
			}
			$sql = "update recommendation set sort=sort+1 where platform={$platform} and sort>={$sort} and sort  < 998";
			DB::update($sql);

			$sql = "insert into recommendation(url,title,subhead,sicon,sort,platform,addtime) 
					values ('{$url}','{$title}','{$subhead}','{$lastFilePath}',{$sort},{$platform},$time)";
			DB::insert($sql);
			return View::make('recommendation.addRecommendation');
		}

		//获取精品列表
		public function getRecommendation() {
			$sql = "select * from recommendation where isdel != 1 order by sort";
			$rs = DB::select($sql);
			if(empty($rs)) {
				return Redirect::to('/admin/addRecommendation');
			} else {
				$url = Config::get('app.url');
				foreach($rs as $key=>&$value) {
					$value['sicon'] = $url.$value['sicon'];
				}
				return View::make('recommendation.recommendation')->with('recommend',$rs);
			}
		}

		//删除精品推荐
		public function delOrDelRecommenda() {
			$id = Input::get('id');
			if(empty($id)) {
				echo "error";
				return;
			} else {
				$tmpRs = DB::table('recommendation')->where('id','=',$id)->first(array('sicon','sort','platform'));
				if(empty($tmpRs)) {
					echo 'error';
					return;
				} else {
					$url = Config::get('app.url');
					$sicon = '.'.$tmpRs['sicon'];
					$sort = $tmpRs['sort'];
					$platform = $tmpRs['platform'];
					//删除文件
					try {
						unlink($sicon);
					} catch (Exception $e) {
						
					}
					//将此排序下面的排序-1
					$sql = "update recommendation set sort=sort-1 where platform = {$platform} and sort>={$sort} and sort<998";
					if(DB::update($sql)) {
						//删除记录
						$sql = "delete from recommendation where id = $id";
						DB::delete($sql);
					} else {
						echo 'error';
						return;
					}
				}
			}
		}

	}
