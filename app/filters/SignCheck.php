<?php
/**
 * 请求签名过滤器
 * @author:wang.hongli
 * @since:2016/05/27
 */
class SignCheck {
	
	private $prefix ;
	private $end ;
	
	function __construct(){
		$this->prefix = 'weinisongdu';
		$this->end = 'LEuQB4yGdQJCrssOyzJ#%XpyEH4clF7E';
	}
	/*
	 * 检测用户签名是否匹配
	 * 签名规则 weinisongdu+参数数组按key排序后取值+ LEuQB4yGdQJCrssOyzJ#%XpyEH4clF7E
	 * 
	 */
	public function signCheck(){
		$return = array('status'=>0,'message'=>'你的版本过低，请卸载后重新安装，以免影响使用！','data'=>null,'hasmore'=>null);
		// $data = Request::all();
		$data = Request::input();
		if(empty($data['self_sign'])){
			echo json_encode($return);
			exit();
		}
		$sign = $data['self_sign'];
		unset($data['self_sign']);
		if(empty($data)){
			$str = md5($this->prefix.$this->end);
		}
		ksort($data);
		$tmp_str = implode('', $data);
		$str = md5($this->prefix.$tmp_str.$this->end);
		if($sign == $str){
			return;
		}else{
			echo json_encode($return);
			exit();
		}
	}
}