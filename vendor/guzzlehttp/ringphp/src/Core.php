<?php
namespace GuzzleHttp\Ring;

use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Ring\Future\FutureArrayInterface;
use GuzzleHttp\Ring\Future\FutureArray;

/**
 * Provides core functionality of Ring handlers and middleware.
 */
class Core
{
    /**
     * Returns a function that calls all of the provided functions, in order,
     * passing the arguments provided to the composed function to each function.
     *
     * @param callable[] $functions Array of functions to proxy to.
     *
     * @return callable
     */
    public static function callArray(array $functions)
    {
        return function () use ($functions) {
            $args = func_get_args();
            foreach ($functions as $fn) {
                call_user_func_array($fn, $args);
            }
        };
    }

    /**
     * Gets an array of header line values from a message for a specific header
     *
     * This method searches through the "headers" key of a message for a header
     * using a case-insensitive search.
     *
     * @param array  $message Request or response hash.
     * @param string $header  Header to retrieve
     *
     * @return array
     */
    public static function headerLines($message, $header)
    {
        $result = [];

        if (!empty($message['headers'])) {
            foreach ($message['headers'] as $name => $value) {
                if (!strcasecmp($name, $header)) {
                    $result = array_merge($result, $value);
                }
            }
        }

        return $result;
    }

    /**
     * Gets a header value from a message as a string or null
     *
     * This method searches through the "headers" key of a message for a header
     * using a case-insensitive search. The lines of the header are imploded
     * using commas into a single string return value.
     *
     * @param array  $message Request or response hash.
     * @param string $header  Header to retrieve
     *
     * @return string|null Returns the header string if found, or null if not.
     */
    public static function header($message, $header)
    {
        $match = self::headerLines($message, $header);
        return $match ? implode(', ', $match) : null;
    }

    /**
     * Returns the first header value from a message as a string or null. If
     * a header line contains multiple values separated by a comma, then this
     * function will return the first value in the list.
     *
     * @param array  $message Request or response hash.
     * @param string $header  Header to retrieve
     *
     * @return string|null Returns the value as a string if found.
     */
    public static function firstHeader($message, $header)
    {
        if (!empty($message['headers'])) {
            foreach ($message['headers'] as $name => $value) {
                if (!strcasecmp($name, $header)) {
                    // Return the match itself if it is a single value.
                    $pos = strpos($value[0], ',');
                    return $pos ? substr($value[0], 0, $pos) : $value[0];
                }
            }
        }

        return null;
    }

