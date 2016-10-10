<?php 
/**
*	支付控制器
*	@author:wang.hognli zhang.zongliang
*	@since:2015/04/12
**/
class ApiPayController extends ApiCommonController
{

	private $apiPay = null;
	public function __construct()
	{
		$this->apiPay = new ApiPay;
	}
	/**
	 * 根据订单id获取订单状态
	 * @author:wang.hongli
	 * @since:2016/06/13
	 */
	public function getOrderStatus(){
		$orderid = Input::get('orderid');
		if(empty($orderid)){
			$this->setReturn(0,'获取订单状态失败');
			return;
		}else{
			try {
				$status = DB::table('order_list')->where('orderid',$orderid)->pluck('status');
				$status =  !empty($status) ? intval($status) : 0;
				$this->setReturn(1,'success',$status);
			} catch (Exception $e) {
				$this->setReturn(0,'获取订单状态失败');
			}
		}
	}
	/**
	*	根据商品id,获取商品信息，以及商品总价格
	*	@author:wang.hongli
	*	@since:2015/04/12
	**/
	public function getGoodsInfo()
	{
		$goodsId = Input::get('goods_id');//商品id
		$goodsNum = !empty(Input::get('goods_num'))?(int)Input::get('goods_num'):1;//商品数量
		//获取商品附加信息
		$attach_flag = !empty(Input::get('attach_flag')) ? intval(Input::get('attach_flag')) : 0;
		$data = $this->apiPay->getGoodsInfo($goodsId,$goodsNum,$attach_flag);
		if('nolog' === $data)
		{
			$this->setReturn(-101,'请登录');
		}
		else if('error' === $data)
		{
			$this->setReturn(0,'拉取数据失败，请重试');
		}else
		{
			//获取以前表单的信息
			$info = array('id'=>0,'uid'=>0,'card'=>'','name'=>'','nick_name'=>'','company'=>'','address'=>'','zip'=>'','mobile'=>'','email'=>'','cause'=>'','province_id'=>0,'city_id'=>0,'area_id'=>0);
			if($goodsId==1){
				$ApiLeague = new ApiLeague;//朗诵会
				$rlt=$ApiLeague->getInfoByUid();
				if($rlt['code']>0){
					$info=$rlt['data'];
				}
			}elseif($goodsId==2){
				$apiCup = new ApiSummerCup;//夏青杯
				$rlt=$apiCup->getInfoByUid();
				if($rlt['code']>0){
					$info=$rlt['data'];
				}
			}else{
				$ApiComp = new ApiCompetition;//赛事报名用户
				$rlt=$ApiComp->getMatchUser();
				if($rlt['code']>0){
					$info=$rlt['data'];
				}
			}
			
			$this->setReturn(1,'success',array('goodinfo'=>$data,'forminfo'=>$info));
		}
	}

	/**
	*	插入用户定单--自己系统
	*	@author:wang.hongli
	*	@since:2015/04/12
	**/
	public function insertOrder()
	{
		$return = array();
		$attach_id = !empty(Input::get('attach_id')) ? intval(Input::get('attach_id')) : 0;
		$attach_price = !empty(Input::get('attach_price')) ? Input::get('attach_price') : 0;
		$data = array(
				'goods_id'=>intval(Input::get('goods_id')),
				'num'=>Input::has('num') ? intval(Input::get('num')) : 1,
				'pay_type'=>intval(Input::get('pay_type')),
				'plat_from'=>intval(Input::get('plat_from')),
				'attach_id'=> $attach_id,
				'attach_price'=>$attach_price,
		);
		$return = $this->apiPay->insertOrder($data);
		//$flag = empty($return['retcode']) ? 1 : $return['retcode'];
		if($return['code'] == 1)
		{
			$this->setReturn(1,$return['orderid'],$return['data']);
		}
		else
		{
			$this->setReturn($return['code'],$return['msg']);	
		}
	}
	
