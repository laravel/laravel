<?php 
/**
*	消息队列
**/
class HttpQueue extends ApiCommon {
	public $httpsqs;
	public function __construct() {
		$this->httpsqs = new httpsqs("192.168.1.249", 1218, "mypass123", "utf-8");
	}
	//入队
	public function putCustom($queue_name,$data){
		if(empty($queue_name) || empty($data)){
			return false;
		}
		$result = $this->httpsqs->put($queue_name, urlencode($data));
		return $result;
	}
    //出列
	public function getCustom($queue_name){
        if(empty($queue_name)){
			return false;
		}
		$result = $this->httpsqs->get($queue_name);
		return $result;
	}
}
