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

/**
 * Abstracts the access to a streamable list of tuples represented
 * as a multibulk reply that alternates keys and values.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiBulkResponseTuple extends MultiBulkResponse implements \OuterIterator
{
    private $iterator;

    /**
     * @param MultiBulkResponseSimple $iterator Multibulk reply iterator.
     */
    public function __construct(MultiBulkResponseSimple $iterator)
    {
        $this->checkPreconditions($iterator);

        $virtualSize = count($iterator) / 2;
        $this->iterator = $iterator;
        $this->position = $iterator->getPosition();
        $this->current = $virtualSize > 0 ? $this->getValue() : null;
        $this->replySize = $virtualSize;
    }

    /**
     * Checks for valid preconditions.
     *
     * @param MultiBulkResponseSimple $iterator Multibulk reply iterator.
     */
    protected function checkPreconditions(MultiBulkResponseSimple $iterator)
    {
        if ($iterator->getPosition() !== 0) {
            throw new \RuntimeException('Cannot initialize a tuple iterator with an already initiated iterator');
        }

        if (($size = count($iterator)) % 2 !== 0) {
            throw new \UnexpectedValueException("Invalid reply size for a tuple iterator [$size]");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->iterator->sync(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        $k = $this->iterator->current();
        $this->iterator->next();

        $v = $this->iterator->current();
        $this->iterator->next();

        return array($k, $v);
    }
}
