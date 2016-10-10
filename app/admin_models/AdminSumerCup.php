<?php 
/**
*	夏青杯模型
*	@author:wang.hongli
*	@since:2015/05/15
**/
class AdminSumerCup extends AdminCommon
{

	protected $table = 'summercup';
	/**
	* 	后台获取夏青杯用户列表
	*	@author:wang.hongli
	*	@since:2015/05/15
	**/
	public function getSummCupUList($pageSize=20)
	{
		$rs = self::select('id','uid','card','name','nick_name','company','address','address','zip','mobile','email','cause','addtime','pass_time','is_pay','pay_time','year','opus_id')
			->orderBy('addtime','desc')
			->paginate($pageSize);
			
		return $rs;
	}

	/**
	*	后台添加夏青杯认证用户
	*	@author:wang.hongli
	*	@since:2015/05/16
	*/
	public function addSumUser($uid)
	{
		$arr['uid'] = $uid;
		$arr['type'] = 2;
		$arr['over_time'] = strtotime('2015-08-01');
		$arr['update_time'] = time();
		if(!empty(DB::table('user_permission')->where('uid','=',$uid)->where('type','=',2)->get()))
		{
			return 'exists';
		}
		else
		{
			return DB::table('user_permission')->insert($arr);
		}
		
	}

	/**
	*	获取后台添加的夏青杯，诵读联盟用户
	*	@author:wang.hongli
	*	@since:2015/05/15
	*	@param:type 1 朗诵会 2夏青杯
	**/
	public function getAdmAddUser($type=1,$pageSize=20)
	{
		if($type == 1)
		{
			$rs = DB::table('user_permission')
				->select('user_permission.id','user_permission.uid','user_permission.type','user_permission.over_time','user_permission.update_time')
				->leftJoin('summercup','user_permission.uid','=','summercup.uid')
				->whereNull('summercup.id')
				->where('user_permission.type','=',$type)
				->orderBy('user_permission.update_time','desc')
				->paginate($pageSize);
		}
		elseif($type ==2 )
		{
			$rs = DB::table('user_permission')
				->select('user_permission.id','user_permission.uid','user_permission.type','user_permission.over_time','user_permission.update_time')
				->leftJoin('league','user_permission.uid','=','league.uid')
				->whereNull('league.id')
				->where('user_permission.type','=',$type)
				->orderBy('user_permission.update_time','desc')
				->paginate($pageSize);
		}
		
		if(!empty($rs))
		{
			$uid = array();
			foreach($rs as $k=>$v)
			{
				$uid[$v['uid']] = $v['uid'];
			}
			//选出用户信息
			$userinfo = DB::table('user')->select('id','nick')->whereIn('id',array_values($uid))->get();

			return array('rs'=>$rs,'userinfo'=>$userinfo);
		}
	}

	/**
	*	后台添加诵读联盟用户
	*	@author:wang.hongli
	*	@since:2015/05/16
	**/
	public function addLeagueUser($uid)
	{
		//判断user_permission中是否存在
		$tmp = DB::table('user_permission')->where('uid','=',$uid)->where('type','=',1)->get();
		if(!empty($tmp))
		{
			DB::table('user')->where('id',$uid)->update(['authtype'=>1,'isleague'=>1]);
			// DB::table('user_permission')->where('uid',$uid)->where('type',1)->update()
			$add_time = 365*86400;
			$sql = "update user_permission set over_time = over_time+? ,update_time=? where uid = ? and type=1";
			DB::update($sql,[$add_time,time(),$uid]);
			$id = DB::table('league_user')->where('uid',$uid)->pluck('id');
			if(empty($id)){
				//将数据插入到league_user联合会冗余表中
				$user_info = DB::table('user')->where('id',$uid)->first(array('praisenum','lnum','repostnum'));
				DB::table('league_user')->insert(array('id'=>0,'uid'=>$uid,'praisenum'=>$user_info['praisenum'],'lnum'=>$user_info['lnum'],'repostnum'=>$user_info['repostnum'],'addtime'=>time()));
			}	
			return true;
		}
		else
		{
			$time = time();
			$arr['uid'] = $uid;
			$arr['type'] = 1;
			$arr['over_time'] = $time+365*24*3600;
			$arr['update_time'] = $time;

			if(!DB::table('user_permission')->insert($arr))
			{
				return false;
			}
			DB::table('user')->where('id','=',$uid)->update(array('isleague'=>1));

			return true;
		}
	}

}
 ?>