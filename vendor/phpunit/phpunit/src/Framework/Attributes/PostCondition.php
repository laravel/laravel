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
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class PostCondition
{
    private int $priority;

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }

    public function priority(): int
    {
        return $this->priority;
    }
}
