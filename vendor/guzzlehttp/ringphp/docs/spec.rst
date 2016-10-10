=============
Specification
=============

RingPHP applications consist of handlers, requests, responses, and
middleware.

Handlers
--------

Handlers are implemented as a PHP ``callable`` that accept a request array 
and return a response array (``GuzzleHttp\Ring\Future\FutureArrayInterface``).

For example:

.. code-block:: php

    use GuzzleHttp\Ring\Future\CompletedFutureArray;

    $mockHandler = function (array $request) {
        return new CompletedFutureArray([
            'status'  => 200,
            'headers' => ['X-Foo' => ['Bar']],
            'body'    => 'Hello!'
         ]);
    };

This handler returns the same response each time it is invoked. All RingPHP
handlers must return a ``GuzzleHttp\Ring\Future\FutureArrayInterface``. Use
``GuzzleHttp\Ring\Future\CompletedFutureArray`` when returning a response that
has already completed.

Requests
--------

A request array is a PHP associative array that contains the configuration
settings need to send a request.

.. code-block:: php

    $request = [
        'http_method' => 'GET',
        'scheme'      => 'http',
        'uri'         => '/',
        'body'        => 'hello!',
        'client'      => ['timeout' => 1.0],
        'headers'     => [
            'host'  => ['httpbin.org'],
            'X-Foo' => ['baz', 'bar']
        ]
    ];

The request array contains the following key value pairs:

request_method
    (string, required) The HTTP request method, must be all caps corresponding
    to a HTTP request method, such as ``GET`` or ``POST``.

scheme
    (string) The transport protocol, must be one of ``http`` or ``https``.
    Defaults to ``http``.

uri
    (string, required) The request URI excluding the query string. Must
    start with "/".

query_string
    (string) The query string, if present (e.g., ``foo=bar``).

version
    (string) HTTP protocol version. Defaults to ``1.1``.

headers
    (required, array) Associative array of headers. Each key represents the
    header name. Each value contains an array of strings where each entry of
    the array SHOULD be sent over the wire on a separate header line.

body
    (string, fopen resource, ``Iterator``, ``GuzzleHttp\Stream\StreamInterface``)
    The body of the request, if present. Can be a string, resource returned
    from fopen, an ``Iterator`` that yields chunks of data, an object that
    implemented ``__toString``, or a ``GuzzleHttp\Stream\StreamInterface``.

future
    (bool, string) Controls the asynchronous behavior of a response.

    Set to ``true`` or omit the ``future`` option to *request* that a request
    will be completed asynchronously. Keep in mind that your request might not
    necessarily be completed asynchronously based on the handler you are using.
    Set the ``future`` option to ``false`` to request that a synchronous
    response be provided.

    You can provide a string value to specify fine-tuned future behaviors that
    may be specific to the underlying handlers you are using. There are,
    however, some common future options that handlers should implement if
    possible.

    lazy
        Requests that the handler does not open and send the request
        immediately, but rather only opens and sends the request once the
        future is dereferenced. This option is often useful for sending a large
        number of requests concurrently to allow handlers to take better
        advantage of non-blocking transfers by first building up a pool of
        requests.

    If an handler does not implement or understand a provided string value,
    then the request MUST be treated as if the user provided ``true`` rather
    than the string value.

    Future responses created by asynchronous handlers MUST attempt to complete
    any outstanding future responses when they are destructed. Asynchronous
    handlers MAY choose to automatically complete responses when the number
    of outstanding requests reaches an handler-specific threshold.

Client Specific Options
~~~~~~~~~~~~~~~~~~~~~~~

The following options are only used in ring client handlers.

.. _client-options:

