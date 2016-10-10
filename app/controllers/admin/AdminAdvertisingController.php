<?php 
class AdminAdvertisingController extends BaseController {
		
	private $adminAdvertising;
	private $ad_type;
	private $upload_path;
	private $url ;
	private $is_close;
	private $type;
	private $platform;
	private $is_del;
	public function __construct(){
		$this->adminAdvertising  = new AdminAdvertising();
		$this->ad_type = [
			-1=>'全部',
			0=>'大图',
			1=>'小图'
		];
		$this->upload_path ='./upload/third_adversing/';
		$this->url = Config::get('app.url');
		$this->is_close = [0=>'不可关闭',1=>'可关闭'];
		$this->type = [
			0=>'站外',
			1=>'站内人',
			2=>'诵读会',
			3=>'夏青杯',
			4=>'诵读联合会',
			5=>'诗经奖',
			6=>'站内比赛',
			8=>'静态图片，不做任何操作',
			9=>'线下活动',
			11=>'班级活动报名'
		];
		$this->platform = [
			-1=>'全部',
			0=>'苹果',
			1=>'安卓'
		];
		$this->is_del = [
			-1=>'全部',
			0=>'未删除',
			1=>'删除'
		];

	}

	/**
	 * 添加广告栏目
	 * @author:wang.hongli
	 * @since :2016/08/08
	 */
	public function addAdvertisingColumn(){
		$method = Request::method();
		if('POST' == $method){
			$rules = [
				'name'=>'required'
			];
			$message = [
				'name.required'=>'请填写栏目名称',
			];
			$insert['name'] = trim(Input::get('name'));
			$insert['addtime'] = time();
			$validator = Validator::make($insert,$rules,$message);
			if($validator->fails()){
				return Redirect::to('/admin/defaultError')->with('message',"请填写栏目名称");
			}
			$flag = $this->adminAdvertising->addAdvertisingColumn($insert);
			if(!$flag){
				return Redirect::to('/admin/defaultError')->with('message'," 插入失败，请重试");
			}
		}
		$data['list'] = $this->adminAdvertising->getAdvertisingColumnList();
		return View::make('adminadvertising.addadvertisingcolumn')->with('data',$data);
	}

	/**
	 * 添加第三方广告
	 * @author :wang.hongli
	 * @since :2016/08/08
	 */
	public function addThirdAdvising(){
		$column_list_tmp = $this->adminAdvertising->getAdvertisingColumnList();
		$column_list = [];
		if(!empty($column_list_tmp)){
			foreach($column_list_tmp as $k=>$v){
				$column_list[$v['id']] = $v['name'];
			}
		}
		$data['column_list'] = $column_list;
		$data['ad_type'] = $this->ad_type;
		$data['type'] = $this->type;
		$method = Request::method();
		if('POST' == $method){
			$rules = [
				'name'=>'required',
				'column_id'=>'required',
				'description'=>'required',
				'ad_type'=>'required|in:0,1',
				'is_close'=>'required|in:0,1',
				'weight'=>'required|integer',
				'duration'=>'required|integer',
				'starttime'=>'required|date',
				'endtime'=>'required|date',
				'pic'=>'required|image',
				'type'=>'required|integer',
				'platform'=>'required|in:0,1',
				'argument'=>'integer',
				'url'=>'url',
			];
			$message = [
				'name.required'=>'请输入广告名称',
				'column_id'=>'请选则栏目',
				'description.required'=>'请输入描述信息',
				'ad_type.required'=>'请选择广告类型',
				'ad_type.in'=>'广告类型错误',
				'is_close.required'=>'用户是否关闭标识错误',
				'is_close.in'=>'用户是否关闭标识错误',
				'weight.required'=>'请填写权重',
				'weight.integer'=>'权重格式错误',
				'duration.required'=>'请填写广告时长',
				'duration.integer'=>'广告时长格式错误',
				'starttime.required'=>'请填写广告开始时间',
				'starttime.date'=>'广告开始时间格式错误',
				'endtime.required'=>'请填写广告结束时间',
				'endtime.date'=>'广告结束格式错误',
				'pic.required'=>'请上传图片',
				'pic.image'=>'图片格式错误',
				'type.required'=>'请选择跳转类型',
				'type.integer'=>'跳转类型错误',
				'platform.required'=>'选择平台类型',
				'platform.in'=>'平台类型错误',
				'argument.integer'=>'跳转位置错误',
				'url.url'=>'广告地址格式错误'

			];
			$input = Input::all();
			$validator = Validator::make($input,$rules,$message);
			if($validator->fails()){
				Input::flash();
				return View::make('adminadvertising.addthirdadvising')->withErrors($validator)->withInput(Input::all())->with('data',$data);
			}

			//图片上传
			$file = Input::file('pic');
			$file_path = $this->adminAdvertising->uploadAdvPic($file,$this->upload_path);
			if(!$file_path){
				return Redirect::to('/admin/defaultError')->with('message','图片上传失败');
			}elseif($file_path == 'error'){
				return Redirect::to('/admin/defaultError')->with('message','请上传符合规格的图片,大图1080*308 小图1080*138');
			}
			if(isset($input['_token'])){
				unset($input['_token']);
			}
			$input['pic'] = ltrim($file_path,'.');
			$input['starttime'] = strtotime($input['starttime']);
			$input['endtime'] = strtotime($input['endtime'])+86399;
			try {
				DB::table('third_advertising')->insert($input);
			} catch (Exception $e) {
				return Redirect::to('/admin/defaultError')->with('message','广告发布失败');
			}
		}
		return View::make('adminadvertising.addthirdadvising')->with('data',$data);
	}

