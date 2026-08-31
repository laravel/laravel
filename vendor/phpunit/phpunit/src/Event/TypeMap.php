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

use function array_key_exists;
use function class_exists;
use function class_implements;
use function in_array;
use function interface_exists;
use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TypeMap
{
    /**
     * @var array<class-string, class-string>
     */
    private array $mapping = [];

    /**
     * @param class-string $subscriberInterface
     * @param class-string $eventClass
     *
     * @throws EventAlreadyAssignedException
     * @throws InvalidEventException
     * @throws InvalidSubscriberException
     * @throws SubscriberTypeAlreadyRegisteredException
     * @throws UnknownEventException
     * @throws UnknownSubscriberException
     */
    public function addMapping(string $subscriberInterface, string $eventClass): void
    {
        $this->ensureSubscriberInterfaceExists($subscriberInterface);
        $this->ensureSubscriberInterfaceExtendsInterface($subscriberInterface);
        $this->ensureEventClassExists($eventClass);
        $this->ensureEventClassImplementsEventInterface($eventClass);
        $this->ensureSubscriberWasNotAlreadyRegistered($subscriberInterface);
        $this->ensureEventWasNotAlreadyAssigned($eventClass);

        $this->mapping[$subscriberInterface] = $eventClass;
    }

    public function isKnownSubscriberType(Subscriber $subscriber): bool
    {
        foreach (class_implements($subscriber) as $interface) {
            if (array_key_exists($interface, $this->mapping)) {
                return true;
            }
        }

        return false;
    }

    public function isKnownEventType(Event $event): bool
    {
        return in_array($event::class, $this->mapping, true);
    }

    /**
     * @throws MapError
     *
     * @return class-string
     */
    public function map(Subscriber $subscriber): string
    {
        foreach (class_implements($subscriber) as $interface) {
            if (array_key_exists($interface, $this->mapping)) {
                return $this->mapping[$interface];
            }
        }

        throw new MapError(
            sprintf(
                'Subscriber "%s" does not implement a known interface',
                $subscriber::class,
            ),
        );
    }

    /**
     * @param class-string $subscriberInterface
     *
     * @throws UnknownSubscriberException
     */
    private function ensureSubscriberInterfaceExists(string $subscriberInterface): void
    {
        if (!interface_exists($subscriberInterface)) {
            throw new UnknownSubscriberException(
                sprintf(
                    'Subscriber "%s" does not exist or is not an interface',
                    $subscriberInterface,
                ),
            );
        }
    }

    /**
     * @param class-string $eventClass
     *
     * @throws UnknownEventException
     */
    private function ensureEventClassExists(string $eventClass): void
    {
        if (!class_exists($eventClass)) {
            throw new UnknownEventException(
                sprintf(
                    'Event class "%s" does not exist',
                    $eventClass,
                ),
            );
        }
    }

    /**
     * @param class-string $subscriberInterface
     *
     * @throws InvalidSubscriberException
     */
    private function ensureSubscriberInterfaceExtendsInterface(string $subscriberInterface): void
    {
        if (!in_array(Subscriber::class, class_implements($subscriberInterface), true)) {
            throw new InvalidSubscriberException(
                sprintf(
                    'Subscriber "%s" does not extend Subscriber interface',
                    $subscriberInterface,
                ),
            );
        }
    }

    /**
     * @param class-string $eventClass
     *
     * @throws InvalidEventException
     */
    private function ensureEventClassImplementsEventInterface(string $eventClass): void
    {
        if (!in_array(Event::class, class_implements($eventClass), true)) {
            throw new InvalidEventException(
                sprintf(
                    'Event "%s" does not implement Event interface',
                    $eventClass,
                ),
            );
        }
    }

    /**
     * @param class-string $subscriberInterface
     *
     * @throws SubscriberTypeAlreadyRegisteredException
     */
    private function ensureSubscriberWasNotAlreadyRegistered(string $subscriberInterface): void
    {
        if (array_key_exists($subscriberInterface, $this->mapping)) {
            throw new SubscriberTypeAlreadyRegisteredException(
                sprintf(
                    'Subscriber type "%s" already registered',
                    $subscriberInterface,
                ),
            );
        }
    }

    /**
     * @param class-string $eventClass
     *
     * @throws EventAlreadyAssignedException
     */
    private function ensureEventWasNotAlreadyAssigned(string $eventClass): void
    {
        if (in_array($eventClass, $this->mapping, true)) {
            throw new EventAlreadyAssignedException(
                sprintf(
                    'Event "%s" already assigned',
                    $eventClass,
                ),
            );
        }
    }
}
