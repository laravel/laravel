<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Api;

use function assert;
use function class_exists;
use function count;
use function interface_exists;
use function sprintf;
use function str_starts_with;
use function trait_exists;
use PHPUnit\Framework\CodeCoverageException;
use PHPUnit\Framework\InvalidCoversTargetException;
use PHPUnit\Metadata\Covers;
use PHPUnit\Metadata\CoversClass;
use PHPUnit\Metadata\CoversDefaultClass;
use PHPUnit\Metadata\CoversFunction;
use PHPUnit\Metadata\CoversMethod;
use PHPUnit\Metadata\CoversTrait;
use PHPUnit\Metadata\Parser\Registry;
use PHPUnit\Metadata\Uses;
use PHPUnit\Metadata\UsesClass;
use PHPUnit\Metadata\UsesDefaultClass;
use PHPUnit\Metadata\UsesFunction;
use PHPUnit\Metadata\UsesMethod;
use PHPUnit\Metadata\UsesTrait;
use ReflectionClass;
use SebastianBergmann\CodeUnit\CodeUnitCollection;
use SebastianBergmann\CodeUnit\Exception as CodeUnitException;
use SebastianBergmann\CodeUnit\InvalidCodeUnitException;
use SebastianBergmann\CodeUnit\Mapper;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CodeCoverage
{
    /**
     * @var array<class-string, non-empty-list<class-string>>
     */
    private array $withParents = [];

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @throws CodeCoverageException
     *
     * @return array<string,list<int>>|false
     */
    public function linesToBeCovered(string $className, string $methodName): array|false
    {
        if (!$this->shouldCodeCoverageBeCollectedFor($className, $methodName)) {
            return false;
        }

        $metadataForClass = Registry::parser()->forClass($className);
        $classShortcut    = null;

        if ($metadataForClass->isCoversDefaultClass()->isNotEmpty()) {
            if (count($metadataForClass->isCoversDefaultClass()) > 1) {
                throw new CodeCoverageException(
                    sprintf(
                        'More than one @coversDefaultClass annotation for class or interface "%s"',
                        $className,
                    ),
                );
            }

            $metadata = $metadataForClass->isCoversDefaultClass()->asArray()[0];

            assert($metadata instanceof CoversDefaultClass);

            $classShortcut = $metadata->className();
        }

        $codeUnits = CodeUnitCollection::fromList();
        $mapper    = new Mapper;

        foreach (Registry::parser()->forClassAndMethod($className, $methodName) as $metadata) {
            if (!$metadata->isCoversClass() && !$metadata->isCoversTrait() && !$metadata->isCoversMethod() && !$metadata->isCoversFunction() && !$metadata->isCovers()) {
                continue;
            }

            /** @phpstan-ignore booleanOr.alwaysTrue */
            assert($metadata instanceof CoversClass || $metadata instanceof CoversTrait || $metadata instanceof CoversMethod || $metadata instanceof CoversFunction || $metadata instanceof Covers);

            if ($metadata->isCoversClass() || $metadata->isCoversTrait() || $metadata->isCoversMethod() || $metadata->isCoversFunction()) {
                $codeUnits = $codeUnits->mergeWith($this->mapToCodeUnits($metadata));
            } elseif ($metadata->isCovers()) {
                assert($metadata instanceof Covers);

                $target = $metadata->target();

                if (interface_exists($target)) {
                    throw new InvalidCoversTargetException(
                        sprintf(
                            'Trying to @cover interface "%s".',
                            $target,
                        ),
                    );
                }

                if ($classShortcut !== null && str_starts_with($target, '::')) {
                    $target = $classShortcut . $target;
                }

                try {
                    $codeUnits = $codeUnits->mergeWith($mapper->stringToCodeUnits($target));
                } catch (InvalidCodeUnitException $e) {
                    throw new InvalidCoversTargetException(
                        sprintf(
                            '"@covers %s" is invalid',
                            $target,
                        ),
                        $e->getCode(),
                        $e,
                    );
                }
            }
        }

        return $mapper->codeUnitsToSourceLines($codeUnits);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     *
     * @throws CodeCoverageException
     *
     * @return array<string,list<int>>
     */
    public function linesToBeUsed(string $className, string $methodName): array
    {
        $metadataForClass = Registry::parser()->forClass($className);
        $classShortcut    = null;

        if ($metadataForClass->isUsesDefaultClass()->isNotEmpty()) {
            if (count($metadataForClass->isUsesDefaultClass()) > 1) {
                throw new CodeCoverageException(
                    sprintf(
                        'More than one @usesDefaultClass annotation for class or interface "%s"',
                        $className,
                    ),
                );
            }

            $metadata = $metadataForClass->isUsesDefaultClass()->asArray()[0];

            assert($metadata instanceof UsesDefaultClass);

            $classShortcut = $metadata->className();
        }

        $codeUnits = CodeUnitCollection::fromList();
        $mapper    = new Mapper;

        foreach (Registry::parser()->forClassAndMethod($className, $methodName) as $metadata) {
            if (!$metadata->isUsesClass() && !$metadata->isUsesTrait() && !$metadata->isUsesMethod() && !$metadata->isUsesFunction() && !$metadata->isUses()) {
                continue;
            }

            /** @phpstan-ignore booleanOr.alwaysTrue */
            assert($metadata instanceof UsesClass || $metadata instanceof UsesTrait || $metadata instanceof UsesMethod || $metadata instanceof UsesFunction || $metadata instanceof Uses);

            if ($metadata->isUsesClass() || $metadata->isUsesTrait() || $metadata->isUsesMethod() || $metadata->isUsesFunction()) {
                $codeUnits = $codeUnits->mergeWith($this->mapToCodeUnits($metadata));
            } elseif ($metadata->isUses()) {
                assert($metadata instanceof Uses);

                $target = $metadata->target();

                if ($classShortcut !== null && str_starts_with($target, '::')) {
                    $target = $classShortcut . $target;
                }

                try {
                    $codeUnits = $codeUnits->mergeWith($mapper->stringToCodeUnits($target));
                } catch (InvalidCodeUnitException $e) {
                    throw new InvalidCoversTargetException(
                        sprintf(
                            '"@uses %s" is invalid',
                            $target,
                        ),
                        $e->getCode(),
                        $e,
                    );
                }
            }
        }

        return $mapper->codeUnitsToSourceLines($codeUnits);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function shouldCodeCoverageBeCollectedFor(string $className, string $methodName): bool
    {
        $metadataForClass  = Registry::parser()->forClass($className);
        $metadataForMethod = Registry::parser()->forMethod($className, $methodName);

        if ($metadataForMethod->isCoversNothing()->isNotEmpty()) {
            return false;
        }

        if ($metadataForMethod->isCovers()->isNotEmpty() ||
            $metadataForMethod->isCoversClass()->isNotEmpty() ||
            $metadataForMethod->isCoversFunction()->isNotEmpty()) {
            return true;
        }

        if ($metadataForClass->isCoversNothing()->isNotEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * @throws InvalidCoversTargetException
     */
    private function mapToCodeUnits(CoversClass|CoversFunction|CoversMethod|CoversTrait|UsesClass|UsesFunction|UsesMethod|UsesTrait $metadata): CodeUnitCollection
    {
        $mapper = new Mapper;
        $names  = $this->names($metadata);

        try {
            if (count($names) === 1) {
                return $mapper->stringToCodeUnits($names[0]);
            }

            $codeUnits = CodeUnitCollection::fromList();

            foreach ($names as $name) {
                $codeUnits = $codeUnits->mergeWith(
                    $mapper->stringToCodeUnits($name),
                );
            }

            return $codeUnits;
        } catch (CodeUnitException $e) {
            throw new InvalidCoversTargetException(
                sprintf(
                    '%s is not a valid target for code coverage',
                    $metadata->asStringForCodeUnitMapper(),
                ),
                $e->getCode(),
                $e,
            );
        }
    }

    /**
     * @throws InvalidCoversTargetException
     *
     * @return non-empty-list<non-empty-string>
     */
    private function names(CoversClass|CoversFunction|CoversMethod|CoversTrait|UsesClass|UsesFunction|UsesMethod|UsesTrait $metadata): array
    {
        $name  = $metadata->asStringForCodeUnitMapper();
        $names = [$name];

        if ($metadata->isCoversClass() || $metadata->isUsesClass()) {
            if (isset($this->withParents[$name])) {
                return $this->withParents[$name];
            }

            if (interface_exists($name)) {
                throw new InvalidCoversTargetException(
                    sprintf(
                        'Interface "%s" is not a valid target for code coverage',
                        $name,
                    ),
                );
            }

            if (!(class_exists($name) || trait_exists($name))) {
                throw new InvalidCoversTargetException(
                    sprintf(
                        '"%s" is not a valid target for code coverage',
                        $name,
                    ),
                );
            }

            assert(class_exists($names[0]) || trait_exists($names[0]));

            $reflector = new ReflectionClass($name);

            while ($reflector = $reflector->getParentClass()) {
                if (!$reflector->isUserDefined()) {
                    break;
                }

                $names[] = $reflector->getName();
            }

            $this->withParents[$name] = $names;
        }

        return $names;
    }
}
