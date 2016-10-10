<?php

/**
 * 发送短信验证码通用模型
 * @author:wang.hongli
 * @since:2016/05/27
 */
class ApiSendSMS extends  ApiCommon{
	
	
	/**
	 * 发送短信消息
	 * @author:wang.hongli
	 * @since:2016/05/27
	 * @param:phone 手机号码 code 验证码 
	 * @param:flag 1 注册 2 绑定手机号
	 */
	public function sendSMS($phone,$code,$flag=1){
		$rules = array(
				'phone'=>'required|regex:/^[1][3-9]{1}\d{9}$/',
				'code'=>'required|regex:/^[0-9]{6}$/',
				'flag'=>'required|in:1,2',
		);
		$message = array(
				'phone.required'=>'请填写手机号',
				'phone.regex'=>'手机号格式错误',
				'code.required'=>'请填写验证码',
				'code.regex'=>'验证码格式错误',
				'flag.required'=>'标示错误',
				'flag.in'=>'请填写正确的标识'
		);
		$data = array('phone'=>$phone,'code'=>$code,'flag'=>$flag);
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			return $validator->messages()->first();
		}
		switch($flag){
			case 1:
				$id = DB::table('user')->where('phone',$phone)->pluck('id');
				if($id){
					return '手机号已经存在';
				}
				break;
			case 2:
				$id = DB::table('user')->where('phone',$phone)->pluck('id');
				if($id){
					return '您的手机号已存在';
				}
				break;
		}
		set_time_limit(0);
		header('Content-type:text/html;charset=utf-8');
		define('SCRIPT_ROOT',public_path().'/packages/yimeinotev2/');
		require_once SCRIPT_ROOT.'include/Client.php';
		require_once SCRIPT_ROOT.'nusoaplib/nusoap.php';
		$gwUrl = 'http://hprpt2.eucp.b2m.cn:8080/sdk/SDKService?wsdl';
		$serialNumber = '8SDK-EMY-6699-RIQNR';
		$password = '365521';
		$sessionKey = '365521';
		$connectTimeOut = 2;
		$readTimeOut = 10;
		$proxyhost = false;
		$proxyport = false;
		$proxyusername = false;
		$proxypassword = false;
		$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
		$client->setOutgoingEncoding("UTF-8");
		//发送短信
		$statusCode = $client->sendSMS(array($phone),'【为你诵读】'.'您的验证码为'.$code.'，欢迎继续“为你诵读”！');
		$tmpArr['code'] = $code;
		//将code放入到SESSION中
		return $tmpArr;
	}





	
}