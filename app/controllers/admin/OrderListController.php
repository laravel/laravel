<?php 
/**
*	后台订单管理
*	@author:wang.hongli
*	@since:2015/05/07
**/
class OrderListController extends BaseController
{
	/**
	 * 获取订单列表
	 * @author:wang.hongli
	 * @since:2016/06/01
	 * @id 标示对应的逻辑 1 联合会会费 2,诵读比赛费 3,诗文比赛费 4,培训班活动费,5,打赏团队费
	 */
	public function orderList($id=0){
		//接收共同参数
		$pagesize = 20;
		//搜索条件拼接数组
		$search_arr = array();
		$com_user_info = array();
		//0支付中或者支付失败 1成功
		$data['status'] = Input::has('status') ? intval(Input::get('status')) : 2;
		$conn = DB::table('order_list');
		if($data['status']>-1){
			if($data['status'] != 2){
				$conn->where('status','<>',2);
			}else{
				$conn->where('status',2);
				$data['status'] = 2;
			}
			
		}
		//支付类型 1是银联2支付宝3支付宝网页版4财付通
		$data['pay_type']= Input::get('pay_type') ? intval(Input::get('pay_type')) : -1;
		if($data['pay_type']>-1){
			$conn->where('pay_type',$data['pay_type']);
		}
		// 平台类型 0 ios 1 android
		$data['plat_from'] = Input::has('plat_from') ? intval(Input::get('plat_from')) : -1;
		if($data['plat_from']>-1){
			$conn->where('plat_from',$data['plat_from']);
		}
		//商品id
		$data['goods_id'] = Input::get('goods_id') ? intval(Input::get('goods_id')) : -1;
		if($data['goods_id']>-1){
			$conn->where('goods_id',$data['goods_id']);
		}
		// 用户id
		$data['uid'] = Input::get('uid') ? (int)Input::get('uid') : '';
		if(!empty($data['uid'])){
			$conn->where('uid',$data['uid']);
		}
		//开始时间
		$input_startime = Input::get('starttime');
		$input_endtime = Input::get('endtime');
		$starttime = strtotime($input_startime);
		$endtime = strtotime($input_endtime);
		if($starttime && $endtime){
			$data['starttime'] = $input_startime;
			$data['endtime'] = $input_endtime;
			$conn->where('addtime','>=',$starttime);
			$conn->where('addtime','<=',$endtime+86399);
		}else{
			$data['starttime'] = 0;
			$data['endtime'] = 0;
		}
		//province_id
		$data['province_id'] = Input::has('province_id') ? Input::get('province_id') : 0;
		if(!empty(Input::get('city_id')) && Input::get('city_id')!= '全部'){
			$data['city_id'] = Input::get('city_id');
		}else{
			$data['city_id'] = 0;
		}
		if(!empty(Input::get('area_id')) && Input::get('area_id') != '全部'){
			$data['area_id'] = Input::get('area_id');
		}else{
			$data['area_id'] = 0;
		}
		$data['age']  = 0;
		if(!empty(Input::get('age'))){
			$data['age'] = Input::get('age');
		}
		switch($id){
			case 1:
				$o_con = DB::table('league');
				if(!empty($data['province_id'])){
					$o_con->where('province_id',$data['province_id']);
				}
				if(!empty($data['age'])){
					$o_con->where('age','<',16)->where('age','>',0);
				}
				break;
			case 2:
			case 3:
				$o_con = DB::table('user_match');
				if(!empty($data['province_id'])){
					$o_con->where('province_id',$data['province_id']);
				}
				if(!empty($data['age'])){
					$o_con->where('age','<',16)->where('age','>',0);
				}
				break;
			case 4:
				$o_con = DB::table('class_active_form');
				if(!empty($data['province_id'])){
					$o_con->where('province_id',$data['province_id']);
				}
				if(!empty($data['age'])){
					$o_con->where('age','<',16)->where('age','>',0);
				}
				break;
		}
		if(!empty($o_con) && in_array($id,array(1,2,3,4))){
			if(!empty($data['city_id']) && $data['city_id'] != '全部'){
				$o_con->where('city_id',$data['city_id']);
			}
			if(!empty($data['area_id']) && $data['area_id'] != '全部'){
				$o_con->where('area_id',$data['area_id']);
			}
			$search_uid = $o_con->lists('uid');
		}
		$adminOrder = new AdminOrder();
		//1 联合会会费 2,诵读比赛费 3,诗文比赛费 4,培训班活动费,5,打赏团队费
		$return = $adminOrder->orderList($id,$conn);
		$conn = $return['conn'];
		$goods_id = $return['goods_id'];
		$data['total_money'] = !empty($return['total_money']) ? round($return['total_money'],2) : 0;
		
		if(!empty($search_uid)){
			$conn->whereIn('uid',$search_uid);
		}
		$data['total']=$conn->count();
		//如果export_flag说明是导出excel
		if(!empty(Input::get('export_flag'))){
			$this->exportXls($id,$conn, $data,$goods_id);
			die;
		}
		$orderList = $conn->orderBy('id','desc')->paginate($pagesize);
		$tmp_uids = array();
		$user_info = array();
		foreach($orderList as $k=>$v){
			$tmp_uids[] = $v['uid'];
		}
		if(!empty($tmp_uids)){
			$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uids)->select('id','gender','nick')->get();
			if(!empty($tmp_user_info)){
				foreach($tmp_user_info as $key=>$value){
					$user_info[$value['id']] = $value;
				}
			}
			switch($id){
				case 1:
					$tmp_com_user_info = DB::table('league')->whereIn('uid',$tmp_uids)->get();
					break;
				case 2:
					$tmp_com_user_info = DB::table('user_match')->whereIn('uid',$tmp_uids)->get();
					break;
				case 3:
					$tmp_com_user_info = DB::table('user_match')->whereIn('uid',$tmp_uids)->get();
					break;
				case 4:
					$tmp_com_user_info = DB::table('class_active_form')->whereIn('uid',$tmp_uids)->get();
					break;
			}
		}
		if(!empty($tmp_com_user_info)){
			foreach($tmp_com_user_info as $k=>$v){
				$com_user_info[$v['uid']] = $v;
			}
		}
		$data['allprovince']=ApiCity::getAllProvince();
		$data['allcity']=ApiCity::getAllCity();
		$data['allarea']=ApiCity::getAllArea();

