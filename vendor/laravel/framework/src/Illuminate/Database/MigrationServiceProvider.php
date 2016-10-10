<?php namespace Illuminate\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class MigrationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRepository();

		// Once we have registered the migrator instance we will go ahead and register
		// all of the migration related commands that are used by the "Artisan" CLI
		// so that they may be easily accessed for registering with the consoles.
		$this->registerMigrator();

		$this->registerCommands();
	}

	/**
	 * Register the migration repository service.
	 *
	 * @return void
	 */
	protected function registerRepository()
	{
		$this->app->bindShared('migration.repository', function($app)
		{
			$table = $app['config']['database.migrations'];

			return new DatabaseMigrationRepository($app['db'], $table);
		});
	}

	/**
	 * Register the migrator service.
	 *
	 * @return void
	 */
	protected function registerMigrator()
	{
		// The migrator is responsible for actually running and rollback the migration
		// files in the application. We'll pass in our database connection resolver
		// so the migrator can resolve any of these connections when it needs to.
		$this->app->bindShared('migrator', function($app)
		{
			$repository = $app['migration.repository'];

			return new Migrator($repository, $app['db'], $app['files']);
		});
	}

	/**
	 * Register all of the migration commands.
	 *
	 * @return void
	 */
	protected function registerCommands()
	{
		$commands = array('Migrate', 'Rollback', 'Reset', 'Refresh', 'Install', 'Make');

		// We'll simply spin through the list of commands that are migration related
		// and register each one of them with an application container. They will
		// be resolved in the Artisan start file and registered on the console.
		foreach ($commands as $command)
		{
			$this->{'register'.$command.'Command'}();
		}

		// Once the commands are registered in the application IoC container we will
		// register them with the Artisan start event so that these are available
		// when the Artisan application actually starts up and is getting used.
		$this->commands(
			'command.migrate', 'command.migrate.make',
			'command.migrate.install', 'command.migrate.rollback',
			'command.migrate.reset', 'command.migrate.refresh'
		);
	}

	/**
	 * Register the "migrate" migration command.
	 *
	 * @return void
	 */
	protected function registerMigrateCommand()
	{
		$this->app->bindShared('command.migrate', function($app)
		{
			$packagePath = $app['path.base'].'/vendor';

			return new MigrateCommand($app['migrator'], $packagePath);
		});
	}

	/**
	 * Register the "rollback" migration command.
	 *
	 * @return void
	 */
	protected function registerRollbackCommand()
	{
		$this->app->bindShared('command.migrate.rollback', function($app)
		{
			return new RollbackCommand($app['migrator']);
		});
	}

	/**
	 * Register the "reset" migration command.
	 *
	 * @return void
	 */
	protected function registerResetCommand()
	{
		$this->app->bindShared('command.migrate.reset', function($app)
		{
			return new ResetCommand($app['migrator']);
		});
	}

	/**
	 * Register the "refresh" migration command.
	 *
	 * @return void
	 */
	protected function registerRefreshCommand()
	{
		$this->app->bindShared('command.migrate.refresh', function($app)
		{
			return new RefreshCommand;
		});
	}

	/**
	 * Register the "install" migration command.
	 *
	 * @return void
	 */
	protected function registerInstallCommand()
	{
		$this->app->bindShared('command.migrate.install', function($app)
		{
			return new InstallCommand($app['migration.repository']);
		});
	}

	/**
	 * Register the "install" migration command.
	 *
	 * @return void
	 */
	protected function registerMakeCommand()
	{
		$this->app->bindShared('migration.creator', function($app)
		{
			return new MigrationCreator($app['files']);
		});

		$this->app->bindShared('command.migrate.make', function($app)
		{
			// Once we have the migration creator registered, we will create the command
			// and inject the creator. The creator is responsible for the actual file
			// creation of the migrations, and may be extended by these developers.
			$creator = $app['migration.creator'];

			$packagePath = $app['path.base'].'/vendor';

			return new MigrateMakeCommand($creator, $packagePath);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'migrator', 'migration.repository', 'command.migrate',
			'command.migrate.rollback', 'command.migrate.reset',
			'command.migrate.refresh', 'command.migrate.install',
			'migration.creator', 'command.migrate.make',
		);
	}

}
