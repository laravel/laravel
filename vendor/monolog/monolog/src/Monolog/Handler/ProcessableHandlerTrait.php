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

use Monolog\ResettableInterface;
use Monolog\Processor\ProcessorInterface;
use Monolog\LogRecord;

/**
 * Helper trait for implementing ProcessableInterface
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
trait ProcessableHandlerTrait
{
    /**
     * @var callable[]
     * @phpstan-var array<(callable(LogRecord): LogRecord)|ProcessorInterface>
     */
    protected array $processors = [];

    /**
     * @inheritDoc
     */
    public function pushProcessor(callable $callback): HandlerInterface
    {
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function popProcessor(): callable
    {
        if (\count($this->processors) === 0) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    protected function processRecord(LogRecord $record): LogRecord
    {
        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }

        return $record;
    }

    protected function resetProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }
}
