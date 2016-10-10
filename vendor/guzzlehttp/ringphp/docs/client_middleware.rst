=================
Client Middleware
=================

Middleware intercepts requests before they are sent over the wire and can be
used to add functionality to handlers.

Modifying Requests
------------------

Let's say you wanted to modify requests before they are sent over the wire
so that they always add specific headers. This can be accomplished by creating
a function that accepts a handler and returns a new function that adds the
composed behavior.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlHandler;

    $handler = new CurlHandler();

    $addHeaderHandler = function (callable $handler, array $headers = []) {
        return function (array $request) use ($handler, $headers) {
            // Add our custom headers
            foreach ($headers as $key => $value) {
                $request['headers'][$key] = $value;
            }

            // Send the request using the handler and return the response.
            return $handler($request);
        }
    };

    // Create a new handler that adds headers to each request.
    $handler = $addHeaderHandler($handler, [
        'X-AddMe'       => 'hello',
        'Authorization' => 'Basic xyz'
    ]);

    $response = $handler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['httpbin.org']]
    ]);

Modifying Responses
-------------------

You can change a response as it's returned from a middleware. Remember that
responses returned from an handler (including middleware) must implement
``GuzzleHttp\Ring\Future\FutureArrayInterface``. In order to be a good citizen,
you should not expect that the responses returned through your middleware will
be completed synchronously. Instead, you should use the
``GuzzleHttp\Ring\Core::proxy()`` function to modify the response when the
underlying promise is resolved. This function is a helper function that makes it
easy to create a new instance of ``FutureArrayInterface`` that wraps an existing
``FutureArrayInterface`` object.

Let's say you wanted to add headers to a response as they are returned from
your middleware, but you want to make sure you aren't causing future
responses to be dereferenced right away. You can achieve this by modifying the
incoming request and using the ``Core::proxy`` function.

.. code-block:: php

    use GuzzleHttp\Ring\Core;
    use GuzzleHttp\Ring\Client\CurlHandler;

    $handler = new CurlHandler();

    $responseHeaderHandler = function (callable $handler, array $headers) {
        return function (array $request) use ($handler, $headers) {
            // Send the request using the wrapped handler.
            return Core::proxy($handler($request), function ($response) use ($headers) {
                // Add the headers to the response when it is available.
                foreach ($headers as $key => $value) {
                    $response['headers'][$key] = (array) $value;
                }
                // Note that you can return a regular response array when using
                // the proxy method.
                return $response;
            });
        }
    };

    // Create a new handler that adds headers to each response.
    $handler = $responseHeaderHandler($handler, ['X-Header' => 'hello!']);

    $response = $handler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['httpbin.org']]
    ]);

    assert($response['headers']['X-Header'] == 'hello!');

Built-In Middleware
-------------------

RingPHP comes with a few basic client middlewares that modify requests
and responses.

Streaming Middleware
~~~~~~~~~~~~~~~~~~~~

If you want to send all requests with the ``streaming`` option to a specific
handler but other requests to a different handler, then use the streaming
middleware.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlHandler;
    use GuzzleHttp\Ring\Client\StreamHandler;
    use GuzzleHttp\Ring\Client\Middleware;

    $defaultHandler = new CurlHandler();
    $streamingHandler = new StreamHandler();
    $streamingHandler = Middleware::wrapStreaming(
        $defaultHandler,
        $streamingHandler
    );

    // Send the request using the streaming handler.
    $response = $streamingHandler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['www.google.com']],
        'stream'      => true
    ]);

    // Send the request using the default handler.
    $response = $streamingHandler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['www.google.com']]
    ]);

Future Middleware
~~~~~~~~~~~~~~~~~

If you want to send all requests with the ``future`` option to a specific
handler but other requests to a different handler, then use the future
middleware.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlHandler;
    use GuzzleHttp\Ring\Client\CurlMultiHandler;
    use GuzzleHttp\Ring\Client\Middleware;

    $defaultHandler = new CurlHandler();
    $futureHandler = new CurlMultiHandler();
    $futureHandler = Middleware::wrapFuture(
        $defaultHandler,
        $futureHandler
    );

    // Send the request using the blocking CurlHandler.
    $response = $futureHandler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['www.google.com']]
    ]);

    // Send the request using the non-blocking CurlMultiHandler.
    $response = $futureHandler([
        'http_method' => 'GET',
        'headers'     => ['Host' => ['www.google.com']],
        'future'      => true
    ]);
