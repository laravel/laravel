<?php 
/**
* 官方诗友会
* @author:wang.hongli
* @since:2014/10/26
**/
class ApiCompetitionController extends ApiCommonController
{
	private $model = null;
	public function __construct() {
		$this->model = new ApiCompetition();
	}
	/**
	*	获取诗友会分类
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getCompCategory()
	{	
		$data = $this->model->getCompCategory();
		if($data === 'nolog')
		{
			$this->setReturn(-101,'请登录');
			return;
		}
		$this->setReturn(1,'success',$data);
	}
	

	/**
	* 	读诗诗友会子分类
	*	@author:wang.hongli
	*	@author:2014/10/26
	**/
	public function getSubComCategory()
	{
		$id = Input::get('id')?Input::get('id'):22; //id,默认夏靑杯
		$pid = Input::get('pid'); //大分类id
		$data= array();
		$params = array();
		$params['count'] = !empty(Input::get('count')) ? Input::get('count') : 20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$params['offSet'] = ($pageIndex-1)*$params['count'];
		++$params['count'];
		$hasmore = 0;//默认没有下一页
		$keywords = Input::get('keywords');
		$rs=  $this->model->getSubComCategory($pid,$params,$keywords,$id);
		if($rs === 'nolog')
		{
			$this->setReturn(-101,'请登录');
			return;
		}
		if(!empty($rs))
		{
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$data = $rs;
		}
		$this->setReturn(1,'success',$data,$hasmore);
	}

	/**
	*	读诗诗友会子分类下作品列表
	**/
	public function getSubCatOpusList()
	{
		$id = Input::get('competitionlistid');//子类id
		$isfinish = !empty(Input::get('isfinish')) ? Input::get('isfinish') : 0;//0没结束1结束
		$data= array();
		$params = array();
		$params['count'] = !empty(Input::get('count')) ? Input::get('count') : 20;
		$params['pageIndex'] = $pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$params['offSet'] = ($pageIndex-1)*$params['count'];
		++$params['count'];
		$hasmore = 0;//默认没有下一页
		$rs = $this->model->getSubCatOpusList($id,$params,$isfinish);
		if($rs === 'nolog')
		{
			$this->setReturn(-101,'请登录');
			return;
		}
		if(!empty($rs))
		{
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$data = $rs;
		}
		$this->setReturn(1,'success',$data,$hasmore);
	}

	/**
	*	诗友会获取某时间榜单列表
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getStaticCompLog()
	{
		$complistid = Input::get('complistid');//子列表id
		$data = $this->model->getStaticCompLog($complistid);
		if($data === 'nolog')
		{
			$this->setReturn(-101,'请登录');
			return;
		}
		$this->setReturn(1,'success',$data);
	}

	/**
	*	诗友会获取特定静态列表
	*	@author:wang.hongli
	*	@since:2014/10/26
	**/
	public function getStaticSubCatOpusList()
	{
		$id = Input::get('id');//staticcomplog id
		$params = array();
		$params['count'] = !empty(Input::get('count')) ? Input::get('count') : 20;
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$params['offSet'] = ($pageIndex-1)*$params['count'];
		++$params['count'];
		$hasmore = 0;//默认没有下一页
		$data = $this->model->getStaticSubCatOpusList($id,$params);
		if($data === 'nolog')
		{
			$this->setReturn(-101,'请登录');
			return;
		}
		if(!empty($data))
		{
			$hasmore = $data['hasmore'];
			unset($data['hasmore']);
		}
		$this->setReturn(1,'success',$data,$hasmore);

	}

	/**
	*	根据比赛id，获取比赛详情
	*	@author:wang.hongli
	*	@since:2015/02/01
	*/
	public function getCompDetail()
	{
		$id = intval(Input::get('competitionid'));
		$data = $this->model->getCompDetail($id);
		if($data === 'error')
		{
			$this->setReturn(0,'请传入正确比赛');
		}
		else
		{
			$this->setReturn(1,'success',$data);
		}
	}

	/**
	*	根据比赛id，获取比赛详情
	*	@author:wang.hongli
	*	@since:2015/02/01
	*/
	public function getCompDetailV2()
	{
		$id = intval(Input::get('competitionid'));
		$data = $this->model->getCompDetailV2($id);
		if($data === 'error')
		{
			$this->setReturn(0,'请传入正确比赛');
		}
		else
		{
			$this->setReturn(1,'success',$data);
		}
	}

