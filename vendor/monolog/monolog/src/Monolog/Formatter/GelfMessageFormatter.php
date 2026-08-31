<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Level;
use Gelf\Message;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Serializes a log message to GELF
 * @see http://docs.graylog.org/en/latest/pages/gelf.html
 *
 * @author Matt Lehner <mlehner@gmail.com>
 */
class GelfMessageFormatter extends NormalizerFormatter
{
    protected const DEFAULT_MAX_LENGTH = 32766;

    /**
     * @var string the name of the system for the Gelf log message
     */
    protected string $systemName;

    /**
     * @var string a prefix for 'extra' fields from the Monolog record (optional)
     */
    protected string $extraPrefix;

    /**
     * @var string a prefix for 'context' fields from the Monolog record (optional)
     */
    protected string $contextPrefix;

    /**
     * @var int max length per field
     */
    protected int $maxLength;

    /**
     * Translates Monolog log levels to Graylog2 log priorities.
     */
    private function getGraylog2Priority(Level $level): int
    {
        return match ($level) {
            Level::Debug     => 7,
            Level::Info      => 6,
            Level::Notice    => 5,
            Level::Warning   => 4,
            Level::Error     => 3,
            Level::Critical  => 2,
            Level::Alert     => 1,
            Level::Emergency => 0,
        };
    }

    /**
     * @throws \RuntimeException
     */
    public function __construct(?string $systemName = null, ?string $extraPrefix = null, string $contextPrefix = 'ctxt_', ?int $maxLength = null)
    {
        if (!class_exists(Message::class)) {
            throw new \RuntimeException('Composer package graylog2/gelf-php is required to use Monolog\'s GelfMessageFormatter');
        }

        parent::__construct('U.u');

        $this->systemName = (null === $systemName || $systemName === '') ? (string) gethostname() : $systemName;

        $this->extraPrefix = null === $extraPrefix ? '' : $extraPrefix;
        $this->contextPrefix = $contextPrefix;
        $this->maxLength = null === $maxLength ? self::DEFAULT_MAX_LENGTH : $maxLength;
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record): Message
    {
        $context = $extra = [];
        if (isset($record->context)) {
            /** @var array<array<mixed>|bool|float|int|string|null> $context */
            $context = parent::normalize($record->context);
        }
        if (isset($record->extra)) {
            /** @var array<array<mixed>|bool|float|int|string|null> $extra */
            $extra = parent::normalize($record->extra);
        }

        $message = new Message();
        $message
            ->setTimestamp($record->datetime)
            ->setShortMessage($record->message)
            ->setHost($this->systemName)
            ->setLevel($this->getGraylog2Priority($record->level));

        // message length + system name length + 200 for padding / metadata
        $len = 200 + \strlen($record->message) + \strlen($this->systemName);

        if ($len > $this->maxLength) {
            $message->setShortMessage(Utils::substr($record->message, 0, $this->maxLength));
        }

        if (isset($record->channel)) {
            $message->setAdditional('facility', $record->channel);
        }

        foreach ($extra as $key => $val) {
            $key = (string) preg_replace('#[^\w.-]#', '-', (string) $key);
            $val = \is_bool($val) ? ($val ? 1 : 0) : $val;
            $val = \is_scalar($val) || null === $val ? $val : $this->toJson($val);
            $len = \strlen($this->extraPrefix . $key . $val);
            if ($len > $this->maxLength) {
                $message->setAdditional($this->extraPrefix . $key, Utils::substr((string) $val, 0, $this->maxLength));

                continue;
            }
            $message->setAdditional($this->extraPrefix . $key, $val);
        }

        foreach ($context as $key => $val) {
            $key = (string) preg_replace('#[^\w.-]#', '-', (string) $key);
            $val = \is_bool($val) ? ($val ? 1 : 0) : $val;
            $val = \is_scalar($val) || null === $val ? $val : $this->toJson($val);
            $len = \strlen($this->contextPrefix . $key . $val);
            if ($len > $this->maxLength) {
                $message->setAdditional($this->contextPrefix . $key, Utils::substr((string) $val, 0, $this->maxLength));

                continue;
            }
            $message->setAdditional($this->contextPrefix . $key, $val);
        }

        if (!$message->hasAdditional('file') && isset($context['exception']['file'])) {
            if (1 === preg_match("/^(.+):([0-9]+)$/", $context['exception']['file'], $matches)) {
                $message->setAdditional('file', $matches[1]);
                $message->setAdditional('line', $matches[2]);
            }
        }

        return $message;
    }
}
