<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Annotation\Parser;

use function array_key_exists;
use PHPUnit\Metadata\AnnotationsAreNotSupportedForInternalClassesException;
use PHPUnit\Metadata\ReflectionException;
use ReflectionClass;
use ReflectionMethod;

/**
 * Reflection information, and therefore DocBlock information, is static within
 * a single PHP process. It is therefore okay to use a Singleton registry here.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Registry
{
    private static ?Registry $instance = null;

    /**
     * @var array<string, DocBlock> indexed by class name
     */
    private array $classDocBlocks = [];

    /**
     * @var array<string, array<string, DocBlock>> indexed by class name and method name
     */
    private array $methodDocBlocks = [];

    public static function getInstance(): self
    {
        return self::$instance ?? self::$instance = new self;
    }

    /**
     * @param class-string $class
     *
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     * @throws ReflectionException
     */
    public function forClassName(string $class): DocBlock
    {
        if (array_key_exists($class, $this->classDocBlocks)) {
            return $this->classDocBlocks[$class];
        }

        try {
            $reflection = new ReflectionClass($class);

            // @codeCoverageIgnoreStart
            /** @phpstan-ignore catch.neverThrown */
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd

        return $this->classDocBlocks[$class] = DocBlock::ofClass($reflection);
    }

    /**
     * @param class-string $classInHierarchy
     *
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     * @throws ReflectionException
     */
    public function forMethod(string $classInHierarchy, string $method): DocBlock
    {
        if (isset($this->methodDocBlocks[$classInHierarchy][$method])) {
            return $this->methodDocBlocks[$classInHierarchy][$method];
        }

        try {
            $reflection = new ReflectionMethod($classInHierarchy, $method);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd

        return $this->methodDocBlocks[$classInHierarchy][$method] = DocBlock::ofMethod($reflection);
    }
}
