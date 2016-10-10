<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

use Predis\Connection\ClusterConnectionInterface;
use Predis\Connection\PredisCluster;
use Predis\Connection\RedisCluster;

/**
 * Option class that returns a connection cluster to be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientCluster extends AbstractOption
{
    /**
     * Checks if the specified value is a valid instance of ClusterConnectionInterface.
     *
     * @param  ClusterConnectionInterface $cluster Instance of a connection cluster.
     * @return ClusterConnectionInterface
     */
    protected function checkInstance($cluster)
    {
        if (!$cluster instanceof ClusterConnectionInterface) {
            throw new \InvalidArgumentException('Instance of Predis\Connection\ClusterConnectionInterface expected');
        }

        return $cluster;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(ClientOptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            return $this->checkInstance(call_user_func($value, $options, $this));
        }

        $initializer = $this->getInitializer($options, $value);

        return $this->checkInstance($initializer());
    }

    /**
     * Returns an initializer for the specified FQN or type.
     *
     * @param  string                 $fqnOrType Type of cluster or FQN of a class implementing ClusterConnectionInterface.
     * @param  ClientOptionsInterface $options   Instance of the client options.
     * @return \Closure
     */
    protected function getInitializer(ClientOptionsInterface $options, $fqnOrType)
    {
        switch ($fqnOrType) {
            case 'predis':
                return function () {
                    return new PredisCluster();
                };

            case 'redis':
                return function () use ($options) {
                    $connectionFactory = $options->connections;
                    $cluster = new RedisCluster($connectionFactory);

                    return $cluster;
                };

            default:
                // TODO: we should not even allow non-string values here.
                if (is_string($fqnOrType) && !class_exists($fqnOrType)) {
                    throw new \InvalidArgumentException("Class $fqnOrType does not exist");
                }

                return function () use ($fqnOrType) {
                    return new $fqnOrType();
                };
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(ClientOptionsInterface $options)
    {
        return new PredisCluster();
    }
}
