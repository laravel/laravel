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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * TimeDataCollector.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TimeDataCollector extends DataCollector implements LateDataCollectorInterface
{
    protected $kernel;
    protected $stopwatch;

    public function __construct(KernelInterface $kernel = null, $stopwatch = null)
    {
        $this->kernel = $kernel;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (null !== $this->kernel) {
            $startTime = $this->kernel->getStartTime();
        } else {
            $startTime = $request->server->get('REQUEST_TIME_FLOAT', $request->server->get('REQUEST_TIME'));
        }

        $this->data = array(
            'token'      => $response->headers->get('X-Debug-Token'),
            'start_time' => $startTime * 1000,
            'events'     => array(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->stopwatch && isset($this->data['token'])) {
            $this->setEvents($this->stopwatch->getSectionEvents($this->data['token']));
        }
        unset($this->data['token']);
    }

    /**
     * Sets the request events.
     *
     * @param array $events The request events
     */
    public function setEvents(array $events)
    {
        foreach ($events as $event) {
            $event->ensureStopped();
        }

        $this->data['events'] = $events;
    }

    /**
     * Gets the request events.
     *
     * @return array The request events
     */
    public function getEvents()
    {
        return $this->data['events'];
    }

    /**
     * Gets the request elapsed time.
     *
     * @return float The elapsed time
     */
    public function getDuration()
    {
        if (!isset($this->data['events']['__section__'])) {
            return 0;
        }

        $lastEvent = $this->data['events']['__section__'];

        return $lastEvent->getOrigin() + $lastEvent->getDuration() - $this->getStartTime();
    }

    /**
     * Gets the initialization time.
     *
     * This is the time spent until the beginning of the request handling.
     *
     * @return float The elapsed time
     */
    public function getInitTime()
    {
        if (!isset($this->data['events']['__section__'])) {
            return 0;
        }

        return $this->data['events']['__section__']->getOrigin() - $this->getStartTime();
    }

    /**
     * Gets the request time.
     *
     * @return int     The time
     */
    public function getStartTime()
    {
        return $this->data['start_time'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'time';
    }
}
