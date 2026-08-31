<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Generator;

use function count;
use function explode;
use function implode;
use function is_object;
use function is_string;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_contains;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function substr_count;
use function trim;
use function var_export;
use ReflectionMethod;
use ReflectionParameter;
use SebastianBergmann\Type\ReflectionMapper;
use SebastianBergmann\Type\Type;
use SebastianBergmann\Type\UnknownType;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MockMethod
{
    use TemplateLoader;

    /**
     * @var class-string
     */
    private readonly string $className;

    /**
     * @var non-empty-string
     */
    private readonly string $methodName;
    private readonly bool $cloneArguments;
    private readonly string $modifier;
    private readonly string $argumentsForDeclaration;
    private readonly string $argumentsForCall;
    private readonly Type $returnType;
    private readonly string $reference;
    private readonly bool $callOriginalMethod;
    private readonly bool $static;
    private readonly ?string $deprecation;

    /**
     * @var array<int, mixed>
     */
    private readonly array $defaultParameterValues;

    /**
     * @var non-negative-int
     */
    private readonly int $numberOfParameters;

    /**
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public static function fromReflection(ReflectionMethod $method, bool $callOriginalMethod, bool $cloneArguments): self
    {
        if ($method->isPrivate()) {
            $modifier = 'private';
        } elseif ($method->isProtected()) {
            $modifier = 'protected';
        } else {
            $modifier = 'public';
        }

        if ($method->isStatic()) {
            $modifier .= ' static';
        }

        if ($method->returnsReference()) {
            $reference = '&';
        } else {
            $reference = '';
        }

        $docComment = $method->getDocComment();

        if (is_string($docComment) &&
            preg_match('#\*[ \t]*+@deprecated[ \t]*+(.*?)\r?+\n[ \t]*+\*(?:[ \t]*+@|/$)#s', $docComment, $deprecation)) {
            $deprecation = trim(preg_replace('#[ \t]*\r?\n[ \t]*+\*[ \t]*+#', ' ', $deprecation[1]));
        } else {
            $deprecation = null;
        }

        return new self(
            $method->getDeclaringClass()->getName(),
            $method->getName(),
            $cloneArguments,
            $modifier,
            self::methodParametersForDeclaration($method),
            self::methodParametersForCall($method),
            self::methodParametersDefaultValues($method),
            count($method->getParameters()),
            (new ReflectionMapper)->fromReturnType($method),
            $reference,
            $callOriginalMethod,
            $method->isStatic(),
            $deprecation,
        );
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public static function fromName(string $className, string $methodName, bool $cloneArguments): self
    {
        return new self(
            $className,
            $methodName,
            $cloneArguments,
            'public',
            '',
            '',
            [],
            0,
            new UnknownType,
            '',
            false,
            false,
            null,
        );
    }

    /**
     * @param class-string      $className
     * @param non-empty-string  $methodName
     * @param array<int, mixed> $defaultParameterValues
     * @param non-negative-int  $numberOfParameters
     */
    private function __construct(string $className, string $methodName, bool $cloneArguments, string $modifier, string $argumentsForDeclaration, string $argumentsForCall, array $defaultParameterValues, int $numberOfParameters, Type $returnType, string $reference, bool $callOriginalMethod, bool $static, ?string $deprecation)
    {
        $this->className               = $className;
        $this->methodName              = $methodName;
        $this->cloneArguments          = $cloneArguments;
        $this->modifier                = $modifier;
        $this->argumentsForDeclaration = $argumentsForDeclaration;
        $this->argumentsForCall        = $argumentsForCall;
        $this->defaultParameterValues  = $defaultParameterValues;
        $this->numberOfParameters      = $numberOfParameters;
        $this->returnType              = $returnType;
        $this->reference               = $reference;
        $this->callOriginalMethod      = $callOriginalMethod;
        $this->static                  = $static;
        $this->deprecation             = $deprecation;
    }

    /**
     * @return non-empty-string
     */
    public function methodName(): string
    {
        return $this->methodName;
    }

    /**
     * @throws RuntimeException
     */
    public function generateCode(): string
    {
        if ($this->static) {
            $templateFile = 'doubled_static_method.tpl';
        } else {
            $templateFile = sprintf(
                '%s_method.tpl',
                $this->callOriginalMethod ? 'proxied' : 'doubled',
            );
        }

        $deprecation  = $this->deprecation;
        $returnResult = '';

        if (!$this->returnType->isNever() && !$this->returnType->isVoid() && !$this->mustNotReturnValue()) {
            $returnResult = <<<'EOT'


        return $__phpunit_result;
EOT;
        }

        if (null !== $this->deprecation) {
            $deprecation         = "The {$this->className}::{$this->methodName} method is deprecated ({$this->deprecation}).";
            $deprecationTemplate = $this->loadTemplate('deprecation.tpl');

            $deprecationTemplate->setVar(
                [
                    'deprecation' => var_export($deprecation, true),
                ],
            );

            $deprecation = $deprecationTemplate->render();
        }

        $template = $this->loadTemplate($templateFile);

        $argumentsCount = 0;

        if (str_contains($this->argumentsForCall, '...')) {
            $argumentsCount = null;
        } elseif (!empty($this->argumentsForCall)) {
            $argumentsCount = substr_count($this->argumentsForCall, ',') + 1;
        }

        $template->setVar(
            [
                'arguments_decl'     => $this->argumentsForDeclaration,
                'arguments_call'     => $this->argumentsForCall,
                'return_declaration' => !empty($this->returnType->asString()) ? (': ' . $this->returnType->asString()) : '',
                'return_type'        => $this->returnType->asString(),
                'arguments_count'    => (string) $argumentsCount,
                'class_name'         => $this->className,
                'method_name'        => $this->methodName,
                'modifier'           => $this->modifier,
                'reference'          => $this->reference,
                'clone_arguments'    => $this->cloneArguments ? 'true' : 'false',
                'deprecation'        => $deprecation ?? '',
                'return_result'      => $returnResult,
            ],
        );

        return $template->render();
    }

    public function returnType(): Type
    {
        return $this->returnType;
    }

    /**
     * @return array<int, mixed>
     */
    public function defaultParameterValues(): array
    {
        return $this->defaultParameterValues;
    }

    /**
     * @return non-negative-int
     */
    public function numberOfParameters(): int
    {
        return $this->numberOfParameters;
    }

    /**
     * @see https://wiki.php.net/rfc/deprecate-return-value-from-construct
     */
    private function mustNotReturnValue(): bool
    {
        $methodName = strtolower($this->methodName);

        return $methodName === '__construct' || $methodName === '__destruct';
    }

    /**
     * Returns the parameters of a function or method.
     *
     * @throws RuntimeException
     */
    private static function methodParametersForDeclaration(ReflectionMethod $method): string
    {
        $parameters = [];
        $types      = (new ReflectionMapper)->fromParameterTypes($method);

        foreach ($method->getParameters() as $i => $parameter) {
            $name = '$' . $parameter->getName();

            /* Note: PHP extensions may use empty names for reference arguments
             * or "..." for methods taking a variable number of arguments.
             */
            if ($name === '$' || $name === '$...') {
                $name = '$arg' . $i;
            }

            $default         = '';
            $reference       = '';
            $typeDeclaration = '';

            if (!$types[$i]->type()->isUnknown()) {
                $typeDeclaration = $types[$i]->type()->asString() . ' ';
            }

            if ($parameter->isPassedByReference()) {
                $reference = '&';
            }

            if ($parameter->isVariadic()) {
                $name = '...' . $name;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $default = ' = ' . self::exportDefaultValue($parameter);
            } elseif ($parameter->isOptional()) {
                $default = ' = null';
            }

            $parameters[] = $typeDeclaration . $reference . $name . $default;
        }

        return implode(', ', $parameters);
    }

    /**
     * Returns the parameters of a function or method.
     *
     * @throws ReflectionException
     */
    private static function methodParametersForCall(ReflectionMethod $method): string
    {
        $parameters = [];

        foreach ($method->getParameters() as $i => $parameter) {
            $name = '$' . $parameter->getName();

            /* Note: PHP extensions may use empty names for reference arguments
             * or "..." for methods taking a variable number of arguments.
             */
            if ($name === '$' || $name === '$...') {
                $name = '$arg' . $i;
            }

            if ($parameter->isVariadic()) {
                continue;
            }

            if ($parameter->isPassedByReference()) {
                $parameters[] = '&' . $name;
            } else {
                $parameters[] = $name;
            }
        }

        return implode(', ', $parameters);
    }

    /**
     * @throws ReflectionException
     */
    private static function exportDefaultValue(ReflectionParameter $parameter): string
    {
        try {
            $defaultValue = $parameter->getDefaultValue();

            if (!is_object($defaultValue)) {
                return var_export($defaultValue, true);
            }

            $parameterAsString = $parameter->__toString();

            return explode(
                ' = ',
                substr(
                    substr(
                        $parameterAsString,
                        strpos($parameterAsString, '<optional> ') + strlen('<optional> '),
                    ),
                    0,
                    -2,
                ),
            )[1];
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
     * @return array<int, mixed>
     */
    private static function methodParametersDefaultValues(ReflectionMethod $method): array
    {
        $result = [];

        foreach ($method->getParameters() as $i => $parameter) {
            if (!$parameter->isDefaultValueAvailable()) {
                continue;
            }

            $result[$i] = $parameter->getDefaultValue();
        }

        return $result;
    }
}
