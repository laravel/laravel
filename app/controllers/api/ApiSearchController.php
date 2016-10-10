<?php 
/**
*	搜索控制器
**/
class ApiSearchController extends ApiCommonController {

	private $apiSearch;
	function __construct(){
		$this->apiSearch = new ApiSearch();
	}
	/**
	 * 搜索接口
	 * @author:wang.hongli
	 * @since:2016/06/01
	 * @flag 
	 */
	public function search() {
		$rs = $this->apiSearch->search();
		if(is_array($rs)) {
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		} else {
			$this->setReturn(1,'success',array());
		}
	}
	
	/*
	* 获取省份
	* $type = 0省份 1城市 2县区
	* $id 省份id或者城市id
	*/
	public function getCity(){
		$data=array();
		$id = (int)Input::get('id');
		$type = (int)Input::get('type');
		switch ($type) {
			case 1:
				$rlt = DB::table('sx_city')->select('id','name')->where('province_id',$id)->orderBy('id','asc')->get();
				break;
			case 2:
				$rlt = DB::table('sx_area')->select('id','name')->where('city_id',$id)->orderBy('id','asc')->get();
				break;
			default:
				$rlt = DB::table('sx_province')->select('id','name')->orderBy('id','asc')->get();
				break;
		}
		if(!empty($rlt)){
			foreach($rlt as $v){
				$data[]=array("id"=>$v["id"],"name"=>$v["name"]);
			}
		}
		
		$this->setReturn(1,'success',$data);
	}
	
}