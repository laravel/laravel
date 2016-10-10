<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronDelAllActiveUser extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronDelAllActiveUser';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '删除月非活跃用户';

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
		$redisActiveUser = new RedisActiveUser();
		$redisActiveUser->delAllNoActiveUser();					
	}
}
