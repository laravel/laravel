<?php 
/**
* 广告控制器
**/
class AdvertisingController extends BaseController {
	//获取广告列表
	public function advertisingList() {
		$search = array();
		$page = 20;
		$search['type'] = isset($_GET['type']) ? $_GET['type'] : -1;
		$search['platform'] = isset($_GET['platform']) ? $_GET['platform'] : -1;
		$search['isnew'] = isset($_GET['isnew']) ? $_GET['isnew'] : -1;
		$url = Config::get('app.url');
		$conn = DB::table('advertising')->select(DB::raw("CONCAT('$url',pic) as pic"),'id','name','des','url','argument','type','status','platform','addtime','isnew','orderby');
		
		if($search['type']>-1){
			$conn->where('type',$search['type']);
		}
		if($search['platform']>-1){
			$conn->where('platform',$search['platform']);
		}
		if($search['isnew']>-1){
			$conn->where('isnew',$search['isnew']);
		}
		$rs = $conn->orderBy('id','desc')->paginate($page);
		//所有类型
		$all_type = array(
				0=>'站外',
				1=>'站内人',
				2=>'站内比赛',
				3=>'夏青杯',
				4=>'诵读联合会',
				5=>'诗经奖',
				6=>'活动',
				8=>'静态图片',
		);
		return View::make('advertising.advertisingList')->with('advertisingList',$rs)->with('search',$search)->with('all_type',$all_type);
	}
	/**
	*	更新广告内容
	*/
	public function advUpdate($id=0)
	{
		if(empty($id))
		{
			// return '请上传jpg,png文件类型';
			return Redirect::to('/admin/defaultError')->with('message','更新广告内容失败');
		}
		$model = DB::table('advertising')->where('id','=',$id)->first();
		return View::make('advertising.addAdvertising')->with('data',$model);

	}
	//修改排序
	public function advOrderby(){
		$id = (int)Input::get('id');
		$orderby = (int)Input::get('orderby');
		try {
			DB::table('advertising')->where('id',$id)->update(array('orderby'=>$orderby));
			echo "操作成功";
		} catch (Exception $e) {
			echo '操作失败';
		}
		
	}

	//开启/关闭广告
	public function delOrDelAdv() {
		$adid = intval(Input::get('adid'));
		$sign = intval(Input::get('sign'));
		if(empty($adid)) {
			echo 'error';
			exit;
		}
		//开启广告->sign修改成0
		$status = 1;
		if($sign) {
			$status = 0;
		}
		try {
			DB::table('advertising')->where('id',$adid)->update(array('status'=>$status));
			echo 'success';
		} catch (Exception $e) {
			echo 'error';
		}
	}

	//删除广告
	public function delAdv() {
		$adid = intval(Input::get('adid'));
		try {
			DB::table('advertising')->where('id',$adid)->delete();
		} catch (Exception $e) {
			echo 'error';
		}
	}

	//添加广告
	public function addAdvertising() {
		return View::make('advertising.addAdvertising');
	}

