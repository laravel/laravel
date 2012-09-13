<?php namespace Laravel; use FilesystemIterator as fIterator; use Closure;

class Blade {

	/**
	 * All of the compiler functions used by Blade.
	 *
	 * @var array
	 */
	protected static $compilers = array(
		'extensions',
		'layouts',
		'comments',
		'echos',
		'empty',
		'endforelse',
		'structure_openings',
		'structure_closings',
		'else',
		'endunless',
		'includes',
		'yields',
		'yield_sections',
		'section_start',
		'section_end',
	);

	/**
	 * An array of user defined compilers.
	 *
	 * @var array
	 */
	protected static $extensions = array();

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
				return;
			}

			$compiled = Blade::compiled($view->path);

			// If the view doesn't exist or has been modified since the last time it
			// was compiled, we will recompile the view into pure PHP from it's
			// Blade representation, writing it to cached storage.
			if ( ! file_exists($compiled) or Blade::expired($view->view, $view->path))
			{
				file_put_contents($compiled, Blade::compile($view));
			}

			$view->path = $compiled;

			// Once the view has been compiled, we can simply set the path to the
			// compiled view on the view instance and call the typical "get"
			// method on the view to evaluate the compiled PHP view.
			return $view->get();
		});
	}

	/**
	 * Register a custom Blade compiler.
	 *
	 * <code>
	 * 		Blade::extend(function($view)
	 *		{
	 * 			return str_replace('foo', 'bar', $view);
	 * 		});
	 * </code>
	 *
	 * @param  Closure  $compiler
	 * @return void
	 */
	public static function extend(Closure $compiler)
	{
		static::$extensions[] = $compiler;
	}

	/**
	 * Determine if a view is "expired" and needs to be re-compiled.
	 *
	 * @param  string  $view
	 * @param  string  $path
	 * @return bool
	 */
	public static function expired($view, $path)
	{
		return filemtime($path) > filemtime(static::compiled($path));
	}

	/**
	 * Compiles the specified file containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $view
	 * @return string
	 */
	public static function compile($view)
	{
		return static::compile_string(file_get_contents($view->path), $view);
	}

	/**
	 * Compiles the given string containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $value
	 * @param  View    $view
	 * @return string
	 */
	public static function compile_string($value, $view = null)
	{
		foreach (static::$compilers as $compiler)
		{
			$method = "compile_{$compiler}";

			$value = static::$method($value, $view);
		}

		return $value;
	}

	/**
	 * Rewrites Blade "@layout" expressions into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_layouts($value)
	{
		// If the Blade template is not using "layouts", we'll just return it
		// unchanged since there is nothing to do with layouts and we will
		// just let the other Blade compilers handle the rest.
		if ( ! starts_with($value, '@layout'))
		{
			return $value;
		}

		// First we'll split out the lines of the template so we can get the
		// layout from the top of the template. By convention, it must be
		// located on the first line of the template contents.
		preg_replace_callback(
			'/^@layout\s*?(\s*?\(.*?\))(\r?\n)?/',
			function($matches) use (&$value)
			{
				$value = substr( $value, strlen( $matches[0] ) ).'@include'.$matches[1];
			},
			$value
		);

		return $value;
	}

	/**
	 * Extract a variable value out of a Blade expression.
	 *
	 * @param  string  $value
	 * @param  string  $expression
	 * @return string
	 */
	protected static function extract($value, $expression)
	{
		if ( preg_match("/@layout\s*?\(\s*?'(.+?)'\s*?\)/", $value, $matches))
		{
			return trim( $matches[1] );
		}
	}

	/**
	 * Rewrites Blade comments into PHP comments.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_comments($value)
	{
		$value = preg_replace('/\{\{--(.*?)--\}\}/', "<?php // $1 ?>", $value);
		
		return preg_replace('/\{\{--(.*?)--\}\}/s', "<?php /* ?>$1<?php */ ?>", $value);
	}

	/**
	 * Rewrites Blade echo statements into PHP echo statements.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_echos($value)
	{
		return preg_replace('/\{\{(.+?)\}\}/s', '<?php echo $1; ?>', $value);
	}

	/**
	 * Rewrites Blade "empty" statements into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_empty($value)
	{
		return str_replace('@empty', '<?php endforeach; else: ?>', $value);
	}

	/**
	 * Rewrites Blade "forelse" endings into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_endforelse($value)
	{
		return str_replace('@endforelse', '<?php endif; ?>', $value);
	}

	/**
	 * Rewrites Blade structure openings into PHP structure openings.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_structure_openings($value)
	{
		preg_replace_callback(
			'/@(if|elseif|foreach|for|while|forelse|render|render_each|unless)(\s*?)(\([^\n\r\t]+\))/',
			function($matches) use (&$value)
			{
				if(count( $matches ) === 4)
				{
					$open  = 0;
					$close = 0;
					$cut   = 0;
					$len   = strlen($matches[3]);
					for($i = 0; $i < $len; $i++)
					{
						if($matches[3][$i] === '(' )
						{
							$open++;
						}
						if($matches[3][$i] === ')' )
						{
							$close++;
						}
						if($open !== 0 && ($open === $close))
						{
							break;
						}
					}
					$condition = substr($matches[3], 0, ($i + 1));
					if( $matches[1] == 'render' || $matches[1] == 'render_each' )
					{
						$value = str_replace(
							'@'.$matches[1].$matches[2].$condition,
							'<?php echo '.$matches[1].$condition.'; ?>',
							$value
						);
					}
					elseif( $matches[1] == 'unless' )
					{
						$value = str_replace(
							'@'.$matches[1].$matches[2].$condition,
							'<?php if( ! '.$condition.' ): ?>',
							$value
						);
					}
					elseif( $matches[1] == 'forelse' )
					{
						if( preg_match('/\(\s*?\$(.+?)\s+as\s+/i', $condition, $match ) )
						{
							$value = str_replace(
								'@'.$matches[1].$matches[2].$condition,
								'<?php if (count($'.$match[1].') > 0): foreach'.$condition.': ?>',
								$value
							);
						}
					}
					else
					{
						$value = str_replace(
							'@'.$matches[1].$matches[2].$condition,
							'<?php '.$matches[1].$condition.': ?>',
							$value
						);
					}
				}
			},
			$value
		);
		return $value;
	}

	/**
	 * Rewrites Blade structure closings into PHP structure closings.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_structure_closings($value)
	{
		$pattern = '/@(endif|endforeach|endfor|endwhile|break|continue)/';

		return preg_replace($pattern, '<?php $1; ?>', $value);
	}

	/**
	 * Rewrites Blade else statements into PHP else statements.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_else($value)
	{
		return str_replace( '@else', '<?php else: ?>', $value);
	}

	/**
	 * Rewrites Blade "unless" endings into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_endunless($value)
	{
		return str_replace('@endunless', '<?php endif; ?>', $value);
	}

	/**
	 * Rewrites Blade @include statements into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_includes($value)
	{
		$pattern = static::matcher('include');

		return preg_replace($pattern, '<?php echo view$1->with(get_defined_vars())->render(); ?>', $value);
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

		return preg_replace($pattern, '<?php echo \\Laravel\\Section::yield$1; ?>', $value);
	}

	/**
	 * Rewrites Blade yield section statements into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_yield_sections($value)
	{
		return str_replace('@yield_section', '<?php echo \\Laravel\\Section::yield_section(); ?>', $value);
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

		return preg_replace($pattern, '<?php \\Laravel\\Section::start$1; ?>', $value);
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
		return str_replace('@endsection', '<?php \\Laravel\\Section::stop(); ?>', $value);
	}

	/**
	 * Execute user defined compilers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_extensions($value)
	{
		foreach (static::$extensions as $compiler)
		{
			$value = $compiler($value);
		}

		return $value;
	}

	/**
	 * Get the regular expression for a generic Blade function.
	 *
	 * @param  string  $function
	 * @return string
	 */
	public static function matcher($function)
	{
		return '/@'.$function.'\s*?(\(.+?\))/';
	}

	/**
	 * Get the fully qualified path for a compiled view.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function compiled($path)
	{
		return path('storage').'views/'.md5($path);
	}

}