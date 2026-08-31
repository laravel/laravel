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

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Level;
use Monolog\LogRecord;
use Predis\Client as Predis;
use Redis;

/**
 * Logs to a Redis key using rpush
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs");
 *   $log->pushHandler($redis);
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */
class RedisHandler extends AbstractProcessingHandler
{
    /** @var Predis<Predis>|Redis */
    private Predis|Redis $redisClient;
    private string $redisKey;
    protected int $capSize;

    /**
     * @param Predis<Predis>|Redis $redis   The redis instance
     * @param string               $key     The key name to push records to
     * @param int                  $capSize Number of entries to limit list size to, 0 = unlimited
     */
    public function __construct(Predis|Redis $redis, string $key, int|string|Level $level = Level::Debug, bool $bubble = true, int $capSize = 0)
    {
        $this->redisClient = $redis;
        $this->redisKey = $key;
        $this->capSize = $capSize;

        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        if ($this->capSize > 0) {
            $this->writeCapped($record);
        } else {
            $this->redisClient->rpush($this->redisKey, $record->formatted);
        }
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     */
    protected function writeCapped(LogRecord $record): void
    {
        if ($this->redisClient instanceof Redis) {
            $mode = \defined('Redis::MULTI') ? Redis::MULTI : 1;
            $this->redisClient->multi($mode)
                ->rPush($this->redisKey, $record->formatted)
                ->ltrim($this->redisKey, -$this->capSize, -1)
                ->exec();
        } else {
            $redisKey = $this->redisKey;
            $capSize = $this->capSize;
            $this->redisClient->transaction(function ($tx) use ($record, $redisKey, $capSize) {
                $tx->rpush($redisKey, $record->formatted);
                $tx->ltrim($redisKey, -$capSize, -1);
            });
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
