<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Code\TestCollection;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestSuiteForTestClass extends TestSuite
{
    /**
     * @var class-string
     */
    private string $className;
    private string $file;
    private int $line;

    /**
     * @param class-string $name
     */
    public function __construct(string $name, int $size, TestCollection $tests, string $file, int $line)
    {
        parent::__construct($name, $size, $tests);

        $this->className = $name;
        $this->file      = $file;
        $this->line      = $line;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function isForTestClass(): true
    {
        return true;
    }
}
