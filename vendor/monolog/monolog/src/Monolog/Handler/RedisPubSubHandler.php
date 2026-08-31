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
 * Sends the message to a Redis Pub/Sub channel using PUBLISH
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisPubSubHandler(new Predis\Client("tcp://localhost:6379"), "logs", Level::Warning);
 *   $log->pushHandler($redis);
 *
 * @author Gaëtan Faugère <gaetan@fauge.re>
 */
class RedisPubSubHandler extends AbstractProcessingHandler
{
    /** @var Predis<Predis>|Redis */
    private Predis|Redis $redisClient;
    private string $channelKey;

    /**
     * @param Predis<Predis>|Redis $redis The redis instance
     * @param string               $key   The channel key to publish records to
     */
    public function __construct(Predis|Redis $redis, string $key, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->redisClient = $redis;
        $this->channelKey = $key;

        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->redisClient->publish($this->channelKey, $record->formatted);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
