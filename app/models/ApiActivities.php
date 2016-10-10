<?php
/**
** 活动表
*/
class ApiActivities extends ApiCommon {
	
	//活动报名观众
	public function addAudience($data){
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('status'=>-100,'message'=>'请登录');
		
		$data['uid']=$info['id'];
		$data['nick_name']=$info['nick'];
		$data['isleague']=$info['isleague'];
		$data['addtime']=time();
		$data['status']=0;
		$id = DB::table('user_audience')->insertGetId($data);
		return array('status'=>1,'message'=>'操作成功');
	}
	
}