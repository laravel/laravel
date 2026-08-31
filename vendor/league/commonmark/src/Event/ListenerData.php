<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Event;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class ListenerData
{
    /** @var class-string */
    private string $event;

    /** @var callable */
    private $listener;

    /**
     * @param class-string $event
     */
    public function __construct(string $event, callable $listener)
    {
        $this->event    = $event;
        $this->listener = $listener;
    }

    /**
     * @return class-string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    public function getListener(): callable
    {
        return $this->listener;
    }
}
