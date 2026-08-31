<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Carbon\CarbonImmutable;
use Illuminate\Console\Application as Artisan;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Illuminate\Mail\Markdown;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Queue\Queue;
use Illuminate\Support\Carbon;
use Illuminate\Support\EncodedHtmlString;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Once;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\Component;
use Mockery;
use Mockery\Exception\InvalidCountException;
use PHPUnit\Metadata\Annotation\Parser\Registry as PHPUnitRegistry;
use Throwable;

trait InteractsWithTestCaseLifecycle
{
    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The callbacks that should be run after the application is created.
     *
     * @var array
     */
    protected $afterApplicationCreatedCallbacks = [];

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * The exception thrown while running an application destruction callback.
     *
     * @var \Throwable
     */
    protected $callbackException;

    /**
     * Indicates if we have made it through the base setUp function.
     *
     * @var bool
     */
    protected $setUpHasRun = false;

    /**
     * Setup the test environment.
     *
     * @internal
     *
     * @return void
     */
    protected function setUpTheTestEnvironment(): void
    {
        Facade::clearResolvedInstances();

        if (! $this->app) {
            $this->refreshApplication();

            ParallelTesting::callSetUpTestCaseCallbacks($this);
        }

        $this->setUpTraits();

        foreach ($this->afterApplicationCreatedCallbacks as $callback) {
            $callback();
        }

        Model::setEventDispatcher($this->app['events']);

        $this->setUpHasRun = true;
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @internal
     *
     * @return void
     */
    protected function tearDownTheTestEnvironment(): void
    {
        if ($this->app) {
            $this->callBeforeApplicationDestroyedCallbacks();

            ParallelTesting::callTearDownTestCaseCallbacks($this);

            $this->app->flush();

            $this->app = null;
        }

        $this->setUpHasRun = false;

        if (property_exists($this, 'serverVariables')) {
            $this->serverVariables = [];
        }

        if (property_exists($this, 'defaultHeaders')) {
            $this->defaultHeaders = [];
        }

        if (class_exists('Mockery')) {
            if ($container = Mockery::getContainer()) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            try {
                Mockery::close();
            } catch (InvalidCountException $e) {
                if (! Str::contains($e->getMethodName(), ['doWrite', 'askQuestion'])) {
                    throw $e;
                }
            }
        }

        if (class_exists(Carbon::class)) {
            Carbon::setTestNow();
        }

        if (class_exists(CarbonImmutable::class)) {
            CarbonImmutable::setTestNow();
        }

        $this->afterApplicationCreatedCallbacks = [];
        $this->beforeApplicationDestroyedCallbacks = [];

        if (property_exists($this, 'originalExceptionHandler')) {
            $this->originalExceptionHandler = null;
        }

        if (property_exists($this, 'originalDeprecationHandler')) {
            $this->originalDeprecationHandler = null;
        }

        AboutCommand::flushState();
        Artisan::forgetBootstrappers();
        Component::flushCache();
        Component::forgetComponentsResolver();
        Component::forgetFactory();
        ConvertEmptyStringsToNull::flushState();
        Factory::flushState();
        EncodedHtmlString::flushState();
        EncryptCookies::flushState();
        HandleCors::flushState();
        HandleExceptions::flushState($this);
        JsonApiResource::flushState();
        JsonResource::flushState();
        Markdown::flushState();
        Migrator::withoutMigrations([]);
        Once::flush();
        PreventRequestsDuringMaintenance::flushState();
        Queue::createPayloadUsing(null);
        RegisterProviders::flushState();
        Response::flushState();
        Sleep::fake(false);
        TrimStrings::flushState();
        TrustProxies::flushState();
        TrustHosts::flushState();
        ValidateCsrfToken::flushState();
        Validator::flushState();
        WorkCommand::flushState();

        if ($this->callbackException) {
            throw $this->callbackException;
        }
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = $this->traitsUsedByTest ?? array_flip(class_uses_recursive(static::class));

        if (isset($uses[RefreshDatabase::class])) {
            $this->refreshDatabase();
        }

        if (isset($uses[DatabaseMigrations::class])) {
            $this->runDatabaseMigrations();
        }

        if (isset($uses[DatabaseTruncation::class])) {
            $this->truncateDatabaseTables();
        }

        if (isset($uses[DatabaseTransactions::class])) {
            $this->beginDatabaseTransaction();
        }

        if (isset($uses[WithoutMiddleware::class])) {
            $this->disableMiddlewareForAllTests();
        }

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }

        foreach ($uses as $trait) {
            if (method_exists($this, $method = 'setUp'.class_basename($trait))) {
                $this->{$method}();
            }

            if (method_exists($this, $method = 'tearDown'.class_basename($trait))) {
                $this->beforeApplicationDestroyed(fn () => $this->{$method}());
            }
        }

        return $uses;
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @internal
     *
     * @return void
     */
    public static function tearDownAfterClassUsingTestCase()
    {
        if (class_exists(PHPUnitRegistry::class)) {
            (function () {
                $this->classDocBlocks = [];
                $this->methodDocBlocks = [];
            })->call(PHPUnitRegistry::getInstance());
        }
    }

    /**
     * Register a callback to be run after the application is created.
     *
     * @param  callable  $callback
     * @return void
     */
    public function afterApplicationCreated(callable $callback)
    {
        $this->afterApplicationCreatedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            $callback();
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    /**
     * Execute the application's pre-destruction callbacks.
     *
     * @return void
     */
    protected function callBeforeApplicationDestroyedCallbacks()
    {
        foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
            try {
                $callback();
            } catch (Throwable $e) {
                if (! $this->callbackException) {
                    $this->callbackException = $e;
                }
            }
        }
    }
}
