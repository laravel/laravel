<?php
/**
 *功能：支付宝接口公用函数
 *详细：该页面是请求、通知返回两个文件所调用的公用函数核心处理文件，不需要修改
 *版本：1.0
 *修改日期：2011-09-15
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */


/**RSA签名
 * $data待签名数据
 * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
 * 最后的签名，需要用base64编码
 * return Sign签名
 */
function sign($data) {
    //读取私钥文件
    $priKey = file_get_contents(dirname(__FILE__).'/key/rsa_private_key.pem');
    //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
    $res = openssl_get_privatekey($priKey);

    //调用openssl内置签名方法，生成签名$sign
    openssl_sign($data, $sign, $res);

    //释放资源
    openssl_free_key($res);
    
    //base64编码
    $sign = base64_encode($sign);
    return $sign;
}

/**RSA验签
 * $data待签名数据
 * $sign需要验签的签名
 * 验签用支付宝公钥
 * return 验签是否通过 bool值
 */
function verify($data, $sign)  {
    //读取支付宝公钥文件
    $pubKey = file_get_contents(dirname(__FILE__).'/key/alipay_public_key.pem');

    //转换为openssl格式密钥
    $res = openssl_get_publickey($pubKey);

    //调用openssl内置方法验签，返回bool值
    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
	
    //释放资源
    openssl_free_key($res);

    //返回资源是否成功
    return $result;
}

/**日志消息,把支付宝返回的参数记录下来
 * 请注意服务器是否开通fopen配置
 */
function  log_result($word) {
    $fp = fopen("log.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**通过节点路径返回字符串的某个节点值
 * $res_data——XML 格式字符串
 * 返回节点参数
 */
function getDataForXML($res_data,$node)
{
    $xml = simplexml_load_string($res_data);
    $result = $xml->xpath($node);

    while(list( , $node) = each($result)) 
    {
        return $node;
    }
}

?>