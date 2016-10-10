<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UndoUserPermission extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:undouserpermission';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 会员到期后取消对应权限,在当天23:59:59秒之前执行
	 * @author:wang.hongli
	 * @since:2016/04/05
	 * @return mixed
	 */
	public function fire()
	{
		//会员到期后取消相应权限
		$start_time = strtotime(date('Y-m-d'));
		$end_time = $start_time + 86400;
		$rs = DB::table('user_permission')->where('over_time','>=',$start_time)->where('over_time','<=',$end_time)->where('type','=',1)->get();
		// $rs = DB::table('user_permission')->where('over_time','<=',$start_time)->where('type',1)->get();
		$auth_uids = [];
		if(!empty($rs)){
			$arr = array('中华诵读联合会会员、','中华诵读联合会会员','中华诵读联合会会员,','中华诵读联合会会员。');
			foreach($rs as $k=>$v){
				if($v['good_id'] == 1 && $v['type'] == 1 && !empty($v['uid'])){
					//修改会员认证信息
					$authconent = DB::table('user')->where('id',$v['uid'])->pluck('authconent');
					if(empty($authconent)){
						 $authconent = '中华诵读联合会会员';
					}
					//user_auth_content 保留用户认证信息
					DB::table('user_auth_content')->insert(['uid'=>$v['uid'],'auth_content'=>$authconent]);
					if($authconent == '中华诵读联合会会员' ){
						$auth_uids[] = $v['uid'];
					}
					$authconent = str_replace($arr, '', $authconent);
					DB::table('user')->where('id',$v['uid'])->update(array('authconent'=>$authconent));
					$tmp_uid[] = $v['uid'];
				}
			}
			if(!empty($tmp_uid)){
				//取消会员
				DB::table('user')->whereIn('id',$tmp_uid)->update(array('isleague'=>0));
				//删除诵读联合会中相关记录
				DB::table('league_user')->whereIn('uid',$tmp_uid)->delete();
				//删除诵读联合会权限
				// DB::table('user_permission')->where('type','=',1)->where('good_id',1)->whereIn('uid',$tmp_uid)->delete();
				//修改诵读会表单状态
				DB::table('league')->whereIn('uid',$tmp_uid)->update(['status'=>0]);
			}
			if(!empty($auth_uids)){
				DB::table('user')->whereIn('id',$auth_uids)->update(['authtype'=>0]);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
// 	protected function getArguments()
// 	{
// 		return array(
// 			array('example', InputArgument::REQUIRED, 'An example argument.'),
// 		);
// 	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
