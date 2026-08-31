<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class DependsOnClassUsingShallowClone
{
    /**
     * @var class-string
     */
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }
}
