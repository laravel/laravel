<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\Matcher;

use InvalidArgumentException;
use Psy\Util\Docblock;

/**
 * A magic method tab completion Matcher.
 *
 * This matcher provides completion for magic methods declared via @method
 * docblock tags on classes and objects.
 */
class MagicMethodsMatcher extends AbstractContextAwareMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = []): array
    {
        $input = $this->getInput($tokens);

        $firstToken = \array_pop($tokens);
        if (self::tokenIs($firstToken, self::T_STRING)) {
            // Pop the operator (-> or ::) which is now at the end
            $operatorToken = \array_pop($tokens);
        } else {
            // First token IS the operator
            $operatorToken = $firstToken;
        }

        // Determine if this is instance (->) or static (::) context
        $isStatic = self::tokenIs($operatorToken, self::T_DOUBLE_COLON);

        if ($isStatic) {
            return $this->getStaticMatches($tokens, $input);
        }

        return $this->getInstanceMatches($tokens, $input);
    }

    /**
     * Get magic method matches for instance context ($obj->).
     *
     * @return string[]
     */
    private function getInstanceMatches(array $tokens, string $input): array
    {
        $objectToken = \array_pop($tokens);
        if (!\is_array($objectToken)) {
            return [];
        }
        $objectName = \str_replace('$', '', $objectToken[1]);

        try {
            $object = $this->getVariable($objectName);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        if (!\is_object($object)) {
            return [];
        }

        try {
            $reflection = new \ReflectionClass($object);
        } catch (\ReflectionException $e) {
            return [];
        }

        $magicMethods = Docblock::getMagicMethods($reflection);

        $matches = [];
        foreach ($magicMethods as $method) {
            if (self::startsWith($input, $method->getName())) {
                $matches[] = $method->getName();
            }
        }

        return $matches;
    }

    /**
     * Get magic method matches for static context (Class::).
     *
     * @return string[]
     */
    private function getStaticMatches(array $tokens, string $input): array
    {
        $class = $this->getNamespaceAndClass($tokens);

        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return [];
        }

        $magicMethods = Docblock::getMagicMethods($reflection);

        // For static context, only return static magic methods
        // unless this is a command like `doc` or `ls` that wants all methods
        $needAll = self::needCompleteClass($tokens[1] ?? null);

        $matches = [];
        foreach ($magicMethods as $method) {
            if ($needAll || $method->isStatic()) {
                if (self::startsWith($input, $method->getName())) {
                    $chunks = \explode('\\', $class);
                    $className = \array_pop($chunks);
                    $matches[] = $className.'::'.$method->getName();
                }
            }
        }

        return $matches;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens): bool
    {
        $token = \array_pop($tokens);
        $prevToken = \array_pop($tokens);

        switch (true) {
            // Instance: $obj-> or $obj->meth
            case self::tokenIs($token, self::T_OBJECT_OPERATOR):
            case self::tokenIs($prevToken, self::T_OBJECT_OPERATOR):
                // Static: Class:: or Class::meth
            case self::tokenIs($token, self::T_DOUBLE_COLON):
            case self::tokenIs($prevToken, self::T_DOUBLE_COLON) && self::tokenIs($token, self::T_STRING):
                return true;
        }

        return false;
    }
}
