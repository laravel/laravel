<?php 
class ApiUserCenter extends ApiCommon {


	public function getUserInfo($id=0){
		if(empty($id)){
			return false;
		}
		$rs = DB::table('user')->where('id',$id)->first(['id','nick','phone','gender','lnum','repostnum','attention','praisenum','fans','opusnum','grade','sportrait','portrait','albums','signature','authtype','opusname','isedit','issex','addtime','bgpic','email','pwd','authconent','teenager','isleague']);
		if(empty($rs)){
			$rs = [];
		}
		$rs['portrait'] = !empty($rs['portrait']) ? $this->poem_url.ltrim($rs['portrait'],'.') : '';
		$rs['sportrait'] = !empty($rs['sportrait'])  ? $this->poem_url.ltrim($rs['sportrait'],'.') : '';
		$rs['bgpic'] = !empty($rs['bgpic']) ? $this->poem_url.ltrim($rs['bgpic'],'.') : '';
		//判断是否为会员
		$apicheckPermission = App::make('apicheckPermission');
		$rs['ismember'] = $apicheckPermission->isMember($id);
		return $rs;
	}
	/**
	 * 获取个人中心列表项
	 * @author :wang.hongli
	 * @since :2016/07/18
	 * @todo : 项目上线稳定后加redis缓存
	 */
	public function userCenterList($uid=0,$otherId=0){
		$flag = 0;
		if(!empty($otherId)){
			$flag = 1;
		}
		$tmp_list = DB::table('personal_homepage')->where('status',0)->where('flag',$flag)->orderBy('id','asc')->orderBy('sort','asc')->get();
		$list = [];
		if(!empty($tmp_list)){
			foreach($tmp_list as $k=>$v){
				$v['icon'] = $this->poem_url.'/upload/homepageicon/'.$v['icon'];
				$list[$v['category']][] = $v;
			}
		}
		return $list;
	}
}
?>