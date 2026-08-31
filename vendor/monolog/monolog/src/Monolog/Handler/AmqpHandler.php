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

use Gelf\Message as GelfMessage;
use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use AMQPExchange;
use Monolog\LogRecord;

class AmqpHandler extends AbstractProcessingHandler
{
    protected AMQPExchange|AMQPChannel $exchange;

    /** @var array<string, mixed> */
    private array $extraAttributes = [];

    protected string $exchangeName;

    /**
     * @param AMQPExchange|AMQPChannel $exchange     AMQPExchange (php AMQP ext) or PHP AMQP lib channel, ready for use
     * @param string|null              $exchangeName Optional exchange name, for AMQPChannel (PhpAmqpLib) only
     */
    public function __construct(AMQPExchange|AMQPChannel $exchange, ?string $exchangeName = null, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        if ($exchange instanceof AMQPChannel) {
            $this->exchangeName = (string) $exchangeName;
        } elseif ($exchangeName !== null) {
            @trigger_error('The $exchangeName parameter can only be passed when using PhpAmqpLib, if using an AMQPExchange instance configure it beforehand', E_USER_DEPRECATED);
        }
        $this->exchange = $exchange;

        parent::__construct($level, $bubble);
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    /**
     * Configure extra attributes to pass to the AMQPExchange (if you are using the amqp extension)
     *
     * @param  array<string, mixed> $extraAttributes One of content_type, content_encoding,
     *                                               message_id, user_id, app_id, delivery_mode,
     *                                               priority, timestamp, expiration, type
     *                                               or reply_to, headers.
     * @return $this
     */
    public function setExtraAttributes(array $extraAttributes): self
    {
        $this->extraAttributes = $extraAttributes;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $data = $record->formatted;
        $routingKey = $this->getRoutingKey($record);

        if($data instanceof GelfMessage) {
            $data = json_encode($data->toArray());
        }

        if ($this->exchange instanceof AMQPExchange) {
            $attributes = [
                'delivery_mode' => 2,
                'content_type'  => 'application/json',
            ];
            if (\count($this->extraAttributes) > 0) {
                $attributes = array_merge($attributes, $this->extraAttributes);
            }
            $this->exchange->publish(
                $data,
                $routingKey,
                0,
                $attributes
            );
        } else {
            $this->exchange->basic_publish(
                $this->createAmqpMessage($data),
                $this->exchangeName,
                $routingKey
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        if ($this->exchange instanceof AMQPExchange) {
            parent::handleBatch($records);

            return;
        }

        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            $record = $this->processRecord($record);
            $data = $this->getFormatter()->format($record);

            if($data instanceof GelfMessage) {
                $data = json_encode($data->toArray());
            }

            $this->exchange->batch_basic_publish(
                $this->createAmqpMessage($data),
                $this->exchangeName,
                $this->getRoutingKey($record)
            );
        }

        $this->exchange->publish_batch();
    }

    /**
     * Gets the routing key for the AMQP exchange
     */
    protected function getRoutingKey(LogRecord $record): string
    {
        $routingKey = sprintf('%s.%s', $record->level->name, $record->channel);

        return strtolower($routingKey);
    }

    private function createAmqpMessage(string $data): AMQPMessage
    {
        $attributes = [
            'delivery_mode' => 2,
            'content_type' => 'application/json',
        ];
        if (\count($this->extraAttributes) > 0) {
            $attributes = array_merge($attributes, $this->extraAttributes);
        }

        return new AMQPMessage($data, $attributes);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, false);
    }
}
