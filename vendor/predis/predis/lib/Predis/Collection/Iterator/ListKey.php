<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Collection\Iterator;

use InvalidArgumentException;
use Iterator;
use Predis\ClientInterface;
use Predis\NotSupportedException;

/**
 * Abstracts the iteration of items stored in a list by leveraging the LRANGE
 * command wrapped in a fully-rewindable PHP iterator.
 *
 * This iterator tries to emulate the behaviour of cursor-based iterators based
 * on the SCAN-family of commands introduced in Redis <= 2.8, meaning that due
 * to its incremental nature with multiple fetches it can only offer limited
 * guarantees on the returned elements because the collection can change several
 * times (trimmed, deleted, overwritten) during the iteration process.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @link http://redis.io/commands/lrange
 */
class ListKey implements Iterator
{
    protected $client;
    protected $count;
    protected $key;

    protected $valid;
    protected $fetchmore;
    protected $elements;
    protected $position;
    protected $current;

    /**
     * @param ClientInterface $client Client connected to Redis.
     * @param string          $key    Redis list key.
     * @param int             $count  Number of items retrieved on each fetch operation.
     */
    public function __construct(ClientInterface $client, $key, $count = 10)
    {
        $this->requiredCommand($client, 'LRANGE');

        if ((false === $count = filter_var($count, FILTER_VALIDATE_INT)) || $count < 0) {
            throw new InvalidArgumentException('The $count argument must be a positive integer.');
        }

        $this->client = $client;
        $this->key = $key;
        $this->count = $count;

        $this->reset();
    }

    /**
     * Ensures that the client instance supports the specified Redis
     * command required to fetch elements from the server to perform
     * the iteration.
     *
     * @param ClientInterface $client    Client connected to Redis.
     * @param string          $commandID Command ID.
     */
    protected function requiredCommand(ClientInterface $client, $commandID)
    {
        if (!$client->getProfile()->supportsCommand($commandID)) {
            throw new NotSupportedException("The specified server profile does not support the `$commandID` command.");
        }
    }

    /**
     * Resets the inner state of the iterator.
     */
    protected function reset()
    {
        $this->valid = true;
        $this->fetchmore = true;
        $this->elements = array();
        $this->position = -1;
        $this->current = null;
    }

    /**
     * Fetches a new set of elements from the remote collection,
     * effectively advancing the iteration process.
     *
     * @return array
     */
    protected function executeCommand()
    {
        return $this->client->lrange($this->key, $this->position + 1, $this->position + $this->count);
    }

    /**
     * Populates the local buffer of elements fetched from the
     * server during the iteration.
     */
    protected function fetch()
    {
        $elements = $this->executeCommand();

        if (count($elements) < $this->count) {
            $this->fetchmore = false;
        }

        $this->elements = $elements;
    }

    /**
     * Extracts next values for key() and current().
     */
    protected function extractNext()
    {
        $this->position++;
        $this->current = array_shift($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->reset();
        $this->next();
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
        if (!$this->elements && $this->fetchmore) {
            $this->fetch();
        }

        if ($this->elements) {
            $this->extractNext();
        } else {
            $this->valid = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->valid;
    }
}
