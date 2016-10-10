<?php
/**
*将用户放入结束活动列表
*@author:wang.hongli
*@since:2016/07/06
**/
class AdminFinishCompetitionController extends BaseController{

	private $adminFinishCompetition;
	public function __construct(){
		// parent::__construct();
		$this->adminFinishCompetition = new AdminFinishCompetition();
	}

	public function addUserToFinishCompetition(){
		$page_size = 50;
		$competitionlist_info = $this->adminFinishCompetition->getFinishCompetitionList();
		$competitionlist = $competitionlist_info['competitionlist'];
		$max=max(array_keys($competitionlist ['诵读比赛']));

		$competitionid_name = $competitionlist_info['competitionid_name'];
		$method = Request::method();
		$search = [];
		$competitionid = Input::has('competitionid') ? intval(Input::get('competitionid')) :$max;
		$good=DB::table("goods")->where('competition_id','=',$competitionid)->where('flag','=',0)->first();
		//$com_opus_rel_conn = DB::table('competition_opus_rel');
		
		$permission_search = Input::has('permission') ? intval(Input::get('permission')) :"";
		$search['permission']  = $permission_search;	
		$plat = Input::has('plat') ? intval(Input::get('plat')) :"0";
		$search['plat']  = $plat;	
		
		$com_opus_rel_conn = DB::table('order_list');

		if($plat==1){
			$com_opus_rel_conn->where('plat_from','=',1);
		}
		if($plat==2){
			$com_opus_rel_conn->where('plat_from','=',0);
		}

		//筛选

		$name = Input::has('name') ? trim(Input::get('name')) :"";
		 
		$search['name']  = $name;	

		$tmp_permission = DB::table('finish_comp_user')->where('competitionid',$competitionid)->get();
			$permission = [];
			if(!empty($tmp_permission)){
				foreach($tmp_permission as $v){
					$permission[] = $v['uid']; 
				}
			}


		$search['competitionid']  = $competitionid;	

		if( $competitionid!=0){
			//$com_opus_rel_conn->where('competition_opus_rel.competitionid',$search['competitionid']);
			$com_opus_rel_conn->where('order_list.goods_id','=',$good['id']);
		}

		if($permission_search==1 && $permission){
				$com_opus_rel_conn->whereIn('order_list.uid',$permission);
		}elseif($permission_search==1 && !$permission){
			$com_opus_rel_conn->whereIn('order_list.uid',array(0));
		}
			if($permission_search==2 && $permission){
				$com_opus_rel_conn->whereNotIn('order_list.uid',$permission);
		}elseif($permission_search==2 && !$permission){
			$com_opus_rel_conn->whereNotIn('order_list.uid',array(0));
		}
	
		$nick = Input::has('nick') ? trim(Input::get('nick')) : '';
		$search['nick'] = $nick;
		$uids = [];
		if(!empty($nick)){
			$search['nick'] = $nick;
			$a = DB::table('user_match')->where('nick_name',$nick)->lists('uid');
			array_unshift($uids,$a[0]);
		}
		if(!empty($name)){
			$search['name'] = $name;
			$b = DB::table('user_match')->where('name',$name)->lists('uid');
				array_unshift($uids,$b[0]);
		}
		//检索省份
		$province_id=Input::has('province_id')?intval(Input::get('province_id')):0;
		$search['province_id']=$province_id; 	
		$city_id=Input::has('city_id')?intval(Input::get('city_id')):0;
		$search['city_id']=$city_id; 	
		$area_id=Input::has('area_id')?intval(Input::get('area_id')):0;
		$search['area_id']=$area_id; 

		$com_opus_rel_conn->leftJoin('user_match', 'order_list.uid', '=', 'user_match.uid');	
		 
		//根据省市县筛选
		if($province_id!=0){
			$com_opus_rel_conn->where('user_match.province_id','=',$province_id);
		}
		if($city_id!=0){
			$com_opus_rel_conn->where('user_match.city_id','=',$city_id);
		}
		if($area_id!=0){
			$com_opus_rel_conn->where('user_match.area_id','=',$area_id);
		}
		$uid = Input::has('uid') ? intval(Input::get('uid')) : '';
		$search['uid'] = $uid;
		if(!empty($uid)){
			array_push($uids,$uid);
		}
		if(!empty($uids)){
			$com_opus_rel_conn->whereIn('order_list.uid',$uids);
		}
		$com_opus_rel_conn->where('order_list.status','=',2)->orderBy("order_list.id",'desc');
		$rs = $com_opus_rel_conn->paginate($page_size);
		$last_uid = [];
		$plat_from = []; 
		if(!empty($rs)){
			foreach($rs as $key=>$value){
			
				$last_uid[] = $value['uid'];
				$plat_from[$value['uid']]=$value['plat_from']?'安卓':'IOS';

			}
		}
	 
		$all_province = ApiCity::getAllProvince();
		$all_city = ApiCity::getAllCity();
		$all_area = ApiCity::getAllArea();
		//get user_match
		$user_match = [];
		$user_info_con = DB::table('user_match');
		//夏青杯特殊处理
		if($competitionid == 22){
			$user_info_con = DB::table('summercup');
		}
		if(!empty($last_uid)){
			$tmp_user_match = $user_info_con->whereIn('uid',$last_uid)->get();
			$tmp_user_info = DB::table('user')->whereIn('id',$last_uid)->get(array('id','gender'));
		
			$user_info  = [];
			if(!empty($tmp_user_info)){
				foreach($tmp_user_info as $k=>$v){
					$user_info[$v['id']] = $v['gender'] == 1 ? '男' : '女';
				}
			}

			if(!empty($tmp_user_match)){
				foreach($tmp_user_match as $k=>$v){
					//所有
				
						$v['provice_name'] = !empty($all_province[$v['province_id']]) ? $all_province[$v['province_id']] : '';
						$v['city_name'] = !empty($all_city[$v['province_id']][$v['city_id']]) ? $all_city[$v['province_id']][$v['city_id']] : '';
						$v['area_name'] = !empty($all_area[$v['city_id']][$v['area_id']]) ? $all_area[$v['city_id']][$v['area_id']] : '';
						$v['competition_name'] = !empty($competitionid_name[$competitionid]) ? $competitionid_name[$competitionid] : '';
						$v['gender'] = $user_info[$v['uid']];
						$v['permission'] = in_array($v['uid'], $permission) ? 1 : 0;
						$user_match[$v['uid']] = $v;
			
			}}
		 
		}
		//获取所有省份
		$data['allprovince'] = ApiCity::getAllProvince();
		//获取所有城市
		$data['allcity'] = ApiCity::getAllCity();
		//获取所有地区
		$data['allarea'] = ApiCity::getAllArea();
		$data['allprovince'][0] = '全部';
		$search['province_id'] = Input::has('province_id') ? intval(Input::get('province_id')) : 0;
		 $data['city'][0] = '全部';
		if(!empty($search['province_id'])){
		
			$data['city']= $data['allcity'][$search['province_id']];
				$data['city'][0] = '全部';
		}
		
		//市
		 $data['area'][0] ='全部';
		if(!empty($search['city_id'])){
			
			$data['area']= $data['allarea'][$search['city_id']];
			$data['area'][0] ='全部';
		}
		 $permission=array(0=>'所有名单',1=>"获得资格",2=>'暂无资格');
		 $plat=array('0'=>"全部平台",'1'=>'安卓用户','2'=>'苹果用户');
		return View::make('adminfinishcompetition.addUserToFinishCompetition')->with('plat',$plat)->with('plat_from',$plat_from)->with('permission',$permission)->with('competitionid',$competitionid)->with('competitionlist',$competitionlist)->with('search',$search)->with('rs',$rs)->with('user_match',$user_match)->with('data',$data);
	}
	
