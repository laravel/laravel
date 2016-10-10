<?php 
/**
 * 新功能相关用户权限检测
 * @author :wang.hongli <[<email address>]>
 * @since :2016/07/12 [<description>]
 */
class ApiUserPermission extends ApiCommon{

	/**
	 * 获取会员权限列表
	 * @author :wang.hongli
	 * @since :2016/08/18 
	 */
	public function memberPermissionList($uid=0,$plat_form=0){
		$plat_form = 1^$plat_form ? 1 : 0;
		$list = DB::table('permission_config')->where('plat_form','<>',$plat_form)->where('status',0)->get(['id','name','addtime','flag','plat_form','icon']);
		if(empty($list)){
			return [];
		}
		foreach($list as $k=>&$v){
			$v['icon'] = $this->poem_url.$v['icon'];
		}
		//获取用户信息
		$apiUserCenter = new ApiUserCenter();
		$user_info = $apiUserCenter->getUserInfo($uid);
		//会员到期时间
		if($user_info['ismember'] == 1){
			$endtime  = DB::table('user_members')->where('uid',$uid)->first(['starttime','endtime']);
			$user_info['member_endtime'] = $endtime['endtime'];
		}
		//获取等级信息
		$grades = DB::table('grade')->orderBy('grade','asc')->get(['lnum','grade']);
		$data['user_info'] = $user_info;
		$data['list'] = $list;
		$data['grades'] = $grades;
		return $data;
	}

	/**
	 * 获取会员权限详细信息
	 * @author :wang.hongli
	 * @since :2016/08/18
	 */
	public function getMemberPerssionDetail($pid = 0){
		$detail = DB::table('permission_config_detail')->where('pid',$pid)->first();
		if(empty($detail)){
			return [];
		}
		$detail['pic'] = $this->poem_url.$detail['pic'];
		return $detail;
	}
	/**
	 * 获取权限对应关系
	 * @author :wang.hongli
	 * @since : 2016/07/12
	 */
	public function permission_config($uid=0,$pid=0){
		$permission = [];
		$conn = DB::table('permission_config')->where('status',0);
		if(!empty($pid)){
			$conn->where('pid',$pid);
		}
		$rs = $conn->get(['id','name','flag','pid','action']);
		if(empty($rs)){
			$rs = [];
		}
		$tmp = [];
		$flags = [];
		foreach($rs as $k=>$v){
			$tmp[$v['pid']][]= $v['action'];
			$flags[$v['action']] = 0;
		}
		foreach($tmp as $k=>$v){
			if($k=1){
				//判断是否为会员
				$apicheckPermission = App::make('apicheckPermission');
				$isMember = $apicheckPermission->isMember($uid);
				if($isMember){
					foreach($v as $kk=>$vv){
						$flags[$vv] = 1;
					}
				}
			}
			if($k == 2){
				//私信通权限检测
				$time = time();
				$letter_permission = DB::table('user_letter_permission')->where('uid',$uid)->where('endtime','>=',$time)->pluck('id');
				if(!empty($letter_permission)){
					foreach($v as $kk=>$vv){
						$flags[$vv] = 1;
					}
				}
			}
		}
		//导出作品次数检测  2下载次数超过限制
		if(isset($flags['free_down']) && $flags['free_down'] == 1){
			$time = time();
			$down_num = DB::table('down_opus_limit')->where('uid',$uid)->where('starttime','<=',$time)->where('endtime','>=',$time)->pluck('down_num');
			if(empty($down_num) || $down_num<=0){
				$flags['free_down'] = 2;
			}
		}
		return $flags;
	}
}

 ?>