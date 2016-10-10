<?php namespace Illuminate\Database\Migrations;

use Closure;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The registered post create hooks.
	 *
	 * @var array
	 */
	protected $postCreate = array();

	/**
	 * Create a new migration creator instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Create a new migration at the given path.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  string  $table
	 * @param  bool    $create
	 * @return string
	 */
	public function create($name, $path, $table = null, $create = false)
	{
		$path = $this->getPath($name, $path);

		// First we will get the stub file for the migration, which serves as a type
		// of template for the migration. Once we have those we will populate the
		// various place-holders, save the file, and run the post create event.
		$stub = $this->getStub($table, $create);

		$this->files->put($path, $this->populateStub($name, $stub, $table));

		$this->firePostCreateHooks();

		return $path;
	}

	/**
	 * Get the migration stub file.
	 *
	 * @param  string  $table
	 * @return string
	 */
	protected function getStub($table, $create)
	{
		if (is_null($table))
		{
			return $this->files->get($this->getStubPath().'/blank.stub');
		}

		// We also have stubs for creating new tables and modifying existing tables
		// to save the developer some typing when they are creating a new tables
		// or modifying existing tables. We'll grab the appropriate stub here.
		else
		{
			$stub = $create ? 'create.stub' : 'update.stub';

			return $this->files->get($this->getStubPath()."/{$stub}");
		}
	}

	/**
	 * Populate the place-holders in the migration stub.
	 *
	 * @param  string  $name
	 * @param  string  $stub
	 * @param  string  $table
	 * @return string
	 */
	protected function populateStub($name, $stub, $table)
	{
		$stub = str_replace('{{class}}', studly_case($name), $stub);

		// Here we will replace the table place-holders with the table specified by
		// the developer, which is useful for quickly creating a tables creation
		// or update migration from the console instead of typing it manually.
		if ( ! is_null($table))
		{
			$stub = str_replace('{{table}}', $table, $stub);
		}

		return $stub;
	}

	/**
	 * Fire the registered post create hooks.
	 *
	 * @return void
	 */
	protected function firePostCreateHooks()
	{
		foreach ($this->postCreate as $callback)
		{
			call_user_func($callback);
		}
	}

	/**
	 * Register a post migration create hook.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function afterCreate(Closure $callback)
	{
		$this->postCreate[] = $callback;
	}

	/**
	 * Get the full path name to the migration.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @return string
	 */
	protected function getPath($name, $path)
	{
		return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
	}

	/**
	 * Get the date prefix for the migration.
	 *
	 * @return string
	 */
	protected function getDatePrefix()
	{
		return date('Y_m_d_His');
	}

	/**
	 * Get the path to the stubs.
	 *
	 * @return string
	 */
	public function getStubPath()
	{
		return __DIR__.'/stubs';
	}

	/**
	 * Get the filesystem instance.
	 *
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}

}
