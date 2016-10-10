<?php
//header('Content-type: text/json');
//header('Content-type: text/html; charset=gb2312');
//---------------------------------------------------------
//微信支付服务器签名支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------
require_once ("classes/RequestHandler.class.php");

require_once ("./tenpay_config.php");

require_once ("classes/ResponseHandler.class.php");

require ("./classes/client/TenpayHttpClient.class.php");

//获取提交的商品价格
$order_price=trim($_GET['order_price']);
if($order_price == ''){
	$order_price = '1';
}

//获取提交的商品名称
$product_name=trim($_GET['product_name']);
if ($product_name == ''){
	$product_name = '测试商品名称';
}

//获取提交的订单号
$out_trade_no=trim($_GET['order_no']);
if ($out_trade_no == ''){
	$out_trade_no = time();
}


$outparams =array();
//商品价格（包含运费），以分为单位
$total_fee= $order_price*100;
//输出类型
$out_type	= strtoupper($_GET['out_type']);
$plat_from	= strtoupper($_GET['plat']);
//获取token值
$reqHandler = new RequestHandler();
$reqHandler->init($APP_ID, $APP_SECRET, $PARTNER_KEY, $APP_KEY);
$Token= $reqHandler->GetToken();
if ( $Token !='' ){
	//=========================
	//生成预支付单
	//=========================
	//设置packet支付参数
	$packageParams =array();		
	
	$packageParams['bank_type']		= 'WX';	            //支付类型
	$packageParams['body']			= $product_name;					//商品描述
	$packageParams['fee_type']		= '1';				//银行币种
	$packageParams['input_charset']	= 'GBK';		    //字符集
	$packageParams['notify_url']	= $notify_url;	    //通知地址
	$packageParams['out_trade_no']	= $out_trade_no;		        //商户订单号
	$packageParams['partner']		= $PARTNER;		        //设置商户号
	$packageParams['total_fee']		= $total_fee;			//商品总金额,以分为单位
	$packageParams['spbill_create_ip']= $_SERVER['REMOTE_ADDR'];  //支付机器IP
	//获取package包
	$package= $reqHandler->genPackage($packageParams);
	$time_stamp = time();
	$nonce_str = md5(rand());
	//设置支付参数
	$signParams =array();
	$signParams['appid']	=$APP_ID;
	$signParams['appkey']	=$APP_KEY;
	$signParams['noncestr']	=$nonce_str;
	$signParams['package']	=$package;
	$signParams['timestamp']=$time_stamp;
	$signParams['traceid']	= 'mytraceid_001';
	//生成支付签名
	$sign = $reqHandler->createSHA1Sign($signParams);
	//增加非参与签名的额外参数
	$signParams['sign_method']		='sha1';
	$signParams['app_signature']	=$sign;
	//剔除appkey
	unset($signParams['appkey']); 
	//获取prepayid
	$prepayid=$reqHandler->sendPrepay($signParams);

	if ($prepayid != null) {
		$pack	= 'Sign=WXPay';
		//输出参数列表
		$prePayParams =array();
		$prePayParams['appid']		=$APP_ID;
		$prePayParams['appkey']		=$APP_KEY;
		$prePayParams['noncestr']	=$nonce_str;
		$prePayParams['package']	=$pack;
		$prePayParams['partnerid']	=$PARTNER;
		$prePayParams['prepayid']	=$prepayid;
		$prePayParams['timestamp']	=$time_stamp;
		//生成签名
		$sign=$reqHandler->createSHA1Sign($prePayParams);

		$outparams['retcode']=0;
		$outparams['retmsg']='ok';
		$outparams['appid']=$APP_ID;
		$outparams['noncestr']=$nonce_str;
		$outparams['package']=$pack;
		$outparams['prepayid']=$prepayid;
		$outparams['timestamp']=$time_stamp;
		$outparams['sign']=$sign;

	}else{
		$outparams['retcode']=-2;
		$outparams['retmsg']='错误：获取prepayId失败';
	}
}else{
	$outparams['retcode']=-1;
	$outparams['retmsg']='错误：获取不到Token';
}


	/**
	=========================
	输出参数列表
	=========================
	*/
	//Json 输出
	ob_clean();
	echo json_encode($outparams);
	//debug信息,注意参数含有特殊字符，需要JsEncode
	if ($DEBUG_ ){
		echo PHP_EOL  .'/*' . ($reqHandler->getDebugInfo()) . '*/';
	}
?> 
