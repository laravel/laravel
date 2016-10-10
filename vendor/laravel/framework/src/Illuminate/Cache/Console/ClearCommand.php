<?php namespace Illuminate\Cache\Console;

use Illuminate\Console\Command;
use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;

class ClearCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cache:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Flush the application cache";

	/**
	 * The cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * The file system instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new cache clear command instance.
	 *
	 * @param  \Illuminate\Cache\CacheManager  $cache
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(CacheManager $cache, Filesystem $files)
	{
		parent::__construct();

		$this->cache = $cache;
		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->cache->flush();

		$this->files->delete($this->laravel['config']['app.manifest'].'/services.json');

		$this->info('Application cache cleared!');
	}

}
