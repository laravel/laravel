<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report;

use PHPUnit\TextUI\Configuration\Directory;
use PHPUnit\TextUI\Configuration\NoCustomCssFileException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class Html
{
    private Directory $target;
    private int $lowUpperBound;
    private int $highLowerBound;
    private string $colorSuccessLow;
    private string $colorSuccessMedium;
    private string $colorSuccessHigh;
    private string $colorWarning;
    private string $colorDanger;
    private ?string $customCssFile;

    public function __construct(Directory $target, int $lowUpperBound, int $highLowerBound, string $colorSuccessLow, string $colorSuccessMedium, string $colorSuccessHigh, string $colorWarning, string $colorDanger, ?string $customCssFile)
    {
        $this->target             = $target;
        $this->lowUpperBound      = $lowUpperBound;
        $this->highLowerBound     = $highLowerBound;
        $this->colorSuccessLow    = $colorSuccessLow;
        $this->colorSuccessMedium = $colorSuccessMedium;
        $this->colorSuccessHigh   = $colorSuccessHigh;
        $this->colorWarning       = $colorWarning;
        $this->colorDanger        = $colorDanger;
        $this->customCssFile      = $customCssFile;
    }

    public function target(): Directory
    {
        return $this->target;
    }

    public function lowUpperBound(): int
    {
        return $this->lowUpperBound;
    }

    public function highLowerBound(): int
    {
        return $this->highLowerBound;
    }

    public function colorSuccessLow(): string
    {
        return $this->colorSuccessLow;
    }

    public function colorSuccessMedium(): string
    {
        return $this->colorSuccessMedium;
    }

    public function colorSuccessHigh(): string
    {
        return $this->colorSuccessHigh;
    }

    public function colorWarning(): string
    {
        return $this->colorWarning;
    }

    public function colorDanger(): string
    {
        return $this->colorDanger;
    }

    /**
     * @phpstan-assert-if-true !null $this->customCssFile
     */
    public function hasCustomCssFile(): bool
    {
        return $this->customCssFile !== null;
    }

    /**
     * @throws NoCustomCssFileException
     */
    public function customCssFile(): string
    {
        if (!$this->hasCustomCssFile()) {
            throw new NoCustomCssFileException;
        }

        return $this->customCssFile;
    }
}
