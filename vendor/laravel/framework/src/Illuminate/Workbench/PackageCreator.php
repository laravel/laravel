<?php namespace Illuminate\Workbench;

use Illuminate\Filesystem\Filesystem;

class PackageCreator {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The basic building blocks of the package.
	 *
	 * @param  array
	 */
	protected $basicBlocks = array(
		'SupportFiles',
		'TestDirectory',
		'ServiceProvider',
	);

	/**
	 * The building blocks of the package.
	 *
	 * @param  array
	 */
	protected $blocks = array(
		'SupportFiles',
		'SupportDirectories',
		'PublicDirectory',
		'TestDirectory',
		'ServiceProvider',
	);

	/**
	 * Create a new package creator instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Create a new package stub.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $path
	 * @param  bool    $plain
	 * @return string
	 */
	public function create(Package $package, $path, $plain = true)
	{
		$directory = $this->createDirectory($package, $path);

		// To create the package, we will spin through a list of building blocks that
		// make up each package. We'll then call the method to build that block on
		// the class, which keeps the actual building of stuff nice and cleaned.
		foreach ($this->getBlocks($plain) as $block)
		{
			$this->{"write{$block}"}($package, $directory, $plain);
		}

		return $directory;
	}

	/**
	 * Create a package with all resource directories.
	 *
	 * @param  Package  $package
	 * @param  string   $path
	 * @return void
	 */
	public function createWithResources(Package $package, $path)
	{
		return $this->create($package, $path, false);
	}

	/**
	 * Get the blocks for a given package.
	 *
	 * @param  bool   $plain
	 * @return array
	 */
	protected function getBlocks($plain)
	{
		return $plain ? $this->basicBlocks : $this->blocks;
	}

	/**
	 * Write the support files to the package root.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writeSupportFiles(Package $package, $directory, $plain)
	{
		foreach (array('PhpUnit', 'Travis', 'Composer', 'Ignore') as $file)
		{
			$this->{"write{$file}File"}($package, $directory, $plain);
		}
	}

	/**
	 * Write the PHPUnit stub file.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	protected function writePhpUnitFile(Package $package, $directory)
	{
		$stub = __DIR__.'/stubs/phpunit.xml';

		$this->files->copy($stub, $directory.'/phpunit.xml');
	}

	/**
	 * Write the Travis stub file.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	protected function writeTravisFile(Package $package, $directory)
	{
		$stub = __DIR__.'/stubs/.travis.yml';

		$this->files->copy($stub, $directory.'/.travis.yml');
	}

	/**
	 * Write the Composer.json stub file.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	protected function writeComposerFile(Package $package, $directory, $plain)
	{
		$stub = $this->getComposerStub($plain);

		$stub = $this->formatPackageStub($package, $stub);

		$this->files->put($directory.'/composer.json', $stub);
	}

	/**
	 * Get the Composer.json stub file contents.
	 *
	 * @param  bool    $plain
	 * @return string
	 */
	protected function getComposerStub($plain)
	{
		if ($plain) return $this->files->get(__DIR__.'/stubs/plain.composer.json');

		return $this->files->get(__DIR__.'/stubs/composer.json');
	}

	/**
	 * Write the stub .gitignore file for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writeIgnoreFile(Package $package, $directory, $plain)
	{
		$this->files->copy(__DIR__.'/stubs/gitignore.txt', $directory.'/.gitignore');
	}

	/**
	 * Create the support directories for a package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writeSupportDirectories(Package $package, $directory)
	{
		foreach (array('config', 'controllers', 'lang', 'migrations', 'views') as $support)
		{
			$this->writeSupportDirectory($package, $support, $directory);
		}
	}

	/**
	 * Write a specific support directory for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $support
	 * @param  string  $directory
	 * @return void
	 */
	protected function writeSupportDirectory(Package $package, $support, $directory)
	{
		// Once we create the source directory, we will write an empty file to the
		// directory so that it will be kept in source control allowing the dev
		// to go ahead and push these components to GitHub right on creation.
		$path = $directory.'/src/'.$support;

		$this->files->makeDirectory($path, 0777, true);

		$this->files->put($path.'/.gitkeep', '');
	}

	/**
	 * Create the public directory for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writePublicDirectory(Package $package, $directory, $plain)
	{
		if ($plain) return;

		$this->files->makeDirectory($directory.'/public');

		$this->files->put($directory.'/public/.gitkeep', '');
	}

	/**
	 * Create the test directory for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writeTestDirectory(Package $package, $directory)
	{
		$this->files->makeDirectory($directory.'/tests');

		$this->files->put($directory.'/tests/.gitkeep', '');
	}

	/**
	 * Write the stub ServiceProvider for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return void
	 */
	public function writeServiceProvider(Package $package, $directory, $plain)
	{
		// Once we have the service provider stub, we will need to format it and make
		// the necessary replacements to the class, namespaces, etc. Then we'll be
		// able to write it out into the package's workbench directory for them.
		$stub = $this->getProviderStub($package, $plain);

		$this->writeProviderStub($package, $directory, $stub);
	}

	/**
	 * Write the service provider stub for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @param  string  $stub
	 * @return void
	 */
	protected function writeProviderStub(Package $package, $directory, $stub)
	{
		$path = $this->createClassDirectory($package, $directory);

		// The primary source directory where the package's classes will live may not
		// exist yet, so we will need to create it before we write these providers
		// out to that location. We'll go ahead and create now here before then.
		$file = $path.'/'.$package->name.'ServiceProvider.php';

		$this->files->put($file, $stub);
	}

	/**
	 * Get the stub for a ServiceProvider.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  bool    $plain
	 * @return string
	 */
	protected function getProviderStub(Package $package, $plain)
	{
		return $this->formatPackageStub($package, $this->getProviderFile($plain));
	}

	/**
	 * Load the raw service provider file.
	 *
	 * @param  bool   $plain
	 * @return string
	 */
	protected function getProviderFile($plain)
	{
		if ($plain)
		{
			return $this->files->get(__DIR__.'/stubs/plain.provider.stub');
		}
		else
		{
			return $this->files->get(__DIR__.'/stubs/provider.stub');
		}
	}

	/**
	 * Create the main source directory for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $directory
	 * @return string
	 */
	protected function createClassDirectory(Package $package, $directory)
	{
		$path = $directory.'/src/'.$package->vendor.'/'.$package->name;

		if ( ! $this->files->isDirectory($path))
		{
			$this->files->makeDirectory($path, 0777, true);
		}

		return $path;
	}

	/**
	 * Format a generic package stub file.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $stub
	 * @return string
	 */
	protected function formatPackageStub(Package $package, $stub)
	{
		foreach (get_object_vars($package) as $key => $value)
		{
			$stub = str_replace('{{'.snake_case($key).'}}', $value, $stub);
		}

		return $stub;
	}

	/**
	 * Create a workbench directory for the package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @param  string  $path
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function createDirectory(Package $package, $path)
	{
		$fullPath = $path.'/'.$package->getFullName();

		// If the directory doesn't exist, we will go ahead and create the package
		// directory in the workbench location. We will use this entire package
		// name when creating the directory to avoid any potential conflicts.
		if ( ! $this->files->isDirectory($fullPath))
		{
			$this->files->makeDirectory($fullPath, 0777, true);

			return $fullPath;
		}

		throw new \InvalidArgumentException("Package exists.");
	}

}
