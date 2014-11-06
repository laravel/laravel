<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup a new Laravel project';

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$factory = $app->make('Illuminate\Filesystem\FilesystemManager', [$app]);
		$this->filesystem = $factory->createLocalDriver(['root' => $app->make('path.base')]);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->setupGitignore();
		$this->setupEnv();
		$this->call('key:generate');
	}

	/**
	 * Remove composer.lock from gitignore file.
	 *
	 * @return void
	 */
	private function setupGitignore()
	{
		$content = $this->filesystem->get('.gitignore');

		$lines = array_filter(explode(PHP_EOL, $content), function ($line) {
			return $line !== 'composer.lock';
		});

		$this->filesystem->put('.gitignore', implode(PHP_EOL, $lines));
	}

	/**
	 * Setting up .env files.
	 *
	 * @return void
	 */
	private function setupEnv()
	{
		$from = '.env.example';
		$to   = '.env';

		if (!$this->filesystem->exists($to)) {
			$this->filesystem->copy($from, $to);
		}
	}

}
