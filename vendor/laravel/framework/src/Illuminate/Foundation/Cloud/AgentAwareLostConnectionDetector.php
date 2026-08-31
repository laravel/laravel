<?php

namespace Illuminate\Foundation\Cloud;

use Illuminate\Contracts\Database\LostConnectionDetector;
use Throwable;

/**
 * Treats an unreachable cloud-agent runtime socket as a lost connection so the
 * worker exits and the pod restarts — the only way to recover an agent that
 * has died in-pod. Every other exception is delegated to parent instance.
 */
class AgentAwareLostConnectionDetector implements LostConnectionDetector
{
    /**
     * Create a new detector instance.
     */
    public function __construct(
        protected LostConnectionDetector $detector,
    ) {
        //
    }

    /**
     * Determine if the given exception was caused by a lost connection.
     */
    public function causedByLostConnection(Throwable $e): bool
    {
        return $e instanceof AgentUnreachableException
            || $this->detector->causedByLostConnection($e);
    }
}
