<?php
/**
 * 类名：接口处理核心类
 * 功能：组转报文请求，发送报文，解析应答报文
 * 版本：1.0
 * 日期：2012-10-11
 * 作者：中国银联UPMP团队
 * 版权：中国银联
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
 */
class upmp_config{

    static $timezone                = "Asia/Shanghai"; //时区
    
    static $version                 = "1.0.0"; // 版本号
    static $charset                 = "UTF-8"; // 字符编码
    static $sign_method             = "MD5"; // 签名方法，目前仅支持MD5
    
    // static $mer_id                  = "880000000000152"; // 测试商户号
    static $mer_id                  = "898111148990090"; // 商户号
    // static $security_key            = "R8gT55vSR4FuJiS53iAPJXTO8cv0XAT9"; // 测试商户密钥
    static $security_key            = "ywhxTZCGfCUiDuzyMiuwQZf5dIbDzyMZ"; // 商户密钥
    static $mer_back_end_url        = "http://www.weinidushi.com.cn/api/callUnipay"; // 后台通知地址
    static $mer_front_end_url       = ""; // 前台通知地址

    // static $upmp_trade_url          = "http://222.66.233.198:8080/gateway/merchant/trade";  //测试地址
    static $upmp_trade_url          = "https://mgate.unionpay.com/gateway/merchant/trade";  //真实地址
    // static $upmp_query_url          = "http://222.66.233.198:8080/gateway/merchant/query";  //测试地址
    static $upmp_query_url          = "https://mgate.unionpay.com/gateway/merchant/query";  //真实地址
    
    const VERIFY_HTTPS_CERT         = false;
    const RESPONSE_CODE_SUCCESS     = "00"; // 成功应答码
    const SIGNATURE                 = "signature"; // 签名
    const SIGN_METHOD               = "signMethod"; // 签名方法
    const RESPONSE_CODE             = "respCode"; // 应答码
    const RESPONSE_MSG              = "respMsg"; // 应答信息
    
    const QSTRING_SPLIT             = "&"; // &
    const QSTRING_EQUAL             = "="; // =
    
}

/**
 * 除去请求要素中的空值和签名参数
 * @param para 请求要素
 * @return 去掉空值与签名参数后的请求要素
 */
function paraFilter($para) {
    $result = array ();
    while ( list ( $key, $value ) = each ( $para ) ) {
        if ($key == upmp_config::SIGNATURE || $key == upmp_config::SIGN_METHOD || $value == "") {
            continue;
        } else {
            $result [$key] = $para [$key];
        }
    }
    return $result;
}

/**
 * 生成签名
 * @param req 需要签名的要素
 * @return 签名结果字符串
 */
function buildSignature($req) {
    $prestr = createLinkstring($req, true, false);
    $prestr = $prestr.upmp_config::QSTRING_SPLIT.md5(upmp_config::$security_key);
    return md5($prestr);
}

/**
 * 把请求要素按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param para 请求要素
 * @param sort 是否需要根据key值作升序排列
 * @param encode 是否需要URL编码
 * @return 拼接成的字符串
 */
function createLinkString($para, $sort, $encode) {
    $linkString  = "";
    if ($sort){
        $para = argSort($para);
    }
    while (list ($key, $value) = each ($para)) {
        if ($encode){
            $value = urlencode($value);
        }
        $linkString.=$key.upmp_config::QSTRING_EQUAL.$value.upmp_config::QSTRING_SPLIT;
    }
    //去掉最后一个&字符
    $linkString = substr($linkString,0,count($linkString)-2);
    
    return $linkString;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
    ksort($para);
    reset($para);
    return $para;
}

/*
 * curl_call
*
* @url:  string, curl url to call, may have query string like ?a=b
* @content: array(key => value), data for post
*
* return param:
*   mixed:
*     false: error happened
*     string: curl return data
*
*/
function post($url, $content = null){
    if (function_exists("curl_init")) {
        $curl = curl_init();

        if (is_array($content)) {
            $data = http_build_query($content);
        }

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); //seconds
        
        // https verify
        curl_setopt($curl, CURLOPT_SSLVERSION,3);   //生产环境是https，需要加这个(nemo)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, upmp_config::VERIFY_HTTPS_CERT);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, upmp_config::VERIFY_HTTPS_CERT);

        $ret_data = curl_exec($curl);

        if (curl_errno($curl)) {
            printf("curl call error(%s): %s\n", curl_errno($curl), curl_error($curl));
            curl_close($curl);
            return false;
        }
        else {
            curl_close($curl);
            return $ret_data;
        }
    } else {
        throw new Exception("[PHP] curl module is required");
    }
}




if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set(upmp_config::$timezone);
}

class UpmpService {
    
    /**
     * 交易接口处理
     * @param req 请求要素
     * @param resp 应答要素
     * @return 是否成功
     */
    static function trade($req, &$resp) {
    	$nvp = self::buildReq($req);
    	$respString = post(upmp_config::$upmp_trade_url, $nvp);
    	return self::verifyResponse($respString, $resp);
    }
    
	/**
	 * 交易查询处理
	 * @param req 请求要素
	 * @param resp 应答要素
	 * @return 是否成功
	 */
    static function query($req, &$resp) {
    	$nvp = self::buildReq($req);
    	$respString = post(upmp_config::$upmp_query_url, $nvp);
    	return self::verifyResponse($respString, $resp);
    }
    
    /**
     * 拼接请求字符串
     * @param req 请求要素
     * @return 请求字符串
     */
    static function buildReq($req) {
    	//除去待签名参数数组中的空值和签名参数
    	$filteredReq = paraFilter($req);
    	// 生成签名结果
    	$signature = buildSignature($filteredReq);
    	
    	// 签名结果与签名方式加入请求
    	$filteredReq[upmp_config::SIGNATURE] = $signature;
    	$filteredReq[upmp_config::SIGN_METHOD] = upmp_config::$sign_method;
    	
    	return createLinkstring($filteredReq, false, true);
    }
    
    /**
     * 拼接保留域
     * @param req 请求要素
     * @return 保留域
     */
    static function buildReserved($req) {
    	$prestr = "{".createLinkstring($req, true, true)."}";
    	return $prestr;
    }
    
    /**
     * 应答解析
     * @param respString 应答报文
     * @param resp 应答要素
     * @return 应答是否成功
     */
    static function verifyResponse($respString, &$resp) {
    	if  ($respString != ""){
    		parse_str($respString, $para);
    		
    		$signIsValid = self::verifySignature($para);
    		
    		$resp = $para;
    		if ($signIsValid) {
    			return true;
    		}else {
    			return false;
    		}
    	}
    	
    	
    }
    
    /**
     * 异步通知消息验证
     * @param para 异步通知消息
     * @return 验证结果
     */
    static function verifySignature($para) {
    	$respSignature = $para[upmp_config::SIGNATURE];
    	// 除去数组中的空值和签名参数
    	$filteredReq = paraFilter($para);
    	$signature = buildSignature($filteredReq);
    	if ("" != $respSignature && $respSignature==$signature) {
    		return true;
    	}else {
    		return false;
    	}
    }
	
}
?>