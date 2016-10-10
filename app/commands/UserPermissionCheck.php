<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UserPermissionCheck extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:permissioncheck';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'user over_time';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// $start_time = strtotime(date('Y-m-d'));
		// $start_time = $start_time+86399;
		// //选出到期用户
		// $uids = DB::table('user_permission')->where('type',1)->where('good_id',1)->where('over_time','<',$start_time)->lists('uid');
		// $sendToLeague = new SendToLeague();
		// $send_msg_info =['type'=>7,'fromid'=>2,'toid'=>6,'opusid'=>0,'name'=>'','addtime'=>time(),'commentid'=>0,'content'=>''];
		// if(!empty($uids)){
		// 	$content = '亲爱的中华诵读联合会会员，你好！你的会员已过有效期，请你继续支持中华诵读联合会的建设，由于会员的平台建设费是按照年度收取的，请及时缴纳相关费用，感谢你一直以来的信任和支持！【中华诵读联合会·外联部】';
		// 	$send_msg_info['content'] = serialize($content);
		// 	$sendToLeague->sendMessagePart($uids,$send_msg_info);
		// }
		// die;die;
		//1,选出即将到期的用户
		$start_time = strtotime(date('Y-m-d'));
		//一个月
		$month_start_time = $start_time+30*86400;
		$month_end_time = $month_start_time+86399;
		//半个月
		$half_month_start_time = $start_time+15*86400;
		$half_month_end_time = $half_month_start_time+86399;
		//7天
		$week_start_time = $start_time+7*86400;
		$week_end_time = $week_start_time+86399;
		//3天
		$three_start_time = $start_time + 3*86400;
		$three_end_time = $three_start_time + 86399;
		//选择月提醒的用户
		$mon_rs_uid = DB::table('user_permission')->where('good_id',1)->where('over_time','>=',$month_start_time)->where('over_time','<=',$month_end_time)->from('user_permission')->lists('uid');
		//选择半个月需要提醒的用户
		$half_mon_rs_uid = DB::table('user_permission')->where('good_id',1)->where('over_time','>=',$half_month_start_time)->where('over_time','<=',$half_month_end_time)->from('user_permission')->lists('uid');
		//选择一周需要提醒的用户
		$week_rs_uid = DB::table('user_permission')->where('good_id',1)->where('over_time','>=',$week_start_time)->where('over_time','<=',$week_end_time)->from('user_permission')->lists('uid');
		//选择3三需要提醒的用户
		$three_days_rs_uid = DB::table('user_permission')->where('good_id',1)->where('over_time','>=',$three_start_time)->where('over_time','<=',$three_end_time)->from('user_permission')->lists('uid');

		$sendToLeague = new SendToLeague();
		$send_msg_info =['type'=>7,'fromid'=>2,'toid'=>6,'opusid'=>0,'name'=>'','addtime'=>time(),'commentid'=>0,'content'=>''];
		if(!empty($mon_rs_id)){
			$content = '您的中华诵读联合会会员证的有效期还有30天到期，请及时续费！如有疑问，请咨询：010-84496899';
			$send_msg_info['content'] = serialize($content);
			$sendToLeague->sendMessagePart($mon_rs_id,$send_msg_info);
		}
		if(!empty($half_mon_rs_uid)){
			$content = '您的中华诵读联合会会员证的有效期还有15天到期，请及时续费！如有疑问，请咨询：010-84496899';
			$send_msg_info['content'] = serialize($content);
			$sendToLeague->sendMessagePart($half_mon_rs_uid,$send_msg_info);
		}
		if(!empty($week_rs_uid)){
			$content = '您的中华诵读联合会会员证的有效期还有7天到期，请及时续费！如有疑问，请咨询：010-84496899';
			$send_msg_info['content'] = serialize($content);
			$sendToLeague->sendMessagePart($week_rs_uid,$send_msg_info);
		}
		if(!empty($three_days_rs_uid)){
			$content = '您的中华诵读联合会会员证的有效期还有3天到期，请及时续费！如有疑问，请咨询：010-84496899';
			$send_msg_info['content'] =serialize($content);
			$sendToLeague->sendMessagePart($three_days_rs_uid,$send_msg_info);
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
