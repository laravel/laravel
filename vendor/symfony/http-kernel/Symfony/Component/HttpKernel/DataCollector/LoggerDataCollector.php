<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * LogDataCollector.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LoggerDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private $logger;

    public function __construct($logger = null)
    {
        if (null !== $logger && $logger instanceof DebugLoggerInterface) {
            $this->logger = $logger;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // everything is done as late as possible
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->logger) {
            $this->data = array(
                'error_count'       => $this->logger->countErrors(),
                'logs'              => $this->sanitizeLogs($this->logger->getLogs()),
                'deprecation_count' => $this->computeDeprecationCount(),
            );
        }
    }

    /**
     * Gets the called events.
     *
     * @return array An array of called events
     *
     * @see TraceableEventDispatcherInterface
     */
    public function countErrors()
    {
        return isset($this->data['error_count']) ? $this->data['error_count'] : 0;
    }

    /**
     * Gets the logs.
     *
     * @return array An array of logs
     */
    public function getLogs()
    {
        return isset($this->data['logs']) ? $this->data['logs'] : array();
    }

    public function countDeprecations()
    {
        return isset($this->data['deprecation_count']) ? $this->data['deprecation_count'] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'logger';
    }

    private function sanitizeLogs($logs)
    {
        foreach ($logs as $i => $log) {
            $logs[$i]['context'] = $this->sanitizeContext($log['context']);
        }

        return $logs;
    }

    private function sanitizeContext($context)
    {
        if (is_array($context)) {
            foreach ($context as $key => $value) {
                $context[$key] = $this->sanitizeContext($value);
            }

            return $context;
        }

        if (is_resource($context)) {
            return sprintf('Resource(%s)', get_resource_type($context));
        }

        if (is_object($context)) {
            return sprintf('Object(%s)', get_class($context));
        }

        return $context;
    }

    private function computeDeprecationCount()
    {
        $count = 0;
        foreach ($this->logger->getLogs() as $log) {
            if (isset($log['context']['type']) && ErrorHandler::TYPE_DEPRECATION === $log['context']['type']) {
                $count++;
            }
        }

        return $count;
    }
}
