<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MemoryUsage
{
    private int $bytes;

    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }

    private function __construct(int $bytes)
    {
        $this->bytes = $bytes;
    }

    public function bytes(): int
    {
        return $this->bytes;
    }

    public function diff(self $other): self
    {
        return self::fromBytes($this->bytes - $other->bytes);
    }
}
