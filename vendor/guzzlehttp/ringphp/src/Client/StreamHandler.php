<?php
namespace GuzzleHttp\Ring\Client;

use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Exception\ConnectException;
use GuzzleHttp\Ring\Exception\RingException;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Stream\InflateStream;
use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\Utils;

/**
 * RingPHP client handler that uses PHP's HTTP stream wrapper.
 */
class StreamHandler
{
    private $options;
    private $lastHeaders;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function __invoke(array $request)
    {
        $url = Core::url($request);
        Core::doSleep($request);

        try {
            // Does not support the expect header.
            $request = Core::removeHeader($request, 'Expect');
            $stream = $this->createStream($url, $request);
            return $this->createResponse($request, $url, $stream);
        } catch (RingException $e) {
            return $this->createErrorResponse($url, $e);
        }
    }

    private function createResponse(array $request, $url, $stream)
    {
        $hdrs = $this->lastHeaders;
        $this->lastHeaders = null;
        $parts = explode(' ', array_shift($hdrs), 3);
        $response = [
            'version'        => substr($parts[0], 5),
            'status'         => $parts[1],
            'reason'         => isset($parts[2]) ? $parts[2] : null,
            'headers'        => Core::headersFromLines($hdrs),
            'effective_url'  => $url,
        ];

        $stream = $this->checkDecode($request, $response, $stream);

        // If not streaming, then drain the response into a stream.
        if (empty($request['client']['stream'])) {
            $dest = isset($request['client']['save_to'])
                ? $request['client']['save_to']
                : fopen('php://temp', 'r+');
            $stream = $this->drain($stream, $dest);
        }

        $response['body'] = $stream;

        return new CompletedFutureArray($response);
    }

    private function checkDecode(array $request, array $response, $stream)
    {
        // Automatically decode responses when instructed.
        if (!empty($request['client']['decode_content'])) {
            switch (Core::firstHeader($response, 'Content-Encoding', true)) {
                case 'gzip':
                case 'deflate':
                    $stream = new InflateStream(Stream::factory($stream));
                    break;
            }
        }

        return $stream;
    }

    /**
     * Drains the stream into the "save_to" client option.
     *
     * @param resource                        $stream
     * @param string|resource|StreamInterface $dest
     *
     * @return Stream
     * @throws \RuntimeException when the save_to option is invalid.
     */
    private function drain($stream, $dest)
    {
        if (is_resource($stream)) {
            if (!is_resource($dest)) {
                $stream = Stream::factory($stream);
            } else {
                stream_copy_to_stream($stream, $dest);
                fclose($stream);
                rewind($dest);
                return $dest;
            }
        }

        // Stream the response into the destination stream
        $dest = is_string($dest)
            ? new Stream(Utils::open($dest, 'r+'))
            : Stream::factory($dest);

        Utils::copyToStream($stream, $dest);
        $dest->seek(0);
        $stream->close();

        return $dest;
    }

    /**
     * Creates an error response for the given stream.
     *
     * @param string        $url
     * @param RingException $e
     *
     * @return array
     */
    private function createErrorResponse($url, RingException $e)
    {
        // Determine if the error was a networking error.
        $message = $e->getMessage();

        // This list can probably get more comprehensive.
        if (strpos($message, 'getaddrinfo') // DNS lookup failed
            || strpos($message, 'Connection refused')
        ) {
            $e = new ConnectException($e->getMessage(), 0, $e);
        }

        return new CompletedFutureArray([
            'status'        => null,
            'body'          => null,
            'headers'       => [],
            'effective_url' => $url,
            'error'         => $e
        ]);
    }

    /**
     * Create a resource and check to ensure it was created successfully
     *
     * @param callable $callback Callable that returns stream resource
     *
     * @return resource
     * @throws \RuntimeException on error
     */
    private function createResource(callable $callback)
    {
        $errors = null;
        set_error_handler(function ($_, $msg, $file, $line) use (&$errors) {
            $errors[] = [
                'message' => $msg,
                'file'    => $file,
                'line'    => $line
            ];
            return true;
        });

        $resource = $callback();
        restore_error_handler();

        if (!$resource) {
            $message = 'Error creating resource: ';
            foreach ($errors as $err) {
                foreach ($err as $key => $value) {
                    $message .= "[$key] $value" . PHP_EOL;
                }
            }
            throw new RingException(trim($message));
        }

        return $resource;
    }

    private function createStream($url, array $request)
    {
        static $methods;
        if (!$methods) {
            $methods = array_flip(get_class_methods(__CLASS__));
        }

        // HTTP/1.1 streams using the PHP stream wrapper require a
        // Connection: close header
        if ((!isset($request['version']) || $request['version'] == '1.1')
            && !Core::hasHeader($request, 'Connection')
        ) {
            $request['headers']['Connection'] = ['close'];
        }

        // Ensure SSL is verified by default
        if (!isset($request['client']['verify'])) {
            $request['client']['verify'] = true;
        }

        $params = [];
        $options = $this->getDefaultOptions($request);

        if (isset($request['client'])) {
            foreach ($request['client'] as $key => $value) {
                $method = "add_{$key}";
                if (isset($methods[$method])) {
                    $this->{$method}($request, $options, $value, $params);
                }
            }
        }

        return $this->createStreamResource(
            $url,
            $request,
            $options,
            $this->createContext($request, $options, $params)
        );
    }

