<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Iterator;

use Predis\ResponseObjectInterface;

/**
 * Iterator that abstracts the access to multibulk replies and allows
 * them to be consumed by user's code in a streaming fashion.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class MultiBulkResponse implements \Iterator, \Countable, ResponseObjectInterface
{
    protected $position;
    protected $current;
    protected $replySize;

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (++$this->position < $this->replySize) {
            $this->current = $this->getValue();
        }

        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->position < $this->replySize;
    }

    /**
     * Returns the number of items of the whole multibulk reply.
     *
     * This method should be used to get the size of the current multibulk
     * reply without using iterator_count, which actually consumes the
     * iterator to calculate the size (rewinding is not supported).
     *
     * @return int
     */
    public function count()
    {
        return $this->replySize;
    }

    /**
     * Returns the current position of the iterator.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function getValue();
}
