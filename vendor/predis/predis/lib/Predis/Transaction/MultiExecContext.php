<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Transaction;

use SplQueue;
use Predis\BasicClientInterface;
use Predis\ClientException;
use Predis\ClientInterface;
use Predis\CommunicationException;
use Predis\ExecutableContextInterface;
use Predis\NotSupportedException;
use Predis\ResponseErrorInterface;
use Predis\ResponseQueued;
use Predis\ServerException;
use Predis\Command\CommandInterface;
use Predis\Connection\AggregatedConnectionInterface;
use Predis\Protocol\ProtocolException;

/**
 * Client-side abstraction of a Redis transaction based on MULTI / EXEC.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiExecContext implements BasicClientInterface, ExecutableContextInterface
{
    const STATE_RESET       = 0;    // 0b00000
    const STATE_INITIALIZED = 1;    // 0b00001
    const STATE_INSIDEBLOCK = 2;    // 0b00010
    const STATE_DISCARDED   = 4;    // 0b00100
    const STATE_CAS         = 8;    // 0b01000
    const STATE_WATCH       = 16;   // 0b10000

    private $state;
    private $canWatch;

    protected $client;
    protected $options;
    protected $commands;

    /**
     * @param ClientInterface $client  Client instance used by the context.
     * @param array           $options Options for the context initialization.
     */
    public function __construct(ClientInterface $client, Array $options = null)
    {
        $this->checkCapabilities($client);
        $this->options = $options ?: array();
        $this->client = $client;
        $this->reset();
    }

    /**
     * Sets the internal state flags.
     *
     * @param int $flags Set of flags
     */
    protected function setState($flags)
    {
        $this->state = $flags;
    }

    /**
     * Gets the internal state flags.
     *
     * @return int
     */
    protected function getState()
    {
        return $this->state;
    }

    /**
     * Sets one or more flags.
     *
     * @param int $flags Set of flags
     */
    protected function flagState($flags)
    {
        $this->state |= $flags;
    }

    /**
     * Resets one or more flags.
     *
     * @param int $flags Set of flags
     */
    protected function unflagState($flags)
    {
        $this->state &= ~$flags;
    }

    /**
     * Checks is a flag is set.
     *
     * @param  int  $flags Flag
     * @return bool
     */
    protected function checkState($flags)
    {
        return ($this->state & $flags) === $flags;
    }

    /**
     * Checks if the passed client instance satisfies the required conditions
     * needed to initialize a transaction context.
     *
     * @param ClientInterface $client Client instance used by the context.
     */
    private function checkCapabilities(ClientInterface $client)
    {
        if ($client->getConnection() instanceof AggregatedConnectionInterface) {
            throw new NotSupportedException('Cannot initialize a MULTI/EXEC context when using aggregated connections');
        }

        $profile = $client->getProfile();

        if ($profile->supportsCommands(array('MULTI', 'EXEC', 'DISCARD')) === false) {
            throw new NotSupportedException('The current profile does not support MULTI, EXEC and DISCARD');
        }

        $this->canWatch = $profile->supportsCommands(array('WATCH', 'UNWATCH'));
    }

    /**
     * Checks if WATCH and UNWATCH are supported by the server profile.
     */
    private function isWatchSupported()
    {
        if ($this->canWatch === false) {
            throw new NotSupportedException('The current profile does not support WATCH and UNWATCH');
        }
    }

    /**
     * Resets the state of a transaction.
     */
    protected function reset()
    {
        $this->setState(self::STATE_RESET);
        $this->commands = new SplQueue();
    }

    /**
     * Initializes a new transaction.
     */
    protected function initialize()
    {
        if ($this->checkState(self::STATE_INITIALIZED)) {
            return;
        }

        $options = $this->options;

        if (isset($options['cas']) && $options['cas']) {
            $this->flagState(self::STATE_CAS);
        }
        if (isset($options['watch'])) {
            $this->watch($options['watch']);
        }

        $cas = $this->checkState(self::STATE_CAS);
        $discarded = $this->checkState(self::STATE_DISCARDED);

        if (!$cas || ($cas && $discarded)) {
            $this->client->multi();

            if ($discarded) {
                $this->unflagState(self::STATE_CAS);
            }
        }

        $this->unflagState(self::STATE_DISCARDED);
        $this->flagState(self::STATE_INITIALIZED);
    }

    /**
     * Dynamically invokes a Redis command with the specified arguments.
     *
     * @param  string $method    Command ID.
     * @param  array  $arguments Arguments for the command.
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $command = $this->client->createCommand($method, $arguments);
        $response = $this->executeCommand($command);

        return $response;
    }

    /**
     * Executes the specified Redis command.
     *
     * @param  CommandInterface $command Command instance.
     * @return $this|mixed
     */
    public function executeCommand(CommandInterface $command)
    {
        $this->initialize();

        if ($this->checkState(self::STATE_CAS)) {
            return $this->client->executeCommand($command);
        }

        $response = $this->client->getConnection()->executeCommand($command);

        if ($response instanceof ResponseQueued) {
            $this->commands->enqueue($command);
        } elseif ($response instanceof ResponseErrorInterface) {
            throw new AbortedMultiExecException($this, $response->getMessage());
        } else {
            $this->onProtocolError('The server did not return a +QUEUED status response.');
        }

        return $this;
    }

    /**
     * Executes WATCH on one or more keys.
     *
     * @param  string|array $keys One or more keys.
     * @return mixed
     */
    public function watch($keys)
    {
        $this->isWatchSupported();

        if ($this->checkState(self::STATE_INITIALIZED) && !$this->checkState(self::STATE_CAS)) {
            throw new ClientException('WATCH after MULTI is not allowed');
        }

        $reply = $this->client->watch($keys);
        $this->flagState(self::STATE_WATCH);

        return $reply;
    }

    /**
     * Finalizes the transaction on the server by executing MULTI on the server.
     *
     * @return MultiExecContext
     */
    public function multi()
    {
        if ($this->checkState(self::STATE_INITIALIZED | self::STATE_CAS)) {
            $this->unflagState(self::STATE_CAS);
            $this->client->multi();
        } else {
            $this->initialize();
        }

        return $this;
    }

    /**
     * Executes UNWATCH.
     *
     * @return MultiExecContext
     */
    public function unwatch()
    {
        $this->isWatchSupported();
        $this->unflagState(self::STATE_WATCH);
        $this->__call('unwatch', array());

        return $this;
    }

    /**
     * Resets a transaction by UNWATCHing the keys that are being WATCHed and
     * DISCARDing the pending commands that have been already sent to the server.
     *
     * @return MultiExecContext
     */
    public function discard()
    {
        if ($this->checkState(self::STATE_INITIALIZED)) {
            $command = $this->checkState(self::STATE_CAS) ? 'unwatch' : 'discard';
            $this->client->$command();
            $this->reset();
            $this->flagState(self::STATE_DISCARDED);
        }

        return $this;
    }

    /**
     * Executes the whole transaction.
     *
     * @return mixed
     */
    public function exec()
    {
        return $this->execute();
    }

    /**
     * Checks the state of the transaction before execution.
     *
     * @param mixed $callable Callback for execution.
     */
    private function checkBeforeExecution($callable)
    {
        if ($this->checkState(self::STATE_INSIDEBLOCK)) {
            throw new ClientException("Cannot invoke 'execute' or 'exec' inside an active client transaction block");
        }

        if ($callable) {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException('Argument passed must be a callable object');
            }

            if (!$this->commands->isEmpty()) {
                $this->discard();
                throw new ClientException('Cannot execute a transaction block after using fluent interface');
            }
        }

        if (isset($this->options['retry']) && !isset($callable)) {
            $this->discard();
            throw new \InvalidArgumentException('Automatic retries can be used only when a transaction block is provided');
        }
    }

    /**
     * Handles the actual execution of the whole transaction.
     *
     * @param  mixed $callable Optional callback for execution.
     * @return array
     */
    public function execute($callable = null)
    {
        $this->checkBeforeExecution($callable);

        $reply = null;
        $values = array();
        $attempts = isset($this->options['retry']) ? (int) $this->options['retry'] : 0;

        do {
            if ($callable !== null) {
                $this->executeTransactionBlock($callable);
            }

            if ($this->commands->isEmpty()) {
                if ($this->checkState(self::STATE_WATCH)) {
                    $this->discard();
                }

                return null;
            }

            $reply = $this->client->exec();

            if ($reply === null) {
                if ($attempts === 0) {
                    $message = 'The current transaction has been aborted by the server';
                    throw new AbortedMultiExecException($this, $message);
                }

                $this->reset();

                if (isset($this->options['on_retry']) && is_callable($this->options['on_retry'])) {
                    call_user_func($this->options['on_retry'], $this, $attempts);
                }

                continue;
            }

            break;
        } while ($attempts-- > 0);

        $exec = $reply instanceof \Iterator ? iterator_to_array($reply) : $reply;
        $commands = $this->commands;

        $size = count($exec);
        if ($size !== count($commands)) {
            $this->onProtocolError("EXEC returned an unexpected number of replies");
        }

        $clientOpts = $this->client->getOptions();
        $useExceptions = isset($clientOpts->exceptions) ? $clientOpts->exceptions : true;

        for ($i = 0; $i < $size; $i++) {
            $commandReply = $exec[$i];

            if ($commandReply instanceof ResponseErrorInterface && $useExceptions) {
                $message = $commandReply->getMessage();
                throw new ServerException($message);
            }

            if ($commandReply instanceof \Iterator) {
                $commandReply = iterator_to_array($commandReply);
            }

            $values[$i] = $commands->dequeue()->parseResponse($commandReply);
        }

        return $values;
    }

    /**
     * Passes the current transaction context to a callable block for execution.
     *
     * @param mixed $callable Callback.
     */
    protected function executeTransactionBlock($callable)
    {
        $blockException = null;
        $this->flagState(self::STATE_INSIDEBLOCK);

        try {
            call_user_func($callable, $this);
        } catch (CommunicationException $exception) {
            $blockException = $exception;
        } catch (ServerException $exception) {
            $blockException = $exception;
        } catch (\Exception $exception) {
            $blockException = $exception;
            $this->discard();
        }

        $this->unflagState(self::STATE_INSIDEBLOCK);

        if ($blockException !== null) {
            throw $blockException;
        }
    }

    /**
     * Helper method that handles protocol errors encountered inside a transaction.
     *
     * @param string $message Error message.
     */
    private function onProtocolError($message)
    {
        // Since a MULTI/EXEC block cannot be initialized when using aggregated
        // connections, we can safely assume that Predis\Client::getConnection()
        // will always return an instance of Predis\Connection\SingleConnectionInterface.
        CommunicationException::handle(new ProtocolException(
            $this->client->getConnection(), $message
        ));
    }
}