    private function getDefaultOptions(array $request)
    {
        $headers = "";
        foreach ($request['headers'] as $name => $value) {
            foreach ((array) $value as $val) {
                $headers .= "$name: $val\r\n";
            }
        }

        $context = [
            'http' => [
                'method'           => $request['http_method'],
                'header'           => $headers,
                'protocol_version' => isset($request['version']) ? $request['version'] : 1.1,
                'ignore_errors'    => true,
                'follow_location'  => 0,
            ],
        ];

        $body = Core::body($request);
        if (isset($body)) {
            $context['http']['content'] = $body;
            // Prevent the HTTP handler from adding a Content-Type header.
            if (!Core::hasHeader($request, 'Content-Type')) {
                $context['http']['header'] .= "Content-Type:\r\n";
            }
        }

        $context['http']['header'] = rtrim($context['http']['header']);

        return $context;
    }

    private function add_proxy(array $request, &$options, $value, &$params)
    {
        if (!is_array($value)) {
            $options['http']['proxy'] = $value;
        } else {
            $scheme = isset($request['scheme']) ? $request['scheme'] : 'http';
            if (isset($value[$scheme])) {
                $options['http']['proxy'] = $value[$scheme];
            }
        }
    }

    private function add_timeout(array $request, &$options, $value, &$params)
    {
        $options['http']['timeout'] = $value;
    }

    private function add_verify(array $request, &$options, $value, &$params)
    {
        if ($value === true) {
            // PHP 5.6 or greater will find the system cert by default. When
            // < 5.6, use the Guzzle bundled cacert.
            if (PHP_VERSION_ID < 50600) {
                $options['ssl']['cafile'] = ClientUtils::getDefaultCaBundle();
            }
        } elseif (is_string($value)) {
            $options['ssl']['cafile'] = $value;
            if (!file_exists($value)) {
                throw new RingException("SSL CA bundle not found: $value");
            }
        } elseif ($value === false) {
            $options['ssl']['verify_peer'] = false;
            $options['ssl']['allow_self_signed'] = true;
            return;
        } else {
            throw new RingException('Invalid verify request option');
        }

        $options['ssl']['verify_peer'] = true;
        $options['ssl']['allow_self_signed'] = false;
    }

    private function add_cert(array $request, &$options, $value, &$params)
    {
        if (is_array($value)) {
            $options['ssl']['passphrase'] = $value[1];
            $value = $value[0];
        }

        if (!file_exists($value)) {
            throw new RingException("SSL certificate not found: {$value}");
        }

        $options['ssl']['local_cert'] = $value;
    }

    private function add_progress(array $request, &$options, $value, &$params)
    {
        $fn = function ($code, $_1, $_2, $_3, $transferred, $total) use ($value) {
            if ($code == STREAM_NOTIFY_PROGRESS) {
                $value($total, $transferred, null, null);
            }
        };

        // Wrap the existing function if needed.
        $params['notification'] = isset($params['notification'])
            ? Core::callArray([$params['notification'], $fn])
            : $fn;
    }

    private function add_debug(array $request, &$options, $value, &$params)
    {
        if ($value === false) {
            return;
        }

        static $map = [
            STREAM_NOTIFY_CONNECT       => 'CONNECT',
            STREAM_NOTIFY_AUTH_REQUIRED => 'AUTH_REQUIRED',
            STREAM_NOTIFY_AUTH_RESULT   => 'AUTH_RESULT',
            STREAM_NOTIFY_MIME_TYPE_IS  => 'MIME_TYPE_IS',
            STREAM_NOTIFY_FILE_SIZE_IS  => 'FILE_SIZE_IS',
            STREAM_NOTIFY_REDIRECTED    => 'REDIRECTED',
            STREAM_NOTIFY_PROGRESS      => 'PROGRESS',
            STREAM_NOTIFY_FAILURE       => 'FAILURE',
            STREAM_NOTIFY_COMPLETED     => 'COMPLETED',
            STREAM_NOTIFY_RESOLVE       => 'RESOLVE',
        ];

        static $args = ['severity', 'message', 'message_code',
            'bytes_transferred', 'bytes_max'];

        $value = Core::getDebugResource($value);
        $ident = $request['http_method'] . ' ' . Core::url($request);
        $fn = function () use ($ident, $value, $map, $args) {
            $passed = func_get_args();
            $code = array_shift($passed);
            fprintf($value, '<%s> [%s] ', $ident, $map[$code]);
            foreach (array_filter($passed) as $i => $v) {
                fwrite($value, $args[$i] . ': "' . $v . '" ');
            }
            fwrite($value, "\n");
        };

        // Wrap the existing function if needed.
        $params['notification'] = isset($params['notification'])
            ? Core::callArray([$params['notification'], $fn])
            : $fn;
    }

    private function applyCustomOptions(array $request, array &$options)
    {
        if (!isset($request['client']['stream_context'])) {
            return;
        }

        if (!is_array($request['client']['stream_context'])) {
            throw new RingException('stream_context must be an array');
        }

        $options = array_replace_recursive(
            $options,
            $request['client']['stream_context']
        );
    }

    private function createContext(array $request, array $options, array $params)
    {
        $this->applyCustomOptions($request, $options);
        return $this->createResource(
            function () use ($request, $options, $params) {
                return stream_context_create($options, $params);
            },
            $request,
            $options
        );
    }

    private function createStreamResource(
        $url,
        array $request,
        array $options,
        $context
    ) {
        return $this->createResource(
            function () use ($url, $context) {
                if (false === strpos($url, 'http')) {
                    trigger_error("URL is invalid: {$url}", E_USER_WARNING);
                    return null;
                }
                $resource = fopen($url, 'r', null, $context);
                $this->lastHeaders = $http_response_header;
                return $resource;
            },
            $request,
            $options
        );
    }
}
