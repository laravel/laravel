<?php 
/**
 * 微信提现相关
 */
class ApiWeiXinMoneyController extends ApiCommonController
{
		private $ApiWeiXinMoney = null;
		private $appid='wxacdb47e17373bccd';
		private $appsecret='cea4e65e743bba484ffbae8a456ebb53';
		private $shopid='1379058902';
		public function __construct() {
			$this->ApiWeiXinMoney = new ApiWeiXinMoney();
		}
	 //添加用户微信unionid
	 public function getUserUnionid(){
		if(Input::get('uid')==''){
            $this->setReturn(-101,'nolog');return;
        	}
		if(Input::get('unionid')==''){
			$this->setReturn(-1,'no_unionid');return;
			}
			$unionid=Input::get('unionid');
			$rs = $this->ApiWeiXinMoney->getUserUnionid(Input::get('uid'),$unionid);
			if($rs){
				$this->setReturn(1,'success');return;
			}else{
				$this->setReturn(2,'repice');return;
			}
	 }




	 //微信回调地址
	 public function weixin(){
		// 获取到微信请求里包含的几项内容
		$signature =Input::get('signature');
		$timestamp =Input::get('timestamp');
		$nonce     =Input::get('nonce');
		$a=Input::get('echostr');
		// ninghao 是我在微信后台手工添加的 token 的值
		$token = 'qwertyu';

		// 加工出自己的 signature
		$our_signature = array($token, $timestamp, $nonce);
		sort($our_signature, SORT_STRING);
		$our_signature = implode($our_signature);
		$our_signature = sha1($our_signature);

		// 用自己的 signature 去跟请求里的 signature 对比
		if ($our_signature != $signature) {
			echo "";
		}else{
			echo  $a;
			}
		//得到相应信息
		$postData = $GLOBALS["HTTP_RAW_POST_DATA"];
		if($postData ){
		$obj=simplexml_load_string($postData);
		$openid=$obj->FromUserName;
		$this->userInfo($openid);

		}
	}


	 //获得token
	 public function get_token(){
		 $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
		 $resule=file_get_contents($url);
		 $resule=json_decode($resule,true);
		 $resule["time"]=time();
		 $access_token=$resule['access_token'];
		
		 $resule=json_encode($resule);
		 file_put_contents("weixin.txt",$resule);
		 return $access_token;
		
	 }
	 //判断token是否过期
	 public function gettoken(){
		$token=file_get_contents('weixin.txt');
		$token=json_decode($token,true);
		if($token['time']+$token['expires_in'] > time()){
			return $token['access_token'];
		}else{
			return $this->get_token();
			
		}

	 }
	 
