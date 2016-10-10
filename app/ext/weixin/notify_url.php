<?php

//---------------------------------------------------------
//即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require ("classes/ResponseHandler.class.php");
//require ("classes/RequestHandler.class.php");
//require ("classes/client/ClientResponseHandler.class.php");
//require ("classes/client/TenpayHttpClient.class.php");
require ("./classes/function.php");
require_once ("./tenpay_config.php");

log_result("进入后台回调页面");
/* 创建支付应答对象 */
unset($_GET['sub']);
$key=$PARTNER_KEY;
$resHandler = new ResponseHandler();
$resHandler->setKey($key);
//初始化页面提交过来的参数
//$resHandler->init(1,1,1,1);

$data= $resHandler->isTenpaySign();
print_r($data);exit;
//判断签名
if($resHandler->isTenpaySign() == true) 
{
	//商户在收到后台通知后根据通知ID向财付通发起验证确认，采用后台系统调用交互模式	
	$notify_id = $resHandler->getParameter("notify_id");//通知id
	//商户交易单号
	$out_trade_no = $resHandler->getParameter("out_trade_no");
	//财付通订单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//商品金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
	$discount = $resHandler->getParameter("discount");
	//支付结果
	$trade_state = $resHandler->getParameter("trade_state");
	//可获取的其他参数还有
	//bank_type			银行类型,默认：BL
	//fee_type			现金支付币种,目前只支持人民币,默认值是1-人民币
	//input_charset		字符编码,取值：GBK、UTF-8，默认：GBK。
	//partner			商户号,由财付通统一分配的10位正整数(120XXXXXXX)号
	//product_fee		物品费用，单位分。如果有值，必须保证transport_fee + product_fee=total_fee
	//sign_type			签名类型，取值：MD5、RSA，默认：MD5
	//time_end			支付完成时间
	//transport_fee		物流费用，单位分，默认0。如果有值，必须保证transport_fee +  product_fee = total_fee

	//判断签名及结果
	if ("0" == $trade_state)
	{
		/**----------------------即时到帐处理业务开始-----------------------*/
		//处理数据库逻辑
		//注意交易单不要重复处理
		//注意判断返回金额
		//-----------------------
		/**-----------------------即时到帐处理业务完毕-----------------------*/
		//给财付通系统发送成功信息，给财付通系统收到此结果后不在进行后续通知
		log_result("后台通知成功");
	} 
	else 
	{
		log_result("后台通知失败");
	}
	//回复服务器处理成功
	echo "Success";
} else {
	echo "<br/>" . "验证签名失败" . "<br/>";
	echo $resHandler->getDebugInfo() . "<br>";
}
?>