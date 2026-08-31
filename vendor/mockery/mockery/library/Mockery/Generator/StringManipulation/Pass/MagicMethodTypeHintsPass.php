<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\Method;
use Mockery\Generator\MockConfiguration;
use Mockery\Generator\Parameter;
use Mockery\Generator\TargetClassInterface;
use function array_filter;
use function array_merge;
use function end;
use function in_array;
use function is_array;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function rtrim;
use function sprintf;

class MagicMethodTypeHintsPass implements Pass
{
    /**
     * @var array
     */
    private $mockMagicMethods = [
        '__construct',
        '__destruct',
        '__call',
        '__callStatic',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__sleep',
        '__wakeup',
        '__toString',
        '__invoke',
        '__set_state',
        '__clone',
        '__debugInfo',
    ];

    /**
     * Apply implementation.
     *
     * @param string $code
     *
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        $magicMethods = $this->getMagicMethods($config->getTargetClass());
        foreach ($config->getTargetInterfaces() as $interface) {
            $magicMethods = array_merge($magicMethods, $this->getMagicMethods($interface));
        }

        foreach ($magicMethods as $method) {
            $code = $this->applyMagicTypeHints($code, $method);
        }

        return $code;
    }

    /**
     * Returns the magic methods within the
     * passed DefinedTargetClass.
     *
     * @return array
     */
    public function getMagicMethods(?TargetClassInterface $class = null)
    {
        if (! $class instanceof TargetClassInterface) {
            return [];
        }

        return array_filter($class->getMethods(), function (Method $method) {
            return in_array($method->getName(), $this->mockMagicMethods, true);
        });
    }

    protected function renderTypeHint(Parameter $param)
    {
        $typeHint = $param->getTypeHint();

        return $typeHint === null ? '' : sprintf('%s ', $typeHint);
    }

    /**
     * Applies type hints of magic methods from
     * class to the passed code.
     *
     * @param int $code
     *
     * @return string
     */
    private function applyMagicTypeHints($code, Method $method)
    {
        if ($this->isMethodWithinCode($code, $method)) {
            $namedParameters = $this->getOriginalParameters($code, $method);
            $code = preg_replace(
                $this->getDeclarationRegex($method->getName()),
                $this->getMethodDeclaration($method, $namedParameters),
                $code
            );
        }

        return $code;
    }

    /**
     * Returns a regex string used to match the
     * declaration of some method.
     *
     * @param string $methodName
     *
     * @return string
     */
    private function getDeclarationRegex($methodName)
    {
        return sprintf('/public\s+(?:static\s+)?function\s+%s\s*\(.*\)\s*(?=\{)/i', $methodName);
    }

    /**
     * Gets the declaration code, as a string, for the passed method.
     *
     * @param array $namedParameters
     *
     * @return string
     */
    private function getMethodDeclaration(Method $method, array $namedParameters)
    {
        $declaration = 'public';
        $declaration .= $method->isStatic() ? ' static' : '';
        $declaration .= ' function ' . $method->getName() . '(';

        foreach ($method->getParameters() as $index => $parameter) {
            $declaration .= $this->renderTypeHint($parameter);
            $name = $namedParameters[$index] ?? $parameter->getName();
            $declaration .= '$' . $name;
            $declaration .= ',';
        }

        $declaration = rtrim($declaration, ',');
        $declaration .= ') ';

        $returnType = $method->getReturnType();
        if ($returnType !== null) {
            $declaration .= sprintf(': %s', $returnType);
        }

        return $declaration;
    }

    /**
     * Returns the method original parameters, as they're
     * described in the $code string.
     *
     * @param int $code
     *
     * @return array
     */
    private function getOriginalParameters($code, Method $method)
    {
        $matches = [];
        $parameterMatches = [];

        preg_match($this->getDeclarationRegex($method->getName()), $code, $matches);

        if ($matches !== []) {
            preg_match_all('/(?<=\$)(\w+)+/i', $matches[0], $parameterMatches);
        }

        $groupMatches = end($parameterMatches);

        return is_array($groupMatches) ? $groupMatches : [$groupMatches];
    }

    /**
     * Checks if the method is declared within code.
     *
     * @param int $code
     *
     * @return bool
     */
    private function isMethodWithinCode($code, Method $method)
    {
        return preg_match($this->getDeclarationRegex($method->getName()), $code) === 1;
    }
}
