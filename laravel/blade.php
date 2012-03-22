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
	 * Register the Blade view engine with Laravel.
	 *
	 * @return void
	 */
	public static function sharpen()
	{
		Event::listen(View::engine, function($view)
		{
			// The Blade view engine should only handle the rendering of views which
			// end with the Blade extension. If the given view does not, we will
			// return false so the View can be rendered as normal.
			if ( ! str_contains($view->path, BLADE_EXT))
			{
				return false;
			}

			$compiled = path('storage').'views/'.md5($view->path);

			// If the view doesn't exist or has been modified since the last time it
			// was compiled, we will recompile the view into pure PHP from it's
			// Blade representation, writing it to cached storage.
			if ( ! file_exists($compiled) or (filemtime($view->path) > filemtime($compiled)))
			{
				file_put_contents($compiled, Blade::compile($view->path));
			}

			$view->path = $compiled;

			// Once the view has been compiled, we can simply set the path to the
			// compiled view on the view instance and call the typical "get"
			// method on the view to evaluate the compiled PHP view.
			return $view->get();
		});
	}

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