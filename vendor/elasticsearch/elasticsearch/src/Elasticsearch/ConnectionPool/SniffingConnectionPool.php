<?php

namespace Elasticsearch\ConnectionPool;

use Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Elasticsearch\Connections\Connection;
use Elasticsearch\Connections\ConnectionFactory;

class SniffingConnectionPool extends AbstractConnectionPool implements ConnectionPoolInterface
{
    /** @var int  */
    private $sniffingInterval = 300;

    /** @var  int */
    private $nextSniff = -1;

    public function __construct($connections, SelectorInterface $selector, ConnectionFactory $factory, $connectionPoolParams)
    {
        parent::__construct($connections, $selector, $factory, $connectionPoolParams);

        $this->setConnectionPoolParams($connectionPoolParams);
        $this->nextSniff = time() + $this->sniffingInterval;
    }

    /**
     * @param bool $force
     *
     * @return Connection
     * @throws \Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function nextConnection($force = false)
    {
        $this->sniff($force);

        $size = count($this->connections);
        while ($size--) {
            /** @var Connection $connection */
            $connection = $this->selector->select($this->connections);
            if ($connection->isAlive() === true || $connection->ping() === true) {
                return $connection;
            }
        }

        if ($force === true) {
            throw new NoNodesAvailableException("No alive nodes found in your cluster");
        }

        return $this->nextConnection(true);
    }

    public function scheduleCheck()
    {
        $this->nextSniff = -1;
    }

    /**
     * @param bool $force
     */
    private function sniff($force = false)
    {
        if ($force === false && $this->nextSniff >= time()) {
            return;
        }

        $total = count($this->connections);

        while ($total--) {
            /** @var Connection $connection */
            $connection = $this->selector->select($this->connections);

            if ($connection->isAlive() xor $force) {
                continue;
            }

            if ($this->sniffConnection($connection) === true) {
                return;
            }
        }

        if ($force === true) {
            return;
        }

        foreach ($this->seedConnections as $connection) {
            if ($this->sniffConnection($connection) === true) {
                return;
            }
        }
    }

    /**
     * @param Connection $connection
     * @return bool
     */
    private function sniffConnection(Connection $connection)
    {
        try {
            $response = $connection->sniff();
        } catch (OperationTimeoutException $exception) {
            return false;
        }

        $nodes = $this->parseClusterState($connection->getTransportSchema(), $response);

        if (count($nodes) === 0) {
            return false;
        }

        $this->connections = array();

        foreach ($nodes as $node) {
            $nodeDetails = array(
                'host' => $node['host'],
                'port' => $node['port']
            );
            $this->connections[] = $this->connectionFactory->create($nodeDetails);
        }

        $this->nextSniff = time() + $this->sniffingInterval;

        return true;
    }

    private function parseClusterState($transportSchema, $nodeInfo)
    {
        $pattern       = '/\/([^:]*):([0-9]+)\]/';
        $schemaAddress = $transportSchema . '_address';
        $hosts         = array();

        foreach ($nodeInfo['nodes'] as $node) {
            if (isset($node[$schemaAddress]) === true) {
                if (preg_match($pattern, $node[$schemaAddress], $match) === 1) {
                    $hosts[] = array(
                        'host' => $match[1],
                        'port' => (int) $match[2],
                    );
                }
            }
        }

        return $hosts;
    }

    private function setConnectionPoolParams($connectionPoolParams)
    {
        if (isset($connectionPoolParams['sniffingInterval']) === true) {
            $this->sniffingInterval = $connectionPoolParams['sniffingInterval'];
        }
    }
}
