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
final readonly class TestWithJson
{
    /**
     * @var non-empty-string
     */
    private string $json;

    /**
     * @var ?non-empty-string
     */
    private ?string $name;

    /**
     * @param non-empty-string  $json
     * @param ?non-empty-string $name
     */
    public function __construct(string $json, ?string $name = null)
    {
        $this->json = $json;
        $this->name = $name;
    }

    /**
     * @return non-empty-string
     */
    public function json(): string
    {
        return $this->json;
    }

    /**
     * @return ?non-empty-string
     */
    public function name(): ?string
    {
        return $this->name;
    }
}
