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
use PHPUnit\Runner\Extension\Extension;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPhpunitExtension
{
    /**
     * @var class-string<Extension>
     */
    private string $extensionClass;

    /**
     * @param class-string<Extension> $extensionClass
     */
    public function __construct(string $extensionClass)
    {
        $this->extensionClass = $extensionClass;
    }

    /**
     * @return class-string<Extension>
     */
    public function extensionClass(): string
    {
        return $this->extensionClass;
    }
}
