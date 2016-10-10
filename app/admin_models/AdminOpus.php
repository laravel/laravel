<?php 
/**
*	后台作品model
*	@author:wang.hongli
*	@since:2015/08/22
**/
class AdminOpus extends AdminCommon
{
	/**
	*	后台将作品从分类中移除
	*	@author:wang.hongli
	*	@since:2015/08/23
	**/
	public static function catremove($opusid=0,$competitionid=0)
	{
		$conn = DB::table('competition_opus_rel')->where('opusid','=',$opusid);
		if($competitionid>0){
			$conn=$conn->where('competitionid','=',$competitionid);
		}
		$rs = $conn->delete();
		if($rs)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * 修改用户等级
	 * @author:wang.hongli
	 * @since:2016/04/21
	 */
	//根据人的id判断用户等级
	public function setUserGrade($uid)
	{
		if(empty($uid)) return;
		//查看用户当前收听数，等级等信息
		$userInfo = DB::table('user')->where('id','=',$uid)->first(array('id','grade','lnum'));
		if(empty($userInfo)) return;
		//根据收听数获取对应的等级
		$grade = DB::table('grade')->where('lnum','>=',$userInfo['lnum'])->pluck('grade');
		if(empty($grade)){
			$grade = 10;
		}
		if($userInfo['grade'] == $grade){
			return;
		}else{
			DB::table('user')->where('id','=',$uid)->update(array('grade'=>$grade));
		}
		return;
	}
	
}
 ?>