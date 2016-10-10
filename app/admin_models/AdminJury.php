<?php 

/**
*	评委模型
*	@author:wang.hongli
*	@since:2015/05/15
**/
class AdminJury extends AdminCommon
{
	protected $table = 'jury';

	/**
	*	删除评委
	*	@author:wang.hongli
	*	@since:2015/05/16
	*	@param:uid 评委id 
	**/
	public function delJury($id=0,$status = 0)
	{
		if(empty($id) || empty($status)){
			return false;
		}
		$status = ($status==1) ? 2 : 1;
		try {
			DB::table('jury')->where('id',$id)->update(['status'=>$status]);
			return true;
		} catch (Exception $e) {
			return false;	
		}
	}
	
}