		$data['com_user_info'] = $com_user_info;
		$data['orderlist'] = $orderList;
		$data['user_info'] = $user_info;
		//所有的商品
		$all_goods=array();
		$rlt = DB::table('goods')->select('id','name')->whereIn('id',$goods_id)->orderBy('id','desc')->get();
		foreach($rlt as $v){
			$all_goods[$v['id']]=$v['name'];
		}
		$data['all_goods']=$all_goods;
		$data['id'] = $id;
		return View::make('orderlist.orderlist',$data);
	}
	/**
	 * 导出excel
	 * @author:wang.hongli
	 * @since:2016/06/02
	 */
	public function exportXls($id,$conn,$data,$goods_id){
		$all_status=array(0=>'支付失败',1=>'支付失败',2=>'支付成功');
		$all_pay=array(1=>'银联',2=>'支付宝',3=>'支付宝网页',4=>'微信');
		$all_from=array(0=>'IOS平台',1=>'安卓平台');
		
		$allprovince=ApiCity::getAllProvince();
		$allcity=ApiCity::getAllCity();
		$allarea=ApiCity::getAllArea();

		$orderList = $conn->orderBy('id','desc')->get();
		$tmp_uids = array();
		$user_info = array();
		$comm_user_info = array();	
		foreach($orderList as $k=>$v){
			$tmp_uids[] = $v['uid'];
		}
		if(!empty($tmp_uids)){
			$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uids)->select('id','gender','nick')->get();
			if(!empty($tmp_user_info)){
				foreach($tmp_user_info as $key=>$value){
					$user_info[$value['id']] = $value;
				}
			}
		}
		$data['orderlist'] = $orderList;
		$data['user_info'] = $user_info;
		//所有的商品
		$all_goods=array();
		$rlt = DB::table('goods')->select('id','name')->whereIn('id',$goods_id)->get();
		foreach($rlt as $v){
			$all_goods[$v['id']]=$v['name'];
		}
		$data['all_goods']=$all_goods;
		$data['id'] = $id;
		switch($data['id']){
			case 1:
				$tmp_com_user_info = DB::table('league')->whereIn('uid',$tmp_uids)->get();
				break;
			case 2:
				$tmp_com_user_info = DB::table('user_match')->whereIn('uid',$tmp_uids)->get();
				break;
			case 3:
				$tmp_com_user_info = DB::table('user_match')->whereIn('uid',$tmp_uids)->get();
				break;
			case 4:
				$tmp_com_user_info = DB::table('class_active_form')->whereIn('uid',$tmp_uids)->get();
				break;
			default:
				$tmp_com_user_info = [];
		}
		$com_user_info = [];
		if(!empty($tmp_com_user_info)){
			foreach($tmp_com_user_info as $k=>$v){
				$com_user_info[$v['uid']] = $v;
			}
		}
		if(!empty($data['orderlist'])){
			foreach($data['orderlist'] as $v){
				$tmp_info = !empty($com_user_info[$v['uid']]) ? $com_user_info[$v['uid']] : [];
				$province_id = !empty($com_user_info[$v['uid']]['province_id']) ? $com_user_info[$v['uid']]['province_id'] : '';
				$city_id = !empty($com_user_info[$v['uid']]['city_id']) ? $com_user_info[$v['uid']]['city_id'] : '';
				$area_id = !empty($com_user_fino[$v['uid']]['area_id']) ? $com_user_info[$v['uid']]['area_id'] :'';
				$tmp_province = !empty($allprovince[$province_id]) ? $allprovince[$province_id] : '';
				$tmp_city = !empty($allcity[$province_id][$city_id]) ? $allcity[$province_id][$city_id] : '';
				$tmp_area = !empty($allarea[$city_id][$area_id]) ? $allarea[$city_id][$area_id] : '';
				$tmp_address = !empty($tmp_info['address']) ? $tmp_info['address'] : '';
				$address = '省: '.$tmp_province.' 市: '.$tmp_city.' 区: '.$tmp_area.' 详细地址: '.$tmp_address;
				$tmp=array();
				$tmp[]=$v['id'];
				$tmp[]=$v['orderid'];
				$tmp[]=$v['uid'];
				$tmp[]= isset($data['user_info'][$v['uid']]['nick']) ? $data['user_info'][$v['uid']]['nick'] : '';
				$tmp[] = isset($tmp_info['name']) ? $tmp_info['name'] : '';
				$tmp[] = isset($tmp_info['birthday']) ? date('Y-m-d',$tmp_info['birthday']) : '';
				$tmp[] = isset($tmp_info['age']) ? $tmp_info['age'] : 0;
				$tmp[] = !empty($tmp_info['gender']) ? 'man' : 'woman';
				$tmp[] = !empty($tmp_info['card']) ? $tmp_info['card'] : 0;
				$tmp[] = $address;
				$tmp[] = !empty($tmp_info['zip']) ? $tmp_info['zip'] : '';
				$tmp[] = !empty($tmp_info['mobile']) ? $tmp_info['mobile']:'';
				$tmp[] = !empty($tmp_info['email']) ? $tmp_info['email']:'';
				$tmp[]=$v['goods_id'];
				$tmp[]=$v['price'];
				$tmp[]=$v['num'];
				$tmp[]=$v['total_price'];
				$tmp[]=$all_pay[$v['pay_type']];
				$tmp[]=$v['description'];
				$tmp[]=$all_status[$v['status']];
				$tmp[]=date("Y-m-d H:i",$v['addtime']);
				$tmp[]=date("Y-m-d H:i",$v['updatetime']);
				$tmp[]=$all_from[$v['plat_from']];
				$excel_data[]=$tmp;
			}
		}else{
			echo "无数据";
			exit();
		}
		//==========================================
		require_once app_path().'/ext/PHPExcel.php';
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('sheet1');
		//设置第一行内容
		$sheetTitle=array('ID','订单号','用户ID','用户昵称','真实姓名','生日','年龄','性别','身份证号','省市区详细地址','邮编','手机号','电子邮件','商品id','价格','数量','总计','支付类型','说明','支付状态','支付时间','修改时间','平台类型');
		// $sheetTitle=array('id','用户ID','用户昵称','真实姓名','生日','年龄','性别','身份证号','省市区详细地址','邮编','手机号','电子邮件','提交时间');
		$cNum=0;
		foreach($sheetTitle as $val){
			$sheet->setCellValueByColumnAndRow($cNum,1,$val);
			$cNum++;
		}
		
		$rNum=2;
		foreach($excel_data as $val){
			$cNum=0;
			foreach($val as $row){
				$sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
				$cNum++;
			}
			$rNum++;
		}
		$outputFileName = "orderlist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		$excel_url = Config::get('app.url').'/upload/'.$outputFileName;
		echo "<a href='$excel_url'>下载</a>";
	}
}
 ?>