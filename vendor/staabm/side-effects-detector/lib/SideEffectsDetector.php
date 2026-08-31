<?php

namespace staabm\SideEffectsDetector;

final class SideEffectsDetector {
    /**
     * @var array<int>
     */
    private array $scopePollutingTokens = [
        T_CLASS,
        T_FUNCTION,
        T_NEW,
        T_EVAL,
        T_GLOBAL,
        T_GOTO,
        T_HALT_COMPILER,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_THROW,
        T_UNSET,
        T_UNSET_CAST
    ];

    private const PROCESS_EXIT_TOKENS = [
        T_EXIT
    ];

    private const OUTPUT_TOKENS = [
        T_PRINT,
        T_ECHO,
        T_INLINE_HTML
    ];

    private const SCOPE_POLLUTING_FUNCTIONS = [
        'putenv',
        'setlocale',
        'class_exists',
        'ini_set',
    ];

    private const STANDARD_OUTPUT_FUNCTIONS = [
        'printf',
        'vprintf'
    ];

    private const INPUT_OUTPUT_FUNCTIONS = [
        'fopen',
        'file_get_contents',
        'file_put_contents',
        'fwrite',
        'fputs',
        'fread',
        'unlink'
    ];

    /**
     * @var array<string, array{'hasSideEffects': bool}>
     */
    private array $functionMetadata;

    public function __construct() {
        $functionMeta = require __DIR__ . '/functionMetadata.php';
        if (!is_array($functionMeta)) {
            throw new \RuntimeException('Invalid function metadata');
        }
        $this->functionMetadata = $functionMeta;

        if (defined('T_ENUM')) {
            $this->scopePollutingTokens[] = T_ENUM;
        }
    }

    /**
     * @api
     *
     * @return array<SideEffect::*>
     */
    public function getSideEffects(string $code): array {
        $tokens = token_get_all($code);

        $sideEffects = [];
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($this->isAnonymousFunction($tokens, $i)) {
                continue;
            }

            if (in_array($token[0], self::OUTPUT_TOKENS, true)) {
                $sideEffects[] = SideEffect::STANDARD_OUTPUT;
                continue;
            }
            if (in_array($token[0], self::PROCESS_EXIT_TOKENS, true)) {
                $sideEffects[] = SideEffect::PROCESS_EXIT;
                continue;
            }
            if (in_array($token[0], $this->scopePollutingTokens, true)) {
                $sideEffects[] = SideEffect::SCOPE_POLLUTION;

                $i++;
                if (in_array($token[0], [T_FUNCTION, T_CLASS], true)) {
                    $this->consumeWhitespaces($tokens, $i);
                }

                // consume function/class-name
                if (
                    !array_key_exists($i, $tokens)
                    || !is_array($tokens[$i])
                    || $tokens[$i][0] !== T_STRING
                ) {
                    continue;
                }

                $i++;
                continue;
            }

            $functionCall = $this->getFunctionCall($tokens, $i);
            if ($functionCall !== null) {
                $callSideEffect = $this->getFunctionCallSideEffect($functionCall);
                if ($callSideEffect !== null) {
                    $sideEffects[] = $callSideEffect;
                }
                continue;
            }

            $methodCall = $this->getMethodCall($tokens, $i);
            if ($methodCall !== null) {
                $sideEffects[] = SideEffect::MAYBE;
                continue;
            }

            $propertyAccess = $this->getPropertyAccess($tokens, $i);
            if ($propertyAccess !== null) {
                $sideEffects[] = SideEffect::SCOPE_POLLUTION;
                continue;
            }

            if ($this->isNonLocalVariable($tokens, $i)) {
                $sideEffects[] = SideEffect::SCOPE_POLLUTION;
                continue;
            }
        }

        return array_values(array_unique($sideEffects));
    }

    /**
     * @return SideEffect::*|null
     */
    private function getFunctionCallSideEffect(string $functionName): ?string { // @phpstan-ignore return.unusedType
        if (in_array($functionName, self::STANDARD_OUTPUT_FUNCTIONS, true)) {
            return SideEffect::STANDARD_OUTPUT;
        }

        if (in_array($functionName, self::INPUT_OUTPUT_FUNCTIONS, true)) {
            return SideEffect::INPUT_OUTPUT;
        }

        if (in_array($functionName, self::SCOPE_POLLUTING_FUNCTIONS, true)) {
            return SideEffect::SCOPE_POLLUTION;
        }

        if (array_key_exists($functionName, $this->functionMetadata)) {
            if ($this->functionMetadata[$functionName]['hasSideEffects'] === true) {
                return SideEffect::UNKNOWN_CLASS;
            }
        } else {
            try {
                $reflectionFunction = new \ReflectionFunction($functionName);
                $returnType = $reflectionFunction->getReturnType();
                if ($returnType === null) {
                    return SideEffect::MAYBE; // no reflection information -> we don't know
                }
                if ((string)$returnType === 'void') {
                    return SideEffect::UNKNOWN_CLASS; // functions with void return type must have side-effects
                }
            } catch (\ReflectionException $e) {
                return SideEffect::MAYBE; // function does not exist -> we don't know
            }
        }

        return null;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function getFunctionCall(array $tokens, int $index): ?string {
        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || $tokens[$index][0] !== T_STRING
        ) {
            return null;
        }
        $functionName = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            array_key_exists($index, $tokens)
            && $tokens[$index] === '('
        ) {
            return $functionName;
        }

        return null;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function getMethodCall(array $tokens, int $index): ?string {
        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_VARIABLE, T_STRING], true)
        ) {
            return null;
        }
        $callee = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_OBJECT_OPERATOR , T_DOUBLE_COLON ], true)
        ) {
            return null;
        }
        $operator = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_STRING], true)
        ) {
            return null;
        }
        $method = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            array_key_exists($index, $tokens)
            && $tokens[$index] !== '('
        ) {
            return null;
        }

        return $callee . $operator . $method;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function getPropertyAccess(array $tokens, int $index): ?string {
        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_VARIABLE, T_STRING], true)
        ) {
            return null;
        }
        $objectOrClass = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_OBJECT_OPERATOR , T_DOUBLE_COLON ], true)
        ) {
            return null;
        }
        $operator = $tokens[$index][1];

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || !in_array($tokens[$index][0], [T_STRING, T_VARIABLE], true)
        ) {
            return null;
        }
        $propName = $tokens[$index][1];

        return $objectOrClass . $operator . $propName;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function isAnonymousFunction(array $tokens, int $index): bool
    {
        if (
            !array_key_exists($index, $tokens)
            || !is_array($tokens[$index])
            || $tokens[$index][0] !== T_FUNCTION
        ) {
            return false;
        }

        $index++;
        $this->consumeWhitespaces($tokens, $index);

        if (
            array_key_exists($index, $tokens)
            && $tokens[$index] === '('
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function isNonLocalVariable(array $tokens, int $index): bool
    {
        if (
            array_key_exists($index, $tokens)
            && is_array($tokens[$index])
            && $tokens[$index][0] === T_VARIABLE
        ) {
            if (
                in_array(
                $tokens[$index][1],
                [
                    '$this',
                    '$GLOBALS', '$_SERVER', '$_GET', '$_POST', '$_FILES', '$_COOKIE', '$_SESSION', '$_REQUEST', '$_ENV',
                ],
            true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string|int> $tokens
     */
    private function consumeWhitespaces(array $tokens, int &$index): void {
        while (
            array_key_exists($index, $tokens)
            && is_array($tokens[$index])
            && $tokens[$index][0] === T_WHITESPACE
        ) {
            $index++;
        }
    }
}
