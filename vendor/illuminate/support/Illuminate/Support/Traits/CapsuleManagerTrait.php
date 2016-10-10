<?php namespace Illuminate\Support\Traits;

use Illuminate\Support\Fluent;
use Illuminate\Container\Container;

trait CapsuleManagerTrait {

	/**
	 * The current globally used instance.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * Setup the IoC container instance.
	 *
	 * @param  \Illuminate\Container\Container|null  $container
	 * @return void
	 */
	protected function setupContainer($container)
	{
		$this->container = $container ?: new Container;

		if ( ! $this->container->bound('config'))
		{
			$this->container->instance('config', new Fluent);
		}
	}

	/**
	 * Make this capsule instance available globally.
	 *
	 * @return void
	 */
	public function setAsGlobal()
	{
		static::$instance = $this;
	}

	/**
	 * Get the IoC container instance.
	 *
	 * @return \Illuminate\Container\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Set the IoC container instance.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

}
