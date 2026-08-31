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
final readonly class ParserChain implements Parser
{
    private Parser $attributeReader;
    private Parser $annotationReader;

    public function __construct(Parser $attributeReader, Parser $annotationReader)
    {
        $this->attributeReader  = $attributeReader;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param class-string $className
     */
    public function forClass(string $className): MetadataCollection
    {
        assert(class_exists($className));

        $metadata = $this->attributeReader->forClass($className);

        if (!$metadata->isEmpty()) {
            return $metadata;
        }

        return $this->annotationReader->forClass($className);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forMethod(string $className, string $methodName): MetadataCollection
    {
        assert(class_exists($className));
        assert(method_exists($className, $methodName));

        $metadata = $this->attributeReader->forMethod($className, $methodName);

        if (!$metadata->isEmpty()) {
            return $metadata;
        }

        return $this->annotationReader->forMethod($className, $methodName);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forClassAndMethod(string $className, string $methodName): MetadataCollection
    {
        return $this->forClass($className)->mergeWith(
            $this->forMethod($className, $methodName),
        );
    }
}
