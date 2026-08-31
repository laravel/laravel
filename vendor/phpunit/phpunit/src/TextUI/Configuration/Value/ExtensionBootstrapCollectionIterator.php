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

use function count;
use Iterator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @template-implements Iterator<non-negative-int, ExtensionBootstrap>
 */
final class ExtensionBootstrapCollectionIterator implements Iterator
{
    /**
     * @var list<ExtensionBootstrap>
     */
    private readonly array $extensionBootstraps;

    /**
     * @var non-negative-int
     */
    private int $position = 0;

    public function __construct(ExtensionBootstrapCollection $extensionBootstraps)
    {
        $this->extensionBootstraps = $extensionBootstraps->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->extensionBootstraps);
    }

    /**
     * @return non-negative-int
     */
    public function key(): int
    {
        return $this->position;
    }

    public function current(): ExtensionBootstrap
    {
        return $this->extensionBootstraps[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
