<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class Test
{
    /**
     * @var non-empty-string
     */
    private string $file;

    /**
     * @param non-empty-string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return non-empty-string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * @phpstan-assert-if-true TestMethod $this
     */
    public function isTestMethod(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true Phpt $this
     */
    public function isPhpt(): bool
    {
        return false;
    }

    /**
     * @return non-empty-string
     */
    abstract public function id(): string;

    /**
     * @return non-empty-string
     */
    abstract public function name(): string;
}
