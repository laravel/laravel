<?php namespace App\Console\Commands;

use App\Contracts\Inspiring as InspiringContract;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InspireCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'inspire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Display an inspiring quote';

	/**
	 * The inspiring class
	 *
	 * @var InspiringContract
	 */
	protected $inspiring;

	/**
	 * Create a new command instance.
	 *
	 * @param InspiringContract $inspiring
	 * @return void
	 */
	public function __construct(InspiringContract $inspiring)
	{
		$this->inspiring = $inspiring;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->comment(PHP_EOL.$this->inspiring->quote().PHP_EOL);
	}

}
