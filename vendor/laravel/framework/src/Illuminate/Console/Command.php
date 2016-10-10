<?php namespace Illuminate\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command {

	/**
	 * The Laravel application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $laravel;

	/**
	 * The input interface implementation.
	 *
	 * @var \Symfony\Component\Console\Input\InputInterface
	 */
	protected $input;

	/**
	 * The output interface implementation.
	 *
	 * @var \Symfony\Component\Console\Output\OutputInterface
	 */
	protected $output;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Create a new console command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct($this->name);

		// We will go ahead and set the name, description, and parameters on console
		// commands just to make things a little easier on the developer. This is
		// so they don't have to all be manually specified in the constructors.
		$this->setDescription($this->description);

		$this->specifyParameters();
	}

	/**
	 * Specify the arguments and options on the command.
	 *
	 * @return void
	 */
	protected function specifyParameters()
	{
		// We will loop through all of the arguments and options for the command and
		// set them all on the base command instance. This specifies what can get
		// passed into these commands as "parameters" to control the execution.
		foreach ($this->getArguments() as $arguments)
		{
			call_user_func_array(array($this, 'addArgument'), $arguments);
		}

		foreach ($this->getOptions() as $options)
		{
			call_user_func_array(array($this, 'addOption'), $options);
		}
	}

	/**
	 * Run the console command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return integer
	 */
	public function run(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;

		$this->output = $output;

		return parent::run($input, $output);
	}

	/**
	 * Execute the console command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return mixed
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		return $this->fire();
	}

	/**
	 * Call another console command.
	 *
	 * @param  string  $command
	 * @param  array   $arguments
	 * @return integer
	 */
	public function call($command, array $arguments = array())
	{
		$instance = $this->getApplication()->find($command);

		$arguments['command'] = $command;

		return $instance->run(new ArrayInput($arguments), $this->output);
	}

	/**
	 * Call another console command silently.
	 *
	 * @param  string  $command
	 * @param  array   $arguments
	 * @return integer
	 */
	public function callSilent($command, array $arguments = array())
	{
		$instance = $this->getApplication()->find($command);

		$arguments['command'] = $command;

		return $instance->run(new ArrayInput($arguments), new NullOutput);
	}

	/**
	 * Get the value of a command argument.
	 *
	 * @param  string  $key
	 * @return string|array
	 */
	public function argument($key = null)
	{
		if (is_null($key)) return $this->input->getArguments();

		return $this->input->getArgument($key);
	}

	/**
	 * Get the value of a command option.
	 *
	 * @param  string  $key
	 * @return string|array
	 */
	public function option($key = null)
	{
		if (is_null($key)) return $this->input->getOptions();

		return $this->input->getOption($key);
	}

	/**
	 * Confirm a question with the user.
	 *
	 * @param  string  $question
	 * @param  bool    $default
	 * @return bool
	 */
	public function confirm($question, $default = true)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		return $dialog->askConfirmation($this->output, "<question>$question</question>", $default);
	}

	/**
	 * Prompt the user for input.
	 *
	 * @param  string  $question
	 * @param  string  $default
	 * @return string
	 */
	public function ask($question, $default = null)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		return $dialog->ask($this->output, "<question>$question</question>", $default);
	}


	/**
	 * Prompt the user for input but hide the answer from the console.
	 *
	 * @param  string  $question
	 * @param  bool    $fallback
	 * @return string
	 */
	public function secret($question, $fallback = true)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		return $dialog->askHiddenResponse($this->output, "<question>$question</question>", $fallback);
	}

	/**
	 * Give the user a single choice from an array of answers.
	 *
	 * @param  string  $question
	 * @param  array   $choices
	 * @param  string  $default
	 * @param  mixed   $attempts
	 * @return bool
	 */
	public function choice($question, array $choices, $default = null, $attempts = false)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		return $dialog->select($this->output, "<question>$question</question>", $choices, $default, $attempts);
	}

	/**
	 * Write a string as standard output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function line($string)
	{
		$this->output->writeln($string);
	}

	/**
	 * Write a string as information output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function info($string)
	{
		$this->output->writeln("<info>$string</info>");
	}

	/**
	 * Write a string as comment output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function comment($string)
	{
		$this->output->writeln("<comment>$string</comment>");
	}

	/**
	 * Write a string as question output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function question($string)
	{
		$this->output->writeln("<question>$string</question>");
	}

	/**
	 * Write a string as error output.
	 *
	 * @param  string  $string
	 * @return void
	 */
	public function error($string)
	{
		$this->output->writeln("<error>$string</error>");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Get the output implementation.
	 *
	 * @return \Symfony\Component\Console\Output\OutputInterface
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * Set the Laravel application instance.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function getLaravel()
	{
		return $this->laravel;
	}

	/**
	 * Set the Laravel application instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $laravel
	 * @return void
	 */
	public function setLaravel($laravel)
	{
		$this->laravel = $laravel;
	}

}
