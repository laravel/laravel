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

use Gelf\PublisherInterface;
use Monolog\Level;
use Monolog\Formatter\GelfMessageFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Handler to send messages to a Graylog2 (http://www.graylog2.org) server
 *
 * @author Matt Lehner <mlehner@gmail.com>
 * @author Benjamin Zikarsky <benjamin@zikarsky.de>
 */
class GelfHandler extends AbstractProcessingHandler
{
    /**
     * @var PublisherInterface the publisher object that sends the message to the server
     */
    protected PublisherInterface $publisher;

    /**
     * @param PublisherInterface $publisher a gelf publisher object
     */
    public function __construct(PublisherInterface $publisher, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->publisher = $publisher;
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->publisher->publish($record->formatted);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new GelfMessageFormatter();
    }
}
