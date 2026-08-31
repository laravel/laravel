<?php

namespace Illuminate\Testing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class ParallelTesting
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The options resolver callback.
     *
     * @var \Closure|null
     */
    protected $optionsResolver;

    /**
     * The token resolver callback.
     *
     * @var \Closure|null
     */
    protected $tokenResolver;

    /**
     * All of the registered "setUp" process callbacks.
     *
     * @var array
     */
    protected $setUpProcessCallbacks = [];

    /**
     * All of the registered "setUp" test case callbacks.
     *
     * @var array
     */
    protected $setUpTestCaseCallbacks = [];

    /**
     * All of the registered "setUp" test database callbacks prior to the migrations.
     *
     * @var array
     */
    protected $setUpTestDatabaseBeforeMigratingCallbacks = [];

    /**
     * All of the registered "setUp" test database callbacks.
     *
     * @var array
     */
    protected $setUpTestDatabaseCallbacks = [];

    /**
     * All of the registered "tearDown" process callbacks.
     *
     * @var array
     */
    protected $tearDownProcessCallbacks = [];

    /**
     * All of the registered "tearDown" test case callbacks.
     *
     * @var array
     */
    protected $tearDownTestCaseCallbacks = [];

    /**
     * Create a new parallel testing instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set a callback that should be used when resolving options.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public function resolveOptionsUsing($resolver)
    {
        $this->optionsResolver = $resolver;
    }

    /**
     * Set a callback that should be used when resolving the unique process token.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public function resolveTokenUsing($resolver)
    {
        $this->tokenResolver = $resolver;
    }

    /**
     * Register a "setUp" process callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function setUpProcess($callback)
    {
        $this->setUpProcessCallbacks[] = $callback;
    }

    /**
     * Register a "setUp" test case callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function setUpTestCase($callback)
    {
        $this->setUpTestCaseCallbacks[] = $callback;
    }

    /**
     * Register a "setUp" test database callback that runs prior to the migrations.
     *
     * @param  callable  $callback
     * @return void
     */
    public function setUpTestDatabaseBeforeMigrating($callback)
    {
        $this->setUpTestDatabaseBeforeMigratingCallbacks[] = $callback;
    }

    /**
     * Register a "setUp" test database callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function setUpTestDatabase($callback)
    {
        $this->setUpTestDatabaseCallbacks[] = $callback;
    }

    /**
     * Register a "tearDown" process callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function tearDownProcess($callback)
    {
        $this->tearDownProcessCallbacks[] = $callback;
    }

    /**
     * Register a "tearDown" test case callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function tearDownTestCase($callback)
    {
        $this->tearDownTestCaseCallbacks[] = $callback;
    }

    /**
     * Call all of the "setUp" process callbacks.
     *
     * @return void
     */
    public function callSetUpProcessCallbacks()
    {
        $this->whenRunningInParallel(function () {
            foreach ($this->setUpProcessCallbacks as $callback) {
                $this->container->call($callback, [
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Call all of the "setUp" test case callbacks.
     *
     * @param  \Illuminate\Foundation\Testing\TestCase  $testCase
     * @return void
     */
    public function callSetUpTestCaseCallbacks($testCase)
    {
        $this->whenRunningInParallel(function () use ($testCase) {
            foreach ($this->setUpTestCaseCallbacks as $callback) {
                $this->container->call($callback, [
                    'testCase' => $testCase,
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Call all of the "setUp" test database callbacks that run prior to migrations.
     *
     * @param  string  $database
     * @return void
     */
    public function callSetUpTestDatabaseBeforeMigratingCallbacks($database)
    {
        $this->whenRunningInParallel(function () use ($database) {
            foreach ($this->setUpTestDatabaseBeforeMigratingCallbacks as $callback) {
                $this->container->call($callback, [
                    'database' => $database,
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Call all of the "setUp" test database callbacks.
     *
     * @param  string  $database
     * @return void
     */
    public function callSetUpTestDatabaseCallbacks($database)
    {
        $this->whenRunningInParallel(function () use ($database) {
            foreach ($this->setUpTestDatabaseCallbacks as $callback) {
                $this->container->call($callback, [
                    'database' => $database,
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Call all of the "tearDown" process callbacks.
     *
     * @return void
     */
    public function callTearDownProcessCallbacks()
    {
        $this->whenRunningInParallel(function () {
            foreach ($this->tearDownProcessCallbacks as $callback) {
                $this->container->call($callback, [
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Call all of the "tearDown" test case callbacks.
     *
     * @param  \Illuminate\Foundation\Testing\TestCase  $testCase
     * @return void
     */
    public function callTearDownTestCaseCallbacks($testCase)
    {
        $this->whenRunningInParallel(function () use ($testCase) {
            foreach ($this->tearDownTestCaseCallbacks as $callback) {
                $this->container->call($callback, [
                    'testCase' => $testCase,
                    'token' => $this->token(),
                ]);
            }
        });
    }

    /**
     * Get a parallel testing option.
     *
     * @param  string  $option
     * @return mixed
     */
    public function option($option)
    {
        $optionsResolver = $this->optionsResolver ?: function ($option) {
            $option = 'LARAVEL_PARALLEL_TESTING_'.Str::upper($option);

            return $_SERVER[$option] ?? false;
        };

        return $optionsResolver($option);
    }

    /**
     * Gets a unique test token.
     *
     * @return string|false
     */
    public function token()
    {
        return $this->tokenResolver
            ? call_user_func($this->tokenResolver)
            : ($_SERVER['TEST_TOKEN'] ?? false);
    }

    /**
     * Apply the callback if tests are running in parallel.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whenRunningInParallel($callback)
    {
        if ($this->inParallel()) {
            $callback();
        }
    }

    /**
     * Indicates if the current tests are been run in parallel.
     *
     * @return bool
     */
    protected function inParallel()
    {
        return ! empty($_SERVER['LARAVEL_PARALLEL_TESTING']) && $this->token();
    }
}
