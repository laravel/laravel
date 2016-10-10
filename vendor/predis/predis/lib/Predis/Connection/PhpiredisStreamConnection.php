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
use Predis\ResponseQueued;
use Predis\Command\CommandInterface;

/**
 * This class provides the implementation of a Predis connection that uses PHP's
 * streams for network communication and wraps the phpiredis C extension (PHP
 * bindings for hiredis) to parse and serialize the Redis protocol. Everything
 * is highly experimental (even the very same phpiredis since it is quite new),
 * so use it at your own risk.
 *
 * This class is mainly intended to provide an optional low-overhead alternative
 * for processing replies from Redis compared to the standard pure-PHP classes.
 * Differences in speed when dealing with short inline replies are practically
 * nonexistent, the actual speed boost is for long multibulk replies when this
 * protocol processor can parse and return replies very fast.
 *
 * For instructions on how to build and install the phpiredis extension, please
 * consult the repository of the project.
 *
 * The connection parameters supported by this class are:
 *
 *  - scheme: it can be either 'tcp' or 'unix'.
 *  - host: hostname or IP address of the server.
 *  - port: TCP port of the server.
 *  - path: path of a UNIX domain socket when scheme is 'unix'.
 *  - timeout: timeout to perform the connection.
 *  - read_write_timeout: timeout of read / write operations.
 *  - async_connect: performs the connection asynchronously.
 *  - tcp_nodelay: enables or disables Nagle's algorithm for coalescing.
 *  - persistent: the connection is left intact after a GC collection.
 *
 * @link https://github.com/nrk/phpiredis
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PhpiredisStreamConnection extends StreamConnection
{
    private $reader;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionParametersInterface $parameters)
    {
        $this->checkExtensions();
        $this->initializeReader();

        parent::__construct($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        phpiredis_reader_destroy($this->reader);

        parent::__destruct();
    }

    /**
     * Checks if the phpiredis extension is loaded in PHP.
     */
    protected function checkExtensions()
    {
        if (!function_exists('phpiredis_reader_create')) {
            throw new NotSupportedException(
                'The phpiredis extension must be loaded in order to be able to use this connection class'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function checkParameters(ConnectionParametersInterface $parameters)
    {
        if (isset($parameters->iterable_multibulk)) {
            $this->onInvalidOption('iterable_multibulk', $parameters);
        }

        return parent::checkParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function tcpStreamInitializer(ConnectionParametersInterface $parameters)
    {
        $uri = "tcp://{$parameters->host}:{$parameters->port}";
        $flags = STREAM_CLIENT_CONNECT;
        $socket = null;

        if (isset($parameters->async_connect) && $parameters->async_connect) {
            $flags |= STREAM_CLIENT_ASYNC_CONNECT;
        }

        if (isset($parameters->persistent) && $parameters->persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
            $uri .= strpos($path = $parameters->path, '/') === 0 ? $path : "/$path";
        }

        $resource = @stream_socket_client($uri, $errno, $errstr, $parameters->timeout, $flags);

        if (!$resource) {
            $this->onConnectionError(trim($errstr), $errno);
        }

        if (isset($parameters->read_write_timeout) && function_exists('socket_import_stream')) {
            $rwtimeout = (float) $parameters->read_write_timeout;
            $rwtimeout = $rwtimeout > 0 ? $rwtimeout : -1;

            $timeout = array(
                'sec'  => $timeoutSeconds = floor($rwtimeout),
                'usec' => ($rwtimeout - $timeoutSeconds) * 1000000,
            );

            $socket = $socket ?: socket_import_stream($resource);
            @socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeout);
            @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);
        }

        if (isset($parameters->tcp_nodelay) && function_exists('socket_import_stream')) {
            $socket = $socket ?: socket_import_stream($resource);
            socket_set_option($socket, SOL_TCP, TCP_NODELAY, (int) $parameters->tcp_nodelay);
        }

        return $resource;
    }

    /**
     * Initializes the protocol reader resource.
     */
    protected function initializeReader()
    {
        $reader = phpiredis_reader_create();

        phpiredis_reader_set_status_handler($reader, $this->getStatusHandler());
        phpiredis_reader_set_error_handler($reader, $this->getErrorHandler());

        $this->reader = $reader;
    }

    /**
     * Gets the handler used by the protocol reader to handle status replies.
     *
     * @return \Closure
     */
    protected function getStatusHandler()
    {
        return function ($payload) {
            switch ($payload) {
                case 'OK':
                    return true;

                case 'QUEUED':
                    return new ResponseQueued();

                default:
                    return $payload;
            }
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
     * {@inheritdoc}
     */
    public function read()
    {
        $socket = $this->getResource();
        $reader = $this->reader;

        while (PHPIREDIS_READER_STATE_INCOMPLETE === $state = phpiredis_reader_get_state($reader)) {
            $buffer = stream_socket_recvfrom($socket, 4096);

            if ($buffer === false || $buffer === '') {
                $this->onConnectionError('Error while reading bytes from the server');
            }

            phpiredis_reader_feed($reader, $buffer);
        }

        if ($state === PHPIREDIS_READER_STATE_COMPLETE) {
            return phpiredis_reader_get_reply($reader);
        } else {
            $this->onProtocolError(phpiredis_reader_get_error($reader));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(CommandInterface $command)
    {
        $cmdargs = $command->getArguments();
        array_unshift($cmdargs, $command->getId());
        $this->writeBytes(phpiredis_format_command($cmdargs));
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_diff(parent::__sleep(), array('mbiterable'));
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        $this->checkExtensions();
        $this->initializeReader();
    }
}
