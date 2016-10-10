<?php 
/**
 * 个人主页显示列表
 * @author :wang.hongli
 * @since : [<2016/07/15>]
 */
class AdminPersonHome extends AdminCommon{

	/**
	 * @author:wang.hongli
	 * @since : [<2016/07/15>]
	 * @param : $[flag] [<0 自己的 1他人的>]
	 */
	public function getList($flag=0){
		$conn = DB::table('personal_homepage');
		$list = $conn->where('flag',$flag)->orderBy('id','asc')->get();
		if(empty($list)){
			$list  = [];
		}else{
			foreach($list as $k=>&$v){
				$v['icon'] = '/upload/homepageicon/'.$v['icon'];
			}
		}
		return $list;
	}

	/**
	 *添加个人主页列表
	 * @author :wang.hongli <[<email address>]>
	 *@since :2016/07/15 [<description>]
	 */
	public function addPersonHome($data=[]){
		if(empty($data)){
			return '添加失败';
		}
		$rules = [
			'name'=>'required|alpha_dash',
			'icon'=>'required|image',
			'sort'=>'required|integer',
			'flag'=>'required|in:0,1',
		];
		$message = [
			'name.required'=>'名称必填',
			'name.alpha_dash'=>'名称必填格式错误',
			'icon.required'=>'请上传图标',
			'icon.image'=>'图标格式错误',
			'sort.required'=>'请填写排序',
			'sort.integer'=>'排序必须为数字',
			'flag.required'=>'请选择分类',
			'flag.in'=>'分类不存在',
		];
		$validator = Validator::make ( $data, $rules,$message );
		if ($validator->fails ()) {
			$msg =  $validator->messages()->first();
			return $msg;
		}
		$filePath = public_path().'/upload/homepageicon/';
		//图片上传
		$file = Input::file('icon');
		$ext = $file->guessExtension();
		$imgName = time().uniqid();
		$imgName = $imgName.'.'.$ext;
		$lastFilePath = $filePath.$imgName;
		if($file->move($filePath,$imgName)){
			$data['icon'] = $imgName;
		}else{
			return '图片上传失败';
		}
		$data['addtime'] = time();
		if(!empty($data['category'])){
			$category = DB::table('personal_homepage')->where('status',0)->where('flag',$data['flag'])->where('id',$data['category'])->pluck('category');
			$data['category'] = $category;
		}else{
			$category = DB::table('personal_homepage')->where('status',0)->where('flag',$data['flag'])->orderBy('category','desc')->first();
			if(empty($category)){
				$data['category'] = 0;
			}else{
				$data['category'] = $category['category'] + 1;
			}
		}
		try {
			DB::table('personal_homepage')->insert($data);
		} catch (Exception $e) {
			return '添加图片失败';
		}
		return true;
	}
}
 ?>