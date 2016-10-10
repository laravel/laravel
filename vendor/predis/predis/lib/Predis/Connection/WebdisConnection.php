<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use Predis\NotSupportedException;
use Predis\ResponseError;
use Predis\Command\CommandInterface;
use Predis\Protocol\ProtocolException;

/**
 * This class implements a Predis connection that actually talks with Webdis
 * instead of connecting directly to Redis. It relies on the cURL extension to
 * communicate with the web server and the phpiredis extension to parse the
 * protocol of the replies returned in the http response bodies.
 *
 * Some features are not yet available or they simply cannot be implemented:
 *   - Pipelining commands.
 *   - Publish / Subscribe.
 *   - MULTI / EXEC transactions (not yet supported by Webdis).
 *
 * The connection parameters supported by this class are:
 *
 *  - scheme: must be 'http'.
 *  - host: hostname or IP address of the server.
 *  - port: TCP port of the server.
 *  - timeout: timeout to perform the connection.
 *  - user: username for authentication.
 *  - pass: password for authentication.
 *
 * @link http://webd.is
 * @link http://github.com/nicolasff/webdis
 * @link http://github.com/seppo0010/phpiredis
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class WebdisConnection implements SingleConnectionInterface
{
    const ERR_MSG_EXTENSION = 'The %s extension must be loaded in order to be able to use this connection class';

    private $parameters;
    private $resource;
    private $reader;

    /**
     * @param ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     */
    public function __construct(ConnectionParametersInterface $parameters)
    {
        $this->checkExtensions();

        if ($parameters->scheme !== 'http') {
            throw new \InvalidArgumentException("Invalid scheme: {$parameters->scheme}");
        }

        $this->parameters = $parameters;
        $this->resource = $this->initializeCurl($parameters);
        $this->reader = $this->initializeReader($parameters);
    }

    /**
     * Frees the underlying cURL and protocol reader resources when PHP's
     * garbage collector kicks in.
     */
    public function __destruct()
    {
        curl_close($this->resource);
        phpiredis_reader_destroy($this->reader);
    }

    /**
     * Helper method used to throw on unsupported methods.
     */
    private function throwNotSupportedException($function)
    {
        $class = __CLASS__;
        throw new NotSupportedException("The method $class::$function() is not supported");
    }

    /**
     * Checks if the cURL and phpiredis extensions are loaded in PHP.
     */
    private function checkExtensions()
    {
        if (!function_exists('curl_init')) {
            throw new NotSupportedException(sprintf(self::ERR_MSG_EXTENSION, 'curl'));
        }

        if (!function_exists('phpiredis_reader_create')) {
            throw new NotSupportedException(sprintf(self::ERR_MSG_EXTENSION, 'phpiredis'));
        }
    }

    /**
     * Initializes cURL.
     *
     * @param  ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function initializeCurl(ConnectionParametersInterface $parameters)
    {
        $options = array(
            CURLOPT_FAILONERROR => true,
            CURLOPT_CONNECTTIMEOUT_MS => $parameters->timeout * 1000,
            CURLOPT_URL => "{$parameters->scheme}://{$parameters->host}:{$parameters->port}",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_WRITEFUNCTION => array($this, 'feedReader'),
        );

        if (isset($parameters->user, $parameters->pass)) {
            $options[CURLOPT_USERPWD] = "{$parameters->user}:{$parameters->pass}";
        }

        curl_setopt_array($resource = curl_init(), $options);

        return $resource;
    }

    /**
     * Initializes phpiredis' protocol reader.
     *
     * @param  ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function initializeReader(ConnectionParametersInterface $parameters)
    {
        $reader = phpiredis_reader_create();

        phpiredis_reader_set_status_handler($reader, $this->getStatusHandler());
        phpiredis_reader_set_error_handler($reader, $this->getErrorHandler());

        return $reader;
    }

    /**
     * Gets the handler used by the protocol reader to handle status replies.
     *
     * @return \Closure
     */
    protected function getStatusHandler()
    {
        return function ($payload) {
            return $payload === 'OK' ? true : $payload;
        };
    }

    /**
     * Gets the handler used by the protocol reader to handle Redis errors.
     *
     * @return \Closure
     */
    protected function getErrorHandler()
    {
        return function ($errorMessage) {
            return new ResponseError($errorMessage);
        };
    }

    /**
     * Feeds phpredis' reader resource with the data read from the network.
     *
     * @param  resource $resource Reader resource.
     * @param  string   $buffer   Buffer with the reply read from the network.
     * @return int
     */
    protected function feedReader($resource, $buffer)
    {
        phpiredis_reader_feed($this->reader, $buffer);

        return strlen($buffer);
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return true;
    }

    /**
     * Checks if the specified command is supported by this connection class.
     *
     * @param  CommandInterface $command The instance of a Redis command.
     * @return string
     */
    protected function getCommandId(CommandInterface $command)
    {
        switch (($commandId = $command->getId())) {
            case 'AUTH':
            case 'SELECT':
            case 'MULTI':
            case 'EXEC':
            case 'WATCH':
            case 'UNWATCH':
            case 'DISCARD':
            case 'MONITOR':
                throw new NotSupportedException("Disabled command: {$command->getId()}");

            default:
                return $commandId;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(CommandInterface $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(CommandInterface $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(CommandInterface $command)
    {
        $resource = $this->resource;
        $commandId = $this->getCommandId($command);

        if ($arguments = $command->getArguments()) {
            $arguments = implode('/', array_map('urlencode', $arguments));
            $serializedCommand = "$commandId/$arguments.raw";
        } else {
            $serializedCommand = "$commandId.raw";
        }

        curl_setopt($resource, CURLOPT_POSTFIELDS, $serializedCommand);

        if (curl_exec($resource) === false) {
            $error = curl_error($resource);
            $errno = curl_errno($resource);
            throw new ConnectionException($this, trim($error), $errno);
        }

        if (phpiredis_reader_get_state($this->reader) !== PHPIREDIS_READER_STATE_COMPLETE) {
            throw new ProtocolException($this, phpiredis_reader_get_error($this->reader));
        }

        return phpiredis_reader_get_reply($this->reader);
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function pushInitCommand(CommandInterface $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "{$this->parameters->host}:{$this->parameters->port}";
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array('parameters');
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        $this->checkExtensions();
        $parameters = $this->getParameters();

        $this->resource = $this->initializeCurl($parameters);
        $this->reader = $this->initializeReader($parameters);
    }
}
