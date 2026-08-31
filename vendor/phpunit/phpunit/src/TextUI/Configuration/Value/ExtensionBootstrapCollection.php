<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<non-negative-int, ExtensionBootstrap>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class ExtensionBootstrapCollection implements IteratorAggregate
{
    /**
     * @var list<ExtensionBootstrap>
     */
    private array $extensionBootstraps;

    /**
     * @param list<ExtensionBootstrap> $extensionBootstraps
     */
    public static function fromArray(array $extensionBootstraps): self
    {
        return new self(...$extensionBootstraps);
    }

    private function __construct(ExtensionBootstrap ...$extensionBootstraps)
    {
        $this->extensionBootstraps = $extensionBootstraps;
    }

    /**
     * @return list<ExtensionBootstrap>
     */
    public function asArray(): array
    {
        return $this->extensionBootstraps;
    }

    public function getIterator(): ExtensionBootstrapCollectionIterator
    {
        return new ExtensionBootstrapCollectionIterator($this);
    }
}
