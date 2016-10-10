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

use Predis\ClientInterface;

/**
 * Abstracts the iteration of members stored in a set by
 * leveraging the SSCAN command (Redis >= 2.8) wrapped in
 * a fully-rewindable PHP iterator.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @link http://redis.io/commands/scan
 */
class SetKey extends CursorBasedIterator
{
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function __construct(ClientInterface $client, $key, $match = null, $count = null)
    {
        $this->requiredCommand($client, 'SSCAN');

        parent::__construct($client, $match, $count);

        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    protected function executeCommand()
    {
        return $this->client->sscan($this->key, $this->cursor, $this->getScanOptions());
    }
}
