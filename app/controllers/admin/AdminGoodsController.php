<?php
/**
 * 后台商品模型
 * @author:wang.hongli
 * @since:2016/05/24
 */
class AdminGoodsController extends BaseController {
	
	private $goods_category = array();
	private $type = array();
	
	function __construct(){
		$this->adminGoods = new AdminGoods();
		$this->goods_category = array(
				0=>'比赛商品',
				1=>'班级活动商品'
		);
		$this->type = array(
				0=>'永久',
				1=>'一年',
				2=>'一个月',
				3=>'一天',
				4=>'一次'
		);
	}
	/**
	 * @author:wang.hongli
	 * @since:2016/05/24
	 * @获取所有商品列表
	 */
	public function getGoodsList(){
		$goods_category_flag = Input::has('good_category_flag') ? intval(Input::get('good_category_flag')) : 0;
		$search = array('goods_category_flag'=>$goods_category_flag);
		$rs = DB::table('goods')->where('flag',$goods_category_flag)->orderby('id','desc')
		->leftjoin('good_param','good_param.goodid','=','goods.id')
		->select('goods.id as id','goods.name as name','goods.description as description','goods.price as price','goods.discount_price as discount_price','good_param.icon as icon')
		->get();
		return View::make('admingoods.admingoods')->with('list',$rs)->with('search',$search)->with('goods_category',$this->goods_category);
		
	}
	/**
	 * [updateGoods description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function updateGoods($id)
	{
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message','获取权限信息失败');
		}
		$goods = DB::table('goods')->where('goods.id','=',$id)
		->leftjoin('good_param','good_param.goodid','=','goods.id')
		->select('goods.id as id','goods.name as name','goods.description as description','goods.price as price','goods.discount_price as discount_price','good_param.icon as icon')
		->first();
		return View::make('admingoods.updategoods')->with('goods',$goods);
	}
	/**
	 * [updGoods description]
	 * @return [type] [description]
	 */
	public function updGoods()
	{
		$id = trim(Input::get('id'));
		$name = trim(Input::get('name'));
		if(empty($name)){	
			return Redirect::to('/admin/defaultError')->with('message',"商品名称不能为空");
		}
		$price = Input::get('price');
		if(empty($price)){
			return Redirect::to('/admin/defaultError')->with('message','商品价格不能为空');
		}
		$description = Input::get('description');
		if (empty($description)) {
			return Redirect::to('/admin/defaultError')->with('message','商品描述不能为空');
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
				$pic = DB::table('good_param')->where('goodid','=',$id)->first(array('icon'));
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
				$lastFilePath = ltrim($lastFilePath,'.');
				//小图
				$img = Image::make('.'.$lastFilePath)->resize(100, 100);
				$imgSportrait = $filePath."s_".$imgName;
				$img->save($imgSportrait);
				$imgSportraitFilePath = ltrim($imgSportrait,'.');
				//删除原图片
				unlink($filePath.$imgName);
				$good_param = DB::table('good_param')->where('goodid','=',$id)->first(array('id','goodid','icon'));
				if(!empty($good_param['goodid']))
				{
					DB::table('good_param')->where('goodid','=',$id)->update(array('icon'=>$imgSportraitFilePath));
				}else
				{
					$goodparam = array(
						'goodid'=>$id,
						'icon'=>$imgSportraitFilePath,
					);
					DB::table('good_param')->insert($goodparam);
				}
			}elseif($measure['0'] && $measure['1'] < 250){
				//删除原有上传的一个图片
				$pic = DB::table('good_param')->where('goodid','=',$id)->first(array('icon'));
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
				//处理入库
				$good_param = DB::table('good_param')->where('goodid','=',$id)->first(array('id','goodid','icon'));
				if(!empty($good_param['goodid']))
				{
					DB::table('good_param')->where('goodid','=',$id)->update(array('icon'=>$imgSportraitFilePath));
				}else
				{
					$goodparam = array(
						'goodid'=>$id,
						'icon'=>$imgSportraitFilePath,
					);
					DB::table('good_param')->insert($goodparam);
				}
			}
		}
		$arr = array(
			'name'=>$name,
			'price'=>$price,
			'description'=>$description,
		);
		$id = DB::table('goods')->where('id','=',$id)->update($arr);
		if($id = true){
			return Redirect::to('/admin/getGoodsList')->with('message','添加成功');
		}else{
			return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
		}
	}
	/**
	 * 修改商品信息
	 * @author:wang.hongli
	 * @since:2016/05/24
	 * @param:data_type 1 修改商品名称 2,修改商品描述 3修改价格
	 * @param:id 商品id
	 * @param:val 修改后的值
	 */
	public function modifyGoodInfo(){
		$rules = array(
				'data_type'=>'required|integer',
				'id'=>'required|integer',
				'val'=>'required'
		);
		$message = array(
				'data_type.required'=>'操作类型不能为空',
				'data_type.integer'=>'操作类型必须为数字',
				'id.required'=>'商品id不能为空',
				'id.integer'=>'商品id必须为整型',
				'val.required'=>'修改信息不能为空',
		);
		$data = Input::all();
		unset($data['_token']);
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			echo $validator->messages()->first();
			return ;
		}else{
			switch($data['data_type']){
				case 1:
					$update_arr = array('name'=>$data['val']);
					break;
				case 2:
					$update_arr = array('description'=>$data['val']);
					break;
				case 3:
					$update_arr = array('price'=>$data['val']);
					break;
				case 4:
					$update_arr = array('discount_price'=>$data['val']);
					break;
				case 5:
					$start_time = strtotime($data['val']);
					$update_arr = array('start_time'=>$start_time);
					break;
				case 6:
					$end_time = strtotime($data['val']);
					$update_arr = array('end_time'=>$end_time);
					break;
			}
			try {
				if(DB::table('goods')->where('id',$data['id'])->update($update_arr)){
					echo 1;
				}else{
					echo 2;
				}
			} catch (Exception $e) {
				echo "修改失败，请重试";
			}
		}
	}
	/**
	 * 添加商品
	 * @author:wang.hongli
	 * @since:2016/05/24
	 */
	public function addGoods(){
		$rules = array(
			'name'=>'required',
			'price'=>'required|digits_between:1,5',
			'description'=>'required',
		);
		$message = array(
			'name.required'=>'请填写商品名称',
			'price.required'=>'请填写商品价格',
			'price.digits_between'=>'商品价格1-99999元',
			'description.required'=>'请填写描述信息'
		);
		if(Request::method() == 'POST'){
			$data = Input::all();
			unset($data['_token']);
			//验证
			$validator = Validator::make($data, $rules,$message);
			if($validator->fails()){
				$msg = $validator->messages()->first();
				return Redirect::to('/admin/defaultError')->with('message',$msg);
			}
			$data['start_time'] = strtotime($data['start_time']);
			$data['end_time'] = strtotime($data['end_time']);
			$data['id'] = 0;
			DB::table('goods')->insert($data);
		}
		return View::make('admingoods.addgoods')->with('goods_category',$this->goods_category)->with('type',$this->type);
	}

	/**
	* 添加商品分类
	*@author:wang.hongli
	*@since:2016/07/08
	**/
	public function addGoodCategory(){
		$method = Request::method();
		if('POST' == $method){
			$name = Input::has('name')  ? trim(Input::get('name')) : '';
			$sort = Input::has('sort')  ? intval(Input::get('sort'))  : 1;
			$data = ['name'=>$name,'sort'=>$sort,'pid'=>0,'status'=>0,'addtime'=>time()];			
			$rs = $this->adminGoods->addGoodCategory($data);
			if($rs !== true){
				return Redirect::to('/admin/defaultError')->with('message',$rs);
			}
		}
		$rs = DB::table('goods_category')->where('status',0)->orderBy('sort','asc')->get();
		if(empty($rs)){
			$rs = [];
		}
		return View::make('admingoods.addgoodcategory')->with('rs',$rs);
	}

	/**
	*修改商品分类
	*@author:wang.hongli
	*@since:2016/07/08
	*/
	public function modGoodCategory(){
		$isajax = Request::ajax();
		if($isajax){
			$id = Input::has('id') ? intval(Input::get('id')) : 0;
			$val = Input::has('val') ? Input::get('val') : '';
			$data_flag = Input::has('data_flag') ? intval(Input::get('data_flag')) : 0;
			if(empty($id) || empty($val) || empty($data_flag)){
				echo "修改失败";
				return;
			}
			switch ($data_flag) {
				case 1:
					$data = ['name'=>$val];
					break;
				case 2:
					$data = ['sort'=>$val];
					break;
			}
			DB::table('goods_category')->where('id',$id)->update($data);
			echo 1;
			return;
		}else{
			echo '修改失败';
		}
	}
	/**
	 * 添加结束比赛商品
	 * @author : wang.hongli
	 * @since : 2016/07/08
	 */
	public function addFinishCompGoods($id=0){
		$finish_comp = $this->adminGoods->getFinishCompetition();
		$method = Request::method();

		if('POST'==$method){
			$data = Input::all();
			$rs = $this->adminGoods->addFinishCompGoods($data);
			if(!$rs){
				return Redirect::to('/admin/defaultError')->with('message','添加错误');
			}
		}
		return View::make('admingoods.addfinishcompgood')->with('finish_comp',$finish_comp);
	}

	/**
	 * 结束比赛商品列表
	 * @param  wang.hongli
	 * @since :2016/07/08
	 */
	public function finishCompGoodsList(){
		$list = DB::table('goods')->where('flag',2)->where('good_pid',0)->get();
		if(empty($list)){
			$list = [];
		}
		$goods_id = [];
		foreach($list as $k=>$v){
			$goods_id[] = $v['id'];
		}
		//select good_attach
		$tmp_good_info = [];
		if(!empty($goods_id)){
			$tmp_good_info = DB::table('goods')->whereIn('good_pid',$goods_id)->where('flag',2)->get();
		}
		$good_info = [];
		if(!empty($tmp_good_info)){
			foreach($tmp_good_info as $v){
				$good_info[$v['good_pid']] = $v;
			}
		}
		foreach($list as $k=>&$v){
			$v['cd_price'] = isset($good_info[$v['id']]) ? $good_info[$v['id']]['price'] : 0;
			$v['cd_id'] = isset($good_info[$v['id']]) ? $good_info[$v['id']]['id'] : 0;
			$v['cd_name'] = isset($good_info[$v['id']]) ? $good_info[$v['id']]['name'] : 0;
		}
		return View::make('admingoods.finishcompgoodlist')->with('list',$list);
	}

	/**
	 * 更新结束比赛商品信息
	 * @author :wang.hongli
	 * @since :2016/07/08
	 */
	public function updateFinishCompGoods($id=0){
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message','更新失败');
		}
		//获取原来信息
		$good_info = DB::table('goods')->where('id',$id)->first();
		if(empty($good_info)){
			return Redirect::to('/admin/defaultError')->with('message','更新失败');
		}
		$finish_comp = $this->adminGoods->getFinishCompetition();
		$good_attach = DB::table('goods')->where('good_pid',$good_info['id'])->where('flag',2)->first();
		$good_info['cd_price'] = !empty($good_attach['price']) ? $good_attach['price'] : 0;
		$good_info['cd_id'] = !empty($good_attach['id']) ? $good_attach['id'] : 0;
		$good_info['cd_name'] = !empty($good_attach['name']) ? $good_attach['name'] : '';
		return View::make('admingoods.updatefinishcompgoods')->with('good_info',$good_info)->with('finish_comp',$finish_comp);
	}

	/**
	 * 执行更新
	 * @author :wang.hongli
	 * @since :2016/07/08
	 */
	public function doUpdateFinishCompGoods(){
		$data = Input::all();
		if(empty($data)){
			return Redirect::to('/admin/defaultError')->with('message','更新失败');
		}
		$good_attach_id_price = !empty($data['cd_price']) ? $data['cd_price'] : 0;
		$good_attach_id_name = !empty($data['cd_name']) ? $data['cd_name'] : '活动现场光盘';
		unset($data['_token'],$data['cd_price'],$data['cd_name']);
		DB::table('goods')->where('id',$data['id'])->update($data);
		$good_id = $data['id'];
		$tmp_good_attach = DB::table('goods')->where('good_pid',$good_id)->where('flag',2)->first();
		$time = time();
		if(empty($tmp_good_attach)){
			$good_data = [
				'name'=>$good_attach_id_name,
				'price'=>$good_attach_id_price,
				'type'=>$data['type'],
				'description'=>$good_attach_id_name,
				'competition_id'=>$data['competition_id'],
				'flag'=>2,
				'start_time'=>$time,
				'end_time'=>$time,
				'discount_price'=>0,
				'good_pid'=>$good_id,
			];
			DB::table('goods')->insert($good_data);
		}elseif(!empty($tmp_good_attach)){
			if(empty($good_attach_id_price)){
				DB::table('goods')->where('good_pid',$good_id)->where('flag',2)->delete();
			}else{
				DB::table('goods')->where('good_pid',$good_id)->where('flag',2)->update(['price'=>$good_attach_id_price,'name'=>$good_attach_id_name]);
			}
		}
		return Redirect::to('admin/finishCompGoodsList');	
	}
}