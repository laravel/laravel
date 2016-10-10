<?php namespace Illuminate\View;

use Illuminate\Support\MessageBag;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;

class ViewServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerEngineResolver();

		$this->registerViewFinder();

		// Once the other components have been registered we're ready to include the
		// view environment and session binder. The session binder will bind onto
		// the "before" application event and add errors into shared view data.
		$this->registerEnvironment();

		$this->registerSessionBinder();
	}

	/**
	 * Register the engine resolver instance.
	 *
	 * @return void
	 */
	public function registerEngineResolver()
	{
		$me = $this;

		$this->app->bindShared('view.engine.resolver', function($app) use ($me)
		{
			$resolver = new EngineResolver;

			// Next we will register the various engines with the resolver so that the
			// environment can resolve the engines it needs for various views based
			// on the extension of view files. We call a method for each engines.
			foreach (array('php', 'blade') as $engine)
			{
				$me->{'register'.ucfirst($engine).'Engine'}($resolver);
			}

			return $resolver;
		});
	}

	/**
	 * Register the PHP engine implementation.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $resolver
	 * @return void
	 */
	public function registerPhpEngine($resolver)
	{
		$resolver->register('php', function() { return new PhpEngine; });
	}

	/**
	 * Register the Blade engine implementation.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $resolver
	 * @return void
	 */
	public function registerBladeEngine($resolver)
	{
		$app = $this->app;

		// The Compiler engine requires an instance of the CompilerInterface, which in
		// this case will be the Blade compiler, so we'll first create the compiler
		// instance to pass into the engine so it can compile the views properly.
		$app->bindShared('blade.compiler', function($app)
		{
			$cache = $app['path.storage'].'/views';

			return new BladeCompiler($app['files'], $cache);
		});

		$resolver->register('blade', function() use ($app)
		{
			return new CompilerEngine($app['blade.compiler'], $app['files']);
		});
	}

	/**
	 * Register the view finder implementation.
	 *
	 * @return void
	 */
	public function registerViewFinder()
	{
		$this->app->bindShared('view.finder', function($app)
		{
			$paths = $app['config']['view.paths'];

			return new FileViewFinder($app['files'], $paths);
		});
	}

	/**
	 * Register the view environment.
	 *
	 * @return void
	 */
	public function registerEnvironment()
	{
		$this->app->bindShared('view', function($app)
		{
			// Next we need to grab the engine resolver instance that will be used by the
			// environment. The resolver will be used by an environment to get each of
			// the various engine implementations such as plain PHP or Blade engine.
			$resolver = $app['view.engine.resolver'];

			$finder = $app['view.finder'];

			$env = new Environment($resolver, $finder, $app['events']);

			// We will also set the container instance on this view environment since the
			// view composers may be classes registered in the container, which allows
			// for great testable, flexible composers for the application developer.
			$env->setContainer($app);

			$env->share('app', $app);

			return $env;
		});
	}

	/**
	 * Register the session binder for the view environment.
	 *
	 * @return void
	 */
	protected function registerSessionBinder()
	{
		list($app, $me) = array($this->app, $this);

		$app->booted(function() use ($app, $me)
		{
			// If the current session has an "errors" variable bound to it, we will share
			// its value with all view instances so the views can easily access errors
			// without having to bind. An empty bag is set when there aren't errors.
			if ($me->sessionHasErrors($app))
			{
				$errors = $app['session.store']->get('errors');

				$app['view']->share('errors', $errors);
			}

			// Putting the errors in the view for every view allows the developer to just
			// assume that some errors are always available, which is convenient since
			// they don't have to continually run checks for the presence of errors.
			else
			{
				$app['view']->share('errors', new MessageBag);
			}
		});
	}

	/**
	 * Determine if the application session has errors.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return bool
	 */
	public function sessionHasErrors($app)
	{
		$config = $app['config']['session'];

		if (isset($app['session.store']) && ! is_null($config['driver']))
		{
			return $app['session.store']->has('errors');
		}
	}

}
