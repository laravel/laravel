<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Pipeline;

use SplQueue;
use Predis\BasicClientInterface;
use Predis\ClientException;
use Predis\ClientInterface;
use Predis\ExecutableContextInterface;
use Predis\Command\CommandInterface;

/**
 * Abstraction of a pipeline context where write and read operations
 * of commands and their replies over the network are pipelined.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PipelineContext implements BasicClientInterface, ExecutableContextInterface
{
    private $client;
    private $executor;
    private $pipeline;

    private $replies = array();
    private $running = false;

    /**
     * @param ClientInterface           $client   Client instance used by the context.
     * @param PipelineExecutorInterface $executor Pipeline executor instace.
     */
    public function __construct(ClientInterface $client, PipelineExecutorInterface $executor = null)
    {
        $this->client = $client;
        $this->executor = $executor ?: $this->createExecutor($client);
        $this->pipeline = new SplQueue();
    }

    /**
     * Returns a pipeline executor depending on the kind of the underlying
     * connection and the passed options.
     *
     * @param  ClientInterface           $client Client instance used by the context.
     * @return PipelineExecutorInterface
     */
    protected function createExecutor(ClientInterface $client)
    {
        $options = $client->getOptions();

        if (isset($options->exceptions)) {
            return new StandardExecutor($options->exceptions);
        }

        return new StandardExecutor();
    }

    /**
     * Queues a command into the pipeline buffer.
     *
     * @param  string $method    Command ID.
     * @param  array  $arguments Arguments for the command.
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $command = $this->client->createCommand($method, $arguments);
        $this->recordCommand($command);

        return $this;
    }

    /**
     * Queues a command instance into the pipeline buffer.
     *
     * @param CommandInterface $command Command to queue in the buffer.
     */
    protected function recordCommand(CommandInterface $command)
    {
        $this->pipeline->enqueue($command);
    }

    /**
     * Queues a command instance into the pipeline buffer.
     *
     * @param  CommandInterface $command Command to queue in the buffer.
     * @return $this
     */
    public function executeCommand(CommandInterface $command)
    {
        $this->recordCommand($command);

        return $this;
    }

    /**
     * Flushes the buffer that holds the queued commands.
     *
     * @param  bool            $send Specifies if the commands in the buffer should be sent to Redis.
     * @return PipelineContext
     */
    public function flushPipeline($send = true)
    {
        if ($send && !$this->pipeline->isEmpty()) {
            $connection = $this->client->getConnection();
            $replies = $this->executor->execute($connection, $this->pipeline);
            $this->replies = array_merge($this->replies, $replies);
        } else {
            $this->pipeline = new SplQueue();
        }

        return $this;
    }

    /**
     * Marks the running status of the pipeline.
     *
     * @param bool $bool True if the pipeline is running.
     *                   False if the pipeline is not running.
     */
    private function setRunning($bool)
    {
        if ($bool === true && $this->running === true) {
            throw new ClientException("This pipeline is already opened");
        }

        $this->running = $bool;
    }

    /**
     * Handles the actual execution of the whole pipeline.
     *
     * @param  mixed $callable Optional callback for execution.
     * @return array
     */
    public function execute($callable = null)
    {
        if ($callable && !is_callable($callable)) {
            throw new \InvalidArgumentException('Argument passed must be a callable object');
        }

        $this->setRunning(true);
        $pipelineBlockException = null;

        try {
            if ($callable !== null) {
                call_user_func($callable, $this);
            }
            $this->flushPipeline();
        } catch (\Exception $exception) {
            $pipelineBlockException = $exception;
        }

        $this->setRunning(false);

        if ($pipelineBlockException !== null) {
            throw $pipelineBlockException;
        }

        return $this->replies;
    }

    /**
     * Returns the underlying client instance used by the pipeline object.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the underlying pipeline executor used by the pipeline object.
     *
     * @return PipelineExecutorInterface
     */
    public function getExecutor()
    {
        return $this->executor;
    }
}
