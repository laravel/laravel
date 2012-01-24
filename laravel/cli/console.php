<?php namespace Laravel\CLI;

class Console {

	/**
	 * Parse the command line arguments and return the results.
	 *
	 * The returned array contains the arguments and the options.
	 *
	 * @param  array  $argv
	 * @param  array
	 */
	public static function options($argv)
	{
		$options = array();

		$arguments = array();

		for ($i = 0, $count = count($argv); $i < $count; $i++)
		{
			$argument = $argv[$i];

			// If the CLI argument starts with a double hyphen, it is an option,
			// so we will extract the value and add it to the array of options
			// to be returned by the method.
			if (starts_with($argument, '--'))
			{
				// By default, we will assume the value of the options is true,
				// but if the option contains an equals sign, we will take the
				// value to the right of the equals sign as the value and
				// remove the value from the option key.
				list($key, $value) = array(substr($argument, 2), true);

				if (($equals = strpos($argument, '=')) !== false)
				{
					$key = substr($argument, 2, $equals - 2);

					$value = substr($argument, $equals + 1);
				}

				$options[$key] = $value;
			}
			// If the CLI argument does not start with a double hyphen it is
			// simply an argument to be passed to the console task so we'll
			// add it to the array of "regular" arguments.
			else
			{
				$arguments[] = $argument;
			}
		}

		return array($arguments, $options);
	}

}