client
    (array) Associative array of client specific transfer options. The
    ``client`` request key value pair can contain the following keys:

    cert
        (string, array) Set to a string to specify the path to a file
        containing a PEM formatted SSL client side certificate. If a password
        is required, then set ``cert`` to an array containing the path to the
        PEM file in the first array element followed by the certificate
        password in the second array element.

    connect_timeout
        (float) Float describing the number of seconds to wait while trying to
        connect to a server. Use ``0`` to wait indefinitely (the default
        behavior).

    debug
        (bool, fopen() resource) Set to true or set to a PHP stream returned by
        fopen() to enable debug output with the handler used to send a request.
        If set to ``true``, the output is written to PHP's STDOUT. If a PHP
        ``fopen`` resource handle is provided, the output is written to the
        stream.

        "Debug output" is handler specific: different handlers will yield
        different output and various various level of detail. For example, when
        using cURL to transfer requests, cURL's `CURLOPT_VERBOSE <http://curl.haxx.se/libcurl/c/CURLOPT_VERBOSE.html>`_
        will be used. When using the PHP stream wrapper, `stream notifications <http://php.net/manual/en/function.stream-notification-callback.php>`_
        will be emitted.

    decode_content
        (bool) Specify whether or not ``Content-Encoding`` responses
        (gzip, deflate, etc.) are automatically decoded. Set to ``true`` to
        automatically decode encoded responses. Set to ``false`` to not decode
        responses. By default, content is *not* decoded automatically.

    delay
        (int) The number of milliseconds to delay before sending the request.
        This is often used for delaying before retrying a request. Handlers
        SHOULD implement this if possible, but it is not a strict requirement.

    progress
        (function) Defines a function to invoke when transfer progress is made.
        The function accepts the following arguments:

        1. The total number of bytes expected to be downloaded
        2. The number of bytes downloaded so far
        3. The number of bytes expected to be uploaded
        4. The number of bytes uploaded so far

    proxy
        (string, array) Pass a string to specify an HTTP proxy, or an
        associative array to specify different proxies for different protocols
        where the scheme is the key and the value is the proxy address.

        .. code-block:: php

            $request = [
                'http_method' => 'GET',
                'headers'     => ['host' => ['httpbin.org']],
                'client'      => [
                    // Use different proxies for different URI schemes.
                    'proxy' => [
                        'http'  => 'http://proxy.example.com:5100',
                        'https' => 'https://proxy.example.com:6100'
                    ]
                ]
            ];

    ssl_key
        (string, array) Specify the path to a file containing a private SSL key
        in PEM format. If a password is required, then set to an array
        containing the path to the SSL key in the first array element followed
        by the password required for the certificate in the second element.

    save_to
        (string, fopen resource, ``GuzzleHttp\Stream\StreamInterface``)
        Specifies where the body of the response is downloaded. Pass a string to
        open a local file on disk and save the output to the file. Pass an fopen
        resource to save the output to a PHP stream resource. Pass a
        ``GuzzleHttp\Stream\StreamInterface`` to save the output to a Guzzle
        StreamInterface. Omitting this option will typically save the body of a
        response to a PHP temp stream.

    stream
        (bool) Set to true to stream a response rather than download it all
        up-front. This option will only be utilized when the corresponding
        handler supports it.

    timeout
        (float) Float describing the timeout of the request in seconds. Use 0 to
        wait indefinitely (the default behavior).

    verify
        (bool, string) Describes the SSL certificate verification behavior of a
        request. Set to true to enable SSL certificate verification using the
        system CA bundle when available (the default). Set to false to disable
        certificate verification (this is insecure!). Set to a string to provide
        the path to a CA bundle on disk to enable verification using a custom
        certificate.

    version
        (string) HTTP protocol version to use with the request.

Server Specific Options
~~~~~~~~~~~~~~~~~~~~~~~

The following options are only used in ring server handlers.

server_port
    (integer) The port on which the request is being handled. This is only
    used with ring servers, and is required.

server_name
    (string) The resolved server name, or the server IP address. Required when
    using a Ring server.

remote_addr
    (string) The IP address of the client or the last proxy that sent the
    request. Required when using a Ring server.

Responses
---------

A response is an array-like object that implements
``GuzzleHttp\Ring\Future\FutureArrayInterface``. Responses contain the
following key value pairs:

body
    (string, fopen resource, ``Iterator``, ``GuzzleHttp\Stream\StreamInterface``)
    The body of the response, if present. Can be a string, resource returned
    from fopen, an ``Iterator`` that yields chunks of data, an object that
    implemented ``__toString``, or a ``GuzzleHttp\Stream\StreamInterface``.

effective_url
    (string) The URL that returned the resulting response.

error
    (``\Exception``) Contains an exception describing any errors that were
    encountered during the transfer.

headers
    (Required, array) Associative array of headers. Each key represents the
    header name. Each value contains an array of strings where each entry of
    the array is a header line. The headers array MAY be an empty array in the
    event an error occurred before a response was received.

reason
    (string) Optional reason phrase. This option should be provided when the
    reason phrase does not match the typical reason phrase associated with the
    ``status`` code. See `RFC 7231 <http://tools.ietf.org/html/rfc7231#section-6.1>`_
    for a list of HTTP reason phrases mapped to status codes.

status
    (Required, integer) The HTTP status code. The status code MAY be set to
    ``null`` in the event an error occurred before a response was received
    (e.g., a networking error).

transfer_stats
    (array) Provides an associative array of arbitrary transfer statistics if
    provided by the underlying handler.

version
    (string) HTTP protocol version. Defaults to ``1.1``.

Middleware
----------

Ring middleware augments the functionality of handlers by invoking them in the
process of generating responses. Middleware is typically implemented as a
higher-order function that takes one or more handlers as arguments followed by
an optional associative array of options as the last argument, returning a new
handler with the desired compound behavior.

Here's an example of a middleware that adds a Content-Type header to each
request.

.. code-block:: php

    use GuzzleHttp\Ring\Client\CurlHandler;
    use GuzzleHttp\Ring\Core;

    $contentTypeHandler = function(callable $handler, $contentType) {
        return function (array $request) use ($handler, $contentType) {
            return $handler(Core::setHeader('Content-Type', $contentType));
        };
    };

    $baseHandler = new CurlHandler();
    $wrappedHandler = $contentTypeHandler($baseHandler, 'text/html');
    $response = $wrappedHandler([/** request hash **/]);
