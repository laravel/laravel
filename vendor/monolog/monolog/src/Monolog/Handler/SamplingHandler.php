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

use Closure;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Sampling handler
 *
 * A sampled event stream can be useful for logging high frequency events in
 * a production environment where you only need an idea of what is happening
 * and are not concerned with capturing every occurrence. Since the decision to
 * handle or not handle a particular event is determined randomly, the
 * resulting sampled log is not guaranteed to contain 1/N of the events that
 * occurred in the application, but based on the Law of large numbers, it will
 * tend to be close to this ratio with a large number of attempts.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @author Kunal Mehta <legoktm@gmail.com>
 */
class SamplingHandler extends AbstractHandler implements ProcessableHandlerInterface, FormattableHandlerInterface
{
    use ProcessableHandlerTrait;

    /**
     * Handler or factory Closure($record, $this)
     *
     * @phpstan-var (Closure(LogRecord|null, HandlerInterface): HandlerInterface)|HandlerInterface
     */
    protected Closure|HandlerInterface $handler;

    protected int $factor;

    /**
     * @phpstan-param (Closure(LogRecord|null, HandlerInterface): HandlerInterface)|HandlerInterface $handler
     *
     * @param Closure|HandlerInterface $handler Handler or factory Closure($record|null, $samplingHandler).
     * @param int                      $factor  Sample factor (e.g. 10 means every ~10th record is sampled)
     */
    public function __construct(Closure|HandlerInterface $handler, int $factor)
    {
        parent::__construct();
        $this->handler = $handler;
        $this->factor = $factor;
    }

    public function isHandling(LogRecord $record): bool
    {
        return $this->getHandler($record)->isHandling($record);
    }

    public function handle(LogRecord $record): bool
    {
        if ($this->isHandling($record) && mt_rand(1, $this->factor) === 1) {
            if (\count($this->processors) > 0) {
                $record = $this->processRecord($record);
            }

            $this->getHandler($record)->handle($record);
        }

        return false === $this->bubble;
    }

    /**
     * Return the nested handler
     *
     * If the handler was provided as a factory, this will trigger the handler's instantiation.
     */
    public function getHandler(LogRecord|null $record = null): HandlerInterface
    {
        if (!$this->handler instanceof HandlerInterface) {
            $handler = ($this->handler)($record, $this);
            if (!$handler instanceof HandlerInterface) {
                throw new \RuntimeException("The factory Closure should return a HandlerInterface");
            }
            $this->handler = $handler;
        }

        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        $handler = $this->getHandler();
        if ($handler instanceof FormattableHandlerInterface) {
            $handler->setFormatter($formatter);

            return $this;
        }

        throw new \UnexpectedValueException('The nested handler of type '.\get_class($handler).' does not support formatters.');
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): FormatterInterface
    {
        $handler = $this->getHandler();
        if ($handler instanceof FormattableHandlerInterface) {
            return $handler->getFormatter();
        }

        throw new \UnexpectedValueException('The nested handler of type '.\get_class($handler).' does not support formatters.');
    }
}
