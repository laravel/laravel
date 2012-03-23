<?php namespace Laravel; use FilesystemIterator as fIterator;

class Blade {

	/**
	 * The cache key for the extension tree.
	 *
	 * @var string
	 */
	const cache = 'laravel.blade.extensions';

	/**
	 * An array containing the template extension tree.
	 *
	 * @var array
	 */
	public static $extensions;

	/**
	 * The original extension tree loaded from the cache.
	 *
	 * @var array
	 */
	public static $original;

	/**
	 * All of the compiler functions used by Blade.
	 *
	 * @var array
	 */
	protected static $compilers = array(
		'extends',
		'includes',
		'echos',
		'forelse',
		'empty',
		'endforelse',
		'structure_openings',
		'structure_closings',
		'else',
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
		static::extensions();

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
	 * Load the extension tree so we can correctly invalidate caches.
	 *
	 * @return void
	 */
	protected static function extensions()
	{
		// The entire view extension tree is cached so we can check for expired
		// views anywhere in the tree. This allows us to recompile a child
		// view if any of its parent views change throughout the tree.
		static::$extensions = Cache::get(Blade::cache);

		static::$original = static::$extensions;

		// If no extension tree was present, we need to invalidate every cache
		// since we have no way of knowing which views needs to be compiled
		// since we don't know any of their parent views.
		if (is_null(static::$extensions))
		{
			static::flush();

			static::$extensions = array();
		}

		// We'll hook into the "done" event of Laravel and write out the tree
		// of extensions if it was changed during the course of the request.
		// The tree would change if new templates were rendered, etc.
		Event::listen('laravel.done', function()
		{
			if (Blade::$extensions !== Blade::$original)
			{
				Cache::forever(Blade::cache, Blade::$extensions);
			}
		});
	}

	/**
	 * Determine if a view is "expired" and needs to be re-compiled.
	 *
	 * @param  string  $view
	 * @param  string  $path
	 * @param  string  $compiled
	 * @return bool
	 */
	public static function expired($view, $path)
	{
		$compiled = static::compiled($path);

		return filemtime($path) > filemtime($compiled) or static::expired_parent($view);
	}

	/**
	 * Determine if the given view has an expired parent view.
	 *
	 * @param  string  $view
	 * @return bool
	 */
	protected static function expired_parent($view)
	{
		// If the view is extending another view, we need to recursively check
		// whether any of the extended views have expired, all the way up to
		// the top most parent view of the extension chain.
		if (isset(static::$extensions[$view]))
		{
			$e = static::$extensions[$view];

			return static::expired($e['view'], $e['path']);
		}

		return false;
	}

	/**
	 * Get the fully qualified path for a compiled view.
	 *
	 * @param  string  $view
	 * @return string
	 */
	public static function compiled($path)
	{
		return path('storage').'views/'.md5($path);
	}

	/**
	 * Compiles the specified file containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $path
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
	 * Rewrites Blade extended templates into valid PHP.
	 *
	 * @param  string  $value
	 * @param  View    $view
	 * @return string
	 */
	protected static function compile_extends($value, $view)
	{
		// If the view doesn't begin with @extends, we don't need to do anything
		// and can simply return the view to be parsed by the rest of Blade's
		// compilers like any other normal Blade view would be compiled.
		if (is_null($view) or ! starts_with($value, '@extends'))
		{
			return $value;
		}

		// First we need to parse the parent template from the extends keyword
		// so we know which parent to render. We will remove the extends
		// from the template after we have extracted the parent.
		$template = static::extract_template($value);

		$path = static::store_extended($value, $view);

		// Once we have stored a copy of the view without the "extends" clause
		// we can load up that stored view and render it. The extending view
		// should only be using "sections", so we don't need the output.
		View::make("path: {$path}", $view->data())->render();

		$parent =  View::make($template);

		// Finally we will make and return the parent view as the output of
		// the compilation. We'll touch the parent to force it to compile
		// when it is rendered so we can make sure we're all fresh.
		touch($parent->path);

		static::log_extension($view, $parent);

		return $parent->render();
	}

	/**
	 * Extract the parent template name from an extending view.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function extract_template($value)
	{
		preg_match('/@extends(\s*\(.*\))(\s*)/', $value, $matches);

		return str_replace(array("('", "')"), '', $matches[1]);
	}

	/**
	 * Store an extended view in the view storage.
	 *
	 * @param  string  $value
	 * @param  View    $view
	 * @return array
	 */
	protected static function store_extended($value, $view)
	{
		$value = preg_replace('/@extends(\s*\(.*\))(\s*)/', '', $value);

		file_put_contents($path = static::compiled($view->path.'_extended').BLADE_EXT, $value);

		return $path;
	}

	/**
	 * Log a view extension for a given view in the extension tree.
	 *
	 * @param  View  $view
	 * @param  View  $parent
	 * @return void
	 */
	protected static function log_extension($view, $parent)
	{
		static::$extensions[$view->view] = array('view' => $parent->view, 'path' => $parent->path);
	}

	/**
	 * Rewrites Blade "include" statements to valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_includes($value)
	{
		$pattern = '/\{\{(\s*)include(\s*\(.*\))(\s*)\}\}/';

		return preg_replace($pattern, '<?php echo render$2->with(get_defined_vars()); ?>', $value);
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
	 * Rewrites Blade "for else" statements into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_forelse($value)
	{
		preg_match_all('/(\s*)@forelse(\s*\(.*\))(\s*)/', $value, $matches);

		// First we'll loop through all of the "@forelse" lines. We need to
		// wrap each loop in an if/else statement that checks the count
		// of the variable that is being iterated by the loop.
		if (isset($matches[0]))
		{
			foreach ($matches[0] as $forelse)
			{
				preg_match('/\$[^\s]*/', $forelse, $variable);

				// Once we have extracted the variable being looped against, we can
				// prepend an "if" statmeent to the start of the loop that checks
				// that the count of the variable is greater than zero.
				$if = "<?php if (count({$variable[0]}) > 0): ?>";

				$search = '/(\s*)@forelse(\s*\(.*\))/';

				$replace = '$1'.$if.'<?php foreach$2: ?>';

				$blade = preg_replace($search, $replace, $forelse);

				// Finally, once we have the check prepended to the loop, we will
				// replace all instances of this "forelse" syntax in the view
				// content of the view being compiled to Blade syntax.
				$value = str_replace($forelse, $blade, $value);
			}
		}

		return $value;
	}

	/**
	 * Rewrites Blade "empty" statements into valid PHP.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function compile_empty($value)
	{
		return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
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

	/**
	 * Remove all of the cached views from storage.
	 *
	 * @return void
	 */
	protected static function flush()
	{
		$items = new fIterator(path('storage').'views');

		foreach ($items as $item)
		{
			if ($item->isFile() and $item->getBasename() !== '.gitignore')
			{
				@unlink($item->getRealPath());
			}
		}
	}

}