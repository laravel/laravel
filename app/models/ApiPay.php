<?php 
/**
*	支付模型
*	@author:wang.hongli,zhang.zongliang
*	@since:2051/04/12
**/
class ApiPay extends ApiCommon
{

	//测试覆盖
	/*public function viaCookieLogin(){
		return array('id'=>13,'nick'=>'张春涛','uid'=>13);
	}*/
	
	/**
	*	根据商品id,获取商品信息，以及商品总价格
	*	@author:wang.hongli,zhangzongliang
	*	@since:2051/04/12
	**/
	public function getGoodsInfo($goodsId,$goodsNum=1,$attach_flag=0)
	{
		//判断用户是否登录
		$data = array();
		$info = $this->viaCookieLogin();
		if(empty($info))
		{
			return 'nolog';
		}
		if(empty($goodsId) || empty($goodsNum))
		{
			return 'error';
		}
		$goodsId = intval($goodsId);
		$goodsNum = abs($goodsNum);

		$info = DB::table('goods')
				->where('id','=',$goodsId)
				->first(array('id','name','price','type','description','flag','start_time','end_time','discount_price'));
		if(empty($info)){
			return 'error';
		}
		$price = $old_price = $info['price'];
		$time = time();
		//判断是否有折扣
		$start_time = $info['start_time'];
		$end_time = $info['end_time'];
		if($time>=$start_time && $time<=$end_time && $info['discount_price']){
			$price = $info['discount_price'];
		}
		$total = $price*$goodsNum;
		$old_total = $old_price*$goodsNum;
		$attach_good_info = [];
		if(!empty($attach_flag)){
			//绑定商品信息
			$attach_good_info = DB::table('goods')->where('good_pid','=',$goodsId)->where('flag','=',2)->first(['id','name','price','type','description','flag','start_time','end_time','discount_price']);
			$total += $attach_good_info['price']*1;
			$old_total += $attach_good_info['price']*1;
		}

		$data['id'] = $info['id'];
		$data['name'] = $info['name'];
		$data['price'] = $price;
		$data['old_price'] = $old_price;
		$data['type'] = $info['type'];
		$data['total'] = $total;
		$data['old_total'] = $old_total;
		$data['description'] = $info['description'];
		$data['attach_id'] =  !empty($attach_good_info) ? intval($attach_good_info['id']) : 0;
		$data['attach_price'] = !empty($attach_good_info) ? $attach_good_info['price'] : 0;
		$data['attach_name'] = !empty($attach_good_info) ? $attach_good_info['name'] : '';
		$unit_array = array(0=>'',1=>'年',2=>'月',3=>'天',4=>'次');
		$data['num'] = $goodsNum;//时长有效期
		$data['unit'] = $unit_array[$data['type']];
		
		return $data;
	}

	/**
	*	生成订单号 28位订单号 全时间+8位随机数+时分秒
	*	@author:wang.hongli,zhang.zongliang
	*	@since:2015/04/12
	*	
	**/
	public function genOrderid()
	{
		$orderId = date('YmdHis',time()).mt_rand(10000000,99999999).date('His',time());
		$flag = DB::table('order_list')->where('orderid',$orderId)->pluck('id');
		$flag2 = DB::table('flower_order_list')->where('orderid',$orderId)->pluck('id');
		if(!$flag && !$flag2){
			return $orderId;
		}else{
			return -1;
		}
		
	}


