<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
/*
    *功能：安全支付服务器同步处理
    *版本：1.0
    *日期：2011-09-15
    *说明：
    *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
    *该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

    ///////////////////页面功能说明///////////////////
    /// 客户端callback回调之后请求服务器callback_url
    /// 验签通过就给客户端返回2，不通过就返回1
    ////////////////////////////////////////////////

    require_once("alipay_config.php");
    require_once("alipay_function.php");

    //得到签名
    $sign = urldecode($_POST["sign"]);

    //得到待签名字符串
    $content = urldecode($_POST["content"]);
    
    //验签数据
    $isVerify = verify($content, $sign);
    
    //判断验签
    if($isVerify){
    	    //验签通过
    	    echo("2");
    }
    else{
    	    //验签失败
    	    echo("1");
    }

?>