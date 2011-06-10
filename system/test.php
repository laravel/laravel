<?php namespace System;

class Test {

	/**
	 * All of the test results.
	 *
	 * @var array
	 */
	public static $results = array();

	/**
	 * Total number of tests being run.
	 *
	 * @var int
	 */
	public static $total = 0;

	/**
	 * Total number of passed tests.
	 *
	 * @var int
	 */
	public static $passed = 0;

	/**
	 * Run a test suite.
	 *
	 * @param  string  $suite
	 * @param  array   $tests
	 * @return void
	 */
	public static function run($suite, $tests)
	{
		static::$total = static::$total + count($tests);

		// -----------------------------------------------------
		// Run each test in the suite.
		// -----------------------------------------------------
		foreach ($tests as $name => $test)
		{
			if ( ! is_callable($test))
			{
				throw new \Exception("Test [$name] in suite [$suite] is not callable.");
			}

			static::$passed = ($result = call_user_func($test)) ? static::$passed + 1 : static::$passed;
			static::$results[$suite][] = array('name' => $name, 'result' => $result);				
		}
	}

	/**
	 * Get the test report view.
	 *
	 * @return View
	 */
	public static function report()
	{
		return View::make('test/report')
								->bind('results', static::$results)
								->bind('passed', static::$passed)
								->bind('total', static::$total);
	}

}