	//添加广告动作
	public function doAddAdvertising() {
		$filePath = './upload/adversing/';
		$arr = Input::file('formName');
		$lastFilePath = null;
		$id = !empty(Input::get('id')) ? intval(Input::get('id')) : 0;
		if(!empty($arr))
		{
			//判断作品类型，只能是png,jpg格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png类型文件');
			}
			$ext = $arr->guessExtension();
			$imgName = time().uniqid();
			$imgName = $imgName.'.'.$ext;
			$lastFilePath = $filePath.$imgName;
			$arr->move($filePath,$imgName);
			$lastFilePath = ltrim($lastFilePath,'.');
		}
		elseif(!empty($id))
		{
			$lastFilePath = DB::table('advertising')->where('id','=',$id)->pluck('pic');
		}	
		$name = Input::get('name');
		$des = Input::get('des');
		$adurl = Input::get('adurl');
		if(empty($adurl)) $adurl = null;
		$argument = Input::get('argument');
		if(empty($argument)) $argument = 0;
		$type = Input::get('type');
		$status = 0;
		$platform = Input::get('platform');
		$isnew = !empty(Input::get('isnew')) ? 1 : 0;
		$arr = array(
			'name'=>$name,
			'des'=>$des,
			'pic'=>$lastFilePath,
			'url'=>$adurl,
			'argument'=>$argument,
			'type'=>$type,
			'status'=>$status,
			'platform'=>$platform,
			'isnew'=>$isnew,
			'addtime'=>time()
		);
		//更新操作
		if(empty($id))
		{
			DB::table('advertising')->insert($arr);
		}
		else
		{
			DB::table('advertising')->where('id','=',$id)->update($arr);
		}
		return Redirect::to('/admin/advertisingList');
	}
	/**
	 * [addHeadPhoto 添加推广图片]
	 */
	public function addHeadPhotoView()
	{
		return View::make('advertising.addheadphoto');
	}
	//推广头部图片
	/**
	 * [workHeadPhoto description]
	 * @return [type] [description]
	 */
	public function addHeadPhoto()
	{
		$name = Input::get('name');
		$method = Request::method();
		if(empty($name)){
			return Redirect::to('/admin/defaultError')->with('message',"名称不能为空");
		}
		$description = Input::get("description");
		if(empty($description)){
			return Redirect::to('/admin/defaultError')->with('message','图片描述信息不能为空');
		}
		//图标
		$fileName = strtotime(date('Y-m-d',time()));
		$filePath = './upload/icon/'.$fileName.'/';
		if(!file_exists($filePath)) {
			mkdir($filePath,0755,true);
		}
		$arr = Input::file('icon');
		$lastFilePath = null;
		if(!empty($arr))
		{
			//判断作品类型，只能是png,jpg格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png','gif');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png类型文件');
			}
			$ext = $arr->guessExtension();
			$imgName = time().uniqid();
			$imgName = $imgName.'.'.$ext;
			$lastFilePath = $filePath.$imgName;
			$arr->move($filePath,$imgName);
			$imgSportraitFilePath = ltrim($lastFilePath,'.');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','请上传推广图片');
		}
		$storage = array(
			'name'=>$name,
			'icon'=>$imgSportraitFilePath,
			'description'=>$description,
			'addtime'=>time(),
		);
		$id = DB::table('headphoto')->insert($storage);
		if($id = true){
			return Redirect::to('/admin/HeadPhotoList')->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
	}
	/**
	 * [HeadPhotoList 列表展示]
	 */
	public function HeadPhotoList()
	{
		//利用php操作reids/
		//实例化Redis对象
		//连接reids服务器请问谁知道在哪买香港或者美国的云服务器Linux的比较好有推荐的吗
		//请问谁知道在哪买香港或者美国的云服务器Linux的比较好有推荐的吗，性价比和速度都比较好的服务商有推荐的吗多谢多谢多谢啊
		$redis = MyRedis::connection('default');
		$redis->set('wanhui','吃饭了哈哈哈哈');
		echo $redis->get('wanhui');
		$headphoto = DB::table('headphoto')->get();
		return View::make('advertising.headphotolist')->with('headphoto',$headphoto);
	}
	//更新推广图片
	public function updHeadPhotoView($id)
	{
		$headphoto = DB::table('headphoto')->where('id','=',$id)->first();
		return View::make('advertising.updheadphotoview')->with('headphoto',$headphoto);
	}
	public function updHeadPhoto()
	{
		$id = Input::get("id");
		$name = Input::get('name');
		$method = Request::method();
		if(empty($name)){
			return Redirect::to('/admin/defaultError')->with('message',"名称不能为空");
		}
		$description = Input::get("description");
		if(empty($description)){
			return Redirect::to('/admin/defaultError')->with('message','图片描述信息不能为空');
		}
		//图标
		$fileName = strtotime(date('Y-m-d',time()));
		$filePath = './upload/icon/'.$fileName.'/';
		if(!file_exists($filePath)) {
			mkdir($filePath,0755,true);
		}
		$arr = Input::file('icon');
		$lastFilePath = null;
		if(!empty($arr))
		{
			//判断作品类型，只能是png,jpg格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png','gif');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png类型文件');
			}
			//删除原有上传的一个图片
			$pic = DB::table('headphoto')->where('id','=',$id)->first(array('icon'));
			if(!empty($pic['icon'])){
				$fn = ".".$pic['icon'];
				if(file_exists($fn))
				{
					unlink($fn);
				}
			}
			$ext = $arr->guessExtension();
			$imgName = time().uniqid();
			$imgName = $imgName.'.'.$ext;
			$lastFilePath = $filePath.$imgName;
			$arr->move($filePath,$imgName);
			$imgSportraitFilePath = ltrim($lastFilePath,'.');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','请上传推广图片');
		}
		$storage = array(
			'name'=>$name,
			'icon'=>$imgSportraitFilePath,
			'description'=>$description,
			'addtime'=>time(),
		);
		$id = DB::table('headphoto')->where('id','=',$id)->update($storage);
		if($id = true){
			return Redirect::to("/admin/HeadPhotoList")->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
	}
}