<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;

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

	private function setupGitignore()
	{
		$content = $this->filesystem->get('.gitignore');
		$gitignore = [];

		foreach (explode(PHP_EOL, $content) as $line) {
			if ($line !== 'composer.lock') {
				$gitignore[] = $line;
			}
		}

		$this->filesystem->put('.gitignore', implode(PHP_EOL, $gitignore));
	}

	/**
	 * Setting up .env files.
	 */
	private function setupEnv()
	{
		$from = '.env.example';
		$to   = '/.env';

		if (!$this->filesystem->exists($to)) {
			$this->filesystem->copy($from, $to);
		}
	}
}
