<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class Constant
{
    private string $name;
    private bool|string $value;

    public function __construct(string $name, bool|string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): bool|string
    {
        return $this->value;
    }
}
