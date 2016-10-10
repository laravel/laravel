<?php

namespace Elasticsearch;

use Elasticsearch\Common\Exceptions\InvalidArgumentException;
use Elasticsearch\Common\Exceptions\RuntimeException;
use Elasticsearch\ConnectionPool\AbstractConnectionPool;
use Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Elasticsearch\ConnectionPool\StaticNoPingConnectionPool;
use Elasticsearch\Connections\Connection;
use Elasticsearch\Connections\ConnectionFactory;
use Elasticsearch\Connections\ConnectionFactoryInterface;
use Elasticsearch\Serializers\SerializerInterface;
use Elasticsearch\ConnectionPool\Selectors;
use Elasticsearch\Serializers\SmartSerializer;
use GuzzleHttp\Ring\Client\CurlHandler;
use GuzzleHttp\Ring\Client\CurlMultiHandler;
use GuzzleHttp\Ring\Client\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Class ClientBuilder
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
  */
class ClientBuilder
{
    /** @var Transport */
    private $transport;

    /** @var callback */
    private $endpoint;

    /** @var  ConnectionFactoryInterface */
    private $connectionFactory;

    private $handler;

    /** @var  LoggerInterface */
    private $logger;

    /** @var  LoggerInterface */
    private $tracer;

    /** @var string */
    private $connectionPool = '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool';

    /** @var  string */
    private $serializer = '\Elasticsearch\Serializers\SmartSerializer';

    /** @var  string */
    private $selector = '\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector';

    /** @var  array */
    private $connectionPoolArgs = [
        'randomizeHosts' => true
    ];

    /** @var array */
    private $hosts;

    /** @var  int */
    private $retries;

    /** @var bool */
    private $sniffOnStart = false;

    /** @var null|array  */
    private $sslCert = null;

    /** @var null|array  */
    private $sslKey = null;

    /** @var null|bool|string */
    private $sslVerification = null;

    /**
     * @return ClientBuilder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Build a new client from the provided config.  Hash keys
     * should correspond to the method name e.g. ['connectionPool']
     * corresponds to setConnectionPool().
     *
     * Missing keys will use the default for that setting if applicable
     *
     * Unknown keys will throw an exception by default, but this can be silenced
     * by setting `quiet` to true
     *
     * @param array $config hash of settings
     * @param bool $quiet False if unknown settings throw exception, true to silently
     *                    ignore unknown settings
     * @throws Common\Exceptions\RuntimeException
     * @return \Elasticsearch\Client
     */
    public static function fromConfig($config, $quiet = false) {
        $builder = new self;
        foreach ($config as $key => $value) {
            $method = "set$key";
            if (method_exists($builder, $method)) {
                $builder->$method($value);
                unset($config[$key]);
            }
        }

        if ($quiet === false && count($config) > 0) {
            $unknown = implode(array_keys($config));
            throw new RuntimeException("Unknown parameters provided: $unknown");
        }
        return $builder->build();
    }

    /**
     * @param array $singleParams
     * @param array $multiParams
     * @throws \RuntimeException
     * @return callable
     */
    public static function defaultHandler($multiParams = [], $singleParams = [])
    {
        $future = null;
        if (extension_loaded('curl')) {
            $config = array_merge([ 'mh' => curl_multi_init() ], $multiParams);
            if (function_exists('curl_reset')) {
                $default = new CurlHandler($singleParams);
                $future = new CurlMultiHandler($config);
            } else {
                $default = new CurlMultiHandler($config);
            }
        } else {
            throw new \RuntimeException('Elasticsearch-PHP requires cURL, or a custom HTTP handler.');
        }

        return $future ? Middleware::wrapFuture($default, $future) : $default;
    }

    /**
     * @param array $params
     * @throws \RuntimeException
     * @return CurlMultiHandler
     */
    public static function multiHandler($params = [])
    {
        if (function_exists('curl_multi_init')) {
            return new CurlMultiHandler(array_merge([ 'mh' => curl_multi_init() ], $params));
        } else {
            throw new \RuntimeException('CurlMulti handler requires cURL.');
        }
    }

