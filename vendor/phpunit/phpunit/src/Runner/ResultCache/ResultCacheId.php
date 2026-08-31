<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\ResultCache;

use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Framework\Reorderable;
use PHPUnit\Framework\TestCase;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ResultCacheId
{
    public static function fromTest(Test $test): self
    {
        if ($test instanceof TestMethod) {
            return new self($test->className() . '::' . $test->name());
        }

        return new self($test->id());
    }

    public static function fromReorderable(Reorderable $reorderable): self
    {
        return new self($reorderable->sortId());
    }

    /**
     * For use in PHPUnit tests only!
     *
     * @param class-string<TestCase> $class
     */
    public static function fromTestClassAndMethodName(string $class, string $methodName): self
    {
        return new self($class . '::' . $methodName);
    }

    private function __construct(
        private string $id,
    ) {
    }

    public function asString(): string
    {
        return $this->id;
    }
}
