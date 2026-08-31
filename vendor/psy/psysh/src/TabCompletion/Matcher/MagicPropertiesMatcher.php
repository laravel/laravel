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
 * A magic property tab completion Matcher.
 *
 * This matcher provides completion for magic properties declared via
 * {@literal @}property, {@literal @}property-read, and {@literal @}property-write
 * docblock tags on classes and objects.
 */
class MagicPropertiesMatcher extends AbstractContextAwareMatcher
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
     * Get magic property matches for instance context ($obj->).
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

        $magicProperties = Docblock::getMagicProperties($reflection);

        $matches = [];
        foreach ($magicProperties as $property) {
            if (self::startsWith($input, $property->getName())) {
                $matches[] = $property->getName();
            }
        }

        return $matches;
    }

    /**
     * Get magic property matches for static context (Class::).
     *
     * Static magic properties are less common but we support them for completeness.
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

        $magicProperties = Docblock::getMagicProperties($reflection);

        $matches = [];
        foreach ($magicProperties as $property) {
            if (self::startsWith($input, $property->getName())) {
                $chunks = \explode('\\', $class);
                $className = \array_pop($chunks);
                $matches[] = $className.'::$'.$property->getName();
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
            // Instance: $obj-> or $obj->prop
            case self::tokenIs($token, self::T_OBJECT_OPERATOR):
            case self::tokenIs($prevToken, self::T_OBJECT_OPERATOR):
                // Static: Class:: or Class::$prop
            case self::tokenIs($token, self::T_DOUBLE_COLON):
            case self::tokenIs($prevToken, self::T_DOUBLE_COLON) && self::tokenIs($token, self::T_STRING):
                return true;
        }

        return false;
    }
}
