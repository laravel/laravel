<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event;

use const PHP_EOL;
use function array_key_exists;
use function dirname;
use function sprintf;
use function str_starts_with;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DirectDispatcher implements SubscribableDispatcher
{
    private readonly TypeMap $typeMap;

    /**
     * @var array<class-string, list<Subscriber>>
     */
    private array $subscribers = [];

    /**
     * @var list<Tracer\Tracer>
     */
    private array $tracers = [];

    public function __construct(TypeMap $map)
    {
        $this->typeMap = $map;
    }

    public function registerTracer(Tracer\Tracer $tracer): void
    {
        $this->tracers[] = $tracer;
    }

    /**
     * @throws MapError
     * @throws UnknownSubscriberTypeException
     */
    public function registerSubscriber(Subscriber $subscriber): void
    {
        if (!$this->typeMap->isKnownSubscriberType($subscriber)) {
            throw new UnknownSubscriberTypeException(
                sprintf(
                    'Subscriber "%s" does not implement any known interface - did you forget to register it?',
                    $subscriber::class,
                ),
            );
        }

        $eventClassName = $this->typeMap->map($subscriber);

        if (!array_key_exists($eventClassName, $this->subscribers)) {
            $this->subscribers[$eventClassName] = [];
        }

        $this->subscribers[$eventClassName][] = $subscriber;
    }

    /**
     * @throws Throwable
     * @throws UnknownEventTypeException
     */
    public function dispatch(Event $event): void
    {
        $eventClassName = $event::class;

        if (!$this->typeMap->isKnownEventType($event)) {
            throw new UnknownEventTypeException(
                sprintf(
                    'Unknown event type "%s"',
                    $eventClassName,
                ),
            );
        }

        foreach ($this->tracers as $tracer) {
            try {
                $tracer->trace($event);
                // @codeCoverageIgnoreStart
            } catch (Throwable $t) {
                $this->handleThrowable($t);
            }
            // @codeCoverageIgnoreEnd
        }

        if (!array_key_exists($eventClassName, $this->subscribers)) {
            return;
        }

        foreach ($this->subscribers[$eventClassName] as $subscriber) {
            try {
                /** @phpstan-ignore method.notFound */
                $subscriber->notify($event);
            } catch (Throwable $t) {
                $this->handleThrowable($t);
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function handleThrowable(Throwable $t): void
    {
        if ($this->isThrowableFromThirdPartySubscriber($t)) {
            Facade::emitter()->testRunnerTriggeredPhpunitWarning(
                sprintf(
                    'Exception in third-party event subscriber: %s%s%s',
                    $t->getMessage(),
                    PHP_EOL,
                    $t->getTraceAsString(),
                ),
            );

            return;
        }

        // @codeCoverageIgnoreStart
        throw $t;
        // @codeCoverageIgnoreEnd
    }

    private function isThrowableFromThirdPartySubscriber(Throwable $t): bool
    {
        return !str_starts_with($t->getFile(), dirname(__DIR__, 2));
    }
}
