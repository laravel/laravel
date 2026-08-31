<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Closure;
use Illuminate\Foundation\Mix;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Vite as ViteFacade;
use Illuminate\Support\HtmlString;
use Mockery;

trait InteractsWithContainer
{
    /**
     * The original Vite handler.
     *
     * @var \Illuminate\Foundation\Vite|null
     */
    protected $originalVite;

    /**
     * The original Laravel Mix handler.
     *
     * @var \Illuminate\Foundation\Mix|null
     */
    protected $originalMix;

    /**
     * The original deferred callbacks collection.
     *
     * @var \Illuminate\Support\Defer\DeferredCallbackCollection|null
     */
    protected $originalDeferredCallbacksCollection;

    /**
     * Register an instance of an object in the container.
     *
     * @template TSwap of object
     *
     * @param  string  $abstract
     * @param  TSwap  $instance
     * @return TSwap
     */
    protected function swap($abstract, $instance)
    {
        return $this->instance($abstract, $instance);
    }

    /**
     * Register an instance of an object in the container.
     *
     * @template TInstance of object
     *
     * @param  string  $abstract
     * @param  TInstance  $instance
     * @return TInstance
     */
    protected function instance($abstract, $instance)
    {
        $this->app->instance($abstract, $instance);

        return $instance;
    }

    /**
     * Mock an instance of an object in the container.
     *
     * @template TInstance of object
     *
     * @param  string|class-string<TInstance>  $abstract
     * @param  \Closure|null  $mock
     * @return ($abstract is class-string<TInstance> ? TInstance&\Mockery\MockInterface : \Mockery\MockInterface)
     */
    protected function mock($abstract, ?Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
    }

    /**
     * Mock a partial instance of an object in the container.
     *
     * @template TInstance of object
     *
     * @param  string|class-string<TInstance>  $abstract
     * @param  \Closure|null  $mock
     * @return ($abstract is class-string<TInstance> ? TInstance&\Mockery\MockInterface : \Mockery\MockInterface)
     */
    protected function partialMock($abstract, ?Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args()))->makePartial());
    }

    /**
     * Spy an instance of an object in the container.
     *
     * @template TInstance of object
     *
     * @param  string|class-string<TInstance>  $abstract
     * @param  \Closure|null  $mock
     * @return ($abstract is class-string<TInstance> ? TInstance&\Mockery\MockInterface : \Mockery\MockInterface)
     */
    protected function spy($abstract, ?Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::spy(...array_filter(func_get_args())));
    }

    /**
     * Instruct the container to forget a previously mocked / spied instance of an object.
     *
     * @param  string  $abstract
     * @return $this
     */
    protected function forgetMock($abstract)
    {
        $this->app->forgetInstance($abstract);

        return $this;
    }

    /**
     * Register an empty handler for Vite in the container.
     *
     * @return $this
     */
    protected function withoutVite()
    {
        if ($this->originalVite == null) {
            $this->originalVite = app(Vite::class);
        }

        ViteFacade::clearResolvedInstance();

        $this->swap(Vite::class, new class extends Vite
        {
            public function __invoke($entrypoints, $buildDirectory = null)
            {
                return new HtmlString('');
            }

            public function __call($method, $parameters)
            {
                return '';
            }

            public function __toString()
            {
                return '';
            }

            public function useIntegrityKey($key)
            {
                return $this;
            }

            public function useBuildDirectory($path)
            {
                return $this;
            }

            public function useHotFile($path)
            {
                return $this;
            }

            public function withEntryPoints($entryPoints)
            {
                return $this;
            }

            public function useScriptTagAttributes($attributes)
            {
                return $this;
            }

            public function useStyleTagAttributes($attributes)
            {
                return $this;
            }

            public function usePreloadTagAttributes($attributes)
            {
                return $this;
            }

            public function preloadedAssets()
            {
                return [];
            }

            public function reactRefresh()
            {
                return '';
            }

            public function content($asset, $buildDirectory = null)
            {
                return '';
            }

            public function asset($asset, $buildDirectory = null)
            {
                return '';
            }
        });

        return $this;
    }

    /**
     * Restore Vite in the container.
     *
     * @return $this
     */
    protected function withVite()
    {
        if ($this->originalVite) {
            $this->app->instance(Vite::class, $this->originalVite);
        }

        return $this;
    }

    /**
     * Register an empty handler for Laravel Mix in the container.
     *
     * @return $this
     */
    protected function withoutMix()
    {
        if ($this->originalMix == null) {
            $this->originalMix = app(Mix::class);
        }

        $this->swap(Mix::class, function () {
            return new HtmlString('');
        });

        return $this;
    }

    /**
     * Restore Laravel Mix in the container.
     *
     * @return $this
     */
    protected function withMix()
    {
        if ($this->originalMix) {
            $this->app->instance(Mix::class, $this->originalMix);
        }

        return $this;
    }

    /**
     * Execute deferred functions immediately.
     *
     * @return $this
     */
    protected function withoutDefer()
    {
        if ($this->originalDeferredCallbacksCollection == null) {
            $this->originalDeferredCallbacksCollection = $this->app->make(DeferredCallbackCollection::class);
        }

        $this->swap(DeferredCallbackCollection::class, new class extends DeferredCallbackCollection
        {
            public function offsetSet(mixed $offset, mixed $value): void
            {
                $value();
            }
        });

        return $this;
    }

    /**
     * Restore deferred functions.
     *
     * @return $this
     */
    protected function withDefer()
    {
        if ($this->originalDeferredCallbacksCollection) {
            $this->app->instance(DeferredCallbackCollection::class, $this->originalDeferredCallbacksCollection);
        }

        return $this;
    }
}
