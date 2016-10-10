<?php 
/**
 * 后台天籁商城商品models
 * @author :wang.hongli
 * @since :2016/08/03
 */
class AdminTianLaiGoods extends AdminCommon {

	
	/**
	 * 天籁商城上传图片
	 * @author :wang.hongli
	 * @since :2016/08/05
	 */
	public function uploadGoodsImage($file='',$path='',$file_name='',$allow_type=['jpg','jpeg','gif','png']){
		if(empty($file)){
			return ['error'=>'图片上传失败'];
		}
		$ext = $file->guessClientExtension();
		if(!in_array($ext,$allow_type)){
			return ['error'=>'图片类型错误'];
		}
		if(empty($file_name)){
			$file_name = time().uniqid().'.'.$ext;
		}
		try {
			$file->move($path,$file_name);
			return ['url'=>$path.$file_name];
		} catch (Exception $e) {
			return ['error'=>'上传失败，请重试'];
		}
	}

	public function insertToGoodsPic($data=[]){
		try {
			$id = DB::table('goodspic')->insertGetId($data);
			return $id;
		} catch (Exception $e) {
			return false;	
		}
	}

	/**
	 * 商品上架/下架
	 * @author :wang.hongli
	 * @since :2016/08/21
	 */
	public function publishOrDelTianLaiGoods($id,$isdel){
		$isdel = 1^$isdel ? 1:0;
		try {
			$flag = DB::table('goods')->where('id',$id)->update(['isdel'=>$isdel]);
		} catch (Exception $e) {
			$flag = 0;	
		}
		return $flag;
	}

}

 ?>	