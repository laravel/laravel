=======
Futures
=======

Futures represent a computation that may have not yet completed. RingPHP
uses hybrid of futures and promises to provide a consistent API that can be
used for both blocking and non-blocking consumers.

Promises
--------

You can get the result of a future when it is ready using the promise interface
of a future. Futures expose a promise API via a ``then()`` method that utilizes
`React's promise library <https://github.com/reactphp/promise>`_. You should
use this API when you do not wish to block.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlMultiHandler;

    $request = [
        'http_method' => 'GET',
        'uri'         => '/',
        'headers'     => ['host' => ['httpbin.org']]
    ];

    $response = $handler($request);

    // Use the then() method to use the promise API of the future.
    $response->then(function ($response) {
        echo $response['status'];
    });

You can get the promise used by a future, an instance of
``React\Promise\PromiseInterface``, by calling the ``promise()`` method.

.. code-block:: php

    $response = $handler($request);
    $promise = $response->promise();
    $promise->then(function ($response) {
        echo $response['status'];
    });

This promise value can be used with React's
`aggregate promise functions <https://github.com/reactphp/promise#functions>`_.

Waiting
-------

You can wait on a future to complete and retrieve the value, or *dereference*
the future, using the ``wait()`` method. Calling the ``wait()`` method of a
future will block until the result is available. The result is then returned or
an exception is thrown if and exception was encountered while waiting on the
the result. Subsequent calls to dereference a future will return the previously
completed result or throw the previously encountered exception. Futures can be
cancelled, which stops the computation if possible.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlMultiHandler;

    $response = $handler([
        'http_method' => 'GET',
        'uri'         => '/',
        'headers'     => ['host' => ['httpbin.org']]
    ]);

    // You can explicitly call block to wait on a result.
    $realizedResponse = $response->wait();

    // Future responses can be used like a regular PHP array.
    echo $response['status'];

In addition to explicitly calling the ``wait()`` function, using a future like
a normal value will implicitly trigger the ``wait()`` function.

Future Responses
----------------

RingPHP uses futures to return asynchronous responses immediately. Client
handlers always return future responses that implement
``GuzzleHttp\Ring\Future\ArrayFutureInterface``. These future responses act
just like normal PHP associative arrays for blocking access and provide a
promise interface for non-blocking access.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlMultiHandler;

    $handler = new CurlMultiHandler();

    $request = [
        'http_method'  => 'GET',
        'uri'          => '/',
        'headers'      => ['Host' => ['www.google.com']]
    ];

    $response = $handler($request);

    // Use the promise API for non-blocking access to the response. The actual
    // response value will be delivered to the promise.
    $response->then(function ($response) {
        echo $response['status'];
    });

    // You can wait (block) until the future is completed.
    $response->wait();

    // This will implicitly call wait(), and will block too!
    $response['status'];

.. important::

    Futures that are not completed by the time the underlying handler is
    destructed will be completed when the handler is shutting down.

Cancelling
----------

Futures can be cancelled if they have not already been dereferenced.

RingPHP futures are typically implemented with the
``GuzzleHttp\Ring\Future\BaseFutureTrait``. This trait provides the cancellation
functionality that should be common to most implementations. Cancelling a
future response will try to prevent the request from sending over the wire.

When a future is cancelled, the cancellation function is invoked and performs
the actual work needed to cancel the request from sending if possible
(e.g., telling an event loop to stop sending a request or to close a socket).
If no cancellation function is provided, then a request cannot be cancelled. If
a cancel function is provided, then it should accept the future as an argument
and return true if the future was successfully cancelled or false if it could
not be cancelled.

Wrapping an existing Promise
----------------------------

You can easily create a future from any existing promise using the
``GuzzleHttp\Ring\Future\FutureValue`` class. This class's constructor
accepts a promise as the first argument, a wait function as the second
argument, and a cancellation function as the third argument. The dereference
function is used to force the promise to resolve (for example, manually ticking
an event loop). The cancel function is optional and is used to tell the thing
that created the promise that it can stop computing the result (for example,
telling an event loop to stop transferring a request).

.. code-block:: php

    use GuzzleHttp\Ring\Future\FutureValue;
    use React\Promise\Deferred;

    $deferred = new Deferred();
    $promise = $deferred->promise();

    $f = new FutureValue(
        $promise,
        function () use ($deferred) {
            // This function is responsible for blocking and resolving the
            // promise. Here we pass in a reference to the deferred so that
            // it can be resolved or rejected.
            $deferred->resolve('foo');
        }
    );
