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

use Predis\Cluster\Hash\HashGeneratorInterface;

/**
 * This class implements an hashring-based distributor that uses the same
 * algorithm of memcache to distribute keys in a cluster using client-side
 * sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @author Lorenzo Castelli <lcastelli@gmail.com>
 */
class HashRing implements DistributionStrategyInterface, HashGeneratorInterface
{
    const DEFAULT_REPLICAS = 128;
    const DEFAULT_WEIGHT   = 100;

    private $ring;
    private $ringKeys;
    private $ringKeysCount;
    private $replicas;
    private $nodeHashCallback;
    private $nodes = array();

    /**
     * @param int   $replicas         Number of replicas in the ring.
     * @param mixed $nodeHashCallback Callback returning the string used to calculate the hash of a node.
     */
    public function __construct($replicas = self::DEFAULT_REPLICAS, $nodeHashCallback = null)
    {
        $this->replicas = $replicas;
        $this->nodeHashCallback = $nodeHashCallback;
    }

    /**
     * Adds a node to the ring with an optional weight.
     *
     * @param mixed $node   Node object.
     * @param int   $weight Weight for the node.
     */
    public function add($node, $weight = null)
    {
        // In case of collisions in the hashes of the nodes, the node added
        // last wins, thus the order in which nodes are added is significant.
        $this->nodes[] = array('object' => $node, 'weight' => (int) $weight ?: $this::DEFAULT_WEIGHT);
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($node)
    {
        // A node is removed by resetting the ring so that it's recreated from
        // scratch, in order to reassign possible hashes with collisions to the
        // right node according to the order in which they were added in the
        // first place.
        for ($i = 0; $i < count($this->nodes); ++$i) {
            if ($this->nodes[$i]['object'] === $node) {
                array_splice($this->nodes, $i, 1);
                $this->reset();
                break;
            }
        }
    }

    /**
     * Resets the distributor.
     */
    private function reset()
    {
        unset(
            $this->ring,
            $this->ringKeys,
            $this->ringKeysCount
        );
    }

    /**
     * Returns the initialization status of the distributor.
     *
     * @return bool
     */
    private function isInitialized()
    {
        return isset($this->ringKeys);
    }

    /**
     * Calculates the total weight of all the nodes in the distributor.
     *
     * @return int
     */
    private function computeTotalWeight()
    {
        $totalWeight = 0;

        foreach ($this->nodes as $node) {
            $totalWeight += $node['weight'];
        }

        return $totalWeight;
    }

    /**
     * Initializes the distributor.
     */
    private function initialize()
    {
        if ($this->isInitialized()) {
            return;
        }

        if (!$this->nodes) {
            throw new EmptyRingException('Cannot initialize empty hashring');
        }

        $this->ring = array();
        $totalWeight = $this->computeTotalWeight();
        $nodesCount = count($this->nodes);

        foreach ($this->nodes as $node) {
            $weightRatio = $node['weight'] / $totalWeight;
            $this->addNodeToRing($this->ring, $node, $nodesCount, $this->replicas, $weightRatio);
        }

        ksort($this->ring, SORT_NUMERIC);
        $this->ringKeys = array_keys($this->ring);
        $this->ringKeysCount = count($this->ringKeys);
    }

    /**
     * Implements the logic needed to add a node to the hashring.
     *
     * @param array $ring        Source hashring.
     * @param mixed $node        Node object to be added.
     * @param int   $totalNodes  Total number of nodes.
     * @param int   $replicas    Number of replicas in the ring.
     * @param float $weightRatio Weight ratio for the node.
     */
    protected function addNodeToRing(&$ring, $node, $totalNodes, $replicas, $weightRatio)
    {
        $nodeObject = $node['object'];
        $nodeHash = $this->getNodeHash($nodeObject);
        $replicas = (int) round($weightRatio * $totalNodes * $replicas);

        for ($i = 0; $i < $replicas; $i++) {
            $key = crc32("$nodeHash:$i");
            $ring[$key] = $nodeObject;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNodeHash($nodeObject)
    {
        if ($this->nodeHashCallback === null) {
            return (string) $nodeObject;
        }

        return call_user_func($this->nodeHashCallback, $nodeObject);
    }

    /**
     * Calculates the hash for the specified value.
     *
     * @param  string $value Input value.
     * @return int
     */
    public function hash($value)
    {
        return crc32($value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->ring[$this->getNodeKey($key)];
    }

    /**
     * Calculates the corrisponding key of a node distributed in the hashring.
     *
     * @param  int $key Computed hash of a key.
     * @return int
     */
    private function getNodeKey($key)
    {
        $this->initialize();
        $ringKeys = $this->ringKeys;
        $upper = $this->ringKeysCount - 1;
        $lower = 0;

        while ($lower <= $upper) {
            $index = ($lower + $upper) >> 1;
            $item  = $ringKeys[$index];

            if ($item > $key) {
                $upper = $index - 1;
            } elseif ($item < $key) {
                $lower = $index + 1;
            } else {
                return $item;
            }
        }

        return $ringKeys[$this->wrapAroundStrategy($upper, $lower, $this->ringKeysCount)];
    }

    /**
     * Implements a strategy to deal with wrap-around errors during binary searches.
     *
     * @param  int $upper
     * @param  int $lower
     * @param  int $ringKeysCount
     * @return int
     */
    protected function wrapAroundStrategy($upper, $lower, $ringKeysCount)
    {
        // Binary search for the last item in ringkeys with a value less or
        // equal to the key. If no such item exists, return the last item.
        return $upper >= 0 ? $upper : $ringKeysCount - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getHashGenerator()
    {
        return $this;
    }
}
