<?php 
/**
*	微信登录
*	@author:wang.hongli
*	@since:2015/07/01
**/
class WeixinOauthClass 
{
	protected $appid;
	protected $secretkey;
	protected $callback;
	protected $apiurl = 'https://open.weixin.qq.com';
	protected $access_token;
	protected $openid;

	function __construct($appid,$secretkey,$callback='')
	{
		$this->appid = $appid;
		$this->secretkey = $secretkey;
		$this->callback = $callback;
	}

	/**
	*	file_get_contents方式或者curl方式获取指定url内容
	*	@author:wang.hongli
	*	@since:2015/07/01
	**/
	function get_url_contents($url)
	{
		if(ini_get('allow_url_fopen' == "1"))
		{
			$response = file_get_contents($url);
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
		}
		return $response;
	}

	/**
	*	拼接跳转到微信获取code地址
	*	@author:wang.hongli
	*	@since:2015/07/01
	*	@param:state 一份放入memcache，一份会原样返回，用于防治csrf攻击
	**/
	function redirect_to_login($state)
	{
		$redirect = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid;
		$redirect .= '&redirect_uri='.urlencode($this->callback);
		$redirect .= '&response_type=code&scope=snsapi_login&state='.$state.'#wechat_redirect';

		return $redirect;
	}

	/**
	*	获取access_token
	*	@author:wang.hongli
	*	@since:2015/07/01
	**/
	function get_access_token($code)
	{

		$data = array();

		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid;
		$url .= '&secret='.$this->secretkey;
		$url .= '&code='.$code;
		$url .= '&grant_type=authorization_code';
		$response = json_decode($this->get_url_contents($url),true);
		if(isset($response['errcode']))
		{
			return array('errorcode'=>$response['errorcode'],'msg'=>$response['errmsg']);
		}
		else
		{
			$this->openid = $response['openid'];
			return $response;
		}
	}
	/**
	*	通过refresh_token 刷新 access_token
	*	@author:wang.hongli
	*	@since:2015/07/02
	**/	
	function refresh_token($refresh_token)
	{
		if(empty($refresh_token))
		{
			return array('errorcode'=>40030,'msg'=>'刷新失败，请重试');
		}
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->appid;
		$url .= '&grant_type=refresh_token&refresh_token='.$refresh_token;

		$response = json_decode($this->get_url_contents($url),true);

		if(!empty($response['errcode']))
		{
			return array('errorcode'=>$response['errorcode'],'msg'=>$response['errmsg']);
		}
		else
		{
			$data['access_token'] = $response['access_token'];
			// $data['refresh_token'] = $response['refresh_token'];
			$this->openid = $response['openid'];
			// 将access_token refresh_token,openid 返回调用方法
			return $data;
		}
	}
	/**
	*	验证access_token是否有效
	*	@author:wang.hongli
	*	@since:2015/07/02
	**/
	function isavaliable_access_token($access_token)
	{
		if(empty($access_token))
		{
			return false;	
		}
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token;
		$url .= '&openid='.$this->openid;
		$response = json_decode($this->get_url_contents($url),true);
		if($response['errorcode'] == 40003)
		{
			return false;
		}
		else if($response['errorcode'] == 0)
		{
			return true;
		}
	}

	/**
	*	获取个人信息
	*	@author:wang.hongli
	*	@since:2015/07/02
	**/
	function get_user_info($access_token)
	{
		$data = array();
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token;
		$url .= '&openid='.$this->openid;
		$data = json_decode($this->get_url_contents($url),true);
		return $data;
	}
}
 ?>