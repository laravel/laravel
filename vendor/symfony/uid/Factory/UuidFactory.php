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

use Symfony\Component\Uid\Exception\LogicException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Uid\UuidV5;
use Symfony\Component\Uid\UuidV7;

class UuidFactory
{
    private string $defaultClass;
    private string $timeBasedClass;
    private string $nameBasedClass;
    private string $randomBasedClass;
    private ?Uuid $timeBasedNode;
    private ?Uuid $nameBasedNamespace;

    public function __construct(string|int $defaultClass = UuidV7::class, string|int $timeBasedClass = UuidV7::class, string|int $nameBasedClass = UuidV5::class, string|int $randomBasedClass = UuidV4::class, Uuid|string|null $timeBasedNode = null, Uuid|string|null $nameBasedNamespace = null)
    {
        if (null !== $timeBasedNode && !$timeBasedNode instanceof Uuid) {
            $timeBasedNode = Uuid::fromString($timeBasedNode);
        }

        if (null !== $nameBasedNamespace) {
            $nameBasedNamespace = $this->getNamespace($nameBasedNamespace);
        }

        $this->defaultClass = is_numeric($defaultClass) ? Uuid::class.'V'.$defaultClass : $defaultClass;
        $this->timeBasedClass = is_numeric($timeBasedClass) ? Uuid::class.'V'.$timeBasedClass : $timeBasedClass;
        $this->nameBasedClass = is_numeric($nameBasedClass) ? Uuid::class.'V'.$nameBasedClass : $nameBasedClass;
        $this->randomBasedClass = is_numeric($randomBasedClass) ? Uuid::class.'V'.$randomBasedClass : $randomBasedClass;
        $this->timeBasedNode = $timeBasedNode;
        $this->nameBasedNamespace = $nameBasedNamespace;
    }

    public function create(): Uuid
    {
        $class = $this->defaultClass;

        return new $class();
    }

    public function randomBased(): RandomBasedUuidFactory
    {
        return new RandomBasedUuidFactory($this->randomBasedClass);
    }

    public function timeBased(Uuid|string|null $node = null): TimeBasedUuidFactory
    {
        $node ??= $this->timeBasedNode;

        if (null !== $node && !$node instanceof Uuid) {
            $node = Uuid::fromString($node);
        }

        return new TimeBasedUuidFactory($this->timeBasedClass, $node);
    }

    /**
     * @throws LogicException When no namespace is defined
     */
    public function nameBased(Uuid|string|null $namespace = null): NameBasedUuidFactory
    {
        $namespace ??= $this->nameBasedNamespace;

        if (null === $namespace) {
            throw new LogicException(\sprintf('A namespace should be defined when using "%s()".', __METHOD__));
        }

        return new NameBasedUuidFactory($this->nameBasedClass, $this->getNamespace($namespace));
    }

    private function getNamespace(Uuid|string $namespace): Uuid
    {
        if ($namespace instanceof Uuid) {
            return $namespace;
        }

        return match ($namespace) {
            'dns' => new UuidV1(Uuid::NAMESPACE_DNS),
            'url' => new UuidV1(Uuid::NAMESPACE_URL),
            'oid' => new UuidV1(Uuid::NAMESPACE_OID),
            'x500' => new UuidV1(Uuid::NAMESPACE_X500),
            default => Uuid::fromString($namespace),
        };
    }
}
