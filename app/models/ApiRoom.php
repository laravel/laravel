<?php
/*
* room聊天室
*/
class ApiRoom extends ApiCommon{
	
	/*
	* 所有聊天室
	*/
	public static function getAllRoom(){
		$data = array();
		$sql = "select id,c_id,hx_id,hx_name from competition_room";
		$rlt = DB::select($sql);
		if(!empty($rlt)){
			foreach($rlt as $v){
				$data[$v["hx_id"]]=$v;
			}
		}
		return $data;
	}
	/*
	* 添加聊天室
	*/
	public function addRoom($arr){
		//查询房间是否存在
		$info = DB::table('competition_room')->where('c_id','=',$arr['c_id'])->first();
		$easemob = new ApiEasemob;
		if(empty($info)){
			//数据库
			$id = DB::table('competition_room')->insertGetId($arr);
			//同步到环信
			$rlt = $easemob->addRoom($arr['hx_uid'],$arr['hx_name'],$arr['content'],$arr['hx_num']);
			//把组的id插入到表
			$room_info = json_decode($rlt,true);
			DB::table('competition_room')->where('id','=',$id)->update(array('hx_id'=>$room_info['data']['id']));
			
			return $id;
		}
	}
	
	/*
	* 更新聊天室
	*/
	public function updateRoom($id,$arr){
		$info = DB::table('competition_room')->where('c_id','=',$arr['c_id'])->first();
		$easemob = new ApiEasemob;
		if(!empty($info)){
			$id=$info['id'];
			//数据库
			DB::table('competition_room')->where('id','=',$id)->update($arr);
			//同步到环信
			$rlt = $easemob->updateRoom($info['hx_id'],$arr['hx_uid'],$arr['hx_name'],$arr['content'],$arr['hx_num']);
			
			return $id;
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