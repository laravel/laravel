<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event;

use function count;
use Countable;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<non-negative-int, Event>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class EventCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<Event>
     */
    private array $events = [];

    public function add(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    /**
     * @return list<Event>
     */
    public function asArray(): array
    {
        return $this->events;
    }

    public function count(): int
    {
        return count($this->events);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): EventCollectionIterator
    {
        return new EventCollectionIterator($this);
    }
}
