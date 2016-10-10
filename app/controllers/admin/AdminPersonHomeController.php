<?php
/**
 * 个人主页管理
 * @author:wang.hongli
 * @since:2016/05/26
 */
class AdminPersonHomeController extends BaseController{

	private $adminpersonhome;

	function __construct(){
		$this->adminpersonhome = new AdminPersonHome();
	}
	/**
	* 个人主页列表
	 * @author:wang.hongli
	 *@since :2016/07/15
	 */
	public function personalHomepage(){
		$flag = Input::has('flag') ? intval(Input::get('flag')) : 0;
		$list = $this->adminpersonhome->getList($flag);
		$last_list = [];
		$column = [];
		if(!empty($list)){
			foreach($list as $k=>$v){
				$last_list[$v['category']][] = $v;
				$column[$v['category']] = '分栏'.$v['category'];
			}
		}
		return View::make('adminpersonhome.homepage')->with('last_list',$last_list)->with('flag',$flag)->with('column',$column);
	}

	/**
	 * 添加个人主页列表
	 * @author :wang.hongli
	 * @since :2016/07/17
	 */
	public function addPersonHome(){
		$method = Request::method();
		$rs = '添加图片失败';
		if('POST' == $method){
			// $data = Input::all();
			$data = Request::all();
			if(isset($data['_token'])){
				unset($data['_token']);
			}
			$rs = $this->adminpersonhome->addPersonHome($data);
			if($rs !== true){
				return Redirect::to('/admin/defaultError')->with('message',$rs);
			}else{
				return Redirect::to('/admin/personalHomepage');
			}
		}
		$list = $this->adminpersonhome->getList(0);
		$category = [0=>'独立'];
		if(!empty($list)){
			foreach($list as $k=>$v){
				if($v['status'] == 1){
					continue;
				}
				$category[$v['id']] = $v['name'];
			}
		}
		return View::make('adminpersonhome.addpersonhome')->with('category',$category);
	}

	/**
	 * 联动菜单
	 * @author :wang.hongli
	 * @since :2016/07/17
	 */
	public function getPersonHomeCategory(){
		$flag = Input::has('flag') ? intval(Input::get('flag')) : 0;
		$list = $this->adminpersonhome->getList($flag);
		$return = [0=>'独立'];
		if(!empty($list)){
			foreach($list as $k=>$v){
				$return[$v['id']] = $v['name'];
			}
		}
		echo  json_encode($return,JSON_FORCE_OBJECT);
	}

	/**
	 * 启用或者禁列表
	 * @author :wang.hongli
	 * @since :2016/07/17
	 */
	public function opetratorPersonHome(){
		$isajax = Request::ajax();
		if(!$isajax){
			echo "操作失败";
			return;
		}
		$status = Input::has('status') ? intval(Input::get('status')) : 0;
		$status = 1 & $status ? 0 : 1;
		$msg = '操作成功';
		$id = Input::has('data_id') ? intval(Input::get('data_id')) : 0;
		if(empty($id)){
			echo '操作失败';
			return;
		}
		try {
			DB::table('personal_homepage')->where('id',$id)->update(['status'=>$status]);
		} catch (Exception $e) {
			echo '操作失败';
			return;
		}
		echo $msg;
	}

	/**
	 * 更新排序
	 * @author :wang.hongli
	 * @since : 2016/07/17
	 */
	public function updateSort(){
		$isajax = Request::ajax();
		if(!$isajax){
			echo "操作失败";
			return;
		}
		$sort = Input::has('sort') ? intval(Input::get('sort')) : 0;
		$id = Input::has('id') ? intval(Input::get('id')) : 0;
		if(empty($sort) || empty($id)) {
			echo "参数错误";
			return;
		}
		DB::table('personal_homepage')->where('id',$id)->update(['sort'=>$sort]);
		echo '修改成功';
	}

	/**
	 * 更新列表名称
	 * @author :wang.honngli
	 * @since :2017/07/18
	 */
	public function updateName(){
		$name = Input::has("name") ? trim(Input::get('name')) : '';
		$id = Input::has('id') ? intval(Input::get('id')) : 0;
		if(empty($id) || empty($name)){
			echo "参数错误";
			return;
		}		
		try {
			DB::table('personal_homepage')->where('id',$id)->update(['name'=>$name]);
			echo '修改成功';
		} catch (Exception $e) {
			echo '参数错误';
			return;
		}
	}

	/**
	 * 更新列表图标
	 * @author :wang.hongli
	 * @since :2016/07/18
	 */
	public function updatePersonHomeIcon($id=0){
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message','参数错误');
		}
		$rules = [
			'icon'=>'required|image',
		];
		$message = [
			'icon.required'=>'请上传图标',
			'icon.image'=>'图标格式错误',
		];
		$data = Input::all();
		if(isset($data['_token'])){
			unset($data['_token']);
		}
		$filePath = public_path().'/upload/homepageicon/';
		$validator = Validator::make ( $data, $rules,$message );
		if ($validator->fails ()) {
			$msg =  $validator->messages()->first();
			return Redirect::to('/admin/defaultError')->with('message',$msg);
		}
		//图片上传
		$file = Input::file('icon');
		$ext = $file->guessExtension();
		$imgName = time().uniqid();
		$imgName = $imgName.'.'.$ext;
		if($file->move($filePath,$imgName)){
			try {
				DB::table('personal_homepage')->where('id',$id)->update(['icon'=>$imgName]);
				$url = "/upload/homepageicon/".$imgName;
				$icon = 'icon'.$id;
				echo "<script type='text/javascript'>window.parent.document.getElementById('$icon').setAttribute('src','$url');</script>";
			} catch (Exception $e) {
				return Redirect::to('/admin/defaultError')->with('message','添加图片失败');
			}
			
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加图片失败');
		}
	}

	public function updateColumn(){
		$id = Input::get('id');
		$category = Input::get('category');
		try {
			DB::table('personal_homepage')->where('id',$id)->update(['category'=>$category]);
			echo "分栏".$category;
		} catch (Exception $e) {
			echo "error";
		}
	}
}
?>