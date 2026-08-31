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
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Ticket
{
    /**
     * @var non-empty-string
     */
    private string $text;

    /**
     * @param non-empty-string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return non-empty-string
     */
    public function text(): string
    {
        return $this->text;
    }
}
