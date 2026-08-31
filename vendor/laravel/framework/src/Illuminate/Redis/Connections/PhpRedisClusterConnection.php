<?php

namespace Illuminate\Redis\Connections;

use InvalidArgumentException;

class PhpRedisClusterConnection extends PhpRedisConnection
{
    /**
     * The RedisCluster client.
     *
     * @var \RedisCluster
     */
    protected $client;

    /**
     * The default node to use from the cluster.
     *
     * @var string|array
     */
    protected $defaultNode;

    /**
     * Scan all keys based on the given options.
     *
     * @param  mixed  $cursor
     * @param  array  $options
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    #[\Override]
    public function scan($cursor, $options = [])
    {
        $result = $this->client->scan($cursor,
            $options['node'] ?? $this->defaultNode(),
            $options['match'] ?? '*',
            $options['count'] ?? 10
        );

        if ($result === false) {
            $result = [];
        }

        return $cursor === 0 && empty($result) ? false : [$cursor, $result];
    }

    /**
     * Flush the selected Redis database on all master nodes.
     *
     * @return void
     */
    public function flushdb()
    {
        $arguments = func_get_args();

        $async = strtoupper((string) ($arguments[0] ?? null)) === 'ASYNC';

        foreach ($this->client->_masters() as $master) {
            $async
                ? $this->command('rawCommand', [$master, 'flushdb', 'async'])
                : $this->command('flushdb', [$master]);
        }
    }

    /**
     * Return default node to use for cluster.
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    private function defaultNode()
    {
        if (! isset($this->defaultNode)) {
            $this->defaultNode = $this->client->_masters()[0] ?? throw new InvalidArgumentException('Unable to determine default node. No master nodes found in the cluster.');
        }

        return $this->defaultNode;
    }
}
