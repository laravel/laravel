<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid\Factory;

use Symfony\Component\Uid\Exception\InvalidArgumentException;
use Symfony\Component\Uid\Exception\LogicException;
use Symfony\Component\Uid\TimeBasedUidInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Uid\UuidV3;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Uid\UuidV5;
use Symfony\Component\Uid\UuidV6;

class MockUuidFactory extends UuidFactory
{
    private \Iterator $sequence;

    /**
     * @param iterable<string|Uuid> $uuids
     */
    public function __construct(
        iterable $uuids,
        private Uuid|string|null $timeBasedNode = null,
        private Uuid|string|null $nameBasedNamespace = null,
    ) {
        $this->sequence = match (true) {
            \is_array($uuids) => new \ArrayIterator($uuids),
            $uuids instanceof \Iterator => $uuids,
            $uuids instanceof \Traversable => new \IteratorIterator($uuids),
        };
    }

    public function create(): Uuid
    {
        if (!$this->sequence->valid()) {
            throw new LogicException('No more UUIDs in sequence.');
        }
        $uuid = $this->sequence->current();
        $this->sequence->next();

        return match (true) {
            $uuid instanceof Uuid => $uuid,
            \is_string($uuid) => Uuid::fromString($uuid),
            default => throw new InvalidArgumentException(\sprintf('Next UUID in sequence is not a valid UUID string or object: "%s" given.', get_debug_type($uuid))),
        };
    }

    public function randomBased(): RandomBasedUuidFactory
    {
        return new class($this->create(...)) extends RandomBasedUuidFactory {
            public function __construct(
                private \Closure $create,
            ) {
            }

            public function create(): UuidV4
            {
                if (!($uuid = ($this->create)()) instanceof UuidV4) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence is not a UuidV4: "%s" given.', get_debug_type($uuid)));
                }

                return $uuid;
            }
        };
    }

    public function timeBased(Uuid|string|null $node = null): TimeBasedUuidFactory
    {
        if (\is_string($node ??= $this->timeBasedNode)) {
            $node = Uuid::fromString($node);
        }

        return new class($this->create(...), $node) extends TimeBasedUuidFactory {
            public function __construct(
                private \Closure $create,
                private ?Uuid $node = null,
            ) {
            }

            public function create(?\DateTimeInterface $time = null): Uuid&TimeBasedUidInterface
            {
                $uuid = ($this->create)();

                if (!($uuid instanceof Uuid && $uuid instanceof TimeBasedUidInterface)) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence is not a Uuid and TimeBasedUidInterface: "%s" given.', get_debug_type($uuid)));
                }

                if (null !== $time && $uuid->getDateTime() !== $time) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence does not match the expected time: "%s" != "%s".', $uuid->getDateTime()->format('@U.uT'), $time->format('@U.uT')));
                }

                if (null !== $this->node && ($uuid instanceof UuidV1 || $uuid instanceof UuidV6) && $uuid->getNode() !== substr($this->node->toRfc4122(), -12)) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence does not match the expected node: "%s" != "%s".', $uuid->getNode(), substr($this->node->toRfc4122(), -12)));
                }

                return $uuid;
            }
        };
    }

    public function nameBased(Uuid|string|null $namespace = null): NameBasedUuidFactory
    {
        if (null === $namespace ??= $this->nameBasedNamespace) {
            throw new LogicException(\sprintf('A namespace should be defined when using "%s()".', __METHOD__));
        }

        return new class($this->create(...), $namespace) extends NameBasedUuidFactory {
            public function __construct(
                private \Closure $create,
                private Uuid|string $namespace,
            ) {
            }

            public function create(string $name): UuidV5|UuidV3
            {
                if (!($uuid = ($this->create)()) instanceof UuidV5 && !$uuid instanceof UuidV3) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence is not a UuidV5 or UuidV3: "%s".', get_debug_type($uuid)));
                }

                $factory = new UuidFactory(nameBasedClass: $uuid::class, nameBasedNamespace: $this->namespace);

                if ($uuid->toRfc4122() !== $expectedUuid = $factory->nameBased()->create($name)->toRfc4122()) {
                    throw new InvalidArgumentException(\sprintf('Next UUID in sequence does not match the expected named UUID: "%s" != "%s".', $uuid->toRfc4122(), $expectedUuid));
                }

                return $uuid;
            }
        };
    }
}
