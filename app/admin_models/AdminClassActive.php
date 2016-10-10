<?php
/**
 * 培训班后台管理model
 * @author:wang.hongli
 * @since:2016/06/13
 */
class AdminClassActive extends AdminCommon{
	
	/**
	 * 获取培训班
	 * @author:wang.hongli
	 * @since:2016/06/14
	 */
	public function getActiveClasses(){
		$tmp_classList = DB::table('class_active')->where('isdel',0)->orderBy('id','desc')->get(array('id','name'));
		$classList = array();
		if(!empty($tmp_classList)){
			foreach($tmp_classList as $key=>$value){
				$classList[$value['id']] = $value['name'];
			}
		}
		return $classList;
	}
}