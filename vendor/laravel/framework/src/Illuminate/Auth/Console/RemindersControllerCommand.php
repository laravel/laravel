<?php namespace Illuminate\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RemindersControllerCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth:reminders-controller';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a stub password reminder controller';

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new reminder table command instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$destination = $this->laravel['path'].'/controllers/RemindersController.php';

		if ( ! $this->files->exists($destination))
		{
			$this->files->copy(__DIR__.'/stubs/controller.stub', $destination);

			$this->info('Password reminders controller created successfully!');

			$this->comment("Route: Route::controller('password', 'RemindersController');");
		}
		else
		{
			$this->error('Password reminders controller already exists!');
		}
	}

}
