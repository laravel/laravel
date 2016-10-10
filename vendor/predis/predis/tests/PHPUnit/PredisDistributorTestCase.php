<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster\Distribution;

use PredisTestCase;

/**
 *
 */
abstract class PredisDistributorTestCase extends PredisTestCase
{
    /**
     * Returns a new instance of the tested distributor.
     *
     * @return Predis\Cluster\Distribution\DistributionStrategyInterface
     */
    abstract protected function getDistributorInstance();

    /**
     * Returns a list of nodes from the hashring.
     *
     * @param  DistributionStrategyInterface $ring       Hashring instance.
     * @param  int                           $iterations Number of nodes to fetch.
     * @return array                         Nodes from the hashring.
     */
    protected function getNodes(DistributionStrategyInterface $ring, $iterations = 10)
    {
        $nodes = array();

        for ($i = 0; $i < $iterations; $i++) {
            $key = $ring->hash($i * $i);
            $nodes[] = $ring->get($key);
        }

        return $nodes;
    }

    /**
     * @group disconnected
     */
    public function testEmptyRingThrowsException()
    {
        $this->setExpectedException('Predis\Cluster\Distribution\EmptyRingException');

        $ring = $this->getDistributorInstance();
        $ring->get('nodekey');
    }

    /**
     * @group disconnected
     */
    public function testRemoveOnEmptyRingDoesNotThrowException()
    {
        $ring = $this->getDistributorInstance();

        $this->assertNull($ring->remove('node'));
    }
}
