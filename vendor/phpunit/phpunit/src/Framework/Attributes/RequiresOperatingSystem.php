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
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresOperatingSystem
{
    /**
     * @var non-empty-string
     */
    private string $regularExpression;

    /**
     * @param non-empty-string $regularExpression
     */
    public function __construct(string $regularExpression)
    {
        $this->regularExpression = $regularExpression;
    }

    /**
     * @return non-empty-string
     */
    public function regularExpression(): string
    {
        return $this->regularExpression;
    }
}
