=======
RingPHP
=======

Provides a simple API and specification that abstracts away the details of HTTP
into a single PHP function. RingPHP be used to power HTTP clients and servers
through a PHP function that accepts a request hash and returns a response hash
that is fulfilled using a `promise <https://github.com/reactphp/promise>`_,
allowing RingPHP to support both synchronous and asynchronous workflows.

By abstracting the implementation details of different HTTP clients and
servers, RingPHP allows you to utilize pluggable HTTP clients and servers
without tying your application to a specific implementation.

.. toctree::
   :maxdepth: 2

   spec
   futures
   client_middleware
   client_handlers
   testing

.. code-block:: php

    <?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Ring\Client\CurlHandler;

    $handler = new CurlHandler();
    $response = $handler([
        'http_method' => 'GET',
        'uri'         => '/',
        'headers'     => [
            'host'  => ['www.google.com'],
            'x-foo' => ['baz']
        ]
    ]);

    $response->then(function (array $response) {
        echo $response['status'];
    });

    $response->wait();

RingPHP is inspired by Clojure's `Ring <https://github.com/ring-clojure/ring>`_,
which, in turn, was inspired by Python's WSGI and Ruby's Rack. RingPHP is
utilized as the handler layer in `Guzzle <http://guzzlephp.org>`_ 5.0+ to send
HTTP requests.
