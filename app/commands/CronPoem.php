<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronPoem extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronPoem';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '计划任务和伴奏相关的修改';

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
	 * @param:type 1 表示修改当天上传伴奏的下载数
	 */
	public function fire()
	{
		//区分修改类型
		$type = $this->argument('type');
		if(empty($type)) return;
		switch($type){
			case 1:
				$this->modifyPoemDowNum();
				break;
		}
		
	}
	/**
	 * 修改当天伴奏的下载数-每5分钟增加一次
	 * @author:wang.hongli
	 * @since:2016/05/15
	 */
	protected function modifyPoemDowNum(){
		$total = 800000;
		$current_time = time();
		$start_time = strtotime(date('Y-m-d',$current_time));
		$end_time = $start_time + 86400;
		//每次增加5000-10000次
		$poem = DB::table('poem')->whereBetween('addtime',array($start_time,$end_time))->lists('id');
		if(empty($poem)) return;
		foreach($poem as $k=>$v){
			$down_num = mt_rand(2500,5500);
			DB::table('poem')->where('id',$v)->increment('downnum',$down_num);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('type',InputArgument::REQUIRED,'modifyPoem'),
// 			array('flag',InputArgument::REQUIRED,'poem')
		);
	}

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
