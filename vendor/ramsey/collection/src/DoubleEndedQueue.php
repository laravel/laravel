<?php

/**
 * This file is part of the ramsey/collection library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Collection;

use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Exception\NoSuchElementException;

use function array_key_last;
use function array_pop;
use function array_unshift;

/**
 * This class provides a basic implementation of `DoubleEndedQueueInterface`, to
 * minimize the effort required to implement this interface.
 *
 * @template T
 * @extends Queue<T>
 * @implements DoubleEndedQueueInterface<T>
 */
class DoubleEndedQueue extends Queue implements DoubleEndedQueueInterface
{
    /**
     * Constructs a double-ended queue (dequeue) object of the specified type,
     * optionally with the specified data.
     *
     * @param string $queueType The type or class name associated with this dequeue.
     * @param array<array-key, T> $data The initial items to store in the dequeue.
     */
    public function __construct(private readonly string $queueType, array $data = [])
    {
        parent::__construct($this->queueType, $data);
    }

    /**
     * @throws InvalidArgumentException if $element is of the wrong type
     */
    public function addFirst(mixed $element): bool
    {
        if ($this->checkType($this->getType(), $element) === false) {
            throw new InvalidArgumentException(
                'Value must be of type ' . $this->getType() . '; value is '
                . $this->toolValueToString($element),
            );
        }

        array_unshift($this->data, $element);

        return true;
    }

    /**
     * @throws InvalidArgumentException if $element is of the wrong type
     */
    public function addLast(mixed $element): bool
    {
        return $this->add($element);
    }

    public function offerFirst(mixed $element): bool
    {
        try {
            return $this->addFirst($element);
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    public function offerLast(mixed $element): bool
    {
        return $this->offer($element);
    }

    /**
     * @return T the first element in this queue.
     *
     * @throws NoSuchElementException if the queue is empty
     */
    public function removeFirst(): mixed
    {
        return $this->remove();
    }

    /**
     * @return T the last element in this queue.
     *
     * @throws NoSuchElementException if this queue is empty.
     */
    public function removeLast(): mixed
    {
        return $this->pollLast() ?? throw new NoSuchElementException(
            'Can\'t return element from Queue. Queue is empty.',
        );
    }

    /**
     * @return T | null the head of this queue, or `null` if this queue is empty.
     */
    public function pollFirst(): mixed
    {
        return $this->poll();
    }

    /**
     * @return T | null the tail of this queue, or `null` if this queue is empty.
     */
    public function pollLast(): mixed
    {
        return array_pop($this->data);
    }

    /**
     * @return T the head of this queue.
     *
     * @throws NoSuchElementException if this queue is empty.
     */
    public function firstElement(): mixed
    {
        return $this->element();
    }

    /**
     * @return T the tail of this queue.
     *
     * @throws NoSuchElementException if this queue is empty.
     */
    public function lastElement(): mixed
    {
        return $this->peekLast() ?? throw new NoSuchElementException(
            'Can\'t return element from Queue. Queue is empty.',
        );
    }

    /**
     * @return T | null the head of this queue, or `null` if this queue is empty.
     */
    public function peekFirst(): mixed
    {
        return $this->peek();
    }

    /**
     * @return T | null the tail of this queue, or `null` if this queue is empty.
     */
    public function peekLast(): mixed
    {
        $lastIndex = array_key_last($this->data);

        if ($lastIndex === null) {
            return null;
        }

        return $this->data[$lastIndex];
    }
}
