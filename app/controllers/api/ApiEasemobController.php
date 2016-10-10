<?php 
/**
* 环信接口
**/
class ApiEasemobController extends ApiCommonController {
	
	private $easemob = null;
	public function __construct() {
		$this->easemob = new ApiEasemob;
	}
	
	//获取token
	public function getToken() {
		//验证权限（待写）
		$token = $this->easemob->getToken();
		$this->setReturn(1,'success',$token);
	}
	
	/*
	* 验证聊天室房间密码
	*/
	public function auditRoomPwd(){
		$cid = (int)Input::get('cid');
		$password = Input::get('password');
		$apiRoom=new ApiRoom;
		$data = $apiRoom->auditRoomPwd($cid,$password);
		if($data['status']==1){
			$this->setReturn(1,'success',$data['data']);
		}else{
			$this->setReturn($data['status'],$data['message']);
		}
		
	}
	
	/*
	* 权限验证
	* @node : 1发消息权限
	*/
	public function auditNode(){
		$node=(int)Input::get('node');
		$apiNode = new ApiNode;
		$data = $apiNode->auditNode($node);
		if($data['status']==1){
			$this->setReturn(1,'success',array('note'=>1));
		}elseif($data['status']==0){
			$this->setReturn(1,'success',array('note'=>0));
		}else{
			$this->setReturn($data['status'],$data['message']);
		}
	}



}

