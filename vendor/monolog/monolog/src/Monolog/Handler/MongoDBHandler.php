<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\MongoDBFormatter;
use Monolog\LogRecord;

/**
 * Logs to a MongoDB database.
 *
 * Usage example:
 *
 *   $log = new \Monolog\Logger('application');
 *   $client = new \MongoDB\Client('mongodb://localhost:27017');
 *   $mongodb = new \Monolog\Handler\MongoDBHandler($client, 'logs', 'prod');
 *   $log->pushHandler($mongodb);
 *
 * The above examples uses the MongoDB PHP library's client class; however, the
 * MongoDB\Driver\Manager class from ext-mongodb is also supported.
 */
class MongoDBHandler extends AbstractProcessingHandler
{
    private Collection $collection;

    private Client|Manager $manager;

    private string|null $namespace = null;

    /**
     * Constructor.
     *
     * @param Client|Manager $mongodb    MongoDB library or driver client
     * @param string         $database   Database name
     * @param string         $collection Collection name
     */
    public function __construct(Client|Manager $mongodb, string $database, string $collection, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        if ($mongodb instanceof Client) {
            $this->collection = method_exists($mongodb, 'getCollection') ? $mongodb->getCollection($database, $collection) : $mongodb->selectCollection($database, $collection);
        } else {
            $this->manager = $mongodb;
            $this->namespace = $database . '.' . $collection;
        }

        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        if (isset($this->collection)) {
            $this->collection->insertOne($record->formatted);
        }

        if (isset($this->manager, $this->namespace)) {
            $bulk = new BulkWrite;
            $bulk->insert($record->formatted);
            $this->manager->executeBulkWrite($this->namespace, $bulk);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new MongoDBFormatter;
    }
}