	/**
	* 	判断活动是否交费，活动是否参加过，活动是否过期
	*	@author:wang.hongli
	*	@since:2015/05/10
	*	@param:competitionid 活动id,type 1，诵读联盟 2，夏青杯
	*/
	public function check_competion()
	{
		$competitionid = intval(Input::get('competitionid'));
		$type = Input::get('type') ? intval(Input::get('type')) : 0;
		$apiCheckPermission = new ApiCheckPermission();
		$rs = $apiCheckPermission->check_permission($competitionid,$type);
		$this->setReturn($rs['status'],$rs['message']);
	}

	/**
	* 	判断活动是否交费，活动是否参加过，活动是否过期
	*	@author:wang.hongli
	*	@since:2015/05/10
	*	@param:competitionid 活动id,type 1，诵读联盟 2，夏青杯
	*/
	public function check_competionv2()
	{
		$competitionid = intval(Input::get('competitionid'));
		$type = Input::get('type') ? intval(Input::get('type')) : 0;
		$apiCheckPermission = new ApiCheckPermission();
		$rs = $apiCheckPermission->check_permissionv2($competitionid,$type);
		$this->setReturn($rs['status'],$rs['message']);
	}
	
	/*
	* 获得赛事列表
	*/
	public function getMatchList(){
		$where = array();
		$pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		if(!empty(Input::get('pid'))){
			$where['pid'] = Input::get('pid');
		}
		if(!empty(Input::get('keywords'))){
			$where['type_id'] = 2;
			$where['keywords'] = Input::get('keywords');
		}
		//1 没有主播大赛和作品大赛
		$flag = Input::get('flag') ? Input::get('flag') : 0;
		$data = $this->model->getMatchList($where,$pageIndex,20,$flag);
		$hasmore = $data['hasmore'];
		unset($data['hasmore']);
		
		$this->setReturn(1,'success',$data,$hasmore);
	}
	
	/*
	* 提交报名表单
	*/
	public function addMatch(){
		$return = array();
		$data = array(
			'name'=>!empty(Input::get('name'))?trim(Input::get('name')):'',
			'company' =>!empty(Input::get('company'))?trim(Input::get('company')):'',
			'address'=>!empty(Input::get('address'))?Input::get('address'):'',
			'province'=>!empty(Input::get('province'))?Input::get('province'):'',
			'province_id'=>!empty(Input::get('province_id'))?(int)Input::get('province_id'):0,
			'city'=>!empty(Input::get('city'))?Input::get('city'):'',
			'city_id'=>!empty(Input::get('city_id'))?(int)Input::get('city_id'):0,
			'area'=>!empty(Input::get('area'))?Input::get('area'):'',
			'area_id'=>!empty(Input::get('area_id'))?(int)Input::get('area_id'):0,
			'card'=>!empty(Input::get('card'))?Input::get('card'):'',
			'zip'=>!empty(Input::get('zip'))?Input::get('zip'):'',
			'mobile'=>!empty(Input::get('mobile'))?trim(Input::get('mobile')):'',
			'email'=>!empty(Input::get('email'))?trim(Input::get('email')):'',
			'cause'=>!empty(Input::get('cause'))?Input::get('cause'):'',
			'note'=>!empty(Input::get('note'))?Input::get('note'):'',
			'status'=>0,
			'addtime'=>time(),
			'update_time'=>time(),
			'invitationcode'=>!empty(Input::get('invitationcode'))?Input::get('invitationcode'):'',
		);
		$data['age'] = accorCardGetAge($data['card']);	
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
		
		//邀请码验证(查询邀请，是否存在)
		if(!empty($data["invitationcode"])){
			$allcode=array();
			$sql="select code from invitecode where status=2";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$allcode[]=$v['code'];
			}
			if(!in_array($data["invitationcode"],$allcode)){
				$this->setReturn(0,'参赛码填写有误，重新填写或不填！');exit;
			}
		}
		
		//存入log
		/*$path="../upload/user_match.txt";
		$log=@implode("|",$data);
		@file_put_contents($path, $log." \r\n", FILE_APPEND);*/
		
		$rlt = $this->model->updateMatchUser($data);
		if($rlt['code'] > 0)
		{
			$this->setReturn(1,'success',array());
		}
		else
		{
			$this->setReturn($rlt['code'],$rlt['msg']);	
		}
	}


}
 ?>