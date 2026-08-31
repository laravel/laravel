<?php declare(strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeUnit;

use function assert;
use function count;
use function file;
use function file_exists;
use function is_readable;
use function range;
use function sprintf;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

/**
 * @immutable
 */
abstract readonly class CodeUnit
{
    /**
     * @var non-empty-string
     */
    private string $name;

    /**
     * @var non-empty-string
     */
    private string $sourceFileName;

    /**
     * @var list<int>
     */
    private array $sourceLines;

    /**
     * @param class-string $className
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forClass(string $className): ClassUnit
    {
        self::ensureUserDefinedClass($className);

        $reflector = new ReflectionClass($className);

        return new ClassUnit(
            $className,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param class-string $className
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forClassMethod(string $className, string $methodName): ClassMethodUnit
    {
        self::ensureUserDefinedClass($className);

        $reflector = self::reflectorForClassMethod($className, $methodName);

        return new ClassMethodUnit(
            $className . '::' . $methodName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param non-empty-string $path
     *
     * @throws InvalidCodeUnitException
     */
    public static function forFileWithAbsolutePath(string $path): FileUnit
    {
        self::ensureFileExistsAndIsReadable($path);

        $lines = file($path);

        assert($lines !== false);

        return new FileUnit(
            $path,
            $path,
            range(
                1,
                count($lines),
            ),
        );
    }

    /**
     * @param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forInterface(string $interfaceName): InterfaceUnit
    {
        self::ensureUserDefinedInterface($interfaceName);

        $reflector = new ReflectionClass($interfaceName);

        return new InterfaceUnit(
            $interfaceName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forInterfaceMethod(string $interfaceName, string $methodName): InterfaceMethodUnit
    {
        self::ensureUserDefinedInterface($interfaceName);

        $reflector = self::reflectorForClassMethod($interfaceName, $methodName);

        return new InterfaceMethodUnit(
            $interfaceName . '::' . $methodName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forTrait(string $traitName): TraitUnit
    {
        self::ensureUserDefinedTrait($traitName);

        $reflector = new ReflectionClass($traitName);

        return new TraitUnit(
            $traitName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forTraitMethod(string $traitName, string $methodName): TraitMethodUnit
    {
        self::ensureUserDefinedTrait($traitName);

        $reflector = self::reflectorForClassMethod($traitName, $methodName);

        return new TraitMethodUnit(
            $traitName . '::' . $methodName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param callable-string $functionName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     */
    public static function forFunction(string $functionName): FunctionUnit
    {
        $reflector = self::reflectorForFunction($functionName);

        if (!$reflector->isUserDefined()) {
            throw new InvalidCodeUnitException(
                sprintf(
                    '"%s" is not a user-defined function',
                    $functionName,
                ),
            );
        }

        return new FunctionUnit(
            // @phpstan-ignore argument.type
            $functionName,
            // @phpstan-ignore argument.type
            $reflector->getFileName(),
            range(
                // @phpstan-ignore argument.type
                $reflector->getStartLine(),
                // @phpstan-ignore argument.type
                $reflector->getEndLine(),
            ),
        );
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $sourceFileName
     * @param list<int>        $sourceLines
     */
    private function __construct(string $name, string $sourceFileName, array $sourceLines)
    {
        $this->name           = $name;
        $this->sourceFileName = $sourceFileName;
        $this->sourceLines    = $sourceLines;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function sourceFileName(): string
    {
        return $this->sourceFileName;
    }

    /**
     * @return list<int>
     */
    public function sourceLines(): array
    {
        return $this->sourceLines;
    }

    /**
     * @phpstan-assert-if-true ClassUnit $this
     */
    public function isClass(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true ClassMethodUnit $this
     */
    public function isClassMethod(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true InterfaceUnit $this
     */
    public function isInterface(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true InterfaceMethodUnit $this
     */
    public function isInterfaceMethod(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true TraitUnit $this
     */
    public function isTrait(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true TraitMethodUnit $this
     */
    public function isTraitMethod(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true FunctionUnit $this
     */
    public function isFunction(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true FileUnit $this
     */
    public function isFile(): bool
    {
        return false;
    }

    /**
     * @param non-empty-string $path
     *
     * @throws InvalidCodeUnitException
     */
    private static function ensureFileExistsAndIsReadable(string $path): void
    {
        if (!(file_exists($path) && is_readable($path))) {
            throw new InvalidCodeUnitException(
                sprintf(
                    'File "%s" does not exist or is not readable',
                    $path,
                ),
            );
        }
    }

    /**
     * @param class-string $className
     *
     * @throws InvalidCodeUnitException
     */
    private static function ensureUserDefinedClass(string $className): void
    {
        try {
            $reflector = new ReflectionClass($className);

            if ($reflector->isInterface()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is an interface and not a class',
                        $className,
                    ),
                );
            }

            if ($reflector->isTrait()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is a trait and not a class',
                        $className,
                    ),
                );
            }

            if (!$reflector->isUserDefined()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is not a user-defined class',
                        $className,
                    ),
                );
            }
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     */
    private static function ensureUserDefinedInterface(string $interfaceName): void
    {
        try {
            $reflector = new ReflectionClass($interfaceName);

            if (!$reflector->isInterface()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is not an interface',
                        $interfaceName,
                    ),
                );
            }

            if (!$reflector->isUserDefined()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is not a user-defined interface',
                        $interfaceName,
                    ),
                );
            }
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     */
    private static function ensureUserDefinedTrait(string $traitName): void
    {
        try {
            $reflector = new ReflectionClass($traitName);

            if (!$reflector->isTrait()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is not a trait',
                        $traitName,
                    ),
                );
            }

            // @codeCoverageIgnoreStart
            if (!$reflector->isUserDefined()) {
                throw new InvalidCodeUnitException(
                    sprintf(
                        '"%s" is not a user-defined trait',
                        $traitName,
                    ),
                );
            }
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    private static function reflectorForClassMethod(string $className, string $methodName): ReflectionMethod
    {
        try {
            return new ReflectionMethod($className, $methodName);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param callable-string $functionName
     *
     * @throws ReflectionException
     */
    private static function reflectorForFunction(string $functionName): ReflectionFunction
    {
        try {
            return new ReflectionFunction($functionName);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