	/**
	*	1,生成自己系统订单 2,调用第三方支付
	*	@author:wang.hongli,zhangzongliang
	*	@since:2015/04/12
	**/
	public function insertOrder($data=array())
	{
		$info = $this->viaCookieLogin();
		if(empty($info))
			return array('code'=>-101,'msg'=>'请登录');
		if(empty($data))
			return array('code'=>-4,'msg'=>'错误：订单错误');
		$time = time();
		$data['orderid']  = $this->genOrderid();
		if($data['orderid'] == -1){
			return array('code'=>-4,'msg'=>'错误：订单错误');
		}
		$data['uid'] = $info['id'];
		$attach_flag = 0;
		if(!empty($data['attach_id']) && !empty($data['attach_price'])){
			$attach_flag = 1;
		}
		//get goodinfo
		$goodInfo = $this->getGoodsInfo($data['goods_id'],$data['num'],$attach_flag);
		$data['price'] = $goodInfo['price'];
		$data['total_price'] = $goodInfo['total'];
		$data['description'] = $goodInfo['description'];
		$data['addtime'] = $time;
		$data['updatetime'] = $time;
		$data['attach_id'] = $goodInfo['attach_id'];
		$data['attach_price'] = $goodInfo['attach_price'];
		$data['status'] = 0;
		//插入订单
		if(!DB::table('order_list')->insert($data))
		{
			return array('code'=>-5,'msg'=>'插入订单失败');
		}
		$return = $this->opOrder($data);
		$return['orderid'] = $data['orderid'];
		return $return;

	}

	/**
	*	operation pay
	*	@author:wanghongli
	*	@since:2015/04/16
	**/
	public function opOrder($data)
	{
		
		if(empty($data))
			return  array('code'=>-4,'msg'=>'错误：订单错误');
		$return = array();
		//operation type
		// 1银联支付2支付宝3支付宝网页4财付通
		$opType = $data['pay_type'];
		switch ($opType) {
			case 1:
				# code...
				$return  = $this->unipay($data);
				break;
			case 2:
				# code...
				$return  = $this->alipay($data);
				break;
			case 3:
				# code...
				$return  = $this->alipaywap($data);
				break;
			case 4:
				$return = $this->tengXun($data);
				break;
		}
		return $return;
	}
	/**
	*	财付通
	*	@author:wang.hongli
	*	@since:2015/04/16
	**/
	public function weiXinPay($data)
	{
		
		//导入微信开发包
		require_once ("../app/ext/weixin/classes/RequestHandler.class.php");
		require_once ("../app/ext/weixin/tenpay_config.php");
		require_once ("../app/ext/weixin/classes/ResponseHandler.class.php");
		require ("../app/ext/weixin/classes/client/TenpayHttpClient.class.php");
		//商品
		$info = $this->getGoodsInfo($data['goods_id']);
		if(empty($info)) {
			$outparams['code']=-3;
			$outparams['msg']='错误：获取商品信息失败';
		}
		
		$time = time();
		//获取提交的商品名称
		$product_name = $info['name'];
		// 获取提交的订单号
		$out_trade_no = trim($data['orderid']);
		$order_price = !empty($data['total_price'])  ?   trim($data['total_price']) : 1;
		$outparams =array();
		$plat_from = strtoupper($data['plat_from']);
		//获取$Token值
		$Token = '';
		$path_token = '../app/config/weixin_token.php';
		$file = @file_get_contents($path_token);
		$is_out = true;
		if(!empty($file)){
			$file_arr=json_decode(file_get_contents($path_token),true);
			if($time-$file_arr['time']<7000){
				$is_out = false;//未超时  7000s内
				$Token = $file_arr['token'];
			}
		}
		
		//如果超时或者不存在，从新获取
		if($is_out == true){
			$reqHandler->init($APP_ID, $APP_SECRET, $PARTNER_KEY, $APP_KEY);
			$Token= $reqHandler->GetToken();
			
			//更新文件
			$file_arr=array('time'=>$time,'token'=>$Token);
			@file_put_contents($path_token,json_encode($file_arr));
		}
		if($Token != '')
		{
			//生成预支付单
			$time_stamp=time();
			$uid=$data['uid'];
			//设置packet支付参数
			$packageParams =array();
			$packageParams['bank_type'] = "WX";
			$packageParams['body'] = $product_name;
			$packageParams['fee_type'] = '1';//银行币种
			$packageParams['input_charset'] = 'GBK'; //字符集
			$packageParams['notify_url'] = $notify_url; //通知地址
			$packageParams['out_trade_no'] = $out_trade_no;//商户订单号
			$packageParams['partner'] = $PARTNER; //设置商户号
			$packageParams['total_fee'] = $order_price*100; //商品总金额,以分为单位
			$packageParams['spbill_create_ip']= !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';//支付机器IP
			
			//获取package包
			$package= $reqHandler->genPackage($packageParams);
			$nonce_str = md5($data['orderid']);
			//设置支付参数
			$signParams =array();
			$signParams['appid']	=$APP_ID;
			$signParams['appkey']	=$APP_KEY;
			$signParams['noncestr']	=$nonce_str;
			$signParams['package']	=$package;
			$signParams['timestamp']=$time_stamp;
			$signParams['traceid']	= 'traceid_'.$uid.'_'.time();
			//生成支付签名
			$sign = $reqHandler->createSHA1Sign($signParams);
			
			//增加非参与签名的额外参数
			$signParams['sign_method'] = 'sha1';
			$signParams['app_signature']	=$sign;
			//剔除appkey
			unset($signParams['appkey']);
			
			//获取prepayid
			$prepayid=$reqHandler->sendPrepay($signParams);
			//return array('code'=>'-2','msg'=>"#".print_r($prepayid,true));
			
			//return array('code'=>'-2','msg'=>"#".$prepayid.print_r($signParams,true));
			if ($prepayid != null) {
				$pack	= 'Sign=WXPay';
				//输出参数列表
				$prePayParams =array();
				$prePayParams['appid']		=$APP_ID;
				$prePayParams['appkey']	=$APP_KEY;
				$prePayParams['noncestr']	=$nonce_str;
				$prePayParams['package']	=$pack;
				$prePayParams['partnerid']	=$PARTNER;
				$prePayParams['prepayid']	=$prepayid;
				$prePayParams['timestamp']	=$time_stamp;
				//生成签名
				$sign=$reqHandler->createSHA1Sign($prePayParams);
				$return=array();
				$return['appid']=$APP_ID;
				$return['noncestr']=$nonce_str;
				$return['package']=$pack;
				$return['prepayid']=$prepayid;
				$return['timestamp']=$time_stamp;
				$return['sign']=$sign;
				
				$outparams['code']=1;
				$outparams['msg']='ok';
				$outparams['data']=$return;
			}
			else
			{
				$outparams['code']=-2;
				$outparams['msg']='错误：获取prepayId失败';
			}
		}
		else
		{
			$outparams['code']=-1;
			$outparams['msg']='错误：获取不到Token';
		}
		ob_clean();
		return $outparams;
	}
	
