<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportFans extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:importfans';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '把某个人粉丝导入redis';

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
		ini_set('memory_limit',-1);
		return false;
		//用户表获取拼音首字母
		$user = DB::table('user')->select('id','lnum','repostnum','praisenum','addtime')->where('isleague',1)->where('isdel',0)->get();
		foreach($user as $k=>$v){
			DB::table('league_user')->insert(array('id'=>0,'uid'=>$v['id'],'lnum'=>$v['lnum'],'repostnum'=>$v['repostnum'],'praisenum'=>$v['praisenum'],'addtime'=>$v['addtime']));
		}
// 		$redis =  new RedisCommon();
// 		$defult = $redis->getDefaultConnect();
// 		$rs = DB::table('user')->orderBy('id','asc')->skip(0)->take(2)->lists('id');
// 		if(!empty($rs)){
// 			foreach($rs as $k=>$v){
// 				$tmp_rs = array();
// 				//关注自己的人
// 				$tmp_rs = DB::table('follow')->where('fid',$v)->select('uid','dateline')->get();
// 				if(empty($tmp_rs)) continue;
// 				$import_arr = array();
// 				foreach($tmp_rs as $key=>$value){
// 					$import_arr[] = array('uid'=>$value['uid'],'fid'=>$value['fid']);
// 				}
// 				$redis_key = 'fans:'.$v;
// 				var_dump($defult->zadd($redis_key,$import_arr));
// 				unset($tmp_rs);
// 				unset($tmp_rs);
// 			}
// 		}
		//将opus表添加写，读名称
// 		$rs = DB::table('poem')->select('id','writername')->get();
// 		$last_rs = array();
// 		if(!empty($rs)){
// 			foreach($rs as $k=>$v){
// 				$last_rs[$v['id']] = $v;
// 			}
// 		}
// 		unset($rs);
// 		foreach($last_rs as $k=>$v){
// 			if(empty($k) || empty($v['writername'])) continue;
// 			DB::table('opus')->where('poemid',$k)->update(array('writer'=>$v['writername']));
// 		}
		
// 		$uids = DB::table('opus')->select('uid')->groupBy('uid')->lists('uid');
// 		$c = count($uids);
// 		for($i=0;$i<$c;$i=$i+1000){
// 			$tmp_uids = array_slice($uids, $i,1000);
// 			$users = DB::table('user')->select('id','nick')->whereIn('id',$tmp_uids)->get();
			
// 			foreach($users as $k=>$v){
// 				if(empty($v['id'])) continue;
// 				DB::table('opus')->where('uid',$v['id'])->update(array('reader'=>$v['nick']));
// 			}
// 			unset($tmp_uids);
// 			unset($users);
// 		}
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
