<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Baseline;

use const FILE_IGNORE_NEW_LINES;
use function assert;
use function file;
use function is_file;
use function sha1;
use PHPUnit\Runner\FileDoesNotExistException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Issue
{
    /**
     * @var non-empty-string
     */
    private string $file;

    /**
     * @var positive-int
     */
    private int $line;

    /**
     * @var non-empty-string
     */
    private string $hash;

    /**
     * @var non-empty-string
     */
    private string $description;

    /**
     * @param non-empty-string  $file
     * @param positive-int      $line
     * @param ?non-empty-string $hash
     * @param non-empty-string  $description
     *
     * @throws FileDoesNotExistException
     * @throws FileDoesNotHaveLineException
     */
    public static function from(string $file, int $line, ?string $hash, string $description): self
    {
        if ($hash === null) {
            $hash = self::calculateHash($file, $line);
        }

        return new self($file, $line, $hash, $description);
    }

    /**
     * @param non-empty-string $file
     * @param positive-int     $line
     * @param non-empty-string $hash
     * @param non-empty-string $description
     */
    private function __construct(string $file, int $line, string $hash, string $description)
    {
        $this->file        = $file;
        $this->line        = $line;
        $this->hash        = $hash;
        $this->description = $description;
    }

    /**
     * @return non-empty-string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * @return positive-int
     */
    public function line(): int
    {
        return $this->line;
    }

    /**
     * @return non-empty-string
     */
    public function hash(): string
    {
        return $this->hash;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return $this->description;
    }

    public function equals(self $other): bool
    {
        return $this->file() === $other->file() &&
               $this->line() === $other->line() &&
               $this->hash() === $other->hash() &&
               $this->description() === $other->description();
    }

    /**
     * @param non-empty-string $file
     * @param positive-int     $line
     *
     * @throws FileDoesNotExistException
     * @throws FileDoesNotHaveLineException
     *
     * @return non-empty-string
     */
    private static function calculateHash(string $file, int $line): string
    {
        $lines = @file($file, FILE_IGNORE_NEW_LINES);

        if ($lines === false && !is_file($file)) {
            throw new FileDoesNotExistException($file);
        }

        $key = $line - 1;

        if (!isset($lines[$key])) {
            throw new FileDoesNotHaveLineException($file, $line);
        }

        $hash = sha1($lines[$key]);

        assert($hash !== '');

        return $hash;
    }
}
