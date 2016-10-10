<?php 
/**
 * 微信相关控制器
 */
class ApiWeiXinController extends BaseController
{
	// 公众平台
	private $apiWeiXin;
	private $appId = 'wx1cda69b2ea12c74e';
	private $appSecret = '29bc0ef9570da110ecbb27c264e5d078';
		// 开发者账号
	private $dev_appId = 'wx9a1e48891a86f0a6';
	private $dev_appSecret = 'c2ec49a903d7a3190ccfa2c319267bef';
	private $dev_callback = 'http://weinidushi.com.cn/api/weixinLogin';

	public function __construct()
	{
		$this->apiWeiXin = new ApiWeiXin($this->appId,$this->appSecret);
	}
	/**
	 * 获取微信需要的签名
	 * @return [type] [description]
	 */
	public function getSignPackage()
	{
		// header("Access-Control-Allow-Origin：http://www.weinidushi.com.cn");
		$data = $this->apiWeiXin->getSignPackage();
		echo json_encode($data);
	}

	/**
	 * 微信录音接口
	 * @author:wang.hongli
	 * @since:2015/11/15
	 */
	public function record()
	{
		// header("Access-Control-Allow-Origin：http://www.weinidushi.com.cn");
		$data = $this->apiWeiXin->getSignPackage();
		return View::make('apiweixin.record')->with(array('signPackage'=>$data));
	}

	/**
	*	微信分享接口
	*	@author:wang.hongli
	*	@since:2016/01/14
	**/
	public function recordShare($id=0)
	{
		$id = intval($id);
		$rs = DB::table('weixinvoice')->where('id',$id)->first();
		//根据id作品id获取用户信息
		$user_info = DB::table('weixinuser')->where('id',$rs['uid'])->first();
		$default_portrait = 'http://weinidushi.com.cn/img/weixin/logo.png';
		if(!empty($user_info))
		{
			$rs['name'] = $user_info['name'];
			if(is_numeric($user_info['portrait']))
			{
				$user_info['portrait'] = $default_portrait;
			}
			$rs['portrait'] = $user_info['portrait'];
		}
		else
		{
			$rs['name'] = '为你读诗';
			$rs['portrait'] = 'http://weinidushi.com.cn/img/weixin/logo.png';
		}
		return View::make('apiweixin.recordShare')->with('rs',$rs);
	}

	/**
	*	获取微信授权相关东西
	*	@author:wang.hongli
	*	@since:2015/01/21
	*/
	public function getWeiXinAccess()
	{
		$checkinfo = array();
		$redirect_url = Config::get('app.url').'/api/record';
		if(isset($_COOKIE['id']))
		{
			$checkinfo = unserialize($_COOKIE['id']);
		}
		// 如果id非空，查询数据库，获取用户信息
		if(!empty($checkinfo))
		{
			$id = intval($checkinfo['id']);
			// token在用户临时票据
			if(isset($checkinfo['salt']))
			{
				$salt = htmlspecialchars($checkinfo['salt']);
			}
			$user_info = DB::table('weixinuser')->where('id',$id)->where('salt',$salt)->first();
			if(empty($user_info))
			{
				// 跳转到授权页面,重新授权
				$weicinOauthClass = new WeixinOauthClass($this->dev_appId,$this->dev_appSecret,$this->dev_callback);
				$state = md5('j@1a2b3c4d#*^');
				$weixinurl =$weicinOauthClass->redirect_to_login($state);
				return Redirect::to($weixinurl);
			}
			else
			{
				// 如果用户信息存在，直接跳转到录音页面
				// 重新设置cookie
				$user_id = $user_info['id'];
				$salt = $user_info['salt'];
				$cookie = serialize(array('id'=>$user_id,'salt'=>$salt));
				setcookie('id',$cookie,time()+86400*30);
				return Redirect::to($redirect_url);
			}
		}
		else
		{
			// 如果获取不到cookie,授权
			$weicinOauthClass = new WeixinOauthClass($this->dev_appId,$this->dev_appSecret,$this->dev_callback);
			$state = md5('j@1a2b3c4d#*^');
			$weixinurl =$weicinOauthClass->redirect_to_login($state);
			return Redirect::to($weixinurl);
		}
	}
	/**
	*	@author:wang.hongli
	*	@since:2016/01/22
	*	微信授权后跳转地址 -- 用于大拜年
	**/
	public function weixinLogin()
	{
		$get_code = $_GET['code'];
		$get_state = $_GET['state'];
		$redirect_url = Config::get('app.url').'/api/record';
		if(empty($get_code))
		{
			return Redirect::to($redirect_url);
		}
		$state = md5('j@1a2b3c4d#*^');
		if($get_state != $state)
		{
			return Redirect::to($redirect_url);
		}
		$weixinOauthClass = new WeixinOauthClass($this->dev_appId,$this->dev_appSecret,$this->dev_callback);
		$access_token = $weixinOauthClass->get_access_token($get_code);
		if(isset($access_token['errorcode']))
		{
			return Redirect::to($redirect_url);
		}
		$user_info = $weixinOauthClass->get_user_info($access_token['access_token']);
		$salt = md5(md5(mt_rand(10000,99999)));
		$arr = array(
			'name'=>$user_info['nickname'],
			'portrait'=>rtrim($user_info['headimgurl'],0).'64',
			'unionid'=>$user_info['unionid'],
			'salt'=> $salt
		);
		$flag = DB::table('weixinuser')->where('unionid',$user_info['unionid'])->first();
		if(!$flag)
		{
			$user_id = DB::table('weixinuser')->insertGetId($arr);
		}
		else
		{
			$user_id = $flag['id'];
			$salt = $flag['salt'];
		}
		$cookie = serialize(array('id'=>$user_id,'salt'=>$salt));
		setcookie('id',$cookie,time()+86400*30);
		// 跳转到录音页面
		return Redirect::to($redirect_url);
	}

