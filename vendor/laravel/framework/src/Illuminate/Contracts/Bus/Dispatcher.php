<?php

namespace Illuminate\Contracts\Bus;

interface Dispatcher
{
    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatch($command);

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchSync($command, $handler = null);

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchNow($command, $handler = null);

    /**
     * Determine if the given command has a handler.
     *
     * @param  mixed  $command
     * @return bool
     */
    public function hasCommandHandler($command);

    /**
     * Retrieve the handler for a command.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function getCommandHandler($command);

    /**
     * Set the pipes commands should be piped through before dispatching.
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes);

    /**
     * Map a command to a handler.
     *
     * @param  array  $map
     * @return $this
     */
    public function map(array $map);
}
