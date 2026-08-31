<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function strrpos;
use function substr;
use const PHP_VERSION_ID;

/**
 * Internal classes can not be instantiated with the newInstanceWithoutArgs
 * reflection method, so need the serialization hack. If the class also
 * implements Serializable, we need to replace the standard unserialize method
 * definition with a dummy
 */
class RemoveUnserializeForInternalSerializableClassesPass implements Pass
{
    public const DUMMY_METHOD_DEFINITION = 'public function unserialize(string $data): void {} ';

    public const DUMMY_METHOD_DEFINITION_LEGACY = 'public function unserialize($string) {} ';

    /**
     * @param  string $code
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        $target = $config->getTargetClass();

        if (! $target) {
            return $code;
        }

        if (! $target->hasInternalAncestor() || ! $target->implementsInterface('Serializable')) {
            return $code;
        }

        return $this->appendToClass(
            $code,
            PHP_VERSION_ID < 80100 ? self::DUMMY_METHOD_DEFINITION_LEGACY : self::DUMMY_METHOD_DEFINITION
        );
    }

    protected function appendToClass($class, $code)
    {
        $lastBrace = strrpos($class, '}');
        return substr($class, 0, $lastBrace) . $code . "\n    }\n";
    }
}
