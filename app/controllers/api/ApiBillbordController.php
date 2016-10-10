<?php 
/**
*	主播大赛，作品大赛控制器
*	@author:wang.hognli
*	@since:2014/11/08
*/
class ApiBillbordController extends ApiCommonController
{

	/**
	* 作品导航列表 ,主播导航列表
	* @author:wang.hognli
	* @since:2014/11/08
	* flag 0 作品导航列表 1主播导航列表
	*/
	public function getBillNav()
	{
		$info = ApiCommonStatic::viaCookieLogin();
		if(empty($info))
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$flag = !empty(Input::get('flag')) ? Input::get('flag') : 0;
		switch ($flag) {
			case 0:
				$arr = array(
					array('type'=>1,'name'=>'周赛','flag'=>0),
					array('type'=>2,'name'=>'月赛','flag'=>0),
					array('type'=>3,'name'=>'年赛','flag'=>0)
				);
				break;
			case 1:
				$arr = array(
					array('type'=>1,'name'=>'周赛','flag'=>1),
					array('type'=>2,'name'=>'月赛','flag'=>1),
					array('type'=>3,'name'=>'年赛','flag'=>1)
				);
				break;
		}
		$this->setReturn(1,'success',$arr);
	}
	/**
	*	获取作品排序列表,主播排序列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	**/
	public function getSubBillNav()
	{	
		$info = ApiCommonStatic::viaCookieLogin();
		if(empty($info))
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$type = !empty(Input::get('type')) ? Input::get('type') : 1; //1周2月3年
		$flag = !empty(Input::get('flag')) ? Input::get('flag') : 0; //0作品 1主播
		$apiBillbord = new ApiBillbord;
		$data = $apiBillbord->getSubBillNav($type,$flag);
		if($data === 'nolog')
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$this->setReturn(1,'success',$data);
	}

	/**
	*	获取作品或者博主人的列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	*/
	public function getOpusUserList()
	{	
		$info = ApiCommonStatic::viaCookieLogin();
		if(empty($info))
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$uid = $info['id'];
		$apiBillbord = new ApiBillbord;
		$type = !empty(Input::get('type')) ? Input::get('type') : 1 ; //1周 2月 3年
		$flag = !empty(Input::get('flag')) ? Input::get('flag') : 0 ; //0作品 1 主播
		$sid = !empty(Input::get('sid')) ? Input::get('sid') : 0 ; // sid 榜单列表id
		$params['count'] = !empty(Input::get('count')) ? Input::get('count') : 100;
		$params['pageIndex'] = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
		$data = $apiBillbord->getOpusUserList($uid,$params,$flag,$type,$sid);
		if($data === 'nolog')
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$hasmore = $data['hasmore'];
		unset($data['hasmore']);
		$this->setReturn(1,'success',$data,$hasmore);
	}


	/**
	*	获取奖状列表
	*	@author:wang.hongli
	*	@since:2014/11/16
	**/
	public function getDiploma()
	{
		$info = ApiCommonStatic::viaCookieLogin();
		if(empty($info))
		{
			$this->setReturn(-101,'未登录');
			return;
		}
		$otherId = intval(Input::get('otherId',0));
		$flag = intval(Input::get('flag',0));
		$pageIndex = intval(Input::get('pageIndex',1));
		$count = intval(Input::get('count',2));
		$apiBillbord = new ApiBillbord;
		$data = $apiBillbord->getDiploma($info['id'],$otherId,$flag,$pageIndex,$count);
		$hasmore = $data['hasmore'];
		unset($data['hasmore']);
		$this->setReturn(1,'success',$data,$hasmore);
	}
}

 ?>