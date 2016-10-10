<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'SharedConfigurations.php';

// Developers can customize the distribution strategy used by the client
// to distribute keys among a cluster of servers simply by creating a class
// that implements Predis\Distribution\DistributionStrategyInterface.

use Predis\Connection\PredisCluster;
use Predis\Cluster\Distribution\DistributionStrategyInterface;
use Predis\Cluster\Hash\HashGeneratorInterface;

class NaiveDistributionStrategy implements DistributionStrategyInterface, HashGeneratorInterface
{
    private $nodes;
    private $nodesCount;

    public function __construct()
    {
        $this->nodes = array();
        $this->nodesCount = 0;
    }

    public function add($node, $weight = null)
    {
        $this->nodes[] = $node;
        $this->nodesCount++;
    }

    public function remove($node)
    {
        $this->nodes = array_filter($this->nodes, function ($n) use ($node) {
            return $n !== $node;
        });

        $this->nodesCount = count($this->nodes);
    }

    public function get($key)
    {
        if (0 === $count = $this->nodesCount) {
            throw new RuntimeException('No connections');
        }

        return $this->nodes[$count > 1 ? abs($key % $count) : 0];
    }

    public function hash($value)
    {
        return crc32($value);
    }

    public function getHashGenerator()
    {
        return $this;
    }
}

$options = array(
    'cluster' => function () {
        $distributor = new NaiveDistributionStrategy();
        $cluster = new PredisCluster($distributor);

        return $cluster;
    },
);

$client = new Predis\Client($multiple_servers, $options);

for ($i = 0; $i < 100; $i++) {
    $client->set("key:$i", str_pad($i, 4, '0', 0));
    $client->get("key:$i");
}

$server1 = $client->getClientFor('first')->info();
$server2 = $client->getClientFor('second')->info();

if (isset($server1['Keyspace'], $server2['Keyspace'])) {
    $server1 = $server1['Keyspace'];
    $server2 = $server2['Keyspace'];
}

printf("Server '%s' has %d keys while server '%s' has %d keys.\n",
    'first', $server1['db15']['keys'], 'second', $server2['db15']['keys']
);
