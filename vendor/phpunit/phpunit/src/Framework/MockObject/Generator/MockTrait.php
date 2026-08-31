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

use function class_exists;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5243
 */
final readonly class MockTrait implements MockType
{
    private string $classCode;

    /**
     * @var class-string
     */
    private string $mockName;

    /**
     * @param class-string $mockName
     */
    public function __construct(string $classCode, string $mockName)
    {
        $this->classCode = $classCode;
        $this->mockName  = $mockName;
    }

    /**
     * @return class-string
     */
    public function generate(): string
    {
        if (!class_exists($this->mockName, false)) {
            eval($this->classCode);
        }

        return $this->mockName;
    }
}