	public function get_alluser(){
	 
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->gettoken()."&openid=oIRTmv3QmqQ7u01OvmV5PYwd4Uns&lang=zh_CN";
		$a=file_get_contents($url);
		$a_arr=json_decode($a,true);
		print_r($a_arr);
	 }
	 //将得到的xml写入数据库
	public function userInfo(){
		require_once ("../app/ext/weixin/classes/RequestHandler.class.php");
		require_once ("../app/ext/weixin/classes/ResponseHandler.class.php");
		require ("../app/ext/weixin/classes/client/TenpayHttpClient.class.php");
		require_once ("../app/ext/weixin/tenpay_config.php");
		$reqHandler = new RequestHandler();
		$reqHandler->init($APP_ID, $APP_SECRET, $PARTNER_KEY, $APP_KEY);
        $Token = $reqHandler->GetToken();
        $this->user = new ApiUser();
		$openid = 'owZYbuDFTWjJowA-OQxp3OPEUW8c';
        $userinfo = $this->user->getWeiXinUserInfo($Token,$openid);
        $a_arr = json_decode($userinfo['data'],true);
		if(!empty($a_arr) && $a_arr != ''){
		    $rs=$this->ApiWeiXinMoney->getUserInfo($a_arr['openid'],$a_arr['nickname'],$a_arr['sex'],$a_arr['city'],$a_arr['province'],$a_arr['country'],$a_arr['unionid']);
		}
		print_r($userinfo);
		die();
		/**
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$Token."&openid=$openid&lang=zh_CN";
		$a = file_get_contents($url);
        print_r($a);

		die();
		$a_arr=json_decode($a,true);
		$rs=$this->ApiWeiXinMoney->getUserInfo($a_arr['openid'],$a_arr['nickname'],$a_arr['sex'],$a_arr['city'],$a_arr['province'],$a_arr['country'],$a_arr['unionid']);
		*/
		
	 }
	 //判断用户是否授权或者关注
	 public function checkUser(){
		$uid =Input::get('uid');
		if($uid=='') {
			$this->setReturn(-101,$this->nolog);return ;
		}
		$rs = $this->ApiWeiXinMoney->checkUser($uid);
		if($rs=='no_sq'){
			$this->setReturn(-1,'未授权',array(),0);return ;
		}else if($rs=='no_gz'){
			$this->setReturn(-2,'未关注',array(),0);return ;
		}else{
			$this->setReturn(1,'可以提现',array(),0);return ;
		}
	 }
	 //用户提现申请
	 public function tocash(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(!$user_info) {
			$this->setReturn(-101,$this->nolog);return ;
		}
		//提现数量
		$num = Input::get('num',0);
		if(!$num){
			$this->setReturn(-1,'提现数量错误',array(),0);return ;
		}	
		$rs=$ApiWeiXinMoney->tocash($user_info['id'],$num);
		if($rs){
			$this->setReturn(1,'提现成功',array(),0);return ;
		}else{
			$this->setReturn(2,'提现失败',array(),0);return ;
		}	
	 }
    //发红包
	public function givemoney($id){
		
		$info=DB::table('weixin_cash')->where('id',$id)->first();

		//openid  查表
		$rs=$ApiWeiXinMoney->getopenid($info['uid']);
		$openid=$rs['openid'];
		$appid=$this->appid;//公众号id
		$mchid=$this->shopid;//商户号id
		$time=date('Ymd',time());
		$nonce_str=$this->getRandom(32);//随机字符串
		//订单号
		$partner_trade_no=$time.time().mt_rand(10000,99999);
		$ip='192.168.1.1';//调用接口的机器Ip地址
		$key='';//商户平台密钥

		$stringA="appid=$appid&amount=$money&check_name=NO_CHECK&desc=红包提现!&id=$id&mch_id=$mch_id&mchid=$mchid&nonce_str=$nonce_str&openid=$openid&partner_trade_no=$partner_trade_no";
		$stringSignTemp="$stringA&key=$key";
		$sign=strtoupper(MD5(stringSignTemp));
		$xml="
		<xml>
		<mch_appid>$appid</mch_appid>
		<mchid>$mchid</mchid>
		<nonce_str>$nonce_str</nonce_str>
		<partner_trade_no>$partner_trade_no</partner_trade_no>
		<openid>$openid</openid>
		<check_name>NO_CHECK</check_name>
		<amount>$money</amount>
		<desc>红包提现!</desc>
		<spbill_create_ip>$ip</spbill_create_ip>
		<sign>$sign</sign>
		</xml>";
		$url="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
		
		$result=$this->postCurl($url,$xml,"","POST");
		$obj=simplexml_load_string($result);
		$return_code=$obj->return_code;
		$result_code=$obj->result_code;
		if($result_code=="SUCCESS" && $return_code=="SUCCESS"){

			DB::table('weixin_cash')->where('id',$id)->update(array('flag'=>1));
			return 1;

		}else{
			return 0;
		}



	}


	public function postCurl($url, $option, $header = '', $type = 'POST') {
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $option )) {
			$options = json_encode ( $option );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		if(!empty($header)){
			curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		}
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
		$result = curl_exec ( $curl ); // 执行操作
		//$res = object_array ( json_decode ( $result ) );
		//$res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		//pre ( $res );
		curl_close ( $curl ); // 关闭CURL会话
		return $result;
	}
	

	//  调用：getRandom(32)
	//  输出结果：一个32位随机数
    public  function getRandom($param){
    $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for($i=0;$i<$param;$i++)
     {
         $key .= $str{mt_rand(0,32)};    //生成php随机数
     }
     return $key;
 }

 
}