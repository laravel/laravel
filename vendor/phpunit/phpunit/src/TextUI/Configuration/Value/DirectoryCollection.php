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
use Countable;
use IteratorAggregate;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 *
 * @template-implements IteratorAggregate<non-negative-int, Directory>
 */
final readonly class DirectoryCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<Directory>
     */
    private array $directories;

    /**
     * @param list<Directory> $directories
     */
    public static function fromArray(array $directories): self
    {
        return new self(...$directories);
    }

    private function __construct(Directory ...$directories)
    {
        $this->directories = $directories;
    }

    /**
     * @return list<Directory>
     */
    public function asArray(): array
    {
        return $this->directories;
    }

    public function count(): int
    {
        return count($this->directories);
    }

    public function getIterator(): DirectoryCollectionIterator
    {
        return new DirectoryCollectionIterator($this);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
