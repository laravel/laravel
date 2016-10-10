<?php 
	class ApiUser extends ApiCommon {
		
		/**
		 * 注册新接口
		 * @author:wang.hongli
		 * @since:2016/05/27
		 */
		public function registerV2(){
			$rules = array(
					'nick'=>'required|between:1,6|unique:user,nick',
					'password'=>'required|regex:/^[0-9]{6}$/',
					'gender'=>'required|integer',
					'email'=>'required|regex:/^[1][3-9]{1}\d{9}$/|unique:user,phone',//原来可能是手机号，邮件，现在只能是手机号
			);
			$message = array(
					'nick.required'=>'请填写昵称',
					'nick.between'=>'昵称在6个字符内',
					'nick.unique'=>'用昵称已经存在',
					'gender.required'=>'请选择性别',
					'gender.integer'=>'性别错误',
					'email.required'=>'请填写手机号',
					'email.regex'=>'手机号格式错误',
					'email.unique'=>'手机号已经存在',
					'password.required'=>'请填写手机号',
					'password.regex'=>'密码为6位数字'
			);
			$input = dealPostData();
			//验证表单数据
			$validator = Validator::make($input, $rules,$message);
			if($validator->fails()){
				 return $validator->messages()->first();
			}
			$data['nick'] = trim($input['nick']);
			//禁用词检测
			if(my_sens_word($data['nick']))
			{
				return '昵称含有禁用词';
			}
			$data['gender'] = !empty($input['gender']) ? intval($input['gender']) : 0;//0女1男
			$data['teenager'] = !empty($input['teenager']) ? 1 : 0;
			$data['sportrait'] = './upload/portrait/sfemale.png';
			$data['portrait'] = './upload/portrait/female.png';
			$data['anchor'] = 18;
			if(!empty($data['gender'])){
				$data['sportrait'] = './upload/portrait/smale.png';
				$data['portrait'] = './upload/portrait/male.png';
				$data['anchor'] = 17;
			}
			$data['pinyin'] =  @getPinyin($data['nick']);
			$data['pwd'] = md5($input['password']);
			$data['email'] = '';
			$data['phone'] = $input['email'];
			$data['token'] = uniqid().mt_rand(10000,99999);
			$data['addtime'] = time();
			$data['bgpic'] = '/upload/bgpic/default.png';
			try {
				$id = DB::table('user')->insertGetId($data);
			} catch (Exception $e) {
				return '注册失败，请重试';
			}
			//自动关注推荐用户
			$data['attention'] = $this->autoAttention($id);
			$data['id'] = $id;
			$data['praisenum'] = $data['lnum'] = $data['repostnum'] = $data['praisenum'] = $data['fans'] =$data['opusnum'] = $data['authtype'] = $data['albums']  = 0;
			$data['grade'] = 1;
			$data['accessToken'] = '';
			$data['expirationDate'] = '';
			$data['thPartid'] = '';
			$cookieStr = $data['id'].'|'.$data['token'];
			setcookie('id',$cookieStr,time()+1600000000);
			$data['portrait'] = !empty($data['portrait']) ?  $this->poem_url.ltrim($data['portrait'],'.') : '';
			$data['sportrait'] = !empty($data['sportrait']) ? $this->poem_url.ltrim($data['sportrait'],'.') : '';
			$data['bgpic'] = !empty($data['bgpic']) ? $this->poem_url.ltrim($data['bgpic'],'.') : '' ;

			$this->lastLoginIP($data["id"]);
			//设置自动登陆cookie
			// $cookieStr = $data['id'].'|'.$data['token'];
			// setcookie('id',$cookieStr,time()+1600000000);
			if(Input::has('idfa')){
				$idfa = Input::get('idfa');
				$this->checkIdfa($idfa,1);
			}
			//同步ES用户
			$apiEsSearch = App::make('apiEsSearch');
			$apiEsSearch->addEsUser(['id'=>$id,'nick'=>$data['nick'],'pinyin'=>$data['pinyin']]);

			return $data;
			
		}
		//注册方法
		public function register() {
			$errorMessage='含有禁用词语，请重试';
			if(!Input::has('nick')) return '用户昵称不能为空';
			if(!Input::has('password')) return '用户密码不能为空';
			if(!Input::has('gender')) return '请选择性别';
			$nick = trim(Input::get('nick'));
			if(my_sens_word($nick))
			{
				return $errorMessage;
			}
			$password = trim(Input::get('password'));
			$email = Input::get('email'); //可能是手机号或者email
			if(empty($email)) return '注册邮箱或手机号不能同时为空';
			$lastEmail = null;
			$lastPhone = 0;
			//判断是邮箱登陆，还是手机号登陆
			$pattern1 = '/^[0-9a-zA-Z\.]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
			$pattern2 = '/^[0-9]{6,11}$/i';
			if(preg_match($pattern1, $email)){
				return '只能通过手机号注册';
			}
			//验证是否为手机号码
			if(preg_match($pattern2,$email)){
				$lastPhone = $email;
				$emailrs = DB::table('user')->where('phone','=',$lastPhone)->first();
				if($emailrs) return '手机号已经注册';
			}else{
				return '输入的手机号或邮箱错误';
			}
			//检查数据库中是否已经存在
			$user = DB::table('user')->where('nick','=',$nick)->first();
			if($user) return '此昵称已经存在';

			$gender = Input::has('gender') ? intval(Input::get('gender')) : 0;//0女1男
			$teenager = !empty(Input::get('teenager')) ? 1 : 0;
			$sportrait = './upload/portrait/sfemale.png';
			$portrait = './upload/portrait/female.png';
			$anchor = 18;
			if(!empty($gender)) {
				$sportrait = './upload/portrait/smale.png';
				$portrait = './upload/portrait/male.png';
				$anchor = 17;
			}
			if(mb_strlen($nick,'utf-8') > 8) {
				return '昵称不能超过八位';
			}
			$nickPinYin = getPinyin($nick);
			$pattern = '/^[0-9]{6}$/';
			if(!preg_match($pattern,$password)) return '密码必须为六位纯数字';
			$password = md5($password);
			$token = uniqid().mt_rand(10000,99999);
			$user_info = array(
					'nick'=>$nick,
					'pwd'=>$password,
					'email'=>$lastEmail,
					'gender'=>$gender,
					'addtime'=>time(),
					'sportrait'=>$sportrait,
					'portrait'=>$portrait,
					'bgpic' => '/upload/bgpic/default.png',
					'anchor'=>$anchor,
					'teenager'=>$teenager,
					'phone' => $lastPhone,
					'token' => $token,
					'pinyin'=>$nickPinYin,
			);
			$id = DB::table('user')->insertGetId($user_info);
			if(!$id) return '注册失败，请重试';
			$info = DB::table('user')
							->where('id','=',$id)->where('token','=',$token)
							->first(array('id','nick','email','phone',
								'gender','lnum','repostnum','attention',
								'praisenum','fans','opusnum','grade',
								'sportrait','portrait','bgpic','albums','signature',
								'authtype','addtime','accessToken','expirationDate','thPartid'));
			if(empty($info)) return '注册失败，请重试';
			
			//start添加到环信=========
			/*if(!empty($info)){
				$easemob = new ApiEasemob;
				$hx_password = $easemob->pwdHash($info['id']);
				$easemob->addUser($info['id'],$hx_password,$info['nick']);
			}*/
			//=========添加到环信end
			
			$info['haspwd'] = 1;
			$cookieStr = $info['id'].'|'.$token;
			setcookie('id',$cookieStr,time()+1600000000);
			$info['portrait'] = !empty($info['portrait']) ?  $this->poem_url.ltrim($info['portrait'],'.') : '';
			$info['sportrait'] = !empty($info['sportrait']) ? $this->poem_url.ltrim($info['sportrait'],'.') : '';
			$info['bgpic'] = !empty($info['bgpic']) ? $this->poem_url.ltrim($info['bgpic'],'.') : '' ;
// 			$info['hx_uid']=$info["id"];
// 			$info['hx_password']=ApiEasemob::pwdHash($info["id"]);
			//记录用户的登陆ip--暂时不记录2016/06/12
// 			$this->lastLoginIP($info["id"]);
			//注册成功后自动关注推荐主播
			$this->autoAttention($id);
			return $info;
		}
		//系统登录
		public function login() {
			$info = array();
			if(!Input::has('password')) return '请输入密码';
			$email = trim(Input::get('email'));
			if(empty($email)) return '请输入邮件或电话号登陆';
			$password = md5(Input::get('password'));
			//判断使用邮箱还是电话号码登陆
			$pattern1 = '/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
			$pattern2 = '/^[0-9]{6,11}$/i';
			if(preg_match($pattern2,$email)) {
				$info = DB::table('user')->where('phone','=',$email)
									->where('pwd','=',$password)
									->first(array('id','nick','email','phone','gender','lnum','repostnum','attention','praisenum','fans','opusnum','grade','sportrait','portrait','bgpic','albums','signature','authtype','addtime','isdel'));
			} else {
				$info = DB::table('user')->where('email','=',$email)
									->where('pwd','=',$password)
									->first(array('id','nick','email','phone','gender','lnum','repostnum','attention','praisenum','fans','opusnum','grade','sportrait','portrait','bgpic','albums','signature','authtype','addtime','isdel'));
			} 
			if($info) {
				if($info['isdel'] == 1) {
					return '账号被禁用';
				}
				$token = uniqid().mt_rand(10000,99999);
				$cookieStr = $info['id'].'|'.$token;
				//del redis cookie token
				$this->delOldUserCookieToken($info['id']);
				//更新cookie
				DB::table('user')->where('id',$info['id'])->update(array('token'=>$token));
				setcookie('id',$cookieStr,time()+1600000000);
				$info['portrait'] = !empty($info['portrait']) ?  $this->poem_url.ltrim($info['portrait'],'.') : '';
				$info['sportrait'] = !empty($info['sportrait']) ? $this->poem_url.ltrim($info['sportrait'],'.') : '';
				$info['bgpic'] = !empty($info['bgpic']) ? $this->poem_url.ltrim($info['bgpic']) : '' ;
				
				$info['hx_uid']=$info["id"];
				$info['hx_password']=ApiEasemob::pwdHash($info["id"]);
				//记录用户的登陆ip -- 暂时去除
				// $this->lastLoginIP($info["id"]);
				if(Input::has('idfa')){
					$idfa = Input::get('idfa');
					$this->checkIdfa($idfa,2);
				}
				return $info;
			} else {
				return '账号密码不匹配';
			}						
		}

		/**
		*	微信登录获取state state 放入到session中
		*	@author:wang.hongli
		*	@since:2015/07/04
		**/
		public function getWeiXinState()
		{
			$uniqkey = md5(uniqid().time());
			session_start();
			$_SESSION['state'] = $uniqkey;
			return $uniqkey;
		}

		/**
		*	微信登录，通过appid,secret,code获取access_token
		*	@author:wang.hongli
		*	@since:2015/07/03
		*/
		public function getAccessToken($code='',$state='')
		{
			session_start();
			$local_state = !empty($_SESSION['state']) ? $_SESSION['state'] : '';
			unset($_SESSION['state']);
			if(empty($code) || empty($state) || ($state != $local_state))
			{
				return false;
			}
			//请求微信接口，获取有效access_token
			$return = array();
			$weixin_rs = require_once '../app/config/weixin_config.php';
			$appid = $weixin_rs['appid'];
			$secret = $weixin_rs['secret'];
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
			$return_tmp= my_get_url_contents($url);
			$return = json_decode($return_tmp,true);
			if(!empty($return) && isset($return['errcode']))
			{
				return false;
			}
			return $return;
		}
		/**
		*	微信登录获取用户信息
		*	@author:wang.hongli
		*	@since:2015/07/08
		*	@param:access_token,openid
		**/
		public function getWeiXinUserInfo($access_token,$openid)
		{
			if(empty($access_token) || empty($openid))
			{
				return false;
			}
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token;
			$url .= '&openid='.$openid;
			$userinfo = json_decode(my_get_url_contents($url),true);
			if(isset($userinfo['errcode']))
			{
				return false;
			}
			return $userinfo;
		}
	
		//第三方登录
		public function thirdPartLogin() {
			if(!Input::has('thpartType')) return false;
			
			//2015-08-09 补救iso把qq的类型传为了0
			$thpartType = !empty(Input::get('thpartType')) ? Input::get('thpartType') : 2; //第三方登录标示 1 新浪 2qq 3微信登录
			
			if(!Input::has('expirationDate') || !Input::has('id') || !Input::has('name')  || !Input::has('gender')) 
				return false;
			$accessToken = Input::get('accessToken');
			$accessToken = $accessToken ? $accessToken : 0;
			$expirationDate = time()+2592000; 
			$thPartid = Input::get('id'); //唯一不变
			$name = Input::get('name');
			//获取拼音
			$namepinyin = @getPinyin($name);
			
			// if(mb_strlen($name,'utf-8') > 5) return -102;
			$description = !empty(Input::get('description')) ? Input::get('description') : '';
			$gender = Input::get('gender') ? Input::get('gender') : 0 ;
			
			//微信特殊处理2015-08-30补
			if($thpartType==3){
				$gender = $gender==1 ?1:0;
			}
			if(!empty($gender)) {
				$sportrait = './upload/portrait/smale.png';
				$portrait = './upload/portrait/male.png';
				$anchor = 17;
			} else {
				$sportrait = './upload/portrait/sfemale.png';
				$portrait = './upload/portrait/female.png';
				$anchor = 18;
			}
			$modifytime = $addtime = time();
			$token = uniqid().mt_rand(10000,99999);
			$rs = DB::table('user')	->where('thPartid','=',$thPartid)
								   	->where('thpartType','=',$thpartType)
								   	->first(array('id','nick','email',
									'phone','gender','lnum','repostnum',
									'attention','praisenum','fans','opusnum',
									'grade','sportrait','portrait','albums',
									'signature','authtype','addtime','accessToken','expirationDate','thPartid','pwd','isdel'));
			$sportrait = !empty($rs['sportrait']) ? $rs['sportrait'] : $sportrait;
			$portrait = !empty($rs['portrait']) ? $rs['portrait'] : $portrait;
			$signature = !empty($rs['signature']) ? $rs['signature'] : $description;
			$arr = array(
							'nick'=>$name,
							'gender'=>$gender,
							'modifytime' => $modifytime,
							'signature' => $signature,
							'expirationDate'=>$expirationDate,
							'accessToken'=>$accessToken,
							'thPartid' => $thPartid,
							'thpartType' => $thpartType,
							'addtime' => $addtime,
							'anchor' =>$anchor,
							'portrait' =>$portrait,
							'sportrait' =>$sportrait,
							'token'	=>$token,
							'pinyin'=>$namepinyin,
						);
			//更新
			if(!empty($rs)) {
				if(1 == $rs['isdel']) return -103; //账号被禁用
				$id = $rs['id'];
				//第三方再次登录，忽略用户名
				unset($arr['gender']);
				unset($arr['nick']);
				unset($arr['addtime']);
				//del redis cookie token
				$this->delOldUserCookieToken($id);
				
				if(!DB::table('user')->where('id','=',$id)->update($arr)) {
					return false;
				} else {
					//清空原来的cookie
					setcookie('id',$id,time()-3600);
					$cookieStr = $id.'|'.$token;
					setcookie('id',$cookieStr,$expirationDate);
					if(!empty($rs['pwd'])) {
						$rs['haspwd'] = 1;
					} else {
						$rs['haspwd'] = 0;
					}
					unset($rs['pwd']);
					$rs['portrait'] = !empty($rs['portrait']) ?  $this->poem_url.ltrim($rs['portrait'],'.') : '';
					$rs['sportrait'] = !empty($rs['sportrait']) ? $this->poem_url.ltrim($rs['sportrait'],'.') : '';
					$rs['bgpic'] = !empty($rs['bgpic']) ? $this->poem_url.ltrim($rs['bgpic']) : '' ;
					
					return $rs;
				}
			} else {
				//插入新用户
				try {
					//判断用户昵称是否超过5个字
					if(mb_strlen($name,'utf-8') > 8) return -102;
					$id = DB::table('user')->insertGetId($arr);
					$this->autoAttention($id);
					//插入新用户后，自动关注歌手推荐榜
					
					//环信用户start
					/*$easemob = new ApiEasemob;
					$hx_password = $easemob->pwdHash($id);
					$easemob->addUser($id,$hx_password,$name);*/
					//环信用户end

				} catch(Exception $e) {
					return -101;
				}
			}
			if(!$id) return false;
			//头像
			$this->uploadPic('user',$id,'portrait',140);
			//清空原来的cookie
			setcookie('id',$id,time()-3600);
			$cookieStr = $id.'|'.$token;
			setcookie('id',$cookieStr,$expirationDate);
			$info = DB::table('user')
						->where('id','=',$id)->where('token','=',$token)
						->first(array('id','nick','email',
							'phone','gender','lnum','repostnum',
							'attention','praisenum','fans','opusnum',
							'grade','sportrait','portrait','albums',
							'signature','authtype','addtime','accessToken','expirationDate','thPartid','pwd'));
			if(!empty($info['pwd'])) {
				$info['haspwd'] = 1;
			} else {
				$info['haspwd'] = 0;
			}
			unset($info['pwd']);
			$info['portrait'] = !empty($info['portrait']) ?  $this->poem_url.ltrim($info['portrait'],'.') : '';
			$info['sportrait'] = !empty($info['sportrait']) ? $this->poem_url.ltrim($info['sportrait'],'.') : '';
			$info['bgpic'] = !empty($info['bgpic']) ? $this->poem_url.ltrim($info['bgpic']) : '' ;
			
			$info['hx_uid']=$info["id"];
			$info['hx_password']=ApiEasemob::pwdHash($info["id"]);
			
			//记录用户的登陆ip -- 暂不记录
// 			$this->lastLoginIP($info["id"]);
			if(Input::has('idfa')){
				$idfa = Input::get('idfa');
				$this->checkIdfa($idfa,3);
			}
			//同步ES用户
			$apiEsSearch = App::make('apiEsSearch');
			$apiEsSearch->addEsUser(['id'=>$id,'nick'=>$info['nick'],'pinyin'=>$info['pinyin']]);
			
			return $info;
		}
		/**
		 * 找回密码 -- 新版本废弃
		 * @author:wang.hongli
		 * @since:2016/05/27
		 */
		public function passwordRetake() {
			if(!Input::has('email')) return '请输入注册邮箱或手机号';
			$email = trim(Input::get('email'));
			$pattern = '/^[0-9a-zA-Z\.]+@(([0-9a-zA-Z]+)[.]*)+[a-z]{2,4}$/i';
			$pattern2 = '/^[0-9]*$/i';
			//邮箱
			$code = mt_rand(100000,999999);
			$data = array('code'=>$code);
			$tmpArr = array();
			if(preg_match($pattern,$email)) {
				if(!DB::table('user')->where('email','=',$email)->first(array('id'))) {
					return '此邮箱不存在，请检查书写是否正确';
				}
				if(Mail::send('email',$data,
					function($message) use($code,$email) {
						$message->to($email,'尊敬的用户')->subject('验证码');
						// return 1;
					}
					)) {
					$tmpArr['sign'] = 1;
					$tmpArr['code'] = $code;
					//将code放入到SESSION中
					Session::put('code',$code);
					Session::save();
					return $tmpArr;
				} else {
					return '验证码发送失败，请重试';
				}
			} elseif(preg_match($pattern2,$email)) { //手机号
				if(!DB::table('user')->where('phone','=',$email)->first(array('id'))) {
					return '此手机号不存在，请检查书写是否正确';
				}
				$tmpArr = array('sign'=>2,'code'=>$code);
				if(sendSMS($email,$code)){
					return $tmpArr;
				}else{
					return '验证码发送失败，请重试';
				}
				
			} else {
				return '请输入正确的邮箱或手机号';
			}
		}
		
		//修改密码
		public function modifyPass() {
			// $code = trim(Input::get('code'));
			// $session_code = trim(Session::get('code'));
			// if(empty($code) || empty($session_code) || $code != $session_code)
			// {
			// 	return '验证码错误，请重新获取验证码';
			// }
			$num = trim(Input::get('email'));
			if(empty($num)) return '邮件或者手机号不为空';
			$pattern = '/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
			$pattern2 = '/^[0-9]*$/i';
			$pattern3 = '/^[0-9]{6}$/';
			$newPass = trim(Input::get('newPass'));
			if(empty($newPass)) return '请输入新密码';
			if(!preg_match($pattern3,$newPass))  return '密码必须为六位纯数字';
			$newPass = md5($newPass);
			$time = time();
			if(preg_match($pattern,$num)) {
				DB::table('user')->where('email',$num)->update(array('pwd'=>$newPass,'modifytime'=>$time));
			} else if(preg_match($pattern2,$num)) {
				DB::table('user')->where('phone',$num)->update(array('pwd'=>$newPass,'modifytime'=>$time));
			} else {
				return '请输入正确的邮箱或手机号';
			}
			return true;
		}

		//编辑个人信息
		public function editPersonInfo() {
			$errorMessage = "你的内容中涉及敏感信息，不能公开发布！";
			$nick = trim(Input::get('nick'));
			$password = trim(Input::get('password'));
			$email = trim(Input::get('email'));
			$signature = trim(Input::get('signature'));
			if(!empty($signature) && my_sens_word($signature))
			{
				return $errorMessage;
			}
			if(!empty($nick) && my_sens_word(($nick))){
				return $errorMessage;
			}
			$arr = array();
			//通过cookie获取信息
			$info = $this->viaCookieLogin();
			if($info) {
				$id = $info['id'];
				//修改头像
				$this->uploadPic('user',$id,'portrait',250);
				if(mb_strlen($nick,'utf-8') > 8) return '用户名不能超过八位';
				if($info['nick'] != $nick) {
					$tmpNickRs = DB::table('user')->where('nick','=',$nick)->first();
					if($tmpNickRs) return '此用户已经存在';
					$arr['nick'] = $nick;
					$es_pinyin =  @getPinyin($arr['nick']);
					if(empty($es_pinyin)){
						$es_pinyin = '';
					}
					$arr['pinyin'] = $es_pinyin;
					$apiEsSearch = App::make('apiEsSearch');
					$apiEsSearch->updateEsUser(['id'=>$id,'nick'=>$arr['nick'],'pinyin'=>$es_pinyin]);
				}
				if(!empty($password)) {
					$pattern = '/[0-9]{6}/';
					if(!preg_match($pattern,$password)) return '密码必须为六位纯数字';
					$password = md5($password);
					$arr['pwd'] = $password;
				}
				if(!empty($email)) {
					if($info['email'] != $email) {
						$pattern = '/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
						if(!preg_match($pattern,$email)) return '请检查邮箱格式';
						$tmpEmailRs = DB::table('user')->where('email','=',$email)->first();
						if($tmpEmailRs) return '邮箱已占用';
						$arr['email'] = $email;
					}
				}
				$arr['signature'] = $signature;
				$modifytime = time();
				$arr['modifytime'] = $modifytime;
				if(DB::table('user')->where('id','=',$id)->update($arr)) {
					//同步昵称
					ApiUser::synNickName($id,$nick);
					// return $this->viaCookieLogin();
					$user_info = DB::table('user')->where('id',$id)->first(['id','nick','email','phone',
								'gender','lnum','repostnum','attention',
								'praisenum','fans','opusnum','grade',
								'sportrait','portrait','bgpic','albums','signature',
								'authtype','addtime','accessToken','expirationDate','thPartid','pwd','isdel','isleague']);
					if(!empty($user_info)){
						$user_info['haspwd'] = 1;
					}else{
						$user_info['haspwd'] = 0;
					}
					unset($user_info['pwd']);
					$user_info['portrait'] = !empty($user_info['portrait']) ?  $this->poem_url.ltrim($user_info['portrait'],'.') : '';
					$user_info['sportrait'] = !empty($user_info['sportrait']) ? $this->poem_url.ltrim($user_info['sportrait'],'.') : '';
					$user_info['bgpic'] = !empty($user_info['bgpic']) ? $this->poem_url.ltrim($user_info['bgpic'],'.') : '' ;
					return $user_info;
				}
			} else {
				return 'nolog';
			}
		}

		//拉黑 or 取消拉黑 flag 1 拉黑 2 取消拉黑
		public function editBlackList() {
			$info = $this->viaCookieLogin();
			if($info) {
				$uid = $info['id'];
				$flag = Input::has('flag') ? intval(Input::get('flag')) : 0;
				$fid = Input::has('fid') ? intval(Input::get('fid')) : 0;
				if(empty($fid)) {
					return '操作失败';
				}
				$time = time();
				try {
					if(1==$flag){
						DB::table('blacklist')->insert(array('uid'=>$uid,'fid'=>$fid,'addtime'=>$time));
					}else{
						DB::table('blacklist')->where('uid',$uid)->where('fid',$fid)->delete();
					}
				} catch (Exception $e) {
					return '操作失败';
				}
				return true;
			} else {
				return 'nolog';
			}
		}

		//获取他人信息
		public function getOtherInfo() {
			$info = $this->viaCookieLogin();
			if(!empty($info)) $uid = $info['id'];
			// if(!Input::has('otherId')) return '获取失败';
			$otherId = Input::get('otherId');
			if(empty($otherId)) {
				if(empty($uid)) return 'nolog';
				$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,addtime,bgpic,email,pwd,authconent,teenager,isleague from user where id = $uid";
			} else {
				$otherId = intval($otherId);
				// $otherId = 36;
				$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,addtime,bgpic,email,pwd,authconent,teenager,isleague from user where id = $otherId";
			}
			$rs = DB::select($sql);
			if(empty($rs)) return '获取失败';
			foreach($rs as $key=>&$value) {
				$value['portrait'] = !empty($value['portrait']) ?  $this->poem_url.ltrim($value['portrait'],'.') : '';
				$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
				$value['bgpic'] = !empty($value['bgpic']) ? $this->poem_url.ltrim($value['bgpic']) : '' ;
			}
			//判断关注状态
			if(empty($uid)) {
				$status = 0;
			} else {
				$status = $this->attentionStatus($uid,$otherId);
			}
			//判断是否有密码
			if(empty($rs[0]['pwd'])) {
				$rs[0]['haspwd'] = 0;
			} else {
				$rs[0]['haspwd'] = 1;
			}
			unset($rs[0]['pwd']);
			$rs[0]['relation'] = $status; //关注状态0陌生人，1我->他 2，他->我 3->相互
			
			
			//判断是否有上传夏青杯权限
			$is_cup=0;
			// $sql="select id from user_permission where type=2 and uid='".$rs[0]['id']."'";
			// $rlt = DB::select($sql);
			// if(!empty($rlt)){
			// 	$is_cup=1;
			// }
			$rs[0]['is_cup']=$is_cup;
			$rs[0]['iscup']=$is_cup;
			
			$rs[0]['hx_uid']=$rs[0]['id'];
			$rs[0]['hx_password']=ApiEasemob::pwdHash($rs[0]['id']);
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$rs[0]['ismember'] = $apicheckPermission->isMember($info['id']);

			return $rs;
		}

		//设置主页背景图片
		public function setBgPic() {
			$info = $this->viaCookieLogin();
			if(!empty($info)) {
				$uid = $info['id'];
				//修改头像
				$filePath = $this->isExistDir('bgpic');
				$arr = Input::file('formName');
				if(!empty($arr)) {
					//验证图片格式
					//判断作品类型，只能是图片格式
					$fileType = strtolower(my_file_type($arr->getRealPath()));
					$img_type_arr = array('gif','jpg','jpeg','png');
					if(empty($fileType) || !in_array($fileType, $img_type_arr))
					{
						return '图片格式错误';
					}
					$ext = $arr->guessExtension();
					$name = time().uniqid();
					$name = $name.'.'.$ext;
					$lastFilePath = $filePath.$name;
					$arr->move($filePath,$name);
					$lastFilePath = ltrim($lastFilePath,'.');
					$flag = DB::table('user')->where('id',$uid)->update(array('bgpic'=>$lastFilePath));
					if(!$flag) return '设置背景图片失败';
					return true;
				} else {
					return '设置背景图片失败';
				}
			} else {
				return 'nolog';
			}
		}
		public function bindPhoneNumV2(){
			$info = $this->viaCookieLogin();
			if(!empty($info)){
				$uid = $info['id'];
				$phoneNum = intval(Input::get('phoneNum'));
				if(empty($phoneNum)){
					return '请添加手机号';
				}
				$id = DB::table('user')->where('phone',$phoneNum)->pluck('id');
				if(!empty($id)){
					return '手机号已经绑定';
				}
				$password = Input::get('password');
				//密码只能为1-6位纯数字
				$pattern = '/^[0-9]{6,11}$/i';
				if(!preg_match($pattern, $password))
				{
					return '请输入6位纯数字密码';
				}
				try {
					DB::table('user')->where('id',$uid)->update(array('phone'=>$phoneNum,'pwd'=>md5($password)));
					return true;
				} catch (Exception $e) {
					return '绑定错误，请重试';
				}
			}
		}
		//绑定手机号
		public function bindPhoneNum() {
			$info = $this->viaCookieLogin();
			if(!empty($info)) {
				$uid = $info['id'];
				$phoneNum = intval(Input::get('phoneNum'));
				if(empty($phoneNum)) return '此手机号已被绑定，不能再次绑定';
				//查看是否绑定过
				$id = DB::table('user')->where('phone',$phoneNum)->pluck('id');
				if($id) {
					return '此手机号已被绑定，不能再次绑定';
				}
				try {
					DB::table('user')->where('id',$uid)->update(array('phone'=>$phoneNum));
					return true;
				} catch (Exception $e) {
					return '绑定错误，请重试';
				}
			} else {
				return 'nolog';
			}
		}
		//获取绑定通讯录的用户
		public function getBindList() {
			$info = $this->viaCookieLogin();
			if(!empty($info)) {
				$uid = $info['id'];
				$phoneList = trim(Input::get('phoneList'),',');
				if(empty($phoneList)) return '获取通讯录失败';
				$ids=explode(",",$phoneList);
				//获取所有用户信息
				$sql = "select user.id,user.nick,user.gender,user.grade,user.signature,user.authtype,user.portrait,user.sportrait,user.phone,user.isleague from user where `isdel` = 0 and phone>0 and phone in ('".implode("','",$ids)."')";
				$rs = DB::select($sql);
				if(!empty($rs)) {
					foreach($rs as $key=>&$value) {
						$value['portrait'] = !empty($value['portrait']) ?  $this->poem_url.ltrim($value['portrait'],'.') : '';
						$value['sportrait'] = !empty($value['sportrait']) ? $this->poem_url.ltrim($value['sportrait'],'.') : '';
						//获取关注状态
						$value['relation'] = $this->attentionStatus($uid,$value['id']); //关注状态0陌生人，1我->他 2，他->我 3->相互
					}
					unset($value);
				}
				return $rs;
			} else {
				return 'nolog';
			}
		}

		//极光id和用户id绑定
		public function bindJId() {
			$info = $this->viaCookieLogin();
			$jId = Input::get('jId');
			$deviceType = Input::get('deviceType');
			if(empty($jId)) return '绑定失败';
			$deviceType = !empty($deviceType) ? 3 : 4;
			try {
				DB::table('jpush')->where('jid',$jId)->delete();
				DB::table('jpush')->where('uid',$info['id'])->delete();
			} catch (Exception $e) {
			}
			//登录状态
			if($info) {
				$uid = $info['id'];
				try {
					if(DB::table('jpush')->insert(array('uid'=>$uid,'jid'=>$jId,'devicetype'=>$deviceType))) {
						return true;
					} else {
						return '绑定失败';
					}
				} catch (Exception $e) {
					DB::table('jpush')->where('uid',$uid)->update(array('jid'=>$jId));
					return true;			
				}
			} else {
				return 'nolog';
			}
		}
		//第三方登陆，只修改密码
		public function thirdPartEditPass() {
			$info = $this->viaCookieLogin();
			if($info) {
				$uid = $info['id'];
				$flag = !empty(Input::get('flag')) ? 1 : 0; //1独立修改密码0第三方修改密码
				$pattern = '/^[0-9]{6}$/i';
				if(empty($flag)) {
					$password = trim(Input::get('password'));
					if(!preg_match($pattern, $password)) return '密码必须为六位纯数字';
					$password = md5($password);
				} else {
					$oldPassword = trim(Input::get('oldPassword'));
					if(empty($oldPassword)) return '请输入原始密码';
					$oldPassword = md5($oldPassword);
					$tmpRs = DB::table('user')->where('id','=',$uid)->where('pwd','=',$oldPassword)->first(array('id'));
					if(empty($tmpRs)) return '原始密码错误，请重新输入';
					$password = trim(Input::get('password'));
					if(empty($password)) return '新密码不能为空';
					if(!preg_match($pattern,$password)) return '密码必须为六位纯数字';
					$password = md5($password);
				}
				$time = time();
				try {
					DB::table('user')->where('id',$uid)->update(array('pwd'=>$password,'modifytime'=>$time));
					return true;
				} catch (Exception $e) {
					return '密码设置失败，请重试';
				}
			} else {
				return 'nolog';
			}
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

					$notice_rs = array(
							'action'=>0,
							'type'=>6,
							'uid'=>$id,
							'fromid'=>$id,
							'toid'=>$value['id'],
							'opusid'=>0,
							'name'=>'',
							'addtime'=>time(),
							'content'=>'',
							'commentid'=>0
					);
					$distributeMessage = new DistributeMessage();
					$distributeMessage->distriMessage($notice_rs);
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

		//报名读诗用户
		public function signUpUser() {
			$info = $this->viaCookieLogin();
			$uid = 0;
			if(!empty($info))
			{
				$uid = $info['id'];
			}
			$name = trim(Input::get('name'));
			$company = trim(Input::get('company'));
			$school = trim(Input::get('school')); //报名学校
			$tel = trim(Input::get('tel'));
			$reason = trim(Input::get('reason'));
			$addtime = time();

			if(empty($name) || empty($company) || empty($school) || empty($tel) || empty($reason))
				return '请检测信息是否写全';
			try {
				DB::table('signup')->insert(array('uid'=>$uid,'name'=>$name,'company'=>$company,'school'=>$school,'tel'=>$tel,'reason'=>$reason,'addtime'=>$addtime));
				return true;
			} catch (Exception $e) {
				return '提交失败，请重试';
			}
			return true;
		}
		
		/*
		* 更新用户登录ip地址
		*/
		public function lastLoginIP($uid){
			try {
				$rlt = DB::table('user_last_ip')->where('uid','=',$uid)->first();
				$ip = GetIP();
				if(!empty($rlt)){
					DB::table('user_last_ip')->where('uid',$uid)->update(array('ip'=>$ip,'last_time'=>time()));
				}else{
					DB::table('user_last_ip')->insert(array('uid'=>$uid,'ip'=>$ip,'last_time'=>time()));
				}
			} catch (Exception $e) {
			}
			return true;
			
		}
		
		/*
		* 昵称同步
		*/
		public static function synNickName($uid,$nick){
			try {
				//同步到赛事
				$nick = addslashes($nick);
				DB::table('user_match')->where('uid',$uid)->update(array('nick_name'=>$nick));
				//同步到朗诵会会员
				DB::table('league')->where('uid',$uid)->update(array('nick_name'=>$nick));
			} catch (Exception $e) {
			}
			return true;
		}
		
		/**
		 * ＠根据idfa来判断今日头条用户质量
		 * @author:wang.hongli
		 * ＠since:2016/06/12
		 * @param:idfa ios传递的值
		 * @param:status １注册２登陆３第三方登陆
		 */
		private function checkIdfa($idfa=0,$status=1){
			if(empty($idfa) || empty($status)){
				return false;
			}
			try {
				$data = array('idfa'=>$idfa,'status'=>$status);
				DB::table('jinri_statistics_click')->where('idfa',$idfa)->update($data);
			} catch (Exception $e) {
			}
		}

		/**
		*add_activer_user
		*@author:wang.hongli
		*@since:2016/06/15
		**/
		public function addActiveUser(){
			$info = $this->viaCookieLogin();
			if(empty($info['id'])){
				return false;
			}
			//标记用户为活跃用户
			if(!empty($info['id'])){
				$redisNotification = new RedisActiveUser();
				$redisNotification->addActiveUser($info['id']);

				//七天活跃用户--用户推关注听条目
				$gender = isset($info['gender']) ? $info['gender'] : 0;
				$uid = DB::table('week_active_user')->where('uid',$info['id'])->take(1)->pluck('uid');
				if(empty($uid)){
					DB::table('week_active_user')->insert(['uid'=>$info['id'],'gender'=>$gender,'addtime'=>time()]);
				}else{
					DB::table('week_active_user')->where('uid',$info['id'])->update(['addtime'=>time()]);
				}

			}
		}

		protected function delOldUserCookieToken($id=0){
			if(empty($id)){
				return false;
			}
			// 删除上一个cookie
			$old_token = DB::table('user')->where('id',$id)->pluck('token');
			if(!empty($old_token)){
				$redisUserInfo = new RedisUserInfo();
				$redisUserInfo->delUserCookieFromRedis($id,$old_token);
			}
		}
	}
