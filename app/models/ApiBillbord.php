<?php
/**
*	主播大赛，作品大赛模型
*	@author:wang.hognli
*	@since:2014/11/08
*/ 
class ApiBillbord extends ApiCommon{

	protected $table = 'subbillnav'; //表明
	public $timestamps = false;//数据表列自动被维护为假
	/**
	*	获取作品排序列表,主播排序列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	**/
	public function getSubBillNav($type=1,$flag=0)
	{
		$arr = array();
		$subbillnav = ApiBillbord::whereRaw('type = ? and flag = ?',array($type,$flag))->orderBy('inserttime','desc')->get();
		foreach($subbillnav as $k=>$row)
		{
			$arr[$k]['sid'] = $row->sid;
			$arr[$k]['inserttime'] = $row->inserttime;
		} 
		return $arr;
	}

	/**
	*	获取榜单列表
	*	@author:wang.hongli
	*	@since:2014/11/08
	*	flag 0作品 1主播
	*	sid 榜单列表id
	*	type 1周 2月 3年
	*/
	public function getOpusUserList($uid,$params=array('count'=>20,'pageIndex'=>1),$flag=0,$type=1,$sid=0)
	{	
		$data = array();
		$flag = (int)$flag;
		switch ($flag) {
			//作品
			case 0:
				//空的直接静态页面
				$data = ApiCommonStatic::getOpusList($uid,$params,$flag,$type,$sid);
				break;
			case 1:
				$data = ApiCommonStatic::getUserList($uid,$params,$flag,$type,$sid);	
				break;
		}
		return $data;
	}


	/**
	*	获取奖状列表
	*	@author:wang.hongli
	*	@since:2014/11/16
	**/
	public function getDiploma($uid,$otherId,$flag=0,$pageIndex = 1,$count=200)
	{
		$data = array();
		if(!empty($uid))
		{	
			if(!empty($otherId))
			{
				$uid = $otherId;
			}
			$uid = intval($uid);
			$conn= DB::table('diploma')->select('sort','flag','type','addtime','ext')->where('uid',$uid);

			if(empty($flag)){
				$conn->where('flag','<>',2);
			}
			$offSet = ($pageIndex-1)*$count;
			$count++;
			$rs = $conn->orderBy('addtime','desc')->skip($offSet)->take($count)->get();
			$hasmore = 0;
			if(!empty($rs))
			{
				$user = DB::table('user')->where('id',$uid)->first(array('id','nick','sportrait','isleague'));
				foreach($rs as $key=>&$value)
				{
					$value['sportrait'] = $this->poem_url.ltrim($user['sportrait'],'.');
					$value['id'] = $user['id'];
					$value['nick'] = $user['nick'];
					$value['isleague'] = $user['isleague'];
					$value['sportrait'] = $this->poem_url.ltrim($user['sportrait'],'.');
				}
				if(count($rs) >= $count){
					$hasmore = 1;
					array_pop($rs);
				}
			}
			$data = $rs;
		}
		$data['hasmore'] = $hasmore;
		return $data;
	}
}
 ?>