	/**
	 * 获取第三方广告列表
	 * @author :wang.hognli
	 * @since :2016/08/09
	 */
	public function thirdAdvisingList(){

		$pagesize = 20;
		$column_list = [0=>'全部'];
		$tmp_columnList = $this->adminAdvertising->getAdvertisingColumnList();
		if(!empty($tmp_columnList)){
			foreach($tmp_columnList as $c=>$column){
				$column_list[$column['id']] = $column['name'];
			}
		}
		$data['column_list'] = $column_list;
		$data['type'] = $this->type;
		$data['platform'] = $this->platform;
		$data['is_del'] = $this->is_del;

		$search['column_id'] = Input::get('column_id',0);
		$search['ad_type'] = Input::get('ad_type',-1);
		$search['name'] = Input::get('name','');
		$search['starttime'] = Input::get('starttime','');
		$search['endtime'] = Input::get('endtime','');
		$search['platform'] = Input::get('platform',-1);
		$search['starttime'] = Input::get('starttime','');
		$search['endtime'] = Input::get('endtime','');
		$search['is_del'] = Input::get('is_del',-1);

		$conn = DB::table('third_advertising');
		$method = Request::method();
		if('POST' == $method){
			if(!empty($search['column_id'])){
				$conn->where('column_id',$search['column_id']);
			}
			if(!empty($search['name'])){
				$conn->where('name',$search['column_id']);
			}
			if(!empty($search['starttime'])){
				$conn->where('starttime','>=',strtotime($search['starttime']));
			}
			if(!empty($search['endtime'])){
				$conn->where('endtime','<=',strtotime($search['endtime'])+86399);
			}
			if($search['ad_type'] != -1){
				$conn->where('ad_type',$search['ad_type']);
			}
			if($search['platform'] != -1){
				$conn->where('platform',$search['platform']);
			}
			if(!empty($search['starttime'])){
				$conn->where('starttime',strtotime($search['starttime']));
			}
			if(!empty($search['endtime'])){
				$conn->where('endtime',strtotime($search['endtime'])+86399);
			}
			if($search['is_del'] != -1){
				$conn->where('is_del',$search['is_del']);
			}
		}
		$list = $conn->orderBy('id','desc')->paginate($pagesize);
		if(!empty($list)){
			foreach($list as $k=>&$v){
				$v['pic'] = $this->url.$v['pic'];
			}
		}
		$data['list'] = [];
		if(!empty($list)){
			$data['list'] = $list;
		}
		$data['ad_type'] = $this->ad_type;
		$data['is_close'] = $this->is_close;
		return View::make('adminadvertising.thirdadvisinglist')->with('data',$data)->with('search',$search);
	}
	/**
	 * 删除广告
	 * @author :wang.hongli
	 * @since :2016/08/09
	 */
	public function delRevertThrAdv(){
		$isajax = Request::ajax();
		if(!$isajax){
			echo '请求方式错误';
			return;
		}
		$id = intval(Input::get('id'));
		if(empty($id)){
			echo '参数错误';
			return;
		}
		$is_del = intval(Input::get('is_del'));
		$is_del = 1^$is_del ? 1 : 0;
		try {
			DB::table('third_advertising')->where('id',$id)->update(['is_del'=>$is_del]);
			echo 1;
		} catch (Exception $e) {
			echo $e->getMessage();
			echo '操作错误';
		}	
	}