	//======================================================
	/*
	* 提交朗诵会
	*/
	public function addLeague(){
		$return = array();
		$data = array(
			'name'=>!empty(Input::get('name'))?trim(Input::get('name')):'',
			'company' =>!empty(Input::get('company'))?trim(Input::get('company')):'',
			'address'=>!empty(Input::get('address'))?Input::get('address'):'',
			'card'=>!empty(Input::get('card'))?Input::get('card'):'',
			'zip'=>!empty(Input::get('zip'))?Input::get('zip'):'',
			'mobile'=>!empty(Input::get('mobile'))?trim(Input::get('mobile')):'',
			'email'=>!empty(Input::get('email'))?trim(Input::get('email')):'',
			'cause'=>!empty(Input::get('cause'))?Input::get('cause'):'',
			'province_id'=>!empty(Input::get('province_id'))?(int)Input::get('province_id'):0,
			'city_id'=>!empty(Input::get('city_id'))?(int)Input::get('city_id'):0,
			'area_id'=>!empty(Input::get('area_id'))?(int)Input::get('area_id'):0,
			'status'=>0,
			'addtime'=>time(),
			'update_time'=>time(),
		);
		$data['age'] =  accorCardGetAge($data['card']);
		if(empty($data['name'])){
			$this->setReturn(0,'请填写您的真实姓名');exit;
		}elseif(empty($data['company'])){
			$this->setReturn(0,'请填写您的工作单位');exit;
		}elseif(empty($data['mobile'])){
			$this->setReturn(0,'请填写您的联系电话');exit;
		}elseif(empty($data['address'])){
			$this->setReturn(0,'请填写您的联系地址');exit;
		}elseif(empty($data['zip'])){
			$this->setReturn(0,'请填写邮编');exit;
		}elseif(empty($data['email'])){
			$this->setReturn(0,'请填写您的邮箱');exit;
		}
		
		//存入log
		$path="../upload/league.txt";
		$log=@implode("|",$data);
		@file_put_contents($path, $log." \r\n", FILE_APPEND);
		
		$apiLeague = new ApiLeague;
		$rlt = $apiLeague->updateInfo($data);
		if($rlt['code'] > 0)
		{
			$this->setReturn(1,'success',array());
		}
		else
		{
			$this->setReturn($rlt['code'],$rlt['msg']);	
		}
		
	}
	/*
	* 提交夏青杯
	*/
	public function addSummerCup(){
		$return = array();
		$data = array(
			'name'=>!empty(Input::get('name'))?trim(Input::get('name')):'',
			'card'=>!empty(Input::get('card'))?Input::get('card'):'',
			'company' =>!empty(Input::get('company'))?Input::get('company'):'',
			'address'=>!empty(Input::get('address'))?Input::get('address'):'',
			'zip'=>!empty(Input::get('zip'))?Input::get('zip'):'',
			'mobile'=>!empty(Input::get('mobile'))?trim(Input::get('mobile')):'',
			'email'=>!empty(Input::get('email'))?trim(Input::get('email')):'',
			'cause'=>!empty(Input::get('cause'))?Input::get('cause'):'',
			'province_id'=>!empty(Input::get('province_id'))?(int)Input::get('province_id'):0,
			'city_id'=>!empty(Input::get('city_id'))?(int)Input::get('city_id'):0,
			'area_id'=>!empty(Input::get('area_id'))?(int)Input::get('area_id'):0,
			'status'=>0,
			'addtime'=>time(),
			'year'=>date("Y"),
		);
		
		if(empty($data['name'])){
			$this->setReturn(0,'请填写您的真实姓名');exit;
		}elseif(empty($data['company'])){
			$this->setReturn(0,'请填写您的工作单位');exit;
		}elseif(empty($data['mobile'])){
			$this->setReturn(0,'请填写您的联系电话');exit;
		}elseif(empty($data['address'])){
			$this->setReturn(0,'请填写您的联系地址');exit;
		}elseif(empty($data['zip'])){
			$this->setReturn(0,'请填写邮编');exit;
		}elseif(empty($data['email'])){
			$this->setReturn(0,'请填写您的邮箱');exit;
		}
		
		
		//存入log
		$path="../upload/summercup.txt";
		$log=@implode("|",$data);
		@file_put_contents($path, $log." \r\n", FILE_APPEND);
		$apiCup = new ApiSummerCup;
		$rlt = $apiCup->updateInfo($data);
		if($rlt['code'] > 0)
		{
			$this->setReturn(1,'success',array());
		}
		else
		{
			$this->setReturn($rlt['code'],$rlt['msg']);	
		}
	}
	
