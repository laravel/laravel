<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCreateFinishComp extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronCreateFinishComp';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '比赛结束计划任务,结束当天执行';

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
		$time = time();
		$start_time = strtotime(date('Y-m-d',time()));
		$end_time = $start_time+86399;
		$competitionList = DB::table('competitionlist')->where('isfinish',0)->where('endtime','>',$start_time)->where('endtime','<=',$end_time)->get(array('id','endtime','name'));
		if(empty($competitionList)){
			die('no competitionid');
		}
		foreach($competitionList as $k=>$v){
			$competition_opus_rel_arr = DB::table('competition_opus_rel')->where('competitionid',$v['id'])->lists('opusid');
			if(empty($competition_opus_rel_arr)){
				continue;
			}
			$competition_opus_rel_str = implode(',', $competition_opus_rel_arr);
			//opus_info
			$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,lnum,lnum as premonthnum,repostnum,praisenum,addtime,opustime,writer from opus where id in ( ".$competition_opus_rel_str."  ) and isdel =0 order by lnum desc,repostnum desc ,praisenum desc limit 1000" ;
			$rs = DB::select($sql);
			if(empty($rs)) continue;
			$tmp_uid = [];
			foreach($rs as $key=>$value){
				if(in_array($value['uid'], $tmp_uid)){
					continue;
				}
				$tmp_uid[] = $value['uid'];
			}
			$tmp_user_info = DB::table('user')->whereIn('id',$tmp_uid)->get(array('id','nick','gender','grade','sportrait','authtype','teenager','isleague'));
			if(empty($tmp_user_info)) continue;
			$user_info = [];
			foreach($tmp_user_info as $key=>$value){
				$user_info[$value['id']] = $value;
			}
			$data = [];
			foreach($rs as $key=>$value){
				if(empty($user_info[$value['uid']])) continue;
				$info = $user_info[$value['uid']];
				$tmp_user = [];
				$tmp_user['id'] = $value['id'];
				$tmp_user['uid'] = $value['uid'];
				$tmp_user['commentnum'] = $value['commentnum'];
				$tmp_user['poemid'] = $value['poemid'];
				$tmp_user['name'] = $value['name'];
				$tmp_user['url'] = $value['url'];
				$tmp_user['lyricurl'] = $value['lyricurl'];
				$tmp_user['type'] = $value['type'];
				$tmp_user['firstchar'] = $value['firstchar'];
				$tmp_user['premonthnum'] = $value['premonthnum'];
				$tmp_user['lnum'] = $value['lnum'];
				$tmp_user['repostnum'] = $value['repostnum'];
				$tmp_user['praisenum'] = $value['praisenum'];
				$tmp_user['addtime'] = $value['addtime'];
				$tmp_user['opustime'] = $value['opustime'];
				$tmp_user['writername'] = $value['writer'];
				$tmp_user['nick'] = $info['nick'];
				$tmp_user['gender'] = $info['gender'];
				$tmp_user['grade'] = $info['grade'];
				$tmp_user['sportrait'] = $info['sportrait'];
				$tmp_user['authtype'] = $info['authtype'];
				$tmp_user['teenager'] = $info['teenager'];
				$tmp_user['isleague'] = $info['isleague'];
				$data[] = $tmp_user;
			}
			if(!empty($data)){
				$complistid = $v['id'];
				$content = serialize($data);
				$insert_data = [
					'complistid' => $v['id'],
					'content'=>serialize($data),
					'addtime'=>$time
				];
				DB::table('staticcomplog')->insert($insert_data);
				$isfinish = 1;
				DB::table('competitionlist')->where('id',$v['id'])->update(['haslist'=>1,'isfinish'=>$isfinish]);
			}else{
				continue;
			}
		}
	}

}
