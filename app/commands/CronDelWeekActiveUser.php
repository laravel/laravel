<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronDelWeekActiveUser extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronDelWeekActiveUser';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '删除周活跃用户';

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
		$time = time()-7*24*3600;
		DB::table('week_active_user')->where('addtime','<=',$time)->delete();
	}
}