    /**
     * @return CurlHandler
     * @throws \RuntimeException
     */
    public static function singleHandler()
    {
        if (function_exists('curl_reset')) {
            return new CurlHandler();
        } else {
            throw new \RuntimeException('CurlSingle handler requires cURL.');
        }
    }

    /**
     * @param $path string
     * @return \Monolog\Logger\Logger
     */
    public static function defaultLogger($path, $level = Logger::WARNING)
    {
        $log       = new Logger('log');
        $handler   = new StreamHandler($path, $level);
        $log->pushHandler($handler);

        return $log;
    }

    /**
     * @param \Elasticsearch\Connections\ConnectionFactoryInterface $connectionFactory
     * @return $this
     */
    public function setConnectionFactory(ConnectionFactoryInterface $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;

        return $this;
    }

    /**
     * @param \Elasticsearch\ConnectionPool\AbstractConnectionPool|string $connectionPool
     * @param array $args
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setConnectionPool($connectionPool, array $args = [])
    {
        if (is_string($connectionPool)) {
            $this->connectionPool = $connectionPool;
            $this->connectionPoolArgs = $args;
        } elseif (is_object($connectionPool)) {
            $this->connectionPool = $connectionPool;
        } else {
            throw new InvalidArgumentException("Serializer must be a class path or instantiated object extending AbstractConnectionPool");
        }

        return $this;
    }

    /**
     * @param callable $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param \Elasticsearch\Transport $transport
     * @return $this
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @param mixed $handler
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $tracer
     * @return $this
     */
    public function setTracer($tracer)
    {
        $this->tracer = $tracer;

        return $this;
    }

    /**
     * @param \Elasticsearch\Serializers\SerializerInterface|string $serializer
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setSerializer($serializer)
    {
        $this->parseStringOrObject($serializer, $this->serializer, 'SerializerInterface');

        return $this;
    }

    /**
     * @param array $hosts
     * @return $this
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * @param int $retries
     * @return $this
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * @param \Elasticsearch\ConnectionPool\Selectors\SelectorInterface|string $selector
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setSelector($selector)
    {
        $this->parseStringOrObject($selector, $this->selector, 'SelectorInterface');

        return $this;
    }

    /**
     * @param boolean $sniffOnStart
     * @return $this
     */
    public function setSniffOnStart($sniffOnStart)
    {
        $this->sniffOnStart = $sniffOnStart;

        return $this;
    }

    /**
     * @param $cert
     * @param null|string $password
     * @return $this
     */
    public function setSSLCert($cert, $password = null)
    {
        $this->sslCert = [$cert, $password];

        return $this;
    }

    /**
     * @param $key
     * @param null|string $password
     * @return $this
     */
    public function setSSLKey($key, $password = null)
    {
        $this->sslKey = [$key, $password];

        return $this;
    }

    /**
     * @param bool|string $value
     * @return $this
     */
    public function setSSLVerification($value = true)
    {
        $this->sslVerification = $value;

        return $this;
    }

