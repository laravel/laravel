<?php
	class UserController extends BaseController 
	{
		public function captcha()
		{
			$validate = new AdminValidateCode(4,100,30);
			$validate->doimg();
		}
		public function index() 
		{
			$validate = new AdminValidateCode(4);
			return View::make('admin.AdLogin');
		}
		public function loginPost() {
			$username = Input::get('username');
			$password = Input::get('password');
			$validatecode = Input::get('valiatecode');
			session_start();
			
			$local_validatecode = strtolower($_SESSION[md5('dushivalcode')]);
			$credentials = array('name' => $username, 'password' => $password);
			if($local_validatecode == $validatecode && Auth::attempt($credentials))
			{
				$_SESSION["name"]=$username; 
				return Redirect::to('/admin');
			}
			else
			{
				return Redirect::to('/admin/login')
				->with('login_errors', true);
			}
		}
		//获取用户列表
		public function getUserList() {
			$pagesize = 20;
			$uid = intval(Input::get('uid'));
			$nick = Input::get('nick');
			$phone = Input::get('phone');
			$input_startime = Input::get('starttime');
			$input_endtime = Input::get('endtime');
			$starttime = strtotime($input_startime);
			$endtime = strtotime($input_endtime);
			$authtype = isset($_GET['authtype']) ? Input::get('authtype') : -1;
			$isdel = isset($_GET['isdel']) ? Input::get('isdel') : -1;
			$conn = DB::table('user');
			if(!empty($uid)){
				$conn = $conn->where('id','=',$uid);
			}
			if(!empty($nick)){
				$conn = $conn->where('nick','like','%'.$nick.'%');
			}
			if(!empty($phone)){
				$conn = $conn->where('phone','=',$phone);
			}
			if($authtype > -1){
				$conn = $conn->where('authtype','=',$authtype);
			}
			if($isdel > -1){
				$conn = $conn->where('isdel','=',$isdel);
			}
			// $conn = $conn->where('isdel','<>',1);
			$uid_count = $count = $conn->count();
			$total = $conn->max('id');
			//全部
			if(($authtype == -1 || !isset($authtype)) && empty($uid) &&  empty($nick) && empty($phone)  && (empty($isdel) || $isdel=-1))
			{
				$count = $total;
			}
			//未认证
			else if($authtype == 0)
			{
				$tmp_count = DB::table('user')->where('authtype','=',1)->count();
				$count = $total-$tmp_count;
			}
			else{
				$count = $uid_count;
			}
			$addtimecount = $conn->max('id');
			if($starttime && $endtime){
				$conn = $conn->where('addtime','>=',$starttime);
				$conn = $conn->where('addtime','<=',$endtime+86399);
				$addtimecount = $conn->count();
			}elseif($starttime) {
				$conn = $conn->where('addtime','>=',$starttime);
				$addtimecount = $conn->count();
			}elseif($endtime){
				$conn = $conn->where('addtime','<=',$endtime+86399);
				$addtimecount = $conn->count();
			}elseif($input_startime == '开始时间' && $input_endtime == '结束时间'){
				$input_startime = 0;
				$input_endtime = 0;
				$addtimecount = $conn->max('id');
			}
			$userlist = $conn->orderBy('id','desc')->paginate($pagesize);
			//查询用户总数
			$array = array('userlist'=>$userlist,'nick'=>$nick,'uid'=>$uid,'phone'=>$phone,'authtype'=>$authtype,'isdel'=>$isdel,'starttime'=>$input_startime,'endtime'=>$input_endtime,'count'=>$count,'addtimecount'=>$addtimecount);
			return View::make('user.userlist',$array);
		}
		
		//获取用户列表-xls
		public function getUserListXls(){
			ini_set("memory_limit", "1024M");
			set_time_limit(0);
			$uid = intval(Input::get('uid'));
			$nick = Input::get('nick');
			$phone = Input::get('phone');
			$input_startime = Input::get('starttime');
			$input_endtime = Input::get('endtime');
			$starttime = strtotime($input_startime);
			$endtime = strtotime($input_endtime);
			$authtype = isset($_GET['authtype']) ? Input::get('authtype') : -1;
			$isdel = isset($_GET['isdel']) ? Input::get('isdel') : -1;
			$conn = DB::table('user');
			if(!empty($uid)){
				$conn->where('id','=',$uid);
			}
			if(!empty($nick)){
				 $conn->where('nick','like','%'.$nick.'%');
			}
			if(!empty($phone)){
				$conn->where('phone','=',$phone);
			}
			if($authtype > -1){
				$conn->where('authtype','=',$authtype);
			}
			if($isdel > -1){
				$conn->where('isdel','=',$isdel);
			}
			// $conn = $conn->where('isdel','<>',1);
			$uid_count = $count = $conn->count();
			$total = $conn->max('id');
			//全部
			if(($authtype == -1 || !isset($authtype)) && empty($uid) &&  empty($nick) && empty($phone)  && (empty($isdel) || $isdel=-1))
			{
				$count = $total;
			}
			//未认证
			else if($authtype == 0)
			{
				$tmp_count = DB::table('user')->where('authtype','=',1)->count();
				$count = $total-$tmp_count;
			}
			else{
				$count = $uid_count;
			}
			//$addtimecount = $conn->max('id');
			if($starttime && $endtime){
				 $conn->where('addtime','>=',$starttime);
				 $conn->where('addtime','<=',$endtime+86399);
				//$addtimecount = $conn->count();
			}elseif($starttime) {
				$conn->where('addtime','>=',$starttime);
				//$addtimecount = $conn->count();
			}elseif($endtime){
				 $conn->where('addtime','<=',$endtime+86399);
				//$addtimecount = $conn->count();
			}elseif($input_startime == '开始时间' && $input_endtime == '结束时间'){
				$input_startime = 0;
				$input_endtime = 0;
				//$addtimecount = $conn->max('id');
			}
			$userlist = $conn->orderBy('id','desc')->get();
			
			foreach($userlist as $k=>$v){
				//array('UID','昵称','姓名','邮箱','手机号','性别','等级','注册时间','来源','认证信息','是否童星');
				$tmp=array();
				$tmp[]=$v['id'];
				$tmp[]=$v['nick'];
				$tmp[]=$v['real_name'];
				$tmp[]=$v['email'];
				$tmp[]=$v['phone'];
				$tmp[]=$v['gender']==1?'男':'女';
				$tmp[]=$v['grade'];
				$tmp[]=date('Y-m-d H:i',$v['addtime']);
				if($v['thpartType']==0){
					$thpartType='本系统';
				}elseif($v['thpartType']==1){
					$thpartType='新浪';
				}else{
					$thpartType='qq';
				}
				$tmp[]=$thpartType;
				$tmp[]=$v['authconent'];
				$tmp[]=$v['teenager']==1?'是':'-';
				$data[]=$tmp;
			}
			//==========================================
			require_once ("../app/ext/PHPExcel.php");
			$excel=new PHPExcel();
			$objWriter = new PHPExcel_Writer_Excel5($excel);
			//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
			
			//设置当前表
			$excel->setActiveSheetIndex(0);
			$sheet=$excel->getActiveSheet();
			$sheet->setTitle('认证用户');		
			//设置第一行内容
			$sheetTitle=array('UID','昵称','姓名','邮箱','手机号','性别','等级','注册时间','来源','认证信息','是否童星');
			$cNum=0;
			foreach($sheetTitle as $val){
			  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
			  $cNum++;
			}
			$rNum=2;
			if(empty($data)){
				return Redirect::to('/admin/GetUserList')->with('message',"选择时间数据为空");
			}
			foreach($data as $val){
			  $cNum=0;
			  foreach($val as $row){
				  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
				  $cNum++;
			  }
			  $rNum++;
			}
			$outputFileName = "userlist.xls";
			$file='upload/'.$outputFileName;	
			$objWriter->save($file);
			//www.weinidushi.com.cn   localhost
			echo "<a href='http://www.weinidushi.com.cn/upload/".$outputFileName."'>下载</a>";			
		}
		//修改昵称
		public function upUserName(){
			$uid = Input::get('id');
			$nick = Input::get('nick');
			$real_name = Input::get('real_name');
			$sex = Input::get('sex');
			if(!empty($nick)){
				DB::table('user')->where('id',$uid)->update(array('nick'=>$nick));
			}elseif(!empty($real_name)){
				DB::table('user')->where('id',$uid)->update(array('real_name'=>$real_name));
			}elseif(!empty($sex)){
				DB::table('user')->where('id',$uid)->update(array('gender'=>$sex)); 
			}
		}
		//禁用/解禁用户
		public function delOrDelUser() {
			$uid = intval(Input::get('uid'));
			$sign = intval(Input::get('sign'));
			if(empty($uid)) {
				echo 'error';
				exit;
			} 
			//解禁 isdel -> 0
			if($sign) {
				$poetry_del = 2;
				$isdel = 0;
			} else {
				$isdel = 1;
				$poetry_del = 0;
			}
			$flag = DB::table('user')->where('id',$uid)->update(array('isdel'=>$isdel,'attention'=>0,'fans'=>0));

			if($flag){
				if($isdel==1)
				{
				//彻底删除 
				//恢复/删除所有作品，定制听中作品
			
				try{
				$list=DB::table('opus')->where('uid',$uid)->get();
					foreach ($list as $key => $value) {
						ApiCommonStatic::delOpus ( $uid,$value['id'] );
					}
					DB::table('personalcustom')->where('uid',$uid)->delete();
					//诗文比赛作品
					DB::table('opus_poetry')->where('uid',$uid)->delete();
					//
				} catch (Exception $e){

				}
					DB::table('follow')->where('uid',$uid)->orWhere('fid',$uid)->delete();
					//删除朗诵会冗余表中记录
					try {
						$apiEsSearch = App::make('apiEsSearch');
						DB::table('league_user')->where('uid',$uid)->delete();
						//同步删除ES中用户
						$apiEsSearch->delEsUser($uid);
						//同步删除ES中用户作品
						$opus_ids = DB::table('opus')->where('uid',$uid)->lists('id');
						if(!empty($opus_ids)){
							foreach($opus_ids as $item){
								//同步ES用户
								$apiEsSearch->delEsOpus($item);
							}
						}
					} catch (Exception $e) {
						
					}
					//清除用户redis缓存数据
					$token = DB::table('user')->where('id',$uid)->pluck('token');
					if(!empty($token)){
						$redisUserInfo = new RedisUserInfo();
						$redisUserInfo->delUserCookieFromRedis($uid,$token);
					}
				}else{
					//关注原来的推荐用户
					$adminUser = new AdminUser();
					$adminUser->autoAttention($uid);
				}
				echo 'success';
			}else{
				echo 'error';

			}
 

		}

		//后台添加用户
		public function addUserGet() {
			return View::make('user.adduser');
		}

		//后台添加用户-动作
		public function addUserPost() {
			$lastPhone = null;
			$lastEmail = null;
			$nick = trim(Input::get('nick'));
			$userRs = DB::table('user')->where('nick','=',$nick)->first(array('id'));
			if(!empty($userRs)) {
				return Redirect::to('/admin/defaultError')->with('message',"用户已经存在");
			}
			//手机号码
			$email = trim(Input::get('email'));
			$pattern = '/^[0-9]{6,12}$/i';
			$pattern2 = '/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
			if(preg_match($pattern,$email)) {
				$lastPhone = $email;
				$phone = DB::table('user')->where('phone','=',$lastPhone)->first(array('id'));
				if(!empty($phone)) {
					return Redirect::to('/admin/defaultError')->with('message',"手机号码已经存在");
				}
			}else {
				return Redirect::to('/admin/defaultError')->with('message',"手机号码格式错误");
			}
			//用户头像
			$fileName = strtotime(date('Y-m-d',time()));
			$filePath = './upload/portrait/'.$fileName.'/';
			if(!file_exists($filePath)) {
				mkdir($filePath,0755,true);
			}
			$arr = Input::file('portrait');
			$lastFilePath = null;
			//$id = !empty(Input::get('id')) ? intval(Input::get('id')) : 0;
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
					$ext = $arr->guessExtension();
					$imgName = time().uniqid();
					$imgName = $imgName.'.'.$ext;
					$lastFilePath = $filePath.$imgName;
					$arr->move($filePath,$imgName);
					$lastFilePath = ltrim($lastFilePath,'.');
					//生成250尺寸图片
					$img = Image::make('.'.$lastFilePath)->resize(250, 250);
					$imgSportrait = $filePath."250".$imgName;
					$img->save($imgSportrait);
					$lastFilePath = ltrim($imgSportrait,'.');
					//生成小图片
					$img = Image::make('.'.$lastFilePath)->resize(100, 100);
					$imgSportrait = $filePath."s_".$imgName;
					$img->save($imgSportrait);
					$imgSportraitFilePath = ltrim($imgSportrait,'.');
					//删除原图片
					// unlink($filePath.$imgName);
				}elseif($measure['0'] && $measure['1'] < 250){
					
					$ext = $arr->guessExtension();
					$imgName = time().uniqid();
					$imgName = $imgName.'.'.$ext;
					$lastFilePath = $filePath.$imgName;
					$arr->move($filePath,$imgName);
					$lastFilePath = ltrim($lastFilePath,'.');
					//小图片
					$img = Image::make('.'.$lastFilePath)->resize(100, 100);
					$imgSportrait = $filePath."s_".$imgName;
					$img->save($imgSportrait);
					$imgSportraitFilePath = ltrim($imgSportrait,'.');
				}
			}else
			{
				return Redirect::to('/admin/defaultError')->with('message','请上传图片');
			}
			/******************************头像****************************/
			$password = trim(Input::get('password'));
			$gender = Input::get('gender');
			$teenager = Input::get('teenager');
			if(!empty($gender)) {
				//$sportrait = './upload/portrait/smale.png';
				//$portrait = './upload/portrait/male.png';
				$anchor = 17;
			} else {
				//$sportrait = './upload/portrait/sfemale.png';
				//$portrait = './upload/portrait/female.png';
				$anchor = 18;
			}
			$authtype = Input::get('authtype');
			$arr = array(
				'nick'=>$nick,
				'portrait'=>$lastFilePath,
				'sportrait'=>$imgSportraitFilePath,
				'pwd'=>md5($password),
				'email'=>$lastEmail,
				'gender'=>$gender,
				'addtime'=>time(),
				//'sportrait'=>$sportrait,
				//'portrait'=>$portrait,
				'bgpic' => '/upload/bgpic/default.png',
				'anchor'=>$anchor,
				'teenager'=>$teenager,
				'phone' => $lastPhone,
				'token' => uniqid().mt_rand(10000,99999),
			);
			$id = DB::table('user')->insertGetId($arr);
			if(!$id){
				return Redirect::to('/admin/defaultError')->with('message','添加失败，请重试');
			}
			$this->autoAttention($id);
			//var_dump($arr);die;
			return View::make('user.adduser');
		}
		/**
		 * @自动关注推荐用户
		 * @param  $id 用户id
		 * @author:wang.hongli
		 * @since:2016/04/02
		 */
		protected function autoAttention($id) {
			if (empty ( $id ))
				return 0;
				$id = intval ( $id );
				$tmpRs = DB::table ( 'user' )->where ( 'recommenduser', '<', 9999 )->orderBy ( 'recommenduser', 'asc' )->get ();
				if (empty ( $tmpRs )) {
					return 0;
				}
				$data = array ();
				$attentionNum = count ( $tmpRs );
				$time = time ();
				$recommendUserId = array ();
				foreach ( $tmpRs as $key => $value ) {
					$time = $time - 1;
					$data [] = array (
							'uid' => $id,
							'fid' => $value ['id'],
							'relation' => 1,
							'dateline' => $time
					);
					$recommendUserId [] = $value ['id'];
				}
				try {
					DB::table ( 'follow' )->insert ( $data );
					// 推荐用户粉丝数+1
					DB::table ( 'user' )->whereIn ( 'id', $recommendUserId )->increment ( 'fans', 1 );
					// 用户的关注数
					DB::table ( 'user' )->where ( 'id', $id )->update ( array (
							'attention' => $attentionNum
					) );
					return $attentionNum;
				} catch (Exception $e) {
					return 0;
				}
		}
		//修改用户认证标识
		public function userAuthStatus() {
			$uid = Input::get('uid');
			$sign = Input::get('sign');
			if(empty($uid)) {
				echo 'error';
				return;
			} else {
				//sign = 1 取消认证
				if($sign) {
					$authtype = 0;
				} else {
					$authtype = 1;
				}
				$sql = "update user set authtype={$authtype} where id = {$uid}";
				if(!DB::update($sql)) {
					echo "error";
					return;
				}
			}
		}
		//修改或添加日志
		/**
		 * [userAuthContent description]
		 * @return [type] [description]
		 */
		public function userAuthContent(){
			$uid = Input::get('uid');
			$authconent = Input::get('authconent');
			if(empty($uid)){
				echo "error";
				return;
			}
			$user = DB::table('user_auth_content')->where('uid','=',$uid)->first(array('id'));
			if(!empty($user['id']))
			{
				DB::table('user_auth_content')->where('uid','=',$uid)->update(array('auth_content'=>$authconent));
			}else{
				$usercontent = array(
					'uid'=>$uid,
					'auth_content'=>$authconent,
				);
				DB::table('user_auth_content')->insert($usercontent);
			}
		}
		//修改认证信息
		public function modifyAuthContent() {
			$authconent = Input::get('authconent');
			$uid = Input::get('uid');
			if(empty($uid)) {
				echo "error";
				return;
			}
			$sql = "update user set authconent='{$authconent}' where id = {$uid}";
			DB::update($sql);
			return;
		}

		//获取报名读诗信息
		public function signUp() {
			$pagesize=20;
			$data = DB::table('signup')->where('uid','<>',0)->orderBy('addtime','desc')->paginate($pagesize);
			$users=array();
			if(!empty($data)){
				$uids=array();
				foreach($data as $v){
					$uids[$v['uid']]=$v['uid'];
				}
				$sql="select id,nick,gender from user where id in('".implode("','",$uids)."')";
				$rlt = DB::select($sql);
				foreach($rlt as $k=>$v){
					$users[$v['id']]=$v;
				}
			}
			return View::make('user.signup')->with('list',$data)->with('users',$users)->with('data',$data);
		}
		//删除报名用户
		public function delSignUp() 
		{
			$id = intval(Input::get('id'));
			if(empty($id)) return 'error';
			$sql = "delete from signup where id = {$id}";
			DB::delete($sql);
		}
		//修改是否为童星
		public function userTeenager()
		{	
			$uid = intval(Input::get('uid'));
			$sign = intval(Input::get('sign'));
			if(empty($uid)) 
			{
				echo 'error';
				return;
			} else {
				//sign = 1 取消认证
				if($sign) {
					$teenager = 0;
				} else {
					$teenager = 1;
				}
				$sql = "update user set teenager={$teenager} where id = {$uid}";
				if(!DB::update($sql)) {
					echo "error";
					return;
				}
			}
		}
		/**
		*	后台用户认证-认证列表
		*	@author:wang.hongli
		*	@since:2015/01/17
		*/
		public function authorUserList()
		{	
			$pagesize = 50;
			$array = array();
			$array['startTime'] = $starTime = Input::get('startTime') ? Input::get('startTime') : '';
			$array['endTime'] = $endTime = Input::get('endTime') ? Input::get('endTime') : '';
			$array['nick'] = $nick = Input::get('nick') ? Input::get('nick') : '';
			$array['authtype'] = $authtype = Input::get('authtype') ? intval(Input::get('authtype')) : 0;
			$array['id'] = $id = Input::has('id') ? intval(Input::get('id')) : '';
			$flag = Input::get('flag')?intval(Input::get('flag')) : 0;
			$conn = DB::table('author')->select('id','uid','nick','realname','telphone','content','addtime','status');
			if(!empty($id)){
				$conn->where('uid',$id);
			}
			if(!empty($nick))
			{	
				$nick = '%'.$nick.'%';
				$conn->where('nick','like',$nick);
			}
			if(!empty($starTime))
			{
				$starTime = strtotime($starTime);
				$conn->where('addtime','>=',$starTime);
			}
			if(!empty($endTime))
			{	
				$endTime = strtotime($endTime);
				$conn->where('addtime','<=',$endTime);
			}
			//选出所有认证过的用户id
			$author_ids = DB::table('user')->where('authtype','=',1)->lists('id');
			//选出认证过用户
			if(!empty($authtype))
			{	
				$conn->whereIn('uid',$author_ids);
			}
			else
			{
				$conn->whereNotIn('uid',$author_ids);
			}
			$array['total'] = $conn->count();
			$conn->orderBy('addtime','desc');
			
			if(empty($flag))
			{	
				$array['userlist'] = $conn->paginate($pagesize);
				//用户作品数量
				$users = $user_num = array();
				if($array['total']>0){
					$uids=array();
					foreach($array['userlist'] as $v){
						$uids[$v['uid']]=$v['uid'];
					}
					$sql="SELECT uid,count(uid) as num from opus where uid in (".implode(",",$uids).") and isdel=0 GROUP BY uid";
					$rlt=DB::select($sql);
					foreach($rlt as $v){
						$user_num[$v['uid']]=$v['num'];
					}
					//用户信息
					$sql="select id,nick,gender from user where id in(".implode(",",$uids).")";
					$rlt=DB::select($sql);
					foreach($rlt as $v){
						$users[$v['id']]=$v;
					}
				}
				$array['user_num']=$user_num;
				$array['users']=$users;
				
				return View::make('user.authoruser',$array);
			}
			else
			{
				$array['userlist'] = $conn->get();
				
				//用户作品数量
				$user_num = array();
				if($array['total']>0){
					$uids=array();
					foreach($array['userlist'] as $v){
						$uids[$v['uid']]=$v['uid'];
					}
					$sql="SELECT uid,count(uid) as num from opus where uid in (".implode(",",$uids).") and isdel=0 GROUP BY uid";
					$rlt=DB::select($sql);
					foreach($rlt as $v){
						$user_num[$v['uid']]=$v['num'];
					}
				}
				
				//导出excel表格
				require_once ("../app/ext/PHPExcel.php");
				$excel=new PHPExcel();
				$objWriter = new PHPExcel_Writer_Excel5($excel);
				//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
				  
				//设置当前表
				$excel->setActiveSheetIndex(0);
				$sheet=$excel->getActiveSheet();
				$sheet->setTitle('认证用户列表');
				$sheet->getColumnDimensionByColumn(0)->setWidth(10);
				$sheet->getColumnDimensionByColumn(1)->setWidth(15);
				$sheet->getColumnDimensionByColumn(2)->setWidth(10);
				$sheet->getColumnDimensionByColumn(3)->setWidth(20);
				$sheet->getColumnDimensionByColumn(4)->setWidth(20);
				$sheet->getColumnDimensionByColumn(5)->setWidth(80);
				$sheet->getColumnDimensionByColumn(6)->setWidth(20);
				$sheet->getColumnDimensionByColumn(7)->setWidth(10);

				//设置第一行内容
				$sheetTitle=array('id','昵称','uid','真实姓名','手机号','认证内容','提交时间','认证状态','作品数量');
				$cNum=0;
				foreach($sheetTitle as $val){
				  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
				  $cNum++;
				}
				if(empty($array['total']))
				{
					echo -1;
				}
				$rNum=2;
				foreach($array['userlist'] as $val){
					$val['addtime'] = date('Y-m-d',$val['addtime']);
					$cNum=0;
					foreach($val as $row){
						$sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
						$cNum++;
					}
					//作品数量
					$_num = isset($user_num[$val['uid']])?$user_num[$val['uid']]:0;
					$sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$_num);
					$cNum++;
				  
				  	$rNum++;
				}
				$outputFileName = "认证用户-".uniqid().'-'.date('Y-m-d-H-i-s').".xls";
				$file='upload/'.$outputFileName;
				$objWriter->save($file);
				$url = Config::get('app.url');
				echo "<a href=$url/".$file.">下载</a>";
			}
			
		}
		/**
		 * 将系统用户和reader用户关联
		 * @author :wang.hongli
		 * @since :2016/08/23
		 */
		public function readerList(){
			$url = Config::get('app.url');
			$user_reader_list = DB::table('user_reader_rel')->where('isdel',0)->orderBy('id','desc')->get();

			$userids = [];
			$readerids = [];
			if(!empty($user_reader_list)){
				foreach($user_reader_list as $k=>$v){
					$userids[] = $v['uid'];
					$readerids[] = $v['reader_id'];
				}
			}
			$user_info = [];
			if(!empty($userids)){
				$tmp_user_info = DB::table('user')->whereIn('id',$userids)->get(['id','nick','sportrait']);
				if(!empty($tmp_user_info)){
					foreach($tmp_user_info as $k=>$v){
						$user_info[$v['id']] = $v;
					}
				}
			}	
			
			$reader_info = [];
			if(!empty($readerids)){
				$tmp_reader_info = DB::table('reader')->whereIn('id',$readerids)->get(['id as reader_id','name']);
				if(!empty($tmp_reader_info)){
					foreach($tmp_reader_info as $k=>$v){
						$reader_info[$v['reader_id']] = $v;
					}
				}	
			}
			if(!empty($user_reader_list)){
				foreach($user_reader_list as $k=>&$v){
					$v['nick'] = isset($user_info[$v['uid']]['nick']) ? $user_info[$v['uid']]['nick'] : '';
					$v['sportrait'] = isset($user_info[$v['uid']]['sportrait']) ? $url.ltrim($user_info[$v['uid']]['sportrait'],'.') : '';
					$v['name'] = isset($reader_info[$v['reader_id']]['name'])  ? $reader_info[$v['reader_id']]['name'] : '';
				}
			}
			return View::make('user.reader')->with('list',$user_reader_list);
		}
		/**
		 * 将系统用户和reader用户关联
		 * @author :wang.hongli
		 * @since :2016/08/23
		 */
		public function relateRUser(){
			$list = DB::table('reader')->get();
			return View::make('user.relateruser')->with('list',$list);
		}

		/**
		 * 删除关联
		 * @author :wang.hongli
		 * @since :2016/08/23
		 */
		public function delRelUser(){
			$method = Request::ajax();
			if(!$method){
				echo -1;
				return;
			}
			$id = intval(Input::get('id',0));
			if(empty($id)){
				echo -1;
				return;
			}
			$flag = DB::table('user_reader_rel')->where('id',$id)->update(['isdel'=>1]);
			if($flag){
				echo "删除完成";
				return;
			}else{
				echo -1;
			}

		}
		/**
		 * 添加关联
		 * @author :wang.hongli
		 * @since :2016/08/23
		 */
		public function addRelUser(){
			$uid = Input::get('uid',0);
			$reader_id = Input::get('reader_id',0);
			if(empty($uid) || empty($reader_id)){
				return Redirect::to('/admin/defaultError')->with('message','添加关联错误，请重试');
			}
			$uid = DB::table('user')->where('id',$uid)->take(1)->pluck('id');
			if(empty($uid)){
				return Redirect::to('/admin/defaultError')->with('message','用户不存在');
			}
			$reader_id = DB::table('reader')->where('id',$reader_id)->take(1)->pluck('id');
			if(empty($reader_id)){
				return Redirect::to('/admin/defaultError')->with('message','导师不存在');
			}
			if(!DB::table('user_reader_rel')->insert(['uid'=>$uid,'reader_id'=>$reader_id,'addtime'=>time()])){
				return Redirect::to('/admin/defaultError')->with('message','添加关联错误，请重试');
			}
			return Redirect::to('/admin/readerList');
		}

	}
