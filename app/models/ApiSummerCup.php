<?php 
/**
*	夏青杯相关
*	@author:zhang.zongliang
*	@since:2015/04/19
**/
class ApiSummerCup extends ApiCommon
{
	
	//测试覆盖
	/*public function viaCookieLogin(){
		return array('id'=>11,'nick'=>'张宗');
	}*/
	
	/**
	*	获取下过表单信息
	**/
	public function getInfoByUid()
	{
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'请登录');
		
		$uid = $info['id'];
		$info = DB::table('summercup')
				->where('uid','=',$uid)
				->first(array('id','uid','card','name','nick_name','company','address','zip','mobile','email','cause','opus_id','province_id','city_id','area_id'));
		if($info) 
		{
			return array('code'=>1,'data'=>$info);
		}
		return array('code'=>-1,'msg'=>'操作失败');
	}
	
	
	/**
	*	添加数据
	**/
	public function add($arr)
	{
		$id = DB::table('summercup')->insertGetId($arr);
		if($id>0) 
		{
			return $id;
		}
		return 0;
	}
	
	/*
	* 更新数据
	*/
	public function upInfo($uid,$arr){
		if(!is_array($arr) || empty($arr) || empty($uid)){
			return 0;
		}
		
		$tmp=array();
		foreach($arr as $k=>$v){
			$tmp[]=$k."='".$v."'";
		}
		
		$sql = "update summercup set ".implode(",",$tmp)." where uid = '".$uid."'";
		if(DB::update($sql)) {
			return 1;
		}else{
			return 0;
		}
	}
	
	/*
	* 以用户uid为主键，判断是更新还是添加
	*/
	public function updateInfo($arr){
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'msg'=>'请登录');
		$uid = $info['id'];
		$data = DB::table('summercup')->where('uid','=',$uid)->first();
		$id=0;
		if(!empty($data)){
			//存在就更新
			$id = $data["id"];
			$this->upInfo($data['uid'],$arr);
		}else{
			//不存在就添加
			$arr['uid'] = $info['id'];
			$arr['nick_name'] = $info['nick'];
			$id = $this->add($arr);
		}
		
		if($id>0){
			return array('code'=>1,'id'=>$id,'msg'=>'提交成功');
		}else{
			return array('code'=>0,'msg'=>'操作失败');
		}
		
	}
	
	
	
	
}