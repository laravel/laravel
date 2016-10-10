<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ComImportPraise extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:comimportpraise';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '导入作品赞数';

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
		$size = 100000;
		$count = DB::table('praise')->count('id');
		$redis =  new RedisCommon();
		$defult = $redis->getDefaultConnect();
		$start = microtime(true);
		for($i = 0;$i<=$count;$i+=$size){
			$rs = DB::table('praise')->skip($i)->take($size)->get();
			$tmp_user_rs = array();
			if(empty(!$rs)){
				foreach($rs as $k=>$v){
					$tmp_user_rs[$v['uid']][] = $v['opusid'];
				}
				foreach($tmp_user_rs as $k=>$v){
					$tmp_key = 'praise:user:'.$k.':opus';
					$defult->sadd($tmp_key,$v);
				}
			}
			unset($tmp_user_rs);
			unset($tmp_key);
			unset($rs);
		}
		print_r(microtime(true)-$start);
		
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
