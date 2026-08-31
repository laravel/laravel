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

use Aws\Sqs\SqsClient;
use Monolog\Level;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Writes to any sqs queue.
 *
 * @author Martijn van Calker <git@amvc.nl>
 */
class SqsHandler extends AbstractProcessingHandler
{
    /** 256 KB in bytes - maximum message size in SQS */
    protected const MAX_MESSAGE_SIZE = 262144;
    /** 100 KB in bytes - head message size for new error log */
    protected const HEAD_MESSAGE_SIZE = 102400;

    private SqsClient $client;
    private string $queueUrl;

    public function __construct(SqsClient $sqsClient, string $queueUrl, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = $sqsClient;
        $this->queueUrl = $queueUrl;
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        if (!isset($record->formatted) || 'string' !== \gettype($record->formatted)) {
            throw new \InvalidArgumentException('SqsHandler accepts only formatted records as a string' . Utils::getRecordMessageForException($record));
        }

        $messageBody = $record->formatted;
        if (\strlen($messageBody) >= static::MAX_MESSAGE_SIZE) {
            $messageBody = Utils::substr($messageBody, 0, static::HEAD_MESSAGE_SIZE);
        }

        $this->client->sendMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageBody' => $messageBody,
        ]);
    }
}
