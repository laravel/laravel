<?php 
/**
*将用户放入结束活动列表 model
*@author:wang.hongli
*@since:2106/07/06
**/
class AdminFinishCompetition extends AdminCommon{
	private $_config;
	public function __construct(){
		parent::__construct();
		$this->_config = [
			4=>'诵读比赛',
			5=>'诵读比赛',
			6=>'诗文比赛',
			7=>'诗文比赛'
		];
	}
	public function getFinishCompetitionList(){
		$list = DB::table('competitionlist')->where('isfinish',1)->whereIn('pid',[4,5,6,7])->orderBy("id",'desc')->get(['id','name','pid']);
		$competitionlist = [];
		$competitionid_name = [];
		if(!empty($list)){
			foreach($list as $k=>$v){
				if(in_array($v['pid'],[4,5])){
					$competitionlist[$this->_config[$v['pid']]][$v['id']] = $v['name'];
				}elseif(in_array($v['pid'],[6,7])){
					$competitionlist[$this->_config[$v['pid']]][$v['id']] = $v['name'];
				}
				$competitionid_name[$v['id']] = $v['name'];
			}
		}
		return ['competitionlist'=>$competitionlist,'competitionid_name'=>$competitionid_name];
	}
}
?>