<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronImportPoem extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronImportPoem';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$plan_time = strtotime(date('Y-m-d H:i',time()));
		$rs = DB::table('poem_xls')->where('plan_time',$plan_time)->where('status',0)->get();
		if(empty($rs)) return;
		$adminImport = new AdminImport();
		foreach($rs as $k=>$v){
			$excelName = DB::table('poem_xls')->where('id',$v['id'])->pluck('name');
			$excelpath = public_path('importexcel/'.$excelName);
			$is_success = $adminImport->importPoem($excelpath);
			if($is_success === true){
				DB::table('poem_xls')->where('id',$v['id'])->update(array('status'=>2));
				echo $plan_time."\n";
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
