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

use PHPUnit\Util\VersionComparisonOperator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class TestFile
{
    /**
     * @var non-empty-string
     */
    private string $path;
    private string $phpVersion;
    private VersionComparisonOperator $phpVersionOperator;

    /**
     * @var list<non-empty-string>
     */
    private array $groups;

    /**
     * @param non-empty-string       $path
     * @param list<non-empty-string> $groups
     */
    public function __construct(string $path, string $phpVersion, VersionComparisonOperator $phpVersionOperator, array $groups)
    {
        $this->path               = $path;
        $this->phpVersion         = $phpVersion;
        $this->phpVersionOperator = $phpVersionOperator;
        $this->groups             = $groups;
    }

    /**
     * @return non-empty-string
     */
    public function path(): string
    {
        return $this->path;
    }

    public function phpVersion(): string
    {
        return $this->phpVersion;
    }

    public function phpVersionOperator(): VersionComparisonOperator
    {
        return $this->phpVersionOperator;
    }

    /**
     * @return list<non-empty-string>
     */
    public function groups(): array
    {
        return $this->groups;
    }
}
