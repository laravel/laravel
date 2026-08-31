<?php

namespace Illuminate\Routing;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerUrlGenerator();
        $this->registerRedirector();
        $this->registerPsrRequest();
        $this->registerPsrResponse();
        $this->registerResponseFactory();
        $this->registerCallableDispatcher();
        $this->registerControllerDispatcher();
    }

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            return new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                ), $app['config']['app.asset_url']
            );
        });

        $this->app->extend('url', function (UrlGeneratorContract $url, $app) {
            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                $config = $this->app->make('config');

                return [$config->get('app.key'), ...($config->get('app.previous_keys') ?? [])];
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * Get the URL generator request rebinder.
     *
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }

    /**
     * Register the Redirector service.
     *
     * @return void
     */
    protected function registerRedirector()
    {
        $this->app->singleton('redirect', function ($app) {
            $redirector = new Redirector($app['url']);

            // If the session is set on the application instance, we'll inject it into
            // the redirector instance. This allows the redirect responses to allow
            // for the quite convenient "with" methods that flash to the session.
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });
    }

    /**
     * Register a binding for the PSR-7 request implementation.
     *
     * @return void
     */
    protected function registerPsrRequest()
    {
        $this->app->bind(ServerRequestInterface::class, function ($app) {
            if (class_exists(PsrHttpFactory::class)) {
                $illuminateRequest = $app->make('request');
                $request = (new PsrHttpFactory)->createRequest($illuminateRequest);

                if ($illuminateRequest->getContentTypeFormat() !== 'json' && $illuminateRequest->request->count() === 0) {
                    return $request;
                }

                return $request->withParsedBody(
                    array_merge($request->getParsedBody() ?? [], $illuminateRequest->getPayload()->all())
                );
            }

            throw new BindingResolutionException('Unable to resolve PSR request. Please install the "symfony/psr-http-message-bridge" package.');
        });
    }

    /**
     * Register a binding for the PSR-7 response implementation.
     *
     * @return void
     */
    protected function registerPsrResponse()
    {
        $this->app->bind(ResponseInterface::class, function () {
            if (class_exists(PsrHttpFactory::class)) {
                return (new PsrHttpFactory)->createResponse(new Response);
            }

            throw new BindingResolutionException('Unable to resolve PSR response. Please install the "symfony/psr-http-message-bridge" package.');
        });
    }

    /**
     * Register the response factory implementation.
     *
     * @return void
     */
    protected function registerResponseFactory()
    {
        $this->app->singleton(ResponseFactoryContract::class, function ($app) {
            return new ResponseFactory($app[ViewFactoryContract::class], $app['redirect']);
        });
    }

    /**
     * Register the callable dispatcher.
     *
     * @return void
     */
    protected function registerCallableDispatcher()
    {
        $this->app->singleton(CallableDispatcherContract::class, function ($app) {
            return new CallableDispatcher($app);
        });
    }

    /**
     * Register the controller dispatcher.
     *
     * @return void
     */
    protected function registerControllerDispatcher()
    {
        $this->app->singleton(ControllerDispatcherContract::class, function ($app) {
            return new ControllerDispatcher($app);
        });
    }
}
