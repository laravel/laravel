HttpFoundation Component
========================

HttpFoundation defines an object-oriented layer for the HTTP specification.

It provides an abstraction for requests, responses, uploaded files, cookies,
sessions, ...

In this example, we get a Request object from the current PHP global
variables:

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    $request = Request::createFromGlobals();
    echo $request->getPathInfo();

You can also create a Request directly -- that's interesting for unit testing:

    $request = Request::create('/?foo=bar', 'GET');
    echo $request->getPathInfo();

And here is how to create and send a Response:

    $response = new Response('Not Found', 404, array('Content-Type' => 'text/plain'));
    $response->send();

The Request and the Response classes have many other methods that implement
the HTTP specification.

Loading
-------

If you are not using Composer but are using PHP 5.3.x, you must add the following to your autoloader:

    // SessionHandlerInterface
    if (!interface_exists('SessionHandlerInterface')) {
        $loader->registerPrefixFallback(__DIR__.'/../vendor/symfony/src/Symfony/Component/HttpFoundation/Resources/stubs');
    }

Resources
---------

You can run the unit tests with the following command:

    $ cd path/to/Symfony/Component/HttpFoundation/
    $ composer.phar install
    $ phpunit
