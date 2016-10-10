<?php 
/**
*	评委控制器
*	@author:zhang.zongliang
*	@since:2015/04/25
**/
class ApiJuryController extends ApiCommonController
{

	/**
	*	获取活动的评委
	*	@author:wang.hongli
	*	@since:2015/01/11
	**/
	public function getJuryList()
	{
		$where = array();
		
		$where['status'] = 2;
		$where['type'] = (int)Input::get('type');
		//$where['level'] = (int)Input::get('level');
		$page = 0;
		$page_size = 0;
		
		$ApiJury = new ApiJury;
		$rlt = $ApiJury->getList($where,$page,$page_size);
		if($rlt['code']==1){
			$this->setReturn(1,'success',$rlt['data']);
		}else{
			$this->setReturn(-2,'fail',$rlt['msg']);
		}
		
	}
	
	/*
	* 赛事评委
	*/
	public function getMatchJury(){
		$where = array();
		
		$where['status'] = 2;
		$where['type'] = (int)Input::get('type');
		
		$ApiJury = new ApiJury;
		$rlt = $ApiJury->getMatchJury($where);
		if($rlt['code']==1){
			$this->setReturn(1,'success',$rlt['data']);
		}else{
			$this->setReturn(-2,'fail',$rlt['msg']);
		}
	}
	
	/*
	* 活动观众报名
	*/
	public function addAudience(){
		$data = array();
		
		$data = array(
			'a_id'=>!empty(Input::get('a_id'))?(int)Input::get('a_id'):0,
			'name'=>!empty(Input::get('name'))?trim(Input::get('name')):'',
			'card'=>!empty(Input::get('card'))?Input::get('card'):'',
			'mobile'=>!empty(Input::get('mobile'))?trim(Input::get('mobile')):'',
			'address'=>!empty(Input::get('address'))?Input::get('address'):'',
			'province_id'=>!empty(Input::get('province_id'))?(int)Input::get('province_id'):0,
			'city_id'=>!empty(Input::get('city_id'))?(int)Input::get('city_id'):0,
			'area_id'=>!empty(Input::get('area_id'))?(int)Input::get('area_id'):0,
		);
		
		if(empty($data['a_id'])){
			$this->setReturn(0,'参数错误');exit;
		}elseif(empty($data['name'])){
			$this->setReturn(0,'请填写您的真实姓名');exit;
		}elseif(empty($data['card'])){
			$this->setReturn(0,'请填写您的身份证号');exit;
		}elseif(empty($data['mobile'])){
			$this->setReturn(0,'请填写您的联系电话');exit;
		}elseif(empty($data['address'])){
			$this->setReturn(0,'请填写您的联系地址');exit;
		}
		
		$ApiActivities = new ApiActivities;
		$rlt = $ApiActivities->addAudience($data);
		if($rlt['status']==1){
			$this->setReturn(1,'操作成功');
		}else{
			$this->setReturn(-100,'fail','请您登陆');
		}
	}
}