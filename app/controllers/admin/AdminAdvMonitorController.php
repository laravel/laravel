<?php
/**
 * 广告监测链接统计
 */
class AdminAdvMonitorController extends BaseController{
	
	private $_config;
	private $_os;
	private $_adid;
	function __construct(){
		$this->_adid = array(
				'android'=>array(
						'android_1'=>'安卓-大图',
						'android_2'=>'安卓-小图',
						'android_3'=>'安卓广告-三',
				),
				'ios'=>array(
						'ios_1'=>'ios-大图',
						'ios_2'=>'ios-小图',
						'ios_3'=>'ios-广告三'
				)
		);
		$this->_os = array(0=>'安卓',1=>'IOS');
		$this->_sc = array(1=>'展现',2=>'点击');
	}
	/**
	 * 今日头条广告监测链接统计
	 * @author:wang.hongli
	 * @since:2016/06/12
	 */
	public function jr_adv_list(){
		$pagesize = 20;
		$search = array();
		$requestMethod = Request::method();
		//广告计划
		$adid = Input::has("adid") ? Input::get('adid') : 'android_1';
		//选中的系统
		$os = Input::has('os') ? intval(Input::get('os')) : 0;
		//统计方式 1展现２点击
		$sc = Input::has('cid') ? intval(Input::get('cid')) : 1;
		//开始时间
		$starttime = Input::has('starttime') ? Input::get('starttime') : '';
		$data['starttime'] = $starttime;
		$search['starttime'] = $starttime;
		//结束时间
		$endtime = Input::has('endtime') ? Input::get('endtime') : '';
		$data['endtime'] = $endtime;
		$search['endtime'] = $endtime;
		//结束时间
		$data['_adid'] = $this->_adid;
		$data['select_adid'] = $adid;
		$search['adid'] = $adid;
		$data['_os'] = $this->_os;
		$data['select_os'] = $os;
		$search['os'] = $os;
		$data['_sc'] = $this->_sc;
		$data['select_sc'] = $sc;
		$search['cid'] = $sc;
		switch($sc){
			case 1:
				$conn = DB::table('jinri_statistics_show');
				break;
			case 2:
				$conn = DB::table('jinri_statistics_click');
				break;
		}
		$conn->where('adid',$adid)->where('cid',$sc)->where('os',$os);
		if(!empty($starttime)){
			$st = strtotime($starttime);
			$conn->where('timestamp','>=',$st);
		}
		if(!empty($endtime)){
			$endt = strtotime($endtime)+86399;
			$conn->where('timestamp','<=',$endt);
		}
		$count = $conn->count();
		$count = !empty($count) ? $count : 0;
		$list = $conn->paginate($pagesize);
		//有效用户num
		$effect_user = DB::table('jinri_statistics_click')->where('status','<>',0)->count();
		return View::make('advmonitor.jr_adv_list')->with('data',$data)->with('list',$list)->with('count',$count)->with('search',$search)->with('effect_user',$effect_user);
	}
}