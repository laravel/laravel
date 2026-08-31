<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Parser;

use function assert;
use function class_exists;
use function method_exists;
use PHPUnit\Metadata\MetadataCollection;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CachingParser implements Parser
{
    private readonly Parser $reader;

    /**
     * @var array<class-string, MetadataCollection>
     */
    private array $classCache = [];

    /**
     * @var array<non-empty-string, MetadataCollection>
     */
    private array $methodCache = [];

    /**
     * @var array<non-empty-string, MetadataCollection>
     */
    private array $classAndMethodCache = [];

    public function __construct(Parser $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string $className
     */
    public function forClass(string $className): MetadataCollection
    {
        assert(class_exists($className));

        if (isset($this->classCache[$className])) {
            return $this->classCache[$className];
        }

        $this->classCache[$className] = $this->reader->forClass($className);

        return $this->classCache[$className];
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forMethod(string $className, string $methodName): MetadataCollection
    {
        assert(class_exists($className));
        assert(method_exists($className, $methodName));

        $key = $className . '::' . $methodName;

        if (isset($this->methodCache[$key])) {
            return $this->methodCache[$key];
        }

        $this->methodCache[$key] = $this->reader->forMethod($className, $methodName);

        return $this->methodCache[$key];
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forClassAndMethod(string $className, string $methodName): MetadataCollection
    {
        $key = $className . '::' . $methodName;

        if (isset($this->classAndMethodCache[$key])) {
            return $this->classAndMethodCache[$key];
        }

        $this->classAndMethodCache[$key] = $this->forClass($className)->mergeWith(
            $this->forMethod($className, $methodName),
        );

        return $this->classAndMethodCache[$key];
    }
}
