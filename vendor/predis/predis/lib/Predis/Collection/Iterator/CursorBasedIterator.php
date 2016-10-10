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

use Iterator;
use Predis\ClientInterface;
use Predis\NotSupportedException;

/**
 * Provides the base implementation for a fully-rewindable PHP iterator
 * that can incrementally iterate over cursor-based collections stored
 * on Redis using commands in the `SCAN` family.
 *
 * Given their incremental nature with multiple fetches, these kind of
 * iterators offer limited guarantees about the returned elements because
 * the collection can change several times during the iteration process.
 *
 * @see http://redis.io/commands/scan
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class CursorBasedIterator implements Iterator
{
    protected $client;
    protected $match;
    protected $count;

    protected $valid;
    protected $fetchmore;
    protected $elements;
    protected $cursor;
    protected $position;
    protected $current;

    /**
     * @param ClientInterface $client Client connected to Redis.
     * @param string          $match  Pattern to match during the server-side iteration.
     * @param int             $count  Hints used by Redis to compute the number of results per iteration.
     */
    public function __construct(ClientInterface $client, $match = null, $count = null)
    {
        $this->client = $client;
        $this->match = $match;
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
        $this->cursor = 0;
        $this->position = -1;
        $this->current = null;
    }

    /**
     * Returns an array of options for the `SCAN` command.
     *
     * @return array
     */
    protected function getScanOptions()
    {
        $options = array();

        if (strlen($this->match) > 0) {
            $options['MATCH'] = $this->match;
        }

        if ($this->count > 0) {
            $options['COUNT'] = $this->count;
        }

        return $options;
    }

    /**
     * Fetches a new set of elements from the remote collection,
     * effectively advancing the iteration process.
     *
     * @return array
     */
    abstract protected function executeCommand();

    /**
     * Populates the local buffer of elements fetched from the
     * server during the iteration.
     */
    protected function fetch()
    {
        list($cursor, $elements) = $this->executeCommand();

        if (!$cursor) {
            $this->fetchmore = false;
        }

        $this->cursor = $cursor;
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
        tryFetch: {
            if (!$this->elements && $this->fetchmore) {
                $this->fetch();
            }

            if ($this->elements) {
                $this->extractNext();
            } elseif ($this->cursor) {
                goto tryFetch;
            } else {
                $this->valid = false;
            }
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
