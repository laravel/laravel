<?php namespace Laravel;

class Blade {

	/**
	 * All of the compiler functions used by Blade.
	 *
	 * @var array
	 */
	protected static $compilers = array(
		'echos',
		'structure_openings',
		'structure_closings',
		'else',
		'yields',
		'section_start',
		'section_end',
	);

	/**
	 * Compiles the specified file containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function compile($path)
	{
		return static::compile_string(file_get_contents($path));
	}

	/**
	 * Compiles the given string containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function compile_string($value)
	{
		foreach (static::$compilers as $compiler)
		{
			$method = "compile_{$compiler}";

			$value = static::$method($value);
		}

		return $value;
	}

	/**
	 * Rewrites Blade echo statements into PHP echo statements.
	 *
	 * Blade echo statements are simply PHP statement enclosed within double curly
	 * braces. For example, {{$content}} will simply echo out the content variable
	 * to the output buffer.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_echos($value)
	{
		return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
	}

	/**
	 * Rewrites Blade structure openings into PHP structure openings.
	 *
	 * By "structures", we mean the if, elseif, foreach, for, and while statements.
	 * All of these structures essentially have the same format, and can be lumped
	 * into a single regular expression.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_structure_openings($value)
	{
		$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
	}

	/**
	 * Rewrites Blade structure closings into PHP structure closings.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_structure_closings($value)
	{
		$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';

		return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
	}

	/**
	 * Rewrites Blade else statements into PHP else statements.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_else($value)
	{
		return preg_replace('/(\s*)@(else)(\s*)/', '$1<?php $2: ?>$3', $value);
	}

	/**
	 * Rewrites Blade @yield statements into Section statements.
	 *
	 * The Blade @yield statement is a shortcut to the Section::yield method.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_yields($value)
	{
		$pattern = static::matcher('yield');

		return preg_replace($pattern, '$1<?php echo \\Laravel\\Section::yield$2; ?>', $value);
	}

	/**
	 * Rewrites Blade @section statements into Section statements.
	 *
	 * The Blade @section statement is a shortcut to the Section::start method.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_section_start($value)
	{
		$pattern = static::matcher('section');

		return preg_replace($pattern, '$1<?php \\Laravel\\Section::start$2; ?>', $value);
	}

	/**
	 * Rewrites Blade @endsection statements into Section statements.
	 *
	 * The Blade @endsection statement is a shortcut to the Section::stop method.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_section_end($value)
	{
		return preg_replace('/@endsection/', '<?php \\Laravel\\Section::stop(); ?>', $value);
	}

	/**
	 * Get the regular expression for a generic Blade function.
	 *
	 * @param  string  $function
	 * @return string
	 */
	protected static function matcher($function)
	{
		return '/(\s*)@'.$function.'(\s*\(.*\))/';
	}

}