	/**
	*	微信上传作品接口
	*	@author:wang.hongli
	*	@since:2016/01/03
	*/
	public function weixinUploadFile()
	{

		$app_url = Config::get('app.url');
		$path = public_path();	
		$save_dir = $path.'/upload/weixinvoice/';
		$dir_name = strtotime(date('Y-m-d'));
		if(!file_exists($save_dir.$dir_name))
		{
			mkdir($save_dir.$dir_name,0755,true);
		}
		// 下载的媒体id
		$media_id = Input::get('media_id');

		if(empty($media_id))
		{
			return 0;
		}
		$access_token = $this->apiWeiXin->getAccessToken();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;
		//	获取视频流
  		$voice_stream = file_get_contents($url);  
      	$path = $save_dir.$dir_name.'/'.$media_id.'.amr';
		@file_put_contents($path, $voice_stream);
		$des_path = $save_dir.$dir_name.'/'.$media_id.'.mp3';
      	exec("/usr/bin/ffmpeg -i ".$path." ".$des_path);
      	// 删除文件
      	@unlink($path);
      	// 获取目标文件时长
		$ffmpegInstance = new ffmpeg_movie($des_path);
		$time = $ffmpegInstance->getDuration();
		// 录音时间向上取整
		if(!empty($time))
		{
			$time = round($time);
		}	
		else
		{
			$time = 0;
		}
		$voice_path = $app_url.'/upload/weixinvoice/'.$dir_name.'/'.$media_id.'.mp3';
		// 将生成的音频入库
		$uid = 0;
		$userportrait = 'http://weinidushi.com.cn/img/weixin/logo.png';
		$username = '为你读诗';
		if(!empty($_COOKIE['id']))
		{
			$user_info = unserialize($_COOKIE['id']);
			$uid = intval($user_info['id']);
			// 获取用户头像,昵称等信息
			$info = DB::table('weixinuser')->where('id',$uid)->first();
			if(!empty($info))
			{
				$userportrait = $info['portrait'];
				$username = $info['name'];
			}
		}
		// $sql = "insert into weixinvoice (uid,voice_path,time) values ($uid,'{$voice_path}',$time);";
		$weixin_arr = array('uid'=>$uid,'voice_path'=>$voice_path,'time'=>$time);
		$id = DB::table('weixinvoice')->insertGetId($weixin_arr);
		echo json_encode(array('id'=>$id,'voice_path'=>$voice_path,'userportrait'=>$userportrait,'username'=>$username));
		return;
		
	}
}