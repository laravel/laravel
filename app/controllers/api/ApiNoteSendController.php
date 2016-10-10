<?php 

	/**
	* 亿美通短信接口
	**/
	class ApiNoteSendController extends ApiCommonController {

		public function sendMsg() {
			set_time_limit(0);
			header('Content-type:text/html;charset=utf-8');
			define('SCRIPT_ROOT',public_path().'/packages/yimeinote/');
			require_once SCRIPT_ROOT.'include/Client.php';
			require_once SCRIPT_ROOT.'nusoaplib/nusoap.php';

			$gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService';
			$serialNumber = '3SDK-EMY-0130-LGXRL';
			$password = '926442';
			$sessionKey = '378626';
			$connectTimeOut = 2;
			$readTimeOut = 10;
			$proxyhost = false;
			$proxyport = false;
			$proxyusername = false;
			$proxypassword = false;

			$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
			$client->setOutgoingEncoding("UTF-8");
			//发送短信	
			$randomCode = mt_rand(100000,999999);
			$randomCode = 123456;		
			// $statusCode = $client->sendSMS(array('18611985381'),'【首善音乐】'.'您的为你读诗验证码为'.$randomCode.'。欢迎使用"随身配乐朗诵录音棚"');
			$statusCode = $client->sendSMS(array('18611985381'),'【为你读诗】'.'您的验证码为'.$randomCode.'，欢迎继续“为你读诗”！');
			if(empty($statusCode)) return '发送验证码失败，请重试';
		}
	}