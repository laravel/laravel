<?php 
/**
 * 第三方广告model
 * @author:wang.hongli
 * @since :2016/08/09
 */
class ApiThirdAdvertising extends ApiCommon {

	/**
	 * 获取广告列表
	 * @author:wang.hongli
	 * @since :2016/08/09
	 * @param : column_id:所属栏目id
	 */
	public function getThirdAdvList($column_id=0,$platform=0,$size=5,$time=0){
		if(empty($column_id)){
			return [];
		}
		$ad_type = DB::table('third_advertising')->where('column_id',$column_id)->where('platform',$platform)->where('is_del',0)->orderBy('weight','desc')->take(1)->pluck('ad_type');
		if($ad_type != 0 && empty($ad_type)){
			return [];
		}
		$list = DB::table('third_advertising')
			->where('is_del',0)
			->where('column_id',$column_id)
			->where('platform',$platform)
			->where('starttime','<=',$time)
			->where('endtime','>=',$time)
			->where('ad_type',$ad_type)
			->orderBy('weight','desd')
			->get(['id','name','description','ad_type','is_close','pic','weight','duration','url','type','argument','platform','column_id','starttime','endtime']);
		if(empty($list)){
			return [];
		}
		foreach($list as $k=>&$v){
			$v['pic'] = $this->poem_url.$v['pic'];
		}
		return $list;
	}
}
 ?>