	/**
	 * 更新广告
	 * @author :wang.hongli
	 * @since :2016/08/10
	 */
	public function updateThrAd($id=0){
		try {
			$rs = DB::table('third_advertising')->where('id',$id)->first();	
		} catch (Exception $e) {
			$rs  = [];	
		}
		if(empty($rs)){
			return Redirect::to('/admin/defaultError')->with('message','更新错误，请重试');
		}
		$column_list_tmp = $this->adminAdvertising->getAdvertisingColumnList();
		$column_list = [];
		if(!empty($column_list_tmp)){
			foreach($column_list_tmp as $k=>$v){
				$column_list[$v['id']] = $v['name'];
			}
		}
		$data['column_list'] = $column_list;
		$data['ad_type'] = $this->ad_type;
		$data['type'] = $this->type;
		return View::make('adminadvertising.updatethrad')->with('data',$data)->with('rs',$rs);		
	}

	/**
	 * 更新广告动作
	 * @author :wang.hongli
	 * @since :2016/08/10
	 */
	public function doUpdateThirdAdvising(){
		$rules = [
			'name'=>'required',
			'column_id'=>'required',
			'description'=>'required',
			'ad_type'=>'required|in:0,1',
			'is_close'=>'required|in:0,1',
			'weight'=>'required|integer',
			'duration'=>'required|integer',
			'starttime'=>'required|date',
			'endtime'=>'required|date',
			'type'=>'required|integer',
			'platform'=>'required|in:0,1',
			'argument'=>'integer',
			'url'=>'url',
		];
		$message = [
			'name.required'=>'请输入广告名称',
			'column_id'=>'请选则栏目',
			'description.required'=>'请输入描述信息',
			'ad_type.required'=>'请选择广告类型',
			'ad_type.in'=>'广告类型错误',
			'is_close.required'=>'用户是否关闭标识错误',
			'is_close.in'=>'用户是否关闭标识错误',
			'weight.required'=>'请填写权重',
			'weight.integer'=>'权重格式错误',
			'duration.required'=>'请填写广告时长',
			'duration.integer'=>'广告时长格式错误',
			'starttime.required'=>'请填写广告开始时间',
			'starttime.date'=>'广告开始时间格式错误',
			'endtime.required'=>'请填写广告结束时间',
			'endtime.date'=>'广告结束格式错误',
			'type.required'=>'请选择跳转类型',
			'type.integer'=>'跳转类型错误',
			'platform.required'=>'选择平台类型',
			'platform.in'=>'平台类型错误',
			'argument.integer'=>'跳转位置错误',
			'url.url'=>'广告地址格式错误'

		];
		$input = Input::all();
		$validator = Validator::make($input,$rules,$message);
		if($validator->fails()){
			Input::flash();
			return View::make('adminadvertising.addthirdadvising')->withErrors($validator)->withInput(Input::all())->with('data',$data);
		}
		$file = Input::file('pic');
		if(!empty($file)){
			$file_path = $this->adminAdvertising->uploadAdvPic($file,$this->upload_path);
			if(!$file_path){
				return Redirect::to('/admin/defaultError')->with('message','图片上传失败');
			}elseif($file_path == 'error'){
				return Redirect::to('/admin/defaultError')->with('message','请上传符合规格的图片,大图1080*308 小图1080*138');
			}
			$input['pic'] = ltrim($file_path,'.');
		}else{
			unset($input['pic']);
		}
		if(isset($input['_token'])){
			unset($input['_token']);
		}
		$input['starttime'] = strtotime($input['starttime']);
		$input['endtime'] = strtotime($input['endtime'])+86399;
		try {
			DB::table('third_advertising')->where('id',$input['id'])->update($input);
		} catch (Exception $e) {
			return Redirect::to('/admin/defaultError')->with('message','广告发布失败');
		}
		return Redirect::to('/admin/updateThrAd/'.$input['id']);
	}
}

 ?>