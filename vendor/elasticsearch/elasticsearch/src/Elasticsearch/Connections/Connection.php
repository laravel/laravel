<?php

namespace Elasticsearch\Connections;

use Elasticsearch\Common\Exceptions\AlreadyExpiredException;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Conflict409Exception;
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\Curl\CouldNotResolveHostException;
use Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException;
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Elasticsearch\Common\Exceptions\MaxRetriesException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\NoDocumentsToGetException;
use Elasticsearch\Common\Exceptions\NoShardAvailableException;
use Elasticsearch\Common\Exceptions\RequestTimeout408Exception;
use Elasticsearch\Common\Exceptions\RoutingMissingException;
use Elasticsearch\Common\Exceptions\ScriptLangNotSupportedException;
use Elasticsearch\Common\Exceptions\ServerErrorResponseException;
use Elasticsearch\Common\Exceptions\TransportException;
use Elasticsearch\Serializers\SerializerInterface;
use Elasticsearch\Transport;
use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Exception\ConnectException;
use GuzzleHttp\Ring\Exception\RingException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractConnection
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Connections
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Connection implements ConnectionInterface
{
    /** @var  callable */
    protected $handler;

    /** @var SerializerInterface */
    protected $serializer;

    /**
     * @var string
     */
    protected $transportSchema = 'http';    // TODO depreciate this default

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string || null
     */
    protected $path;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var LoggerInterface
     */
    protected $trace;

    /**
     * @var array
     */
    protected $connectionParams;

    /** @var bool  */
    protected $isAlive = false;

    /** @var float  */
    private $pingTimeout = 1;    //TODO expose this

    /** @var int  */
    private $lastPing = 0;

    /** @var int  */
    private $failedPings = 0;

    private $lastRequest = array();

    /**
     * Constructor
     *
     * @param $handler
     * @param array $hostDetails
     * @param array $connectionParams Array of connection-specific parameters
     * @param \Elasticsearch\Serializers\SerializerInterface $serializer
     * @param \Psr\Log\LoggerInterface $log              Logger object
     * @param \Psr\Log\LoggerInterface $trace
     */
    public function __construct($handler, $hostDetails, $connectionParams,
                                SerializerInterface $serializer, LoggerInterface $log, LoggerInterface $trace)
    {
        if (isset($hostDetails['port']) !== true) {
            $hostDetails['port'] = 9200;
        }

        if (isset($hostDetails['scheme'])) {
            $this->transportSchema = $hostDetails['scheme'];
        }

        if (isset($hostDetails['user']) && isset($hostDetails['pass'])) {
            $connectionParams['client']['curl'][CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $connectionParams['client']['curl'][CURLOPT_USERPWD] = $hostDetails['user'].':'.$hostDetails['pass'];
        }

        $host = $hostDetails['host'].':'.$hostDetails['port'];
        $path = null;
        if (isset($hostDetails['path']) === true) {
            $path = $hostDetails['path'];
        }
        $this->host             = $host;
        $this->path             = $path;
        $this->log              = $log;
        $this->trace            = $trace;
        $this->connectionParams = $connectionParams;
        $this->serializer       = $serializer;

        $this->handler = $this->wrapHandler($handler, $log, $trace);
    }

    /**
     * @param $method
     * @param $uri
     * @param null $params
     * @param null $body
     * @param array $options
     * @param \Elasticsearch\Transport $transport
     * @return mixed
     */
    public function performRequest($method, $uri, $params = null, $body = null, $options = [], Transport $transport = null)
    {
        if (isset($body) === true) {
            $body = $this->serializer->serialize($body);
        }

        $request = [
            'http_method' => $method,
            'scheme'      => $this->transportSchema,
            'uri'         => $this->getURI($uri, $params),
            'body'        => $body,
            'headers'     => [
                'host'  => [$this->host]
            ]

        ];
        $request = array_merge_recursive($request, $this->connectionParams, $options);


        $handler = $this->handler;
        $future = $handler($request, $this, $transport, $options);

        return $future;
    }

    /** @return string */
    public function getTransportSchema()
    {
        return $this->transportSchema;
    }

    /** @return array */
    public function getLastRequestInfo()
    {
        return $this->lastRequest;
    }

    private function wrapHandler(callable $handler, LoggerInterface $logger, LoggerInterface $tracer)
    {
        return function (array $request, Connection $connection, Transport $transport = null, $options) use ($handler, $logger, $tracer) {

            $this->lastRequest = [];
            $this->lastRequest['request'] = $request;

            // Send the request using the wrapped handler.
            $response =  Core::proxy($handler($request), function ($response) use ($connection, $transport, $logger, $tracer, $request, $options) {

                $this->lastRequest['response'] = $response;

                if (isset($response['error']) === true) {
                    if ($response['error'] instanceof ConnectException || $response['error'] instanceof RingException) {

                        $this->log->warning("Curl exception encountered.");

                        $exception = $this->getCurlRetryException($request, $response);

                        $this->logRequestFail(
                            $request['http_method'],
                            $response['effective_url'],
                            $request['body'],
                            $request['headers'],
                            $response['status'],
                            $response['body'],
                            $response['transfer_stats']['total_time'],
                            $exception
                        );

                        $node = $connection->getHost();
                        $this->log->warning("Marking node $node dead.");
                        $connection->markDead();

                        // If the transport has not been set, we are inside a Ping or Sniff,
                        // so we don't want to retrigger retries anyway.
                        //
                        // TODO this could be handled better, but we are limited because connectionpools do not
                        // have access to Transport.  Architecturally, all of this needs to be refactored
                        if (isset($transport) === true) {
                            $transport->connectionPool->scheduleCheck();

                            $neverRetry = isset($request['client']['never_retry']) ? $request['client']['never_retry'] : false;
                            $shouldRetry = $transport->shouldRetry($request);
                            $shouldRetryText = ($shouldRetry) ? 'true' : 'false';

                            $this->log->warning("Retries left? $shouldRetryText");
                            if ($shouldRetry && !$neverRetry) {
                                return $transport->performRequest(
                                    $request['http_method'],
                                    $request['uri'],
                                    [],
                                    $request['body'],
                                    $options
                                );
                            }
                        }

                        $this->log->warning("Out of retries, throwing exception from $node");
                        // Only throw if we run out of retries
                        throw $exception;
                    } else {
                        // Something went seriously wrong, bail
                        $exception = new TransportException($response['error']->getMessage());
                        $this->logRequestFail(
                            $request['http_method'],
                            $response['effective_url'],
                            $request['body'],
                            $request['headers'],
                            $response['status'],
                            $response['body'],
                            $response['transfer_stats']['total_time'],
                            $exception
                        );
                        throw $exception;
                    }
                } else {
                    $connection->markAlive();

                    if (isset($response['body']) === true) {
                        $response['body'] = stream_get_contents($response['body']);
                        $this->lastRequest['response']['body'] = $response['body'];
                    }

                    if ($response['status'] >= 400 && $response['status'] < 500) {
                        $ignore = isset($request['client']['ignore']) ? $request['client']['ignore'] : [];
                        $this->process4xxError($request, $response, $ignore);
                    } elseif ($response['status'] >= 500) {
                        $ignore = isset($request['client']['ignore']) ? $request['client']['ignore'] : [];
                        $this->process5xxError($request, $response, $ignore);
                    }

                    // No error, deserialize
                    $response['body'] = $this->serializer->deserialize($response['body'], $response['transfer_stats']);
                }
                $this->logRequestSuccess(
                    $request['http_method'],
                    $response['effective_url'],
                    $request['body'],
                    $request['headers'],
                    $response['status'],
                    $response['body'],
                    $response['transfer_stats']['total_time']
                );

                return isset($request['client']['verbose']) && $request['client']['verbose'] === true ? $response : $response['body'];

            });

            return $response;
        };
    }

    /**
     * @param string $uri
     * @param array $params
     *
     * @return string
     */
    private function getURI($uri, $params)
    {
        if (isset($params) === true && !empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        if ($this->path !== null) {
            $uri = $this->path . $uri;
        }

        return $uri;
    }

    /**
     * Log a successful request
     *
     * @param string $method
     * @param string $fullURI
     * @param string $body
     * @param array  $headers
     * @param string $statusCode
     * @param string $response
     * @param string $duration
     *
     * @return void
     */
    public function logRequestSuccess($method, $fullURI, $body, $headers, $statusCode, $response, $duration)
    {
        $this->log->debug('Request Body', array($body));
        $this->log->info(
            'Request Success:',
            array(
                'method'    => $method,
                'uri'       => $fullURI,
                'headers'   => $headers,
                'HTTP code' => $statusCode,
                'duration'  => $duration,
            )
        );
        $this->log->debug('Response', array($response));

        // Build the curl command for Trace.
        $curlCommand = $this->buildCurlCommand($method, $fullURI, $body);
        $this->trace->info($curlCommand);
        $this->trace->debug(
            'Response:',
            array(
                'response'  => $response,
                'method'    => $method,
                'uri'       => $fullURI,
                'HTTP code' => $statusCode,
                'duration'  => $duration,
            )
        );
    }

    /**
     * Log a a failed request
     *
     * @param string $method
     * @param string $fullURI
     * @param string $body
     * @param array $headers
     * @param null|string $statusCode
     * @param null|string $response
     * @param string $duration
     * @param \Exception|null $exception
     *
     * @return void
     */
    public function logRequestFail($method, $fullURI, $body, $headers, $statusCode, $response, $duration, \Exception $exception)
    {
        $this->log->debug('Request Body', array($body));
        $this->log->warning(
            'Request Failure:',
            array(
                'method'    => $method,
                'uri'       => $fullURI,
                'headers'   => $headers,
                'HTTP code' => $statusCode,
                'duration'  => $duration,
                'error'     => $exception->getMessage(),
            )
        );
        $this->log->warning('Response', array($response));

        // Build the curl command for Trace.
        $curlCommand = $this->buildCurlCommand($method, $fullURI, $body);
        $this->trace->info($curlCommand);
        $this->trace->debug(
            'Response:',
            array(
                'response'  => $response,
                'method'    => $method,
                'uri'       => $fullURI,
                'HTTP code' => $statusCode,
                'duration'  => $duration,
            )
        );
    }

    /**
     * @return bool
     */
    public function ping()
    {
        $options = [
            'client' => [
                'timeout' => $this->pingTimeout,
                'never_retry' => true,
                'verbose' => true
            ]
        ];
        try {
            $response = $this->performRequest('HEAD', '/', null, null, $options);
            $response = $response->wait();
        } catch (TransportException $exception) {
            $this->markDead();

            return false;
        }

        if ($response['status'] === 200) {
            $this->markAlive();

            return true;
        } else {
            $this->markDead();

            return false;
        }
    }

    /**
     * @return array
     */
    public function sniff()
    {
        $options = [
            'client' => [
                'timeout' => $this->pingTimeout,
                'never_retry' => true
            ]
        ];

        return $this->performRequest('GET', '/_nodes/_all/clear', null, null, $options);
    }

    /**
     * @return bool
     */
    public function isAlive()
    {
        return $this->isAlive;
    }

    public function markAlive()
    {
        $this->failedPings = 0;
        $this->isAlive = true;
        $this->lastPing = time();
    }

    public function markDead()
    {
        $this->isAlive = false;
        $this->failedPings += 1;
        $this->lastPing = time();
    }

    /**
     * @return int
     */
    public function getLastPing()
    {
        return $this->lastPing;
    }

    /**
     * @return int
     */
    public function getPingFailures()
    {
        return $this->failedPings;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $request
     * @param $response
     * @return \Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost|\Elasticsearch\Common\Exceptions\Curl\CouldNotResolveHostException|\Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException|\Elasticsearch\Common\Exceptions\MaxRetriesException
     */
    protected function getCurlRetryException($request, $response)
    {
        $exception = null;
        $message = $response['error']->getMessage();
        $exception = new MaxRetriesException($message);
        switch ($response['curl']['errno']) {
            case 6:
                $exception = new CouldNotResolveHostException($message, null, $exception);
                break;
            case 7:
                $exception = new CouldNotConnectToHost($message, null, $exception);
                break;
            case 28:
                $exception = new OperationTimeoutException($message, null, $exception);
                break;
        }

        return $exception;
    }

    /**
     * Construct a string cURL command
     *
     * @param string $method HTTP method
     * @param string $uri    Full URI of request
     * @param string $body   Request body
     *
     * @return string
     */
    private function buildCurlCommand($method, $uri, $body)
    {
        if (strpos($uri, '?') === false) {
            $uri .= '?pretty=true';
        } else {
            str_replace('?', '?pretty=true', $uri);
        }

        $curlCommand = 'curl -X' . strtoupper($method);
        $curlCommand .= " '" . $uri . "'";

        if (isset($body) === true && $body !== '') {
            $curlCommand .= " -d '" . $body . "'";
        }

        return $curlCommand;
    }

    /**
     * @param $request
     * @param $response
     * @param $ignore
     * @throws \Elasticsearch\Common\Exceptions\AlreadyExpiredException|\Elasticsearch\Common\Exceptions\BadRequest400Exception|\Elasticsearch\Common\Exceptions\Conflict409Exception|\Elasticsearch\Common\Exceptions\Forbidden403Exception|\Elasticsearch\Common\Exceptions\Missing404Exception|\Elasticsearch\Common\Exceptions\ScriptLangNotSupportedException|null
     */
    private function process4xxError($request, $response, $ignore)
    {
        $statusCode = $response['status'];
        $responseBody = $response['body'];

        /** @var \Exception $exception */
        $exception = $this->tryDeserialize400Error($response);

        if (array_search($response['status'], $ignore) !== false) {
            return;
        }

        if ($statusCode === 400 && strpos($responseBody, "AlreadyExpiredException") !== false) {
            $exception = new AlreadyExpiredException($responseBody, $statusCode);
        } elseif ($statusCode === 403) {
            $exception = new Forbidden403Exception($responseBody, $statusCode);
        } elseif ($statusCode === 404) {
            $exception = new Missing404Exception($responseBody, $statusCode);
        } elseif ($statusCode === 409) {
            $exception = new Conflict409Exception($responseBody, $statusCode);
        } elseif ($statusCode === 400 && strpos($responseBody, 'script_lang not supported') !== false) {
            $exception = new ScriptLangNotSupportedException($responseBody. $statusCode);
        } elseif ($statusCode === 408 ) {
            $exception = new RequestTimeout408Exception($responseBody, $statusCode);
        }

        $this->logRequestFail(
            $request['http_method'],
            $response['effective_url'],
            $request['body'],
            $request['headers'],
            $response['status'],
            $response['body'],
            $response['transfer_stats']['total_time'],
            $exception
        );

        throw $exception;
    }

    /**
     * @param $request
     * @param $response
     * @param $ignore
     * @throws \Elasticsearch\Common\Exceptions\NoDocumentsToGetException|\Elasticsearch\Common\Exceptions\NoShardAvailableException|\Elasticsearch\Common\Exceptions\RoutingMissingException|\Elasticsearch\Common\Exceptions\ServerErrorResponseException
     */
    private function process5xxError($request, $response, $ignore)
    {
        $statusCode = $response['status'];
        $responseBody = $response['body'];

        /** @var \Exception $exception */
        $exception = $this->tryDeserialize500Error($response);

        $exceptionText = "[$statusCode Server Exception] ".$exception->getMessage();
        $this->log->error($exceptionText);
        $this->log->error($exception->getTraceAsString());

        if (array_search($statusCode, $ignore) !== false) {
            return;
        }

        if ($statusCode === 500 && strpos($responseBody, "RoutingMissingException") !== false) {
            $exception = new RoutingMissingException($exception->getMessage(), $statusCode, $exception);
        } elseif ($statusCode === 500 && preg_match('/ActionRequestValidationException.+ no documents to get/', $responseBody) === 1) {
            $exception = new NoDocumentsToGetException($exception->getMessage(), $statusCode, $exception);
        } elseif ($statusCode === 500 && strpos($responseBody, 'NoShardAvailableActionException') !== false) {
            $exception = new NoShardAvailableException($exception->getMessage(), $statusCode, $exception);
        }

        $this->logRequestFail(
            $request['http_method'],
            $response['effective_url'],
            $request['body'],
            $request['headers'],
            $response['status'],
            $response['body'],
            $response['transfer_stats']['total_time'],
            $exception
        );

        throw $exception;
    }

    private function tryDeserialize400Error($response) {
        return $this->tryDeserializeError($response, 'Elasticsearch\Common\Exceptions\BadRequest400Exception');
    }

    private function tryDeserialize500Error($response) {
        return $this->tryDeserializeError($response, 'Elasticsearch\Common\Exceptions\ServerErrorResponseException');
    }

    private function tryDeserializeError($response, $errorClass) {
        $error = $this->serializer->deserialize($response['body'], $response['transfer_stats']);
        if (is_array($error) === true) {
            // 2.0 structured exceptions
            if (isset($error['error']['reason']) === true) {

                // Try to use root cause first (only grabs the first root cause)
                $root = $error['error']['root_cause'];
                if (isset($root) && isset($root[0])) {
                    $cause = $root[0]['reason'];
                    $type = $root[0]['type'];
                } else {
                    $cause = $error['error']['reason'];
                    $type = $error['error']['type'];
                }

                $original = new $errorClass($response['body'], $response['status']);

                return new $errorClass("$type: $cause", $response['status'], $original);

            } elseif (isset($error['error']) === true) {
                // <2.0 semi-structured exceptions
                $original = new $errorClass($response['body'], $response['status']);

                return new $errorClass($error['error'], $response['status'], $original);
            }

            // <2.0 "i just blew up" nonstructured exception
            // $error is an array but we don't know the format, reuse the response body instead
            return new $errorClass($response['body'], $response['status']);

        }

        // <2.0 "i just blew up" nonstructured exception
        return new $errorClass($error, $response['status']);
    }
}