	/*
	* 银联支付回调接口
	*/
	public function callUnipay(){
		
		require_once ("../app/ext/unipay/lib/upmp_service.php");
		//$path = dirname(__FILE__);
		//file_put_contents($path.'test.txt',serialize($_POST));
		//$a=file_get_contents($path.'test.txt');
		if (UpmpService::verifySignature($_POST)){// 服务器签名验证成功
			//请在这里加上商户的业务逻辑程序代码
			//获取通知返回参数，可参考接口文档中通知参数列表(以下仅供参考)
			$transStatus = $_POST['transStatus'];// 交易状态
			if (""!=$transStatus && "00"==$transStatus){
				// 交易处理成功
				$orderid=$_POST['orderNumber'];  //订单号
				$this->apiPay->callUnipay($orderid);
			}
			$this->setReturn(1,'success','操作成功');	
		}else {// 服务器签名验证失败
			$this->setReturn(-1,'fail','操作失败');	
		}
	}
	
	/*
	* 阿里支付回调接口
	*/
	public function callAlipay(){
		require_once ("../app/ext/alipay/alipay_config.php");
		require_once ("../app/ext/alipay/alipay_function.php");
				
		//获取notify_data
		$notify_data = "notify_data=" . $_POST["notify_data"];
		//获取sign签名
		$sign = $_POST["sign"];
		//验证签名
		$isVerify = verify($notify_data, $sign);
		if(!$isVerify){
			$this->setReturn(-1,'fail','签名失败');	//如果验签没有通过
		}
		//获取交易状态
		$trade_status = getDataForXML($_POST["notify_data"] , '/notify/trade_status');
		//判断交易是否完成
		if($trade_status == "TRADE_FINISHED"){
			//在此处添加您的业务逻辑，作为收到支付宝交易完成的依据
			$orderid=simplexml_load_string($_POST["notify_data"])->out_trade_no;
			$this->apiPay->callUnipay($orderid);
			$this->setReturn(1,'success','签名失败');	
		}else{
			$this->setReturn(-2,'fail','操作失败');
		}
		
	}
	
	/*
	* 阿里网页支付回调(异步通知)
	*/
	public function callAlipayWap(){
		require_once ("../app/ext/alipaywap/alipay.config.php");
		require_once ("../app/ext/alipaywap/lib/alipay_notify.class.php");
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
					$this->apiPay->callUnipay($orderid);
					$this->setReturn(1,'success','签名失败');	
					//echo "success";		//请不要修改或删除
				}
			}
		}else {
			//验证失败
			$this->setReturn(-2,'fail','操作失败');
		}

	}

	/**
	* 微信支付回调
	**/
	public function callWeiXin()
	{
		$return = $this->apiPay->callWeiXin();
		$this->setReturn($return['code'],$return['msg'],$return);
	}
	/*
	* 阿里网页支付回调(同步显示通知)
	*/
	public function callBackWap(){
		
	}
	
	/*
	* 测试
	*/
	public function test(){
		echo "nihao";
		//$apiCup = new ApiSummerCup;
		//$info=$apiCup->getInfoByUid();
		$other=Input::get('aa');
		/*$apiLeague = new ApiLeague;
		$info=$apiLeague->getInfoByUid();*/
		
		$apiLeague = new ApiLeague;
		$info=$apiLeague->test();
		
		$this->setReturn(33,'success',array('other'=>$other,'info'=>$info));
	}
}
 ?>