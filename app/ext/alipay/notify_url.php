
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
</head>

<?php
/*
    *功能：安全致富服务器异步通知页面
    *版本：1.0
    *日期：2011-09-15
    *说明：
    *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
    *该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

///////////页面功能说明///////////////
// 创建该页面文件时，请留心该页面文件中无任何HTML代码和空格
// 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面
// TRADE_FINISHED(表示交易已经成功结束)
/////////////////////////////////////
// $_POST=unserialize('a:3:{s:4:"sign";s:172:"VaolXQym3tfVzAGtMneHvLa0O/UwRxHi+//VH0FVWM8h1fe2wB2hXn+/uzHzjGHckQOvMhkzvGngMyRyS/hx7UphETfSA6YV2EGd6OOjyOBgoNKR2UUEW+tjKqa25cNNcZPma2Ei4dE93IU2XWskObpAn9fStRDijYJEJhu8ja0=";s:9:"sign_type";s:3:"RSA";s:11:"notify_data";s:696:"<notify><partner>2088901585680783</partner><discount>0.00</discount><payment_type>1</payment_type><subject>购买漂流瓶</subject><trade_no>2013090627434391</trade_no><buyer_email>1358551681@qq.com</buyer_email><gmt_create>2013-09-06 15:56:49</gmt_create><quantity>1</quantity><out_trade_no>52298ab0024c2</out_trade_no><seller_id>2088901585680783</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>0.10</total_fee><gmt_payment>2013-09-06 15:56:50</gmt_payment><seller_email>sxzte@sina.com</seller_email><gmt_close>2013-09-06 15:56:50</gmt_close><price>0.10</price><buyer_id>2088602131134911</buyer_id><use_coupon>N</use_coupon></notify>";}');
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';exit;
require_once("alipay_config.php");
require_once("alipay_function.php");

//获取notify_data，需要添加notify_data=
//不需要解密，是明文的格式
$notify_data = "notify_data=" . $_POST["notify_data"];

// $notify_data = 'notify_data=<notify><partner>2088702043538774</partner><discount>0.00</discount><payment_type>1</payment_type><subject>骨头1</subject><trade_no>2011102716746901</trade_no><buyer_email>4157874@qq.com</buyer_email><gmt_create>2011-10-27 12:10:51</gmt_create><quantity>1</quantity><out_trade_no>1027040323-9215</out_trade_no><seller_id>2088702043538774</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>0.01</total_fee><gmt_payment>2011-10-27 12:10:52</gmt_payment><seller_email>17648787@qq.com</seller_email><gmt_close>2011-10-27 12:10:52</gmt_close><price>0.01</price><buyer_id>2088002456173013</buyer_id><use_coupon>N</use_coupon></notify>';

//获取sign签名
$sign = $_POST["sign"];
// $sign='HQq7oZ+gvdSDwV4bQh8QO0TdbwG2gEwjtY/fJbZWiuMEBwSG1VBdsQsocfuNALrEEGCFEIDM+bBjFN/idR+zV2CoGuRuofoZP4mEJ28+4p16iapilTr51boBTd2lQmMd8RQjlkNAz6QF6J5soCDH3WjI4pF75U0OqupacF9q7JA=';

//验证签名
$isVerify = verify($notify_data, $sign);

//如果验签没有通过
if(!$isVerify){
	echo "fail";
	return;
}
else{echo "true";}

//获取交易状态
$trade_status = getDataForXML($_POST["notify_data"] , '/notify/trade_status');

//判断交易是否完成
if($trade_status == "TRADE_FINISHED"){
	echo "success";
	// out_trade_no
	//在此处添加您的业务逻辑，作为收到支付宝交易完成的依据
	$orderid=simplexml_load_string($_POST["notify_data"])->out_trade_no;
    require '../zcl/Common/payfunc.php';
    afterPay($orderid);
}
else{
	echo "fail";
}
?>