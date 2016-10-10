<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster;

use Predis\Cluster\Hash\CRC16HashGenerator;
use Predis\Cluster\Hash\HashGeneratorInterface;

/**
 * Default class used by Predis to calculate hashes out of keys of
 * commands supported by redis-cluster.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class RedisClusterHashStrategy extends PredisClusterHashStrategy
{
    /**
     *
     */
    public function __construct(HashGeneratorInterface $hashGenerator = null)
    {
        parent::__construct($hashGenerator ?: new CRC16HashGenerator());
    }

    /**
     * Returns only the hashable part of a key (delimited by "{...}"), or the
     * whole key if a key tag is not found in the string.
     *
     * @param  string $key A key.
     * @return string
     */
    protected function extractKeyTag($key)
    {
        if (false !== $start = strpos($key, '{')) {
            if (false !== ($end = strpos($key, '}', $start)) && $end !== ++$start) {
                $key = substr($key, $start, $end - $start);
            }
        }

        return $key;
    }
}
