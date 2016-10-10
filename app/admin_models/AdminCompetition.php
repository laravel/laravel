<?php
/**
 * 比赛后台model
 * @author:wang.hongli
 * @since:2016/08/01
 */
class AdminCompetition extends AdminCommon{
	
	/**
	 * 获取获取添加过评委的比赛
	 * @author :wang.hongli
	 * @since :2016/08/01
	 */
	public function getJueryCompetitionList(){
		$comp_ids = DB::table('jury')->where('status',2)->groupBy('type')->lists('type');
		$return = [0=>'全部'];
		if(!empty($comp_ids)){
			$jury_competition = DB::table('competitionlist')->whereIn('id',$comp_ids)->orderBy('id','desc')->get(['id','name']);
			foreach($jury_competition as $k=>$v){
				$return[$v['id']] = $v['name'];
			}
		}
		return $return;
	}

	/**
	 * 获取所有比赛列表
	 * @author :wang.hongli
	 * @since :2016/08/02
	 */
	public function getCompetitionList($data=['isfinish'=>0]){
		$conn = DB::table('competitionlist');
		if($data['isfinish'] === 0 || $data['isfinish'] ===1){
			$conn->where('pid','<>',0)->where('isfinish',$data['isfinish']);
		}
		$competitionList = $conn->orderBy('id','desc')->get(['id','name','isfinish']);
		$return = [];
		if(!empty($competitionList)){
			foreach($competitionList as $k=>$v){
				$return[$v['id']] = $v['name'];
			}
		}
		return $return;
	}
}