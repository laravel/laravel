<?php
/**
* @author :wang.hongli
* @since :2016/07/12
*/
class AdminAuthoritymanagementController extends BaseController
{
	//展示列表
	public function authorityManagement()
	{
		header("content-type:text/html;charset=utf8");
		$permission_config = DB::table('permission_config')
		->leftJoin('permission_config_detail','permission_config_detail.pid','=','permission_config.id')
		->select('permission_config.id as perid','permission_config.name as name','permission_config.status as status','permission_config.addtime as addtime','permission_config.flag as flag','permission_config.pid as pid','permission_config.plat_form as plat_form','permission_config.icon as icon','permission_config.action as action','permission_config.sort as sort','permission_config_detail.title as title','permission_config_detail.desc as desc','permission_config_detail.id as detailid')
		->paginate(7);
		// var_dump($permission_config);die;
		// var_dump(DB::getQueryLog());die;
		return View::make('authority.authoritymanagement')->with('permission_config',$permission_config);
	}
	//添加权限
	public function addFile()
	{	
		$name = trim(Input::get('name'));
		$method = Request::method();
		$userRs = DB::table('permission_config')->where('name','=',$name)->first(array('id'));
		if(!empty($userRs)){	
			return Redirect::to('/admin/defaultError')->with('message',"权限名称已存在");
		}
		if('POST' == $method){
			$name = trim(Input::get('name'));
			$data = ['name'=>$name];
			$rules = ['name'=>'required'];
			$message = ['name.required'=>'请填写权限名称'];
			$validator = Validator::make($data,$rules,$message);
			if($validator->fails()){
				// Notification::success('添加成功');
				return Redirect::to('/admin/defaultError')->with('message',"请填写权限名称");
			}
		}
		if(empty($name)){
			return View::make('authority.addfile');
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
			//判断上传类型，只能是png,jpg,gif格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png','gif');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png,gif类型文件');
			}
			$measure = getimagesize($arr);
			if($measure['0'] && $measure['1'] > 250){
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
				//删除原图片
				unlink($filePath.$imgName);
			}elseif($measure['0'] && $measure['1'] < 250){
				
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图片
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
			}
		}else
		{
			return Redirect::to('/admin/defaultError')->with('message','请上传图片');
		}
		$sort = trim(Input::get('sort'));
		if(empty($sort)){
			return Redirect::to('/admin/defaultError')->with('message','请填写排序名称');
		}
		$flag = trim(Input::get('flag'));
		if(empty($flag)){
			return Redirect::to('/admin/defaultError')->with('message','请填写权限标识');
		}
		$plat_form = trim(Input::get('plat_form'));
		$pid =  Input::get('pid');
		$action = Input::get('action');
		$status = Input::get('status');
		if (empty($action)) {
			return Redirect::to('/admin/defaultError')->with('message','请填写客户端标识');
		}
		$arr = array(
				'name'=>$name,
				'icon'=>$imgSportraitFilePath,
				'sort'=>$sort,
				'addtime'=>time(),
				'flag'=>$flag,
				'action'=>$action,
				'pid'=>$pid,
				'plat_form' =>$plat_form,
				'status'=>$status,
			);
		// var_dump($arr);
		$id = DB::table('permission_config')->insert($arr);
		if($id = true){
			return Redirect::to('/admin/defaultError')->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
		if(!$id){
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
		// $this->autoAttention($id);
		return View::make('authority.addfile');
	}
	//添加权限详情
	public function permissionsDetail()
	{	
		$pid = Input::get('pid');
		if($pid === '0'){
			return Redirect::to('/admin/defaultError')->with('message','请选择详情列表');
		}
		if(empty($pid)){
			$permissionsdetail = DB::table('permission_config')->get();
			return View::make('authority.permissionsdetail')->with('permissionsdetail',$permissionsdetail);
		}
		
		//图标
		$fileName = strtotime(date('Y-m-d',time()));
		$filePath = './upload/icon/'.$fileName.'/';
		if(!file_exists($filePath)) {
			mkdir($filePath,0755,true);
		}
		$arr = Input::file('pic');
		$lastFilePath = null;
		if(!empty($arr))
		{
			//判断上传类型，只能是png,jpg,gif格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png','gif');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png,gif类型文件');
			}
			$measure = getimagesize($arr);
			if($measure['0'] && $measure['1'] > 250){
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
				unlink($filePath.$imgName);
			}elseif($measure['0'] && $measure['1'] < 250){
				
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图片
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
			}
		}else
		{
			//return Redirect::to('/admin/defaultError')->with('message','请上传图片');
		}
		$title = Input::get('title');
		if(empty($title)){
			return Redirect::to('/admin/defaultError')->with('message','请填写标题');
		}
		$desc = Input::get('desc');
		if(empty($desc)){
			return Redirect::to('/admin/defaultError')->with('message','请填写描述信息');
		}
		if(empty($imgSportraitFilePath)){
			$arr = array(
			'pid'=>$pid,
			'title'=>$title,
			'desc'=>$desc,
			);
		}else{
			$arr = array(
			'pid'=>$pid,
			'pic'=>$imgSportraitFilePath,
			'title'=>$title,
			'desc'=>$desc,
			);
		}	
		$id = DB::table('permission_config_detail')->insert($arr);
		if($id = true){
			return Redirect::to('/admin/defaultError')->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
		$permissionsdetail = DB::table('permission_config')->get();
		return View::make('authority.permissionsdetail')->with('permissionsdetail',$permissionsdetail);
	}
	//修改标题
	public function listTitle()
	{
		header("Content-type:text/html;charset=utf8");
		$title = Input::get('title');
		$detailid = Input::get('detailid');
		if(empty($detailid)) {
			echo "error";
			return;
		}
		$sql = "update permission_config_detail set title='{$title}' where id = {$detailid}";
		if(!DB::update($sql)){
			echo "修改成功";
			return;
		}
	}
	//修改描述
	public function listDesc()
	{
		header("Content-type:text/html;charset=utf8");
		$desc = Input::get('desc');
		$detailid = Input::get('detailid');
		if(empty($detailid)) {
			echo "error";
			return;
		}
		$sql = "update permission_config_detail set `desc`='{$desc}' where id = {$detailid}";
		if(!DB::update($sql)){
			echo "error";
			return;
		}
	}
	//修改状态
	public function listStatus()
	{
		$perid = Input::get('perid');
		$sign = Input::get('sign');	
		if(empty($perid)) {
			echo 'error';
			return;
		}else{
			if($sign) {
				$status = 0;
			} else {
				$status = 1;
			}
			$sql = "update permission_config set status={$status} where id = {$perid}";
			if(!DB::update($sql)) {
				echo "error";
				return;
			}
		}
	}
	//更新
	public function updatePermissions($id)
	{
		header("Content-type:text/html;charset=utf8");
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message','获取权限信息失败');
		}
		$permission = DB::table('permission_config')->where('id','=',$id)->first(array('id','name','status','addtime','flag','pid','plat_form','icon','sort','action'));
		return View::make('authority.updatepermissions')->with('permission',$permission);
	}
	//更新
	public function updatePer()
	{
		$privilegeid = trim(Input::get('id'));
		$name = trim(Input::get('name'));
		$method = Request::method();
		$userRs = DB::table('permission_config')->where('name','=',$name)->first(array('id'));
		if(!empty($userRs)){	
			return Redirect::to('/admin/defaultError')->with('message',"权限名称已存在");
		}
		if('POST' == $method){
			$name = trim(Input::get('name'));
			$data = ['name'=>$name];
			$rules = ['name'=>'required'];
			$message = ['name.required'=>'请填写权限名称'];
			$validator = Validator::make($data,$rules,$message);
			if($validator->fails()){
				// Notification::success('添加成功');
				return Redirect::to('/admin/defaultError')->with('message',"请填写权限名称");
			}
		}
		if(empty($name)){
			return View::make('authority.addfile');
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
			//判断上传类型，只能是png,jpg,gif格式
			$my_file_type = strtolower(my_file_type($arr->getRealPath()));
			$arrow_type = array('jpg','png','gif');
			if(empty($my_file_type) || !in_array($my_file_type, $arrow_type))
			{
				// return '请上传jpg,png文件类型';
				return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png,gif类型文件');
			}
			$measure = getimagesize($arr);
			if($measure['0'] && $measure['1'] > 250){
				//删除原有上传的一个图片
				$pic = DB::table('permission_config')->where('id','=',$privilegeid)->first(array('icon'));
				if(!empty($pic['icon'])){
					unlink(".".$pic['icon']);
				}
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
				//删除原图片
				unlink($filePath.$imgName);
			}elseif($measure['0'] && $measure['1'] < 250){
				//删除原有上传的一个图片
				$pic = DB::table('permission_config')->where('id','=',$privilegeid)->first(array('icon'));
				if(!empty($pic['icon'])){
					unlink(".".$pic['icon']);
				}
				$ext = $arr->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$arr->move($filePath,$imgName);
				$imgSportraitFilePath = ltrim($lastFilePath,'.');
				//小图片
				/*
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
				*/
			}
		}
		$sort = trim(Input::get('sort'));
		if(empty($sort)){
			return Redirect::to('/admin/defaultError')->with('message','请填写排序名称');
		}
		$flag = trim(Input::get('flag'));
		if(empty($flag)){
			return Redirect::to('/admin/defaultError')->with('message','请填写权限标识');
		}
		$plat_form = trim(Input::get('plat_form'));
		$pid =  Input::get('pid');
		$action = Input::get('action');
		$status = Input::get('status');
		if (empty($action)) {
			return Redirect::to('/admin/defaultError')->with('message','请填写客户端标识');
		}
		if(empty($imgSportraitFilePath)){
			$arr = array(
				'name'=>$name,
				'sort'=>$sort,
				'addtime'=>time(),
				'flag'=>$flag,
				'action'=>$action,
				'pid'=>$pid,
				'plat_form' =>$plat_form,
				'status'=>$status,
			);
		}else{
			$arr = array(
				'name'=>$name,
				'icon'=>$imgSportraitFilePath,
				'sort'=>$sort,
				'addtime'=>time(),
				'flag'=>$flag,
				'action'=>$action,
				'pid'=>$pid,
				'plat_form' =>$plat_form,
				'status'=>$status,
			);
		}
		$id = DB::table('permission_config')->where('id','=',$privilegeid)->update($arr);
		if($id = true){
			return Redirect::to('/admin/authorityManagement')->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
		if(!$id){
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
	}
}