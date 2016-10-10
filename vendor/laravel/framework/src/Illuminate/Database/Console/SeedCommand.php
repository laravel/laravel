<?php namespace Illuminate\Database\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class SeedCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed the database with records';

	/**
	 * The connection resolver instance.
	 *
	 * @var  \Illuminate\Database\ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * Create a new database seed command instance.
	 *
	 * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
	 * @return void
	 */
	public function __construct(Resolver $resolver)
	{
		parent::__construct();

		$this->resolver = $resolver;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->resolver->setDefaultConnection($this->getDatabase());

		$this->getSeeder()->run();
	}

	/**
	 * Get a seeder instance from the container.
	 *
	 * @return \Illuminate\Database\Seeder
	 */
	protected function getSeeder()
	{
		$class = $this->laravel->make($this->input->getOption('class'));

		return $class->setContainer($this->laravel)->setCommand($this);
	}

	/**
	 * Get the name of the database connection to use.
	 *
	 * @return string
	 */
	protected function getDatabase()
	{
		$database = $this->input->getOption('database');

		return $database ?: $this->laravel['config']['database.default'];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', 'DatabaseSeeder'),

			array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed'),
		);
	}

}
