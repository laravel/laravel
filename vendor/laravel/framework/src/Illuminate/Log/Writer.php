<?php namespace Illuminate\Log;

use Closure;
use Illuminate\Events\Dispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class Writer {

	/**
	 * The Monolog logger instance.
	 *
	 * @var \Monolog\Logger
	 */
	protected $monolog;

	/**
	 * All of the error levels.
	 *
	 * @var array
	 */
	protected $levels = array(
		'debug',
		'info',
		'notice',
		'warning',
		'error',
		'critical',
		'alert',
		'emergency',
	);

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Create a new log writer instance.
	 *
	 * @param  \Monolog\Logger  $monolog
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(MonologLogger $monolog, Dispatcher $dispatcher = null)
	{
		$this->monolog = $monolog;

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}
	}

	/**
	 * Call Monolog with the given method and parameters.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	protected function callMonolog($method, $parameters)
	{
		if (is_array($parameters[0]))
		{
			$parameters[0] = json_encode($parameters[0]);
		}

		return call_user_func_array(array($this->monolog, $method), $parameters);
	}

	/**
	 * Register a file log handler.
	 *
	 * @param  string  $path
	 * @param  string  $level
	 * @return void
	 */
	public function useFiles($path, $level = 'debug')
	{
		$level = $this->parseLevel($level);

		$this->monolog->pushHandler($handler = new StreamHandler($path, $level));

		$handler->setFormatter(new LineFormatter(null, null, true));
	}

	/**
	 * Register a daily file log handler.
	 *
	 * @param  string  $path
	 * @param  int     $days
	 * @param  string  $level
	 * @return void
	 */
	public function useDailyFiles($path, $days = 0, $level = 'debug')
	{
		$level = $this->parseLevel($level);

		$this->monolog->pushHandler($handler = new RotatingFileHandler($path, $days, $level));

		$handler->setFormatter(new LineFormatter(null, null, true));
	}

	/**
	 * Parse the string level into a Monolog constant.
	 *
	 * @param  string  $level
	 * @return int
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function parseLevel($level)
	{
		switch ($level)
		{
			case 'debug':
				return MonologLogger::DEBUG;

			case 'info':
				return MonologLogger::INFO;

			case 'notice':
				return MonologLogger::NOTICE;

			case 'warning':
				return MonologLogger::WARNING;

			case 'error':
				return MonologLogger::ERROR;

			case 'critical':
				return MonologLogger::CRITICAL;

			case 'alert':
				return MonologLogger::ALERT;

			case 'emergency':
				return MonologLogger::EMERGENCY;

			default:
				throw new \InvalidArgumentException("Invalid log level.");
		}
	}

	/**
	 * Register a new callback handler for when
	 * a log event is triggered.
	 *
	 * @param  Closure  $callback
	 * @return void
	 *
	 * @throws \RuntimeException
	 */
	public function listen(Closure $callback)
	{
		if ( ! isset($this->dispatcher))
		{
			throw new \RuntimeException("Events dispatcher has not been set.");
		}

		$this->dispatcher->listen('illuminate.log', $callback);
	}

	/**
	 * Get the underlying Monolog instance.
	 *
	 * @return \Monolog\Logger
	 */
	public function getMonolog()
	{
		return $this->monolog;
	}

	/**
	 * Get the event dispatcher instance.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher
	 * @return void
	 */
	public function setEventDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Fires a log event.
	 *
	 * @param  string  $level
	 * @param  array   $parameters
	 * @return void
	 */
	protected function fireLogEvent($level, $message, array $context = array())
	{
		// If the event dispatcher is set, we will pass along the parameters to the
		// log listeners. These are useful for building profilers or other tools
		// that aggregate all of the log messages for a given "request" cycle.
		if (isset($this->dispatcher))
		{
			$this->dispatcher->fire('illuminate.log', compact('level', 'message', 'context'));
		}
	}

	/**
	 * Dynamically pass log calls into the writer.
	 *
	 * @param  dynamic (level, param, param)
	 * @return mixed
	 */
	public function write()
	{
		$level = head(func_get_args());

		return call_user_func_array(array($this, $level), array_slice(func_get_args(), 1));
	}

	/**
	 * Dynamically handle error additions.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, $this->levels))
		{
			call_user_func_array(array($this, 'fireLogEvent'), array_merge(array($method), $parameters));

			$method = 'add'.ucfirst($method);

			return $this->callMonolog($method, $parameters);
		}

		throw new \BadMethodCallException("Method [$method] does not exist.");
	}

}
