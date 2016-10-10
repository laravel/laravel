<?php
namespace GuzzleHttp\Tests\Ring\Client;

use GuzzleHttp\Ring\Client\StreamHandler;
use GuzzleHttp\Ring\Core;

/**
 * Class uses to control the test webserver.
 *
 * Queued responses will be served to requests using a FIFO order.  All requests
 * received by the server are stored on the node.js server and can be retrieved
 * by calling {@see Server::received()}.
 *
 * Mock responses that don't require data to be transmitted over HTTP a great
 * for testing.  Mock response, however, cannot test the actual sending of an
 * HTTP request using cURL.  This test server allows the simulation of any
 * number of HTTP request response transactions to test the actual sending of
 * requests over the wire without having to leave an internal network.
 */
class Server
{
    public static $started;
    public static $url = 'http://127.0.0.1:8125/';
    public static $host = '127.0.0.1:8125';
    public static $port = 8125;

    /**
     * Flush the received requests from the server
     * @throws \RuntimeException
     */
    public static function flush()
    {
        self::send('DELETE', '/guzzle-server/requests');
    }

    /**
     * Queue an array of responses or a single response on the server.
     *
     * Any currently queued responses will be overwritten. Subsequent requests
     * on the server will return queued responses in FIFO order.
     *
     * @param array $responses An array of responses. The shape of a response
     *                         is the shape described in the RingPHP spec.
     * @throws \Exception
     */
    public static function enqueue(array $responses)
    {
        $data = [];

        foreach ($responses as $response) {
            if (!is_array($response)) {
                throw new \Exception('Each response must be an array');
            }

            if (isset($response['body'])) {
                $response['body'] = base64_encode($response['body']);
            }

            $response += ['headers' => [], 'reason' => '', 'body' => ''];
            $data[] = $response;
        }

        self::send('PUT', '/guzzle-server/responses', json_encode($data));
    }

    /**
     * Get all of the received requests as a RingPHP request structure.
     *
     * @return array
     * @throws \RuntimeException
     */
    public static function received()
    {
        if (!self::$started) {
            return [];
        }

        $response = self::send('GET', '/guzzle-server/requests');
        $body = Core::body($response);
        $result = json_decode($body, true);
        if ($result === false) {
            throw new \RuntimeException('Error decoding response: '
                . json_last_error());
        }

        foreach ($result as &$res) {
            if (isset($res['uri'])) {
                $res['resource'] = $res['uri'];
            }
            if (isset($res['query_string'])) {
                $res['resource'] .= '?' . $res['query_string'];
            }
            if (!isset($res['resource'])) {
                $res['resource'] = '';
            }
            // Ensure that headers are all arrays
            if (isset($res['headers'])) {
                foreach ($res['headers'] as &$h) {
                    $h = (array) $h;
                }
                unset($h);
            }
        }

        unset($res);
        return $result;
    }

    /**
     * Stop running the node.js server
     */
    public static function stop()
    {
        if (self::$started) {
            self::send('DELETE', '/guzzle-server');
        }

        self::$started = false;
    }

    public static function wait($maxTries = 20)
    {
        $tries = 0;
        while (!self::isListening() && ++$tries < $maxTries) {
            usleep(100000);
        }

        if (!self::isListening()) {
            throw new \RuntimeException('Unable to contact node.js server');
        }
    }

    public static function start()
    {
        if (self::$started) {
            return;
        }

        try {
            self::wait();
        } catch (\Exception $e) {
            exec('node ' . __DIR__ . \DIRECTORY_SEPARATOR . 'server.js '
                . self::$port . ' >> /tmp/server.log 2>&1 &');
            self::wait();
        }

        self::$started = true;
    }

    private static function isListening()
    {
        $response = self::send('GET', '/guzzle-server/perf', null, [
            'connect_timeout' => 1,
            'timeout'         => 1
        ]);

        return !isset($response['error']);
    }

    private static function send(
        $method,
        $path,
        $body = null,
        array $client = []
    ) {
        $handler = new StreamHandler();

        $request = [
            'http_method'  => $method,
            'uri'          => $path,
            'request_port' => 8125,
            'headers'      => ['host' => ['127.0.0.1:8125']],
            'body'         => $body,
            'client'       => $client,
        ];

        if ($body) {
            $request['headers']['content-length'] = [strlen($body)];
        }

        return $handler($request);
    }
}
