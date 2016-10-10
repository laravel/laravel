<?php
/*
* group群组
*/
class AdminGroup extends AdminCommon{

/*
* 所有群组
*/
public static function getAllRoom(){
	$data = array();
	$sql = "select * from class_group";
	$rlt = DB::select($sql);
	if(!empty($rlt)){
		foreach($rlt as $v){
			$data[$v["hx_id"]]=$v;
		}
	}
	return $data;
}
/*
* 添加群组
*$name 		群组名  		 必填
*$desc 		群组描述		必填
*$public 	是否公开		必填
*$maxusers 	人数上限		可选
*$approval 	入群权限		加入公开群true须审核  false加入公开群不审核
*$owner 	群管理员		必须
*/
public function addGroup($arr){

	//查询房间是否存在
	$info = DB::table('class_group')->where('classid','=',$arr['class_id'])->first();
	$easemob = new ApiEasemob;
	$info=""; 
	if(empty($info)){	
		//数据库
			$id = DB::table('class_group')->insertGetId(array("classid"=>$arr['class_id'],
			'groupname'=>$arr['name'],'groupinfo'=>$arr['desc'],'num'=>$arr['maxusers'],'power'=>0,
			'admin'=>$arr['owner'],"addtime"=>time()));
		//同步到环信
		$rlt=$easemob->addGroup($arr['name'],$arr['desc'],true,$arr['maxusers'],false,$arr['owner']);
		$result=json_decode($rlt,true);
		DB::table('class_group')->where('id','=',$id)->update(array('groupid'=>$result['data']['groupid']));
		return $result['data']['groupid']?$result['data']['groupid']:00;
	}
}





/*群组删除
*$id    群组id		 必填
*/
public function delGroup($id){
	$group=DB::table('class_group')->where('id','=',$id)->first();
	if($group){
		$easemob = new ApiEasemob;
		$rlt=$easemob ->delGroup($arr['groupid']);
		$return=json_decode($rlt,true);
		if($return['data']['succcess']===true){

			$re=DB::table('class_group')->where('id','=',$id)->delete();
			if($re){
				return true;
			}else{
				return false;
			}
		}
	}else{
		return false;
	}
}

/**
	* 获取群组成员
	*
	* @param     $group_id
	*/
public function groupsUser($id){
	if($id=="") return false;
		$users=DB::table("class_group_user")->where('groupid','=',$id)->get();
		if($user!=""){
			return $user;
		}else{
			return false;
		}
}
//添加成员  单加
public function addGroupUser($groupid,$userid){
		
		if($groupid== "" || $userid=="")return false;
		$group=DB::table('class_group')->where('groupid','=',$groupid)->first();
		if(!$group){
			return 11;
		}
		$easemob = new ApiEasemob;
		$rlt=$easemob->addGroupsUser($groupid,$userid);
		$return=json_decode($rlt,true);
		if($return["data"]["result"]==true){
			$a=DB：：table('class_group_user')->insert(array('groupid'=>$groupid,'uid'=>$userid,'ower'=>$group['uid'],'addtime'=>time()));
		}
		return $rlt;
}

//添加成员  单删
public function delGruopUser($groupid,$userid){
	if($groupid== "" || $userid=="")return false; 
	$userinfo=DB::table('class_group_user')->where('groupid','=',$groupid)->where('uid','=',$userid)->frist();
	if(!$userinfo) return false;
	$easemob = new ApiEasemob;
	$rlt=$easemob->delGroupsUser($groupid,$userid);
	$return=json_decode($rlt,true);
	if($return["data"]["result"]==true){		
		DB：：table('class_group_user')->where('groupid','=',$groupid)->where('uid','=',$userid)->delete();
	return true;
	}else{
		return false;
	}
}



//添加成员   多删
public function delGruopUsers($groupid,$userids){
	if($groupid== "" || $userids=="")return false;
	$userinfo=DB::table('class_group_user')->where('groupid','=',$groupid)->whereIn('uid',$userids)->frist();
	if(!$userinfo) return  false;
	$easemob = new ApiEasemob;
	$rlt=$easemob ->delGroupsUsers($groupid,$userids);
	$return=json_decode($rlt,true);
	foreach ($return['data'] as $key => $value) {
		if($value['resule']==true){
			DB：：table('class_group_user')->where('groupid','=',$groupid)->where('uid','=',$value['user'])->delete();
		}
	}
}
/**********************聊天室成员*********************************************8/
/*
* 聊天室成员列表
*/
public function getRoomUserList($params,$page=1,$pagesize=20){
	$data = array();
	$where = "";
	if(isset($params['hx_id'])){
		$where.=" and hx_id='".$params['hx_id']."'";
	}
	if(isset($params['hx_uid'])){
		$where.=" and hx_uid='".$params['hx_uid']."'";
	}
	if(isset($params['is_owner'])){
		$where.=" and is_owner='".$params['is_owner']."'";
	}
	$sql="select * from competition_room_user where 1 ".$where." order by updatetime desc";
	$data=DB::select($sql);
	if(!empty($data)){
		$users=$uids=array();
		foreach($data as $v){
			$uids[$v['hx_uid']]=$v['hx_uid'];
		}
		$sql="select id,nick,gender from user where id in(".implode(",",$uids).")";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$users[$v['id']]=$v;
		}
		foreach($data as $k=>$v){
			$data[$k]['nick']=$users[$v['hx_uid']]['nick'];
			$data[$k]['gender']=$users[$v['hx_uid']]['gender'];
		}
		
	}
	return $data;
}

/*
* 添加聊天室成员
*/
public function addRoomUser($hx_id,$hx_uid,$is_owner=0){
	$info = DB::table('competition_room_user')->where('hx_id','=',$hx_id)->where('hx_uid','=',$hx_uid)->first();
	if(empty($info)){
		//添加数据库
		$data = array(
			'hx_id'=>$hx_id,
			'hx_uid'=>$hx_uid,
			'is_owner'=>$is_owner,
			'addtime'=>time(),
			'updatetime'=>time(),
		);
		$id = DB::table('competition_room_user')->insertGetId($data);
		//同步到环信
		$apiEasemob=new ApiEasemob;
		$rlt = $apiEasemob->addRoomUser($hx_id,$hx_uid);
	}
	return true;
}

/*
* 删除聊天室成员
*/
public function deleteRoomUser(){
	
}

/*********************验证成员***********************************************/
//验证聊天室密码
public function auditRoomPwd($cid,$password){
	$user = $this->viaCookieLogin();
	if(empty($user['id'])){
					return array('status'=>-100,'message'=>'请先登录');
	}
	
	//查询比赛是否有聊天室
	$info = DB::table('competition_room')->where('c_id','=',$cid)->first();
	if(empty($info) || $info["hx_id"]==0){
					return array('status'=>0,'message'=>'参数错误');
	}
	
	//验证密码
	if($info['password']==$password){
		
		//查询用户是否已经存在room中，不存在就添加到环信
		$rlt = DB::table('competition_room_user')->where('hx_id','=',$info["hx_id"])->where('hx_uid','=',$user["id"])->first();
		if(empty($rlt)){
			//添加
			$this->addRoomUser($info['hx_id'],$info["id"]);
		}else{
			//更新
			$sql="update competition_room_user set updatetime='".time()."' where hx_id=".$info["hx_id"]." and hx_uid=".$user["id"];
			DB::update($sql);
		}
		return array('status'=>1,'message'=>'验证成功','data'=>array('hx_id'=>$info['hx_id']));
	}else{
		return array('status'=>0,'message'=>'密码错误');
	}
}	


}
?>