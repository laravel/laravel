<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Tests\Unit\ProviderTest;
use Whoops\Run;
use Whoops\RunInterface;

/**
 * @internal
 *
 * @see ProviderTest
 */
final class Provider
{
    /**
     * Holds an instance of the Run.
     */
    private RunInterface $run;

    /**
     * Holds an instance of the handler.
     */
    private Handler $handler;

    /**
     * Creates a new instance of the Provider.
     */
    public function __construct(?RunInterface $run = null, ?Handler $handler = null)
    {
        $this->run = $run ?: new Run;
        $this->handler = $handler ?: new Handler;
    }

    /**
     * Registers the current Handler as Error Handler.
     */
    public function register(): self
    {
        $this->run->pushHandler($this->handler)
            ->register();

        return $this;
    }

    /**
     * Returns the handler.
     */
    public function getHandler(): Handler
    {
        return $this->handler;
    }
}
