<?php

namespace Elasticsearch\ConnectionPool;

use Elasticsearch\Common\Exceptions\InvalidArgumentException;
use Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Elasticsearch\Connections\Connection;
use Elasticsearch\Connections\ConnectionFactory;

/**
 * Class AbstractConnectionPool
 *
 * @category Elasticsearch
 * @package  Elasticsearch\ConnectionPool
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
abstract class AbstractConnectionPool implements ConnectionPoolInterface
{
    /**
     * Array of connections
     *
     * @var ConnectionInterface[]
     */
    protected $connections;

    /**
     * Array of initial seed connections
     *
     * @var ConnectionInterface[]
     */
    protected $seedConnections;

    /**
     * Selector object, used to select a connection on each request
     *
     * @var SelectorInterface
     */
    protected $selector;

    /** @var \Elasticsearch\Connections\ConnectionFactory  */
    protected $connectionFactory;

    /**
     * Constructor
     *
     * @param ConnectionInterface[] $connections          The Connections to choose from
     * @param SelectorInterface     $selector             A Selector instance to perform the selection logic for the available connections
     * @param ConnectionFactory     $factory              ConnectionFactory instance
     * @param array                 $connectionPoolParams
     */
    public function __construct($connections, SelectorInterface $selector, ConnectionFactory $factory, $connectionPoolParams)
    {
        $paramList = array('connections', 'selector', 'connectionPoolParams');
        foreach ($paramList as $param) {
            if (isset($$param) === false) {
                throw new InvalidArgumentException('`' . $param . '` parameter must not be null');
            }
        }

        if (isset($connectionPoolParams['randomizeHosts']) === true
            && $connectionPoolParams['randomizeHosts'] === true) {
            shuffle($connections);
        }

        $this->connections          = $connections;
        $this->seedConnections      = $connections;
        $this->selector             = $selector;
        $this->connectionPoolParams = $connectionPoolParams;
        $this->connectionFactory    = $factory;
    }

    /**
     * @param bool $force
     *
     * @return Connection
     */
    abstract public function nextConnection($force = false);

    abstract public function scheduleCheck();
}
