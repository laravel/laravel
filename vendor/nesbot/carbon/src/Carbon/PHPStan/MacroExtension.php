<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\PHPStan;

use Carbon\CarbonInterface;
use Carbon\FactoryImmutable;
use Closure;
use InvalidArgumentException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ClosureTypeFactory;
use ReflectionFunction;
use ReflectionMethod;
use stdClass;
use Throwable;

/**
 * Class MacroExtension.
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class MacroExtension implements MethodsClassReflectionExtension
{
    /**
     * @var ReflectionProvider
     */
    protected $reflectionProvider;

    /**
     * @var ClosureTypeFactory
     */
    protected $closureTypeFactory;

    /**
     * Extension constructor.
     *
     * @param ReflectionProvider $reflectionProvider
     * @param ClosureTypeFactory $closureTypeFactory
     */
    public function __construct(
        ReflectionProvider $reflectionProvider,
        ClosureTypeFactory $closureTypeFactory
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->closureTypeFactory = $closureTypeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (
            $classReflection->getName() !== CarbonInterface::class &&
            !$classReflection->isSubclassOf(CarbonInterface::class)
        ) {
            return false;
        }

        $className = $classReflection->getName();

        return \is_callable([$className, 'hasMacro']) &&
            $className::hasMacro($methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $macros = FactoryImmutable::getDefaultInstance()->getSettings()['macros'] ?? [];
        $macro = $macros[$methodName] ?? throw new InvalidArgumentException("Macro '$methodName' not found");
        $static = false;
        $final = false;
        $deprecated = false;
        $docComment = null;

        if (\is_array($macro) && \count($macro) === 2 && \is_string($macro[1])) {
            \assert($macro[1] !== '');

            $reflection = new ReflectionMethod($macro[0], $macro[1]);
            $closure = \is_object($macro[0]) ? $reflection->getClosure($macro[0]) : $reflection->getClosure();

            $static = $reflection->isStatic();
            $final = $reflection->isFinal();
            $deprecated = $reflection->isDeprecated();
            $docComment = $reflection->getDocComment() ?: null;
        } elseif (\is_string($macro)) {
            $reflection = new ReflectionFunction($macro);
            $closure = $reflection->getClosure();
            $deprecated = $reflection->isDeprecated();
            $docComment = $reflection->getDocComment() ?: null;
        } elseif ($macro instanceof Closure) {
            $closure = $macro;

            try {
                $boundClosure = Closure::bind($closure, new stdClass());
                $static = (!$boundClosure || (new ReflectionFunction($boundClosure))->getClosureThis() === null);
            } catch (Throwable) {
                $static = true;
            }

            $reflection = new ReflectionFunction($macro);
            $deprecated = $reflection->isDeprecated();
            $docComment = $reflection->getDocComment() ?: null;
        }

        if (!isset($closure)) {
            throw new InvalidArgumentException('Could not create reflection from the spec given'); // @codeCoverageIgnore
        }

        $closureType = $this->closureTypeFactory->fromClosureObject($closure);

        return new MacroMethodReflection(
            $classReflection,
            $methodName,
            $closureType,
            $static,
            $final,
            $deprecated,
            $docComment,
        );
    }
}
