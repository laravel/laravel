<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use PHPUnit\TextUI\Configuration\ExtensionBootstrapCollection;
use PHPUnit\TextUI\Configuration\Php;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
abstract readonly class Configuration
{
    private ExtensionBootstrapCollection $extensions;
    private Source $source;
    private CodeCoverage $codeCoverage;
    private Groups $groups;
    private Logging $logging;
    private Php $php;
    private PHPUnit $phpunit;
    private TestSuiteCollection $testSuite;

    public function __construct(ExtensionBootstrapCollection $extensions, Source $source, CodeCoverage $codeCoverage, Groups $groups, Logging $logging, Php $php, PHPUnit $phpunit, TestSuiteCollection $testSuite)
    {
        $this->extensions   = $extensions;
        $this->source       = $source;
        $this->codeCoverage = $codeCoverage;
        $this->groups       = $groups;
        $this->logging      = $logging;
        $this->php          = $php;
        $this->phpunit      = $phpunit;
        $this->testSuite    = $testSuite;
    }

    public function extensions(): ExtensionBootstrapCollection
    {
        return $this->extensions;
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function codeCoverage(): CodeCoverage
    {
        return $this->codeCoverage;
    }

    public function groups(): Groups
    {
        return $this->groups;
    }

    public function logging(): Logging
    {
        return $this->logging;
    }

    public function php(): Php
    {
        return $this->php;
    }

    public function phpunit(): PHPUnit
    {
        return $this->phpunit;
    }

    public function testSuite(): TestSuiteCollection
    {
        return $this->testSuite;
    }

    /**
     * @phpstan-assert-if-true DefaultConfiguration $this
     */
    public function isDefault(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true LoadedFromFileConfiguration $this
     */
    public function wasLoadedFromFile(): bool
    {
        return false;
    }
}
