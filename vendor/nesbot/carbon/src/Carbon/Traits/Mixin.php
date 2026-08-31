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

namespace Carbon\Traits;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Closure;
use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Throwable;

/**
 * Trait Mixin.
 *
 * Allows mixing in entire classes with multiple macros.
 */
trait Mixin
{
    /**
     * Stack of macro instance contexts.
     */
    protected static array $macroContextStack = [];

    /**
     * Mix another object into the class.
     *
     * @example
     * ```
     * Carbon::mixin(new class {
     *   public function addMoon() {
     *     return function () {
     *       return $this->addDays(30);
     *     };
     *   }
     *   public function subMoon() {
     *     return function () {
     *       return $this->subDays(30);
     *     };
     *   }
     * });
     * $fullMoon = Carbon::create('2018-12-22');
     * $nextFullMoon = $fullMoon->addMoon();
     * $blackMoon = Carbon::create('2019-01-06');
     * $previousBlackMoon = $blackMoon->subMoon();
     * echo "$nextFullMoon\n";
     * echo "$previousBlackMoon\n";
     * ```
     *
     * @throws ReflectionException
     */
    public static function mixin(object|string $mixin): void
    {
        \is_string($mixin) && trait_exists($mixin)
            ? self::loadMixinTrait($mixin)
            : self::loadMixinClass($mixin);
    }

    /**
     * @throws ReflectionException
     */
    private static function loadMixinClass(object|string $mixin): void
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED,
        );

        foreach ($methods as $method) {
            if (self::cannotBeAMixinMethod($method)) {
                continue;
            }

            $macro = $method->invoke($mixin);

            if (\is_callable($macro)) {
                static::macro($method->name, $macro);
            }
        }
    }

    private static function cannotBeAMixinMethod(ReflectionMethod $method): bool
    {
        if ($method->isConstructor() || $method->isDestructor()) {
            return true;
        }

        $returnType = $method->getReturnType();

        if ($returnType instanceof ReflectionNamedType) {
            $returnedTypeName = $returnType->getName();

            if ($returnType->isBuiltin()) {
                return !\in_array($returnedTypeName, [
                    'callable',
                    'object', // could have __invoke
                    'array', // could be [MyClass::class, 'myMethod']
                    'mixed', // could be one of the above
                    // The other builtin types cannot be callable, so we can skip invoking them
                ], true);
            }

            // If it returns a non-invokable object, it cannot be a mixin method
            if (class_exists($returnedTypeName)) {
                return !is_a($returnedTypeName, Closure::class, true) && !\is_callable([$returnedTypeName, '__invoke']);
            }
        }

        return false;
    }

    private static function loadMixinTrait(string $trait): void
    {
        $context = eval(self::getAnonymousClassCodeForTrait($trait));
        $className = \get_class($context);
        $baseClass = static::class;

        foreach (self::getMixableMethods($context) as $name) {
            $closureBase = Closure::fromCallable([$context, $name]);

            static::macro($name, function (...$parameters) use ($closureBase, $className, $baseClass) {
                $downContext = isset($this) ? ($this) : new $baseClass();
                $context = isset($this) ? $this->cast($className) : new $className();

                try {
                    // @ is required to handle error if not converted into exceptions
                    $closure = @$closureBase->bindTo($context);
                } catch (Throwable) { // @codeCoverageIgnore
                    $closure = $closureBase; // @codeCoverageIgnore
                }

                // in case of errors not converted into exceptions
                $closure = $closure ?: $closureBase;

                $result = $closure(...$parameters);

                if (!($result instanceof $className)) {
                    return $result;
                }

                if ($downContext instanceof CarbonInterface && $result instanceof CarbonInterface) {
                    if ($context !== $result) {
                        $downContext = $downContext->copy();
                    }

                    return $downContext
                        ->setTimezone($result->getTimezone())
                        ->modify($result->format('Y-m-d H:i:s.u'))
                        ->settings($result->getSettings());
                }

                if ($downContext instanceof CarbonInterval && $result instanceof CarbonInterval) {
                    if ($context !== $result) {
                        $downContext = $downContext->copy();
                    }

                    $downContext->copyProperties($result);
                    self::copyStep($downContext, $result);
                    self::copyNegativeUnits($downContext, $result);

                    return $downContext->settings($result->getSettings());
                }

                if ($downContext instanceof CarbonPeriod && $result instanceof CarbonPeriod) {
                    if ($context !== $result) {
                        $downContext = $downContext->copy();
                    }

                    return $downContext
                        ->setDates($result->getStartDate(), $result->getEndDate())
                        ->setRecurrences($result->getRecurrences())
                        ->setOptions($result->getOptions())
                        ->settings($result->getSettings());
                }

                return $result;
            });
        }
    }

    private static function getAnonymousClassCodeForTrait(string $trait): string
    {
        return 'return new class() extends '.static::class.' {use '.$trait.';};';
    }

    private static function getMixableMethods(self $context): Generator
    {
        foreach (get_class_methods($context) as $name) {
            if (method_exists(static::class, $name)) {
                continue;
            }

            yield $name;
        }
    }

    /**
     * Stack a Carbon context from inside calls of self::this() and execute a given action.
     */
    protected static function bindMacroContext(?self $context, callable $callable): mixed
    {
        static::$macroContextStack[] = $context;

        try {
            return $callable();
        } finally {
            array_pop(static::$macroContextStack);
        }
    }

    /**
     * Return the current context from inside a macro callee or a null if static.
     */
    protected static function context(): ?static
    {
        return end(static::$macroContextStack) ?: null;
    }

    /**
     * Return the current context from inside a macro callee or a new one if static.
     */
    protected static function this(): static
    {
        return end(static::$macroContextStack) ?: new static();
    }
}
