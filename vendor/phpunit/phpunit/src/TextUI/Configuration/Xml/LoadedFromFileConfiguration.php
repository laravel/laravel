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
final readonly class LoadedFromFileConfiguration extends Configuration
{
    /**
     * @var non-empty-string
     */
    private string $filename;
    private ValidationResult $validationResult;

    /**
     * @param non-empty-string $filename
     */
    public function __construct(string $filename, ValidationResult $validationResult, ExtensionBootstrapCollection $extensions, Source $source, CodeCoverage $codeCoverage, Groups $groups, Logging $logging, Php $php, PHPUnit $phpunit, TestSuiteCollection $testSuite)
    {
        $this->filename         = $filename;
        $this->validationResult = $validationResult;

        parent::__construct(
            $extensions,
            $source,
            $codeCoverage,
            $groups,
            $logging,
            $php,
            $phpunit,
            $testSuite,
        );
    }

    /**
     * @return non-empty-string
     */
    public function filename(): string
    {
        return $this->filename;
    }

    public function hasValidationErrors(): bool
    {
        return $this->validationResult->hasValidationErrors();
    }

    public function validationErrors(): string
    {
        return $this->validationResult->asString();
    }

    public function wasLoadedFromFile(): bool
    {
        return true;
    }
}
