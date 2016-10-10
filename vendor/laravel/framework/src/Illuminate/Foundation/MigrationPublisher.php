<?php namespace Illuminate\Foundation;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;

class MigrationPublisher {

	/**
	 * A cache of migrations at a given destination.
	 *
	 * @var array
	 */
	protected $existing = array();

	/**
	 * Create a new migration publisher instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Publish the given package's migrations.
	 *
	 * @param  string  $source
	 * @param  string  $destination
	 * @return array
	 */
	public function publish($source, $destination)
	{
		$add = 0;

		$published = array();

		foreach ($this->getFreshMigrations($source, $destination) as $file)
		{
			$add++;

			$newName = $this->getNewMigrationName($file, $add);

			$this->files->copy(
				$file, $newName = $destination.'/'.$newName
			);

			$published[] = $newName;
		}

		return $published;
	}

	/**
	 * Get the fresh migrations for the source.
	 *
	 * @param  string  $source
	 * @param  string  $destination
	 * @return array
	 */
	protected function getFreshMigrations($source, $destination)
	{
		$me = $this;

		return array_filter($this->getPackageMigrations($source), function($file) use ($me, $destination)
		{
			return ! $me->migrationExists($file, $destination);
		});
	}

	/**
	 * Determine if the migration is already published.
	 *
	 * @param  string  $migration
	 * @return bool
	 */
	public function migrationExists($migration, $destination)
	{
		$existing = $this->getExistingMigrationNames($destination);

		return in_array(substr(basename($migration), 18), $existing);
	}

	/**
	 * Get the existing migration names from the destination.
	 *
	 * @param  string  $destination
	 * @return array
	 */
	public function getExistingMigrationNames($destination)
	{
		if (isset($this->existing[$destination])) return $this->existing[$destination];

		return $this->existing[$destination] = array_map(function($file)
		{
			return substr(basename($file), 18);

		}, $this->files->files($destination));
	}

	/**
	 * Get the file list from the source directory.
	 *
	 * @param  string  $source
	 * @return array
	 */
	protected function getPackageMigrations($source)
	{
		$files = array_filter($this->files->files($source), function($file)
		{
			return ! starts_with($file, '.');
		});

		sort($files);

		return $files;
	}

	/**
	 * Get the new migration name.
	 *
	 * @param  string  $file
	 * @param  int  $add
	 * @return string
	 */
	protected function getNewMigrationName($file, $add)
	{
		return Carbon::now()->addSeconds($add)->format('Y_m_d_His').substr(basename($file), 17);
	}

}
