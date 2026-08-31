<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Html;

use function is_file;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;

/**
 * @immutable
 */
final class CustomCssFile
{
    private readonly string $path;

    public static function default(): self
    {
        return new self(__DIR__ . '/Renderer/Template/css/custom.css');
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function from(string $path): self
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(
                '$path does not exist',
            );
        }

        return new self($path);
    }

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }
}