    /**
     * @return Client
     */
    public function build()
    {
        $this->buildLoggers();

        if (is_null($this->handler)) {
            $this->handler = ClientBuilder::defaultHandler();
        }

        $sslOptions = null;
        if (isset($this->sslKey)) {
            $sslOptions['ssl_key'] = $this->sslKey;
        }
        if (isset($this->sslCert)) {
            $sslOptions['cert'] = $this->sslCert;
        }
        if (isset($this->sslVerification)) {
            $sslOptions['verify'] = $this->sslVerification;
        }

        if (!is_null($sslOptions)) {
            $sslHandler = function (callable $handler, array $sslOptions) {
                return function (array $request) use ($handler, $sslOptions) {
                    // Add our custom headers
                    foreach ($sslOptions as $key => $value) {
                        $request['client'][$key] = $value;
                    }

                    // Send the request using the handler and return the response.
                    return $handler($request);
                };
            };
            $this->handler = $sslHandler($this->handler, $sslOptions);
        }

        if (is_null($this->serializer)) {
            $this->serializer = new SmartSerializer();
        } elseif (is_string($this->serializer)) {
            $this->serializer = new $this->serializer;
        }

        if (is_null($this->connectionFactory)) {
            $connectionParams = [];
            $this->connectionFactory = new ConnectionFactory($this->handler, $connectionParams, $this->serializer, $this->logger, $this->tracer);
        }

        if (is_null($this->hosts)) {
            $this->hosts = $this->getDefaultHost();
        }

        if (is_null($this->selector)) {
            $this->selector = new Selectors\RoundRobinSelector();
        } elseif (is_string($this->selector)) {
            $this->selector = new $this->selector;
        }

        $this->buildTransport();

        if (is_null($this->endpoint)) {
            $transport = $this->transport;
            $serializer = $this->serializer;

            $this->endpoint = function ($class) use ($transport, $serializer) {
                $fullPath = '\\Elasticsearch\\Endpoints\\' . $class;
                if ($class === 'Bulk' || $class === 'Msearch' || $class === 'MPercolate') {
                    return new $fullPath($transport, $serializer);
                } else {
                    return new $fullPath($transport);
                }
            };
        }

        return new Client($this->transport, $this->endpoint);
    }

    private function buildLoggers()
    {
        if (is_null($this->logger)) {
            $this->logger = new NullLogger();
        }

        if (is_null($this->tracer)) {
            $this->tracer = new NullLogger();
        }
    }

    private function buildTransport()
    {
        $connections = $this->buildConnectionsFromHosts($this->hosts);

        if (is_string($this->connectionPool)) {
            $this->connectionPool = new $this->connectionPool(
                $connections,
                $this->selector,
                $this->connectionFactory,
                $this->connectionPoolArgs);
        } elseif (is_null($this->connectionPool)) {
            $this->connectionPool = new StaticNoPingConnectionPool(
                $connections,
                $this->selector,
                $this->connectionFactory,
                $this->connectionPoolArgs);
        }

        if (is_null($this->retries)) {
            $this->retries = count($connections);
        }

        if (is_null($this->transport)) {
            $this->transport = new Transport($this->retries, $this->sniffOnStart, $this->connectionPool, $this->logger);
        }
    }

    private function parseStringOrObject($arg, &$destination, $interface)
    {
        if (is_string($arg)) {
            $destination = new $arg;
        } elseif (is_object($arg)) {
            $destination = $arg;
        } else {
            throw new InvalidArgumentException("Serializer must be a class path or instantiated object implementing $interface");
        }
    }

    /**
     * @return array
     */
    private function getDefaultHost()
    {
        return ['localhost:9200'];
    }

    /**
     * @param array $hosts
     *
     * @throws \InvalidArgumentException
     * @return \Elasticsearch\Connections\Connection[]
     */
    private function buildConnectionsFromHosts($hosts)
    {
        if (is_array($hosts) === false) {
            throw new InvalidArgumentException('Hosts parameter must be an array of strings');
        }

        $connections = [];
        foreach ($hosts as $host) {
            $host = $this->prependMissingScheme($host);
            $host = $this->extractURIParts($host);
            $connections[] = $this->connectionFactory->create($host);
        }

        return $connections;
    }

    /**
     * @param array $host
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    private function extractURIParts($host)
    {
        $parts = parse_url($host);

        if ($parts === false) {
            throw new InvalidArgumentException("Could not parse URI");
        }

        if (isset($parts['port']) !== true) {
            $parts['port'] = 9200;
        }

        return $parts;
    }

    /**
     * @param string $host
     *
     * @return string
     */
    private function prependMissingScheme($host)
    {
        if (!filter_var($host, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            $host = 'http://' . $host;
        }

        return $host;
    }
}
