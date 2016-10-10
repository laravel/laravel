<?php 
/**
 * 后台天籁商城
 * @author :wang.hongli
 * @since :2016/08/03
 */
class AdminTianLaiGoodsController extends BaseController {

	private $adminTianLaiGoods ;
	private $goodsCategory;
	private $unit_array = [1=>'年',2=>'月',3=>'天',4=>'次',5=>'件'];
	private $promptgoods = [0=>'现货',1=>'无货',2=>'众筹'];
	private $upload_path = './upload/goodspic/';
	public function __construct(){
		$this->adminTianLaiGoods = new AdminTianLaiGoods();
		$this->goodsCategory = [
			'3'=>'会员增值商品',
			'4'=>'实体商品',
			'5'=>'出版物商品'
		];
	}

	/**
	 * 后台--更新商品
	 * @author :wang.hongli
	 * @since :2016/08/21
	 */
	public function updateTianLaiGoods($id){
		$msg = '获取商品信息失败';
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message',$msg);
		}
		$data['goodsCategory'] = $this->goodsCategory;
		$data['type'] = $this->unit_array;
		$data['promptgoods'] = $this->promptgoods;
		$rs = DB::table('goods')->leftjoin('good_param','goods.id','=','good_param.goodid')->where('goods.id',$id)->first(
			['goods.id','goods.name','goods.type','goods.description','goods.flag','goods.buynum','good_param.price','good_param.discount_price','good_param.member_price','good_param.postage_price','good_param.flower_price','good_param.discount_flower_price','good_param.member_flower_price','good_param.flower_postage_price','good_param.promptgoods','good_param.crowdfunding','good_param.diamond','good_param.member_diamond','good_param.normal_section','good_param.member_section','good_param.icon','good_param.des_detail','good_param.crowdfundinged','good_param.normal_price_section','good_param.member_price_section','good_param.normal_flower_price_section','good_param.member_flower_price_section']
		);
		if(empty($rs)){
			return Redirect::to('/admin/defaultError')->with('message',$msg);
		}
		$url = Config::get('app.url');
		$rs['icon'] = $url.$rs['icon'];
		//根据商品id获取商品图片
		$images = DB::table('goodspic')->where('goodid',$rs['id'])->get();
		$initialPreview = [];
		$initialPreviewConfig = [];
		if(!empty($images)){
			foreach($images as $k=>$v){
				$initialPreview[] = "<img src='".$url.$v['url']."' class='file-preview-image' style='width:auto;height:120px;'>";
				$initialPreviewConfig[] = [
					'cpation'=>"<img src='".$url.$v['url']."' class='file-preview-image' style='width:auto;height:120px;'>",
					// 'width'=>'120px',
					'url'=>$url.'/admin/delGoodsImage',
					'key'=>$v['id'],
					'extra'=>['id'=>$v['id']],
				];
			}
		}
		$initialPreview = json_encode($initialPreview);
		$initialPreviewConfig = json_encode($initialPreviewConfig);
		return View::make('admintianlaigoods.updatetianlaigoods')->with('data',$data)->with('rs',$rs)->with('initialPreview',$initialPreview)->with('initialPreviewConfig',$initialPreviewConfig);
	}

	/**
	 * 后台--更新商品
	 * @author :wang.hongli
	 * @since :2016/08/22
	 */
	public function doUpdateTianLaiGoods(){
		$method = Request::method();
		$data['goodsCategory'] = $this->goodsCategory;
		$data['type'] = $this->unit_array;
		if('POST' == $method){
			$rules = [
				'id'=>'required|numeric',
				'name'=>'required',
				'price'=>'required|numeric',
				'discount_price'=>'numeric',
				'member_price'=>'numeric',
				'postage_price'=>'numeric',
				'flower_price'=>'integer',
				'discount_flower_price'=>'integer',
				'member_flower_price'=>'integer',
				'flower_postage_price'=>'integer',
				'crowdfunding'=>'numeric',
				'diamond'=>'integer',
				'member_diamond'=>'integer',
				'description'=>'required',
			];
			$message = [
				'id.required'=>'商品id为空',
				'id.numeric'=>'商品id格式错误',
				'name.required'=>'商品名为空',
				'price.required'=>'填写商品价格',
				'price.numeric'=>'价格格式错误',
				'discount_price.numeric'=>'折扣价格错误',
				'member_price.numeric'=>'会员价格式错误',
				'postage_price.numeric'=>'邮费格式错误',
				'flower_price.integer'=>'鲜花数格式错误',
				'discount_flower_price.integer'=>'鲜花数格式错误',
				'member_flower_price.integer'=>'鲜花数格式错误',
				'flower_postage_price.integer'=>'鲜花数格式错误',
				'crowdfunding.numeric'=>'众筹金额格式错误',
				'diamond.integer'=>'钻石数为整数',
				'member_diamond.integer'=>'钻石数为整数',
				'description.required'=>'商品简介空'
			];
			$rs = Input::all();
			$rs['crowdfunding'] = isset($rs['crowdfunding']) ? $rs['crowdfunding'] : 0;
			$validator = Validator::make($rs,$rules,$message);
			if($validator->fails()){
				Input::flash();
				return View::make('admintianlaigoods.addtianlaigoods')->withErrors($validator)->withInput(Input::all())->with('data',$data);
			}
			if(isset($rs['_token'])){
				unset($rs['_token']);
			}
			if(isset($rs['goodspic'])){
				$rs['goodspic'] = trim($rs['goodspic'],'|');
				$pic_ids = [];
				if(!empty($rs['goodspic'])){
					$tmp_arr = explode('|', $rs['goodspic']);
					foreach($tmp_arr as $k=>$v){
						$tmp_pic_ids = explode('_', $v);
						$pic_ids[] = $tmp_pic_ids[1];
					}
				}
				unset($rs['goodspic']);
			}
			$file = Input::file('icon');
			if(!empty($rs['icon']) && !empty($file)){
				//上传商品缩略图
				$pic_rs = $this->adminTianLaiGoods->uploadGoodsImage($file,$this->upload_path);
				if(isset($pic_rs['url'])){
					$rs['icon'] = ltrim($pic_rs['url'],'.');
				}else{
					return Redirect::to('/admin/defaultError')->with('message',"图片上传失败");
				}
			}
			try {
				DB::beginTransaction();
				$goodsinfo = [
					'good_pid'=>0,
					'name'=>$rs['name'],
					'price'=>$rs['price'],
					'type'=>$rs['type'],
					'description'=>$rs['description'],
					'competition_id'=>0,
					'flag'=>$rs['category'],
					'start_time'=>0,
					'end_time'=>0,
					'discount_price'=>$rs['discount_price'],
				];
				DB::table('goods')->where('id',$rs['id'])->update($goodsinfo);
				$goodsParams = [
					'goodid'=>$rs['id'],
					'price'=>$rs['price'],
					'discount_price'=>$rs['discount_price'],
					'member_price'=>$rs['member_price'],
					'postage_price'=>$rs['postage_price'],
					'flower_price'=>$rs['flower_price'],
					'discount_flower_price'=>$rs['discount_flower_price'],
					'member_flower_price'=>$rs['member_flower_price'],
					'flower_postage_price'=>$rs['flower_postage_price'],
					'promptgoods'=>$rs['promptgoods'],
					'crowdfunding'=>$rs['crowdfunding'],
					'diamond'=>$rs['diamond'],
					'member_diamond'=>$rs['member_diamond'],
					'normal_section'=>$rs['normal_section'],
					'member_section'=>$rs['member_section'],
					'normal_price_section'=>trim($rs['normal_price_section']),
					'member_price_section'=>trim($rs['member_price_section']),
					'normal_flower_price_section'=>trim($rs['normal_flower_price_section']),
					'member_flower_price_section'=>trim($rs['member_flower_price_section']),
					'icon'=>$rs['icon'],
					'des_detail'=>$rs['des_detail'],

				];
				if(empty($rs['icon'])){
					unset($goodsParams['icon']);
				}
				DB::table('good_param')->where('goodid',$rs['id'])->update($goodsParams);
				//更新图片附件表状态
				if(!empty($pic_ids)){
					DB::table('goodspic')->whereIn('id',$pic_ids)->update(['goodid'=>$rs['id'],'is_del'=>0]);
				}
				//删除无效图片记录
				DB::table('goodspic')->where('goodid',0)->where('is_del',1)->delete();
				DB::commit();
			} catch (Exception $e) {
				return Redirect::to('/admin/defaultError')->with('message',"商品更新失败");
			}	
			return Redirect::to('/admin/tianLaiGoodsList');
		}
	}
	/**
	 * 天籁商城添加商品
	 * @author :wang.hongli
	 * @since :2016/08/03
	 */
	public function addTianLaiGoods(){
		//default icon
		$data['goodsCategory'] = $this->goodsCategory;
		$data['type'] = $this->unit_array;
		$method = Request::method();
		$errors = [];
		if('POST' == $method){
			$rules = [
				'name'=>'required',
				'price'=>'required|numeric',
				'discount_price'=>'numeric',
				'member_price'=>'numeric',
				'postage_price'=>'numeric',
				'flower_price'=>'integer',
				'discount_flower_price'=>'integer',
				'member_flower_price'=>'integer',
				'flower_postage_price'=>'integer',
				'crowdfunding'=>'numeric',
				'diamond'=>'integer',
				'member_diamond'=>'integer',
				'icon'=>'required|image',
				'description'=>'required',
			];
			$message = [
				'name.required'=>'商品名为空',
				'price.required'=>'填写商品价格',
				'price.numeric'=>'价格格式错误',
				'discount_price.numeric'=>'折扣价格错误',
				'member_price.numeric'=>'会员价格式错误',
				'postage_price.numeric'=>'邮费格式错误',
				'flower_price.integer'=>'鲜花数格式错误',
				'discount_flower_price.integer'=>'鲜花数格式错误',
				'member_flower_price.integer'=>'鲜花数格式错误',
				'flower_postage_price.integer'=>'鲜花数格式错误',
				'crowdfunding.numeric'=>'众筹金额格式错误',
				'diamond.integer'=>'钻石数为整数',
				'member_diamond.integer'=>'钻石数为整数',
				'icon.required'=>'商品图标为空',
				'icon.image'=>'商品图标格式错误',
				'description.required'=>'商品简介空'
			];
			$rs = Input::all();
			$rs['crowdfunding'] = isset($rs['crowdfunding']) ? $rs['crowdfunding'] : 0;
			$validator = Validator::make($rs,$rules,$message);
			if($validator->fails()){
				Input::flash();
				return View::make('admintianlaigoods.addtianlaigoods')->withErrors($validator)->withInput(Input::all())->with('data',$data);
			}
			if(isset($rs['_token'])){
				unset($rs['_token']);
			}
			if(isset($rs['goodspic'])){
				$rs['goodspic'] = trim($rs['goodspic'],'|');
				$pic_ids = [];
				if(!empty($rs['goodspic'])){
					$tmp_arr = explode('|', $rs['goodspic']);
					foreach($tmp_arr as $k=>$v){
						$tmp_pic_ids = explode('_', $v);
						$pic_ids[] = $tmp_pic_ids[1];
					}
				}
				unset($rs['goodspic']);
			}
			$file = Input::file('icon');
			//上传商品缩略图
			$pic_rs = $this->adminTianLaiGoods->uploadGoodsImage($file,$this->upload_path);
			if(isset($pic_rs['url'])){
				$rs['icon'] = ltrim($pic_rs['url'],'.');
			}else{
				return Redirect::to('/admin/defaultError')->with('message',"图片上传失败");
			}
			DB::beginTransaction();
			try {
				$goodsinfo = [
					'good_pid'=>0,
					'name'=>$rs['name'],
					'price'=>$rs['price'],
					'type'=>$rs['type'],
					'description'=>$rs['description'],
					'competition_id'=>0,
					'flag'=>$rs['category'],
					'start_time'=>0,
					'end_time'=>0,
					'discount_price'=>$rs['discount_price'],
				];
				$goodid = DB::table('goods')->insertGetId($goodsinfo);
				if(!$goodid){
					return Redirect::to('/admin/defaultError')->with('message',"商品添加失败");
				}
				$goodsParams = [
					'goodid'=>$goodid,
					'price'=>$rs['price'],
					'discount_price'=>$rs['discount_price'],
					'member_price'=>$rs['member_price'],
					'postage_price'=>$rs['postage_price'],
					'flower_price'=>$rs['flower_price'],
					'discount_flower_price'=>$rs['discount_flower_price'],
					'member_flower_price'=>$rs['member_flower_price'],
					'flower_postage_price'=>$rs['flower_postage_price'],
					'promptgoods'=>$rs['promptgoods'],
					'crowdfunding'=>$rs['crowdfunding'],
					'diamond'=>$rs['diamond'],
					'member_diamond'=>$rs['member_diamond'],
					'normal_section'=>trim($rs['normal_section'],'|'),
					'member_section'=>trim($rs['member_section'],'|'),
					'normal_price_section'=>trim($rs['normal_price_section']),
					'member_price_section'=>trim($rs['member_price_section']),
					'normal_flower_price_section'=>trim($rs['normal_flower_price_section']),
					'member_flower_price_section'=>trim($rs['member_flower_price_section']),
					'icon'=>$rs['icon'],
					'des_detail'=>$rs['des_detail']
				];
				$goodsParamsId = DB::table('good_param')->insertGetId($goodsParams);
				if(empty($goodsParamsId)){
					DB::rollback();
					return Redirect::to('/admin/defaultError')->with('message',"商品添加失败");
				}
				//更新图片附件表状态
				if(!empty($pic_ids)){
					$status = DB::table('goodspic')->whereIn('id',$pic_ids)->update(['goodid'=>$goodid,'is_del'=>0]);
					if(!$status){
						DB::rollback();
						return Redirect::to('/admin/defaultError')->with('message',"商品添加失败");
					}
				}
				//删除无效图片记录
				DB::table('goodspic')->where('goodid',0)->where('is_del',1)->delete();
				DB::commit();
				return Redirect::to('/admin/tianLaiGoodsList');
			} catch (Exception $e) {
				return Redirect::to('/admin/defaultError')->with('message',"商品添加失败");
			}	
			Input::flash();
			return View::make('admintianlaigoods.addtianlaigoods')->withInput(Input::all())->with('data',$data);
		}
		return View::make('admintianlaigoods.addtianlaigoods')->with('data',$data);
		
	}

	/**
	 * 天籁商城上传图片
	 * @author :wang.hongli
	 * @since :2016/08/05
	 */
	public function uploadGoodsImage(){
		$files = Input::file('inutdim1');
		if(empty($files)){
			echo json_encode(['error'=>'请上传图片']);
		}
		if(!empty($files)){
			$file = $files[0];
			$rs = $this->adminTianLaiGoods->uploadGoodsImage($file,$this->upload_path);
			if(isset($rs['url'])){
				//将图片放入商品图片附件表
				$rs['url'] = ltrim($rs['url'],'.');
				$id = $this->adminTianLaiGoods->insertToGoodsPic(['goodid'=>0,'url'=>$rs['url'],'is_del'=>1,'addtime'=>time()]);
				echo json_encode(['id'=>$id]);
			}else{
				echo json_encode($rs);
			}
		}
	}

	/**
	 * 天籁商城删除图片
	 * @author :wang.hongli
	 * @since :2016/08/11
	 */
	public function delGoodsImage(){
		$id = Input::get('id',0);
		$error_msg = ['error'=>'删除失败,请重试'];
		$success_msg = ['success'=>'删除成功'];
		if(empty($id)){
			echo json_encode($error_msg);
			return;
		}
		try {
			$flag = DB::table('goodspic')->where('id',$id)->delete();
			if($flag){
				echo json_encode($success_msg);
			}else{
				echo json_encode($error_msg);
			}
		} catch (Exception $e) {
			echo json_encode($error_msg);
		}
	}

	/**
	 * 天籁商城商品列表
	 * @author :wang.hongli
	 * @since :2016/08/03
	 */
	public function tianLaiGoodsList(){
		$pagesize = 10;
		$goodsCategory = [
			'0'=>'所有商品',
			'3'=>'会员增值商品',
			'4'=>'实体商品',
			'5'=>'出版物商品'
		];
		$isdel = [
			-1=>'全部',
			0=>'正常',
			1=>'下架'
		];
		$data['category'] = $goodsCategory;
		$data['isdel'] = $isdel;
		$data['promptgoods'] = $this->promptgoods;

		$search['category'] = intval(Input::get('category',0));
		$search['isdel'] = intval(Input::get('isdel',-1));
		$search['id'] = Input::get('id','');
		try {
			$conn = DB::table('goods')
				->leftjoin('good_param','goods.id','=','good_param.goodid')
				->select('goods.id','goods.name','goods.type','goods.description','flag','goods.buynum','goods.isdel','good_param.price','good_param.discount_price','good_param.member_price','good_param.postage_price','good_param.flower_price','good_param.discount_flower_price','good_param.member_flower_price','good_param.flower_postage_price','good_param.promptgoods','good_param.crowdfunding','good_param.diamond','good_param.member_diamond','good_param.normal_section','good_param.member_section','good_param.icon','good_param.crowdfundinged');

			if(!empty($search['category'])){
				$conn->where('goods.flag',$search['category']);
			}else{
				$conn->whereIn('goods.flag',[3,4,5]);
			}
			if($search['isdel'] != -1){
				$conn->where('goods.isdel',$search['isdel']);
			}
			if(!empty($search['id'])){
				$conn->where('goods.id',$search['id']);
			}
			$data['promptgoods'] = $this->promptgoods;
			$conn->orderBy('goods.id','desc');
			$data['list'] = $conn->paginate($pagesize);
		} catch (Exception $e) {
		}
		$url = Config::get('app.url');
		return View::make('admintianlaigoods.tianlaigoodslist')->with('data',$data)->with('search',$search)->with('url',$url);
	}

	/**
	 * 商品上架/下架
	 * @author :wang.hongli
	 * @since :2016/08/21
	 */
	public function publishOrDelTianLaiGoods($id=0){
		$method = Request::ajax();
		if(!$method || !$id){
			echo -1;
			return;
		}
		$isdel = intval(Input::get('isdel'));
		$flag = $this->adminTianLaiGoods->publishOrDelTianLaiGoods($id,$isdel);
		if($flag){
			echo 1^$isdel;
		}else{
			echo -1;
		}
	}
}

 ?>