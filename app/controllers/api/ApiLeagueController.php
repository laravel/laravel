<?php 
/**
*	朗诵会控制器
*	@author:zhang.zongliang
*	@since:2015/05/10
**/
class ApiLeagueController extends ApiCommonController
{

	/**
	*	获取朗诵会员
	**/
	public function getLeagueList()
	{
		$where = array();
		$where['type'] = 1;
		$where['over_time'] = time();
		$page = Input::get('pageIndex')?(int)Input::get('pageIndex'):1;//当前页
		$page_size = Input::get('count')?(int)Input::get('count'):20;//每页多少条
		
		$ApiLeague = new ApiLeague;
		$rlt = $ApiLeague->getLeague($where,$page,$page_size);
		if('nolog' === $rlt){
			$this->setReturn(-101,'请登录');
		}else{
			$this->setReturn(1,'success',$rlt['list'],$rlt['hasmore']);
		}
		
		
	}
}