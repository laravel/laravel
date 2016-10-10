<?php
/*
* 权限model
*/
class ApiNode extends ApiCommon{
	
	//验证权限
	public function auditNode($node){
		$user = $this->viaCookieLogin();
		if(empty($user['id'])){
			return array('status'=>-100,'message'=>'请先登录');
		}
		$time=time();
		$sql="select id from user_node where uid='".$user["id"]."' and node='".$node."' and end_time>".$time." limit 1";
		$info=DB::select($sql);
		if(!empty($info)){
			return array('status'=>1,'message'=>'有权限');
		}else{
			return array('status'=>0,'message'=>'没有权限');
		}
	}
	
	

}
?>