<?php
/**
 * 请求类
 * ============================================================================
 * api说明：
 * init(),初始化函数，默认给一些参数赋值，如cmdno,date等。
 * getGateURL()/setGateURL(),获取/设置入口地址,不包含参数值
 * getKey()/setKey(),获取/设置密钥
 * getParameter()/setParameter(),获取/设置参数值
 * getAllParameters(),获取所有参数
 * getRequestURL(),获取带参数的请求URL
 * getDebugInfo(),获取debug信息
 * 
 * ============================================================================
 *
 */
class RequestHandler {
	
	/** Token获取网关地址*/
	var $tokenUrl;
	
	/**预支付网关url地址 */
	var $gateUrl;
	
	/** 商户参数 */
	var $app_id, $partner_key, $app_secret, $app_key;

	/**  Token */
	var $Token;

	/** debug信息 */
	var $debugInfo;

	function __construct(){
		$this->RequestHandler();
	}
	function RequestHandler(){
		$this->tokenUrl		= 'https://api.weixin.qq.com/cgi-bin/token';
		$this->gateUrl		= 'https://api.weixin.qq.com/pay/genprepay';
		$this->notifyUrl	= 'https://gw.tenpay.com/gateway/simpleverifynotifyid.xml';
	}
	/**
	*初始化函数。
	*/
	function init($appid, $appsecret,$partnerkey, $appkey) {
		$this->debugInfo	= '';
		$this->Token		= '';
		$this->app_id		= $appid;
		$this->partner_key	= $partnerkey;
		$this->app_secret	= $appsecret;
		$this->app_key		= $appkey;
	}
	/**
	*获取debug信息
	*/
	function getDebugInfo() {
		$res = $this->debugInfo;
		$this->debugInfo = '';
		return $res;
	}

	//
	function httpSend($url, $method, $data){
		$client = new TenpayHttpClient();
		$client->setReqContent($url);
		$client->setMethod($method);
		$client->setReqBody($data);
		$res =  '';
		if( $client->call()){
			$res =  $client->getResContent();
		}
		//设置debug信息
		$this->_setDebugInfo('Req Url:' .$url);
		$this->_setDebugInfo('Req data:' .$data);
		$this->_setDebugInfo('Res Content:' .$res);

		return $res;
	}

	//获取TOKEN，一天最多获取200次
	function GetToken(){
		$url= $this->tokenUrl . '?grant_type=client_credential&appid='.$this->app_id .'&secret='.$this->app_secret;
		$json=$this->httpSend($url,'GET','');
		if( $json != ""){
			$tk = json_decode($json);
			if( $tk->access_token != "" )
			{
				$this->Token =$tk->access_token;
			}else{
				$this->Token = '';
			}
		}
		//设置debug信息
		$this->_setDebugInfo('tokenUrl:' .$url);
		$this->_setDebugInfo('tokenRes jsonContent:' .$json);
		return $this->Token;
	}

	/**
	*创建package签名
	*/
	function createMd5Sign($signParams) {
		$signPars = '';
		
		ksort($signParams);
		foreach($signParams as $k =>$v) {
			if($v != "" && 'sign' !=$k) {
				$signPars .= $k . '=' .$v.'&';
			}
		}
			$signPars .= 'key=' .$this->partner_key;
		
		$sign = strtoupper(md5($signPars));	
		//debug信息
		$this->_setDebugInfo('md5签名:'.$signPars . ' => sign:' .$sign);

		return $sign;
		
	}	

	//获取带参数的签名包
	function genPackage($packageParams){
		
		$sign = $this->createMd5Sign($packageParams);
		$reqPars = '';
		foreach ($packageParams as $k =>$v ){
			$reqPars.=$k . '='.URLencode($v) . '&';
		}
		$reqPars = $reqPars . 'sign=' .$sign;
		//debug信息
		$this->_setDebugInfo('gen package:' .$reqPars);

		return $reqPars;
	}
	
	//创建签名SHA1
	function createSHA1Sign($packageParams){
		$signPars = '';
		ksort($packageParams);
		foreach($packageParams as $k=> $v) {
			if($signPars == ''){
				$signPars =$signPars .$k. '=' .$v;
			}else{
				$signPars =$signPars. '&' .$k. '=' .$v;
			}
		}

		$sign = SHA1($signPars);
		
		//debug信息
		$this->_setDebugInfo('sha1:' .$signPars .'=>'. $sign);

		return $sign;		
	}
	
	//提交预支付
	function sendPrepay($packageParams){

		$prepayid=null;

		$reqPars= json_encode($packageParams);
		

		$url= $this->gateUrl .'?access_token='.$this->Token;

		$json=$this->httpSend($url,'POST',$reqPars);
		$tk= json_decode($json);
		//echo "aaaaaaaaaaaaaaaaaaa".$json."ccccccccccccccccccccc<br>";
		if ( $tk->errcode == 0){

			$prepayid= $tk->prepayid;
		}


		return $prepayid;
	}
	/**
	*设置debug信息
	*/
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = PHP_EOL.$this->debugInfo.$debugInfo.PHP_EOL;
	}
}
?>