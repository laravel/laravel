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
 * This class provides the implementation of a Predis connection that uses the
 * PHP socket extension for network communication and wraps the phpiredis C
 * extension (PHP bindings for hiredis) to parse the Redis protocol. Everything
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
 *
 * @link http://github.com/nrk/phpiredis
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PhpiredisConnection extends AbstractConnection
{
    const ERR_MSG_EXTENSION = 'The %s extension must be loaded in order to be able to use this connection class';

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
     * Disconnects from the server and destroys the underlying resource and the
     * protocol reader resource when PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        phpiredis_reader_destroy($this->reader);

        parent::__destruct();
    }

    /**
     * Checks if the socket and phpiredis extensions are loaded in PHP.
     */
    private function checkExtensions()
    {
        if (!function_exists('socket_create')) {
            throw new NotSupportedException(sprintf(self::ERR_MSG_EXTENSION, 'socket'));
        }
        if (!function_exists('phpiredis_reader_create')) {
            throw new NotSupportedException(sprintf(self::ERR_MSG_EXTENSION, 'phpiredis'));
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
        if (isset($parameters->persistent)) {
            $this->onInvalidOption('persistent', $parameters);
        }

        return parent::checkParameters($parameters);
    }

    /**
     * Initializes the protocol reader resource.
     */
    private function initializeReader()
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
    private function getStatusHandler()
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
    private function getErrorHandler()
    {
        return function ($errorMessage) {
            return new ResponseError($errorMessage);
        };
    }

    /**
     * Helper method used to throw exceptions on socket errors.
     */
    private function emitSocketError()
    {
        $errno  = socket_last_error();
        $errstr = socket_strerror($errno);

        $this->disconnect();

        $this->onConnectionError(trim($errstr), $errno);
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource()
    {
        $parameters = $this->parameters;

        $isUnix = $this->parameters->scheme === 'unix';
        $domain = $isUnix ? AF_UNIX : AF_INET;
        $protocol = $isUnix ? 0 : SOL_TCP;

        $socket = @call_user_func('socket_create', $domain, SOCK_STREAM, $protocol);
        if (!is_resource($socket)) {
            $this->emitSocketError();
        }

        $this->setSocketOptions($socket, $parameters);

        return $socket;
    }

    /**
     * Sets options on the socket resource from the connection parameters.
     *
     * @param resource                      $socket     Socket resource.
     * @param ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     */
    private function setSocketOptions($socket, ConnectionParametersInterface $parameters)
    {
        if ($parameters->scheme !== 'tcp') {
            return;
        }

        if (!socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1)) {
            $this->emitSocketError();
        }

        if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            $this->emitSocketError();
        }

        if (isset($parameters->read_write_timeout)) {
            $rwtimeout = $parameters->read_write_timeout;
            $timeoutSec = floor($rwtimeout);
            $timeoutUsec = ($rwtimeout - $timeoutSec) * 1000000;

            $timeout = array(
                'sec' => $timeoutSec,
                'usec' => $timeoutUsec,
            );

            if (!socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeout)) {
                $this->emitSocketError();
            }

            if (!socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout)) {
                $this->emitSocketError();
            }
        }
    }

    /**
     * Gets the address from the connection parameters.
     *
     * @param  ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     * @return string
     */
    protected static function getAddress(ConnectionParametersInterface $parameters)
    {
        if ($parameters->scheme === 'unix') {
            return $parameters->path;
        }

        $host = $parameters->host;

        if (ip2long($host) === false) {
            if (false === $addresses = gethostbynamel($host)) {
                return false;
            }

            return $addresses[array_rand($addresses)];
        }

        return $host;
    }

    /**
     * Opens the actual connection to the server with a timeout.
     *
     * @param  ConnectionParametersInterface $parameters Parameters used to initialize the connection.
     * @return string
     */
    private function connectWithTimeout(ConnectionParametersInterface $parameters)
    {
        if (false === $host = self::getAddress($parameters)) {
            $this->onConnectionError("Cannot resolve the address of '$parameters->host'.");
        }

        $socket = $this->getResource();

        socket_set_nonblock($socket);

        if (@socket_connect($socket, $host, $parameters->port) === false) {
            $error = socket_last_error();
            if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY) {
                $this->emitSocketError();
            }
        }

        socket_set_block($socket);

        $null = null;
        $selectable = array($socket);

        $timeout = $parameters->timeout;
        $timeoutSecs = floor($timeout);
        $timeoutUSecs = ($timeout - $timeoutSecs) * 1000000;

        $selected = socket_select($selectable, $selectable, $null, $timeoutSecs, $timeoutUSecs);

        if ($selected === 2) {
            $this->onConnectionError('Connection refused', SOCKET_ECONNREFUSED);
        }
        if ($selected === 0) {
            $this->onConnectionError('Connection timed out', SOCKET_ETIMEDOUT);
        }
        if ($selected === false) {
            $this->emitSocketError();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        parent::connect();

        $this->connectWithTimeout($this->parameters);

        if ($this->initCmds) {
            $this->sendInitializationCommands();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            socket_close($this->getResource());
            parent::disconnect();
        }
    }

    /**
     * Sends the initialization commands to Redis when the connection is opened.
     */
    private function sendInitializationCommands()
    {
        foreach ($this->initCmds as $command) {
            $this->writeCommand($command);
        }
        foreach ($this->initCmds as $command) {
            $this->readResponse($command);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function write($buffer)
    {
        $socket = $this->getResource();

        while (($length = strlen($buffer)) > 0) {
            $written = socket_write($socket, $buffer, $length);

            if ($length === $written) {
                return;
            }
            if ($written === false) {
                $this->onConnectionError('Error while writing bytes to the server');
            }

            $buffer = substr($buffer, $written);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $socket = $this->getResource();
        $reader = $this->reader;

        while (($state = phpiredis_reader_get_state($reader)) === PHPIREDIS_READER_STATE_INCOMPLETE) {
            if (@socket_recv($socket, $buffer, 4096, 0) === false || $buffer === '') {
                $this->emitSocketError();
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
        $this->write(phpiredis_format_command($cmdargs));
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