	/**
	*将用户添加到总决赛
	*@author:wang.hongli
	*@since:2016/07/07
	**/
	public function modifyComAuth(){
		$error_msg = '设置错误';
		$isajax = Request::ajax();
		if(!$isajax){
			echo $error_msg;
			return;
		}
		$competitionid = Input::has('competitionid') ? intval(Input::get("competitionid")) : 0;
		$uid = Input::has('uid') ? intval(Input::get('uid')) : 0;
		$flag = Input::has('flag') ? intval(Input::get('flag')) : 0;
		if(empty($uid) || empty($competitionid)){
			echo $error_msg;
			return;
		}
		// 0 delete 1 add 
		try {
			switch ($flag) {
				case 0:
					DB::table('finish_comp_user')->where('uid',$uid)->where('competitionid',$competitionid)->delete();
					break;
				case 1:
					$type = DB::table('finish_comp_user')->where('uid',$uid)->where('competitionid',$competitionid)->pluck('id');
					if(empty($type)){
						$data = ['uid'=>$uid,'competitionid'=>$competitionid,'addtime'=>time(),'flag'=>1];
						DB::table('finish_comp_user')->insert($data);
					}
					break;
			}
			echo 1;
			return;
		} catch (Exception $e) {
			echo $error_msg;
		}
		
	}
	/*
	*将参加决赛的用户显示出来	
	*@author:hgz
	*@since:2016/07/14
	*/
	public function listFinishCompetition(){
		$page_size = 10 ;
		$competitionlist_info = $this->adminFinishCompetition->getFinishCompetitionList();
		$competitionlist = $competitionlist_info['competitionlist'];
		$competitionid_name = $competitionlist_info['competitionid_name'];
		$max=max(array_keys($competitionlist ['诵读比赛']));
		$competitionid = Input::has('competitionid') ? intval(Input::get('competitionid')) :$max;
		$search['competitionid']  = $competitionid;	
		//0 没筛选    1有光盘   2 无光盘
		$CD = Input::has('CD') ? intval(Input::get('CD')) :0;
		$search['CD']  = $CD;


		$plat_from = Input::has('plat_from') ? intval(Input::get('plat_from')) :0;
		$search['plat_from']  = $plat_from;
		$pay_type = Input::has('pay_type') ? intval(Input::get('pay_type')) :0;
		$search['pay_type']  = $pay_type;

		$nick = Input::has('nick_name') ? trim(Input::get('nick_name')) : '';
		$search['nick_name'] = $nick;

		$name = Input::has('name') ? trim(Input::get('name')) : '';
		$search['name'] = $name;

		$sdate = Input::has('sdate') ? trim(Input::get('sdate')) : '';
		$search['sdate'] = $sdate;

		$edate = Input::has('edate') ? trim(Input::get('edate')) : '';
		$search['edate'] = $edate;


		$uid = Input::has('uid') ? Input::get('uid') : '';
		$search['uid'] = $uid;
		$where_id=[];
		if($uid){
			array_unshift($where_id,$uid);
		}
		if(!empty($nick)){
			$aa = DB::table('user')->where('nick',$nick)->lists('id');
			array_unshift($where_id,$aa[0]);
		}
			if(!empty($name)){
			$bb = DB::table('user')->where('nick',$name)->lists('id');
			array_unshift($where_id,$bb[0]);
		}
		//商品
 	 
		$goodid=DB::table('goods')->where('competition_id','=',$competitionid)->where('flag','=','2')->get();
		//审核名单
		$namelist=DB::table('finish_comp_user')->where('competitionid','=',$competitionid);
		if(!empty($where_id)){
			$namelist->whereIn('uid',$where_id);
		}
		//筛选名单(全部)
		$name_list=$namelist->get();
			$last_uid = [];
		if(!empty($name_list)){
			foreach($name_list as $key=>$value){
				$last_uid[] = $value['uid'];
			}
		
		}
		if($last_uid){
			$tmp_user_info = DB::table('user')->whereIn('id',$last_uid)->get(array('id','gender'));
		}
	

 
		$uid_array=array();
		foreach ($name_list as $key => $value) {
				$uid[$key]=$value['uid'];
		}
		$all_price=0;
		$cd_price=0;
		$list_info="";
		if($goodid){
			$list=DB::table('order_list') ;
			$list_all=DB::table('order_list') ;

			if($uid_array){
				$list->whereIn('uid',$uid_array);
				$list_all->whereIn('uid',$uid_array);
			}
			if($plat_from==1){
				$list->where('plat_from','=',1);
				$list_all->where('plat_from','=',1);
			}
			if($plat_from==2){
				$list->where('plat_from','=',0);
				$list_all->where('plat_from','=',0);
			}


		if($search['pay_type']){
			switch($search['pay_type']){
				case  $search['pay_type']==1:
				$list->where('pay_type','=',1);
				$list_all->where('pay_type','=',1);
				break; 
				case  $search['pay_type']==2:
				$list->where('pay_type','=',2);
				$list_all->where('pay_type','=',2);
				 break; 
				case  $search['pay_type']==3:
				$list->where('pay_type','=',3);
				$list_all->where('pay_type','=',3);
				break; 
				case  $search['pay_type']==4:
				$list->where('pay_type','=',4);
				$list_all->where('pay_type','=',4);
				break; 				
				}
		}
		if($search['sdate']){
				$starttime=strtotime($search['sdate']);
				$list->where('updatetime','>=',$starttime);
				$list_all->where('updatetime','>=',$starttime);
		}  
		if($search['edate']){
			$endtime=strtotime($search['edate']);
			$list->where('updatetime','<=',$endtime+24*3600-1);
			$list_all->where('updatetime','<=',$endtime+24*3600-1);
		 
		}  
	 
			//默认 或者无下属商品
		if(count($goodid)==1 || $CD==0){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->orderBy("updatetime",'desc');
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->orderBy("updatetime",'desc');
		}
		//有
		if(count($goodid)==2  && $CD==1){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','=',$goodid['1']['id'])->orderBy("updatetime",'desc');	
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','=',$goodid['1']['id'])->orderBy("updatetime",'desc');	
		}
		//无光盘
		if(count($goodid)==2  && $CD==2){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','!=',$goodid['1']['id'])->orderBy("updatetime",'desc');
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','!=',$goodid['1']['id'])->orderBy("updatetime",'desc');
		}
	
			$rs=$list->paginate($page_size);
			foreach ($rs as $key => $value) {
				$list_info[$key]['order']=$value;
				if(count($goodid)==2){
					$list_info[$key]['order']['good_name']=$goodid[1]['name'];
					$list_info[$key]['order']['good_id']=$goodid[1]['id'];
				}	
				if($competitionid !=22){
				$list_info[$key] ['user']=DB::table('user_match')->where('uid','=',$value['uid'])->first();
				}else{
					$list_info[$key]['user']=DB::table('summercup')->where('uid','=',$value['uid'])->first();
				}	 
				$flag=DB::table('user_competition_flag')->where('uid','=',$value['uid'])->first();
				if($flag){
					$list_info[$key]['flag']=1;
				}else{
					$list_info[$key]['flag']=0;
				}
			}
		$all=$list_all->get();
		foreach ($all as $key => $value) {
				$all_price += $value['price'];
				if(count($goodid)==2 && $value['attach_id'] == $goodid[1]["id"] ){
					$cd_price +=$value["attach_price"];
				}
		}

		}else{
			$rs="";
		}
 	$competitionname=$competitionid_name[$competitionid];
 
 	//获取所有省份
		$data['allprovince'] = ApiCity::getAllProvince();
		//获取所有城市
		$data['allcity'] = ApiCity::getAllCity();
		//获取所有地区
		$data['allarea'] = ApiCity::getAllArea();
 
		
		//市
 		$plat_from=array('0'=>"全部平台",'1'=>'安卓用户','2'=>'苹果用户');
 		$CD=array(0=>'全部名单',1=>"购买光盘",2=>'未买光盘');
		$pay_type=array(0=>'全部方式',1=>"银联付款",2=>'支付宝',3=>'支付宝网页版',4=>'财付通');
		$url=Config::get('app.url');


 
		return View::make('adminfinishcompetition.listFinishCompetition')->with('url',$url)->with('all_price',$all_price)->with('cd_price',$cd_price)->with('CD',$CD)->with('competitionlist',$competitionlist)->with('competitionid',$competitionid)->with('search',$search)->with('competitionname',$competitionname)->with('info1',$list_info)->with("pay_type",$pay_type)->with("plat_from",$plat_from)->with('name',$name_list)->with('rs',$rs)->with('data',$data);
	}
	/**
	*审核决赛用户
	*@author:wang.hongli
	*@since:2016/07/07
	**/
	public function modifyFinalFlag(){
		$error_msg = '设置错误';
		$isajax = Request::ajax();
		if(!$isajax){
			echo $error_msg;
			return;
		}
		//比赛id
		$competitionid = Input::has('competitionid') ? intval(Input::get("competitionid")) : 0;
		//用户id
		$uid = Input::has('uid') ? intval(Input::get('uid')) : 0;
		$flag = Input::has('flag') ? intval(Input::get('flag')) : 0;
		if(empty($uid) || empty($competitionid)){
			echo $error_msg;
			return;
		}
		// 0 delete 1 add 
		try {
			switch ($flag) {
				case 0:
					DB::table('user_competition_flag')->where('uid',$uid)->where('competitionid',$competitionid)->delete();
					break;
				case 1:
					$type = DB::table('user_competition_flag')->where('uid',$uid)->where('competitionid',$competitionid)->pluck('id');
					if(empty($type)){
						$data = ['uid'=>$uid,'competitionid'=>$competitionid,'flag'=>1];
						DB::table('user_competition_flag')->insert($data);
					}
					break;
			}
			echo 1;
			return;
		} catch (Exception $e) {
			echo $error_msg;
		}
		
	}
	public function listFinishdown(){
		$page_size = 10 ;
		$competitionlist_info = $this->adminFinishCompetition->getFinishCompetitionList();
		$competitionlist = $competitionlist_info['competitionlist'];
		$competitionid_name = $competitionlist_info['competitionid_name'];
		$max=max(array_keys($competitionlist ['诵读比赛']));
		$competitionid = Input::has('competitionid') ? intval(Input::get('competitionid')) :$max;
		$search['competitionid']  = $competitionid;	
		//0 没筛选    1有光盘   2 无光盘
		$CD = Input::has('CD') ? intval(Input::get('CD')) :0;
		$search['CD']  = $CD;


		$plat_from = Input::has('plat_from') ? intval(Input::get('plat_from')) :0;
		$search['plat_from']  = $plat_from;
		$pay_type = Input::has('pay_type') ? intval(Input::get('pay_type')) :0;
		$search['pay_type']  = $pay_type;

		$nick = Input::has('nick_name') ? trim(Input::get('nick_name')) : '';
		$search['nick_name'] = $nick;

		$name = Input::has('name') ? trim(Input::get('name')) : '';
		$search['name'] = $name;

		$sdate = Input::has('sdate') ? trim(Input::get('sdate')) : '';
		$search['sdate'] = $sdate;

		$edate = Input::has('edate') ? trim(Input::get('edate')) : '';
		$search['edate'] = $edate;


		$uid = Input::has('uid') ? Input::get('uid') : '';
		$search['uid'] = $uid;
		$where_id=[];
		if($uid){
			array_unshift($where_id,$uid);
		}
		if(!empty($nick)){
			$aa = DB::table('user')->where('nick',$nick)->lists('id');
			array_unshift($where_id,$aa[0]);
		}
			if(!empty($name)){
			$bb = DB::table('user')->where('nick',$name)->lists('id');
			array_unshift($where_id,$bb[0]);
		}
		//商品
 	 
		$goodid=DB::table('goods')->where('competition_id','=',$competitionid)->where('flag','=','2')->get();
		//审核名单
		$namelist=DB::table('finish_comp_user')->where('competitionid','=',$competitionid);
		if(!empty($where_id)){
			$namelist->whereIn('uid',$where_id);
		}
		//筛选名单(全部)
		$name_list=$namelist->get();
			$last_uid = [];
		if(!empty($name_list)){
			foreach($name_list as $key=>$value){
				$last_uid[] = $value['uid'];
			}
		
		}
		if($last_uid){
			$tmp_user_info = DB::table('user')->whereIn('id',$last_uid)->get(array('id','gender'));
		} 
		$uid_array=array();
		foreach ($name_list as $key => $value) {
				$uid[$key]=$value['uid'];
		}
		$all_price=0;
		$cd_price=0;
		$list_info="";
		if($goodid){
			$list=DB::table('order_list') ;
			$list_all=DB::table('order_list') ;

			if($uid_array){
				$list->whereIn('uid',$uid_array);
				$list_all->whereIn('uid',$uid_array);
			}
			if($plat_from==1){
				$list->where('plat_from','=',1);
				$list_all->where('plat_from','=',1);
			}
			if($plat_from==2){
				$list->where('plat_from','=',0);
				$list_all->where('plat_from','=',0);
			}


		if($search['pay_type']){
			switch($search['pay_type']){
				case  $search['pay_type']==1:
				$list->where('pay_type','=',1);
				$list_all->where('pay_type','=',1);
				break; 
				case  $search['pay_type']==2:
				$list->where('pay_type','=',2);
				$list_all->where('pay_type','=',2);
				 break; 
				case  $search['pay_type']==3:
				$list->where('pay_type','=',3);
				$list_all->where('pay_type','=',3);
				break; 
				case  $search['pay_type']==4:
				$list->where('pay_type','=',4);
				$list_all->where('pay_type','=',4);
				break; 				
				}
		}
		if($search['sdate']){
				$starttime=strtotime($search['sdate']);
				$list->where('updatetime','>=',$starttime);
				$list_all->where('updatetime','>=',$starttime);
		}  
		if($search['edate']){
			$endtime=strtotime($search['edate']);
			$list->where('updatetime','<=',$endtime+24*3600-1);
			$list_all->where('updatetime','<=',$endtime+24*3600-1);
		 
		}  
	 
			//默认 或者无下属商品
		if(count($goodid)==1 || $CD==0){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->orderBy("updatetime",'desc');
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->orderBy("updatetime",'desc');
		}
		//有
		if(count($goodid)==2  && $CD==1){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','=',$goodid['1']['id'])->orderBy("updatetime",'desc');	
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','=',$goodid['1']['id'])->orderBy("updatetime",'desc');	
		}
		//无光盘
		if(count($goodid)==2  && $CD==2){
			$list->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','!=',$goodid['1']['id'])->orderBy("updatetime",'desc');
			$list_all->where('goods_id','=',$goodid[0]['id'])->where('status','=',2)->where('attach_id','!=',$goodid['1']['id'])->orderBy("updatetime",'desc');
		}
	
			$rs=$list->paginate($page_size);
			foreach ($rs as $key => $value) {
				$list_info[$key]['order']=$value;
				if(count($goodid)==2){
					$list_info[$key]['order']['good_name']=$goodid[1]['name'];
					$list_info[$key]['order']['good_id']=$goodid[1]['id'];
				}	
				if($competitionid !=22){
				$list_info[$key] ['user']=DB::table('user_match')->where('uid','=',$value['uid'])->first();
				}else{
					$list_info[$key]['user']=DB::table('summercup')->where('uid','=',$value['uid'])->first();
				}	 
				$flag=DB::table('user_competition_flag')->where('uid','=',$value['uid'])->first();
				if($flag){
					$list_info[$key]['flag']=1;
				}else{
					$list_info[$key]['flag']=0;
				}
			}
		$all=$list_all->get();
		foreach ($all as $key => $value) {
				$all_price += $value['price'];
				if(count($goodid)==2 && $value['attach_id'] == $goodid[1]["id"] ){
					$cd_price +=$value["attach_price"];
				}
		}

		}else{
			$rs="";
		}
	 
 	//获取所有省份
		$data['allprovince'] = ApiCity::getAllProvince();
		//获取所有城市
		$data['allcity'] = ApiCity::getAllCity();
		//获取所有地区
		$data['allarea'] = ApiCity::getAllArea();
 			$competitionname=$competitionid_name[$competitionid];
		$tmp=array();
		foreach($list_info as $k=>$value){			
			$tmp[$k][]=$value['user']['uid'];
			$tmp[$k][]=$value['user']['nick_name'];
			$tmp[$k][]=$value['user']['name'];
			$tmp[$k][]=$value['user']['mobile'];
			$tmp[$k][]=$value['user']['gender']?'男':'女';;
			$tmp[$k][]=$value['order']['plat_from']?'安卓':'ios';;
			$tmp[$k][]=$competitionname;
			$tmp[$k][]=$value['order']['price'];
			if(isset($value['order']['good_id']) && $value['order']['attach_id']==$value['order']['good_id'] && $value['order']['good_name']){
				$tmp[$k][]=$value['order']['attach_price'];
			}elseif(isset($value['order']['good_id']) &&  $value['order']['attach_id']!=$value['order']['good_id'] && $value['order']['good_name']){
				$tmp[$k][]="-";
			}else{
				$tmp[$k][]="无光盘";
			}
			switch($value['order']['pay_type']){
				case  $value['order']['pay_type']==1:
				$tmp[$k][]= "银联";  break; 
				case  $value['order']['pay_type']==2:
				$tmp[$k][]= "支付宝";  break; 
				case  $value['order']['pay_type']==3:
				$tmp[$k][]="支付宝网银";  break; 
				case  $value['order']['pay_type']==4:
				$tmp[$k][]="财付通";  break; 
			}
			if($value['flag']==0){
				$tmp[$k][]='未审核';

			}else{
				$tmp[$k][]='已审核';
			}
			$tmp[$k][]=$value['user']['card']?$value['user']['card']:"";
			$tmp[$k][]=$value['user']['age']?$value['user']['age']:''; 
			$tmp[$k][]=$value['user']['province_id']?$data['allprovince'][$value['user']['province_id']]:'';
			$tmp[$k][]=$value['user']['city_id']?$data['allcity'][$value['user']['province_id']][$value['user']['city_id']]:"";
			$tmp[$k][]=$value['user']['area_id']?$data['allarea'][$value['user']['city_id']][$value['user']['area_id']]:"";
			$tmp[$k][]=$value['user']['address']?$value['user']['address']:"";
			$tmp[$k][]=$value['user']['zip']?$value['user']['zip']:"";
			$tmp[$k][]=$value['user']['email']?$value['user']['email']:"";
			$tmp[$k][]=$value['user']['note']?$value['user']['note']:"";
		}
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('决赛报名列表');		
		$sheetTitle=array('用户ID','用户昵称','真实姓名',"手机号码",'性别','用户品台','比赛项目','决赛费','光盘费','交费平台','审核状态','身份证号','年龄',"省",'城','县/区','地址','邮编','邮箱','组合类型');	
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		$rNum=2;
		foreach($tmp as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  $rNum++;
		}
		$outputFileName = "FinishList.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		$excel_url = '/upload/'.$outputFileName;
		echo "<a href='".$excel_url."'>下载</a>";
	}





}
 ?>
