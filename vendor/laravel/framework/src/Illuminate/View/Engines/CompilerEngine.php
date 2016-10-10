<?php namespace Illuminate\View\Engines;

use Illuminate\View\Compilers\CompilerInterface;

class CompilerEngine extends PhpEngine {

	/**
	 * The Blade compiler instance.
	 *
	 * @var \Illuminate\View\Compilers\CompilerInterface
	 */
	protected $compiler;

	/**
	 * A stack of the last compiled templates.
	 *
	 * @var array
	 */
	protected $lastCompiled = array();

	/**
	 * Create a new Blade view engine instance.
	 *
	 * @param  \Illuminate\View\Compilers\CompilerInterface  $compiler
	 * @return void
	 */
	public function __construct(CompilerInterface $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string  $path
	 * @param  array   $data
	 * @return string
	 */
	public function get($path, array $data = array())
	{
		$this->lastCompiled[] = $path;

		// If this given view has expired, which means it has simply been edited since
		// it was last compiled, we will re-compile the views so we can evaluate a
		// fresh copy of the view. We'll pass the compiler the path of the view.
		if ($this->compiler->isExpired($path))
		{
			$this->compiler->compile($path);
		}

		$compiled = $this->compiler->getCompiledPath($path);

		// Once we have the path to the compiled file, we will evaluate the paths with
		// typical PHP just like any other templates. We also keep a stack of views
		// which have been rendered for right exception messages to be generated.
		$results = $this->evaluatePath($compiled, $data);

		array_pop($this->lastCompiled);

		return $results;
	}

	/**
	 * Handle a view exception.
	 *
	 * @param  \Exception  $e
	 * @return void
	 *
	 * @throws $e
	 */
	protected function handleViewException($e)
	{
		$e = new \ErrorException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

		ob_get_clean(); throw $e;
	}

	/**
	 * Get the exception message for an exception.
	 *
	 * @param  \Exception  $e
	 * @return string
	 */
	protected function getMessage($e)
	{
		return $e->getMessage().' (View: '.realpath(last($this->lastCompiled)).')';
	}

	/**
	 * Get the compiler implementation.
	 *
	 * @return \Illuminate\View\Compilers\CompilerInterface
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

}
