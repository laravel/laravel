<?php 
/**
 * 后台广告管理--是关注听和新作抢先听列表广告
 * @author :wang.hongli
 * @since :2016/08/08
 */
class AdminAdvertising extends AdminCommon {

	private $pic_w;
	private $pic_h;
	function __construct(){
		parent::__construct();
		$this->pic_w = [1080];
		$this->pic_h = [308,138];
	}
	/**
	 * 广告位管理--添加广告栏目
	 * @author :wang.hongli
	 * @since :2016/08/08
	 */
	public function addAdvertisingColumn($data=[]){
		$id = DB::table('third_advertising_column')->insertGetId($data);
		if(empty($id)){
			return false;
		}else{
			return $id;
		}
	}

	/**
	 * 获取广告栏目列表
	 * @author :wang.hongli
	 * @since :2016/08/08
	 */
	public function getAdvertisingColumnList(){
		$list = DB::table('third_advertising_column')->get(['id','name','addtime']);
		if(empty($list)){
			$list = [];
		}
		return $list;
	}

	/**
	 * 添加第三方广告
	 * @author :wang.hongli
	 * @since :2016/08/08
	 */
	public function addThirdAdvising($data=[]){
		if(empty($data)){
			return false;
		}

	}

	/**
	 * 图片上传
	 * @author :wang.hongli
	 * @since :2016/08/09
	 * @param : $file -- 文件资源句柄
	 * @param : $path -- 文件上传路径
	 */
	public function uploadAdvPic($file='',$path='',$file_name='',$allowfile_type=['jpg','jpeg','gif','png']){
		if(empty($file) || empty($path)){
			return false;
		}
		$ext = $file->guessClientExtension();
		if(!in_array(strtolower($ext),$allowfile_type)){
			return false;
		}
		//判断图片的宽高
		$w_h = getimagesize($file);
		if(empty($w_h)){
			return false;
		}
		$w = $w_h[0];
		$h = $w_h[1];
		if(!in_array($w, $this->pic_w) || !in_array($h,$this->pic_h)){
			return 'error';
		}
		if(empty($file_name)){
			$file_name = time().uniqid().'.'.$ext;
		}
		try {
			$file->move($path,$file_name);
			return $path.$file_name;
		} catch (Exception $e) {
			return false;
		}
	}
}

 ?>