    /**
     * Returns true if a message has the provided case-insensitive header.
     *
     * @param array  $message Request or response hash.
     * @param string $header  Header to check
     *
     * @return bool
     */
    public static function hasHeader($message, $header)
    {
        if (!empty($message['headers'])) {
            foreach ($message['headers'] as $name => $value) {
                if (!strcasecmp($name, $header)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Parses an array of header lines into an associative array of headers.
     *
     * @param array $lines Header lines array of strings in the following
     *                     format: "Name: Value"
     * @return array
     */
    public static function headersFromLines($lines)
    {
        $headers = [];

        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $headers[trim($parts[0])][] = isset($parts[1])
                ? trim($parts[1])
                : null;
        }

        return $headers;
    }

    /**
     * Removes a header from a message using a case-insensitive comparison.
     *
     * @param array  $message Message that contains 'headers'
     * @param string $header  Header to remove
     *
     * @return array
     */
    public static function removeHeader(array $message, $header)
    {
        if (isset($message['headers'])) {
            foreach (array_keys($message['headers']) as $key) {
                if (!strcasecmp($header, $key)) {
                    unset($message['headers'][$key]);
                }
            }
        }

        return $message;
    }

    /**
     * Replaces any existing case insensitive headers with the given value.
     *
     * @param array  $message Message that contains 'headers'
     * @param string $header  Header to set.
     * @param array  $value   Value to set.
     *
     * @return array
     */
    public static function setHeader(array $message, $header, array $value)
    {
        $message = self::removeHeader($message, $header);
        $message['headers'][$header] = $value;

        return $message;
    }

    /**
     * Creates a URL string from a request.
     *
     * If the "url" key is present on the request, it is returned, otherwise
     * the url is built up based on the scheme, host, uri, and query_string
     * request values.
     *
     * @param array $request Request to get the URL from
     *
     * @return string Returns the request URL as a string.
     * @throws \InvalidArgumentException if no Host header is present.
     */
    public static function url(array $request)
    {
        if (isset($request['url'])) {
            return $request['url'];
        }

        $uri = (isset($request['scheme'])
                ? $request['scheme'] : 'http') . '://';

        if ($host = self::header($request, 'host')) {
            $uri .= $host;
        } else {
            throw new \InvalidArgumentException('No Host header was provided');
        }

        if (isset($request['uri'])) {
            $uri .= $request['uri'];
        }

        if (isset($request['query_string'])) {
            $uri .= '?' . $request['query_string'];
        }

        return $uri;
    }

    /**
     * Reads the body of a message into a string.
     *
     * @param array|FutureArrayInterface $message Array containing a "body" key
     *
     * @return null|string Returns the body as a string or null if not set.
     * @throws \InvalidArgumentException if a request body is invalid.
     */
    public static function body($message)
    {
        if (!isset($message['body'])) {
            return null;
        }

        if ($message['body'] instanceof StreamInterface) {
            return (string) $message['body'];
        }

        switch (gettype($message['body'])) {
            case 'string':
                return $message['body'];
            case 'resource':
                return stream_get_contents($message['body']);
            case 'object':
                if ($message['body'] instanceof \Iterator) {
                    return implode('', iterator_to_array($message['body']));
                } elseif (method_exists($message['body'], '__toString')) {
                    return (string) $message['body'];
                }
            default:
                throw new \InvalidArgumentException('Invalid request body: '
                    . self::describeType($message['body']));
        }
    }

    /**
     * Rewind the body of the provided message if possible.
     *
     * @param array $message Message that contains a 'body' field.
     *
     * @return bool Returns true on success, false on failure
     */
    public static function rewindBody($message)
    {
        if ($message['body'] instanceof StreamInterface) {
            return $message['body']->seek(0);
        }

        if ($message['body'] instanceof \Generator) {
            return false;
        }

        if ($message['body'] instanceof \Iterator) {
            $message['body']->rewind();
            return true;
        }

        if (is_resource($message['body'])) {
            return rewind($message['body']);
        }

        return is_string($message['body'])
            || (is_object($message['body'])
                && method_exists($message['body'], '__toString'));
    }

    /**
     * Debug function used to describe the provided value type and class.
     *
     * @param mixed $input
     *
     * @return string Returns a string containing the type of the variable and
     *                if a class is provided, the class name.
     */
    public static function describeType($input)
    {
        switch (gettype($input)) {
            case 'object':
                return 'object(' . get_class($input) . ')';
            case 'array':
                return 'array(' . count($input) . ')';
            default:
                ob_start();
                var_dump($input);
                // normalize float vs double
                return str_replace('double(', 'float(', rtrim(ob_get_clean()));
        }
    }

    /**
     * Sleep for the specified amount of time specified in the request's
     * ['client']['delay'] option if present.
     *
     * This function should only be used when a non-blocking sleep is not
     * possible.
     *
     * @param array $request Request to sleep
     */
    public static function doSleep(array $request)
    {
        if (isset($request['client']['delay'])) {
            usleep($request['client']['delay'] * 1000);
        }
    }

    /**
     * Returns a proxied future that modifies the dereferenced value of another
     * future using a promise.
     *
     * @param FutureArrayInterface $future      Future to wrap with a new future
     * @param callable    $onFulfilled Invoked when the future fulfilled
     * @param callable    $onRejected  Invoked when the future rejected
     * @param callable    $onProgress  Invoked when the future progresses
     *
     * @return FutureArray
     */
    public static function proxy(
        FutureArrayInterface $future,
        callable $onFulfilled = null,
        callable $onRejected = null,
        callable $onProgress = null
    ) {
        return new FutureArray(
            $future->then($onFulfilled, $onRejected, $onProgress),
            [$future, 'wait'],
            [$future, 'cancel']
        );
    }

    /**
     * Returns a debug stream based on the provided variable.
     *
     * @param mixed $value Optional value
     *
     * @return resource
     */
    public static function getDebugResource($value = null)
    {
        if (is_resource($value)) {
            return $value;
        } elseif (defined('STDOUT')) {
            return STDOUT;
        } else {
            return fopen('php://output', 'w');
        }
    }
}
