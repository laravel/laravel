<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Logger;
use Gelf\Message;

/**
 * Serializes a log message to GELF
 * @see http://www.graylog2.org/about/gelf
 *
 * @author Matt Lehner <mlehner@gmail.com>
 */
class GelfMessageFormatter extends NormalizerFormatter
{
    const MAX_LENGTH = 32766;

    /**
     * @var string the name of the system for the Gelf log message
     */
    protected $systemName;

    /**
     * @var string a prefix for 'extra' fields from the Monolog record (optional)
     */
    protected $extraPrefix;

    /**
     * @var string a prefix for 'context' fields from the Monolog record (optional)
     */
    protected $contextPrefix;

    /**
     * Translates Monolog log levels to Graylog2 log priorities.
     */
    private $logLevels = array(
        Logger::DEBUG     => 7,
        Logger::INFO      => 6,
        Logger::NOTICE    => 5,
        Logger::WARNING   => 4,
        Logger::ERROR     => 3,
        Logger::CRITICAL  => 2,
        Logger::ALERT     => 1,
        Logger::EMERGENCY => 0,
    );

    public function __construct($systemName = null, $extraPrefix = null, $contextPrefix = 'ctxt_')
    {
        parent::__construct('U.u');

        $this->systemName = $systemName ?: gethostname();

        $this->extraPrefix = $extraPrefix;
        $this->contextPrefix = $contextPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $record = parent::format($record);

        if (!isset($record['datetime'], $record['message'], $record['level'])) {
            throw new \InvalidArgumentException('The record should at least contain datetime, message and level keys, '.var_export($record, true).' given');
        }

        $message = new Message();
        $message
            ->setTimestamp($record['datetime'])
            ->setShortMessage((string) $record['message'])
            ->setHost($this->systemName)
            ->setLevel($this->logLevels[$record['level']]);

        // start count with message length + system name length + 200 for padding / metadata
        $len = 200 + strlen((string) $record['message']) + strlen($this->systemName);

        if ($len > self::MAX_LENGTH) {
            $message->setShortMessage(substr($record['message'], 0, self::MAX_LENGTH - 200));

            return $message;
        }

        if (isset($record['channel'])) {
            $message->setFacility($record['channel']);
            $len += strlen($record['channel']);
        }
        if (isset($record['extra']['line'])) {
            $message->setLine($record['extra']['line']);
            $len += 10;
            unset($record['extra']['line']);
        }
        if (isset($record['extra']['file'])) {
            $message->setFile($record['extra']['file']);
            $len += strlen($record['extra']['file']);
            unset($record['extra']['file']);
        }

        foreach ($record['extra'] as $key => $val) {
            $val = is_scalar($val) ? $val : $this->toJson($val);
            $len += strlen($this->extraPrefix . $key . $val);
            if ($len > self::MAX_LENGTH) {
                $message->setAdditional($this->extraPrefix . $key, substr($val, 0, self::MAX_LENGTH - $len));
                break;
            }
            $message->setAdditional($this->extraPrefix . $key, $val);
        }

        foreach ($record['context'] as $key => $val) {
            $val = is_scalar($val) ? $val : $this->toJson($val);
            $len += strlen($this->contextPrefix . $key . $val);
            if ($len > self::MAX_LENGTH) {
                $message->setAdditional($this->contextPrefix . $key, substr($val, 0, self::MAX_LENGTH - $len));
                break;
            }
            $message->setAdditional($this->contextPrefix . $key, $val);
        }

        if (null === $message->getFile() && isset($record['context']['exception']['file'])) {
            if (preg_match("/^(.+):([0-9]+)$/", $record['context']['exception']['file'], $matches)) {
                $message->setFile($matches[1]);
                $message->setLine($matches[2]);
            }
        }

        return $message;
    }
}
