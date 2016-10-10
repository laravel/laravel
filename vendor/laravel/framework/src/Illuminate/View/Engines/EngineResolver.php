<?php namespace Illuminate\View\Engines;

use Closure;

class EngineResolver {

	/**
	 * The array of engine resolvers.
	 *
	 * @var array
	 */
	protected $resolvers = array();

	/**
	 * The resolved engine instances.
	 *
	 * @var array
	 */
	protected $resolved = array();

	/**
	 * Register a new engine resolver.
	 *
	 * The engine string typically corresponds to a file extension.
	 *
	 * @param  string   $engine
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function register($engine, Closure $resolver)
	{
		$this->resolvers[$engine] = $resolver;
	}

	/**
	 * Resolver an engine instance by name.
	 *
	 * @param  string  $engine
	 * @return \Illuminate\View\Engines\EngineInterface
	 */
	public function resolve($engine)
	{
		if ( ! isset($this->resolved[$engine]))
		{
			$this->resolved[$engine] = call_user_func($this->resolvers[$engine]);
		}

		return $this->resolved[$engine];
	}

}
