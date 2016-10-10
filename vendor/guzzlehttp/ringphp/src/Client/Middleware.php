<?php
namespace GuzzleHttp\Ring\Client;

/**
 * Provides basic middleware wrappers.
 *
 * If a middleware is more complex than a few lines of code, then it should
 * be implemented in a class rather than a static method.
 */
class Middleware
{
    /**
     * Sends future requests to a future compatible handler while sending all
     * other requests to a default handler.
     *
     * When the "future" option is not provided on a request, any future responses
     * are automatically converted to synchronous responses and block.
     *
     * @param callable $default Handler used for non-streaming responses
     * @param callable $future  Handler used for future responses
     *
     * @return callable Returns the composed handler.
     */
    public static function wrapFuture(
        callable $default,
        callable $future
    ) {
        return function (array $request) use ($default, $future) {
            return empty($request['client']['future'])
                ? $default($request)
                : $future($request);
        };
    }

    /**
     * Sends streaming requests to a streaming compatible handler while sendin
     * all other requests to a default handler.
     *
     * This, for example, could be useful for taking advantage of the
     * performance benefits of curl while still supporting true streaming
     * through the StreamHandler.
     *
     * @param callable $default   Handler used for non-streaming responses
     * @param callable $streaming Handler used for streaming responses
     *
     * @return callable Returns the composed handler.
     */
    public static function wrapStreaming(
        callable $default,
        callable $streaming
    ) {
        return function (array $request) use ($default, $streaming) {
            return empty($request['client']['stream'])
                ? $default($request)
                : $streaming($request);
        };
    }
}
