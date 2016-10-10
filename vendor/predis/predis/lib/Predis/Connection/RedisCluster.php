<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use Predis\NotSupportedException;
use Predis\ResponseErrorInterface;
use Predis\Cluster\CommandHashStrategyInterface;
use Predis\Cluster\RedisClusterHashStrategy;
use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;

/**
 * Abstraction for a Redis-backed cluster of nodes (Redis >= 3.0.0).
 *
 * This connection backend offers smart support for redis-cluster by handling
 * automatic slots map (re)generation upon -MOVED or -ASK responses returned by
 * Redis when redirecting a client to a different node.
 *
 * The cluster can be pre-initialized using only a subset of the actual nodes in
 * the cluster, Predis will do the rest by adjusting the slots map and creating
 * the missing underlying connection instances on the fly.
 *
 * It is possible to pre-associate connections to a slots range with the "slots"
 * parameter in the form "$first-$last". This can greatly reduce runtime node
 * guessing and redirections.
 *
 * It is also possible to ask for the full and updated slots map directly to one
 * of the nodes and optionally enable such a behaviour upon -MOVED redirections.
 * Asking for the cluster configuration to Redis is actually done by issuing a
 * CLUSTER SLOTS command to a random node in the pool.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class RedisCluster implements ClusterConnectionInterface, IteratorAggregate, Countable
{
    private $askClusterNodes = true;
    private $defaultParameters = array();
    private $pool = array();
    private $slots = array();
    private $slotsMap;
    private $strategy;
    private $connections;

    /**
     * @param ConnectionFactoryInterface $connections Connection factory object.
     */
    public function __construct(ConnectionFactoryInterface $connections = null)
    {
        $this->strategy = new RedisClusterHashStrategy();
        $this->connections = $connections ?: new ConnectionFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        foreach ($this->pool as $connection) {
            if ($connection->isConnected()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($connection = $this->getRandomConnection()) {
            $connection->connect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        foreach ($this->pool as $connection) {
            $connection->disconnect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(SingleConnectionInterface $connection)
    {
        $this->pool[(string) $connection] = $connection;
        unset($this->slotsMap);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(SingleConnectionInterface $connection)
    {
        if (false !== $id = array_search($connection, $this->pool, true)) {
            unset(
                $this->pool[$id],
                $this->slotsMap
            );

            return true;
        }

        return false;
    }

    /**
     * Removes a connection instance by using its identifier.
     *
     * @param  string $connectionID Connection identifier.
     * @return bool   True if the connection was in the pool.
     */
    public function removeById($connectionID)
    {
        if (isset($this->pool[$connectionID])) {
            unset(
                $this->pool[$connectionID],
                $this->slotsMap
            );

            return true;
        }

        return false;
    }

    /**
     * Generates the current slots map by guessing the cluster configuration out
     * of the connection parameters of the connections in the pool.
     *
     * Generation is based on the same algorithm used by Redis to generate the
     * cluster, so it is most effective when all of the connections supplied on
     * initialization have the "slots" parameter properly set accordingly to the
     * current cluster configuration.
     */
    public function buildSlotsMap()
    {
        $this->slotsMap = array();

        foreach ($this->pool as $connectionID => $connection) {
            $parameters = $connection->getParameters();

            if (!isset($parameters->slots)) {
                continue;
            }

            $slots = explode('-', $parameters->slots, 2);
            $this->setSlots($slots[0], $slots[1], $connectionID);
        }
    }

    /**
     * Generates an updated slots map fetching the cluster configuration using
     * the CLUSTER SLOTS command against the specified node or a random one from
     * the pool.
     *
     * @param  SingleConnectionInterface $connection Optional connection instance.
     * @return array
     */
    public function askClusterNodes(SingleConnectionInterface $connection = null)
    {
        if (!$connection && !$connection = $this->getRandomConnection()) {
            return array();
        }

        $command = RawCommand::create('CLUSTER', 'SLOTS');
        $response = $connection->executeCommand($command);

        foreach ($response as $slots) {
            // We only support master servers for now, so we ignore subsequent
            // elements in the $slots array identifying slaves.
            list($start, $end, $master) = $slots;

            if ($master[0] === '') {
                $this->setSlots($start, $end, (string) $connection);
            } else {
                $this->setSlots($start, $end, "{$master[0]}:{$master[1]}");
            }
        }

        return $this->slotsMap;
    }

    /**
     * Returns the current slots map for the cluster.
     *
     * @return array
     */
    public function getSlotsMap()
    {
        if (!isset($this->slotsMap)) {
            $this->slotsMap = array();
        }

        return $this->slotsMap;
    }

    /**
     * Pre-associates a connection to a slots range to avoid runtime guessing.
     *
     * @param int                              $first      Initial slot of the range.
     * @param int                              $last       Last slot of the range.
     * @param SingleConnectionInterface|string $connection ID or connection instance.
     */
    public function setSlots($first, $last, $connection)
    {
        if ($first < 0x0000 || $first > 0x3FFF ||
            $last < 0x0000 || $last > 0x3FFF ||
            $last < $first
        ) {
            throw new OutOfBoundsException(
                "Invalid slot range for $connection: [$first-$last]"
            );
        }

        $slots = array_fill($first, $last - $first + 1, (string) $connection);
        $this->slotsMap = $this->getSlotsMap() + $slots;
    }

    /**
     * Guesses the correct node associated to a given slot using a precalculated
     * slots map, falling back to the same logic used by Redis to initialize a
     * cluster (best-effort).
     *
     * @param  int    $slot Slot index.
     * @return string Connection ID.
     */
    protected function guessNode($slot)
    {
        if (!isset($this->slotsMap)) {
            $this->buildSlotsMap();
        }

        if (isset($this->slotsMap[$slot])) {
            return $this->slotsMap[$slot];
        }

        $count = count($this->pool);
        $index = min((int) ($slot / (int) (16384 / $count)), $count - 1);
        $nodes = array_keys($this->pool);

        return $nodes[$index];
    }

    /**
     * Creates a new connection instance from the given connection ID.
     *
     * @param  string                    $connectionID Identifier for the connection.
     * @return SingleConnectionInterface
     */
    protected function createConnection($connectionID)
    {
        $host = explode(':', $connectionID, 2);

        $parameters = array_merge($this->defaultParameters, array(
            'host' => $host[0],
            'port' => $host[1],
        ));

        $connection = $this->connections->create($parameters);

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(CommandInterface $command)
    {
        $hash = $this->strategy->getHash($command);

        if (!isset($hash)) {
            throw new NotSupportedException(
                "Cannot use {$command->getId()} with redis-cluster"
            );
        }

        $slot = $hash & 0x3FFF;

        if (isset($this->slots[$slot])) {
            return $this->slots[$slot];
        } else {
            return $this->getConnectionBySlot($slot);
        }
    }

    /**
     * Returns the connection currently associated to a given slot.
     *
     * @param  int                       $slot Slot index.
     * @return SingleConnectionInterface
     */
    public function getConnectionBySlot($slot)
    {
        if ($slot < 0x0000 || $slot > 0x3FFF) {
            throw new OutOfBoundsException("Invalid slot [$slot]");
        }

        if (isset($this->slots[$slot])) {
            return $this->slots[$slot];
        }

        $connectionID = $this->guessNode($slot);

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
            $this->pool[$connectionID] = $connection;
        }

        return $this->slots[$slot] = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionById($connectionID)
    {
        if (isset($this->pool[$connectionID])) {
            return $this->pool[$connectionID];
        }
    }

    /**
     * Returns a random connection from the pool.
     *
     * @return SingleConnectionInterface
     */
    protected function getRandomConnection()
    {
        if ($this->pool) {
            return $this->pool[array_rand($this->pool)];
        }
    }

    /**
     * Permanently associates the connection instance to a new slot.
     * The connection is added to the connections pool if not yet included.
     *
     * @param SingleConnectionInterface $connection Connection instance.
     * @param int                       $slot       Target slot index.
     */
    protected function move(SingleConnectionInterface $connection, $slot)
    {
        $this->pool[(string) $connection] = $connection;
        $this->slots[(int) $slot] = $connection;
    }

    /**
     * Handles -ERR responses returned by Redis.
     *
     * @param  CommandInterface       $command Command that generated the -ERR response.
     * @param  ResponseErrorInterface $error   Redis error response object.
     * @return mixed
     */
    protected function onErrorResponse(CommandInterface $command, ResponseErrorInterface $error)
    {
        $details = explode(' ', $error->getMessage(), 2);

        switch ($details[0]) {
            case 'MOVED':
                return $this->onMovedResponse($command, $details[1]);

            case 'ASK':
                return $this->onAskResponse($command, $details[1]);

            default:
                return $error;
        }
    }

    /**
     * Handles -MOVED responses by executing again the command against the node
     * indicated by the Redis response.
     *
     * @param  CommandInterface $command Command that generated the -MOVED response.
     * @param  string           $details Parameters of the -MOVED response.
     * @return mixed
     */
    protected function onMovedResponse(CommandInterface $command, $details)
    {
        list($slot, $connectionID) = explode(' ', $details, 2);

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
        }

        if ($this->askClusterNodes) {
            $this->askClusterNodes($connection);
        }

        $this->move($connection, $slot);
        $response = $this->executeCommand($command);

        return $response;
    }

    /**
     * Handles -ASK responses by executing again the command against the node
     * indicated by the Redis response.
     *
     * @param  CommandInterface $command Command that generated the -ASK response.
     * @param  string           $details Parameters of the -ASK response.
     * @return mixed
     */
    protected function onAskResponse(CommandInterface $command, $details)
    {
        list($slot, $connectionID) = explode(' ', $details, 2);

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
        }

        $connection->executeCommand(RawCommand::create('ASKING'));
        $response = $connection->executeCommand($command);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(CommandInterface $command)
    {
        $this->getConnection($command)->writeCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(CommandInterface $command)
    {
        return $this->getConnection($command)->readResponse($command);
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(CommandInterface $command)
    {
        $connection = $this->getConnection($command);
        $response = $connection->executeCommand($command);

        if ($response instanceof ResponseErrorInterface) {
            return $this->onErrorResponse($command, $response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->pool);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator(array_values($this->pool));
    }

    /**
     * Returns the underlying hash strategy used to hash commands by their keys.
     *
     * @return CommandHashStrategyInterface
     */
    public function getCommandHashStrategy()
    {
        return $this->strategy;
    }

    /**
     * Enables automatic fetching of the current slots map from one of the nodes
     * using the CLUSTER SLOTS command. This option is disabled by default but
     * asking the current slots map to Redis upon -MOVED responses may reduce
     * overhead by eliminating the trial-and-error nature of the node guessing
     * procedure, mostly when targeting many keys that would end up in a lot of
     * redirections.
     *
     * The slots map can still be manually fetched using the askClusterNodes()
     * method whether or not this option is enabled.
     *
     * @param bool $value Enable or disable the use of CLUSTER SLOTS.
     */
    public function enableClusterNodes($value)
    {
        $this->askClusterNodes = (bool) $value;
    }

    /**
     * Sets a default array of connection parameters to be applied when creating
     * new connection instances on the fly when they are not part of the initial
     * pool supplied upon cluster initialization.
     *
     * These parameters are not applied to connections added to the pool using
     * the add() method.
     *
     * @param array $parameters Array of connection parameters.
     */
    public function setDefaultParameters(array $parameters)
    {
        $this->defaultParameters = array_merge(
            $this->defaultParameters,
            $parameters ?: array()
        );
    }
}
