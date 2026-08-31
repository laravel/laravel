<?php

namespace GuzzleHttp;

use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\PromisorInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Sends an iterator of requests concurrently using a capped pool size.
 *
 * The pool will read from an iterator until it is cancelled or until the
 * iterator is consumed. When a request is yielded, the request is sent after
 * applying the "request_options" request options (if provided in the ctor).
 *
 * When a function is yielded by the iterator, the function is provided the
 * "request_options" array that should be merged on top of any existing
 * options, and the function MUST then return a wait-able promise.
 *
 * @final
 */
class Pool implements PromisorInterface
{
    /**
     * @var EachPromise
     */
    private $each;

    /**
     * @param ClientInterface $client   Client used to send the requests.
     * @param array|\Iterator $requests Requests or functions that return
     *                                  requests to send concurrently.
     * @param array           $config   Associative array of options
     *                                  - concurrency: (int) Maximum number of requests to send concurrently
     *                                  - options: Array of request options to apply to each request.
     *                                  - fulfilled: (callable) Function to invoke when a request completes.
     *                                  - rejected: (callable) Function to invoke when a request is rejected.
     */
    public function __construct(ClientInterface $client, $requests, array $config = [])
    {
        if (!isset($config['concurrency'])) {
            $config['concurrency'] = 25;
        }

        if (isset($config['options'])) {
            $opts = $config['options'];
            unset($config['options']);
        } else {
            $opts = [];
        }

        if (!\is_iterable($requests)) {
            \trigger_deprecation(
                'guzzlehttp/guzzle',
                '7.11',
                'Passing a non-iterable request collection to %s::__construct() or %s::batch() is deprecated; guzzlehttp/guzzle 8.0 will require an iterable.',
                __CLASS__,
                __CLASS__
            );

            $requests = [$requests];
        }

        $iterable = P\Create::iterFor($requests);
        $requests = static function () use ($iterable, $client, $opts) {
            foreach ($iterable as $key => $rfn) {
                if ($rfn instanceof RequestInterface) {
                    yield $key => $client->sendAsync($rfn, $opts);
                } elseif (\is_callable($rfn)) {
                    yield $key => $rfn($opts);
                } else {
                    throw new \InvalidArgumentException('Each value yielded by the iterator must be a Psr\Http\Message\RequestInterface or a callable that returns a promise that fulfills with a Psr\Http\Message\ResponseInterface object.');
                }
            }
        };

        $this->each = new EachPromise($requests(), $config);
    }

    /**
     * Get promise
     */
    public function promise(): PromiseInterface
    {
        return $this->each->promise();
    }

    /**
     * Sends multiple requests concurrently and returns an array of responses
     * and exceptions that uses the same ordering as the provided requests.
     *
     * IMPORTANT: This method keeps every request and response in memory, and
     * as such, is NOT recommended when sending a large number or an
     * indeterminate number of requests concurrently.
     *
     * @param ClientInterface $client   Client used to send the requests
     * @param array|\Iterator $requests Requests to send concurrently.
     * @param array           $options  Passes through the options available in
     *                                  {@see Pool::__construct}
     *
     * @return array Returns an array containing the response or an exception
     *               in the same order that the requests were sent.
     *
     * @throws \InvalidArgumentException if the event format is incorrect.
     */
    public static function batch(ClientInterface $client, $requests, array $options = []): array
    {
        $res = [];
        self::cmpCallback($options, 'fulfilled', $res);
        self::cmpCallback($options, 'rejected', $res);
        $pool = new static($client, $requests, $options);
        $pool->promise()->wait();
        \ksort($res);

        return $res;
    }

    /**
     * Execute callback(s)
     */
    private static function cmpCallback(array &$options, string $name, array &$results): void
    {
        if (!isset($options[$name])) {
            $options[$name] = static function ($v, $k) use (&$results) {
                $results[$k] = $v;
            };
        } else {
            $currentFn = $options[$name];
            $options[$name] = static function ($v, $k) use (&$results, $currentFn) {
                $currentFn($v, $k);
                $results[$k] = $v;
            };
        }
    }
}
