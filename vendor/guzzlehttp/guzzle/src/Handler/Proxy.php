<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;

/**
 * Provides basic proxies for handlers.
 *
 * @final
 */
class Proxy
{
    /**
     * Sends synchronous requests to a specific handler while sending all other
     * requests to another handler.
     *
     * @param callable(RequestInterface, array): PromiseInterface $default Handler used for normal responses
     * @param callable(RequestInterface, array): PromiseInterface $sync    Handler used for synchronous responses.
     *
     * @return callable(RequestInterface, array): PromiseInterface Returns the composed handler.
     */
    public static function wrapSync(callable $default, callable $sync): callable
    {
        return static function (RequestInterface $request, array $options) use ($default, $sync): PromiseInterface {
            return empty($options[RequestOptions::SYNCHRONOUS]) ? $default($request, $options) : $sync($request, $options);
        };
    }

    /**
     * Sends streaming requests to a streaming compatible handler while sending
     * all other requests to a default handler.
     *
     * This, for example, could be useful for taking advantage of the
     * performance benefits of curl while still supporting true streaming
     * through the StreamHandler.
     *
     * @param callable(RequestInterface, array): PromiseInterface $default   Handler used for non-streaming responses
     * @param callable(RequestInterface, array): PromiseInterface $streaming Handler used for streaming responses
     *
     * @return callable(RequestInterface, array): PromiseInterface Returns the composed handler.
     */
    public static function wrapStreaming(callable $default, callable $streaming): callable
    {
        return static function (RequestInterface $request, array $options) use ($default, $streaming): PromiseInterface {
            return empty($options['stream']) ? $default($request, $options) : $streaming($request, $options);
        };
    }

    /**
     * Sends requests to a fallback handler when the default cURL handler cannot
     * honor TLS 1.2 selection.
     *
     * @param callable(RequestInterface, array): PromiseInterface $default
     * @param callable(RequestInterface, array): PromiseInterface $fallback
     *
     * @return callable(RequestInterface, array): PromiseInterface Returns the composed handler.
     */
    public static function wrapTlsFallback(callable $default, callable $fallback): callable
    {
        return static function (RequestInterface $request, array $options) use ($default, $fallback): PromiseInterface {
            if (self::requiresTls12Fallback($options)) {
                return $fallback($request, $options);
            }

            return $default($request, $options);
        };
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function requiresTls12Fallback(array $options): bool
    {
        return isset($options[RequestOptions::CRYPTO_METHOD])
            && $options[RequestOptions::CRYPTO_METHOD] === \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
            && !CurlVersion::supportsTls12();
    }
}
