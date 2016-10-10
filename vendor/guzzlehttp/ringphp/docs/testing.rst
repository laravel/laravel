=======
Testing
=======

RingPHP tests client handlers using `PHPUnit <https://phpunit.de/>`_ and a
built-in node.js web server.

Running Tests
-------------

First, install the dependencies using `Composer <https://getcomposer.org>`_.

    composer.phar install

Next, run the unit tests using ``Make``.

    make test

The tests are also run on Travis-CI on each commit: https://travis-ci.org/guzzle/guzzle-ring

Test Server
-----------

Testing client handlers usually involves actually sending HTTP requests.
RingPHP provides a node.js web server that returns canned responses and
keep a list of the requests that have been received. The server can then
be queried to get a list of the requests that were sent by the client so that
you can ensure that the client serialized and transferred requests as intended.

The server keeps a list of queued responses and returns responses that are
popped off of the queue as HTTP requests are received. When there are not
more responses to serve, the server returns a 500 error response.

The test server uses the ``GuzzleHttp\Tests\Ring\Client\Server`` class to
control the server.

.. code-block:: php

    use GuzzleHttp\Ring\Client\StreamHandler;
    use GuzzleHttp\Tests\Ring\Client\Server;

    // First return a 200 followed by a 404 response.
    Server::enqueue([
        ['status' => 200],
        ['status' => 404]
    ]);

    $handler = new StreamHandler();

    $response = $handler([
        'http_method' => 'GET',
        'headers'     => ['host' => [Server::$host]],
        'uri'         => '/'
    ]);

    assert(200 == $response['status']);

    $response = $handler([
        'http_method' => 'HEAD',
        'headers'     => ['host' => [Server::$host]],
        'uri'         => '/'
    ]);

    assert(404 == $response['status']);

After requests have been sent, you can get a list of the requests as they
were sent over the wire to ensure they were sent correctly.

.. code-block:: php

    $received = Server::received();

    assert('GET' == $received[0]['http_method']);
    assert('HEAD' == $received[1]['http_method']);