	//
	public function tengXun($data){
		//导入微信开发包
		require_once ("../app/ext/weixin/classes/RequestHandler.class.php");
		require_once ("../app/ext/weixin/tenpay_config.php");
		require_once ("../app/ext/weixin/classes/ResponseHandler.class.php");
		require ("../app/ext/weixin/classes/client/TenpayHttpClient.class.php");
		
		$product_name=$data['description'];
        		//获取商品金额
        		$total_fee = $data['total_price']*100;
        		//订单号
        		$out_trade_no=$data['orderid'];
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
			$packageParams['input_charset']	= 'UTF-8';		    //字符集
			$packageParams['notify_url']	= $notify_url;	    //通知地址
			$packageParams['out_trade_no']	= $out_trade_no;		        //商户订单号
			$packageParams['partner']		= $PARTNER;		        //设置商户号
			$packageParams['total_fee']		= $total_fee;			//商品总金额,以分为单位
			$packageParams['spbill_create_ip']= $_SERVER['REMOTE_ADDR'];  //支付机器IP
			
			//获取package包
			$package= $reqHandler->genPackage($packageParams);
			
			$time_stamp = time();
			//$nonce_str = md5(rand());
			$nonce_str = md5($data['orderid']);
			//设置支付参数
			$signParams =array();
			$signParams['appid']	=$APP_ID;
			$signParams['appkey']	=$APP_KEY;
			$signParams['noncestr']	=$nonce_str;
			$signParams['package']	=$package;
			$signParams['timestamp']=$time_stamp;
			$signParams['traceid']	= 'mytraceid_'.$data['uid'];
			//$signParams['traceid']	= 'mytraceid_'.$data['uid']."_".$time_stamp;
			//生成支付签名
			$sign = $reqHandler->createSHA1Sign($signParams);
			
			//增加非参与签名的额外参数
			$signParams['sign_method']		='sha1';
			$signParams['app_signature']	=$sign;
			//剔除appkey
			unset($signParams['appkey']); 
			
			//获取prepayid
			$prepayid=$reqHandler->sendPrepay($signParams);
			//return array('code'=>'-2','msg'=>"#".print_r($prepayid,true));
			if ($prepayid != null) {
				//return array('code'=>-1,'msg'=>$prepayid."#".print_r($signParams,true));
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
				
				//return array('code'=>-1,'msg'=>$prepayid."#".print_r($prePayParams,true));
				//生成签名
				$sign=$reqHandler->createSHA1Sign($prePayParams);
		
				$outparams['appid']=$APP_ID;
				$outparams['noncestr']=$nonce_str;
				$outparams['package']=$pack;
				$outparams['prepayid']=$prepayid;
				$outparams['timestamp']=$time_stamp;
				$outparams['sign']=$sign;
				$outparams['partnerid']=$PARTNER;
				
				return array('code'=>1,'msg'=>'ok','data'=>$outparams);
			}else{
				return array('code'=>-2,'msg'=>'错误：获取prepayId失败');
			}
		}else{
			return array('code'=>-1,'msg'=>'错误：获取不到Token');
		}
	}

	/**
	*	财付通回调函数
	*	@author:wang.hongli
	*	@since:2015/04/26
	**/
	public function callWeiXin()
	{
		require ("../app/ext/weixin/classes/ResponseHandler.class.php");
		require ("../app/ext/weixin/classes/function.php");
		require_once("../app/ext/weixin/tenpay_config.php");
		
		$a=print_r($_GET,true);
		$b=print_r($_POST,true);
		$log = '../app/config/log.txt';
		@file_put_contents($log,$out_trade_no."#".$a.$b);

		/* 创建支付应答对象 */
		$key=$PARTNER_KEY;
		$resHandler = new ResponseHandler();
		$resHandler->setKey($key);
		//初始化页面提交过来的参数
		//$resHandler->init();
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

			if("0" == $trade_state)
			{
				//处理自己数据库业务
				//log_result("后台通知成功");
				//更新操作
				$this->callUnipay($out_trade_no);
				return true;
			}
			else
			{
				//log_result("后台通知失败");
				return false;
			}
		}
	}
	
	/*
	* 银联支付
	* @author:zhang.zongliang
	* @since:2015/04/19
	*/
	public function unipay($data){
		
		require_once ("../app/ext/unipay/lib/upmp_service.php");
		$desc=$data['description'];;
		$orderid=$data['orderid'];
		$payment=$data['total_price'];
		//需要填入的部分
        		$req['version']             = upmp_config::$version; // 版本号
        		$req['charset']             = upmp_config::$charset; // 字符编码
        		$req['transType']           = "01"; // 交易类型
        		$req['merId']               = upmp_config::$mer_id; // 商户代码
        		$req['backEndUrl']          = upmp_config::$mer_back_end_url; // 通知URL
        		$req['frontEndUrl']         = upmp_config::$mer_front_end_url; // 前台通知URL(可选)
        		$req['orderDescription']    = $desc;// 订单描述(可选)
        		$req['orderTime']           = date("YmdHis"); // 交易开始日期时间yyyyMMddHHmmss
        		$req['orderTimeout']        = ""; // 订单超时时间yyyyMMddHHmmss(可选)
        		$req['orderNumber']         = $orderid; //订单号(商户根据自己需要生成订单号)
        		$req['orderAmount']         = $payment*100; // 订单金额 (后两位为角和分，所以没有小数点)
        		$req['orderCurrency']       = "156"; // 交易币种(可选)
       		$req['reqReserved']         = ""; // 请求方保留域(可选，用于透传商户信息)
        		// 保留域填充方法
        		// $merReserved['test']        = "test";
        		// $req['merReserved']         = UpmpService::buildReserved($merReserved); // 商户保留域(可选)
        		$resp = array ();
		
        		$validResp = UpmpService::trade($req, $resp);
		
        		// 商户的业务逻辑
        		if ($validResp){
            			// 服务器应答签名验证成功
            			$tn=$resp['tn'];
			//$sql="update `order_list` set note='{$tn}',status=1 where orderid='{$orderid}'";
			DB::update("update `order_list` set note='{$tn}',status=1 where orderid='{$orderid}'");
		    	return array('code'=>1,'msg'=>'操作成功','data'=>$tn);
        		}else {
            			// 服务器应答签名验证失败
           			return array('code'=>-1,'msg'=>'签名失败');
        		}
	}
	
	/**
	 * 银联支付回调操作
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function callUnipay($orderid){
			//订单信息
			$order_info = DB::table('order_list')->where('orderid',$orderid)->first();
			if(empty($order_info)){
				return true;
			}
			//产品信息
			if($order_info['status'] ==2){
				return true;
			}
			$good_id = $order_info['goods_id'];
			//产品信息
			$good_info = DB::table('goods')->where('id',$good_id)->first();
			if(empty($good_info)){
				return true;
			}
			//商品参数信息
			$good_param = DB::table('good_param')->where('goodid',$good_id)->first();
			if(!empty($good_param)){
				$good_info['price'] = $good_param['price'];
				$good_info['promptgoods'] = $good_param['promptgoods'];//是否现货 0现货 1无货 2众筹
				$good_info['crowdfunding'] = $good_param['crowdfunding'];//众筹数量
				$good_info['diamond'] = $good_param['diamond']; //普通人购买，赠送钻石
				$good_info['member_diamond'] = $good_param['member_diamond'];//会员购买赠送钻石
				$good_info['normal_section'] = $good_param['normal_section'];//普通人购买分段赠送钻石
				$good_info['member_section'] = $good_param['member_section'];//会员购买商品分段赠送钻石
			}
			//实例化调用方法
			$apiPayCallBack = new ApiPayCallBack($orderid,$order_info,$good_info);
			//打赏
			if($good_id == 16){
				$apiPayCallBack->callReward();
			}elseif($good_id == 1){
				//中华诵读联合会交过费的自动通过，没交过费的后台审核
				$apiPayCallBack->callLeague();
			}elseif($good_id == 2){
				$apiPayCallBack->callSummerCup();
			//flag == 0 原来付费赛事
			}elseif($good_info['flag'] == 0){
				$apiPayCallBack->callCompetition();
			//flag == 1 表示新的班级活动
			}elseif($good_info['flag'] == 1){
				$apiPayCallBack->callClassActive();
			// flag == 2 结束比赛
			}elseif($good_info['flag'] == 2){
				$apiPayCallBack->callFinishCompetition();
			//会员增值--虚拟商品
			}elseif($good_info['flag'] == 3){
				switch ($good_id) {
					//开通会员
					case 59:
						$apiPayCallBack->callPassMember();
						break;
					//开通私信通
					case 60:
						$apiPayCallBack->callPassPersonLetter();
						break;
					//购买钻石
					case 61:
						$apiPayCallBack->callBuyDiamond();
						break;
				}
			//实体商品
			}elseif($good_info['flag'] == 4){
				$apiPayCallBack->callEntityGood();
			//出版物
			}elseif($good_info['flag'] == 5){
				$apiPayCallBack-> callPublicationGood();
			}
	}

	/*
	* 支付宝快捷支付
	*/
	public function alipay($data){
		require_once ("../app/ext/alipay/alipay_config.php");
		require_once ("../app/ext/alipay/alipay_function.php");

        		//获取客户端创建交易请求的参数
        		//获取商品名称
        		$subject=$data['description'];
		
        		//获取商品描述
        		$body = $data['description'];
        		//获取商品金额
        		$totalFee = $data['total_price'];
        		//订单号
        		$out_trade_no=$data['orderid'];
        
        		//组装待签名数据
        		$signData = "partner=" . "\"" . $partner ."\"";
        		$signData .= "&";
        		$signData .= "seller=" . "\"" .$seller . "\"";
        		$signData .= "&";
        		$signData .= "out_trade_no=" . "\"" . $out_trade_no ."\"";
        		$signData .= "&";
        		$signData .= "subject=" . "\"" . $subject ."\"";
        		$signData .= "&";
        		$signData .= "body=" . "\"" . $body ."\"";
        		$signData .= "&";
        		$signData .= "total_fee=" . "\"" . $totalFee ."\"";
        		$signData .= "&";
        		$signData .= "notify_url=" . "\"" . urlencode($notify_url)."\"";    //需要对地址urlencode,demo里没有

        		//获取待签名字符串
        		$content = $signData;
        		//生成签名
        		$mySign = urlencode(sign($content));    //真实的需要base64后urlencode，demo里只有前者
        		//返回参数格式
        		// $return = "<result><is_success>T</is_success><content>" . $content . "</content><sign>" . $mySign . "</sign></result>";  //demo里骗人的形式
        		$return = $content.'&sign_type="RSA"&sign="'.$mySign.'"';   //真实需要的形式
        		//返回数据到手机客户端
		return array('code'=>1,'msg'=>'签名成功','data'=>$return);
    	}
	
	
	/*
	* 支付宝网页支付
	*/
	public function alipaywap($data){
		require_once ("../app/ext/alipaywap/alipay.config.php");
		require_once ("../app/ext/alipaywap/lib/alipay_submit.class.php");
        		//返回格式
        		$format = "xml";    //必填，不需要修改
        		//返回格式
        		$v = "2.0";         //必填，不需要修改
        		//请求号
        		$req_id = uniqid();   //必填，须保证每次请求都是唯一

        		//**req_data详细信息**
        		$notify_url = $this->url."/api/callAlipayWap";   //服务器异步通知页面路径
        		$call_back_url = $this->url."/api/callBackWap";    //页面跳转同步通知页面路径
        
        		$seller_email = 'sxzte@sina.com';   //卖家支付宝帐户
        		$out_trade_no = $data['orderid'];   //商户订单号
        		$subject=$data['description'];
        		$total_fee = $data['total_price'];  //付款金额

        		//请求业务参数详细
        		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee></direct_trade_create_req>';

        		/************************************************************/
        		//构造要请求的参数数组，无需改动
        		$para_token = array(
            			"service" => "alipay.wap.trade.create.direct",
            			"partner" => trim($alipay_config['partner']),
            			"sec_id" => trim($alipay_config['sign_type']),
            			"format"    => $format,
            			"v" => $v,
            			"req_id"    => $req_id,
            			"req_data"  => $req_data,
            			"_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        		);

        		//建立请求
        		$alipaySubmit = new AlipaySubmit($alipay_config);
        		$html_text = $alipaySubmit->buildRequestHttp($para_token);

        		//URLDECODE返回的信息
        		$html_text = urldecode($html_text);
        		//解析远程模拟提交后返回的信息
        		$para_html_text = $alipaySubmit->parseResponse($html_text);

        		//获取request_token
        		$request_token = $para_html_text['request_token'];

        		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/

        		//业务详细
        		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';

        		//构造要请求的参数数组，无需改动
        		$parameter = array(
            			"service" => "alipay.wap.auth.authAndExecute",
            			"partner" => trim($alipay_config['partner']),
            			"sec_id" => trim($alipay_config['sign_type']),
            			"format"    => $format,
            			"v" => $v,
            			"req_id"    => $req_id,
            			"req_data"  => $req_data,
            			"_input_charset"    => trim(strtolower($alipay_config['input_charset']))
       		);

        		//建立请求
        		$alipaySubmit = new AlipaySubmit($alipay_config);
        		$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
        		//return $html_text;
		return array('code'=>1,'msg'=>'签名成功','data'=>$html_text);
	}
	
	public function ceshi(){
		require ("../app/ext/weixin/test.php");
		echo $a;
	}
}
 ?>