<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use RollbarNotifier;
use Exception;
use Monolog\Logger;

/**
 * Sends errors to Rollbar
 *
 * If the context data contains a `payload` key, that is used as an array
 * of payload options to RollbarNotifier's report_message/report_exception methods.
 *
 * @author Paul Statezny <paulstatezny@gmail.com>
 */
class RollbarHandler extends AbstractProcessingHandler
{
    /**
     * Rollbar notifier
     *
     * @var RollbarNotifier
     */
    protected $rollbarNotifier;

    /**
     * Records whether any log records have been added since the last flush of the rollbar notifier
     *
     * @var bool
     */
    private $hasRecords = false;

    /**
     * @param RollbarNotifier $rollbarNotifier RollbarNotifier object constructed with valid token
     * @param int             $level           The minimum logging level at which this handler will be triggered
     * @param bool            $bubble          Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(RollbarNotifier $rollbarNotifier, $level = Logger::ERROR, $bubble = true)
    {
        $this->rollbarNotifier = $rollbarNotifier;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Exception) {
            $context = $record['context'];
            $exception = $context['exception'];
            unset($context['exception']);

            $payload = array();
            if (isset($context['payload'])) {
                $payload = $context['payload'];
                unset($context['payload']);
            }

            $this->rollbarNotifier->report_exception($exception, $context, $payload);
        } else {
            $extraData = array(
                'level' => $record['level'],
                'channel' => $record['channel'],
                'datetime' => $record['datetime']->format('U'),
            );

            $context = $record['context'];
            $payload = array();
            if (isset($context['payload'])) {
                $payload = $context['payload'];
                unset($context['payload']);
            }

            $this->rollbarNotifier->report_message(
                $record['message'],
                $record['level_name'],
                array_merge($record['context'], $record['extra'], $extraData),
                $payload
            );
        }

        $this->hasRecords = true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->hasRecords) {
            $this->rollbarNotifier->flush();
            $this->hasRecords = false;
        }
    }
}
