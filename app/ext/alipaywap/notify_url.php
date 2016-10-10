<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
// $_POST=unserialize('a:5:{s:7:"service";s:30:"alipay.wap.trade.create.direct";s:4:"sign";s:32:"e81b4b7868834e70539393455c42cf2f";s:6:"sec_id";s:3:"MD5";s:1:"v";s:3:"1.0";s:11:"notify_data";s:785:"<notify><payment_type>1</payment_type><subject>购买60朵鲜花</subject><trade_no>2013090627727491</trade_no><buyer_email>1358551681@qq.com</buyer_email><gmt_create>2013-09-06 17:35:00</gmt_create><notify_type>trade_status_sync</notify_type><quantity>1</quantity><out_trade_no>5229a1ba5e218</out_trade_no><notify_time>2013-09-06 17:35:46</notify_time><seller_id>2088901585680783</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>0.10</total_fee><gmt_payment>2013-09-06 17:35:46</gmt_payment><seller_email>sxzte@sina.com</seller_email><gmt_close>2013-09-06 17:35:45</gmt_close><price>0.10</price><buyer_id>2088602131134911</buyer_id><notify_id>a475340836df8ce33ba77731334e31b072</notify_id><use_coupon>N</use_coupon></notify>";}');
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
// echo $verify_result;exit;
if($verify_result) {//验证成功
	//解密（如果是RSA签名需要解密，如果是MD5签名则下面一行清注释掉）
	// $notify_data = decrypt($_POST['notify_data']);
	$doc = new DOMDocument();
	// $doc->loadXML($notify_data);
    $doc->loadXML($_POST['notify_data']);
	
	if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
		//商户订单号
		$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
		//支付宝交易号
		$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
		//交易状态
		$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
		
		// if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
        if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
			$orderid=$out_trade_no;  //订单号
	        require '../zcl/Common/payfunc.php';
            // echo $orderid;exit;
            afterPay($orderid);
			echo "success";		//请不要修改或删除
		}
	}
}else {
    //验证失败
    echo "fail";
    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>