# Stack/Builder

Builder for stack middlewares based on HttpKernelInterface.

Stack/Builder is a small library that helps you construct a nested
HttpKernelInterface decorator tree. It models it as a stack of middlewares.

## Example

If you want to decorate a [silex](https://github.com/fabpot/Silex) app with
session and cache middlewares, you'll have to do something like this:

    use Symfony\Component\HttpKernel\HttpCache\Store;

    $app = new Silex\Application();

    $app->get('/', function () {
        return 'Hello World!';
    });

    $app = new Stack\Session(
        new Symfony\Component\HttpKernel\HttpCache\HttpCache(
            $app,
            new Store(__DIR__.'/cache')
        )
    );

This can get quite annoying indeed. Stack/Builder simplifies that:

    $stack = (new Stack\Builder())
        ->push('Stack\Session')
        ->push('Symfony\Component\HttpKernel\HttpCache\HttpCache', new Store(__DIR__.'/cache'));

    $app = $stack->resolve($app);

As you can see, by arranging the layers as a stack, they become a lot easier
to work with.

In the front controller, you need to serve the request:

    use Symfony\Component\HttpFoundation\Request;

    $request = Request::createFromGlobals();
    $response = $app->handle($request)->send();
    $app->terminate($request, $response);

Stack/Builder also supports pushing a `callable` on to the stack, for situations
where instantiating middlewares might be more complicated. The `callable` should
accept a `HttpKernelInterface` as the first argument and should also return a
`HttpKernelInterface`. The example above could be rewritten as:

    $stack = (new Stack\Builder())
        ->push('Stack\Session')
        ->push(function ($app) {
            $cache = new HttpCache($app, new Store(__DIR__.'/cache'));
            return $cache;
        });

## Inspiration

* [Rack::Builder](http://rack.rubyforge.org/doc/Rack/Builder.html)
* [HttpKernel middlewares](https://igor.io/2013/02/02/http-kernel-middlewares.html)
