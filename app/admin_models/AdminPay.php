<?php 
/**
*	支付模型
**/
class AdminPay extends AdminCommon
{
	private $weiXinPay = null;
	//导入微信开发包
	public function getWeixinPay(){
		require_once ("../app/ext/weixin/classes/RequestHandler.class.php");
		require_once ("../app/ext/weixin/classes/ResponseHandler.class.php");
		require ("../app/ext/weixin/classes/client/TenpayHttpClient.class.php");
	}
	
	public function tengXun($data){
        $this->getWeixinPay();
		require_once ("../app/ext/weixin/tenpay_config.php");
        //获取商品金额
        $amount = $data['amount']*100;
        $re_user_name = $data['re_user_name']; //收款人姓名
        $openid = $data['openid']; //用户openid
        //订单号
         $out_trade_no=$data['orderid'];
		//获取token值
		$reqHandler = new RequestHandler();
		$reqHandler->init($APP_ID, $APP_SECRET, $PARTNER_KEY, $APP_KEY);
		$Token= $reqHandler->GetToken();
		if ( $Token !='' ){
			//生成签名
			$params = array();
		    $params['mch_appid']	= $APP_ID;
		    $params['mchid']	= $PARTNER; //商户号
		    $params['device_info']	= 'WEB'; //设备号
			$params['nonce_str']	= md5($out_trade_no); //随机字符串
			$params['partner_trade_no']	= $out_trade_no; //商户订单号
			$params['openid']	= $openid; //用户openid
			$params['check_name']	= 'OPTION_CHECK'; //校验用户姓名选项
			$params['re_user_name']	= $re_user_name != '' ? $re_user_name : ''; //收款用户姓名
			$params['amount']	= $amount; //金额
			$params['desc']	= '红包提现转账'; //企业付款描述信息
			$params['spbill_create_ip']	= $_SERVER['REMOTE_ADDR']; //Ip地址
			$sign = $this->MakeSign($params,$PARTNER_KEY);
			$params['sign']	= $sign; //签名
            $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
            $xml = $this->arrayToXml($params); 
            $xml_string = $this->postXmlSSLCurl($xml,$url,$second=30);
            $result = $this->xmlToArray($xml_string);
	        //接口调用成功，明确返回调用失败
			if($result["return_code"] == "SUCCESS" &&
			   $result["result_code"] == "FAIL" && 
			   $result["err_code"] != "USERPAYING" && 
			   $result["err_code"] != "SYSTEMERROR")
			{
				return array('code'=>-1,'msg'=> $result["return_msg"]);
			}else{

				return array('code'=>1,'msg'=>'ok','data'=>$result);
			}
			
		}else{
			return array('code'=>-1,'msg'=>'错误：获取不到Token');
		}
	}
 

	public function arrayToXml($arr)
    {
    
    	if(!is_array($arr) 
			|| count($arr) <= 0)
		{
    		throw new Exception("数组数据异常！");
    	}
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
            	$xml.="<".$key.">".$val."</".$key.">";
                //$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    //将XML转为array
    public function xmlToArray($xml)
    {    
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $values;
    }

    /**
     *     作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml,$url,$second=30)
    {        
        //初始化curl        
           $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else 
        { 
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>"; 
            curl_close($ch);
            return false;
        }
    }

    /**
     *  使用证书，以post方式提交xml到对应的接口url
     */
   public function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);


        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
		
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, getcwd().'/apiclient_cert.pem');
 
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, getcwd().'/apiclient_key.pem');


		curl_setopt($ch, CURLOPT_CAINFO, getcwd().'/rootca.pem');// CA根证书（用来验证的网站证书是否是CA颁布）

        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else { 
            $error = curl_errno($ch);

            if ($error){
			    print_r(curl_error($ch));
			}
 
            echo "curl出错，错误码:$error"."<br>"; 
            curl_close($ch);
            return false;
        }
    }
    
    /**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams($Obj)
	{
		$buff = "";
		foreach ($Obj as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign($Obj,$KEY)
	{
		//签名步骤一：按字典序排序参数
		ksort($Obj);
		$string = $this->ToUrlParams($Obj);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
}
 ?>